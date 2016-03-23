<div class="title title-spacing">
    <?php echo (isset($domainaccess_title) && $domainaccess_title) ? "<h2>$domainaccess_title</h2>" : "<h2>All domains access</h2>" ?>
    
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<?php
$uri = $this->uri->uri_to_assoc(2);
$uri['field'] = isset($uri['field']) ? $uri['field'] : '';

$arrow1 = (($uri['field'] == 'project') && $sortby == 'asc') ? 'arrowdown' : (($uri['field'] == 'project') ? 'arrowup' : '') ; 
$arrow2 = (($uri['field'] == 'acctype') && $sortby == 'asc') ? 'arrowdown' : (($uri['field'] == 'acctype') ? 'arrowup' : '') ; 
$arrow3 = (($uri['field'] == 'changefreq') && $sortby == 'asc') ? 'arrowdown' : (($uri['field'] == 'changefreq') ? 'arrowup' : '') ; 
?>




	<div class="groupby" >
    <ul>
<!--    <li><a href="<?php echo site_url('admin/domain/department') ?>" >Group by Department</a></li>
-->    <li><a href="<?php echo site_url("admin/user/domainaccess/field/project/sort/$sortby") ?>" class="<?= $arrow1 ?>" >Group by Project</a></li>
    <li><a href="<?php echo site_url("admin/user/domainaccess/field/acctype/sort/$sortby") ?>" class="<?= $arrow2 ?>" >Group by Type</a></li>
    <li><a href="<?php echo site_url("admin/user/domainaccess/field/changefreq/sort/$sortby") ?>" class="<?= $arrow3 ?>" >Group by Frequency</a></li>    
    </ul>
    </div>

   
<form method="post" action="<?= site_url("admin/user/domainaccess/$userid") ?>" >   

<div style="float:left;">
<input type="submit" value="Update domain access" class="submit" />
<input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/user/browse')?>'" />
<p>Select a domain to give him access or Uncheck the checkbox to remove his access</p>
</div>
<div class="clearfix"></div>

<div class="hastable"  >
<table width="100%" border="1"  id="sort-table" >
<thead> 
  <tr>
    <th width="3%" ><input type="checkbox" name="checkall" value="" id="checkall" checked=checked /></th>
    <th width="21%">Domain</th>
    <th width="4%">Type</th>
    <th width="9%">Importance</th>
    <th width="13%">URL</th>
    <th width="8%">Username</th>
    <th width="8%">Change<br />frequency </th>
    <th width="10%">Expire</th>
    <th width="20%">Last Changed</th>
    <th width="4%">&nbsp;</th>
  </tr>
</thead>  
<?php
$alt = 0;
$gidlist = array();
if ($results) {
foreach($results as $v): 
$id = $v['domain_id'];
$gidlist[] = $v['domain_id'];
$expirydate = date('M d, Y g:i a', $v['expirydate']) ;
$ischecked = (in_array($id, $results2)) ? " checked " : '' ;
$lastmodified  = ($v['last_modified'] == 0 || empty($v['last_modified']))  ? '' : date('M d, Y g:i a', $v['last_modified']);
?>
  <tr class="<?= (++$alt%2 == 0) ?  'alt' : '' ?>">
    <td><input type="checkbox" name="chk[]" value="<?= $id ?>" <?php echo $ischecked ?>   /></td>
    <td><?= $v['project'] ?></td>
    <td><?= $v['acctype'] ?></td>
    <td><?= $v['importance'] ?></td>
    <td><?= $v['url'] ?></td>
    <td><?= $v['username'] ?></td>
    <td><?= $v['changefreq'] ?></td>
    <td><?= $expirydate ?></td>
    <td><?= $lastmodified ?></td>
    <td>
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Edit this access" href="<?php echo site_url("admin/domain/form/$id") ?>" >
<span class="ui-icon ui-icon-wrench"></span>
</a>        



<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="view full details" href="<?php echo site_url("admin/domain/view/$id") ?>" >
<span class="ui-icon ui-icon-circle-zoomin"></span>
</a> 
 
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="People who access" href="<?php echo site_url("admin/domain/assign_user_project/$id") ?>" >
<span class="ui-icon ui-icon-person"></span>
</a>

 
    </td>
  </tr>
<?php endforeach; ?>  
<?php } else {?>
<tr><td colspan="10"><div align="center">No records found.</div></td></tr>
<?php } ?>
</table>


</div>


<input type="hidden" name="userid" value="<?= $userid ?>" />
<input type="hidden" name="gidlist" value="<?php echo implode(',', $gidlist) ?>" >
</form> 
<div class="clearfix"></div>
<ul class="pagination">
<?php echo isset($pagination) ? $pagination : '' ?>
</ul>
