<div class="title title-spacing">
<?php echo (!empty($results['projectid'])) ? '<h2>Assign user to domain access</h2>' :  '<h2>Assign user to domain access</h2>' ?>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<?php
$project 		= set_value('project');
$project		= !empty($project) ? $project : (isset($results['project']) ? $results['project'] : ''); 
$desc 			= set_value('desc');
$desc			= !empty($desc) ? $desc : (isset($results['desc']) ? $results['desc'] : ''); 
$projectid		= isset($results['domain_id']) ? $results['domain_id'] : 0;
$projectname	= isset($results['project']) ? $results['project'] : 0;
$url			= isset($results['url']) ? $results['url'] : '';
$type			= isset($results['acctype']) ? $results['acctype'] : '';
?>
<?php
if (!empty($projectid)) {
?>
<form action="<?php echo site_url('admin/domain/assign_user_project') ?>" method="post" >
<ul>
    <li>
        <label  class="desc">Domain name</label>
        <div>
          <h1 style="font-size:18px"><?php echo $projectname ?></h1>
        </div>
    </li>

    <li>
    <label  class="desc">Type</label>
    <?= $type ?>
    </li>
        
    <li>
    <label  class="desc">URL</label>
    <?= $url ?>
    </li>
    
<label class="desc">Select a people to give access on this domain.</label>
<div class="peopleaccess" >
<ul>
<?php
$tmp = array();
foreach($userprojects as $k => $v) $tmp[] = $v['user_id'];
foreach($allusers as $k => $v ) { 
$gidlist[] = $v['id'];
$id = $v['id'];

?>
<li style="padding:3px 0"><label><input type="checkbox" name="user_domains[]" value="<?= $id ?>" <?= in_array($id, $tmp) ? 'checked=checked' : '' ?>  /><?= $v['firstname'] . ' ' . $v['lastname']?></label></li>
<?php } ?>
</ul>
</div>
    
        
    
    <li class="buttons">
        <input type="submit" value="Save" class="submit" />
        <input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/domain/browse')?>'" />
    </li>
</ul>
<input type="hidden" name="domain_id" value="<?= $projectid ?>" />
<input type="hidden" name="gidlist" value="<?php echo implode(',', $gidlist) ?>" >
</form>

<?php } else { ?>
         
<?php } ?>