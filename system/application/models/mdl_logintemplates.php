<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class mdl_logintemplates extends Model {

    var $where;
    var $templateid;

    function __construct() {
        parent::Model();
        $this->tlogin_templates = 'login_templates';
        $this->tdomains = 'domains';
    }

    function save() {
        $data = array(
                'name'                  => $this->input->post( 'name' ),
                'template' 		=> $this->input->post( 'template' ),
        );
        if (!empty($this->templateid)) {
            $this->db->where('templateid', $this->templateid);
            $this->db->update($this->tlogin_templates, $data);
        }else {
            $data['created'] = time();
            $this->db->insert($this->tlogin_templates, $data);
            $this->templateid = $this->db->insert_id();
        }
    }

    function _prep_query() {
        if ($this->where) $this->db->where($this->where);
    }

    function get_all_logintemplates( $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'            => 'result_array',
                'field'                 => 'login_templates.name',
                'sort'                  => 'asc',
                ) ;
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        !empty($params['field'] ) ? $this->db->order_by($params['field'], $params['sort']) : $this->db->order_by('login_templates.name', 'asc') ;
        $this->_prep_query();
        return $this->db->get($this->tlogin_templates)->{$params['resulttype']}();
    }

    function get_template_by_id( $templateid, $params = array()) {
        $default = array(
                'resulttype'	=> 'row_array',
        );
        $templateid = intval($templateid);
        if(!empty($templateid)) {
            $this->db->where('login_templates.templateid', $templateid);
            $params = array_merge( $default, $params );
            $this->_prep_query();
            return $this->db->get( $this->tlogin_templates )->{$params['resulttype']}();
        }
        return FALSE;
    }


    function template_check($template) {
        $this->db->select('name');
        $this->db->where('name', $template);
        $rs = $this->db->get( $this->tlogin_templates );
        if ($rs->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }
    
    function getinsert_id(){
        return $this->templateid ;
    }

}