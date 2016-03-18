<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login</title>
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
<style type="text/css" >
body { background:#333333 }
.loginform {width:400px; margin:10px auto}
</style>

</head>

<body>

<div class="portlet form-bg loginform"  >
							<div class="portlet-header">Forgot password</div>
							<div class="portlet-content">
 <?php echo isset($flash) ? flash($flash) : ''; ?>
                             
								<form action="<?php echo site_url('forgotpassword/') ?>" method="post" class="forms" name="form" >
									<ul>
										<li>
											<label class="desc">Enter your email address :</label>
											<div><input type="text" tabindex="1" maxlength="255" value="" class="field text full" name="email" /></div>
										</li>                          				
										<li class="buttons">
											<button type="submit" value="Submit" class="ui-state-default ui-corner-all" id="saveForm">Retrieve password</button>
										    <a href="<?= site_url("") ?>" style="font-size:15px ; padding-right:15px; float:right; line-height:2.5em " >&laquo;Back</a>                                                                                    
										</li>
									</ul>

								</form>
								<div class="linetop clearfix"></div>
							</div>
						</div>

</body>
</html>