<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------
class Cpanel {
    public $url ;
    function Cpanel() {
        $this->CI =& get_instance();
        log_message('debug', 'Cpanel Class Initialized');        
        //decide how we will be contacing the server
        //curl is prefered, if not available we will use file_get_contents
        if(function_exists('curl_setopt')) {
            $this->http_client = "curl";
            log_message('debug', 'Cpanel Class will use cUrl');
        }
        else {
            $this->http_client = "file";
            log_message('debug', 'Cpanel Class will use file_get_contents()');
        }

    }

    //find out how the class will connect to the cpanel server.
    function get_client_method() {
        return $this->http_client;
    }

    private function _request($method, $logins = array(), $params = array(), $return_response = FALSE) {
        if($this->http_client == "file") {
            $url = 'http://' . $logins['user'].':'.$logins['pass'].'@'.$logins['host'].':2082/frontend/'.$logins['skin']."/".$method."?"
                    . http_build_query($params);
            return @file_get_contents($url.$request);
        }

        if($this->http_client == "curl") {
            $url = "https://".$logins['user'].':'.$logins['pass'].'@'.$logins['host'].':2083/frontend/'.$logins['skin']."/".$method."?";
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

    }
    
    function init($method, $logins = array(), $params = array(), $return_response = FALSE) {
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
