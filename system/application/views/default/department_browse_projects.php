<div class="title title-spacing">
    <?php echo (isset($domainaccess_title) && $domainaccess_title) ? "<h2>$domainaccess_title</h2>" : "<h2>All domains access</h2>" ?>
</div>
   
<div class="clearfix"></div>

<div class="hastable" >
<table  id="sort-table" width="100%" border="0" >
<thead> 
  <tr>
    <th width="31%">Project name</th>
    <th width="69%">Description</th>
    </tr>
</thead>
<?php
if ($results) {
foreach($results as $v): 
$id = $v['projectid'];
?>
  <tr>
    <td><?php echo $v['project'] ?></td>
    <td><?php echo $v['desc'] ?></td>
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


<div id="dialog2" title="Dialog Title" style="display:none" >
	<p>Delete this image</p>
</div>