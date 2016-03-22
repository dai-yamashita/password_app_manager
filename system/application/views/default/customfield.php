<div class="title title-spacing">
<h2>Create custom field in domain access table</h2>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<?php
$project 		= set_value('project');
$project		= !empty($project) ? $project : (isset($results['project']) ? $results['project'] : ''); 
$desc 			= set_value('desc');
$desc			= !empty($desc) ? $desc : (isset($results['desc']) ? $results['desc'] : ''); 


?>
<form action="<?php echo site_url('admin/domain/customfield') ?>" method="post" >
<ul>
    <li>
        <label  class="desc">Custom fieldname</label>
        <div><input type="text" tabindex="1" maxlength="255" value="<?= $project  ?>" class="field text small" name="fieldname" /></div>
    </li>
          
    <!--<li>
        <label  class="desc">Type</label>
        <div><select tabindex="3" class="field select small" name="domainname" >
<?php 
$type = array('text', 'textarea');
foreach($type as  $v) { ?>  
    <option value="<?= $v ?>"  ><?= strtoupper($v) ?></option>
<?php } ?>
  </select></div>
    </li>-->

    
    <li class="buttons">
        <input type="submit" value="Save" class="submit" />
        <input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/domain/browse')?>'" />
    </li>
</ul>
<input type="hidden" name="tablename" value="" />
</form>


<div class="hastable"  >

<table width="100%" border="1"  id="sort-table" >
<thead> 
  <tr>
    <th width="38%">Field</th>
    <th width="19%">Type</th>
    <th width="33%">&nbsp;</th>
  </tr>
</thead>  
<?php
$alt = 0;
if ($results) {

foreach($results as $v): 
$id = $v->name; 
?>
  <tr class="<?= (++$alt%2 == 0) ?  'alt' : '' ?>" >
    <td><?= substr($v->name,2) ?></td>
    <td><?= $v->type ?></td>
    <td>
    
      
      <a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Delete" href="<?php echo site_url("admin/domain/deletefield/$id") ?>" onclick="return confirm('Are you sure you want to delete?')" >
        <span class="ui-icon ui-icon-circle-close"></span></a>
      
    </td>
  </tr>
<?php endforeach; ?>  
<?php } else { ?>  
	<tr><td colspan="3" ><div align="center">No record found.</div></td></tr>
<?php } ?>
</table>
</div>






