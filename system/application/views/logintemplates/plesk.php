		<form name="form2" action="/login_up.php3" method="post" onSubmit="return login_oC(document.forms[0], document.forms[1])">
		<table class="formFields" cellspacing="0" width="100%">
			<tr>
				<td class="name"><label for="fid-passwd"> Password</label></td>

				<td><INPUT maxlength="255" tabindex="2" name="passwd" id="fid-passwd" type="password" value="" size="25"></td>
			</tr>
			
			<tr>
				<td class="name"><label for="fid-locale">Interface language</label></td>
				<td><select  name="locale_id" id="fid-locale_id" onChange="locale_oC(document.forms[0], document.forms[1])">	<option value='default' SELECTED>Default</option>
	<option value='en-US'>ENGLISH (United States)</option>
	<option value='es-ES'>SPANISH (Spain)</option>

	<option value='fr-FR'>FRENCH (France)</option>
</select>
</td>
			</tr>
		</table>
			
		<input type="hidden" name="login_name" value="">
		
		</form>
