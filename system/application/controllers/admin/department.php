<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Department extends Controller {
    var $browse;
    function __construct() {
        parent::Controller();
        if (! $this->dx_auth->is_logged_in()) redirect('login');
        $this->dx_auth->check_uri_permissions();
        $this->load->helper( 'custom' );
        $this->load->library( 'form_validation' );
        $this->load->library( 'pagination' );
        $this->load->library( 'csvimport' );
        $this->load->model('dx_auth/users', 'users');
        $this->load->model('dx_auth/roles', 'roles');
        $this->paging = $this->paging->get_paging_template();
        $this->logged_userid = $this->dx_auth->get_user_id();
        $this->allowed_type = 'csv|txt';
        $this->csv_path = './uploads/csv/';
        $this->tdepartment = 'department';
        $this->sessd = array('impfile'   => '' ,'delim'     => '' , 'enc' => '' , 'step' => '' );
        // set the maximum memory limit
	ini_set('memory_limit', '150M');
    }

    function index() {
        $this->browse();
    }

// <editor-fold defaultstate="collapsed" desc=" form ">
    function form() {
        $this->form_validation->set_rules('department', 'group', 'required|trim|max_length[254]');
        $this->form_validation->set_rules('visibility', 'flag', 'required|trim|max_length[254]');
        $this->form_validation->set_error_delimiters('<p style="padding:2px" >', '</p>');
        $id = isset($_POST['deptid']) ? intval($_POST['deptid']) : $this->uri->segment(4);
        if ($id) {
            $this->mdl_department->where = array('deptid' => $id);
            $rs = $this->mdl_department->get_all_department(array('resulttype' => 'row_array'));
            $this->mdl_department->where = '';
            $data['results'] = $rs;
            $group = $this->input->post( 'department' );
            if ($group != $rs['groupname']) {
                $this->form_validation->set_rules('department', 'group', 'required|trim|callback_group_check');
            }
        }
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        if ( $this->form_validation->run() ) {
            $this->mdl_department->deptid = $id;
            $this->mdl_department->save();
            $this->session->set_flashdata( 'flash', 'Successfully saved group.' ) ;
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }else {
            $data['flash']['error'] = validation_errors();
            $this->template->write_view('content', 'default/department_form', $data);
        }
        $this->template->render();
    }
// </editor-fold>

    function browse() {
        $uri = $this->uri->uri_to_assoc(2);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        $total_rows = count($this->mdl_department->get_all_department());
        $this->paging['per_page'] = 20;
        $this->paging['uri_segment'] = 5;
        $this->paging['base_url'] = site_url('admin/department/browse/p');
        $this->paging['total_rows'] = $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['results'] = $this->mdl_department->get_all_department( array('rows' => $this->paging['per_page'], 'offset' => $uri['p'] ) );
        $this->template->write_view('content', 'default/department_browse', $data);
        $this->template->render();
    }

    function delete($id) {
        $id = intval($id);
        if ($this->dx_auth->is_role('member')) $this->dx_auth->deny_access('deny');
        if (!empty($id)) {
            $this->db->delete('department', array('deptid' => $id));
            $this->session->set_flashdata( 'flash', 'Successfully delete group.' ) ;
        }
        redirect('admin/department/browse');
    }

    function users($groupid = -1) {

        $this->load->helper('date');
        $this->dx_auth->check_uri_permissions();
        $rs = $this->mdl_department->get_department_by_id($groupid, array('resulttype' => 'row_array'));
        if ( count($rs) > 0) {
            $data['flash']['success'] = $this->session->flashdata( 'flash' );
            $uri = $this->uri->uri_to_assoc(2);
            $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
            $total_rows = count($this->mdl_users->get_all_users( array('deptid' => $groupid) ));
            $this->paging['per_page'] = 20;
            $this->paging['uri_segment'] = 5;
            $this->paging['base_url'] = site_url('admin/department/users/p');
            $this->paging['total_rows'] = $total_rows ;
            $this->pagination->initialize($this->paging);
            $data['pagination'] = $this->pagination->create_links();
            $data['results'] = $this->mdl_users->get_all_users( array('deptid' => $groupid, 'rows' => $this->paging['per_page'], 'offset' => $uri['p'] ) );
            $data['title'] = "All users in " . $rs['groupname'];
            $this->template->write_view('content', 'default/user_browse', $data);
            $this->template->render();
        }

        
    }

    function projects($gid) {
        $gid = !empty($gid) ? $gid : $this->input->post('gid');
        $data['flash']['success'] = $this->session->flashdata('flash');
        $this->mdl_department->where = array( 'deptid' => $gid);
        $rs = $this->mdl_department->get_all_department(array('resulttype' => 'row_array'));
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
        $total_rows = count($rs);
        $this->paging['per_page'] = 20;
        $this->paging['uri_segment'] = 6;
        $this->paging['base_url'] = site_url( "admin/department/projects/$gid/p/");
        $this->paging['total_rows'] = $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['domainaccess_title'] = "All projects of " .$rs['groupname'] ;
        $data['gid'] = !empty($gid) ? $gid : '';
        $data['results'] = $this->mdl_domains->get_all_groupprojects( $gid, array('rows' => $this->paging['per_page'], 'offset' => $uri['p'] ) );
        #$rs = $this->mdl_domains->get_all_groupprojects( $gid, array('rows' => '', 'offset' => '' ) );
        #$data['results2'] = $this->mdl_domains->to_array($rs, 'projectid');
        $this->template->write_view('content', 'default/department_browse_projects', $data);
        $this->template->render();
    }

    function add_to_project($gid) {
        $gid = !empty($gid) ? $gid : $this->input->post('gid');
        $data['flash']['success'] = $this->session->flashdata('flash');
        $this->mdl_department->where = array( 'deptid' => $gid);
        $rs = $this->mdl_department->get_all_department(array('resulttype' => 'row_array'));
        if ($_POST) {
            $this->session->set_flashdata( 'flash', 'Successfully saved.' ) ;
            $this->mdl_domains->save_group_projects();

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
        $this->paging['base_url'] = site_url( "admin/department/add_to_project/$gid/p/");
        $this->paging['total_rows'] = $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['domainaccess_title'] = "Groupname: " .$rs['groupname'] ;
        $data['gid'] = !empty($gid) ? $gid : '';
        $data['results'] = $this->mdl_projects->get_all_projects( array('rows' => $this->paging['per_page'], 'offset' => $uri['p'] ) );
        $rs = $this->mdl_domains->get_all_groupprojects( $gid, array('rows' => '', 'offset' => '' ) );
        $data['results2'] = $this->mdl_domains->to_array($rs, 'projectid');
        $this->template->write_view('content', 'default/addtoproject', $data);
        $this->template->render();

    }

    function add_to_domain($gid) {
        $gid = !empty($gid) ? $gid : $this->input->post('gid');
        $data['flash']['success'] = $this->session->flashdata('flash');
        $this->mdl_department->where = array( 'deptid' => $gid);
        $rs = $this->mdl_department->get_all_department(array('resulttype' => 'row_array'));
        if ($_POST) {
            $this->session->set_flashdata( 'flash', 'Successfully saved group access.' ) ;
            $this->mdl_domains->save_group_domains();
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
        $total_rows = count($this->mdl_domains->get_all_domains());
        $this->paging['per_page'] = 20;
        $this->paging['uri_segment'] = 6;
        $this->paging['base_url'] = site_url( "admin/department/add_to_domain/$gid/p/");
        $this->paging['total_rows'] = $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['domainaccess_title'] = "Groupname: " .$rs['groupname'] ;
        $data['gid'] = !empty($gid) ? $gid : '';
        $data['results'] = $this->mdl_domains->get_all_domains( array(
                'rows' => $this->paging['per_page'], 'offset' => $uri['p'],
                'field' => $this->browse['field'], 'sort' => $this->browse['sort'] )
        );
        $rs = $this->mdl_domains->get_all_groupdomains( $gid, array('rows' => '', 'offset' => '' ) );
        $data['results2'] = $this->mdl_domains->to_array($rs, 'domain_id');
        $this->template->write_view('content', 'default/addtodomain', $data);
        $this->template->render();

    }

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
            $uploadeddata = $this->do_upload( array( 'encrypt_name' => TRUE ) );
            if ( isset($uploadeddata['upload_data']) ) {
                $impfile = $this->csv_path . $uploadeddata['upload_data']['file_name'] ;
                $this->session->set_flashdata('impfile', $impfile);
                if ($format == 'csv') {
                    $totalrows = $this->importer->import_csv(array('tablename' => $this->tdepartment, 'primary_key' => 'deptid', 'filename' => $impfile ));
                }
                if ($format == 'xml') {
                    $totalrows = $this->importer->import_xml(array('tablename' => $this->tdepartment, 'primary_key' => 'deptid', 'filename' => $impfile ));
                }
                if ($totalrows > 0) {
                    $this->session->set_flashdata('flash', "Successfully imported data" ) ;
                }else{
                    $this->session->set_flashdata('flash', 'No data has been imported.' ) ;
                }
                redirect("admin/department/import/$format");
            } else {
                $data['flash']['defaulterror'] = $uploadeddata['upload_error'] ;
            }
        }
        // delete the imported csv file.
        $csvfile = $this->session->flashdata( 'impfile' );
        if (!empty($csvfile)) {
            $this->session->unset_userdata('impfile');
            @unlink($csvfile);
        }
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $this->template->write_view('content', 'default/department_import', $data );
        $this->template->render();
    }

    // import_step/csv/step/1
    // import_step/csv/step/2
    // import_step/csv/step/3
    function import_step() {
        $uri = $this->uri->uri_to_assoc(1);
        $type = strtolower($uri['import_step']);
        $type = !empty($type) ? $type : (!empty($_POST['import_step']) ? $_POST['import_step'] : '');
        $data = array();
        $step = !empty($uri['step']) ? $uri['step'] : ( !empty($_POST['step']) ? $_POST['step'] : '' );
        $field_list = array();
        #$query = $this->db->query('SELECT department.deptid, department.department as groupname, department.desc, department.visibility FROM department');
        #$field_list = $query->list_fields();
        $field_list = $this->db->list_fields($this->tdepartment);
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
                        redirect('admin/department/import_step/csv/step/2');
                    }
                    else {
                        $data['flash']['error'] = $uploadeddata['upload_error'] ;
                        $this->template->write_view('content', 'default/import/step1_department', $data );
                    }
                } else {
                    $this->session->unset_userdata($this->sessd);
                    $data['fields'] = $this->db->list_fields($this->tdepartment);
                    $this->template->write_view('content', 'default/import/step1_department', $data );
                }
            }

            // step2
            if ($step == 2) {
                $step = $this->session->userdata('step');
                if ($step !== 'step1') redirect('admin/department/import_step/csv/step/1');

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
                    if ( count($field_without_nulls) < 2 ) {
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
                            redirect('admin/department/import_step/csv/step/3');
                        }
                    }
                    if ( $error_found ) {
                        $this->template->write_view('content', 'default/import/step2_department', $data );
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
                    $data['prevurl'] = site_url("admin/department/import_step/csv/step/1");
                    $data['field_list'] = $field_list;
                    $this->template->write_view('content', 'default/import/step2_department', $data );
                }
            }
            // step3
            if ($step == 3) {
                $step = $this->session->userdata('step');
                if ($step !== 'step3') redirect('admin/department/import_step/csv/step/2');
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
                    $n = count($rows);
                    for($j=0; $j < $n; $j++) {
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
                            unset($tmpdata['deptid']);
                            if (in_array( $j, $selectedcsvrowstmp)) {
                                $this->db->insert($this->tdepartment, $tmpdata);
                            }
                    }
                    $this->session->set_flashdata('flash', 'Successfully imported data.');
                    $this->session->unset_userdata($this->sessd);
                    redirect("admin/department/import_step/csv/step/1");
                }
                else {
                    $this->csvimport->init($this->csv_path . $impfile, false, $delimiter, $enclosure );
                    $rows = $this->csvimport->get();
                    //pre($rows);
                    $n = count($rows);
                    for ($j=0; $j < $n; $j++) {
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
                    $this->template->write_view('content', 'default/import/step3_department', $data );
                }
            }


        }

        if ($type == 'xml') {

        }

        $this->template->render();
    }


    function export($format='csv') {
        $data = array();
        $format = isset($_POST['exportfmt']) ? $_POST['exportfmt'] : (!empty($format) ? $format : 'csv');
        $data['exportfmt'] = $format ;
        if ($_POST) {
            $this->load->library('exporter');
            // CSV
            if ($format == 'csv') {
                $filename = date('m-d-Y') . '-groups.csv';
                $this->exporter->export_csv( array('filename' => $filename, 'sql_query' => 'SELECT * FROM department' ));
            }
            // XML
            if ($format == 'xml') {
                $filename = date('m-d-Y') . '-groups.xml';
                $this->exporter->export_xml( array('filename' => $filename, 'sql_query' => 'SELECT * FROM department' ));
            }
        }
        $this->template->write_view('content', 'default/department_export', $data);
        $this->template->render();
    }

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

// <editor-fold defaultstate="collapsed" desc=" _check_valid_extensio ">
    function _check_valid_extension( $f ) {
        $validext = explode( '|', $this->allowed_type );
        $ext = end(explode( '.', $f ));
        if ( !in_array( $ext, $validext ) ) {
            return FALSE;
        }
        return TRUE;
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" group_check ">
    function group_check($group) {
        $res = $this->mdl_department->group_check($group);
        if ($res) {
            $this->form_validation->set_message('group_check', "Group name $group already exist. Please choose another.");
            return FALSE;
        }
        return TRUE;
    }
// </editor-fold>


}

