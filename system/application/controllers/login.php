<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends Controller {
    function __construct() {
        parent::Controller();
    }

    function index() {

        if ( $this->dx_auth->is_logged_in()) {
            redirect('admin/main');
        }
        // Default is we don't show captcha until max login attempts eceeded
        $data['show_captcha'] = FALSE;
        $use_captcha = $this->sitesettings->get_settings('use_captcha');
        if ( $use_captcha == 'yes' ) {
                // Create catpcha
                $this->dx_auth->captcha();
                // Set view data to show captcha on view file
                $data['show_captcha'] = TRUE;
        }
        $this->load->view( 'login', $data );
    }

    function ret() {
        $url = $this->uri->uri_string();
        $url = substr($url, 11);
        $this->session->set_userdata('returl', $url);
        redirect($url);
    }

}

