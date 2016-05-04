<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Alert extends Controller {
    function __construct() {
        parent::Controller();
        if (! $this->dx_auth->is_logged_in()) redirect('login');
        $this->dx_auth->check_uri_permissions();
        $this->load->library( 'form_validation' );
        $this->form_validation->set_error_delimiters('<p style="padding:2px" >', '</p>');
        $this->paging = $this->paging->get_paging_template();
        $this->logged_userid = $this->dx_auth->get_user_id();

    }

    function index() {
        $this->browse();
    }

// <editor-fold defaultstate="collapsed" desc="form" >
    function form() {
        /*$this->form_validation->set_rules('project', 'project name', 'required|trim|max_length[254]');
		$id = isset($_POST['projectid']) ? intval($_POST['projectid']) : $this->uri->segment(4);
 		if ($id) {
			$this->mdl_projects->where = array('projectid' => $id);
 			$rs = $this->mdl_projects->get_all_projects(array('resulttype' => 'row_array'));
			$data['results'] = $rs;
		}
		$data['flash']['success'] = $this->session->flashdata( 'flash' );
		if ( $this->form_validation->run() ) {
			$this->mdl_projects->projectid = $id;
			$this->mdl_projects->save();
			$this->session->set_flashdata( 'flash', 'Successfully saved project.' ) ;
			header('location:' . $_SERVER['HTTP_REFERER'] );
		}else{
			$data['flash']['error'] = validation_errors();
			$this->template->write_view('content', 'default/project_form', $data);
		}
		$this->template->render();*/
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" view ">
    function view() {
        $data = array();
        $id = isset($_POST['alertid']) ? intval($_POST['alertid']) : $this->uri->segment(4);
        if ($id) {
            $this->db->where('alertid', $id);
            $rs = $this->db->get('alerts')->row_array();
            if ($rs) {
                $data['results'] = $rs;
            }
            $this->db->where('alertid', $id);
            $this->db->update('alerts', array('isread' => 1));
        }


        $this->template->write_view('content', 'default/alert_view', $data);
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" browse ">
    function browse() {
        $this->load->library( 'pagination' );
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $uri = $this->uri->uri_to_assoc(2);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        #print_r($uri);

        if ( $this->input->post('delete') ) {
            $this->_delete_alerts();
        }

        if ($this->dx_auth->is_role( array('member'))) {
            $rs = $this->alerts->check_user_alerts($this->logged_userid);
            $total_rows = count($rs);

            $this->paging['per_page']		= 20;
            $this->paging['uri_segment'] 	= 5;
            $this->paging['base_url'] 		= site_url('admin/alert/browse/p');
            $this->paging['total_rows'] 	= $total_rows ;
            $this->pagination->initialize($this->paging);
            isset($uri['p']) ? $this->db->limit($this->paging['per_page'], $uri['p'])  : '' ;
            $rs = $this->alerts->check_user_alerts($this->logged_userid);
            $data['pagination'] = $this->pagination->create_links();
            $data['results'] 	= $rs;
            $this->template->write_view('content', 'default/alert_browse', $data);
        } else {
            $rs = $this->alerts->check_user_alerts($this->logged_userid);
            $total_rows = count($rs);
            $this->paging['per_page']		= 20;
            $this->paging['uri_segment'] 	= 5;
            $this->paging['base_url'] 		= site_url('admin/alert/browse/p');
            $this->paging['total_rows'] 	= $total_rows ;
            $this->pagination->initialize($this->paging);
            isset($uri['p']) ? $this->db->limit($this->paging['per_page'], $uri['p'])  : '' ;
            //$rs = $this->db->get('alerts')->result_array();
            $rs = $this->alerts->check_user_alerts($this->logged_userid);
            $data['pagination'] = $this->pagination->create_links();
            $data['results'] 	= $rs;
            $this->template->write_view('content', 'default/alert_browse', $data);
        }
        
        $this->template->render();
    }
// </editor-fold>



    function _delete_alerts() {
        #if ($this->dx_auth->is_role('member')) $this->dx_auth->deny_access('deny');
        $chk = isset($_POST['chk']) ? $_POST['chk'] : '' ;
        if ( is_array($chk) && count($chk) > 0) {
            foreach($chk as $k => $v) {
                $this->db->where('alertid', $v);
                $this->db->delete('alerts');
            }
            /*if (!empty($id)) $this->db->delete('projects', array('projectid' => intval($id))); */
            $this->session->set_flashdata( 'flash', 'Successfully delete.' ) ;
            redirect('admin/alert/browse');
        }

    }

    function delete($id) {
        //if ($this->dx_auth->is_role('member')) $this->dx_auth->deny_access('deny');
        if (!empty($id)) $this->db->delete('alerts', array('alertid' => intval($id)));
        $this->session->set_flashdata( 'flash', 'Successfully delete.' ) ;
        redirect('admin/alert/browse');
    }

    function is_project_exist($id) {
        $rs = $this->mdl_projects->get_project_by_id($id);
        if (FALSE == $rs) {
            return FALSE;
        }
        return $rs;
    }


}

