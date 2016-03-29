<?php
$uri = $this->uri->uri_to_assoc(1); 
//pre($uri);
$this->mdl_department->where = '';
$this->mdl_department->where = array('visibility' => 'public');
$this->mdl_department->order_by = 'groupname ASC';
$rsoptions = $this->mdl_department->get_all_department();
$this->mdl_department->order_by = 'groupname ASC';

$this->mdl_projects->where = '';
$this->mdl_projects->where = array('visibility' => 'public');
$this->mdl_projects->order_by = 'project ASC';
$rsoptions2 = $this->mdl_projects->get_all_projects();
$this->mdl_projects->order_by = '';
$showimportexport = FALSE;
$showimport = FALSE;
$showexport = FALSE;
$showrequestaccess = FALSE;
if (isset($uri['admin']) && $uri['admin'] != '') {	
	$urilink = 'admin/' . $uri['admin'];
	$tmptitle = $uri['admin'];	
	if($uri['admin'] == 'domain') {
		if ($this->dx_auth->is_role(array('member'))) {
			$showimportexport = TRUE;
			$showexport = TRUE;
			$showrequestaccess = TRUE;
		} else {
			$showimportexport = TRUE;
			$showimport = TRUE;
			$showexport = TRUE;			
			$showrequestaccess = TRUE;			
		}
		$tmptitle = 'domain';	
	}elseif ($uri['admin'] == 'mydomain') {		
		$showimportexport = TRUE;
		$showimport = TRUE;
		$showexport = TRUE;					
		$showrequestaccess = TRUE;
		$tmptitle = 'personal domain';
	} elseif($uri['admin'] == 'accounttype') {
		$showimportexport = TRUE;
		$showimport = TRUE;
		$showexport = TRUE;					
		$showrequestaccess = TRUE;
		$tmptitle = 'accounttype';
	}
	elseif($uri['admin'] == 'project') {
		$showimportexport = TRUE;
		$showimport = TRUE;
		$showexport = TRUE;					
		$showrequestaccess = TRUE;
		$tmptitle = 'projects';
	}elseif($uri['admin'] == 'department') {
		$showimportexport = TRUE;	
		$showimport = TRUE;
		$showexport = TRUE;					
		$showrequestaccess = TRUE;
		$tmptitle = 'group';	
//		pre($rsoptions);
	}
	elseif($uri['admin'] == 'settings') {
		$showimportexport = FALSE;		
		$tmptitle = '';	
	}	
	elseif($uri['admin'] == 'account') {
		$showimportexport = FALSE;		
		$tmptitle = '';	
	}
	elseif($uri['admin'] == 'alert') {
		$showimportexport = FALSE;		
		$tmptitle = '';	
	}	
	elseif($uri['admin'] == 'user') {
		$showimportexport = TRUE;		
		$showimport = TRUE;
		$showexport = TRUE;					
		$showrequestaccess = TRUE;		
	}		
	elseif($uri['admin'] == 'main') {
		$showimportexport = FALSE;		
		$showrequestaccess = TRUE;		
	}	
	else {
		$showimportexport = FALSE;	
		$tmptitle = $uri['admin'];
	}
	
	$csvtitle = $tmptitle;

}
else {
 		$showrequestaccess = TRUE;		

}

