<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class mdl_settings extends Model
{
	
	var $where;
	var $projectid;
	
	function __construct(){
		parent::Model();
		$this->table1 = 'sitesettings';
	}
	
	function save() {
            $data = array(
                'use_captcha'                       => $this->input->post( 'use_captcha' ) == 'yes' ? 'yes' : 'no' ,
                'account_expired_message'           => $this->input->post( 'account_expired_message' ),
                'admin_email'                       => $this->input->post( 'admin_email' ),
            );

            if (!empty($data['use_captcha'])) {
                $this->db->where('key','use_captcha');
                $tmp = array('key' => 'use_captcha', 'value' => $data['use_captcha'] );
                $this->db->update($this->table1, $tmp);
            }
            
            if (!empty($data['account_expired_message'])) {
                $this->db->where('key','account_expired_message');
                $tmp = array('key' => 'account_expired_message', 'value' => $data['account_expired_message'] );
                $this->db->update($this->table1, $tmp);
            }

            if (!empty($data['admin_email'])) {
                $this->db->where('key','admin_email');
                $tmp = array('key' => 'admin_email', 'value' => $data['admin_email'] );
                $this->db->update($this->table1, $tmp);
            }
            


	}
	
	function _prep_query() {
		if ($this->where) $this->db->where($this->where);
	}

	function get_all_settings( $params = array()) {
		$default = array(
			'rows'			=> '10',
			'offset'		=> '',
			'resulttype'	=> 'result_array',
		);
		$params = array_merge( $default, $params );		
		!empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
		$this->_prep_query();
		return $this->db->get( $this->table1 )->{$params['resulttype']}();
	}

        function to_array($result) {
            $settings = array() ;
            foreach ($result as $row)
            {
                    $settings[$row['key']] = $row['value'] ;
            }
            return $settings ;

        }

	function get_project_by_id( $projectid, $params = array()) {
		$default = array(
			'resulttype'	=> 'result_array',
		);
		$projectid = intval($projectid);
		if(!empty($projectid)){
			$this->db->where('projectid', $projectid);
			$params = array_merge( $default, $params );		
			$this->_prep_query();
			return $this->db->get( $this->table1 )->{$params['resulttype']}();
		}
		return FALSE;
	} 
	
	function get_user_projects($projectid, $params = array()){
            $default = array(
                'resulttype'	=> 'result_array',
            );
            $this->db->where('projectid', $projectid);
            $this->_prep_query();
            $params = array_merge( $default, $params );
            return $this->db->get( $this->table2 )->{$params['resulttype']}();
        }
}

