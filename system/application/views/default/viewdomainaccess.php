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

    
<form method="post" action="<?= site_url("admin/user/domainaccess/$userid") ?>" >   
  
<div class="clearfix"></div>

<div class="hastable"  >
<table width="100%" border="1"  id="sort-table" >
<thead> 
  <tr>
    <th width="21%">Domain</th>
    <th width="4%">Type</th>
    <th width="8%">Importance</th>
    <th width="13%">URL</th>
    <th width="7%">Username</th>
    <th width="8%">Change<br />frequency </th>
    <th width="11%">Expire</th>
    <th width="11%">Last Changed</th>
    <th width="14%">&nbsp;</th>
  </tr>
</thead>  
<?php
$alt = 0;
if ($results) {
foreach($results as $v): 
$id = $v['domain_id'];
$userid = $v['user_id'];
$expirydate = ($v['expirydate'] !== 0 && !empty($v['expirydate'])) ? date('M d, Y g:i a', $v['expirydate']) : '' ;
$lastmodified  = ($v['last_modified'] == 0 || empty($v['last_modified']))  ? '' : date('M d, Y g:i a', $v['last_modified']);
?>
  <tr class="<?= (++$alt%2 == 0) ?  'alt' : '' ?>">
    <td><?= $v['project'] ?></td>
    <td><?= $v['acctype'] ?></td>
    <td><?= $v['importance'] ?></td>
    <td><?= $v['url'] ?></td>
    <td><?= $v['username'] ?></td>
    <td><?= $v['changefreq'] ?></td>
    <td><?= $expirydate ?></td>
    <td><?= $lastmodified ?></td>
    <td>
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="view full details" href="<?php echo site_url("admin/domain/view/$id") ?>" >
<span class="ui-icon ui-icon-circle-zoomin"></span>
</a> 
    
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Remove user from this domain" href="<?php echo site_url("admin/user/deletedomain/$id/userid/$userid") ?>" onclick="return confirm('Are you sure you want to delete?')" >
<span class="ui-icon ui-icon-circle-close"></span></a>      
 
  
    </td>
  </tr>
<?php endforeach; ?>  
<?php } else { ?>
<tr><td colspan="9" ><div align="center">No record found.</div></td></tr>
<?php } ?>
</table>


</div>
<input type="hidden" name="userid" value="<?= $userid ?>" />

</form> 
<div class="clearfix"></div>
<ul class="pagination">
<?php echo isset($pagination) ? $pagination : '' ?>
</ul>
