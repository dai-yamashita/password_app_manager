<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Main extends Controller {
    function __construct() {
        parent::Controller();
        if (! $this->dx_auth->is_logged_in()) redirect('login');
        $this->session->unset_userdata('returl');
    }

    function index() {
            $this->data['user_id'] = $this->dx_auth->get_user_id();
            $rs = $this->mdl_users->get_user_by_id($this->data['user_id']);
            $this->data['firstname'] = $rs['firstname'];
            $this->template->write_view('content', 'default/main', $this->data);
            $this->template->render();
    }
	
}

