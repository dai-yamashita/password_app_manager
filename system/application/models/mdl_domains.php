<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class mdl_domains extends Model {

    var $where;
  var $order_by;
    var $domain_id;

    function __construct() {
        parent::Model();
        $this->tdomains['fields'] = array();
        $this->tdomains = 'domains';
        $this->tuser_domains = 'user_domains';
        $this->tpersonal_domains = 'personal_domains';
        $this->tuser_personal_domains = 'user_personal_domains';

    }

    function save($params = array()) {
        $default = array(
                'tablename'		=> $this->tdomains ,
        );
        $params = array_merge( $default, $params );
        $data = array(
                'project_id'            => $this->input->post( 'domainname' ),
                'type' 			=> $this->input->post( 'type' ),
                'templateid'            => $this->input->post( 'templateid' ),
                'importance'            => $this->input->post( 'importance' ),
                'url' 			=> prep_url($this->input->post( 'url' )),
                'loginurl' 		=> prep_url($this->input->post( 'loginurl' )),
                'username' 		=> $this->input->post( 'username' ),
                'password' 		=> $this->input->post( 'password' ),
                'pwlength' 		=> $this->input->post( 'pwlength' ),
                'changefreq'            => $this->input->post( 'changefreq' ),
                'mark' 			=> $this->input->post( 'mark' ),
                'notes' 		=> $this->input->post( 'notes' ),
        );

        $customfields = $this->mdl_customfields->list_customfields($params['tablename']);
        #echo '<pre>';
        #print_r($customfields);
        #print_r($_POST);
        foreach($customfields as $k => $v) {
            if (in_array($v->name , array_keys($_POST) )) {
                $data[$v->name] = $_POST[$v->name];
            }
        }
        #foreach($customfields as $v)
        // templateid -100 means it is a custom-template made by you
        if ($_POST['templateid'] == -100 ) {
            $data['customtemplate'] = trim($_POST['customtemplate']);
        }

        $timezone = $this->sitesettings->get_settings('timezone');
        $isdaylightsaving = $this->sitesettings->get_settings('isdaylightsaving');
        if (!empty($this->domain_id)) {
            $data['last_modified'] = gmt_to_local( now(), $timezone, $isdaylightsaving);
            #$data['last_modified'] = now();
            $this->db->where('domain_id', $this->domain_id);
            $rs = $this->db->get($params['tablename']);
            if ($rs->num_rows() > 0 ) {
                $rs = $rs->result_array();
                $changefreq = $rs[0]['changefreq'] ;
                // e-extend ang expiry date to specific time
                $this->alerts->recalculate_days(array('domain_id' => $this->domain_id, 'addtime' => $changefreq, 'tablename' => $params['tablename'] ));
            }
            $this->db->where('domain_id', $this->domain_id);
            $this->db->update($params['tablename'], $data);
        } else {
            $data['created'] = gmt_to_local(now(), $timezone, $isdaylightsaving);
            $this->db->insert($params['tablename'], $data);
            $this->domain_id = $this->db->insert_id();
            $this->alerts->recalculate_days(array('domain_id' => $this->domain_id, 'addtime' => $data['changefreq'], 'tablename' => $params['tablename'] ));
        }

    }

    function _prep_query() {
        if (isset($this->where)) $this->db->where($this->where);
    if ($this->order_by) $this->db->order_by($this->order_by);
    }

    /**
     * 1 domain saved to many users
     * domain_id=1 is assigned to 5 users
     */
    function save_user_domains() {
        $ids = $this->input->post( 'user_domains' );
        $ids = !empty($ids) ? $ids : array();
        $gidlist    = $this->input->post( 'gidlist' );
        $tmpgidlist = explode(',', $gidlist) ;
        // delete previous user ids
        if ( !empty($tmpgidlist)) {
            foreach($tmpgidlist as $tmpuserid) {
                $this->db->delete( $this->tuser_domains, array('user_id' => $tmpuserid, 'domain_id' => $this->domain_id));
            }
        }
        // save only katong na.check nga domains
        $tmpgid = array_intersect($tmpgidlist, $ids);
        if (count($tmpgid) > 0) {
            foreach($tmpgid as $id ) {
                $data = array('domain_id' => $this->domain_id, 'user_id' => $id );
                $this->db->insert('user_domains', $data);
            }
        }
    }

    function save_personaldomain($params = array()) {
        $default = array(
                'user_id'		=> '',
                'domain_id'		=> '',
        );
        $params = array_merge( $default, $params );
        $rs = $this->db->get_where($this->tuser_personal_domains, array('user_id' => $params['user_id'], 'domain_id' => $params['domain_id']));
        if ($rs->num_rows() == 0) {
            $data = array('domain_id' => $params['domain_id'], 'user_id' => $params['user_id']);
            $this->db->insert( $this->tuser_personal_domains, $data);
        }
    }

    function save_user_domainaccess() {
        $ids        = $this->input->post( 'chk' );
        $userid     = $this->input->post( 'userid' );
        $gidlist    = $this->input->post( 'gidlist' );
        $tmpgidlist = explode(',', $gidlist) ;
        // delete previous domain-ids only the selected list
        if ( !empty($tmpgidlist)) {
            foreach($tmpgidlist as $id) {
                $this->db->delete('user_domains', array('domain_id' => $id, 'user_id' => $userid));
            }
        }
        // after delete, re.insert the submitted list
        if ($ids && !empty($userid)) {
            foreach($ids as $id ) {
                $data = array('domain_id' => $id, 'user_id' => $userid );
                $this->db->insert( 'user_domains', $data);
            }
        }
    }

    function save_group_domains() {
        $ids        = $this->input->post( 'chk' );
        $ids        = !empty($ids) ? $ids : array();
        $deptid     = $this->input->post( 'gid' );
        $gidlist    = $this->input->post( 'gidlist' );
        $tmpgidlist = explode(',', $gidlist) ;

        // delete previous domain-ids
        if ( !empty($tmpgidlist)) {
            foreach($tmpgidlist as $id) {
                $this->db->delete('group_domains', array('domain_id' => $id, 'deptid' => $deptid ));
            }
        }
        // after delet, insert balik ang domainid
        if ($ids && !empty($deptid)) {
            foreach($ids as $id ) {
                $data = array('domain_id' => $id, 'deptid' => $deptid );
                $this->db->insert( 'group_domains', $data);
            }
        }
        $users = array();
        // get all users belong sa kani nga group
        $rs = $this->mdl_users->get_all_users_by_group($deptid);
        foreach($rs as $k => $v) {
            $users[] = $v['id'];
        }

        #print_r($tmpgidlist);
        // delete old userdomains
        foreach($tmpgidlist as $id ) {
            foreach($users as $k => $u) {
                $this->db->delete('user_domains', array('user_id' => $u, 'domain_id' => $id ));
            }
        }
        // save only katong na.check nga domains
        $tmpgid = array_intersect($tmpgidlist, $ids);
        foreach($tmpgid as $id ) {
            foreach($users as $k => $u) {
                $rs = $this->db->get_where('user_domains', array('domain_id' => $id, 'user_id' => $u ));
                if ($rs->num_rows() == 0) {
                    $data = array('domain_id' => $id, 'user_id' => $u );
                    $this->db->insert('user_domains', $data);
                }
            }
        }

    }

    function save_group_projects() {
        $ids        = $this->input->post( 'chk' );
        $ids        = !empty($ids) ? $ids : array();
        $gid        = $this->input->post( 'gid' );
        $gidlist    = $this->input->post( 'gidlist' );
        $tmpgidlist = explode(',', $gidlist) ;

        // delete previous domain-ids
        if ( !empty($tmpgidlist)) {
            foreach($tmpgidlist as $id) {
                $this->db->delete('group_projects', array('deptid' => $gid, 'projectid' => $id ));
            }
        }
        // after delet, insert balik ang domainid
        if ($ids && !empty($gid)) {
            //$this->db->delete('group_projects', array('deptid' => $gid));
            foreach($ids as $id ) {
                $data = array('projectid' => $id, 'deptid' => $gid );
                $this->db->insert( 'group_projects', $data);
            }
        }

    // populate also the domains belong in the project to the user.

    }

    function get_all_domains( $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'            => 'result_array',
                'field'                 => 'domains.created',
                'sort'                  => 'desc',
        );
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        !empty($params['field'] ) ? $this->db->order_by($params['field'], $params['sort']) : $this->db->order_by('domains.created', 'desc') ;

        $this->_prep_query();
        $this->db->join('account_type', 'account_type.type_id = domains.type', 'left');
        $this->db->join('projects', 'projects.projectid = domains.project_id');
        return $this->db->get( 'domains' )->{$params['resulttype']}();
    }



    function get_all_userdomains( $userid, $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'            => 'result_array',
                'domain_id'             => '',
                'field'                 => 'domains.created',
                'sort'                  => 'desc',
        );
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        !empty($params['field'] ) ? $this->db->order_by($params['field'], $params['sort']) : $this->db->order_by('domains.created', 'desc');
        $this->_prep_query();
        if($userid != -1) $this->db->where('user_domains.user_id', $userid);
        !empty($params['domain_id'] ) ? $this->db->where('user_domains.domain_id', $params['domain_id']) : '';
        $this->db->join('account_type', 'account_type.type_id = domains.type');
        $this->db->join('projects', 'projects.projectid = domains.project_id');
        if($userid != -1) $this->db->join('user_domains', 'user_domains.domain_id = domains.domain_id');


    return $this->db->get( 'domains' )->{$params['resulttype']}();
    }


    function get_all_personaldomains( $userid, $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'            => 'result_array',
                'domain_id'             => '',
        );
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        !empty($params['field'] ) ? $this->db->order_by($params['field'], $params['sort']) : $this->db->order_by('personal_domains.created', 'desc') ;

        $this->_prep_query();
        if($userid != -1) $this->db->where('user_personal_domains.user_id', $userid);
        !empty($params['domain_id'] ) ? $this->db->where('user_personal_domains.domain_id', $params['domain_id']) : '';
        $this->db->join('account_type', 'account_type.type_id = personal_domains.type', 'left');
        $this->db->join('projects', 'projects.projectid = personal_domains.project_id', 'left');
        if($userid != -1) $this->db->join('user_personal_domains', 'user_personal_domains.domain_id = personal_domains.domain_id');
        return $this->db->get($this->tpersonal_domains)->{$params['resulttype']}();
    }

    function get_all_groupdomains( $gid, $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'            => 'result_array',
                'field'                 => '',
                'sort'                  => 'desc',
                ) ;
        if($gid != -1 && $gid != '') $this->db->where('group_domains.deptid', $gid);
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        !empty($params['field'] ) ? $this->db->order_by($params['field'], $params['sort']) : $this->db->order_by('department.deptid', 'desc') ;
        $this->_prep_query();
        $this->db->join('department', 'department.deptid = group_domains.deptid');
        return $this->db->get( 'group_domains' )->{$params['resulttype']}();
    }

    function get_all_groupprojects( $gid, $params = array()) {
        $default = array(
                'rows'			=> '',
                'offset'		=> '',
                'resulttype'            => 'result_array',
                'field'                 => '',
                'sort'                  => 'desc',
                ) ;
        if($gid != -1 && $gid != '') $this->db->where('group_projects.deptid', $gid);
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        !empty($params['field'] ) ? $this->db->order_by($params['field'], $params['sort']) : $this->db->order_by('projects.projectid', 'desc') ;
        $this->_prep_query();
        $this->db->join('projects', 'group_projects.projectid = projects.projectid');
        return $this->db->get( 'group_projects' )->{$params['resulttype']}();
    }

  function get_all_userprojects_old( $uid, $params = array()) {
        $default = array(
                'rows'					=> '',
                'offset'				=> '',
                'resulttype'            => 'result_array',
                'field'                 => '',
                'sort'                  => 'desc',
                ) ;
        if($uid != -1 && $uid != '') $this->db->where('user_projects.user_id', $uid);
        $params = array_merge( $default, $params );
        !empty($params['rows'] ) ? $this->db->limit($params['rows'], $params['offset'])  : '' ;
        !empty($params['field'] ) ? $this->db->order_by($params['field'], $params['sort']) : $this->db->order_by('projects.projectid', 'desc') ;
        $this->_prep_query();
        $this->db->join('projects', 'user_projects.projectid = projects.projectid');
        return $this->db->get( 'user_projects' )->{$params['resulttype']}();
    }

  function get_all_userprojects( $uid, $params = array()) {
    $default = array(
      'rows'					=> '',
      'offset'				=> '',
      'resulttype'            => 'result_array',
      'field'                 => '',
      'sort'                  => 'desc',
      ) ;
    $params = array_merge( $default, $params );

    $s2 = $s3 = '';
    if($uid != -1) {
      $s2 = " WHERE user_domains.user_id = $uid ";
    }

    if(!empty($params['domain_id'])) {
      $s3 = " AND user_domains.domain_id = " . $params['domain_id'];
    }


    $sql = "
    SELECT
    user_domains.user_domain_id,
    user_domains.user_id,
    projects.*,
    account_type.acctype,
    domains.*
    FROM
    domains
    Inner Join user_domains ON user_domains.domain_id = domains.domain_id
    Inner Join user_projects ON user_projects.projectid = domains.project_id
    Inner Join projects ON projects.projectid = user_projects.projectid
    Inner Join account_type ON account_type.type_id = domains.`type`
    $s2
    $s3
    GROUP BY
    domains.project_id
    ORDER BY projects.project ASC
    ";
    $rs = $this->db->query($sql);
    return $rs->{$params['resulttype']}();
  }

    function get_domainid() {
        return $this->domain_id ;
    }

    function to_array($data, $key) {
        $result = array();
        foreach($data as $v ) {
            $result[] = $v[$key];
        }
        return $result;
    }

    function get_domain_by_id( $domain_id, $params = array()) {
        $default = array(
                'resulttype'	=> 'result_array',
        );
        $domain_id = intval($domain_id);
        if(!empty($domain_id)) {
            $this->db->where('domains.domain_id', $domain_id);
            $params = array_merge( $default, $params );
            $this->_prep_query();
            $this->db->join('account_type', 'account_type.type_id = domains.type');
            $this->db->join('projects', 'projects.projectid = domains.project_id');
            $result = $this->db->get( $this->tdomains )->{$params['resulttype']}();
            print_r($this->db->last_query());
            return $result;
        }
        return FALSE;
    }

    function get_personaldomain_by_id( $domain_id, $params = array()) {
        $default = array(
                'resulttype'	=> 'result_array',
        );
        $domain_id = intval($domain_id);
        if(!empty($domain_id)) {
            $this->db->where('personal_domains.domain_id', $domain_id);
            $params = array_merge( $default, $params );
            $this->_prep_query();
            $this->db->join('account_type', 'account_type.type_id = personal_domains.type');
            $this->db->join('projects', 'projects.projectid = personal_domains.project_id');
            return $this->db->get($this->tpersonal_domains)->{$params['resulttype']}();
        }
        return FALSE;
    }

    function get_user_projects($projectid, $params = array()) {
        $default = array(
                'resulttype'	=> 'result_array',
        );
        $this->db->where('domain_id', $projectid);
        $this->_prep_query();
        $params = array_merge( $default, $params );
        return $this->db->get( $this->tuser_domains )->{$params['resulttype']}();
    }

    function get_domain_customtemplate($domainid, $params = array()) {
        $default = array(
                'resulttype'	=> 'row_array',
        );
        $params = array_merge( $default, $params );
        $this->db->select('customtemplate');
        $this->db->where('domain_id', $domainid);
        return $this->db->get( $this->tdomains )->{$params['resulttype']}();
    }

    function get_personaldomain_customtemplate($domainid, $params = array()) {
        $default = array(
                'resulttype'	=> 'row_array',
        );
        $params = array_merge( $default, $params );
        $this->db->select('customtemplate');
        $this->db->where('personal_domains.domain_id', $domainid);
        return $this->db->get($this->tpersonal_domains)->{$params['resulttype']}();
    }



}
