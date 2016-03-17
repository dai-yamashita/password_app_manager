<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
cook by mandoy add me @facebook.com/artheman
*/
class User extends Controller {
    var $browse;

    function __construct() {
        parent::Controller();
        if (! $this->dx_auth->is_logged_in()) redirect('login');
        $this->dx_auth->check_uri_permissions();
        $this->load->helper( 'custom' );
        $this->load->helper('string');
        $this->load->library( 'pagination' );
        $this->load->library( 'form_validation' );
        $this->load->library( 'csvimport' );
        $this->load->model('dx_auth/users', 'users');
        $this->load->model('dx_auth/roles', 'roles');
        $this->logged_userid = $this->dx_auth->get_user_id();
        $this->paging = $this->paging->get_paging_template();
        $this->allowed_type = 'csv|txt';
        $this->csv_path = './uploads/csv/';
        $this->tdomain_customfields = 'domain_customfields';
        $this->tusers = 'users';
        $this->tuser_groups = 'user_groups';
        $this->tuser_domains = 'user_domains';
        $this->tuser_projects = 'user_projects';
        $this->tuser_profile = 'user_profile';
        $this->sessd = array('impfile'   => '' ,'delim'     => '' , 'enc' => '' , 'step' => '' );
        // set the maximum memory limit
	ini_set('memory_limit', '150M');
    }

