<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function flash($msg) {
$flash = '';
	if (!empty($msg['error'])) {
		$flash = "<div class=\"response-msg error ui-corner-all\" >
	    <span>An error occured in saving the record.</span>
		" . $msg['error'] .
		"</div>";
	}

	if (!empty($msg['defaulterror'])) {
		$flash = "<div class=\"response-msg error defaulterror ui-corner-all\" >
	    
		" . $msg['defaulterror'] .
		"</div>";
	}

	if (!empty($msg['success'])) {
	$flash = "<div class=\"response-msg success ui-corner-all\" >
		<span>Success</span>
		" . $msg['success'] .
		"</div>";
	}
	
	echo $flash;
}

function pre($vars){
echo "<pre>";
print_r( $vars );
echo "</pre>";
}