?>
		<div id="sidebar">
			<div class="side-col ui-sortable">
				<!--<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
					<div class="portlet-header ui-widget-header">Theme Switcher</div>
					<div class="portlet-content">
						<ul class="side-menu">
							<li>
								<a class="set_theme" id="default" href="javascript:void(0);" style="font-weight:bold;" title="Default Theme">Default Theme</a>
							</li>
							<li>
								<a class="set_theme" id="light_blue" href="javascript:void(0);" title="Light Blue Theme">Light Blue Theme</a>
							</li>
						</ul>
					</div>
				</div>-->
				<!--<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
					<div class="portlet-header ui-widget-header">Layout Options</div>
					<div class="portlet-content">
						<ul class="side-menu">
							<li>
								Here, you can set the page width, either fixed or fluid. You decide!<br /><br />
							</li>
							<li id="fluid_layout">
								<a href="javascript:void(0);" title="Fluid Layout">Switch to <b>Fluid Layout</b></a>
							</li>
							<li id="fixed_layout">
								<a href="javascript:void(0);" title="Fixed Layout">Switch to <b>Fixed Layout</b></a>
							</li>
						</ul>
					</div>
				</div>-->
				<?php if ($showimportexport) { ?>
				<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
					<div class="portlet-header ui-widget-header">Import/Export</div>
					<div class="portlet-content">
						<div id="accordion">
                            <?php if ($showimport) { ?>
							<div>
								<h3><a href="#">Import</a></h3>
								<div>
									<ul class="side-menu">
										<li><a href="<?= site_url("$urilink/import_step/csv/step/1") ?>" >Import <?=$csvtitle?> CSV</a></li>
										<li><a href="<?= site_url("$urilink/import/xml") ?>" >Import <?=$csvtitle?> XML</a></li>
 									</ul>
								</div>
							</div>
                            <?php } ?>
                            <?php if ($showexport) { ?>
							<div>
								<h3><a href="#">Export</a></h3>
								<div>
									<ul class="side-menu">
										<li><a href="<?= site_url("$urilink/export/csv") ?>" >Export <?=$csvtitle?> CSV</a></li>
										<li><a href="<?= site_url("$urilink/export/xml") ?>" >Export <?=$csvtitle?> XML</a></li>
									</ul>
								</div>
							</div>
                            <?php } ?>
						</div>
					</div>
				</div>
				<?php } ?>
                <?php if ($showrequestaccess) { ?>
				 <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
					<div class="portlet-header ui-widget-header">Request Group Access</div>
					<div class="portlet-content">
                    	<p>Select a group to request an access</p>
						<div id="req_groupresult"></div> 
						<form>
                        <select name="req_group" id="req_group" >
<?php 
foreach($rsoptions as $k => $v): 
?>
      	    <option value="<?= $v['deptid'] ?>" >
      	      <?= $v['groupname'] ?>
   	        </option>
      	    <?php endforeach; ?>

                        </select>
                        <input type="submit" name="request" id="btnreq_group" value="Send Request"  />
                        </form>
					</div>
				</div>
				<?php } ?>
                
                <?php if ($showrequestaccess) { ?>
				 <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
					<div class="portlet-header ui-widget-header">Request Project Access</div>
					<div class="portlet-content">
                    	<p>Select a project to request an access</p>
						<div id="req_projectresult"></div> 
						<form>
                        <select name="req_project" id="req_project">
<?php 
foreach($rsoptions2 as $k => $v): 
?>
      	    <option value="<?= $v['projectid'] ?>" >
      	      <?= $v['project'] ?>
   	        </option>
      	    <?php endforeach; ?>

                        </select>
                        <input type="submit" name="request" id="btnreq_project"  value="Send Request"  />
                        </form>
					</div>
				</div>
				<?php } ?>
                
				<!--<div class="other-box yellow-box ui-corner-all">
					<div class="cont tooltip ui-corner-all" title="Tooltips, tooltips, tooltips !">
						<h3>Tooltip !</h3>
						<p>This is a sample tooltip !</p>
					</div>
				</div> 
				 <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
					<div class="portlet-header ui-widget-header">Accordion</div>
					<div class="portlet-content">
						<div id="datepicker"></div>
					</div>
				</div>-->
			</div>
			<div class="clearfix"></div>
		</div>
 
<script type="text/javascript" >
/*$("#req_group").change(function () {
	  var str = "";
	  $("select option:selected").each(function () {
			str += $(this).text() + " ";
		  });
	  $("div").text(str);
	})
	.change();*/

$('#btnreq_project').click(function(){
var str = "";
$("select#req_project option:selected").each(function () {
	str = $(this).val() + " ";
  });	
$.post("<?= site_url('admin/domain/sendrequest_access') ?>", { req_project: str }, function(data) {
	if (data.result == 'error') {
		$('#req_projectresult').addClass('response-msg error ui-corner-all').text(data.flashmessage) 		
	} else {
		$('#req_projectresult').addClass('response-msg success ui-corner-all').text(data.flashmessage)
	}
}, 'json' );
return false;
});

$('#btnreq_group').click(function(){
var str = "";
$("select#req_group option:selected").each(function () {
	str = $(this).val() + " ";
  });									
$.post("<?= site_url('admin/account/sendrequest_access') ?>", { req_group: str }, function(data) {
	if (data.result == 'error') {
		$('#req_groupresult').addClass('response-msg error ui-corner-all').text(data.flashmessage) 		
	} else {
		$('#req_groupresult').addClass('response-msg success ui-corner-all').text(data.flashmessage)
	}
}, 'json' );

return false;
});

</script> 