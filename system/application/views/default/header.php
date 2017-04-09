<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Password manager</title>
	<link href="<?php echo base_url() ?>themes/default/style.css" rel="stylesheet" media="all" />
	<!--[if IE 6]>
	<link href="<?php echo base_url() ?>themes/default/css/ie6.css" rel="stylesheet" media="all" />

	<script src="<?php echo base_url() ?>themes/default/js/pngfix.js"></script>
	<script>
	  /* EXAMPLE */
	  DD_belatedPNG.fix('.logo, .other ul#dashboard-buttons li a');

	</script>
	<![endif]-->
	<!--[if IE 7]>
	<link href="<?php echo base_url() ?>themes/default/css/ie7.css" rel="stylesheet" media="all" />
	<![endif]-->

	<script type="text/javascript" src="<?php echo base_url() ?>themes/default/js/jquery-1.3.2.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>themes/default/js/superfish.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>themes/default/js/jquery-ui-1.7.2.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>themes/default/js/tooltip.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>themes/default/js/tablesorter.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>themes/default/js/tablesorter-pager.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>themes/default/js/cookie.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>themes/default/js/custom.js"></script>
</head>
<body>
<?php
$user_id = $this->dx_auth->get_user_id();
// check the overdued accounts
$this->alerts->check_overdue_account();
// check the unread alerts
$unreadalets = $this->alerts->check_unread_alerts();
//print_r($unreadalets);
$total_unreadalerts = $unreadalets ? count($unreadalets) : 0;
$total_unreadalerts = ($total_unreadalerts > 0 ) ? " ($total_unreadalerts new) " : "";
$siteurl = 'admin';
?>
	<div id="header">
		<div id="top-menu">
<?php if ($this->dx_auth->is_logged_in()) { ?>
			<a href="<?php echo site_url('admin/alert') ?>" title="Message alerts" >Message alerts<?= $total_unreadalerts ?></a> |

			<!--<a href="#" title="Settings">Settings</a> |
			<a href="#" title="Contact us">Contact us</a>-->
			<span>Logged in as <b><a href="<?php echo site_url("admin/account/editprofile/$user_id") ?>" ><?= $this->dx_auth->get_username() ?></a></b></span>
			| <a href="<?php echo site_url('admin/auth/logout') ?>" title="Logout">Logout</a>
<?php } ?>
		</div>
		<div id="sitename">
			<a href="index.php" class="logo float-left" title="Admintasia">Password Manager</a>
			<!--<div class="button float-right">
				<a href="#" id="dialog_link" class="btn ui-state-default ui-corner-all"><span class="ui-icon ui-icon-newwin"></span>Open Dialog</a>
				<a href="#" id="login_dialog" class="btn ui-state-default ui-corner-all"><span class="ui-icon ui-icon-image"></span>Open Login Box</a>
			</div>-->
			<div id="login" title="Members Loginxx">
				<form action="#" method="post" enctype="multipart/form-data" class="forms" name="form" >
					<ul>
						<li>
							<label for="email" class="desc">
								Email:
							</label>
							<div>
								<input type="text" tabindex="1" maxlength="255" value="" class="field text full" name="email" id="email" />
							</div>
						</li>
						<li>
							<label for="password" class="desc">
								Password:
							</label>
							<div>
								<input type="text" tabindex="1" maxlength="255" value="" class="field text full" name="password" id="password" />
							</div>
						</li>
					</ul>
				</form>
			</div>

		</div>
		<ul id="navigation" class="sf-navbar">
			<li>
				<a href="<?= site_url('') ?>" >Dashboard</a>
			</li>
