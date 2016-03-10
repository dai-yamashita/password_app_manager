<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Forgotpassword extends Controller {
    function __construct() {
        parent::Controller();
    }

    function index( ) {
        $data = array();
        $flash = $this->session->flashdata('flash');
        $data['flash']['success'] = ($flash == 'forgotpw') ? 'Your password will be sent in your email. Please check in your email or Spam box.'  : '';
        if ($_POST) {
            $email = $this->input->post('email');
            $rs = $this->mdl_users->get_user_by_email($email);
            if ($rs) {
                $admin_email = $this->sitesettings->get_settings('admin_email');
                $noreply_email = $this->sitesettings->get_settings('noreply_email');
                $id = $rs['id'];
                $username = $rs['username'];
                $password = $rs['clearpassword'];
                #print_r($rs);
                $config['protocol'] = 'sendmail';
                $config['mailpath'] = '/usr/sbin/sendmail';
                $config['charset'] = 'iso-8859-1';
                $config['wordwrap'] = TRUE;
                $config['mailtype'] = 'html';
                $this->load->library('email');
                $this->email->initialize($config);
                $this->email->from($noreply_email, 'noreply');
                $this->email->to($email);
                $this->email->subject('Forgot password');
$url = site_url("admin/login");
$msg = "You have requested a password. See below the details:
<br><br>ID: $id
<br>Username: $username
<br>Password: $password
<br><a href='$url' >Click here to Login</a>
<br>Thanks
<br>Trimorph Team
" ;
                $this->email->message($msg);
                $this->email->send();
                #echo $this->email->print_debugger();
                $this->session->set_flashdata('flash', 'forgotpw');
                redirect("forgotpassword");
            }
        }
        else {
            $this->load->view( 'forgotpassword', $data );
        }
        
    }

}

