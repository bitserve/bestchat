<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();

function bestchat_is_logged_in() {
  return @strlen($_SESSION['nick']) > 0;
}

function bestchat_logout() {
  $_SESSION['nick'] = '';
  unset($_SESSION['nick']);

  header("Location: index.php");
  exit(0);
}

function bestchat_login() {
  $captcha = @trim($_POST['captcha']);
  if (strlen($captcha) <= 0) { return "Captcha error"; }
  if ($captcha !== @$_SESSION['captcha']) { return "Captcha error"; }
  $nick = @strtolower(trim($_POST['nick']));
  $pass = @$_POST['pass'];
  if (strlen($nick) < 3) { return "Nick must be at least 3 chars"; }
  if (strlen($pass) < 6) { return "Password must be 6 chars"; }
  if (strlen($nick) > 10) { return "Nick cannot be longer than 10 chars"; }
  if (!preg_match('#^[a-z]+[a-z0-9_]*$#i', $nick)) { return "Nick must contain only basic characters"; }

  $user = bestchat_query(
    "select * from `bc_user`",
    "where `nick` = :nick",
    [':nick' => $nick]
  )->fetch();

  if (empty($user)) {
    $salt = sha1(openssl_random_pseudo_bytes(10));
    $secret = sha1($nick.$salt.$pass);

    bestchat_query(
      "insert `bc_user` set   ",
      "  `nick`   = :nick,    ",
      "  `salt`   = :salt,    ",
      "  `secret` = :secret,  ",
      "  `firstSeen` = now()  ",
      [':nick' => $nick, ':salt' => $salt, ':secret' => $secret]
    );

    $_SESSION['nick'] = $nick;
  } else {
    $hash = sha1($user->nick.$user->salt.$pass);
    if ($hash !== $user->secret) { return "Invalid password"; }
    $_SESSION['nick'] = $user->nick;
  }
}

function bestchat_get_hash($messages) {
  $hash = sha1("bestchat etag start");

  foreach ($messages as $msg) {
    $hash = sha1($hash.$msg->when.$msg->sender.$msg->content);
  }

  $hash = sha1("bestchat $hash etag finish");
  return substr($hash, 0, 10);
}

function bestchat_check_etag($messages, &$hash=null) {
  if (!isset($hash)) { $hash = bestchat_get_hash($messages); }
  header("ETag: $hash");
  header("Cache-Control: public");

  $old = @trim($_SERVER['HTTP_IF_NONE_MATCH']);
  if (strlen($old) !== 10) { return; }
  if ($old !== $hash) {
    if (@$_GET['h'] !== $hash) {
      header("Location: bd.php?h=$hash#b_$hash");
      exit(0);
    }

    return;
  }

  header("HTTP/1.1 304 Not Modified");
  exit(0);
  throw new Exception("Should not continue execution");
}

function bestchat_get_pdo() {
  global $bestchat_pdo;
  bestchat_pdo_connect();
  return $bestchat_pdo;
}

function bestchat_load_config() {
  $cfg = (include __DIR__.'/config.php');
  if (empty($cfg)) { throw new Exception("Modify config.php"); }
  return (object)$cfg;
}

function bestchat_pdo_connect() {
  global $bestchat_pdo;
  if (isset($bestchat_pdo) && $bestchat_pdo instanceof pdo) { return; }
  $cfg = bestchat_load_config();
  $dsn = "mysql:dbname={$cfg->dbname};host={$cfg->dbhost}";
  $bestchat_pdo = new pdo($dsn, $cfg->dbuser, $cfg->dbpass);
  $bestchat_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $bestchat_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
}

function bestchat_query() {
  $pdo = bestchat_get_pdo();
  $args = func_get_args();
  $sql = [];
  $params = [];

  foreach ($args as $x) {
    if (is_string($x)) {
      $sql[] = $x;
    } else if (is_array($x)) {
      foreach ($x as $key => $y) {
        if (substr($key, 0, 1) !== ':') { throw new Exception("Query arguments must start with :"); }
        if (!is_scalar($y)) { throw new Exception("Only pass scalars to sql queries"); }
      }

      $params = array_merge($params, $x);
    } else {
      throw new Exception("Only pass strings or arrays");
    }
  }

  $sql = implode(' ', $sql);
  $statement = $pdo->prepare($sql);
  if (!$statement) { throw new Exception("Unable to prepare statement"); }
  if (!$statement->execute($params)) { throw new Exception("Unable to execute statement"); }
  return $statement;
}

function bestchat_get_messages() {
  return bestchat_query(
    "select * from `bc_msg`",
    "order by `when` desc",
    "limit 50"
  )->fetchAll();
}

function bestchat_add_message($content) {
  if (!bestchat_is_logged_in()) { return; }
  if (!is_string($content)) { return; }
  $content = @trim($content);
  if (strlen($content) <= 0) { return; }
  if (!preg_match('#^[a-z]+[a-z0-9_]*$#i', $_SESSION['nick'])) { bestchat_logout(); }

  bestchat_query(
    "insert into `bc_msg` set ",
    "   `when` = now(),       ",
    "   `sender` = :nick,     ",
    "   `content` = :content  ",
    [
      ':nick' => $_SESSION['nick'],
      ':content' => $content
    ]
  );
}
