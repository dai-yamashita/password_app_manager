<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
cook by mandoy add me @facebook.com/artheman
*/
class Domain extends Controller {
    public $logged_userid, $browse ;

    function __construct() {
        parent::Controller();
        if (! $this->dx_auth->is_logged_in()) redirect('login');
        $this->dx_auth->check_uri_permissions();
        $this->load->helper( 'custom' );
        $this->load->library( 'form_validation' );
        $this->load->library( 'pagination' );
        $this->load->library( 'csvimport' );
        $this->paging = $this->paging->get_paging_template();
        $this->logged_userid = $this->dx_auth->get_user_id();
        $uri = $this->uri->uri_to_assoc(2);
        //print_r($uri);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        if( !empty($uri['field'])) {
            $this->browse['field']       = $uri['field'];
            $this->browse['sort']        = $uri['sort'];
            $this->browse['extra']       = "/field/{$this->browse['field']}/sort/{$this->browse['sort']}";
        }
        $this->sessdata = array('tid' => '', 'pid' => '' , 'uid' => '', 's' => '');
        $this->tdomains = 'domains';
        $this->tuser_domains = 'user_domains';
        $this->tdomain_customfields = 'domain_customfields';
        $this->fieldprefix = 'c_';
        $this->allowed_type = 'csv|txt';
        $this->csv_path = './uploads/csv/';
        $this->sessd = array('impfile'   => '' ,'delim'     => '' , 'enc' => '' , 'step' => '' );
        // set the maximum memory limit
  ini_set('memory_limit', '150M');
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" index ">
    function index() {
        $this->browse();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" form ">
    function form() {
        $this->load->view( 'validations/domain' );
        $this->form_validation->set_rules('url', 'Homepage url', 'trim');
        $this->form_validation->set_rules('loginurl', 'Login url', 'trim');
        $this->form_validation->set_error_delimiters('<p style="padding:2px" >', '</p>');

        $id = $this->uri->segment(4);
        $id = isset($_POST['domain_id']) ? intval($_POST['domain_id']) : (!empty($id) ? $id : -1 );
        if ($id) {
            if ( isset($_POST['submit_domain_customfield'])) $this->form_validation->set_rules('domain_customfield', 'custom fieldname', 'required');
            $this->mdl_domains->where = array('domains.domain_id' => $id);
            $rs = $this->mdl_domains->get_all_domains(array('resulttype' => 'row_array'));
            $data['results'] = $rs;
        }

        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $data['account_types'] = $this->mdl_account_type->get_account_types();
        $this->mdl_users->select = '';
        $this->mdl_users->where = array('domain_id' => $id);
        $this->mdl_users->select = '';
        $data['user_domains'] = $this->mdl_users->get_all_userdomains();
        $data['userprojects'] = $this->mdl_projects->get_user_projects($id);
        $this->mdl_projects->order_by = 'project ASC';
        $data['allprojects'] = $this->mdl_projects->get_all_projects();
        $this->mdl_projects->order_by = '';
        $data['alllogintemplates'] = $this->mdl_logintemplates->get_all_logintemplates();
        $data['customfields'] = $this->mdl_customfields->list_customfields('domains');
        $data['domain_customfields'] = $this->mdl_domain_customfields->get_all_domain_customfields($id);
        #print_r($_POST);
        if ( $this->form_validation->run() ) {
            if ($this->input->post('submit_domain_customfield') ) {
                $this->mdl_domain_customfields->save();
                $this->session->set_flashdata( 'flash', 'Successfully saved domain.' ) ;
                header('location:' . $_SERVER['HTTP_REFERER'] );
            } else {
                $this->mdl_domains->domain_id = $id;
                $this->mdl_domains->save();
                $tmpid = $this->mdl_domains->get_domainid();
                $this->mdl_domain_customfields->update(array('tablename' => $this->tdomain_customfields, 'user_id' => $this->logged_userid ));
                $this->session->set_flashdata( 'flash', 'Successfully saved domain.' ) ;
                header('location:' . $_SERVER['HTTP_REFERER'] );
            }

        }else {
            $data['flash']['error'] = validation_errors();
            $this->template->write_view('content', 'default/domain_form', $data);
        }
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" browse ">
    function browse() {
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $this->paging['num_links']      = 5;
        $this->paging['per_page']       = 20;
        $this->paging['uri_segment']    = 5;
        $this->paging['base_url']       = site_url('admin/domain/browse/p/')  ;
        $uri = $this->uri->uri_to_assoc(2);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        $tmpsortby = ($this->browse['sort']) == 'desc' ? 'asc' : 'desc';
        $data['sortby'] = $tmpsortby ;

        if($this->dx_auth->is_role('member')) {
            $total_rows = count($this->mdl_domains->get_all_userdomains($this->logged_userid));
            $this->paging['total_rows']     = $total_rows ;
            $this->pagination->initialize($this->paging);
            $this->pagination->extra_query_strings = $this->browse['extra'];
            $data['pagination'] = $this->pagination->create_links();
            $data['results'] = $this->mdl_domains->get_all_userdomains($this->logged_userid, array(
                'rows' => $this->paging['per_page'], 'offset' => $uri['p'],
                'field' => $this->browse['field'], 'sort' => $this->browse['sort']
                ));
            $this->template->write_view('content', 'default/domain_browse_user', $data);
        } else {
            $total_rows = count($this->mdl_domains->get_all_domains());
            $this->paging['total_rows'] = $total_rows ;
            $this->pagination->initialize($this->paging);
            $this->pagination->extra_query_strings = $this->browse['extra'];
            $data['pagination'] = $this->pagination->create_links();
            $data['results'] = $this->mdl_domains->get_all_domains( array(
                    'rows' => $this->paging['per_page'], 'offset' => $uri['p'],
                    'field' => $this->browse['field'], 'sort' => $this->browse['sort'] )
            );
            $this->template->write_view('content', 'default/domain_browse', $data);
        }


        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" delete ">
    function delete($id) {
        $id = intval($id);
        if ($this->dx_auth->is_role('member')) $this->dx_auth->deny_access('deny');
        if (!empty($id)) {
            $this->db->delete('user_domains', array('domain_id' => $id));
            $this->db->delete('group_domains', array('domain_id' => $id));
            $this->db->delete('domains', array('domain_id' => $id));

        }
        $this->session->set_flashdata( 'flash', 'Successfully delete domain.' ) ;
        redirect('admin/domain/browse');
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" view ">
    function view() {
        $id = isset($_POST['domain_id']) ? intval($_POST['domain_id']) : $this->uri->segment(4);
        $data['customfields'] = $this->mdl_customfields->list_customfields('domains');
        $data['backurl'] = site_url('admin/domain/browse');
        if ($id) {
            if ($this->dx_auth->is_role('member')) {
                #$this->mdl_domains->where = array('user_domains.domain_id' => $id);
                $rs = $this->mdl_domains->get_all_userdomains( $this->logged_userid, array('resulttype' => 'row_array', 'domain_id' => $id ));
                if ($rs) {
                    $data['results'] = $rs;
                }else {
                    $this->dx_auth->deny_access('deny');
                }
            }elseif($this->dx_auth->is_role(array('owner','manager','administrator' ))) {
                $rs = $this->mdl_domains->get_domain_by_id($id, array('resulttype' => 'row_array'));
                if ($rs) {
                    $data['results'] = $rs;
                    $data['userprojects'] = $this->mdl_domains->get_user_projects($id);
                    $data['allusers'] = $this->mdl_users->get_all_users();
                }
            }
        }
        $this->template->write_view('content', 'default/domain_view', $data);
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="import" >
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
            if ( isset($uploadeddata['upload_data'])) {
                $impfile = $this->csv_path . $uploadeddata['upload_data']['file_name'] ;
                $this->session->set_flashdata('impfile', $impfile);
                if ($format == 'csv') {
                    $totalrows = $this->importer->import_csv(array('tablename' => $this->tdomains, 'primary_key' => 'domain_id', 'filename' => $impfile ));
                }
                if ($format == 'xml') {
                    $totalrows = $this->importer->import_xml(array('tablename' => $this->tdomains, 'primary_key' => 'domain_id'));
                }
                if ($totalrows > 0) {
                    $this->session->set_flashdata('flash', "Successfully imported data" ) ;
                }else {
                    $this->session->set_flashdata('flash', 'No data has been imported.' ) ;
                }
                redirect("admin/domain/import/$format");

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
        $this->template->write_view('content', 'default/domain_import', $data );
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
            /*$sql = "SELECT
                domains.domain_id as domain_id, domains.project_id, domains.type, domains.templateid,
                domains.customtemplate, domains.importance, domains.url,
                domains.loginurl, domains.username, domains.password,
                domains.changefreq, domains.mark, domains.notes
                FROM domains"; */
            $sql = "SELECT * FROM domains";
            // CSV
            if ($format == 'csv') {
                $filename = date('m-d-Y') . '-domainaccess.csv';
                $this->exporter->export_csv( array('filename' => $filename, 'sql_query' => $sql));
            }
            // XML
            if ($format == 'xml') {
                $filename = date('m-d-Y') . '-domainaccess.xml';
                $this->exporter->export_xml( array('filename' => $filename, 'sql_query' => $sql));
            }
        }
        $this->template->write_view('content', 'default/domain_export', $data);
        $this->template->render();
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" assign_user_project ">
    function assign_user_project() {
        $this->form_validation->set_rules('user_domains[]', 'domain access ', 'trim');
        $id = isset($_POST['domain_id']) ? intval($_POST['domain_id']) : $this->uri->segment(4);
        $rs = $this->mdl_domains->get_domain_by_id($id);
        #print_r($_POST);
        #pre($rs);
        if($rs == FALSE) {
            $data['flash']['error'] = 'Project doesnot exist.';
        }
        else {
            $data['flash']['success'] = $this->session->flashdata( 'flash' );
            if ( $this->form_validation->run() ) {
                $this->mdl_domains->domain_id = $id;
                $this->mdl_domains->save_user_domains();
                $this->session->set_flashdata( 'flash', 'Successfully saved domain access.' ) ;
                header('location:' . $_SERVER['HTTP_REFERER'] );
            }
        }

        //$this->mdl_users->select = 'id, firstname, lastname';
        $this->mdl_users->order_by = 'firstname ASC';
        $data['allusers'] = $this->mdl_users->get_all_users();
        $this->mdl_users->order_by = '';
        $data['allprojects'] = $this->mdl_domains->get_all_domains();
        $data['results'] = $rs[0] ;
        $data['userprojects'] = $this->mdl_domains->get_user_projects($id);
        $this->template->write_view('content', 'default/project_assign_to_user', $data);
        $this->template->render();

    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="is_domain_exist ">
    function is_domain_exist($id) {
        $rs = $this->mdl_domains->get_domain_by_id($id);
        if (FALSE == $rs) {
            return FALSE;
        }
        return $rs;
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" request_access ">
    function request_access() {
        $this->form_validation->set_rules('domainname', 'project name', 'required|trim|max_length[254]');
        $domainname = $this->input->post('domainname');

        $data['allprojects'] = $this->mdl_projects->get_all_projects();
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        if ( $this->form_validation->run() ) {
            $this->session->set_flashdata( 'flash', 'Successfully send request.' ) ;
            $this->_send_notification( array('projectid' => $domainname, 'userid' => $this->logged_userid));
            #$this->_send_mail_password_request( array('projectid' => $domainname, 'userid' => $this->logged_userid) );
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }else {
            $data['flash']['error'] = validation_errors();
            $this->template->write_view('content', 'default/request_access', $data);
        }
        /*$data['flash']['success'] = $this->session->flashdata( 'flash' );
        if ( $this->form_validation->run() ) {
                $this->mdl_projects->projectid = $id;
                $this->mdl_projects->save();
                $this->session->set_flashdata( 'flash', 'Successfully saved project.' ) ;
                header('location:' . $_SERVER['HTTP_REFERER'] );
        }else{
                $data['flash']['error'] = validation_errors();
                $this->template->write_view('content', 'default/project_form', $data);
        }*/
        $this->template->render();

    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" sendrequest_access ">
    function sendrequest_access() {
        $timezone = $this->sitesettings->get_settings('timezone');
        $isdaylightsaving = $this->sitesettings->get_settings('isdaylightsaving');
        $currenttime = gmt_to_local(time(), $timezone, $isdaylightsaving );
        $id = $_POST['req_project'];
        $rs1 = $this->db->get_where('alerts', array('from' => $this->logged_userid, 'projectid' => $id, 'isread' => 0));
        if ($rs1->num_rows() > 0) {
            $arr = array ('flashmessage' => "Request error: you have already sent a request. Kindly contact the admin for approval.",
                'result' => 'error' );
            echo json_encode($arr);
        } else {
            $rs = $this->mdl_users->get_user_by_id($this->logged_userid);
            $fullname = $rs['firstname'] . ' ' . $rs['lastname'];
            $rs = $this->mdl_projects->get_project_by_id( $id, array( 'resulttype' => 'row_array'));
            $project = $rs['project'];
            $msg =
    "$fullname would like to request a password for the project: $project
    <br />
    Thanks<br />
    Trimorp Team
    ";
            $data = array(
                    'title'         => "New password request by $fullname" ,
                    'alert'         => $msg,
                    'created'       => $currenttime,
                    'isread'        => 0,
                    'from'          => $this->logged_userid,
                    'to'            => -1,
                    'projectid'     => $id,
            );
            $this->db->insert('alerts', $data) ;
            $arr = array ('flashmessage' => "Successfully send request.",
                'result' => 'success' );
            echo json_encode($arr);

        }

    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" import_step ">

    // import_step/csv/step/1
    // import_step/csv/step/2
    // import_step/csv/step/3
    function import_step() {
        if ($this->dx_auth->is_role(array('member'))) $this->dx_auth->deny_access('deny');
        $uri = $this->uri->uri_to_assoc(1);
        $type = strtolower($uri['import_step']);
        $type = !empty($type) ? $type : (!empty($_POST['import_step']) ? $_POST['import_step'] : '');
        $data = array();
        $step = !empty($uri['step']) ? $uri['step'] : ( !empty($_POST['step']) ? $_POST['step'] : '' );
        $field_list = array();
        $field_list = $this->db->list_fields($this->tdomains);
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
                        redirect('admin/domain/import_step/csv/step/2');
                    }
                    else {
                        $data['flash']['error'] = $uploadeddata['upload_error'] ;
                        $this->template->write_view('content', 'default/import/step1_domain', $data );
                    }
                } else {
                    $this->session->unset_userdata($this->sessd);
                    $data['fields'] = $this->db->list_fields($this->tdomains);
                    $this->template->write_view('content', 'default/import/step1_domain', $data );
                }
            }

            // step2
            if ($step == 2) {
                $step = $this->session->userdata('step');
                if ($step !== 'step1') redirect('admin/domain/import_step/csv/step/1');

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
                                    if ( ! isset( $tmp[$f] )  ) {
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
                            redirect('admin/domain/import_step/csv/step/3');
                        }
                    }
                    if ( $error_found ) {
                        $this->template->write_view('content', 'default/import/step2_domain', $data );
                    }
                }
                else {
                    $this->csvimport->init($this->csv_path . $impfile, '', $delimiter, $enclosure );
                    $rows = $this->csvimport->get( );
                    #pre($rows );
                    $data['csv_total_fields'] = @count($rows[0]);
                    $use_header = 1;
                    $data['csv_sample_data'] = $use_header ? @array_values($rows[0]) : @array_values($rows[rand(0, count($rows)-1)]);
                    #pre($data['csv_sample_data']);
                    $data['prevurl'] = site_url("admin/domain/import_step/csv/step/1");
                    $data['field_list'] = $field_list;
                    $this->template->write_view('content', 'default/import/step2_domain', $data );
                }
            }
            // step3
            if ($step == 3) {
                $step = $this->session->userdata('step');
                if ($step !== 'step3') redirect('admin/domain/import_step/csv/step/2');
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
                            $tmpdata['created'] = $currenttime;
                            #echo 'xx=';
                            #pre($tmpdata);
                            unset($tmpdata['domain_id']);
                            if (in_array( $j, $selectedcsvrowstmp)) {
                                #pre($tmpdata);
                                $this->db->insert($this->tdomains, $tmpdata);
                                #$insertid = $this->db->insert_id();
                                #$this->db->insert($this->tdomains, array('user_id' => $this->logged_userid, 'domain_id' => $insertid));
                            }
                    }
                    $this->session->set_flashdata('flash', 'Successfully imported data.');
                    $this->session->unset_userdata($this->sessd);
                    redirect("admin/domain/import_step/csv/step/1");
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
                    $this->template->write_view('content', 'default/import/step3_domain', $data );
                }
            }


        }

        if ($type == 'xml') {

        }

        $this->template->render();
    }
// </editor-fold>


// <editor-fold defaultstate="collapsed" desc=" _send_mail_password_request ">
    function _send_mail_password_request($params = array()) {
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';

        $rs = $this->mdl_users->get_user_by_id($params['userid']);
        $fullname = $rs['firstname'] . ' ' . $rs['lastname'];
        $firstname = $rs['firstname'];
        $email = $rs['email'];
        #print_r($rs);
        $admin_email = $this->sitesettings->get_settings('admin_email');
        $rs = $this->mdl_projects->get_project_by_id( $params['projectid'] );
        $project = $rs['project'];
        $this->email->initialize($config);
        $this->load->library('email');
        $this->email->from($email, $firstname);
        $this->email->to($admin_email);
        $this->email->subject( "New password request by $firstname" );
        $msg =
                "$fullname would like to request a password for the project: $project

<br />Thanks
                ";
        $this->email->message($msg);
        $this->email->send();
        #echo $this->email->print_debugger();

    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" _send_notification ">
    function _send_notification($params = array()) {
        $rs = $this->mdl_users->get_user_by_id($params['userid']);
        $fullname = $rs['firstname'] . ' ' . $rs['lastname'];
        $firstname = $rs['firstname'];
        $rs = $this->mdl_projects->get_project_by_id( $params['projectid'], array( 'resulttype' => 'row_array'));
        $project = $rs['project'];
        $msg =
                "$fullname would like to request a password for the project: $project
<br />Thanks";
        $data = array(
                'title'         => "New password request by $firstname" ,
                'alert'         => $msg,
                'created'       => time(),
                'isread'        => 0,
                'from'          => $params['userid'],
                'to'            => -1
        );
        $this->db->insert('alerts', $data) ;

    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" isvalid_url_check ">
    function isvalid_url_check($url) {
        if (!empty($url)) {
            if (filter_var($url, FILTER_VALIDATE_URL) == FALSE) {
                $this->form_validation->set_message('isvalid_url_check', '%s is invalid.');
                return FALSE;
            }
            return TRUE;
        }
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" search ">

    function search() {
        $this->session->unset_userdata($this->sessdata);
        $data['results'] = array();
        $data['projectid'] = '';
        $data['userid'] = '';
        $data['type_id'] = '';
        $userid         = $this->input->post('userid') ? $this->input->post('userid') : '';
        $projectid      = $this->input->post('projectid') ? $this->input->post('projectid') : '' ;
        $type_id        = $this->input->post('type_id') ? $this->input->post('type_id') : '';
        if ($_POST) {
            if($projectid != -1 && $type_id != -1 ) {
                $this->mdl_domains->where = array('projects.projectid' => $projectid, 'account_type.type_id' => $type_id );
            } elseif($type_id != -1) {
                $this->mdl_domains->where = array('account_type.type_id' => $type_id);
            } elseif($projectid != -1) {
                $this->mdl_domains->where = array('projects.projectid' => $projectid);
            }
            $this->session->set_userdata(array('tid' => $type_id, 'pid' => $projectid, 'uid' => $userid, 's' => 'go' ));
            redirect('admin/domain/searchresult');
        }

        $data['allusers'] = $this->mdl_users->get_all_users();
        $this->mdl_projects->order_by = 'project ASC';
        $data['allprojects'] = $this->mdl_projects->get_all_projects();
        $this->mdl_projects->order_by = '';
        $data['allaccounttypes'] = $this->mdl_account_type->get_all_accounttypes( array('rows' => NULL));
        $this->template->write_view('content', 'default/domain_search', $data);
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" searchresult ">
    function searchresult() {
        $this->load->library( 'pagination' );
        $data['results'] = array();

        $s              = $this->session->userdata('s');
        if (empty($s)) redirect('admin/domain/search');
        $userid         = $this->session->userdata('uid');
        $projectid      = $this->session->userdata('pid');
        $type_id        = $this->session->userdata('tid');
        $userid         = $this->input->post('userid') ? $this->input->post('userid') : $userid;
        $projectid      = $this->input->post('projectid') ? $this->input->post('projectid') : $projectid ;
        $type_id        = $this->input->post('type_id') ? $this->input->post('type_id') : $type_id;

        $uri = $this->uri->uri_to_assoc(2);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;

        $this->paging['per_page']	= 20;
        $this->paging['uri_segment'] 	= 5;
        $this->paging['base_url'] 	= site_url('admin/domain/searchresult/p');
        $rs1 = array();
        $rs2 = array();

        if($projectid != -1 && $type_id != -1 ) {
            $this->mdl_domains->where = array('projects.projectid' => $projectid, 'account_type.type_id' => $type_id );
        } elseif($type_id != -1) {
            $this->mdl_domains->where = array('account_type.type_id' => $type_id);
        } elseif($projectid != -1) {
            $this->mdl_domains->where = array('projects.projectid' => $projectid);
        }
        $rs1 = $this->mdl_domains->get_all_userdomains($userid);
        $rs2 = $this->mdl_domains->get_all_userdomains($userid, array('rows' => $this->paging['per_page'], 'offset' => $uri['p']));

        $this->paging['total_rows'] = count($rs1) ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['results'] = $rs2;
        $data['projectid'] = $projectid;
        $data['userid'] = $userid;
        $data['type_id'] = $type_id;


        $data['allusers'] = $this->mdl_users->get_all_users();
        $data['allprojects'] = $this->mdl_projects->get_all_projects();
        $data['allaccounttypes'] = $this->mdl_account_type->get_all_accounttypes( array('rows' => NULL));
        $this->template->write_view('content', 'default/domain_search', $data);
        $this->template->render();
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" customfield ">
    function customfield() {
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        #echo '<pre>'        ;
        #print_r($results);
        $data['results'] = $this->mdl_customfields->list_customfields($this->tdomains);
        $this->form_validation->set_rules('fieldname', 'fieldname', 'required|trim|max_length[254]|callback_fieldname_check');
        if ( $this->form_validation->run() ) {
            $this->mdl_customfields->add_field(array(
                    'fieldname' => $this->input->post('fieldname'),
                    'tablename' => $this->tdomains,
                    'fieldtype' => 'text'));

            $this->session->set_flashdata( 'flash', 'Successfully created a field.' ) ;
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }else {
            $data['flash']['error'] = validation_errors();
            $this->template->write_view('content', 'default/customfield', $data);
        }
        $this->template->render();

    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" dologin ">
    function dologin($id) {
        $rs = $this->mdl_domains->get_domain_by_id($id, array('resulttype' => 'row_array'));
        if ($rs) {
            $type = strtolower($rs['acctype']);
            $templateid = $rs['templateid'];
            switch($type) {
                case 'ftp':
                    $this->load->library('cpanel');
                    $params = array('user' => $rs['username'], 'pass' => $rs['password'], 'host' => $rs['url'] );
                    $rs = $this->cpanel->init('filemanager/index.html', $params, array(), FALSE );
                    $this->cpanel->execute_page('filemanager/index.html');
                    break;

                case 'cms':
                    $tpl = $this->mdl_logintemplates->get_template_by_id($templateid);
                    if ( isset($tpl['name']) && $tpl['name'] == 'wordpress') {
                        $data = array( '{username}' => $rs['username'], '{password}' => $rs['password'], '{loginurl}' => $rs['loginurl'], '{redirect_to}' => './wp-admin'  );
                        $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                        $this->load->view('logintemplates/templateholder', $v);
                    }elseif(isset($tpl['name']) && $tpl['name'] == 'drupal') {
                        $data = array( '{username}' => $rs['username'], '{password}' => $rs['password'], '{loginurl}' => $rs['loginurl'] );
                        $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                        $this->load->view('logintemplates/templateholder', $v);
                    }else {
                        $tpl = $this->mdl_domains->get_domain_customtemplate($id);
                        $tpl['template'] = $tpl['customtemplate'];
                        if (! empty($tpl['template'])) {
                            $tags = $this->mdl_domain_customfields->get_domain_customfields($id);
                            $customfield = array();
                            foreach($tags as $k => $v) {
                                $customfield['{'.$v['customfield'].'}'] = $v['customfield'];
                                // $customfield['{'.$v['customfield'].'_value}'] = $v['value'];
                            }
                            $data = array( '{username}' => $rs['username'], '{password}' => $rs['password'],  '{username_value}' => $rs['username'], '{password_value}' => $rs['password'], '{loginurl}' => $rs['loginurl'] );
                            $data = array_merge($customfield, $data);
                            $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                            // TODO: convert ang html element submit into hidden textfield.
                            $v['template'] = str_replace('type="submit"', 'type="hidden"', $v['template']);
                            $this->load->view('logintemplates/templateholder', $v);
                        }
                        else {
                            $this->session->set_flashdata( 'flash', 'Template not found.' ) ;
                            header('location:' . $_SERVER['HTTP_REFERER'] );
                        }
                    }
                    break;

                case 'cpanel' :
//                    $this->load->library('cpanel');
//                    $params = array('user' => $rs['username'], 'pass' => $rs['password'], 'host' => $rs['url'] );
//                    $this->cpanel->init('index.html', $params, array(), FALSE );
//                    $this->cpanel->execute_page('index.html', $params);
                    $tpl = $this->mdl_logintemplates->get_template_by_id($templateid);
                    if ($tpl) {
                        $data = array( '{username}' => $rs['username'], '{password}' => $rs['password'], '{loginurl}' => $rs['loginurl'] );
                        $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                        $this->load->view('logintemplates/templateholder', $v);
                    }
                    break;

                default:
                    $tpl = $this->mdl_logintemplates->get_template_by_id($templateid);
                    if ( isset($tpl['name']) && $tpl['name'] == 'wordpress') {
                        $data = array( '{username}' => $rs['username'], '{password}' => $rs['password'], '{loginurl}' => $rs['loginurl'], '{redirect_to}' => './wp-admin'  );
                        $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                        $this->load->view('logintemplates/templateholder', $v);
                    }elseif(isset($tpl['name']) && $tpl['name'] == 'drupal') {
                        $data = array( '{name}' => $rs['username'], '{pass}' => $rs['password'], '{loginurl}' => $rs['loginurl'] );
                        $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                        $this->load->view('logintemplates/templateholder', $v);
                    }else {
                        $tpl = $this->mdl_logintemplates->get_template_by_id($templateid);
                        if (!empty($tpl['name'])) {
                            $data = array( '{username}' => $rs['username'], '{password}' => $rs['password'], '{loginurl}' => $rs['loginurl']);
                            $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                            $v['template'] = str_replace('type="submit"', 'type="hidden"', $v['template']);
                            $this->load->view('logintemplates/templateholder', $v);
                        } else {
                            $tpl = $this->mdl_domains->get_domain_customtemplate($id);
                            $tpl['template'] = $tpl['customtemplate'];
                            if (! empty($tpl['template'])) {
                                $tags = $this->mdl_domain_customfields->get_domain_customfields($id);
                                $customfield = array();
                                foreach($tags as $k => $v) {
                                    $customfield['{'.$v['customfield'].'}'] = $v['customfield'];
                                }
                                $data = array( '{username}' => 'username', '{password}' => 'password',  '{username_value}' => $rs['username'], '{password_value}' => $rs['password'], '{loginurl}' => $rs['loginurl'] );
                                $data = array_merge($customfield, $data);
                                $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                                $this->load->view('logintemplates/templateholder', $v);
                            }
                            else {
                                $this->session->set_flashdata( 'flash', 'templatenotfound' ) ;
                                header('location:' . $_SERVER['HTTP_REFERER'] );
                            }
                        }
                    }
                    break;
            }
        }
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" deletefield ">
    function deletefield() {
        $uri = $this->uri->uri_to_assoc(1);
        $f = $uri['deletefield'] ;
        $this->mdl_customfields->delete_field( array('fieldname' => $f, 'tablename' => 'domains' ) );
        $this->session->set_flashdata( 'flash', 'Successfully deleted a field.' ) ;
        header('location:' . $_SERVER['HTTP_REFERER'] );
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" delete_customfield ">
    function delete_customfield($id) {
        if (!empty($id)){
            $this->db->where('customfieldid', $id);
            $this->db->delete('domain_customfields');
            $this->session->set_flashdata( 'flash', 'Successfully deleted a field.' ) ;
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" fieldname_check ">
    function fieldname_check() {
        $fieldname = $this->input->post('fieldname');
        if ($this->db->field_exists( $this->fieldprefix . $fieldname, $this->tdomains)) {
            $this->form_validation->set_message('fieldname_check',  sprintf('Field %s already exist in table %s. Please choose another one.', $fieldname, $this->tdomains));
            return FALSE;
        }
        return TRUE;
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

// <editor-fold defaultstate="collapsed" desc=" _check_valid_extension ">
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
