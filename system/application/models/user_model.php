<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class user_model extends Model {

    var $where;
    var $select;
    var $user_id;
    var $insert_id;

    function __construct() {
        parent::Model();
        $this->table1 = 'users';

    }

    function save() {
        $data = array(
                'tmpid'			=> $this->input->post( 'tmpid' ),
                'username'		=> $this->input->post( 'username' ),
                'firstname'             => $this->input->post( 'firstname' ),
                'lastname' 		=> $this->input->post( 'lastname' ),
                'email' 		=> $this->input->post( 'email' ),
                'skypeid' 		=> $this->input->post( 'skypeid' ),
                'position' 		=> $this->input->post( 'position' ),
                'pwlength'		=> $this->input->post( 'pwlength' ),
        );
        $password = $this->input->post( 'password' );
        $password = ! empty($password) ? ($this->dx_auth->_encode($password)) : '';
        if (! empty($password)) {
            $data['password'] = $password;
            $data['clearpassword'] = $this->input->post( 'password' );
        }

        if ($this->input->post('type')) {
            $data['role_id'] = $this->input->post('type');
        }
        if (!empty($this->user_id) && $this->user_id > 0 ) {
            $data['modified'] = date('Y-m-d H:i:s', time());
            $this->db->where('id', $this->user_id);
            $this->db->update('users', $data);
        }else {
            $data['created'] = date('Y-m-d H:i:s', time());
            $this->db->insert('users', $data);
            $this->insert_id = $this->db->insert_id();
        }

        // upon adding the user, lets see if the group alread assign to a project
        $this->db->select('domain_id');
        $this->db->where('deptid',$this->input->post('deptid'));
        $rs = $this->db->get('group_domains');
        if ($rs->num_rows() > 0) {
            $data = $rs->result_array();
            $tmpuserid = !empty($this->user_id) ? intval($this->user_id) : intval($this->insert_id);
            if (! empty($tmpuserid)) {
                $this->db->delete('user_domains', array( 'user_id' => $tmpuserid));
            }
            foreach($data as $k => $v) {
                $domain_id = $v['domain_id'];
                $d = array('domain_id' => $domain_id, 'user_id' =>  $tmpuserid );
                $this->db->insert('user_domains', $d);
            }
        }

    }


    function _prep_query() {
        if ($this->where) $this->db->where($this->where);
        if ($this->select) $this->db->select($this->select);
    }


    function get_all_users( $params = array()) {
        $default = array(
                'rows'			=> '10',
                'offset'		=> '',
                'deptid'		=> '',
                'resulttype'            => 'result_array',
                ) ;
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        if ( !empty($params['deptid'])) {
			$this->db->where('user_groups.deptid', $params['deptid']);
			$this->db->join('user_groups', 'user_groups.userid = users.id');
		}
        $this->_prep_query();
        return $this->db->get( 'users' )->{$params['resulttype']}();

    }

    function get_all_userdomains( $params = array()) {
        $default = array(
                'rows'			=> '10',
                'offset'		=> '',
                'resulttype'	=> 'result_array',
                ) ;
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        $this->_prep_query();
        return $this->db->get( 'user_domains' )->{$params['resulttype']}();
    }

    function get_user_by_id( $id, $params = array()) {
        $default = array(
                'resulttype'	=> 'row_array',
        );
        $id = intval($id);
        if(!empty($id)) {
            $this->db->where('id', $id);
            $params = array_merge( $default, $params );
            $this->_prep_query();
            return $this->db->get( $this->table1 )->{$params['resulttype']}();
        }
        return FALSE;
    }

    function get_all_users_by_group( $gid, $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'	=> 'result_array',
                ) ;
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        #!empty($gid) ? $this->db->where('users.deptid', $gid ) : '';
		if (!empty($gid)) {
			$this->db->where('user_groups.deptid', $gid);
			$this->db->join('user_groups', 'user_groups.userid = users.id');
		}
        $this->_prep_query();
        return $this->db->get( 'users' )->{$params['resulttype']}();

    }

}

