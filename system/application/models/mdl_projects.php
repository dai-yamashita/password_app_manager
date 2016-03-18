<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class mdl_projects extends Model {

    var $where;
	var $order_by;
    var $projectid;

    function __construct() {
        parent::Model();
        $this->tprojects = 'projects';
        $this->tuser_projects = 'user_projects';
    }

    function save() {
        $data = array(
                'project'	=> $this->input->post( 'project' ),
                'desc' 		=> $this->input->post( 'desc' ),
				'visibility'=> $this->input->post( 'visibility' ),
        );
        if (!empty($this->projectid)) {
            $this->db->where('projectid', $this->projectid);
            $this->db->update($this->tprojects, $data);
        }else {
            $data['created']	= time();
            $this->db->insert($this->tprojects, $data);
            $this->projectid = $this->db->insert_id();
        }
    }

    function _prep_query() {
        if ($this->where) $this->db->where($this->where);
		if ($this->order_by) $this->db->order_by($this->order_by);
    }

    function save_user_domains() {
        $ids = $_POST['user_domains'];
        $this->db->delete($this->tprojects, array('domain_id' => $this->domain_id));
        foreach($ids as $id ) {
            $data = array('domain_id' => $this->domain_id, 'user_id' => $id );
            $this->db->insert( $this->tprojects, $data);
        }

    }

    function save_user_to_proj() {
        $ids = $_POST['user_projects'];
        $this->db->delete($this->tuser_projects, array('projectid' => $this->projectid));
        foreach($ids as $id ) {
            $data = array('projectid' => $this->projectid, 'user_id' => $id );
            $this->db->insert( $this->tuser_projects, $data);
        }

    }

    function get_all_projects( $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'    => 'result_array',
				
        );
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        $this->_prep_query();
        return $this->db->get( $this->tprojects )->{$params['resulttype']}();
    }

    function get_all_projectdomains($projectid, $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'            => 'result_array',
        );
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        if($projectid != -1 && $projectid != '') $this->db->where('projects.projectid', $projectid);
        $this->db->join('domains', 'domains.project_id = projects.projectid');
        $this->db->join('account_type', 'account_type.type_id = domains.type', 'left');
        return $this->db->get('projects')->{$params['resulttype']}();
    }

    function get_project_by_id( $projectid, $params = array()) {
        $default = array(
                'resulttype'	=> 'result_array',
        );
        $projectid = intval($projectid);
        if(!empty($projectid)) {
            $this->db->where('projectid', $projectid);
            $params = array_merge( $default, $params );
            $this->_prep_query();
            return $this->db->get( $this->tprojects )->{$params['resulttype']}();
        }
        return FALSE;
    }

    function get_user_projects($projectid, $params = array()) {
        $default = array(
                'resulttype'	=> 'result_array',
        );
        $this->db->where('projectid', $projectid);
        $this->_prep_query();
        $params = array_merge( $default, $params );
        return $this->db->get( $this->tuser_projects )->{$params['resulttype']}();
    }
	
	function get_projectlist_by_id($ids) {
		if ( is_array($ids) && count($ids) > 0) {
			foreach($ids as $id) {
				$this->db->or_where('projectid', $id);
			}		
			return $this->db->get('projects')->result_array();	
		}
		return false;
	}
	
	function delete_user_project($params = array()) {
		if (!empty($params['user_id']) && !empty($params['projectid']) ) {
			$projectid = $params['projectid'];
			$user_id = $params['user_id'];
			
			// delete user project
			$this->db->delete($this->tuser_projects, array('user_id' => $params['user_id'], 'projectid' => $params['projectid']));
			$sql = "SELECT * FROM user_domains
			INNER JOIN domains ON domains.domain_id = user_domains.domain_id
			WHERE domains.project_id  = $projectid
			AND user_domains.user_id = $user_id ";
			$rs = $this->db->query($sql);
			$domains = array();
			foreach ($rs->result_array() as $row){
				$domains[]['domain_id'] = $row['domain_id'];			
			}
			// remove each domains that belong on that project
			foreach($domains as $v) {
				$this->db->delete('user_domains', array('domain_id' => $v['domain_id'], 'user_id' => $user_id) );
			}			
		}
		return TRUE;
	}
	
	
	
}

