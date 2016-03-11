<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_users extends Model {

    var $where;
    var $select;
	var $order_by;
    var $user_id;
    var $insert_id;

    function __construct() {
        parent::Model();
        $this->table1 = 'users';
        $this->tuser_groups = 'user_groups';
        $this->tuser_domains = 'user_domains';
        $this->tuser_projects = 'user_projects';

    }

    function save() {
        $data = array(
                'tmpid'			=> $this->input->post( 'tmpid' ),
                'username'		=> $this->input->post( 'username' ),
                'firstname'     => $this->input->post( 'firstname' ),
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
        if (!empty($this->user_id)) {
            $data['modified'] = date('Y-m-d H:i:s', time());
            $this->db->where('id', $this->user_id);
            $this->db->update('users', $data);
        }else {
            $data['created'] = date('Y-m-d H:i:s', time());
            $this->db->insert('users', $data);
            $this->insert_id = $this->db->insert_id();
        }

        // upon adding the user, lets see if the group already assign to a project
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

    function save_user_to_group() {
        $ids = $this->input->post( 'user_groups' );
        $ids = !empty($ids) ? $ids : array();
        $gidlist    = $this->input->post( 'gidlist' );
        $user_id   = $this->input->post( 'user_id' );
        $tmpgidlist = explode(',', $gidlist) ;
        // delete previous user ids
        if ( !empty($tmpgidlist)) {
            foreach($tmpgidlist as $tmpid) {
                $this->db->delete($this->tuser_groups, array('userid' => $user_id, 'deptid' => $tmpid));
            }
        }
        // save only katong na.check nga domains
        $tmpgid = array_intersect($tmpgidlist, $ids);
        if (count($tmpgid) > 0) {
            foreach($tmpgid as $id ) {
                $data = array('userid' => $user_id, 'deptid' => $id );
                $this->db->insert($this->tuser_groups, $data);
            }
        }
        
    }

    function save_projectaccess() {
        $ids = $this->input->post( 'chk' );
        $ids = !empty($ids) ? $ids : array();
        $gidlist    = $this->input->post( 'gidlist' );
        $user_id   = $this->input->post( 'gid' );
        $tmpgidlist = explode(',', $gidlist);

        if ( !empty($tmpgidlist)) {
            foreach($tmpgidlist as $tmpid) {
                $projectdomains = $this->mdl_projects->get_all_projectdomains($tmpid);
                foreach ($projectdomains as $d) {
                    // remove domains
                    $this->db->delete($this->tuser_domains, array('user_id' => $user_id, 'domain_id' => $d['domain_id']));
                }
                // remove projects
                $this->db->delete($this->tuser_projects, array('user_id' => $user_id, 'projectid' => $tmpid));
            }
        }

        // save only katong na.check nga projs
        $tmpgid = array_intersect($tmpgidlist, $ids);
        if (count($tmpgid) > 0) {
            foreach($tmpgid as $id ) {
                $data = array('user_id' => $user_id, 'projectid' => $id );
                $this->db->insert($this->tuser_projects, $data);
            }
        }


 
        // save ang mga domains under sa kani nga projects
        foreach ($tmpgid as $projectid) {
            $projectdomains = $this->mdl_projects->get_all_projectdomains($projectid);
            foreach ($projectdomains as $d) {
                $rs = $this->db->get_where($this->tuser_domains, array('domain_id' => $d['domain_id'], 'user_id' => $user_id ));
                if ($rs->num_rows() == 0) {
                    $uddata = array('domain_id' => $d['domain_id'], 'user_id' => $user_id);
                    $this->db->insert($this->tuser_domains, $uddata);
                }
            }
        }
		
		
		
		
			
    }


    function _prep_query() {
        if ($this->where) $this->db->where($this->where);
        if ($this->select) $this->db->select($this->select);
        if ($this->order_by) $this->db->order_by($this->order_by);
    }


    function get_all_users( $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'deptid'		=> '',
                'resulttype'            => 'result_array',
                ) ;
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        if ( !empty($params['deptid'])) $this->db->where('user_groups.deptid', $params['deptid']);
        /*$this->db->select('users.id as id, users.tmpid as tmpid, users.role_id as role_id, 
			users.username as username, users.password, users.email, users.banned, users.ban_reason, users.last_ip,  
			users.created, users.modified, users.firstname, users.lastname, users.skypeid, users.position');*/
		$this->db->select('users.*, users.id as id');	
		$this->db->join('user_groups', 'user_groups.userid = users.id', 'LEFT');
		$this->db->group_by('users.id');
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


    function get_user_by_email( $email, $params = array()) {
        $default = array(
                'resulttype'	=> 'row_array',
        );
        if(!empty($email)) {
            $this->db->where('email', $email);
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
		if (!empty($gid)) {
			$this->db->where('user_groups.deptid', $gid );		
			$this->db->join('user_groups', 'user_groups.userid = users.id', 'LEFT');
		}
        $this->_prep_query();
        return $this->db->get( 'users' )->{$params['resulttype']}();

    }

}

