<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class mdl_domain_customfields extends Model {

    #var $where;
     private $where;
    var $domainid;

    function __construct() {
        parent::Model();
        $this->table1 = 'domain_customfields';
        $this->personaldomain_customfields = 'personaldomain_customfields';
    }

    function _prep_query() {
            if ($this->where) $this->db->where($this->where);
    }

    function save($params = array()) {
        $default = array(
                'tablename'		=> $this->table1,
        );
        $params = array_merge( $default, $params );
        $data = array(
                'domain_id'             => $this->input->post( 'domain_id' ),
                'customfield' 		=> $this->input->post( 'domain_customfield' ),
                'value' 		=> $this->input->post( 'value' ),
                'user_id'                => $this->input->post( 'logged_userid' ),
        );
        $this->db->insert($params['tablename'], $data);
        $this->customfieldid = $this->db->insert_id();
    }

    function update($params = array()) {
        $default = array(
                'tablename'		=> $this->table1,
                'user_id'               => '',
        );
        $params = array_merge( $default, $params );
        $customfieldvalues = isset($_POST['customfieldvalues']) ? $_POST['customfieldvalues'] : NULL ;
        if (count($customfieldvalues) > 0) {
            foreach($customfieldvalues as $k => $v) {
                $data = array('value' => $v);
                $this->db->where( array('customfieldid' => $k, 'user_id' => $params['user_id']));
                $this->db->update($params['tablename'], $data);
            }
        }
    }

    function get_all_domain_customfields( $domainid, $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'            => 'result_array',
        );
        if(!empty($domainid)) $this->db->where('domain_customfields.domain_id', $domainid);
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        $this->_prep_query();
        return $this->db->get( $this->table1 )->{$params['resulttype']}();
    }

    function get_all_personaldomain_customfields( $domainid, $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'            => 'result_array',
        );
        if(!empty($domainid)) $this->db->where('personaldomain_customfields.domain_id', $domainid);
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        $this->_prep_query();
        return $this->db->get($this->personaldomain_customfields)->{$params['resulttype']}();
    }

    function get_domain_customfields($domainid, $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'            => 'result_array',
        );
        $params = array_merge( $default, $params );
        if($domainid != -1 && $domainid != '') $this->db->where('domain_customfields.domain_id', $domainid);
        return $this->db->get( $this->table1 )->{$params['resulttype']}();
    }

    function get_personaldomain_customfields($domainid, $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'            => 'result_array',
        );
        $params = array_merge( $default, $params );
        if($domainid != -1 && $domainid != '') $this->db->where('personaldomain_customfields.domain_id', $domainid);
        return $this->db->get($this->personaldomain_customfields)->{$params['resulttype']}();
    }

}

