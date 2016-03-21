<div class="title title-spacing">
<?php echo (!empty($results['deptid'])) ? '<h2>Edit group</h2>' :  '<h2>Add group</h2>' ?>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<?php
$department 		= set_value('department');
$department			= !empty($department) ? $department : (isset($results['groupname']) ? $results['groupname'] : ''); 
$visibility 		= set_value('visibility');
$visibility			= !empty($visibility) ? $visibility : (isset($results['visibility']) ? $results['visibility'] : ''); 

?>
<form action="<?php echo site_url('admin/department/form') ?>" method="post" >
<ul>
    <li>
        <label  class="desc">Group name</label>
        <div><input type="text" tabindex="1" maxlength="255" value="<?= $department ?>" class="field text small" name="department"   /></div>
    </li>
    <li>
        <label  class="desc">Flag</label>
        <div>
        <label class="desc" style="float:left"><input type="radio" name="visibility" value="public" <?= set_radio('visibility', 'public', ($visibility == 'public') ? true: false) ?> />Public</label>        
        <label class="desc" style="float:left"><input type="radio" name="visibility" value="private" <?= set_radio('visibility', 'private', ($visibility == 'private') ? true: false) ?> />Private</label>

        </div>
    </li>


        
    <li class="buttons">
        <input type="submit" value="Save" class="submit" />
        <input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/department/browse')?>'" />
    </li>
</ul>
<input type="hidden" name="deptid" value="<?= !empty($results['deptid']) ? $results['deptid'] : '' ?>" />
</form>

