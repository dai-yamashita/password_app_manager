<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sitesettings {

    var $CI;

    function __construct() {
        $this->CI =& get_instance();
        $this->CI->config->load('sitesettings');
        $this->CI->load->library('logger');
        define('BASEURL', $this->CI->config->item('base_url') );
        define('UPLOADDIR', './uploads/' );
        define('THEMEPATH_IMG', BASEURL.'themes/default/images/');
        $this->CI->logger->removetemp( array('tempfolder' => UPLOADDIR . 'csv/' ));
    }

    function get_settings($k) {
        $this->CI->db->where('key', $k);
        $tmp = $this->CI->db->get('sitesettings')->row_array();
        // print 'get_settings:';
        // print $k;
        // print_r($tmp);
        $tmp = $tmp['value'];
        return $tmp;
    }

}
