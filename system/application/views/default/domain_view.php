<?php

$domainname	= isset($results['project']) ? $results['project'] : '';
$url		= isset($results['url']) ? $results['url'] : '';
$username	= isset($results['username']) ? $results['username'] : '';
$password	= isset($results['password']) ? $results['password'] : '';
$acctype	= isset($results['acctype']) ? $results['acctype'] : '';
$pwlength	= isset($results['pwlength']) ? $results['pwlength'] : '';
$changefreq	= isset($results['changefreq']) ? $results['changefreq'] : '';
$mark		= isset($results['mark']) ? $results['mark'] : '';
$notes		= isset($results['notes']) ? $results['notes'] : '';
$importance	= isset($results['importance']) ? $results['importance'] : '';
$password	= isset($results['password']) ? $results['password'] : '';
$domain_id	= isset($results['domain_id']) ? $results['domain_id'] : '';
# echo $domain_id ;
#print_r($results); 
?><div class="title title-spacing">
    <h2>View domain access</h2>
</div>


<ul>
<li><label class="desc">Domain Name</label>
    <div class="val" >
	<?= $domainname?>  
    </div>
</li>
<li><label class="desc">Type</label>
<div class="val" >
  	<?= $acctype ?>  
</div>
</li>
<li><label class="desc">Importance</label>
<div class="val" style="text-transform:capitalize"><?= $importance ?></div>
</li>
<li><label class="desc">URL</label>
<div class="val" >
  <?= $url ?>
</div>
</li>
<li><label class="desc">Username</label>
<div class="val" >
  <?= $username ?>
</div>
</li>
<li><label class="desc">Password</label>
<div class="val" >
  <?= $password ?>	
</div>
</li>
<li><label class="desc">PW Length</label>
<div class="val">
  <?= $pwlength ?>
</div>
</li>
<li><label class="desc">Change Frequency</label>
<div class="val" style="text-transform:capitalize" >
  <?= $changefreq ?>
</div>
</li>
<li><label class="desc">Mark</label>
<div class="val" >
  <?= $mark ?>
</div>
</li>
<li><label class="desc">Notes</label>
<div class="val" >
  <?= $notes ?>
</div>
</li>
<?php
if (isset($customfields)) {
	foreach($customfields as $k => $v ) {
 		echo form_label( substr($v->name,2), $v->name ,  array('class' => 'desc'));
		echo $results[$v->name] . "<br /><br />"  ;
?>
<?php
	} 
}
?>

</ul>
<br />
<?php if ($this->dx_auth->is_role('admin')) {?>
<label class="desc">Below are the people who had access on this domain.</label>
<div style="border:1px solid #ccc; padding:15px" >
<?php
$tmp = array();
if ($userprojects) {
foreach($userprojects as $k => $v) {
$name = $this->mdl_users->get_user_by_id($v['user_id']) ;
?>
<label><?= $name['firstname'] . ' ' .$name['lastname'] ?></label><br />
<?php } ?>
<?php } else { ?>
None
<?php } ?>
</div>
<?php } ?>
<br />
<input type="button" value="&laquo;Back" class="submit" onclick="history.go(-1)" />