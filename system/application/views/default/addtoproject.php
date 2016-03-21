<div class="title title-spacing">
    <?php echo (isset($domainaccess_title) && $domainaccess_title) ? "<h2>$domainaccess_title</h2>" : "<h2>All domains access</h2>" ?>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<form method="post" action="<?= site_url("admin/department/add_to_project/$gid") ?>" >   
<div style="float:left;">
<input type="submit" value="Update group project access" class="submit" />
<input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/department/')?>'" />
<p>Check a project to give the group an access or Uncheck to remove from access.</p>
</div>

<div class="clearfix"></div>

<div class="hastable" >
<table  id="sort-table" width="100%" border="0" >
<thead> 
  <tr>
    <th width="3%"><input type="checkbox" name="checkall" value="" id="checkall"   /></th>
    <th width="81%">Project name</th>
    <th width="16%"># of domains</th>
   </tr>
</thead>
<?php
foreach($results as $v): 
$id = $v['projectid'];
$gidlist[] = $v['projectid'];
$ischecked = (in_array($id, $results2)) ? " checked " : '' ;
$list = $this->mdl_projects->get_all_projectdomains($v['projectid']);
$nitems = count($list);
?>
  <tr>
    <td><input type="checkbox" name="chk[]" value="<?= $id ?>" <?php echo $ischecked ?>   /></td>
    <td><?php echo $v['project'] ?></td>
    <td><?php echo $nitems; ?></td>
    </tr>
<?php endforeach; ?> 
</table>
<div style="float:left;">
<input type="submit" value="Update group project access" class="submit" />
<input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/department/')?>'" />
<p>Check a project to give the group an access or Uncheck to remove from access.</p>
</div>
</div>

<input type="hidden" name="gid" value="<?= $gid ?>" />
<input type="hidden" name="gidlist" value="<?php echo implode(',', $gidlist) ?>" >

</form>


<div class="clearfix"></div>
<ul class="pagination">
<?php echo isset($pagination) ? $pagination : '' ?>
</ul>

<div class="clearfix"></div>


<div id="dialog2" title="Dialog Title" style="display:none" >
	<p>Delete this image</p>
</div>