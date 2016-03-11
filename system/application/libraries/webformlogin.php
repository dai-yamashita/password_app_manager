<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------
class Webformlogin {
    public $url ;
    function  __construct() {
        $this->CI =& get_instance();
        log_message('debug', 'Webformlogin Class Initialized');

    }

    function init($logins = array(), $params = array(), $return_response = FALSE) {
        $url = "http://".$logins['username'].':'.$logins['password'].'@'.$logins['host'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_POST, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;

    }

    function init2($method, $logins = array(), $params = array(), $return_response = FALSE) {
        $default = array(
                'user'      => '',
                'pass'      => '',
                'host'      => '',
                'skin'      => 'x3',
        );
        $logins = array_merge( $default, $logins );
        $this->_request($method, $logins, $params, $return_response);
        $this->url = "https://".$logins['user'].':'.$logins['pass'].'@'.$logins['host'].':2083/frontend/'.$logins['skin']."/";

    }

    function execute_page($page) {
        redirect( $this->url . $page, "refresh");
    }

}



// END Cpanel class
/* End of file cpanel.php */
/* Location: ./system/application/libraries/cpanel.php */
