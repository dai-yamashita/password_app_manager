<div class="title title-spacing">
<?php echo (!empty($results['projectid'])) ? '<h2>Edit project</h2>' :  '<h2>Add project</h2>' ?>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<?php
$project 		= set_value('project');
$project		= !empty($project) ? $project : (isset($results['project']) ? $results['project'] : ''); 
$desc 			= set_value('desc');
$desc			= !empty($desc) ? $desc : (isset($results['desc']) ? $results['desc'] : ''); 

$visibility 		= set_value('visibility');
$visibility			= !empty($visibility) ? $visibility : (isset($results['visibility']) ? $results['visibility'] : ''); 


?>
<form action="<?php echo site_url('admin/project/form') ?>" method="post" >
<ul>
    <li>
        <label  class="desc">Project name</label>
        <div><input type="text" tabindex="1" maxlength="255" value="<?= $project  ?>" class="field text small" name="project" /></div>
    </li>
    
    <li>
    <label class="desc">Description</label>
    <div>
      <textarea name="desc" rows="3" cols="40" class="field textarea small" ><?= $desc ?></textarea>
    </div>
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
        <input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/project/browse')?>'" />
    </li>
</ul>
<input type="hidden" name="projectid" value="<?= !empty($results['projectid']) ? $results['projectid'] : '' ?>" />
</form>

