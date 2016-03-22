
<div class="other-box yellow-box ui-corner-all">
    <div class="cont tooltip ui-corner-all" title="">
        <h3>Welcome <?= $firstname ?>!</h3>
        <p>Welcome to the password manager. This is where we keep all the password accounts for our project.</p>

    </div>
</div>

<div class="page-title ui-widget-content ui-corner-all">
    <h1>Administration Options</h1>
    <div class="other">
<?php 
if ($this->dx_auth->is_role('owner')) {
?>
        <ul id="dashboard-buttons">
            <li><a href="<?= site_url('admin/user')?>" class="Book_phones tooltip" title="Add user">All User</a></li>
            <li><a href="<?= site_url('admin/domain')?>" class="Books tooltip" title="Add domain">All Domain access</a></li>
            <li><a href="<?= site_url('admin/department')?>" class="Book_phones tooltip" title="Add group">All Group</a></li>
            <li><a href="<?= site_url('admin/project')?>" class="Books tooltip" title="Add Project">All Project</a></li>
            <li><a href="<?= site_url('admin/accounttype')?>" class="Book_phones tooltip" title="Add Project">All Account types</a></li> 
            <li><a href="<?= site_url('admin/settings')?>" class="Books tooltip" title="Add Project">Admin settings</a></li> 
        </ul>
<?php  } 
elseif($this->dx_auth->is_role('administrator')) { ?>     
        <ul id="dashboard-buttons">
            <li><a href="<?= site_url("admin/domain") ?>" class="Books tooltip" title="View my domain access">All Domain access</a></li>
            <li><a href="<?= site_url('admin/department')?>" class="Book_phones tooltip" title="Add group">All Group</a></li>
            <li><a href="<?= site_url('admin/project')?>" class="Books tooltip" title="Add Project">All Project</a></li>
            <li><a href="<?= site_url('admin/accounttype')?>" class="Book_phones tooltip" title="Add Project">All Account types</a></li>
            <li><a href="<?= site_url("admin/account/editprofile/$user_id") ?>" class="Book_phones tooltip" title="Edit profile">Edit profile</a></li>            
        </ul>   
<?php }
elseif($this->dx_auth->is_role('manager')) { ?>
        <ul id="dashboard-buttons" >
            <li><a href="<?= site_url('admin/user')?>" class="Book_phones tooltip" title="Add user">All User</a></li>
            <li><a href="<?= site_url('admin/department')?>" class="Books tooltip" title="Add group">All Group</a></li>            
            <li><a href="<?= site_url("admin/account/editprofile/$user_id") ?>" class="Book_phones tooltip" title="Edit profile">Edit profile</a></li>
            <li><a href="<?= site_url("admin/mydomain") ?>" class="Books tooltip" title="View my domain access">View my domain access</a></li>
        </ul>
<?php } 
elseif($this->dx_auth->is_role('member')) { ?>
        <ul id="dashboard-buttons">
            <li><a href="<?= site_url("admin/mydomain/form/") ?>" class="Book_phones tooltip" title="create personal domain">Create personal domain</a></li>        
            <li><a href="<?= site_url("admin/mydomain") ?>" class="Books tooltip" title="View my domain access">View personal domain access</a></li>
            
            <li><a href="<?= site_url("admin/account/editprofile/$user_id") ?>" class="Book_phones tooltip" title="Edit profile">Edit profile</a></li>
            <li><a href="<?= site_url("admin/domain") ?>" class="Books tooltip" title="View my domain access">View my domain access</a></li>
 
 
         </ul>   
<?php } ?>
        <div class="clearfix"></div>
    </div>
    
</div>
