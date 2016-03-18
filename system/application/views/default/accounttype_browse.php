 
<div class="title title-spacing">
    <h2>All Account types</h2>
</div>
<?php !empty($flash) ? flash($flash) : '' ?>

<ul class="toolbar2" >
<li><a href="<?= site_url('admin/accounttype/form') ?>" ><img src="<?= THEMEPATH_IMG ?>126_add.png" />Create login templates</a></li>
<li><a href="<?= site_url('admin/accounttype/form') ?>" ><img src="<?= THEMEPATH_IMG ?>126_add.png" />Create account-type</a></li>
</ul>

<div class="clearfix"></div> 

<div class="hastable" >
<table  id="sort-table" width="100%" border="0" >
<thead> 
  <tr>
    <th width="25%">Project name</th>
    <th width="27%">Description</th>
    <th width="15%">&nbsp;</th>
  </tr>
</thead>
<?php
foreach($results as $v): 
$id = $v['type_id'];
?>
  <tr>
    <td><?php echo $v['acctype'] ?></td>
    <td><?php echo $v['desc'] ?></td>
    <td>
     
  <a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Edit this account type" href="<?php echo site_url("admin/accounttype/form/$id") ?>" >
  <span class="ui-icon ui-icon-wrench"></span>
  </a>

<!--<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="view full details" href="<?php echo site_url("admin/domain/view/$id") ?>" >
<span class="ui-icon ui-icon-circle-zoomin"></span>
</a> -->

  <a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Delete this account type" href="<?php echo site_url("admin/accounttype/delete/$id") ?>" onclick="return confirm('Are you sure you want to delete?')" >
  <span class="ui-icon ui-icon-circle-close"></span></a>
      
    </td>
  </tr>
<?php endforeach; ?> 
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