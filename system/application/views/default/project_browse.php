 
<div class="title title-spacing">
    <h2>All projects</h2>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<ul class="toolbar2" >
 <li><a href="<?= site_url('admin/project/form') ?>" ><img src="<?= THEMEPATH_IMG ?>126_add.png" />Create Project</a></li>
</ul>
<div class="clearfix"></div>

<div class="hastable" >
<table  id="sort-table" width="100%" border="0" >
<thead> 
  <tr>
    <th width="23%">Project name</th>
    <th width="38%">Description</th>
    <th width="6%">Flag</th>
    <th width="10%"># of domains</th>
    <th width="23%">&nbsp;</th>
  </tr>
</thead>
<?php
if (count($results)>0) {
foreach($results as $v): 
$id = $v['projectid'];
$list = $this->mdl_projects->get_all_projectdomains($v['projectid']);
$nitems = count($list);
?>
  <tr>
    <td><?php echo $v['project'] ?></td>
    <td><?php echo $v['desc'] ?></td>
    <td><?php echo $v['visibility'] ?></td>
    <td><div align="center"><?php echo $nitems; ?></div></td>
    <td>
      
  <a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Edit this project" href="<?php echo site_url("admin/project/form/$id") ?>" >
  <span class="ui-icon ui-icon-wrench"></span>
  </a>

<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="View project domain access" href="<?php echo site_url("admin/project/view/$id") ?>" >
<span class="ui-icon ui-icon-circle-zoomin"></span>
</a>

  <a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Delete this project" href="<?php echo site_url("admin/project/delete/$id") ?>" onclick="return confirm('Are you sure you want to delete?')" >
  <span class="ui-icon ui-icon-circle-close"></span></a>

    
    </td>
  </tr>
<?php endforeach; ?> 
<?php } else { ?>
  <tr>
    <td colspan="5"><div align="center">No records found</div></td>
  </tr>
<?php } ?>
</table>
</div>


<div class="clearfix"></div>
<ul class="pagination">
<?php echo isset($pagination) ? $pagination : '' ?>
</ul>

<div class="clearfix"></div>


<div id="dialog2" title="Dialog Title" style="display:none" >
	<p>Delete this image</p>
</div>