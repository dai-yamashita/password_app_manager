<div class="title title-spacing">
    <?php echo (isset($domainaccess_title) && $domainaccess_title) ? "<h2>$domainaccess_title</h2>" : "<h2>User project access</h2>" ?>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<form method="post" action="<?= site_url("admin/user/add_to_project/$gid") ?>"  style="width:100% !important" >   
<div style="float:left;">
<input type="submit" value="Update user project access" class="submit" />
<input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/user/')?>'" />

<p class="warning"  >Check a project to give the user an access or Uncheck to remove his access. 
<br />When removing a project access, the user will be remove also from all domain access on that particular project. </p>
</div>

<div class="clearfix"></div>

<div class="hastable" style="border:0px solid red" >
<table  id="sort-table" width="100%" border="0" >
<thead> 
  <tr>
    <th width="3%"><input type="checkbox" name="checkall" value="" id="checkall"   /></th>
    <th width="33%">Project name</th>
    <th width="64%"><div align="left"># of domains</div></th>
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
<div style="float:left; margin:0; padding:0" >
<input type="submit" value="Update user project access" class="submit" />
<input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/user/')?>'" />
<p class="warning"  >Check a project to give the user an access or Uncheck to remove his access. 
<br />When removing a project access, the user will be remove also from all domain access on that particular project. </p>
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