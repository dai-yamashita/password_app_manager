 
<div class="title title-spacing">
    <h2>All Groups</h2>
</div>

<ul class="toolbar2" >
 <li><a href="<?= site_url('admin/department/form') ?>" ><img src="<?= THEMEPATH_IMG ?>126_add.png" />Create Group</a></li>
</ul>
<div class="clearfix"></div>

<?php !empty($flash) ? flash($flash) : '' ?>

<div class="hastable" >
<table  id="sort-table" width="100%" border="0" >
<thead> 
  <tr>
    <th width="25%">Group</th>
    <th width="10%">Flag</th>
    <th width="10%">&nbsp;</th>
  </tr>
</thead>
<?php
if ($results) {
foreach($results as $v): 
$id = $v['deptid'];
?>
  <tr>
    <td><?php echo $v['groupname'] ?></td>
    <td><?php echo $v['visibility'] ?></td>
    <td>
      <a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Edit this group" href="<?php echo site_url("admin/department/form/$id") ?>" >
        <span class="ui-icon ui-icon-wrench"></span>
        </a>

<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="List all projects" href="<?php echo site_url("admin/department/projects/$id") ?>" >
<span class="ui-icon ui-icon-circle-zoomin"></span>
</a> 

<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="List all members" href="<?php echo site_url("admin/department/users/$id") ?>" >
<span class="ui-icon ui-icon-person"></span>
</a>

<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Add group to domain access" href="<?php echo site_url("admin/department/add_to_domain/$id") ?>" >
<span class="ui-icon ui-folder-collapsed"></span>
</a>

<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Add group to project" href="<?php echo site_url("admin/department/add_to_project/$id") ?>" >
<span class="ui-icon ui-icon-folder-collapsed"></span>
</a>
      
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip confirmdelete" title="Delete this group" href="<?php echo site_url("admin/department/delete/$id") ?>" onclick="return confirm('Are you sure you want to delete?')"  >
<span class="ui-icon ui-icon-circle-close"></span>
</a>
      
    </td>
  </tr>
<?php endforeach; ?> 
<?php } else { ?>  
	<tr><td colspan="3" ><div align="center">No record found.</div></td></tr>
<?php } ?>
</table>
</div>


<div class="clearfix"></div>
<ul class="pagination">
<?php echo isset($pagination) ? $pagination : '' ?>
</ul>

<div class="clearfix"></div>