    function index() {
        $this->browse();
    }

// <editor-fold defaultstate="collapsed" desc=" add_to_project ">
    function add_to_project($gid) {
        $gid = !empty($gid) ? $gid : $this->input->post('gid');
        $data['flash']['success'] = $this->session->flashdata('flash');
        $rs = $this->mdl_users->get_user_by_id($gid);
        $fullname = $rs['firstname'] . '&nbsp;' . $rs['lastname'];
        if ($_POST) {
            // save the user project access
            $this->mdl_users->save_projectaccess();
            // send alert to the user
            $this->alerts->alert_add_to_project();
            $this->session->set_flashdata( 'flash', 'Successfully saved project access.' ) ;
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }
        $uri = $this->uri->uri_to_assoc(1);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        #echo $uri['p'];
        $tmpsortby = ($this->browse['sort']) == 'desc' ? 'asc' : 'desc';
        $data['sortby'] = $tmpsortby ;
        if( !empty($uri['field'])) {
            $this->browse['field']       = $uri['field'];
            $this->browse['sort']        = $uri['sort'];
            $this->browse['extra']       = "/field/{$this->browse['field']}/sort/{$this->browse['sort']}";
        }
        $total_rows = count($this->mdl_projects->get_all_projects());
        $this->paging['per_page'] = 100;
        $this->paging['uri_segment'] = 6;
        $this->paging['base_url'] = site_url( "admin/user/add_to_project/$gid/p/");
        $this->paging['total_rows'] = $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['domainaccess_title'] = "Update project access of $fullname";
        $data['gid'] = !empty($gid) ? $gid : '';
        $this->mdl_projects->order_by = 'project ASC';
        $data['results'] = $this->mdl_projects->get_all_projects( array('rows' => $this->paging['per_page'], 'offset' => $uri['p'] ) );
        $this->mdl_projects->order_by = '';
		
        $rs = $this->mdl_domains->get_all_userprojects( $gid, array('rows' => '', 'offset' => '' ));
        $data['results2'] = $this->mdl_domains->to_array($rs, 'projectid');
        $this->template->write_view('content', 'default/usertoproject', $data);
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" form ">
    function form() {
        $id = isset($_POST['user_id']) ? intval($_POST['user_id']) : $this->uri->segment(4);
        if ($this->dx_auth->is_role('member')) {
            if ($id != $this->logged_userid) $this->dx_auth->deny_access('deny');
        }
        if(empty($id)) {
            $this->form_validation->set_rules('username', 'username', 'required|trim|alpha_numeric|callback_username_check');
            $this->form_validation->set_rules('password', 'password', 'required|trim');            
        }

        //if ( ! $this->dx_auth->is_role('owner') && $id ) $this->form_validation->set_rules('password', 'password', 'required|trim');
        if(!empty($id)) {
            $oldusername = $this->input->post( 'username' );
            $this->mdl_users->where = array('users.id' => $id);
            $rs = $this->mdl_users->get_all_users(array('resulttype' => 'row_array'));
            $data['results'] = $rs;
            if ($oldusername != $rs['username']) {
                $this->form_validation->set_rules('username', 'username', 'required|trim|alpha_numeric|callback_username_check');
            }
        }
        $this->load->view( 'validations/user' );
        $this->form_validation->set_error_delimiters('<p style="padding:2px" >', '</p>');
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $data['roles'] = $this->roles->get_all()->result_array();
        $data['department'] = $this->mdl_department->get_all_department();
        $data['usergroups'] = $id ? $this->mdl_department->get_user_groups( array('userid' => $id) ) : '';
        if ( $this->form_validation->run() ) {
            $this->mdl_users->user_id = $id;
            $this->mdl_users->save();
            // save user to group
            $this->mdl_users->save_user_to_group();
            $this->session->set_flashdata( 'flash', 'Successfully saved user.' ) ;
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }else {
            $data['flash']['error'] = validation_errors();
            $data['results']['user_id']  = $id;
            $this->template->write_view('content', 'default/user_form', $data);
        }
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" browse ">
    function browse() {
        $this->load->helper('date');
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $uri = $this->uri->uri_to_assoc(2);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        $total_rows = count($this->mdl_users->get_all_users());
        $this->paging['num_links'] = 5;
        $this->paging['per_page'] = 20;
        $this->paging['uri_segment'] = 5;
        $this->paging['base_url'] = site_url('admin/user/browse/p');
        $this->paging['total_rows'] = $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['results'] = $this->mdl_users->get_all_users( array('rows' => $this->paging['per_page'], 'offset' => $uri['p'] ) );
        $this->template->write_view('content', 'default/user_browse', $data);
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" domainaccess ">
    function domainaccess($uid) {
        $this->load->helper('date');
        $data['flash']['success'] = $this->session->flashdata( 'flash' );

        $data['userid'] = !empty($uid) ? $uid : '';
        $uid = !empty($uid) ? $uid : $this->input->post('userid');
        $rs = $this->mdl_users->get_user_by_id($uid);
        if ($_POST) {
            $this->session->set_flashdata( 'flash', 'Successfully saved access.' ) ;
            $this->mdl_domains->save_user_domainaccess();



            header('location:' . $_SERVER['HTTP_REFERER'] );
        }
        $data['domainaccess_title'] = "Update domain access of " . $rs['firstname'] . ' ' . $rs['lastname'] ;
        $uri = $this->uri->uri_to_assoc(1);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        $tmpsortby = ($this->browse['sort']) == 'desc' ? 'asc' : 'desc';
        $data['sortby'] = $tmpsortby ;
        if( !empty($uri['field'])) {
            $this->browse['field']       = $uri['field'];
            $this->browse['sort']        = $uri['sort'];
            $this->browse['extra']       = "/field/{$this->browse['field']}/sort/{$this->browse['sort']}";
        }
        $total_rows = count($this->mdl_domains->get_all_domains());
        $this->paging['per_page'] = 100;
        $this->paging['uri_segment'] = 6;
        $this->paging['base_url'] = site_url("admin/user/domainaccess/$uid/p");
        $this->paging['total_rows'] = $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['gid'] = !empty($gid) ? $gid : '';
        $this->mdl_domains->order_by = 'projects.project ASC';
		$data['results'] = $this->mdl_domains->get_all_domains( array(
                'rows' => $this->paging['per_page'], 'offset' => $uri['p'],
                'field' => $this->browse['field'], 'sort' => $this->browse['sort'] )
        );
		$this->mdl_domains->order_by = '';
        $rs = $this->mdl_domains->get_all_userdomains( $uid, array('rows' => '', 'offset' => '' ) );

        $data['results2'] = $this->mdl_domains->to_array($rs, 'domain_id');
        #$data['results'] = $this->mdl_domains->get_all_userdomains($uid, array('rows' => '', 'offset' => '' ) );
        $this->template->write_view('content', 'default/domainaccess', $data);
        $this->template->render();
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" viewdomainaccess ">
    function viewdomainaccess($uid) {
        $this->load->helper('date');
        $data['flash']['success'] = $this->session->flashdata( 'flash' );

        $data['userid'] = !empty($uid) ? $uid : '';
        $uid = !empty($uid) ? $uid : $this->input->post('userid');
        $rs = $this->mdl_users->get_user_by_id($uid);
        if ($_POST) {
            $this->session->set_flashdata( 'flash', 'Successfully saved access.' ) ;
            $this->mdl_domains->save_user_domainaccess();
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }
        $data['domainaccess_title'] = "All domain access of " . $rs['firstname'] . ' ' . $rs['lastname'] ;
        $uri = $this->uri->uri_to_assoc(1);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        $tmpsortby = ($this->browse['sort']) == 'desc' ? 'asc' : 'desc';
        $data['sortby'] = $tmpsortby ;
        if( !empty($uri['field'])) {
            $this->browse['field']       = $uri['field'];
            $this->browse['sort']        = $uri['sort'];
            $this->browse['extra']       = "/field/{$this->browse['field']}/sort/{$this->browse['sort']}";
        }
        $total_rows = count($this->mdl_domains->get_all_userdomains($uid));
        $this->paging['per_page'] = 100;
        $this->paging['uri_segment'] = 6;
        $this->paging['base_url'] = site_url("admin/user/viewdomainaccess/$uid/p");
        $this->paging['total_rows'] = $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['gid'] = !empty($gid) ? $gid : '';
        $this->mdl_domains->order_by = 'projects.project ASC';
        $data['results'] = $this->mdl_domains->get_all_userdomains($uid, array(
                'rows' => $this->paging['per_page'], 'offset' => $uri['p'],
                'field' => $this->browse['field'], 'sort' => $this->browse['sort'] )
        );
        $this->mdl_domains->order_by = '';
        #$rs = $this->mdl_domains->get_all_userdomains( $uid, array('rows' => '', 'offset' => '' ) );
        #$data['results2'] = $this->mdl_domains->to_array($rs, 'domain_id');
        #$data['results'] = $this->mdl_domains->get_all_userdomains($uid, array('rows' => '', 'offset' => '' ) );
        $this->template->write_view('content', 'default/viewdomainaccess', $data);
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" viewprojectacces ">
function viewprojectaccess($uid) {
        $this->load->helper('date');
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $data['userid'] = !empty($uid) ? $uid : '';
        $uid = !empty($uid) ? $uid : $this->input->post('userid');
        $rs = $this->mdl_users->get_user_by_id($uid);
        $data['domainaccess_title'] = "All project access of " . $rs['firstname'] . ' ' . $rs['lastname'] ;
        $uri = $this->uri->uri_to_assoc(1);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        $tmpsortby = ($this->browse['sort']) == 'desc' ? 'asc' : 'desc';
        $data['sortby'] = $tmpsortby ;
        if( !empty($uri['field'])) {
            $this->browse['field']       = $uri['field'];
            $this->browse['sort']        = $uri['sort'];
            $this->browse['extra']       = "/field/{$this->browse['field']}/sort/{$this->browse['sort']}";
        }
        $total_rows = count($this->mdl_domains->get_all_userdomains($uid));
        $this->paging['per_page'] = 100;
        $this->paging['uri_segment'] = 6;
        $this->paging['base_url'] = site_url("admin/user/viewprojectaccess/$uid/p");
        $this->paging['total_rows'] = $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['gid'] = !empty($gid) ? $gid : '';
        $this->mdl_domains->order_by = 'projects.project ASC';
        $data['results'] = $this->mdl_domains->get_all_userdomains($uid, array(
                'rows' => $this->paging['per_page'], 'offset' => $uri['p'],
                'field' => $this->browse['field'], 'sort' => $this->browse['sort'] )
        );
        $this->mdl_domains->order_by = '';
        #$rs = $this->mdl_domains->get_all_userdomains( $uid, array('rows' => '', 'offset' => '' ) );
        #$data['results2'] = $this->mdl_domains->to_array($rs, 'domain_id');
        #$data['results'] = $this->mdl_domains->get_all_userdomains($uid, array('rows' => '', 'offset' => '' ) );
        $rs = $this->mdl_domains->get_all_userprojects( $uid, array('rows' => '', 'offset' => '' ));
        #pre($rs);
        #$data['results'] = $this->mdl_domains->to_array($rs, 'projectid');
        $data['results'] = $rs;
        $this->template->write_view('content', 'default/viewprojectaccess', $data);
        $this->template->render();
}

// </editor-fold>

// delete his domain-access
// <editor-fold defaultstate="collapsed" desc=" deletedomainaccess ">
function deletedomain() {
    $uri = $this->uri->uri_to_assoc(1);
    #pre($uri );
    $domainid = intval($uri['deletedomain']);
    $userid = intval($uri['userid']);
    if ($this->dx_auth->is_role('member')) $this->dx_auth->deny_access('deny');
    $rs = $this->db->get_where('user_domains', array('domain_id' => $domainid, 'user_id' => $userid ));
    if ($rs->num_rows() > 0) {
        $this->db->delete('user_domains', array('domain_id' => $domainid, 'user_id' => $userid));
        $this->session->set_flashdata( 'flash', 'Successfully delete domain.' );
    }else {
        $this->session->set_flashdata( 'flash', 'Cannot delete the domain that dont belong to him.');
    }
    
    redirect("admin/user/viewdomainaccess/$userid");
}
// </editor-fold>

// delete user
// <editor-fold defaultstate="collapsed" desc=" delete ">
    function delete($id) {
        if ($this->dx_auth->is_role('member')) $this->dx_auth->deny_access('deny');
        if (!empty($id)) {
            $this->db->delete($this->tusers, array('id' => intval($id)));
            // delete user domain access
            $this->db->delete($this->tuser_domains, array('user_id' => $id));
            // delete domain custom field
            $this->db->delete($this->tdomain_customfields, array('user_id' => $id));
            // deelte user profile
            $this->db->delete($this->tuser_profile, array('user_id' => $id));
            $this->session->set_flashdata( 'flash', 'Successfully delete user.' ) ;
        }
        redirect('admin/user/browse');
    }
// </editor-fold>


// <editor-fold defaultstate="collapsed" desc=" delete_from_group ">
function delete_from_group() {
$uri = $this->uri->uri_to_assoc(1);
$groupid = $uri['delete_from_group'];
$userid = $uri['user'];
    if (!empty($userid) && !empty($groupid)) {
        $this->db->where(array('userid' => $userid, 'deptid' => $groupid));
        $this->db->delete($this->tuser_groups);
        $this->session->set_flashdata( 'flash', 'Successfully remove user from group.');
        header('location:' . $_SERVER['HTTP_REFERER'] );
    }
}
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="import">
    function import($format='csv') {
        $this->load->library('importer');
        $this->load->dbutil();
        $data = array();
        $totalrows = 0;
        $format = isset($_POST['importfmt']) ? $_POST['importfmt'] : (!empty($format) ? $format : 'csv');
        $data['importfmt'] = $format ;
        #echo '<pre>';
        if ($_FILES) {
            if ($format == 'csv') $this->allowed_type = 'csv|txt';
            if ($format == 'xml') $this->allowed_type = 'xml';
            $uploadeddata = $this->do_upload(array( 'encrypt_name' => TRUE));
            #print_r($uploadeddata);
            if ( isset($uploadeddata['upload_data']) ) {
                $impfile = $this->csv_path . $uploadeddata['upload_data']['file_name'] ;
                $this->session->set_flashdata('impfile', $impfile);
                if ($format == 'csv') {
                    $totalrows = $this->importer->import_csv(array('tablename' => $this->tusers, 'primary_key' => 'id', 'filename' => $impfile ));
                }
                if ($format == 'xml') {
                    $totalrows = $this->importer->import_xml(array('tablename' => $this->tusers, 'primary_key' => 'id', 'filename' => $impfile ));
                }
                if ($totalrows > 0) {
                    $this->session->set_flashdata('flash', "Successfully imported data" ) ;
                }else{
                    $this->session->set_flashdata('flash', 'No data has been imported.' ) ;
                }
                redirect("admin/user/import/$format");
            } else {
                $data['flash']['defaulterror'] = $uploadeddata['upload_error'] ;
            }
        }
                // delete the imported csv file.
        $csvfile = $this->session->flashdata( 'impfile' );
        if (!empty($csvfile)){
            $this->session->unset_userdata('impfile');
            @unlink($csvfile);
        }
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $this->template->write_view('content', 'default/user_import', $data );
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" import_step ">
    function import_step() {
        $uri = $this->uri->uri_to_assoc(1);
        $type = strtolower($uri['import_step']);
        $type = !empty($type) ? $type : (!empty($_POST['import_step']) ? $_POST['import_step'] : '');
        $data = array();
        $step = !empty($uri['step']) ? $uri['step'] : ( !empty($_POST['step']) ? $_POST['step'] : '' );
        $field_list = array();
        $query = $this->db->query('SELECT tmpid, role_id, username, password, email,
            created, firstname, lastname, skypeid, position  FROM users');
        $field_list = $query->list_fields();
        #pre($field_list);
        $data['step'] = $step;
        $data['importfmt'] = $type;
        $data['flash']['success'] = $this->session->flashdata('flash');
        if ($type == 'csv') {
            $impfile        = $this->session->userdata('impfile');
            $delimiter      = $this->session->userdata('delim');
            $enclosure      = $this->session->userdata('enc');
            #$params = array_merge( $default, $params );
            // step1
            if ($step == 1) {
                if ($_FILES) {
                    $uploadeddata = $this->do_upload( array( 'encrypt_name' => TRUE, 'allowed_types' => 'csv|txt' ) );
                    //pre($uploadeddata);
                    if( isset($uploadeddata['upload_data'])) {
                        $impfile = $uploadeddata['upload_data']['file_name'] ;
                        $sessd = array(
                            'impfile'   => $uploadeddata['upload_data']['file_name'],
                            'delim'     => $_POST['delimiter'], 'enc' => $_POST['enclosure'], 'step' => 'step1' );
                        $this->session->set_userdata($sessd);
                        redirect('admin/user/import_step/csv/step/2');
                    }
                    else {
                        $data['flash']['error'] = $uploadeddata['upload_error'] ;
                        $this->template->write_view('content', 'default/import/step1_user', $data );
                    }
                } else {
                    $this->session->unset_userdata($this->sessd);
                    $data['fields'] = $this->db->list_fields($this->tusers);
                    $this->template->write_view('content', 'default/import/step1_user', $data );
                }
            }

            // step2
            if ($step == 2) {
                $step = $this->session->userdata('step');
                if ($step !== 'step1') redirect('admin/user/import_step/csv/step/1');

                if ($_POST) {
                    $field_without_nulls    = array_filter($_POST['field_list'], "is_field_negative" );
                    $csvfield_index         = $_POST['csv_field'];
                    $tmp                    = array();
                    $tmpfields          = array();
                    $conflict           = array();
                    $m 			= count($field_list);
                    $duplicate_found    = FALSE ;
                    $error_found	= FALSE ;
                    # check nato if naa ba sulod ang csvtextbox g-input sa user
                    $str = '' ;
                    $blank_txtbox_found = FALSE ;
                    foreach( $field_without_nulls as $f => $v ) {
                        if ( ! isset( $csvfield_without_nulls[$f])) {
                            $str .= sprintf( 'error_blank_csvfield', $v );
                            $blank_txtbox_found = TRUE ;
                            $error_found	= TRUE ;
                        }
                    }
                    // must have at least two(2) fields was selected
                    if ( count($field_without_nulls) < 3 ) {
                        $data['flash'] = "<div class ='error-note' >Please select at least 2 fields.</div>";
                        $error_found = TRUE ;
                    }
                    else {
                        for($i=0; $i < $m; $i++) {
                            foreach( $field_list as $k => $f ) {
                                if ( isset( $_POST['field_list'][$i] ) && ($_POST['field_list'][$i] == $f) ) {
                                    if ( ! isset( $tmp[$f])) {
                                        $tmpfields[] = $f ;
                                        $tmp[$f] = TRUE ;
                                    }
                                    else {
                                        $conflict[$f] = TRUE ;
                                        $duplicate_found = TRUE ;
                                    }
                                }
                            }
                        }

                        if ( $duplicate_found ) {
                            $str = '';
                            foreach($conflict as $k => $v ) {
                                $str .= " $k <br />" ;
                            }
                            $data['flash'] = "<div class ='error-note' >Error duplicated fields:<br>" . $str . "</div>" ;
                            $error_found = TRUE ;
                        }
                        else {

                            $s = array (
                                    'postfld' 		=> $field_without_nulls,
                                    'csvfld' 		=> array_keys($csvfield_index),
                                    'step' 		=> 'step3'
                            );
                            $this->session->set_userdata($s);
                            redirect('admin/user/import_step/csv/step/3');
                        }
                    }
                    if ( $error_found ) {
                        $this->template->write_view('content', 'default/import/step2_user', $data );
                    }
                }
                else {
                    $this->csvimport->init($this->csv_path . $impfile, '', $delimiter, $enclosure );
                    $rows = $this->csvimport->get( );
                    #pre($rows );
                    $data['csv_total_fields'] = @count($rows[0]);
                    $use_header = 0;
                    $data['csv_sample_data'] = $use_header ? @array_values($rows[0]) : @array_values($rows[rand(0, count($rows)-1)]);
                    #pre($data['csv_sample_data']);
                    $data['prevurl'] = site_url("admin/user/import_step/csv/step/1");
                    $data['field_list'] = $field_list;
                    $this->template->write_view('content', 'default/import/step2_user', $data );
                }
            }
            // step3
            if ($step == 3) {
                $step = $this->session->userdata('step');
                if ($step !== 'step3') redirect('admin/user/import_step/csv/step/2');
                $timezone = $this->sitesettings->get_settings('timezone');
                $isdaylightsaving = $this->sitesettings->get_settings('isdaylightsaving');
                $currenttime = gmt_to_local(time(), $timezone, $isdaylightsaving );
                $field_without_nulls		 = $this->session->userdata( 'postfld' ) ;
                $csvfield_without_nulls		 = $this->session->userdata( 'csvfld' ) ;
                #pre($csvfield_without_nulls);
                if ($_POST) {
                    // save to the db
                    $selectedcsvrows            = $this->input->post( 'ischecked' );
                    $this->csvimport->init($this->csv_path . $impfile, false, $delimiter, $enclosure );
                    $rows = $this->csvimport->get( );
                    $selectedcsvrowstmp = is_array($selectedcsvrows) ? array_keys(array_flip( $selectedcsvrows )) : array();
                    for($j=0; $j < count($rows); $j++) {
                            $u          = array();
                            $tmpdata    = array();
                            foreach( $csvfield_without_nulls as $k => $v ) {
                                if ( isset($field_without_nulls[$k]) ) {
                                    if ( in_array( $field_without_nulls[$k], $field_list) ) {
                                        $u[] = array($field_without_nulls[$k] => $rows[$j][$k] );
                                    }
                                }
                            }
                            // lets get the field for user table
                            foreach( $u as $m => $v2 ) {
                                $k = key($v2);
                                $tmpdata[$k] = current( $v2 );
                            }
                            // re-create the pasword
                            $pw = random_string('numeric');
                            $tmpdata['clearpassword'] = $pw;
                            $tmpdata['password'] = $this->dx_auth->_encode($pw);
                            unset($tmpdata['id']);
                            if (in_array( $j, $selectedcsvrowstmp)) {
                                $this->db->insert($this->tusers, $tmpdata);
                            }
                    }
                    $this->session->set_flashdata('flash', 'Successfully imported data.');
                    $this->session->unset_userdata($this->sessd);
                    redirect("admin/user/import_step/csv/step/1");
                }
                else {
                    $this->csvimport->init($this->csv_path . $impfile, false, $delimiter, $enclosure );
                    $rows = $this->csvimport->get();
                    //pre($rows);
                    for ($j=0; $j < count($rows); $j++) {
                        if ( $rows[$j] ) {
                            foreach( $csvfield_without_nulls as $k => $v ) {
                                if ( isset($field_without_nulls[$k]) ) {
                                    $u[$j][$field_without_nulls[$k]] = $rows[$j][$k] ;
                                }
                            }
                        }
                    }
                    $data['imported_csvdata'] = $u ;
                    $data['csvrows'] = $rows ;
                    $this->template->write_view('content', 'default/import/step3_user', $data );
                }
            }


        }

        if ($type == 'xml') {

        }

        $this->template->render();
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" export ">
    function export($format='csv') {
        $data = array();
        $format = isset($_POST['exportfmt']) ? $_POST['exportfmt'] : (!empty($format) ? $format : 'csv');
        $data['exportfmt'] = $format ;
        if ($_POST) {
            $this->load->library('exporter');
            $sql = "SELECT
                tmpid, role_id, username, password, email, created, firstname, lastname,
                skypeid, position, last_ip FROM users
                ";
            // CSV
            if ($format == 'csv') {
                $filename = date('m-d-Y') . '-users.csv';
                $this->exporter->export_csv( array('filename' => $filename, 'sql_query' => $sql));
            }
            // XML
            if ($format == 'xml') {
                $filename = date('m-d-Y') . '-users.xml';
                $this->exporter->export_xml( array('filename' => $filename, 'sql_query' => $sql));
            }
        }
        $this->template->write_view('content', 'default/user_export', $data);
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" hello ">
    function hello() {
        if( $this->dx_auth->is_role('editor')) {
            echo "hi editor!";
        } else {
            echo "hello";

        }
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" callback username_check ">
    function username_check($username) {
        $res = $this->users->check_username($username);
        if ($res->num_rows() > 0) {
            $this->form_validation->set_message('username_check', 'Username already exist. Please choose another username.');
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" do_upload ">
    function do_upload( $params ) {
        $default = array(
                'upload_path'		=> $this->csv_path ,
                'allowed_types'		=> $this->allowed_type
                ) ;
        if ( isset($_FILES['userfile']) && empty($_FILES['userfile']['name']) ) {
            return array('upload_error' => 'UploadError: File is empty' ) ;
        }
        elseif ( isset($_FILES['userfile']) && $this->_check_valid_extension( $_FILES['userfile']['name'] ) === FALSE ) {
            return array('upload_error' => 'UploadError: Invalid file extension' ) ;
        }
        else {
            $config = array_merge( $default, $params );
            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload()) {
                $error = array('upload_error' => $this->upload->display_errors());
                return $error ;
            }
            else {
                $data = array('upload_data' => $this->upload->data());
                return $data ;
            }
        }

    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" callback _check_valid_extension ">
    function _check_valid_extension( $f ) {
        $validext = explode( '|', $this->allowed_type );
        $ext = end(explode( '.', $f ));
        if ( !in_array( $ext, $validext ) ) {
            return FALSE;
        }
        return TRUE;
    }
// </editor-fold>




}

