<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
cook by mandoy add me @facebook.com/artheman
*/
class Project extends Controller {
    var $browse;
    function __construct() {
        parent::Controller();
        if (! $this->dx_auth->is_logged_in()) redirect('login');
        $this->dx_auth->check_uri_permissions();
        $this->load->helper( 'custom' );
        $this->load->library( 'form_validation' );
        $this->load->library( 'pagination' );
        $this->load->library( 'csvimport' );
        $this->form_validation->set_error_delimiters('<p style="padding:2px" >', '</p>');
        $this->paging = $this->paging->get_paging_template();
        $this->allowed_type = 'csv|txt';
        $this->csv_path = './uploads/csv/';
        $this->tprojects = 'projects';
        $this->tuser_domains = 'user_domains';
        $this->sessd = array('impfile'   => '' ,'delim'     => '' , 'enc' => '' , 'step' => '' );
        // set the maximum memory limit
	ini_set('memory_limit', '150M');
    }

    function index() {
        $this->browse();
    }

// <editor-fold defaultstate="collapsed" desc=" form ">
    function form() {
        #$this->form_validation->set_rules('project', 'project name', 'required|trim|max_length[254]');
        $this->form_validation->set_rules('visibility', 'flag', 'required|trim');
        $this->form_validation->set_rules('project', 'project name', 'required|trim|max_length[254]');
        $id = isset($_POST['projectid']) ? intval($_POST['projectid']) : $this->uri->segment(4);
        if(empty($id)) $this->form_validation->set_rules('project', 'project name', 'callback_project_check');
        //echo "id=$id ";
        
        if($id) {
            $this->mdl_projects->where = array('projectid' => $id);
            $rs = $this->mdl_projects->get_all_projects(array('resulttype' => 'row_array'));
            $data['results'] = $rs;
            $project = $this->input->post( 'project' );
            if ($project != $rs['project']) {
                $this->form_validation->set_rules('project', 'project', 'required|callback_project_check');
            }
        }
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        if ( $this->form_validation->run() ) {
            $this->mdl_projects->projectid = $id;
            $this->mdl_projects->save();
            $this->session->set_flashdata( 'flash', 'Successfully saved project.' ) ;
            header('location:' . $_SERVER['HTTP_REFERER'] );
        }else {
            $data['flash']['error'] = validation_errors();
            $this->template->write_view('content', 'default/project_form', $data);
        }
        $this->template->render();
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" browse ">
    function browse() {
        $data['flash']['success'] = $this->session->flashdata( 'flash' );
        $uri = $this->uri->uri_to_assoc(2);
        $uri['p'] = !empty($uri['p']) ? intval($uri['p']) : 0;
        $total_rows = count($this->mdl_projects->get_all_projects());
        $this->paging['per_page']  = 100;
        $this->paging['uri_segment'] = 5;
        $this->paging['base_url'] = site_url('admin/project/browse/p');
        $this->paging['total_rows'] = $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $this->mdl_projects->order_by = 'project asc';
        $data['results'] 	= $this->mdl_projects->get_all_projects( array('rows' => $this->paging['per_page'], 'offset' => $uri['p'] ) );
        $this->mdl_projects->order_by = '';
        $this->template->write_view('content', 'default/project_browse', $data);
        $this->template->render();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" delete project by id ">
// delete a project
    function delete($id) {
        if ($this->dx_auth->is_role('member')) $this->dx_auth->deny_access('deny');
        if (!empty($id)) $this->db->delete('projects', array('projectid' => intval($id)));
        // delete also the project
        $this->db->delete('domain', array('project_id' => intval($id)));
        // i delete pud apil ang mga user nga nagpasubscribe sa mga domains in this project ??
        
        // k delete pud apil ang mga user
        $this->session->set_flashdata( 'flash', 'Successfully delete project.' ) ;
        redirect('admin/project/browse');
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" view project by id ">
    function view($projectid) {
        $projectid = !empty($projectid) ? $projectid : $this->input->post('projectid');
        $data['flash']['success'] = $this->session->flashdata('flash');
        $rs = $this->mdl_projects->get_project_by_id($projectid, array('resulttype' => 'row_array'));
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

        $total_rows = count($this->mdl_projects->get_all_projectdomains($projectid));
        #echo $total_rows;
        $this->paging['per_page'] = 20;
        $this->paging['uri_segment'] = 6;
        $this->paging['base_url'] = site_url( "admin/project/view/$projectid/p/");
        $this->paging['total_rows'] = $total_rows ;
        $this->pagination->initialize($this->paging);
        $data['pagination'] = $this->pagination->create_links();
        $data['domainaccess_title'] = "Projectname: " .$rs['project'] ;
        $data['title'] = $rs['project'] ;
        $data['gid'] = !empty($projectid) ? $projectid : '';
        $data['results'] = $this->mdl_domains->get_all_domains( array(
                'rows' => $this->paging['per_page'], 'offset' => $uri['p'],
                'field' => $this->browse['field'], 'sort' => $this->browse['sort'] )
        );

        $data['results'] = $this->mdl_projects->get_all_projectdomains( $projectid, array('rows' => $this->paging['per_page'], 'offset' => $uri['p']) );
        $this->template->write_view('content', 'default/project_domain_access', $data);
        $this->template->render();

    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" deleteproject ">
function deleteproject() {
    $uri = $this->uri->uri_to_assoc(1);
    $data = array('user_id' => intval($uri['userid']), 'projectid' => intval($uri['deleteproject']));
    #pre($data );
    $this->mdl_projects->delete_user_project($data);
    $this->session->set_flashdata( 'flash', 'Successfully remove user from project.' ) ;
    header('location:' . $_SERVER['HTTP_REFERER'] );
}

// </editor-fold>




// <editor-fold defaultstate="collapsed" desc=" is_project_exist ">

    function is_project_exist($id) {
        $rs = $this->mdl_projects->get_project_by_id($id);
        if (FALSE == $rs) {
            return FALSE;
        }
        return $rs;
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" project_check ">
    function project_check($project) {
        $this->mdl_projects->where = array('project' => $project);
        $rs = $this->mdl_projects->get_all_projects();
        if ( count($rs) > 0 ) {
            $this->form_validation->set_message('project_check',  sprintf('Projectname %s already exist. Please choose another one.', $project) );
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" import ">
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
                    $totalrows = $this->importer->import_csv(array('tablename' => $this->tprojects, 'primary_key' => 'projectid', 'filename' => $impfile ));
                }
                if ($format == 'xml') {
                    $totalrows = $this->importer->import_xml(array('tablename' => $this->tprojects, 'primary_key' => 'projectid', 'filename' => $impfile ));
                }
                if ($totalrows > 0) {
                    $this->session->set_flashdata('flash', "Successfully imported data" ) ;
                }else{
                    $this->session->set_flashdata('flash', 'No data has been imported.' ) ;
                }
                redirect("admin/project/import/$format");
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
        $this->template->write_view('content', 'default/projects_import', $data );
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
        $field_list = $this->db->list_fields($this->tprojects);
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
                        redirect('admin/project/import_step/csv/step/2');
                    }
                    else {
                        $data['flash']['error'] = $uploadeddata['upload_error'] ;
                        $this->template->write_view('content', 'default/import/step1_project', $data );
                    }
                } else {
                    $this->session->unset_userdata($this->sessd);
                    $data['fields'] = $this->db->list_fields($this->tprojects);
                    $this->template->write_view('content', 'default/import/step1_project', $data );
                }
            }

            // step2
            if ($step == 2) {
                $step = $this->session->userdata('step');
                if ($step !== 'step1') redirect('admin/project/import_step/csv/step/1');

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
                            redirect('admin/project/import_step/csv/step/3');
                        }
                    }
                    if ( $error_found ) {
                        $this->template->write_view('content', 'default/import/step2_project', $data );
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
                    $data['prevurl'] = site_url("admin/project/import_step/csv/step/1");
                    $data['field_list'] = $field_list;
                    $this->template->write_view('content', 'default/import/step2_project', $data );
                }
            }
            // step3
            if ($step == 3) {
                $step = $this->session->userdata('step');
                if ($step !== 'step3') redirect('admin/project/import_step/csv/step/2');
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
                            unset($tmpdata['projectid']);
                            if (in_array( $j, $selectedcsvrowstmp)) {
                                $this->db->insert($this->tprojects, $tmpdata);
                            }
                    }
                    $this->session->set_flashdata('flash', 'Successfully imported data.');
                    $this->session->unset_userdata($this->sessd);
                    redirect("admin/project/import_step/csv/step/1");
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
                    $this->template->write_view('content', 'default/import/step3_project', $data );
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
            $sql = 'SELECT projects.project, projects.desc FROM projects';
            // CSV
            if ($format == 'csv') {
                $filename = date('m-d-Y') . '-project.csv';
                $this->exporter->export_csv( array('filename' => $filename, 'sql_query' => $sql));
            }
            // XML
            if ($format == 'xml') {
                $filename = date('m-d-Y') . '-project.xml';
                $this->exporter->export_xml( array('filename' => $filename, 'sql_query' => $sql));
            }
        }
        $this->template->write_view('content', 'default/projects_export', $data);
        $this->template->render();
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

