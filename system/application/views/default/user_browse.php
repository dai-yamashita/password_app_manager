<?php
$uri = $this->uri->uri_to_assoc(1);
?><div class="title title-spacing">
    <?php if (isset($title) && $title != '' ) { ?>
    <h2><?= $title ?></h2>
    <?php } else {?>
    <h2>All users</h2>
    <?php } ?>
</div>

<?php if(isset($uri['admin']) && $uri['admin'] == 'user' ) { ?>
<ul class="toolbar2" >
<li><a href="<?= site_url('admin/user/form') ?>" ><img src="<?= THEMEPATH_IMG ?>adduser_01.png" />Create User</a></li>
</ul>
<?php } ?>

<div class="clearfix" ></div>

<?php !empty($flash) ? flash($flash) : '' ?>
<div class="hastable" >
<table  id="sort-table" width="100%" border="0" >
<thead> 
  <tr>
    <th width="17%">Username</th>
    <th width="15%">Firstname </th>
    <th width="13%">Lastname</th>
    <th width="15%">Type</th>
    <th width="19%">Last login</th>
    <th width="16%">Last modified</th>
    <th width="5%">&nbsp;</th>
  </tr>
</thead>
<?php
$groupid = isset($uri['users']) ? intval($uri['users']) : '';
$alt = 0;
if ($results) {
foreach($results as $v): 
$id = $v['id'];
$role = $this->roles->get_role_by_id($v['role_id']);
$role = $role->result_array();

$timezone = $this->sitesettings->get_settings('timezone');
$isdaylightsaving = $this->sitesettings->get_settings('isdaylightsaving');
$t2 = human_to_unix($v['last_login']);
$t2 = gmt_to_local($t2, $timezone, $isdaylightsaving );
$t2 = ($v['last_login'] !== '0000-00-00 00:00:00') ? date('M d, Y g:i a', $t2) : '';

$t3 = human_to_unix($v['modified']);
//$t3 = gmt_to_local($t3, $timezone, $isdaylightsaving ); 
$t3 = date('M d, Y g:i a', $t3);
?>
  <tr class="<?= (++$alt%2 == 0) ?  'alt' : '' ?>" >
    <td><?php echo $v['username'] ?></td>
    <td><?php echo $v['firstname'] ?></td>
    <td><?php echo $v['lastname'] ?></td>
    <td><?php echo $role[0]['name'] ?></td>
    <td><?php echo $t2 ?></td>
    <td><?php echo $t3 ?></td>
    <td>
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Edit this user" href="<?php echo site_url("admin/user/form/$id") ?>" >
<span class="ui-icon ui-icon-wrench"></span>
</a>

<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="View all his domain access" href="<?php echo site_url("admin/user/viewdomainaccess/$id") ?>" >
<span class="ui-icon ui-icon-circle-triangle-e"></span>
</a>
    
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Update his domain access" href="<?php echo site_url("admin/user/domainaccess/$id") ?>" >
<span class="ui-icon ui-icon-document"></span>
</a>

<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="View all his project access" href="<?php echo site_url("admin/user/viewprojectaccess/$id") ?>" >
<span class="ui-icon ui-icon-circle-triangle-e"></span>
</a>

<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="Update his project access" href="<?php echo site_url("admin/user/add_to_project/$id") ?>" >
<span class="ui-icon ui-icon-document"></span>
</a>

<?php if(isset($uri['admin']) && $uri['admin'] == 'user' ) { ?>
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip confirmdelete" title="Delete this user" href="<?php echo site_url("admin/user/delete/$id") ?>" onclick="return confirm('Are you sure you want to delete?')"  >
<span class="ui-icon ui-icon-circle-close"></span></a>
<?php } else { ?>
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip confirmdelete" title="Remove user from group" href="<?php echo site_url("admin/user/delete_from_group/$groupid/user/$id") ?>" onclick="return confirm('Are you sure you want to delete?')"  >
<span class="ui-icon ui-icon-circle-close"></span></a>
<?php } ?>
	</td>
  </tr>
<?php endforeach; ?> 
<?php } else { ?>  
	<tr><td colspan="7" ><div align="center">No record found.</div></td></tr>
<?php } ?>
</table>
</div>
 

<div class="clearfix"></div>
<ul class="pagination">
<?php echo isset($pagination) ? $pagination : '' ?>
</ul>

<div class="clearfix"></div>
 
<script type="text/javascript" >
</script>