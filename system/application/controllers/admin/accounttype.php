<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
cook by mandoy add me @facebook.com/artheman
*/
class Accounttype extends Controller {
    function __construct() {
        parent::Controller();
        if (! $this->dx_auth->is_logged_in()) redirect('login');
        $this->dx_auth->check_uri_permissions();
        $this->load->helper( 'custom' );
        $this->load->library( 'form_validation' );
        $this->load->library( 'csvimport' );
        $this->form_validation->set_error_delimiters('<p style="padding:2px" >', '</p>');
        $this->paging = $this->paging->get_paging_template();
        $this->allowed_type = 'csv|txt';
        $this->csv_path = './uploads/csv/';
        $this->taccount_type = 'account_type';
        $this->sessd = array('impfile'   => '' ,'delim'     => '' , 'enc' => '' , 'step' => '' );
        // set the maximum memory limit
	ini_set('memory_limit', '150M');
    }

    function index() {
        $this->browse();
    }

    function form() {
        $this->form_validation->set_rules('acctype', 'account type', 'required|trim|max_length[254]');
        $this->form_validation->set_rules('desc', 'desc', 'trim|max_length[254]');
        $id = isset($_POST['type_id']) ? intval($_POST['type_id']) : $this->uri->segment(4);
        if ($id) {
            $this->mdl_account_type->where = array('type_id' => $id);
            $rs = $this->mdl_account_type->get_all_accounttypes(array('resulttype' => 'row_array'));
            $data['results'] = $rs;
            $acctype = $this->input->post( 'acctype' );
            if ($acctype != $rs['acctype']) {
                $this->form_validation->set_rules('acctype', 'type', 'required|callback_accounttype_check');
            }
        }else {
            $this->form_validation->set_rules('acctype', 'type', 'required|callback_accounttype_check');
        }
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        if ( $this->form_validation->run() ) {
            $this->mdl_account_type->type_id = $id;
            $this->mdl_account_type->save();
            $this->session->set_flashdata( 'flash', 'Successfully saved account type.' ) ;
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }else {
            $data['flash']['error'] = validation_errors();
            $this->template->write_view('content', 'default/accounttype_form', $data);
        }
        $this->template->render();
    }

    function browse() {
        $this->load->library( 'pagination' );
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $uri = $this->uri->uri_to_assoc(2);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        $total_rows = count($this->mdl_account_type->get_all_accounttypes());
        $this->paging['per_page']		= 20;
        $this->paging['uri_segment'] 	= 5;
        $this->paging['base_url'] 		= site_url('admin/accounttype/browse/p');
        $this->paging['total_rows'] 	= $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['results'] 	= $this->mdl_account_type->get_all_accounttypes( array('rows' => $this->paging['per_page'], 'offset' => $uri['p'] ) );
        $this->template->write_view('content', 'default/accounttype_browse', $data);
        $this->template->render();
    }

    function delete($id) {
        if ($this->dx_auth->is_role('member')) $this->dx_auth->deny_access('deny');
        $this->mdl_account_type->delete($id);
        $this->session->set_flashdata( 'flash', 'Successfully delete account type.' ) ;
        redirect('admin/accounttype/browse');
    }

