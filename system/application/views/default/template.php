<?php
$uri = $this->uri->uri_to_assoc(1);
?>
<?php $this->load->view('default/header'); ?>
	 

	<div id="page-wrapper">
		<div id="main-wrapper">
			<div id="main-content">
            <?= $content ?>
            </div>
			<div class="clearfix"></div>
		</div>
<?php $this->load->view('default/sidebar'); ?>

	</div>
	<div class="clearfix"></div>
<?php $this->load->view('default/footer'); ?>
</body>
</html>

