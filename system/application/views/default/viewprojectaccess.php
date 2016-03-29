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
<table  id="sort-table" width="100%" border="0" >
<thead> 
  <tr>
    <th width="23%">Project name</th>
    <th width="47%">Description</th>
    <th width="8%">Flag</th>
    <th width="10%"># of domains</th>
    <th width="12%">&nbsp;</th>
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
    <td><?php echo $v['project'] ?>&nbsp;</td>
    <td><?php echo $v['desc'] ?>&nbsp;</td>
    <td><?php echo $v['visibility'] ?></td>
    <td><div align="center"><?php echo $nitems; ?>&nbsp;</div></td>
    <td>
      
  <a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Edit this project" href="<?php echo site_url("admin/project/form/$id") ?>" >
  <span class="ui-icon ui-icon-wrench"></span>
  </a>

<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="View project domain access" href="<?php echo site_url("admin/project/view/$id") ?>" >
<span class="ui-icon ui-icon-circle-zoomin"></span>
</a>

  <a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Remove user from this project" href="<?php echo site_url("admin/project/deleteproject/$id/userid/$userid") ?>" onclick="return confirm('Are you sure you want to delete?')" >
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
<input type="hidden" name="userid" value="<?= $userid ?>" />

</form> 
<div class="clearfix"></div>
<ul class="pagination">
<?php echo isset($pagination) ? $pagination : '' ?>
</ul>
