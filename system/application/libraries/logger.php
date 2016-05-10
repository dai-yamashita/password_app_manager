<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Logger {
    function __construct() {
        $this->ci =& get_instance();

    }

    function removetemp($params = array()) {
        $default = array(
                'tempfolder'		=> '',
                'file_extension'        => '*.*',
                'expiretime'            => 1, // 1 weeks
        );
        $params = array_merge( $default, $params );
        $expire_time    = $params['expiretime'] ;
        $dir = $params['tempfolder'] . $params['file_extension'];
        foreach(glob($dir) as $filename) {
            $datecreated = filectime($filename);
            // Calculate file age in seconds
            $diff = (time() - filectime($filename))/60/60/24;
            if ($diff > $expire_time) @unlink($filename);
        }
    }
}