<form name="loginform" id="loginform" action="http://localhost/attorney/wp-login.php" method="post">
	<p>
		<label>Username<br />
		<input type="text" name="log" id="user_login" class="input" value="" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label>Password<br />
		<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>

	</p>
	<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" /> Remember Me</label></p>
	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" value="Log In" tabindex="100" />
		<input type="hidden" name="redirect_to" value="http://localhost/attorney/wp-admin/" />
		<input type="hidden" name="testcookie" value="1" />
	</p>
</form>

