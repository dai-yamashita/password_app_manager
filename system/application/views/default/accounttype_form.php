<div class="title title-spacing">
<?php echo (!empty($results['type_id'])) ? '<h2>Edit account type</h2>' :  '<h2>Add account type</h2>' ?>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<?php
$acctype 		= set_value('acctype');
$acctype		= !empty($project) ? $project : (isset($results['acctype']) ? $results['acctype'] : ''); 
$desc 			= set_value('desc');
$desc			= !empty($desc) ? $desc : (isset($results['desc']) ? $results['desc'] : ''); 


?>
<form action="<?php echo site_url('admin/accounttype/form') ?>" method="post" >
<ul>
    <li>
        <label  class="desc">Account type</label>
        <div><input type="text" tabindex="1" maxlength="255" value="<?= $acctype  ?>" class="field text small" name="acctype" /></div>
    </li>
    
    <li>
    <label class="desc">Description</label>
    <div>
      <textarea name="desc" rows="3" cols="40" class="field textarea small" ><?= $desc ?></textarea>
    </div>
    </li>    
    
    <li class="buttons">
        <input type="submit" value="Save" class="submit" />
        <input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/accounttype/browse')?>'" />
    </li>
</ul>
<input type="hidden" name="type_id" value="<?= !empty($results['type_id']) ? $results['type_id'] : '' ?>" />
</form>

