<?php
$siteurl = "admin/mydomain";
?>
<div class="title title-spacing">
<?php echo (!empty($results['domain_id'])) ? '<h2>Edit personal domain access</h2>' :  '<h2>Add personal domain access</h2>' ?>    
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<?php
$domain_id = !empty($results['domain_id']) ? $results['domain_id'] : '';

$domainname 	= set_value('domainname');
$domainname		= !empty($domainname) ? $domainname : (isset($results['project_id']) ? $results['project_id'] : '');
$project		= (isset($results['project']) ? $results['project'] : '');
$importance 	= set_value('importance');
$importance		= !empty($importance) ? $importance : (isset($results['importance']) ? $results['importance'] : '');
$url 			= set_value('url');
$url			= !empty($url) ? $url : (isset($results['url']) ? $results['url'] : '');
$loginurl 		= set_value('loginurl');
$loginurl		= !empty($loginurl) ? $loginurl : (isset($results['loginurl']) ? $results['loginurl'] : '');

$username 		= set_value('username');
$username		= !empty($username) ? $username : (isset($results['username']) ? $results['username'] : '');
$password 		= set_value('password');
$password		= !empty($password) ? $password : (isset($results['password']) ? $results['password'] : '');
$changefreq 	= set_value('changefreq');
$changefreq		= !empty($changefreq) ? $changefreq : (isset($results['changefreq']) ? $results['changefreq'] : '');
$pwlength 		= set_value('pwlength');
$pwlength		= !empty($pwlength) ? $pwlength : (isset($results['pwlength']) ? $results['pwlength'] : '');
$type 			= set_value('type');
$type			= !empty($type) ? $type : (isset($results['type']) ? $results['type'] : '');
$templateid 	= set_value('templateid');
$templateid		= !empty($templateid) ? $templateid : (isset($results['templateid']) ? $results['templateid'] : '');

$customtemplate = set_value('customtemplate');
$customtemplate	= !empty($customtemplate) ? $customtemplate : (isset($results['customtemplate']) ? $results['customtemplate'] : '');

$notes 			= set_value('notes');
$notes			= !empty($notes) ? $notes : (isset($results['notes']) ? $results['notes'] : '');
$mark 			= set_value('mark');
$mark			= !empty($mark) ? $mark : (isset($results['mark']) ? $results['mark'] : '');


#echo '<br >last_modified '. date('F d Y g:i a', $results['last_modified']) ;
#echo '<br >expirydate '. date('F d Y g:i a', $results['expirydate']) ;
?>
<form action="<?php echo site_url( "$siteurl/form/$domain_id") ?>" method="post" >
<ul>
<li><label class="desc">Domain Name</label>
<?php if ( isset($results['domain_id'])) { ?>
<h2><?php echo $project ?></h2>
<input type="hidden" name="domainname" value="<?= $domainname ?>"  />
<?php } else { ?>
    <div>
<select tabindex="3" class="field select small" name="domainname" >
<?php foreach($allprojects as $k => $v) { ?>  
    <option value="<?= $v['projectid'] ?>" <?= ($v['projectid'] == $domainname) ? 'selected=selected' : '' ?> ><?= $v['project'] ?></option>
<?php } ?>
  </select>
<!--    <input type="text" name="domainname" tabindex="1" maxlength="255" value="<?= $domainname?>" class="field text small" />-->
    </div>
<?php } ?>      
</li>
<li><label class="desc">Type</label>
<div>
  <select tabindex="3" class="field select small" name="type" >
<?php foreach($account_types as $k => $v) { ?>  
    <option value="<?= $v['type_id'] ?>" <?= ($v['type_id'] == $type) ? 'selected=selected' : '' ?> ><?= $v['acctype'] ?></option>
<?php } ?>
  </select>
</div>
</li>
 