<?php
if ($this->dx_auth->is_role('administrator')) {
?>
			<li>
				<a href="<?php echo site_url('admin/user/browse') ?>">Users</a>
				<ul>
					<li>
						<a href="<?php echo site_url('admin/user/form') ?>" >Create user</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/user/browse') ?>">View all users</a>
					</li>
				</ul>
			</li>
            <li>
				<a href="<?php echo site_url('admin/domain/browse') ?>">Domain access</a>
				<ul>
					<li>
						<a href="<?php echo site_url('admin/domain/form') ?>">Create domain access</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/domain/browse') ?>">View all domain access</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/domain/search') ?>">Search domain access</a>
					</li>

					<li>
						<a href="<?php echo site_url('admin/domain/customfield') ?>">Create domain custom field</a>
					</li>
				</ul>
			</li>
            <li>
          	<a href="<?php echo site_url('admin/mydomain/browse') ?>" >My Personal domain access</a>
				<ul>
					<li>
						<a href="<?php echo site_url('admin/mydomain/form') ?>">Create domain access</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/mydomain/browse') ?>">View my domain access</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/mydomain/search') ?>">Search domain access</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/mydomain/customfield') ?>">Create domain custom field</a>
					</li>
                </ul>
            </li>

            <li>
				<a href="<?php echo site_url('admin/department/browse') ?>">Group</a>
				<ul>
					<li>
						<a href="<?php echo site_url('admin/department/form') ?>">Create group</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/department/browse') ?>">View all group</a>
					</li>
				</ul>
			</li>

            <li>
				<a href="<?php echo site_url('admin/project/browse') ?>">Projects</a>
				<ul>
					<li>
						<a href="<?php echo site_url('admin/project/form') ?>">Create project</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/project/browse') ?>">View all project</a>
					</li>
				</ul>
			</li>
            <li>
				<a href="<?php echo site_url('admin/accounttype/browse') ?>">Account type</a>
				<ul>
					<li>
						<a href="<?php echo site_url('admin/accounttype/form') ?>">Create account type</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/accounttype/browse') ?>">View all account type</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/logintemplate/browse') ?>">View all Login templates</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/logintemplate/form') ?>">Create login templates</a>
					</li>

 				</ul>
			</li>
            <li>
				<a href="<?php echo site_url('admin/settings/form') ?>" >Admin settings</a>
			</li>

<?php }
elseif($this->dx_auth->is_role('manager')) { ?>
            <li>
				<a href="<?php echo site_url('admin/domain/browse') ?>">Domain access</a>
				<ul>
					<li>
						<a href="<?php echo site_url('admin/domain/form') ?>">Create domain access</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/domain/browse') ?>">View all domain access</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/domain/search') ?>">Search domain access</a>
					</li>
 					<li>
						<a href="<?php echo site_url('admin/domain/customfield') ?>">Create domain custom field</a>
					</li>
 				</ul>
			</li>
			<li>
          	<a href="<?php echo site_url('admin/mydomain/browse') ?>" >My Personal domain access</a>
				<ul>
					<li>
						<a href="<?php echo site_url('admin/mydomain/form') ?>">Create domain access</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/mydomain/browse') ?>">View my domain access</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/mydomain/search') ?>">Search domain access</a>
					</li>
                </ul>
            </li>
            <li>
				<a href="<?php echo site_url('admin/department/browse') ?>">Group</a>
				<ul>
					<li>
						<a href="<?php echo site_url('admin/department/form') ?>">Create group</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/department/browse') ?>">View all group</a>
					</li>
				</ul>
			</li>

            <li>
				<a href="<?php echo site_url('admin/project/browse') ?>">Projects</a>
				<ul>
					<li>
						<a href="<?php echo site_url('admin/project/form') ?>">Create project</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/project/browse') ?>">View all project</a>
					</li>
				</ul>
			</li>
            <li>
				<a href="<?php echo site_url('admin/user/browse') ?>">Users</a>
				<ul>
					<li>
						<a href="<?php echo site_url('admin/user/form') ?>" >Create user</a>
					</li>
					<li>
						<a href="<?php echo site_url('admin/user/browse') ?>">View all users</a>
					</li>
				</ul>
			</li>

<?php }
elseif($this->dx_auth->is_role('member')) { ?>
        <li>
		<a href="<?php echo site_url('admin/domain/browse') ?>">Domain access</a>
		</li>

		<li>
            <a href="<?php echo site_url('admin/mydomain/browse') ?>">My personal domain access</a>
            <ul>
                <li>
                    <a href="<?php echo site_url('admin/mydomain/form') ?>">Create domain access</a>
                </li>
                <li>
                    <a href="<?php echo site_url('admin/mydomain/search') ?>">Search domain access</a>
                </li>

            </ul>
        </li>

<?php } ?>

			<li>
				<a href="<?php echo site_url("admin/account/editprofile/$user_id") ?>">Edit profile</a>
			</li>
		</ul>
	</div>
