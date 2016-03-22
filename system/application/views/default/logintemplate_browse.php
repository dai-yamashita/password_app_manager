 
<div class="title title-spacing">
    <h2>All Login templates</h2>
</div>
<?php !empty($flash) ? flash($flash) : '' ?>
<div class="hastable" >
<table  id="sort-table" width="100%" border="0" >
<thead> 
  <tr>
    <th width="25%">Template name</th>
    <th width="10%">&nbsp;</th>
  </tr>
</thead>
<?php
if ($results) {
foreach($results as $v): 
$id = $v['templateid'];
?>
  <tr>
    <td><?php echo $v['name'] ?></td>
    <td>
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Edit this template" href="<?php echo site_url("admin/logintemplate/form/$id") ?>" >
<span class="ui-icon ui-icon-wrench"></span>
</a>

<a class="btn_no_text btn ui-state-default ui-corner-all tooltip confirmdelete" title="Delete this template" href="<?php echo site_url("admin/logintemplate/delete/$id") ?>" onclick="return confirm('Are you sure you want to delete?')"  >
<span class="ui-icon ui-icon-circle-close"></span>
</a>
      
    </td>
  </tr>
<?php endforeach; ?> 
<?php } else { ?>  
	<tr><td colspan="2" ><div align="center">No record found.</div></td></tr>
<?php } ?>
</table>
</div>


<div class="clearfix"></div>
<ul class="pagination">
<?php echo isset($pagination) ? $pagination : '' ?>
</ul>

<div class="clearfix"></div>



