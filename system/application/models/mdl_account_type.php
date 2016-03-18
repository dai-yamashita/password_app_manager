<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class mdl_account_type extends Model {
    var $type_id;
    var $where;
    function __construct() {
        parent::Model();
        $this->taccount_type = 'account_type';
    }

    function get_account_types() {
        $this->db->order_by('acctype', 'asc');
        return $this->db->get($this->taccount_type)->result_array();
    }

    function save() {
        $data = array(
                'acctype'	=> $this->input->post( 'acctype' ),
                'desc' 		=> $this->input->post( 'desc' ),
        );
        if (!empty($this->type_id)) {
            $this->db->where('type_id', $this->type_id);
            $this->db->update($this->taccount_type, $data);
        }else {
            $data['created'] = time();
            $this->db->insert($this->taccount_type, $data);
            $this->type_id = $this->db->insert_id();
        }
    }

    function _prep_query() {
        if ($this->where) $this->db->where($this->where);
    }

    function save_user_domains() {
        $ids = $_POST['user_domains'];
        $this->db->delete($this->taccount_type, array('domain_id' => $this->domain_id));
        foreach($ids as $id ) {
            $data = array('domain_id' => $this->domain_id, 'user_id' => $id );
            $this->db->insert( $this->taccount_type, $data);
        }

    }

    function get_all_accounttypes( $params = array()) {
        $default = array(
                'rows'          => '10',
                'offset'        => '',
                'resulttype'    => 'result_array',
        );
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        $this->_prep_query();
        $this->db->order_by('created', 'desc');
        return $this->db->get( $this->taccount_type )->{$params['resulttype']}();
    }

    function get_account_by_id( $type_id) {
        $type_id = intval($type_id);
        if(!empty($type_id)) {
            $this->db->where('type_id', $type_id);
            $this->_prep_query();
            return $this->db->get( $this->taccount_type )->result_array();
        }
        return FALSE;
    }

    function accounttype_check($acctype) {
        $this->db->where('acctype', $acctype);
        $rs = $this->db->get( $this->taccount_type );
        if ($rs->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function delete($id) {
        if (!empty($id)) $this->db->delete($this->taccount_type, array('type_id' => intval($id)));
        return TRUE;
    }

}


