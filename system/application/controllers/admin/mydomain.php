<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
cook by mandoy add me @facebook.com/artheman
*/
class Mydomain extends Controller {
    public $logged_userid, $browse;

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
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        if( !empty($uri['field'])) {
            $this->browse['field']       = $uri['field'];
            $this->browse['sort']        = $uri['sort'];
            $this->browse['extra']       = "/field/{$this->browse['field']}/sort/{$this->browse['sort']}";
        }
        $this->sessdata = array('tid' => '', 'pid' => '' , 'uid' => '', 's' => '');
        $this->tpersonal_domains = 'personal_domains';
        $this->tuser_personal_domains = 'user_personal_domains';
        $this->tpersonaldomain_customfields = 'personaldomain_customfields';
        $this->fieldprefix = 'c_';
        $this->allowed_type = 'csv|txt';
        $this->csv_path = './uploads/csv/';
        $this->sessd = array('impfile'   => '' ,'delim'     => '' , 'enc' => '' , 'step' => '' );
        // set the maximum memory limit
	     ini_set('memory_limit', '150M');
    }

    function index() {
        $this->browse();
    }

    function form() {
        $this->load->view( 'validations/domain' );
        $this->form_validation->set_rules('url', 'Homepage url', 'trim');
        $this->form_validation->set_rules('loginurl', 'Login url', 'trim');
        $this->form_validation->set_error_delimiters('<p style="padding:2px" >', '</p>');
        $id = $this->uri->segment(4);
        $id = isset($_POST['domain_id']) ? intval($_POST['domain_id']) : (!empty($id) ? $id : null );
        $data = array();
        if ($id) {
            if ( isset($_POST['submit_domain_customfield'])) $this->form_validation->set_rules('domain_customfield', 'custom fieldname', 'required');
            $rs = $this->mdl_domains->get_all_personaldomains($this->logged_userid, array('resulttype' => 'row_array', 'domain_id' => $id));
            $data['results'] = $rs;
        }
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $data['account_types'] = $this->mdl_account_type->get_account_types();
        #$this->mdl_users->select = '';
        #$this->mdl_users->where = array('domain_id' => $id);
        $data['userprojects'] = $this->mdl_projects->get_user_projects($id);
        $data['allprojects'] = $this->mdl_projects->get_all_projects();
        $data['alllogintemplates'] = $this->mdl_logintemplates->get_all_logintemplates();
        $data['customfields'] = $this->mdl_customfields->list_customfields($this->tpersonal_domains);
        $data['domain_customfields'] = $this->mdl_domain_customfields->get_all_personaldomain_customfields($id);
        if ( $this->form_validation->run() ) {
            if ($this->input->post('submit_domain_customfield') ) {
                $this->mdl_domain_customfields->save(array('tablename' => $this->tpersonaldomain_customfields ));
                $this->session->set_flashdata( 'flash', 'Successfully saved domain.' ) ;
                header('location:' . $_SERVER['HTTP_REFERER'] );
            } else {
                if ($id) $this->mdl_domains->domain_id = $id;
                $this->mdl_domains->save(array('tablename' => $this->tpersonal_domains));
                $tmpid = $this->mdl_domains->get_domainid();
                $this->mdl_domains->save_personaldomain( array('user_id' => $this->logged_userid, 'domain_id' => $tmpid ) );
                $this->mdl_domain_customfields->update(array('tablename' => $this->tpersonaldomain_customfields, 'user_id' => $this->logged_userid ));
                $this->session->set_flashdata( 'flash', 'Successfully saved domain.' ) ;
                header('location:' . $_SERVER['HTTP_REFERER']);
            }

        }else {
            $data['flash']['error'] = validation_errors();
            $this->template->write_view('content', 'default/mydomain_form', $data);
        }
        $this->template->render();
    }

    function browse() {
        $s = 'Template not found. It might be the login template is wrong or does not match in the domain type.';
        $flash = $this->session->flashdata( 'flash' );
        if ($flash == 'templatenotfound') {
            $data['flash']['defaulterror'] = $s;
        } else {
            $data['flash']['success'] = $flash;
        }
        $this->paging['num_links']      = 5;
        $this->paging['per_page']       = 20;
        $this->paging['uri_segment']    = 5;
        $this->paging['base_url']       = site_url('admin/mydomain/browse/p/')  ;
        $uri = $this->uri->uri_to_assoc(2);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        $tmpsortby = ($this->browse['sort']) == 'desc' ? 'asc' : 'desc';
        $data['sortby'] = $tmpsortby ;
        $total_rows = count($this->mdl_domains->get_all_personaldomains($this->logged_userid));
        $this->paging['total_rows']     = $total_rows ;
        $this->pagination->initialize($this->paging);
        $this->pagination->extra_query_strings = $this->browse['extra'];
        $data['pagination'] = $this->pagination->create_links();
        $data['results'] = $this->mdl_domains->get_all_personaldomains($this->logged_userid, array(
                'rows'  => $this->paging['per_page'], 'offset' => $uri['p'],
                'field' => $this->browse['field'], 'sort' => $this->browse['sort']
            ) );
        $this->template->write_view('content', 'default/mydomain_browse', $data);
        $this->template->render();
    }

    function delete($id) {
        $id = intval($id);
        if (!empty($id)) {
            $rs = $this->db->get_where($this->tuser_personal_domains, array('domain_id' => $id, 'user_id' => $this->logged_userid));
            if ($rs->num_rows() > 0) {
                // delete ang personal domain access
                $this->db->delete($this->tpersonal_domains, array('domain_id' => $id));
                // delete ang iyang related table
                $this->db->delete($this->tuser_personal_domains, array('domain_id' => $id, 'user_id' => $this->logged_userid));
                // delete ang iyang custom fields
                $this->db->delete($this->tpersonaldomain_customfields, array('domain_id' => $id, 'user_id' => $this->logged_userid));
            }

        }
        $this->session->set_flashdata( 'flash', 'Successfully delete domain.' ) ;
        redirect('admin/mydomain/browse');
    }

    function view() {
        $id = isset($_POST['domain_id']) ? intval($_POST['domain_id']) : $this->uri->segment(4);
        $data['customfields'] = $this->mdl_customfields->list_customfields($this->tpersonal_domains);
		    $data['backurl'] = site_url('admin/mydomain/browse');
        if ($id) {
            $rs = $this->mdl_domains->get_all_personaldomains( $this->logged_userid, array('resulttype' => 'row_array', 'domain_id' => $id ));
            if ($rs) {
                $data['results'] = $rs;
            }else {
                $this->dx_auth->deny_access('deny');
            }
        }
        $this->template->write_view('content', 'default/domain_view', $data);
        $this->template->render();
    }

    function import($format='csv') {
        $this->load->library('importer');
        $this->load->dbutil();
        $data = array();
        $totalrows = 0;
        $format = isset($_POST['importfmt']) ? $_POST['importfmt'] : (!empty($format) ? $format : 'csv');
        $data['importfmt'] = $format ;
        if ($_FILES) {
            if ($format == 'csv') $this->allowed_type = 'csv|txt';
            if ($format == 'xml') $this->allowed_type = 'xml';
            $uploadeddata = $this->do_upload( array( 'encrypt_name' => TRUE ) );
            if ( isset($uploadeddata['upload_data'])) {
                $impfile = $this->csv_path . $uploadeddata['upload_data']['file_name'] ;
                $this->session->set_flashdata('impfile', $impfile);
                if ($format == 'csv') {
                    $totalrows = $this->importer->import_csv(array('tablename' => $this->tpersonal_domains, 'primary_key' => 'domain_id', 'filename' => $impfile ));
                }
                if ($format == 'xml') {
                    $totalrows = $this->importer->import_xml(array('tablename' => $this->tpersonal_domains, 'primary_key' => 'domain_id'));
                }
                if ($totalrows > 0) {
                    $this->session->set_flashdata('flash', "Successfully imported data" ) ;
                }else {
                    $this->session->set_flashdata('flash', 'No data has been imported.' ) ;
                }
                redirect("admin/mydomain/import/$format");

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
        $this->template->write_view('content', 'default/mydomain_import', $data );
        $this->template->render();
    }

    function doimport() {
        $this->import_step();
    }

    // import_step/csv/step/1
    // import_step/csv/step/2
    // import_step/csv/step/3
    function import_step() {
        $uri = $this->uri->uri_to_assoc(1);
        #pre($uri);
        $tablename = $this->tpersonal_domains;
        $type = strtolower($uri['import_step']);
        $type = !empty($type) ? $type : (!empty($_POST['import_step']) ? $_POST['import_step'] : '');
        #echo 'i='.$type;
        $data = array();
        $step = !empty($uri['step']) ? $uri['step'] : ( !empty($_POST['step']) ? $_POST['step'] : '' );
        #echo 'x='.$step;
        #$type = !empty($uri['step']) ? $uri['step'] : ( !empty($_POST['step']) ? $_POST['step'] : '' );
        $field_list = array();
        $field_list = $this->db->list_fields($this->tpersonal_domains);
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
                        redirect('admin/mydomain/import_step/csv/step/2');
                    }
                    else {
                        $data['flash']['error'] = $uploadeddata['upload_error'] ;
                        $this->template->write_view('content', 'default/import/step1', $data );
                    }
                } else {
                    $data['fields'] = $this->db->list_fields($tablename);
                    $this->template->write_view('content', 'default/import/step1', $data );
                }
            }

            // step2
            if ($step == 2) {
                $step = $this->session->userdata('step');
                if ($step !== 'step1') redirect('admin/mydomain/import_step/csv/step/1');

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
                            redirect('admin/mydomain/import_step/csv/step/3');
                        }
                    }
                    if ( $error_found ) {
                        $this->template->write_view('content', 'default/import/step2', $data );
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
                    $data['prevurl'] = site_url("admin/mydomain/import_step/csv/step/1");
                    $data['field_list'] = $field_list;
                    $this->template->write_view('content', 'default/import/step2', $data );
                }
            }
            // step3
            if ($step == 3) {
                $step = $this->session->userdata('step');
                if ($step !== 'step3') redirect('admin/mydomain/import_step/csv/step/2');
                $timezone = $this->sitesettings->get_settings('timezone');
                $isdaylightsaving = $this->sitesettings->get_settings('isdaylightsaving');
                $currenttime = gmt_to_local(time(), $timezone, $isdaylightsaving );
                $field_without_nulls		 = $this->session->userdata( 'postfld' ) ;
                $csvfield_without_nulls		 = $this->session->userdata( 'csvfld' ) ;
                #pre($csvfield_without_nulls);
                if ($_POST) {
                    // save to the db
                    $selectedcsvrows            = $this->input->post( 'ischecked' );
                    #pre($selectedcsvrows);
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
                                $this->db->insert($this->tpersonal_domains, $tmpdata);
                                $insertid = $this->db->insert_id();
                                $this->db->insert($this->tuser_personal_domains, array('user_id' => $this->logged_userid, 'domain_id' => $insertid));
                            }
                    }
                    $this->session->set_flashdata('flash', 'Successfully imported data.');
                    redirect("admin/mydomain/import_step/csv/step/1");
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
                    $this->template->write_view('content', 'default/import/step3', $data );
                }
            }


        }

        if ($type == 'xml') {

        }

        $this->template->render();
    }


    function import_csv() {
    }

    function export($format='csv') {
        $data = array();
        $format = isset($_POST['exportfmt']) ? $_POST['exportfmt'] : (!empty($format) ? $format : 'csv');
        $data['exportfmt'] = $format ;
        $flash = $this->session->flashdata('flash');
        if($flash == 'emptydata') {
            $data['flash']['defaulterror'] = 'ExportError: Data is empty';
        }
        if ($_POST) {
            $this->load->library('exporter');
            /*$sql = "SELECT
                personal_domains.domain_id as domain_id, personal_domains.project_id as project_id, personal_domains.type, personal_domains.templateid,
                personal_domains.customtemplate, personal_domains.importance, personal_domains.url,
                personal_domains.loginurl, personal_domains.username, personal_domains.password,
                personal_domains.changefreq, personal_domains.mark, personal_domains.notes
                FROM personal_domains
                INNER JOIN user_personal_domains ON user_personal_domains.domain_id = personal_domains.domain_id
                WHERE user_personal_domains.user_id = $this->logged_userid ";*/
            $sql = "SELECT * FROM personal_domains
                INNER JOIN user_personal_domains ON user_personal_domains.domain_id = personal_domains.domain_id
                WHERE user_personal_domains.user_id = $this->logged_userid ";
            // CSV
            if ($format == 'csv') {
                $filename = date('m-d-Y') . '-personal-domainaccess.csv';
                $this->exporter->export_csv( array('filename' => $filename, 'sql_query' => $sql ));
            }
            // XML
            if ($format == 'xml') {
                $filename = date('m-d-Y') . '-personal-domainaccess.xml';
                $this->exporter->export_xml( array('filename' => $filename, 'sql_query' => $sql ));
            }
        }
        $this->template->write_view('content', 'default/mydomain_export', $data);
        $this->template->render();
    }

    function is_domain_exist($id) {
        $rs = $this->mdl_domains->get_domain_by_id($id);
        if (FALSE == $rs) {
            return FALSE;
        }
        return $rs;
    }


    function isvalid_url_check($url) {
        if (!empty($url)) {
            if (filter_var($url, FILTER_VALIDATE_URL) == FALSE) {
                $this->form_validation->set_message('isvalid_url_check', '%s is invalid.');
                return FALSE;
            }
            return TRUE;
        }
    }

    function search() {
        $this->session->unset_userdata($this->sessdata);
        $data['results'] = array();
        $data['projectid'] = '';
        $data['userid'] = '';
        $data['type_id'] = '';
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
            $this->session->set_userdata(array('tid' => $type_id, 'pid' => $projectid, 's' => 'go' ));
            redirect('admin/mydomain/searchresult');
        }

        $data['allusers'] = $this->mdl_users->get_all_users();
        $this->mdl_projects->where = '';
        $this->mdl_projects->where = array('visibility' => 'public');
        $this->mdl_projects->order_by = 'project ASC';
        $data['allprojects'] = $this->mdl_projects->get_all_projects();
        $this->mdl_projects->where = '';
        $this->mdl_projects->order_by = '';
        $data['allaccounttypes'] = $this->mdl_account_type->get_all_accounttypes( array('rows' => NULL));
        $this->template->write_view('content', 'default/mydomain_search', $data);
        $this->template->render();
    }

    function searchresult() {
        $this->load->library( 'pagination' );
        $data['results'] = array();

        $s              = $this->session->userdata('s');
        if (empty($s)) redirect('admin/mydomain/search');
        $userid         = $this->logged_userid;
        $projectid      = $this->session->userdata('pid');
        $type_id        = $this->session->userdata('tid');
        $projectid      = $this->input->post('projectid') ? $this->input->post('projectid') : $projectid ;
        $type_id        = $this->input->post('type_id') ? $this->input->post('type_id') : $type_id;

        $uri = $this->uri->uri_to_assoc(2);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;

        $this->paging['per_page']	= 20;
        $this->paging['uri_segment'] 	= 5;
        $this->paging['base_url'] 	= site_url('admin/mydomain/searchresult/p');
        $rs1 = array();
        $rs2 = array();

        if($projectid != -1 && $type_id != -1 ) {
            $this->mdl_domains->where = array('projects.projectid' => $projectid, 'account_type.type_id' => $type_id );
        } elseif($type_id != -1) {
            $this->mdl_domains->where = array('account_type.type_id' => $type_id);
        } elseif($projectid != -1) {
            $this->mdl_domains->where = array('projects.projectid' => $projectid);
        }
        $rs1 = $this->mdl_domains->get_all_personaldomains($userid);
        $rs2 = $this->mdl_domains->get_all_personaldomains($userid, array('rows' => $this->paging['per_page'], 'offset' => $uri['p']));

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
        $this->template->write_view('content', 'default/mydomain_search', $data);
        $this->template->render();
    }

    function customfield() {
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $data['results'] = $this->mdl_customfields->list_customfields($this->tpersonal_domains);
        $this->form_validation->set_rules('fieldname', 'fieldname', 'required|trim|max_length[254]|callback_fieldname_check');
        if ( $this->form_validation->run() ) {
            $this->mdl_customfields->add_field(array(
                    'fieldname' => $this->input->post('fieldname'),
                    'tablename' => $this->tpersonal_domains,
                    'fieldtype' => 'text'));

            $this->session->set_flashdata( 'flash', 'Successfully created a field.' ) ;
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }else {
            $data['flash']['error'] = validation_errors();
            $this->template->write_view('content', 'default/mydomain_customfield', $data);
        }
        $this->template->render();
    }

    function deletefield() {
        $uri = $this->uri->uri_to_assoc(1);
        $f = $uri['deletefield'] ;
        $this->mdl_customfields->delete_field( array('fieldname' => $f, 'tablename' => $this->tpersonal_domains ) );
        $this->session->set_flashdata( 'flash', 'Successfully deleted a field.' ) ;
        header('location:' . $_SERVER['HTTP_REFERER'] );
    }

    function dologin2($id) {
        $rs = $this->mdl_domains->get_personaldomain_by_id($id, array('resulttype' => 'row_array'));
        #print_r($rs);
        if ($rs) {
            $type = strtolower($rs['acctype']);
            $templateid = $rs['templateid'];
            #echo "e=$templateid" ;

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
                        $tpl = $this->mdl_domains->get_personaldomain_customtemplate($id);
                        $tpl['template'] = $tpl['customtemplate'];
                        if (! empty($tpl['template'])) {
                            $tags = $this->mdl_domain_customfields->get_personaldomain_customfields($id);
                            $customfield = array();
                            foreach($tags as $k => $v) {
                                $customfield['{'.$v['customfield'].'}'] = $v['customfield'];
                                #$customfield['{'.$v['customfield'].'_value}'] = $v['value'];
                            }
                            $data = array( '{username}' => $rs['username'], '{password}' => $rs['password'],  '{username_value}' => $rs['username'], '{password_value}' => $rs['password'], '{loginurl}' => $rs['loginurl'] );
                            $data = array_merge($customfield, $data);
                            $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                            // convert ang html element submit into hidden textfield.
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
          // $this->load->library('cpanel');
          // $params = array('user' => $rs['username'], 'pass' => $rs['password'], 'host' => $rs['url'] );
          // $this->cpanel->init('index.html', $params, array(), FALSE );
          // $this->cpanel->execute_page('index.html', $params);
                    $tpl = $this->mdl_logintemplates->get_template_by_id($templateid);
                    $data = array( '{username}' => $rs['username'], '{password}' => $rs['password'], '{loginurl}' => $rs['loginurl'] );
                    $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                    $this->load->view('logintemplates/templateholder', $v);
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
                        $tpl = $this->mdl_domains->get_personaldomain_customtemplate($id);
                        $tpl['template'] = $tpl['customtemplate'];
                        if (! empty($tpl['template'])) {
                            $tags = $this->mdl_domain_customfields->get_personaldomain_customfields($id);
                            $customfield = array();
                            foreach($tags as $k => $v) {
                                $customfield['{'.$v['customfield'].'}'] = $v['customfield'];
                                #$customfield['{'.$v['customfield'].'_value}'] = $v['value'];
                            }
                            $data = array( '{username}' => 'username', '{password}' => 'password',  '{username_value}' => $rs['username'], '{password_value}' => $rs['password'], '{loginurl}' => $rs['loginurl'] );
                            $data = array_merge($customfield, $data);
                            $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                            // convert ang html element submit into hidden textfield.
                            $v['template'] = str_replace('type="submit"', 'type="hidden"', $v['template']);
                            $this->load->view('logintemplates/templateholder', $v);
                        }
                        else {
                            $this->session->set_flashdata( 'flash', 'Template not found.' ) ;
                            header('location:' . $_SERVER['HTTP_REFERER'] );
                        }
                    }
                    break;
            }
        }
    }

    function dologin($id) {
        $rs = $this->mdl_domains->get_personaldomain_by_id($id, array('resulttype' => 'row_array'));
        #print_r($rs);
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
                default:
                    $tpl = $this->mdl_logintemplates->get_template_by_id($templateid);
                    if ( isset($tpl['name']) && $tpl['name'] == 'wordpress') {
                        $data = array( '{username}' => $rs['username'], '{password}' => $rs['password'], '{loginurl}' => $rs['loginurl'], '{redirect_to}' => './wp-admin'  );
                        $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                        // convert ang html element submit into hidden textfield.
                        $v['template'] = str_replace('type="submit"', 'type="hidden"', $v['template']);
                        $this->load->view('logintemplates/templateholder', $v);
                    }elseif(isset($tpl['name']) && $tpl['name'] == 'drupal') {
                        $data = array( '{name}' => $rs['username'], '{pass}' => $rs['password'], '{loginurl}' => $rs['loginurl'] );
                        $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                        $v['template'] = str_replace('type="submit"', 'type="hidden"', $v['template']);
                        $this->load->view('logintemplates/templateholder', $v);
                    }else {
                        $tpl = $this->mdl_logintemplates->get_template_by_id($templateid);
                        if (!empty($tpl['name'])) {
                            $data = array( '{username}' => $rs['username'], '{password}' => $rs['password'], '{loginurl}' => $rs['loginurl'], '{redirect_to}' => './wp-admin'  );
                            $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                            $v['template'] = str_replace('type="submit"', 'type="hidden"', $v['template']);
                            $this->load->view('logintemplates/templateholder', $v);
                        } else {
                            $tpl = $this->mdl_domains->get_personaldomain_customtemplate($id);
                            $tpl['template'] = $tpl['customtemplate'];
                            if (! empty($tpl['template'])) {
                                $tags = $this->mdl_domain_customfields->get_personaldomain_customfields($id);
                                $customfield = array();
                                // get the custom login fields of the personal-domain
                                foreach($tags as $k => $v) {
                                    $customfield['{'.$v['customfield'].'}'] = $v['customfield'];
                                }
                                $data = array( '{username}' => $rs['username'], '{password}' => $rs['password'], '{loginurl}' => $rs['loginurl'] );
                                $data = array_merge($customfield, $data);
                                $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
                                $v['template'] = str_replace('type="submit"', 'type="hidden"', $v['template']);
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

    function delete_customfield($id) {
        if (!empty($id)){
            $this->db->where( array('customfieldid' => $id, 'user_id' => $this->logged_userid) );
            $this->db->delete($this->tpersonaldomain_customfields);
            $this->session->set_flashdata( 'flash', 'Successfully deleted a field.' ) ;
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }
    }

    function fieldname_check() {
        $fieldname = $this->input->post('fieldname');
        if ($this->db->field_exists( $this->fieldprefix . $fieldname, $this->tpersonal_domains)) {
            $this->form_validation->set_message('fieldname_check',  sprintf('Field %s already exist in table %s. Please choose another one.', $fieldname, $this->tpersonal_domains));
            return FALSE;
        }
        return TRUE;
    }

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

    function _check_valid_extension( $f ) {
        $validext = explode( '|', $this->allowed_type );
        $ext = end(explode( '.', $f ));
        if ( !in_array( $ext, $validext ) ) {
            return FALSE;
        }
        return TRUE;
    }
}
