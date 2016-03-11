<form action="/login/" method="post" >
    <input type="hidden" name="login_theme" value="cpanel" />
    <table width="200" class="login" cellpadding="0" cellspacing="0">
        <tr>
            <td align="left"><b>Login</b></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Username</td>

            <td><input id="user" type="text" name="user" size="16" tabindex="1" /></td>
        </tr>
        <tr class="row2">
            <td>Password</td>
            <td><input id="pass" type="password" name="pass" size="16" tabindex="2" /></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center"><input type="submit" value="Login" class="input-button" tabindex="3" /></td>
         </tr>
    </table>
    <input type="hidden" name="goto_uri" value="/?" />
</form>
