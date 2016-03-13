<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
cook by mandoy add me @facebook.com/artheman
*/
class Dologin extends Controller {
    function __construct() {
        parent::Controller();
        if (! $this->dx_auth->is_logged_in()) redirect('login');
        $this->dx_auth->check_uri_permissions();
        $this->load->library( 'form_validation' );
        $this->form_validation->set_error_delimiters('<p style="padding:2px" >', '</p>');
        $this->paging = $this->paging->get_paging_template();
    }


    function index($id) {
        $rs = $this->mdl_domains->get_domain_by_id($id, array('resulttype' => 'row_array'));
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
//                    $this->load->library('parser');
//                    $this->load->library('webformlogin');
//                    $rs = $this->webformlogin->init( array(
//                        'host' => $rs['url'], 'username' => $rs['username'], 'password' => $rs['password'] ));
//                    if ($rs) redirect($rs['url']);
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
                        $tpl = $this->mdl_domains->get_domain_customtemplate($id);
                        $tpl['template'] = $tpl['customtemplate'];
                        if (! empty($tpl['template'])) {
                            $tags = $this->mdl_domain_customfields->get_domain_customfields($id);
                            $customfield = array();
                            foreach($tags as $k => $v) {
                                $customfield['{'.$v['customfield'].'}'] = $v['customfield'];
                                $customfield['{'.$v['customfield'].'_value}'] = $v['value'];
                            }
                            $data = array( '{username}' => 'username', '{password}' => 'password',  '{username_value}' => $rs['username'], '{password_value}' => $rs['password'], '{loginurl}' => $rs['loginurl'] );
                            $data = array_merge($customfield, $data);
                            #print_r($data);
                            $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
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
                        $tpl = $this->mdl_domains->get_domain_customtemplate($id);
                        $tpl['template'] = $tpl['customtemplate'];
                        if (! empty($tpl['template'])) {
                            $tags = $this->mdl_domain_customfields->get_domain_customfields($id);
                            $customfield = array();
                            foreach($tags as $k => $v) {
                                $customfield['{'.$v['customfield'].'}'] = $v['customfield'];
                                $customfield['{'.$v['customfield'].'_value}'] = $v['value'];
                            }
                            $data = array( '{username}' => 'username', '{password}' => 'password',  '{username_value}' => $rs['username'], '{password_value}' => $rs['password'], '{loginurl}' => $rs['loginurl'] );
                            $data = array_merge($customfield, $data);
                            #print_r($data);
                            $v['template'] = str_replace( array_keys($data) , array_values($data), $tpl['template']);
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
}