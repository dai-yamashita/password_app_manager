<div class="title title-spacing">
    <h2>All alert messages</h2>
    
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
 	 
<div class="clearfix"></div>

<form method="post" action="<?= site_url("admin/alert/browse") ?>" >

<div class="hastable"  >
<div style="padding:10px 0" ><input type="submit" name="delete" value="Delete"  /></div>


<table width="100%" border="1"  id="sort-table" >
<thead> 
  <tr>
    <th width="4%" align="center" ><input type="checkbox" name="" value="" id="checkall" /></th>
    <th width="38%">Title</th>
    <th width="19%">Date created</th>
    <th width="6%">Status</th>
    <th width="33%">&nbsp;</th>
  </tr>
</thead>  
<?php
$alt = 0;

if ($results) {
foreach($results as $v): 
$id = $v['alertid'];
$created = $v['created'];
$created = date('F d Y g:i:s a', $created);
$status = ($v['isread'] == 0) ? 'unread' : 'read';

?>
  <tr class="<?= (++$alt%2 == 0) ?  'alt' : '' ?>" >
    <td align="center"><input type="checkbox" name="chk[]" value="<?= $id?>" /></td>
    <td><?= $v['title'] ?></td>
    <td><?= $created ?></td>
    <td class="<?= $status ?>" ><?= $status ?></td>
    <td>

<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="view this alert" href="<?php echo site_url("admin/alert/view/$id") ?>" >
<span class="ui-icon ui-icon-circle-zoomin"></span>
</a>     
       
  <a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Delete this alert" href="<?php echo site_url("admin/alert/delete/$id") ?>" onclick="return confirm('Are you sure you want to delete?')" >
  <span class="ui-icon ui-icon-circle-close"></span></a>
  
    </td>
  </tr>
<?php endforeach; ?>  
<?php } else { ?>  
	<tr><td colspan="5" ><div align="center">No record found.</div></td></tr>
<?php } ?>
</table>
</div>

</form>
<div class="clearfix"></div>
<ul class="pagination">
<?php echo isset($pagination) ? $pagination : '' ?>
</ul>

 