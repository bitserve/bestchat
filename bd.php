<?php

include __DIR__.'/private/bestchat.php';

$messages = bestchat_get_messages();
$hash = bestchat_get_hash($messages);
bestchat_check_etag($messages, $hash);

$me = @trim($_SESSION['nick']);

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="5">
<style>

html, body {
  padding: 0;
  margin: 0;
  font-family: Arial, sans-serif;
  font-size: 16px;
  background: #fff;
  color: #333;
}

.msg {
  padding: 3px;
}

.msg:hover {
  background: #eee;
}

.when {
  color: #888;
  float: right;
}

.content {
  display: block;
  margin-left: 10em;
  line-height: 20px;
}

.sender {
  position: absolute;
  width: 10em;
  text-align: right;
}

.sender span {
  margin-right: 3px;
  display: inline-block;
  font-weight: bold;
  background: #ddd;
  color: #555;
  padding: 2px 4px;
  border-radius: 3px;
}

.mention {
  background: #ffc;
}

</style>
</head>
<body>

<? foreach (array_reverse($messages) as $msg):
  $class = "msg";

  if (strlen($me)) {
    if (stripos($msg->content, "@$me") !== false) {
      $class = "$class mention";
    }
  }

?>
<div class="<?php echo $class ?>">
  <span class="when"><?php echo htmlspecialchars($msg->when) ?></span>
  <span class="sender"><span><?php echo htmlspecialchars($msg->sender) ?></span></span>
  <span class="content"><?php echo htmlspecialchars($msg->content) ?></span>
</div>
<? endforeach; ?>

<div id="bottom"></div>
<div id="<?php echo "b_$hash" ?>"></div>

</body>
</html>