    function import($format = 'csv') {
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
                    $totalrows = $this->importer->import_csv(array('tablename' => $this->taccount_type, 'primary_key' => 'type_id', 'filename' => $impfile ));
                }
                if ($format == 'xml') {
                    $totalrows = $this->importer->import_xml(array('tablename' => $this->taccount_type, 'primary_key' => 'type_id', 'filename' => $impfile ));
                }
                if ($totalrows > 0) {
                    $this->session->set_flashdata('flash', "Successfully imported data" ) ;
                }else{
                    $this->session->set_flashdata('flash', 'No data has been imported.' ) ;
                }
                redirect("admin/accounttype/import/$format");
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
        $this->template->write_view('content', 'default/accounttype_import', $data );
        $this->template->render();
    }

    function import_step() {
        $uri = $this->uri->uri_to_assoc(1);
        #pre($uri);
        $type = strtolower($uri['import_step']);
        $type = !empty($type) ? $type : (!empty($_POST['import_step']) ? $_POST['import_step'] : '');
        $data = array();
        $step = !empty($uri['step']) ? $uri['step'] : ( !empty($_POST['step']) ? $_POST['step'] : '' );
        $field_list = array();
        $field_list = $this->db->list_fields($this->taccount_type);
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
                        redirect('admin/accounttype/import_step/csv/step/2');
                    }
                    else {
                        $data['flash']['error'] = $uploadeddata['upload_error'] ;
                        $this->template->write_view('content', 'default/import/step1_accounttype', $data );
                    }
                } else {
                    $this->session->unset_userdata($this->sessd);
                    $data['fields'] = $this->db->list_fields($this->taccount_type);
                    $this->template->write_view('content', 'default/import/step1_accounttype', $data );
                }
            }

            // step2
            if ($step == 2) {
                $step = $this->session->userdata('step');
                if ($step !== 'step1') redirect('admin/accounttype/import_step/csv/step/1');
                #echo "x=$step";
                if ($_POST) {
                    #pre($_POST);
                    $field_without_nulls    = array_filter($_POST['field_list'], "is_field_negative" );
                    #pre($field_without_nulls);
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
                            redirect('admin/accounttype/import_step/csv/step/3');
                        }
                    }
                    if ( $error_found ) {
                        #echo 'error f';
                        $this->template->write_view('content', 'default/import/step2_accounttype', $data );
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
                    $data['prevurl'] = site_url("admin/accounttype/import_step/csv/step/1");
                    $data['field_list'] = $field_list;
                    $this->template->write_view('content', 'default/import/step2_accounttype', $data );
                }
            }
            // step3
            if ($step == 3) {
                $step = $this->session->userdata('step');
                if ($step !== 'step3') redirect('admin/accounttype/import_step/csv/step/2');
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
                            $tmpdata['created'] = $currenttime;
                            unset($tmpdata['type_id']);
                            if (in_array( $j, $selectedcsvrowstmp)) {
                                $this->db->insert($this->taccount_type, $tmpdata);
                            }
                    }
                    $this->session->set_flashdata('flash', 'Successfully imported data.');
                    $this->session->unset_userdata($this->sessd);
                    redirect("admin/accounttype/import_step/csv/step/1");
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
                    $this->template->write_view('content', 'default/import/step3_accounttype', $data );
                }
            }


        }

        if ($type == 'xml') {

        }

        $this->template->render();
    }

    function export($format = 'csv') {
        $data = array();
        $format = isset($_POST['exportfmt']) ? $_POST['exportfmt'] : (!empty($format) ? $format : 'csv');
        $data['exportfmt'] = $format ;
        if ($_POST) {
            $this->load->library('exporter');
            $sql = 'SELECT account_type.type_id, account_type.acctype, account_type.desc, account_type.created FROM account_type';
            // CSV
            if ($format == 'csv') {
                $filename = date('m-d-Y') . '-accounttypes.csv';
                $this->exporter->export_csv( array('filename' => $filename, 'sql_query' => $sql));
            }
            // XML
            if ($format == 'xml') {
                $filename = date('m-d-Y') . '-accounttypes.xml';
                $this->exporter->export_xml( array('filename' => $filename, 'sql_query' => $sql));
            }
        }
        $this->template->write_view('content', 'default/accounttype_export', $data);
        $this->template->render();
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

    function accounttype_check($type) {
        $res = $this->mdl_account_type->accounttype_check($type);
        if ($res) {
            $this->form_validation->set_message('accounttype_check', "Acounttype $type already exist. Please choose another.");
            return FALSE;
        }
        return TRUE;
    }



}

