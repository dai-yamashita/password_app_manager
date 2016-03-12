<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
cook by mandoy add me @facebook.com/artheman
*/
class Settings extends Controller {
    function __construct() {
        parent::Controller();
 	if (! $this->dx_auth->is_logged_in()) redirect('login');
	$this->dx_auth->check_uri_permissions();
        $this->load->library( 'form_validation' );
	$this->form_validation->set_error_delimiters('<p style="padding:2px" >', '</p>');		
        $this->paging = $this->paging->get_paging_template();
    }

    function index() {
        $this->form();
    }

    function form() {
        $this->form_validation->set_rules('account_expired_message', 'alert message', 'required|trim|max_length[254]');
        $this->form_validation->set_rules('admin_email', 'admin email', 'required|trim|max_length[254]|valid_email');
        $this->form_validation->set_rules('use_captcha', 'use captcha', 'required|trim');
        $rs = $this->mdl_settings->get_all_settings( array('resulttype' => 'result_array') );
        $data['results'] = $this->mdl_settings->to_array($rs);
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        if ( $this->form_validation->run() ) {
                $this->mdl_settings->save();
                $this->session->set_flashdata( 'flash', 'Successfully saved settings.' ) ;
                header('location:' . $_SERVER['HTTP_REFERER'] );
        }else{
                $data['flash']['error'] = validation_errors();
                $this->template->write_view('content', 'default/settings_form', $data);
        }
        $this->template->render();
    }
	

}