<li><label class="desc">Importance</label>
<div><select tabindex="3" class="field select small" name="importance" > 
<?php 
$s = array('critical', 'high', 'medium', 'low' );
foreach($s as $k => $v): 
?>
<option value="<?= $v ?>" <?= ($v == $importance) ? 'selected=selected' : '' ?> ><?= $v ?></option>
<?php endforeach; ?>
</select></div>
</li>
<li><label class="desc">Homepage URL</label><em>(e.g mysite.com)</em><br /><br />
<div><input type="text" name="url" tabindex="1" maxlength="255" value="<?= $url ?>" class="field text small" /></div>
</li>
<li><label class="desc">Login URL</label><em>(Full path including the base URL e.g mysite.com/login.php)</em><br /><br />
<div><input type="text" name="loginurl" tabindex="1" maxlength="255" value="<?= $loginurl ?>" class="field text small" /></div>
</li>
<li><label class="desc">Username or Email address</label>
  
  <div><input type="text" name="username" tabindex="1" maxlength="255" value="<?php echo !empty($username) ? $username : (isset($results['username']) ? $results['username'] : '') ?>" class="field text small" /></div>
</li>
<li><label class="desc">Password&nbsp; </label></li>
<div id="pwdmanager" class="pwdmanager" >
 <table width="100%" border="0" >
   <tr>
    <td width="227"><input type="text" name="password" id="password" tabindex="1" maxlength="255" value="<?php echo !empty($password) ? $password : (isset($results['password']) ? $results['password'] : '') ?>" style="width:200px" class="field text small"  /></td>
    <td width="120">Uppercase</td>
    <td width="115"><input type="checkbox" name="chartype" value="upper" id="chartype_upper" checked="checked"  /></td>
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

<li><label class="desc">Change Frequency</label>
<div>



<select tabindex="3" class="field select small" name="changefreq" > 
<?php 
$s = array('hourly', 'daily', 'weekly', 'bi-weekly', 'monthly', 'quarterly', 'half-yearly', 'annually', 'bi-annually');
foreach($s as $k => $v): 
?>
<option value="<?= $v ?>" <?= ($v == $changefreq) ? 'selected=selected' : '' ?> ><?= $v ?></option>
<?php endforeach; ?>
</select>
</div>
</li>
<!--<li>
<label class="desc">First Logged </label>
<div><input type="text" name="first_log" tabindex="1" maxlength="255" value="" class="field text small" /></div>
</li>
<li><label class="desc">Last Changed</label>
<div><input type="text" name="last_modified" tabindex="1" maxlength="255" value="" class="field text small" /></div>
</li>-->
<li><label class="desc">Mark</label>
<div>
  <textarea name="mark" rows="3" cols="40" class="field textarea small" ><?= $mark ?></textarea>
</div>
</li>
<li><label class="desc">Notes</label>
<div><textarea name="notes" rows="3" cols="40" class="field textarea small" ><?= $notes ?></textarea></div>
</li>

<?php
if ( isset($customfields)) {
	foreach($customfields as $v ) {
	echo form_label( substr($v->name,2), '',  array('class' => 'desc'));
	echo form_input( $v->name , (isset($results[$v->name]) ? $results[$v->name] : ''), ' class="field text small"' );
?>
<?php
	} 
}
?>

<div id="domain_customfield" style="border:0px solid red" >
    <li><label class="desc">Login template</label>
    <div>
      <select tabindex="3" class="field select small" name="templateid" id="templateid" >
      <option value="-1"  >- Select -</option>
		<option value="-100" <?= ( $templateid == -100) ? 'selected=selected' : '' ?> >Custom template</option>      
    <?php foreach($alllogintemplates as $k => $v) { ?>  
        <option value="<?= $v['templateid'] ?>" <?= ($v['templateid'] == $templateid) ? 'selected=selected' : '' ?> ><?= $v['name'] ?></option>
    <?php } ?>
      </select> 
    </div>

	
    <div id="createlogin" <?= !empty($customtemplate) ? 'style="display:block"' : 'style="display:none"' ?> >
	<textarea style="width:500px; " rows="10" name="customtemplate"  ><?php
	$defaulttemplate = '&lt;form method=&quot;post&quot; action=&quot;{loginurl}&quot; &gt;<br />
