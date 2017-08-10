
</script>
<?php 
$timezone = $this->sitesettings->get_settings('timezone');
$isdaylightsaving = $this->sitesettings->get_settings('isdaylightsaving');
$localtime = gmt_to_local(now(), $timezone, $isdaylightsaving );
$localtime = date('F d Y g:i:s a', $localtime);
?>
	<div id="footer">
<!--		<div id="menu" style="display:none">
			<a href="#" title="Home">Home</a>
			<a href="#" title="Administration">Administration</a>
			<a href="#" title="Settings">Settings</a>
			<a href="#" title="Contact">Contact</a>
		</div>-->
        <div id="menu" >
        Local time: <?php echo $localtime  ?>
        </div>
