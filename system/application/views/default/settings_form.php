<div class="title title-spacing">
<h2>Admin settings</h2>
</div>
<?php !empty($flash) ? flash($flash) : '' ?>
<?php
$account_expired_message 		= set_value('account_expired_message');
$account_expired_message		= !empty($account_expired_message) ? $account_expired_message : (isset($results['account_expired_message']) ? $results['account_expired_message'] : ''); 
$use_captcha 					= set_value('use_captcha');
$use_captcha					= !empty($use_captcha) ? $use_captcha : (isset($results['use_captcha']) ? $results['use_captcha'] : ''); 

$admin_email 					= set_value('admin_email');
$admin_email					= !empty($admin_email) ? $admin_email : (isset($results['admin_email']) ? $results['admin_email'] : ''); 

?>

<form action="<?php echo site_url('admin/settings/form') ?>" method="post" >
<ul>

    <li>
        <label  class="desc">Admin email address</label>
        <div>
<input type="text" tabindex="1" maxlength="255" value="<?= $admin_email  ?>" class="field text small" name="admin_email" />        
        </div>
    </li>
    
    
    <li>
        <label  class="desc">Alert message for expired password</label>
        <div>
<textarea name="account_expired_message" rows="5" cols="40" class="field textarea small" style="height:100px" ><?= $account_expired_message ?></textarea>
        </div>
    </li>

    
    <li>
    <label class="desc">Enable CAPTCHA in login form</label>
	<label><input type="radio" name="use_captcha" value="yes" <?= $use_captcha == 'yes' ? 'checked=checked' : '' ?> />YES</label>
   	<label><input type="radio" name="use_captcha" value="no" <?= $use_captcha == 'no' ? 'checked=checked' : '' ?> />NO</label>
    </li>    
    
    <li class="buttons">
        <input type="submit" value="Save" class="submit" />
        <input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/main')?>'" />
    </li>
</ul>


</form>

