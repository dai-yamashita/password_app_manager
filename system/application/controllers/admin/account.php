<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
cook by mandoy add me @facebook.com/artheman
*/
class Account extends Controller {
    var $browse;

    function  __construct() {
        parent::Controller();
        if (! $this->dx_auth->is_logged_in()) redirect('login');
        $this->load->library( 'form_validation' );
        $this->logged_userid = $this->dx_auth->get_user_id();        
        #$this->dx_auth->check_uri_permissions();
        $this->load->model('dx_auth/users', 'users');
        $this->load->model('dx_auth/roles', 'roles');

    }

// <editor-fold defaultstate="collapsed" desc=" index ">
    function index() {
        $this->editprofile();
    }

// </editor-fold>

    
// <editor-fold defaultstate="collapsed" desc=" form ">
    function editprofile() {
        $id = isset($_POST['user_id']) ? intval($_POST['user_id']) : $this->uri->segment(4);
        if ($id != $this->logged_userid) $this->dx_auth->deny_access('deny');        
        
        if(! $id) {
            $this->form_validation->set_rules('username', 'username', 'required|alpha_numeric|callback_username_check');
            $this->form_validation->set_rules('password', 'password', 'required|trim');
        }

        if ( ! $this->dx_auth->is_role(array('owner')) && $id ) $this->form_validation->set_rules('password', 'password', 'required|trim');
        if($id) {
            $oldusername = $this->input->post( 'username' );
            $this->mdl_users->where = array('users.id' => $id);
            $rs = $this->mdl_users->get_all_users(array('resulttype' => 'row_array'));
            $data['results'] = $rs;
            if ($oldusername != $rs['username']) {
                $this->form_validation->set_rules('username', 'username', 'required|alpha_numeric|callback_username_check');
            }
        }
        $this->load->view( 'validations/user' );
        $this->form_validation->set_error_delimiters('<p style="padding:2px" >', '</p>');
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $data['roles'] = $this->roles->get_all()->result_array();
        $data['department'] = $this->mdl_department->get_all_department();
        $data['usergroups'] = $this->mdl_department->get_user_groups( array('userid' => $id) );
        if ( $this->form_validation->run() ) {
            $this->mdl_users->user_id = $id;
            $this->mdl_users->save();
            $this->session->set_flashdata( 'flash', 'Successfully saved user.' ) ;
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }else {
            $data['flash']['error'] = validation_errors();
            $data['results']['user_id']  = $id;
            $this->template->write_view('content', 'default/account_form', $data);
        }
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" sendrequest_access ">
    function sendrequest_access() {
        $timezone = $this->sitesettings->get_settings('timezone');
        $isdaylightsaving = $this->sitesettings->get_settings('isdaylightsaving');
        $currenttime = gmt_to_local(time(), $timezone, $isdaylightsaving );
        $d = array();
        $id = $_POST['req_group'];
        $rs1 = $this->db->get_where('alerts', array('from' => $this->logged_userid, 'groupid' => $id));
        if ($rs1->num_rows() > 0) {
            $arr = array ('flashmessage' => "Request error: you have already sent a request. Kindly contact the admin for approval.",
                'result' => 'error' );
            echo json_encode($arr);
        } else {
            $rs = $this->mdl_users->get_user_by_id($this->logged_userid);
            $fullname = $rs['firstname'] . ' ' . $rs['lastname'];
            $rs = $this->mdl_department->get_department_by_id( $id, array( 'resulttype' => 'row_array'));
            $department = $rs['groupname'];
            $msg =
    "$fullname would like to join in the group: $department
    <br />
    Thanks<br />
    Trimorp Team
    ";
            $data = array(
                    'title'         => "New member requested to join." ,
                    'alert'         => $msg,
                    'created'       => $currenttime,
                    'isread'        => 0,
                    'from'          => $this->logged_userid,
                    'to'            => -1,
                    'groupid'       => $id,
            );
            $this->db->insert('alerts', $data) ;
            $arr = array ('flashmessage' => "Successfully send request.",
                'result' => 'success' );
            echo json_encode($arr);
        }

    }

// </editor-fold>




}