<div class="title title-spacing">
<h2>Request access new password</h2>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<?php
$domainname 		= set_value('domainname');
 
?>
<form action="<?php echo site_url('admin/domain/request_access') ?>" method="post" >
<ul>
    <li>
        <label  class="desc">Select a Project to request a password</label>
        <div>
<select tabindex="3" class="field select small" name="domainname" >
<?php foreach($allprojects as $k => $v) { ?>  
    <option value="<?= $v['projectid'] ?>" <?= ($v['projectid'] == $domainname) ? 'selected=selected' : '' ?> ><?= $v['project'] ?></option>
<?php } ?>
  </select>        
        </div>
    </li>
    
    <li class="buttons">
        <input type="submit" value="Request a password" class="submit" />
        <input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/main')?>'" />
    </li>
</ul>
<input type="hidden" name="projectid" value="<?= !empty($results['projectid']) ? $results['projectid'] : '' ?>" />
</form>

