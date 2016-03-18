<div class="title title-spacing">
<?php echo (!empty($results['user_id'])) ? '<h2>Edit user</h2>' :  '<h2>Add user</h2>' ?>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<?php
$user_id 		= !empty($results['user_id']) ? $results['user_id'] : '';

$username 		= set_value('username');
$username		= !empty($username) ? $username : (isset($results['username']) ? $results['username'] : ''); 

$password 		= set_value('password');
$password		= !empty($password) ? $password : (isset($results['clearpassword']) ? $results['clearpassword'] : ''); 
$pwlength 		= set_value('pwlength');
$pwlength		= !empty($pwlength) ? $pwlength : (isset($results['pwlength']) ? $results['pwlength'] : ''); 

$firstname 		= set_value('firstname');
$firstname		= !empty($firstname) ? $firstname : (isset($results['firstname']) ? $results['firstname'] : ''); 

$lastname 		= set_value('lastname');
$lastname		= !empty($lastname) ? $lastname : (isset($results['lastname']) ? $results['lastname'] : ''); 

$position 		= set_value('position');
$position		= !empty($position) ? $position : (isset($results['position']) ? $results['position'] : ''); 

$skypeid 		= set_value('skypeid');
$skypeid		= !empty($skypeid) ? $skypeid : (isset($results['skypeid']) ? $results['skypeid'] : ''); 

$email 			= set_value('email');
$email			= !empty($email) ? $email : (isset($results['email']) ? $results['email'] : ''); 

$type 			= set_value('type');
$type			= !empty($type) ? $type : (isset($results['role_id']) ? $results['role_id'] : '');

$deptid 		= set_value('deptid');
$deptid			= !empty($deptid) ? $deptid : (isset($results['deptid']) ? $results['deptid'] : ''); 

$tmpid 			= set_value('tmpid');	
$tmpid 			= !empty($tmpid) ? $tmpid : (isset($results['tmpid']) ? $results['tmpid'] : random_string('numeric')); 
$confirmation_code = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8
);
?>
<form action="<?php echo site_url('admin/account/editprofile') ?>" method="post" >
<ul>
    <li>
        <label  class="desc">ID</label>
        <div><input type="text" tabindex="1" maxlength="255" value="<?= $tmpid ?>" class="field text small" name="tmpid" readonly="readonly" /></div>
    </li>

    <li>
        <label  class="desc">Username</label>
        <div><input type="text" tabindex="1" maxlength="255" value="<?= $username ?>" class="field text small" name="username" /></div>
    </li>

<?php if ( ! $this->dx_auth->is_role('admin') || empty($user_id) ) { ?> 
    <li>
        <label class="desc">Password</label>
     </li>
<div id="pwdmanager" class="pwdmanager" >
 <table width="100%" border="0" >
   <tr>
    <td width="227"><input type="text" name="password" id="password" tabindex="1" maxlength="255" value="<?php echo $password ?>" style="width:200px" class="field text small"  /></td>
    <td width="120">Uppercase</td>
    <td width="115"><input type="checkbox" name="chartype" value="upper" id="chartype_upper"  checked="checked" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Lowercase</td>
    <td><input type="checkbox" name="chartype" value="lower" id="chartype_lower" checked="checked" /></td>
  </tr>
  <tr>
    <td><input type="button" id="regenerate" value="Regenerate"  /></td>
    <td>Numbers</td>
    <td><input type="checkbox" name="chartype" value="number" id="chartype_number" checked="checked" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Symbols</td>
    <td><input type="checkbox" name="chartype" value="symbol" id="chartype_symbol" checked="checked" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Password length</td>
    <td><select tabindex="3" class="field select  " name="pwlength" id="pwlength"> 
<?php 
$s = array('8', '12', '18', '24');
foreach($s as $k => $v): 
?>
<option value="<?= $v ?>" <?= ($v == $pwlength) ? 'selected=selected' : '' ?> ><?= $v ?></option>
<?php endforeach; ?>
</select></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>  
<?php } else { ?>  
    <li>
        <label class="desc">Password</label>
		<div>****************   </div>         
     </li>
<?php } ?>
    <li>
    	 <label class="desc">Firstname</label>
      <div>
            <input type="text" tabindex="1" maxlength="255" value="<?= $firstname ?>" class="field text small" name="firstname" />
        </div>
    </li>

    <li>
     <label class="desc">Lastname</label>
      <div>
            <input type="text" tabindex="1" maxlength="255" value="<?= $lastname ?>" class="field text small" name="lastname" />
        </div>
    </li>

    <li>
     <label class="desc">Position</label>
      <div>
            <input type="text" tabindex="1" maxlength="255" value="<?= $position ?>" class="field text small" name="position" />
        </div>
    </li>

	<li>
     <label class="desc">Email</label>
      <div>
            <input type="text" tabindex="1" maxlength="255" value="<?= $email ?>" class="field text small" name="email" />
        </div>
    </li>
    
    <li>
     <label class="desc">Skype ID</label>
      <div>
            <input type="text" tabindex="1" maxlength="255" value="<?= $skypeid ?>" class="field text small" name="skypeid" />
        </div>
    </li>
<?php if ( $this->dx_auth->is_admin() ) { ?>
   <li>
        <label  class="desc">
            Type
        </label>
        <div>
        

            <select tabindex="3" class="field select small" name="type" > 
<?php 
foreach($roles as $k => $v): 
?>
<option value="<?= $v['id'] ?>" <?= ($v['id'] == $type) ? 'selected=selected' : '' ?> ><?= $v['name'] ?></option>
<?php endforeach; ?>    
            </select>
        </div>
    </li>
<?php } ?>
     
        
    <li class="buttons">
        <input type="submit" value="Save" class="submit" />
        <input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/user/browse')?>'" />
    </li>
</ul>
<input type="hidden" name="user_id" value="<?= $user_id ?>" />
</form>

<script type="text/javascript" src="<?= base_url() ?>js/jquery.pstrength.js" ></script>
<script type="text/javascript">
params = new Object()
$('#regenerate').click(function(){
 	params.chartype_upper 	= $('#chartype_upper').attr('checked' )
	params.chartype_lower 	= $('#chartype_lower').attr('checked' )
	params.chartype_number = $('#chartype_number').attr('checked' )
	params.chartype_symbol = $('#chartype_symbol').attr('checked' )	
	params.pwlength 		= $('#pwlength').attr('value', this.text )	
	$("#password").triggerHandler("change");		
	passwordgenerator(params)
});

$(function() {
$('#password').pstrength();
});
</script>