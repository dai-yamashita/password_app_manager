<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Mdl_department also known as groups
class Mdl_department extends Model {

    var $where;
    var $select;
    var $deptid;
	var $order_by;
    public $tuser_groups;
    
    function __construct() {
        parent::Model();
        $this->tdepartment = 'department';
        $this->tuser_groups = 'user_groups';

    }

    function save() {
        $data = array(
            'groupname' 	=> $this->input->post( 'department' ),
            'visibility'        => $this->input->post( 'visibility' ),
        );
        if (!empty($this->deptid) && $this->deptid > 0 ) {
            $this->db->where('deptid', $this->deptid);
            $this->db->update('department', $data);
        }else {
            $this->db->insert('department', $data);
        }
    }

 

    function _prep_query() {
        if ($this->where) $this->db->where($this->where);
        if ($this->select) $this->db->select($this->select);
		if ($this->order_by) $this->db->order_by($this->order_by);
    }

    function get_all_department( $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'            => 'result_array',
                ) ;
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        $this->_prep_query();
        return $this->db->get( 'department' )->{$params['resulttype']}() ;

    }

    function group_check($group) {
        $this->db->select('groupname');
        $this->db->where('department', $group);
        $rs = $this->db->get( $this->tdepartment );
        if ($rs->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function get_user_groups($params=array()) {
        $default = array(
                'resulttype'            => 'result_array',
                'userid'                => NULL,
                'deptid'                => NULL,
        ) ;
        $params = array_merge( $default, $params );
        !empty($params['userid'] ) ? $this->db->where('userid', $params['userid'])   : '' ;
        !empty($params['deptid'] ) ? $this->db->where('deptid', $params['deptid'])   : '' ;
        $this->_prep_query();
        return $this->db->get($this->tuser_groups  )->{$params['resulttype']}() ;

    }
    
    function get_department_by_id( $gid, $params = array()) {
        $default = array(
                'resulttype'	=> 'result_array',
        );
        $gid = intval($gid);
        if(!empty($gid)) {
            $this->db->where('deptid', $gid);
            $params = array_merge( $default, $params );
            $this->_prep_query();
            return $this->db->get($this->tdepartment)->{$params['resulttype']}();
        }
        return FALSE;

    }


}

