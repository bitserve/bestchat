<style>
#login-form {
  line-height: 50px;
  height: 50px;
  vertical-align: middle;
}

#login-form img {
  display: inline-block;
  line-height: 50px;
  vertical-align: middle;
}

#login-form label {
  display: inline-block;
  height: 50px;
  line-height: 50px;
  vertical-align: middle;
}

#login-form input {
  display: inlin-block;
  width: 6em;
}

#login-hint {
  text-align: center;
  color: #555;
  font-style: italic;
}

#login-error {
  margin: auto;
  width: 200px;
  text-align: center;
  color: #800;
  font-weight: bold;
}
</style>

<form id="login-form" method="post">
  <label>
    Nick:
    <input type="text" name="nick" id="nick" autocomplete="off">
  </label>

  <label>
    Password:
    <input type="password" name="pass" id="pass" autocomplete="off">
  </label>

  <label>
    Captcha:
    <img width="100" height="50" src="captcha.php"></img>
    <input type="text" name="captcha" autocomplete="off">
  </label>

  <button name="login" id="login" value="1">Login</button>
</form>

<?php if (@strlen($_SESSION['loginError'])): ?>
<div id="login-error">
  <?php echo htmlspecialchars($_SESSION['loginError']) ?>
</div>
<?php else: ?>
<div id="login-hint">
  Pick a nick (at least 4 chars) and password (at least 6 chars)
</div>
<?php endif; ?>
