<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class mdl_customfields extends Model
{
	
	var $where;
	var $projectid;
	
	function __construct(){
		parent::Model();
		$this->table1 = 'customfield';
                $this->table2 = 'domains';
                $this->fieldprefix = 'c_';
	}
	
	function add_field($params = array()) {
		$default = array(
			'fieldtype'	=> 'text',
			'fieldname'	=> 'test',
			'tablename'     => 'test',
		);
		$params = array_merge( $default, $params );
		$tablename = $params['tablename'];
                $fieldname = $this->fieldprefix . $params['fieldname'];

                if ( ! $this->db->field_exists($fieldname, $tablename)) {
                    $sql = "ALTER TABLE `$tablename`
                            ADD `$fieldname` varchar(255);";
                    $rs = $this->db->query($sql);
                    if ($rs) return TRUE;
                    return FALSE;
                } 


	}

        function delete_field($params = array()) {
            $default = array(
                    'fieldtype'	=> 'text',
                    'fieldname'	=> 'test',
                    'tablename'     => 'test',
            );
            $params = array_merge( $default, $params );
            $tablename = $params['tablename'];
            $fieldname = $params['fieldname'];
            if ( $this->db->field_exists($fieldname, $tablename)) {
                $sql = "ALTER TABLE `$tablename`
                    DROP COLUMN `$fieldname` ";
                $rs = $this->db->query($sql);
                if ($rs) return TRUE;
                return FALSE;
            }
        }

	function _prep_query() {
		if ($this->where) $this->db->where($this->where);
	}

        function list_customfields( $table) {
            $results = array();
            $rs = $this->db->field_data($table);
            foreach($rs as $v) {
                if (strstr($v->name, $this->fieldprefix )) $results[] = $v;
            }
            return $results;
        }

}

