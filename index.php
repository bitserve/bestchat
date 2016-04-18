<?php

include __DIR__.'/private/bestchat.php';

if (@$_POST['logout']) {
  bestchat_logout();
  header("Location: /");
  exit(0);
}

if (@$_POST['login']) {
  $_SESSION['loginError'] = bestchat_login();
  header("Location: /");
  exit(0);
}

if (isset($_POST['content'])) {
  bestchat_add_message($_POST['content']);
  header("Location: /");
  exit(0);
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>bestchat</title>
<meta http-equiv="refresh" content="2400">
<style>

html, body {
  padding: 0;
  margin: 0;
  font-family: Arial, sans-serif;
  font-size: 16px;
  background: #fff;
  color: #333;
}

#hd-box {
  position: fixed;
  left: 0;
  right: 0;
  top: 0;
  height: 100px;
}

#bd-box {
  position: fixed;
  left: 0;
  right: 0;
  top: 100px;
  bottom: 100px;
}

#ft-box {
  position: fixed;
  left: 0;
  right: 0;
  bottom: 0;
  height: 100px;
}

iframe {
  background: #fff;
  border: none;
  width: 100%;
  height: 100%;
}

#menu {
  float: right;
  padding: 4px;
}

input {
  display: inline-block;
  height: 16px;
  padding: 3px;
  border-radius: 4px;
  background: #fff;
  color: #222;
  border: 1px solid #aaa;
}

#login-form {
  text-align: center;
  width: 80%;
  margin: auto;
}

</style>
</head>
<body>

<div id="hd-box">
  <div id="menu">
    <div>
      <form method="post">
        <button name="logout" value="1">Logout</button>
      </form>
    </div>
  </div>

  <?php include __DIR__.'/private/header.php'; ?>
</div>

<div id="bd-box">
  <iframe name="bd" id="bd" src="bd.php#bottom"></iframe>
</div>

<div id="ft-box">
<?php

if (bestchat_is_logged_in()) {
  include __DIR__.'/private/input.php';
} else {
  include __DIR__.'/private/login.php';
}

?>
</div>

</body>
</html>
