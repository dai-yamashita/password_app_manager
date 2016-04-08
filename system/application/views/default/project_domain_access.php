<div class="title title-spacing">
    <?php echo (isset($domainaccess_title) && $domainaccess_title) ? "<h2>$domainaccess_title</h2>" : "<h2>All domains access</h2>" ?>
    
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<?php
$uri = $this->uri->uri_to_assoc(2);
$uri2 = $this->uri->uri_to_assoc(1);
$projectid = $uri2['view'];
$uri['field'] = isset($uri['field']) ? $uri['field'] : '';

$arrow1 = (($uri['field'] == 'project') && $sortby == 'asc') ? 'arrowdown' : (($uri['field'] == 'project') ? 'arrowup' : '') ; 
$arrow2 = (($uri['field'] == 'acctype') && $sortby == 'asc') ? 'arrowdown' : (($uri['field'] == 'acctype') ? 'arrowup' : '') ; 
$arrow3 = (($uri['field'] == 'changefreq') && $sortby == 'asc') ? 'arrowdown' : (($uri['field'] == 'changefreq') ? 'arrowup' : '') ; 
?>

	<div class="groupby" >
    <ul>
<!--<li><a href="<?php echo site_url('admin/domain/department') ?>" >Group by Department</a></li>-->
	<li><a href="<?php echo site_url("admin/user/domainaccess/field/project/sort/$sortby") ?>" class="<?= $arrow1 ?>" >Group by Project</a></li>
    <li><a href="<?php echo site_url("admin/user/domainaccess/field/acctype/sort/$sortby") ?>" class="<?= $arrow2 ?>" >Group by Type</a></li>
    <li><a href="<?php echo site_url("admin/user/domainaccess/field/changefreq/sort/$sortby") ?>" class="<?= $arrow3 ?>" >Group by Frequency</a></li>    
    </ul>
    </div>

<div class="hastable"  >
<ul class="toolbar2" > 
<li><a href="<?= site_url("admin/domain/form/project_id/$projectid") ?>" ><img src="<?= THEMEPATH_IMG ?>126_add.png" />Create domain</a></li>
</ul>
<h1 class="subtitle" >List of all domains belong in <?= $title ?></h1>
<table width="100%" border="1"  id="sort-table" >
<thead> 
  <tr>
    <th width="18%">Domain</th>
    <th width="4%">Type</th>
    <th width="7%">Visit</th>
    <th width="9%">Importance</th>
    <th width="9%">URL</th>
    <th width="8%">Username</th>
    <th width="8%">Change<br />frequency </th>
    <th width="15%">Expire</th>
    <th width="17%">Last Changed</th>
    <th width="5%">&nbsp;</th>
  </tr>
</thead>  
<?php
$alt = 0;
$gidlist = array();
if ($results) {
foreach($results as $v): 
$gidlist[] = $v['domain_id'];
$id = $v['domain_id'];
$expirydate = (!empty($v['expirydate'])) ? date('M d, Y <br />g:i a', $v['expirydate']) : '';
$linkurl = prep_url($v['url']) or '';
$linkurl = site_url("admin/dologin/index/$id");

#$ischecked = (in_array($id, $results2)) ? " checked " : '' ;
$ischecked = '';
?>
  <tr class="<?= (++$alt%2 == 0) ?  'alt' : '' ?>">
    <td><?= $v['project'] ?></td>
    <td><?= $v['acctype'] ?></td>
    <td><?php if($linkurl):?>
      <a href="<?= $linkurl ?>" target="_blank" >Visit URL</a>
      <?php endif;?></td>
    <td><?= $v['importance'] ?></td>
    <td><?= $v['url'] ?></td>
    <td><?= $v['username'] ?></td>
    <td><?= $v['changefreq'] ?></td>
    <td><?= $expirydate ?></td>
    <td><?= !empty($v['last_modified']) ? date('M d, Y <br />g:i a', $v['last_modified']) : '&nbsp;' ?></td>
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

<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Delete this domain" href="<?php echo site_url("admin/domain/delete/$id") ?>" onclick="return confirm('Are you sure you want to delete?')" >
<span class="ui-icon ui-icon-circle-close"></span></a>

 
    </td>
  </tr>
<?php endforeach; ?>
<?php } else { ?>
<tr>
<td colspan="10"><div align="center">No records found</div></td>
</tr>

<?php } ?>
</table>
</div>

<input type="hidden" name="gid" value="<?= $gid ?>" />
<input type="hidden" name="gidlist" value="<?php echo implode(',', $gidlist) ?>" >

 
<div class="clearfix"></div>
<ul class="pagination">
<?php echo isset($pagination) ? $pagination : '' ?>
</ul>
