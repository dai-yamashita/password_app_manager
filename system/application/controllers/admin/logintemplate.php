<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Logintemplate extends Controller {
    var $browse;
    function __construct() {
        parent::Controller();
        if (! $this->dx_auth->is_logged_in()) redirect('login');
        $this->dx_auth->check_uri_permissions();
        $this->load->library( 'form_validation' );
        $this->load->library( 'pagination' );
        $this->form_validation->set_error_delimiters('<p style="padding:2px" >', '</p>');
        $this->paging = $this->paging->get_paging_template();
        $this->tlogin_templates = 'login_templates';
    }

// <editor-fold defaultstate="collapsed" desc=" index ">
    function index() {
        $this->browse();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" browse ">
    function browse() {
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $uri = $this->uri->uri_to_assoc(2);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        $total_rows = count($this->mdl_logintemplates->get_all_logintemplates());
        $this->paging['per_page']	= 20;
        $this->paging['uri_segment'] 	= 5;
        $this->paging['base_url'] 	= site_url('admin/logintemplate/browse/p');
        $this->paging['total_rows'] 	= $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['results'] 	= $this->mdl_logintemplates->get_all_logintemplates( array('rows' => $this->paging['per_page'], 'offset' => $uri['p'] ) );
        $this->template->write_view('content', 'default/logintemplate_browse', $data);
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" form ">
    function form() {
        $this->form_validation->set_rules('name', 'template name', 'required|trim');
        $this->form_validation->set_rules('template', 'template body', 'required|trim');
        $id = isset($_POST['templateid']) ? intval($_POST['templateid']) : $this->uri->segment(4);
        if(!$id) $this->form_validation->set_rules('name', 'template name', 'required|trim|callback_templatename_check');
        if($id) {
            $rs = $this->mdl_logintemplates->get_template_by_id( $id, array('resulttype' => 'row_array'));
            $data['results'] = $rs;
            $templatename = $this->input->post( 'name' );
            if ($templatename != $rs['name']) {
                $this->form_validation->set_rules('name', 'template name', 'required|trim|callback_templatename_check');
            }
        }
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        if ( $this->form_validation->run() ) {
            $this->mdl_logintemplates->templateid = $id;
            $this->mdl_logintemplates->save();
            $this->session->set_flashdata( 'flash', 'Successfully saved template.' ) ;
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }else {
            $data['flash']['error'] = validation_errors();
            $this->template->write_view('content', 'default/logintemplate_form', $data);
        }
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" delete ">
    function delete($id) {
        if ($this->dx_auth->is_role('member')) $this->dx_auth->deny_access('deny');
        if (!empty($id)) $this->db->delete($this->tlogin_templates, array('templateid' => intval($id)));
        $this->session->set_flashdata( 'flash', 'Successfully delete template.' ) ;
        redirect('admin/logintemplate/browse');
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" templatename_check ">
    function templatename_check($template) {
        $res = $this->mdl_logintemplates->template_check($template);
        if ($res) {
            $this->form_validation->set_message('templatename_check', "Template name $template already exist. Please choose another.");
            return FALSE;
        }
        return TRUE;
    }
// </editor-fold>

 
}