&lt;label&gt;username&lt;/label&gt;&lt;br /&gt;<br />
&lt;input type=&quot;text&quot; name=&quot;username&quot; value=&quot;{username}&quot; /&gt;&lt;br /&gt;&lt;br /&gt; 
&lt;label&gt;password&lt;/label&gt;&lt;br /&gt;<br />
&lt;input type=&quot;text&quot; name=&quot;password&quot; value=&quot;{password}&quot; /&gt;&lt;br /&gt; 
&lt;input type=&quot;submit&quot; name=&quot;submit&quot; value=&quot;Submit&quot; /&gt;<br />
&lt;/form&gt;';	
	if (!empty($customtemplate)) echo $customtemplate ;
	else echo $defaulttemplate;
	?>    
    </textarea>
<p>Variable must enclosed with open and close braces {}. <br />
Default value tags: <br />
username = {username} <br />
password = {password} <br />
Login URL = {loginurl}<br />
<br />
To use the custom login fields, you must include it in the custom template. <br />
Must be enclosed with open and close braces {} .
</p>    
<div class="title title-spacing" style="margin-bottom:0;" >
<h2>Define custom login fields for this domain</h2>
</div>

<?php if ($domain_id ) { ?>
	<li>
    <label  class="desc">Custom fieldname</label>
    <div><input type="text" tabindex="1" maxlength="255" value="" class="field text small" name="domain_customfield" />
	<input type="submit" name="submit_domain_customfield" value="Add custom"  />    
    </div>
    </li>
    
<div class="hastable"  >
<table width="100%" border="1"  id="sort-table domaincustomfield" >
<thead> 
  <tr>
    <th width="23%">Field name</th>
    <th width="77%">Value</th>
    <th width="77%">&nbsp;</th>
    </tr>
</thead>  
<?php
$alt = 0;
if (count($domain_customfields)>0) {
foreach($domain_customfields as $k => $v): 
$customfieldid = $v['customfieldid'] ;
$customfield = $v['customfield'] ;
?>
  <tr class="<?= (++$alt%2 == 0) ?  'alt' : '' ?>" >
    <td><input type="hidden" name="customfieldids[<?= $customfieldid ?>]" value="<?= $customfieldid ?>"  /> <?php echo isset($v['customfield']) ? $v['customfield'] : '' ?></td>
    <td><input type="text" name="customfieldvalues[<?= $customfieldid ?>]" value="<?= $v['value'] ?>" class="field text large"  /> </td>
    <td>
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Delete this access" href="<?php echo site_url("$siteurl/delete_customfield/$customfieldid") ?>" onclick="return confirm('Are you sure you want to delete?')" >
<span class="ui-icon ui-icon-circle-close"></span>
</a>

    </td>    
  </tr>
  <?php endforeach; ?>  
<?php } else { ?>
<tr><td colspan="3"><div align="center" >No field found</div></td></tr>
<?php } ?>

</table>
</div>
<?php } ?>
    </div><!-- createlogin -->
    </li>
    
<?php if (!$domain_id) { ?>
<p style="background:#FF9">NOTE: After saving the data, you can add custom login fields when it is needed.</p>
<?php } ?>
 

    <li class="buttons">
    <input type="submit" name="submit" value="Save"  />
    <input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url("$siteurl/browse")?>'" />
    </li>
</ul>
<input type="hidden" name="domain_id" value="<?= $domain_id ?>" />
<input type="hidden" name="logged_userid" value="<?= $this->logged_userid ?>" />

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

$("#templateid").change(function () {
	var str = "";
	$("#templateid option:selected").each(function () {
		str = $(this).val() + " ";
	});
	if (str == -100) {
		$('#createlogin').css("display", "block")
	}else{
		$('#createlogin').css("display", "none")
	}
	//if (str)
});

</script>

