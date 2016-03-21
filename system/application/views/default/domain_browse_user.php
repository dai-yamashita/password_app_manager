<div class="title title-spacing">
    <h2>Domain access</h2>
    
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
	
<div class="clearfix"></div>

<?php
$uri = $this->uri->uri_to_assoc(2);
$uri['field'] = isset($uri['field']) ? $uri['field'] : '';

$arrow1 = (($uri['field'] == 'project') && $sortby == 'asc') ? 'arrowdown' : (($uri['field'] == 'project') ? 'arrowup' : '') ; 
$arrow2 = (($uri['field'] == 'acctype') && $sortby == 'asc') ? 'arrowdown' : (($uri['field'] == 'acctype') ? 'arrowup' : '') ; 
$arrow3 = (($uri['field'] == 'changefreq') && $sortby == 'asc') ? 'arrowdown' : (($uri['field'] == 'changefreq') ? 'arrowup' : '') ; 
?>
	<div class="groupby2" >
    <ul>
<!--    <li><a href="<?php echo site_url('admin/domain/department') ?>" >Group by Department</a></li>
-->    <li><a href="<?php echo site_url("admin/domain/browse/field/project/sort/$sortby") ?>" class="<?= $arrow1 ?>" >Group by Project</a></li>
    <li><a href="<?php echo site_url("admin/domain/browse/field/acctype/sort/$sortby") ?>" class="<?= $arrow2 ?>" >Group by Type</a></li>
    <li><a href="<?php echo site_url("admin/domain/browse/field/changefreq/sort/$sortby") ?>" class="<?= $arrow3 ?>" >Group by Frequency</a></li>    
    </ul>
    </div>
   <div class="clearfix"></div>
 
<div class="hastable"  >
<table width="100%" border="1"  id="sort-table" >
<thead> 
  <tr>
    <th width="14%">Domain</th>
    <th width="9%">Type</th>
    <th width="8%">Importance</th>
    <th width="13%">URL</th>
    <th width="11%">Username</th>
    <th width="8%">Change<br />frequency </th>
    <th width="8%">Last Changed</th>
    <th width="8%">&nbsp;</th>
   </tr>
</thead>  
<?php
if ($results) {
foreach($results as $v): 
$id = $v['domain_id'];
$last_modified = ! empty($v['last_modified']) ? date('M d, Y g:i a', $v['last_modified']) : '' ;

?>
  <tr>
    <td><?= $v['project'] ?></td>
    <td><?= $v['acctype'] ?></td>
    <td><?= $v['importance'] ?></td>
    <td><?= $v['url'] ?></td>
    <td><?= $v['username'] ?></td>
    <td><?= $v['changefreq'] ?></td>
    <td><?= $last_modified ?></td>
    <td>
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="view full details" href="<?php echo site_url("admin/domain/view/$id") ?>" >
<span class="ui-icon ui-icon-wrench"></span>
</a>      
    </td>
  </tr>
<?php endforeach; ?>  
<?php } else { ?>
	<tr><td colspan="8"><div align="center">No record found.</div></td></tr>
<?php } ?>
</table>
</div>

<div class="clearfix"></div>
<ul class="pagination">
<?php echo isset($pagination) ? $pagination : '' ?>
</ul>

 