<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Alerts
{
    var $frequency ;
    var $ci;
    function __construct() {
        $this->ci =& get_instance();
        $this->frequency = array(
            'bi-annually'    => '+ 730 days',
            'annually'      => '+ 365 days',
            'half-yearly'   => '+ 180 days',
            'quarterly'     => '+ 90 days',
            'monthly'       => '+ 30 days',
            'bi-weekly'     => '+ 14 days',
            'weekly'        => '+ 7 days',
            'daily'         => '+ 1 day',
            'hourly'        => '+ 1 hour',
            );
        $this->tdomains = 'domains';
		$this->tpersonal_domains = 'personal_domains';
        $this->talerts = 'alerts';
        $this->logged_userid = $this->ci->dx_auth->get_user_id();
        $this->ci->load->helper('url');
    }

    function check_overdue_account($params=array()) {
        $default = array(
                'rows'		=> '10',
                'offset'	=> '',
                'resulttype'    => 'result_array',
                ''
        );
        $params = array_merge( $default, $params );
        $data = array();
$timezone = $this->ci->sitesettings->get_settings('timezone');
$isdaylightsaving = $this->ci->sitesettings->get_settings('isdaylightsaving');
$localtime = gmt_to_local( time(), $timezone, $isdaylightsaving );

        $currenttime = $localtime;
        foreach($this->frequency as $k => $v) {
            if ($k) {
                $this->ci->db->select('domain_id, changefreq, expirydate, last_modified');
                $this->ci->db->where('changefreq', $k);
                $rs = $this->ci->db->get($this->tdomains);
                if($rs->num_rows() > 0) {
                    $result = $rs->result_array();
                    $n = count($result);
                    for($j=0; $j<$n; $j++ ) {
                        $expirydate = $result[$j]['expirydate'];
                        $domain_id = $result[$j]['domain_id'];
                        $last_modified = $result[$j]['last_modified'];
                        #$a = date("Y-m-d g:i a", $currenttime);
                        #$b = date("Y-m-d g:i a", $expirydate);
                        #echo "c:$a exp:$b <br>";
                        if (($expirydate !== '' or $expirydate != 0) && $currenttime > $expirydate) {
                            # automatic create a new password for the domain access
                            // $this->regenerate_password()
                            # then send a notification to all allowed users/admin with a new password
                            $this->send_email_notification( array(
                                'expirydate' => date("M d, Y g:i a", $expirydate), 'domain_id' => $domain_id,
                                ));
                            #use this to recalculate the days
                            // $this->recalculate_days( array('domain_id' => $domain_id, 'addtime' => $v) );
                            $this->create_alert( array('expirydate' => date("M d, Y g:i a", $expirydate), 'domain_id' => $domain_id) );
                        }
                    }
                }
            }
        }
		
		$this->check_overdue_account_personal($params);
    }
	
    function check_overdue_account_personal($params=array()) {
        $default = array(
                'rows'		=> '10',
                'offset'	=> '',
                'resulttype'    => 'result_array',
        );
        $params = array_merge( $default, $params );
        $data = array();
		$timezone = $this->ci->sitesettings->get_settings('timezone');
		$isdaylightsaving = $this->ci->sitesettings->get_settings('isdaylightsaving');
		$localtime = gmt_to_local( time(), $timezone, $isdaylightsaving );
        $currenttime = $localtime;
        foreach($this->frequency as $k => $v) {
            if ($k) {
                $this->ci->db->select('domain_id, changefreq, expirydate, last_modified');
                $this->ci->db->where('changefreq', $k);
                $rs = $this->ci->db->get($this->tpersonal_domains);
                if($rs->num_rows() > 0) {
                    $result = $rs->result_array();
                    $n = count($result);
                    for($j=0; $j<$n; $j++ ) {
                        $expirydate = $result[$j]['expirydate'];
                        $domain_id = $result[$j]['domain_id'];
                        $last_modified = $result[$j]['last_modified'];
                        #$a = date("Y-m-d g:i a", $currenttime);
                        #$b = date("Y-m-d g:i a", $expirydate);
                        #echo "c:$a exp:$b <br>";
                        if (($currenttime > $expirydate) ) {
                            # automatic create a new password for the domain access
                            // $this->regenerate_password()
                            # then send a notification to all allowed users/admin with a new password
                            $this->send_email_notification( array(
                                'expirydate' => date("M d, Y g:i a", $expirydate), 'domain_id' => $domain_id, 'domaintype' => 'personal',
                                ) );
                            #use this to recalculate the days
                            // $this->recalculate_days( array('domain_id' => $domain_id, 'addtime' => $v) );
                            $this->create_alert( array('expirydate' => date("M d, Y g:i a", $expirydate), 'domain_id' => $domain_id, 'domaintype' => 'personal') );
                        }
                    }
                }
            }
        }
    }
	
    function create_alert($params = array()) {
        $default = array(
            'rows'				=> '10',
            'offset'            => '',
            'resulttype'        => 'result_array',
            'expirydate'        => '',
            'domain_id'         => '',
			'domaintype'        => '',
        );
        
        $params = array_merge( $default, $params );
        $this->ci->db->where( array('isread' => 0, 'domainid' => $params['domain_id']));
        $rs = $this->ci->db->get($this->talerts);
        if ($rs->num_rows() > 0) {
           // wlaa
        } else {
			$url = 'admin/domain';
			if ($params['domaintype'] == 'personal') {
				$url = 'admin/mydomain';
	            $msg = $this->ci->sitesettings->get_settings('account_expired_message');
	            $timezone = $this->ci->sitesettings->get_settings('timezone');
	            $isdaylightsaving = $this->ci->sitesettings->get_settings('isdaylightsaving');
	            $rs = $this->ci->mdl_domains->get_personaldomain_by_id($params['domain_id'], array('resulttype' => 'row_array')) ;            			
			} else {				
	            $msg = $this->ci->sitesettings->get_settings('account_expired_message');
	            $timezone = $this->ci->sitesettings->get_settings('timezone');
	            $isdaylightsaving = $this->ci->sitesettings->get_settings('isdaylightsaving');
	            $rs = $this->ci->mdl_domains->get_domain_by_id($params['domain_id'], array('resulttype' => 'row_array')) ;            			
			}
            $msg2 = "";
            $msg2 = "<br /> Domain: " . $rs['project'] . "<br />";
            $msg2 .= "Type: " . $rs['acctype'] . "<br />";
            $msg2 .= "URL: " . $rs['url'] . "<br />";
            $msg2 .= "Username: " . $rs['username'] . "<br />";
            $link = anchor("$url/form/{$params['domain_id']}", "{$rs['project']}" );
            $msg2 .= "<br /><br />Click this link to update the password: $link <br /><br />";
            $data = array(
                '{expirydate}'      => $params['expirydate'] ,
                '{domaindetails}'   => $msg2,
            );
            $msg = str_replace( array_keys($data), array_values($data), $msg );
            // send alert message to admins
            $this->ci->mdl_users->where = array('role_id' => 1);
            $rs = $this->ci->mdl_users->get_all_users();
            $n = count($rs);
            if ($n> 0) {
                for($j=0; $j < $n; $j++) {
                    $id = $rs[$j]['id'];
                    $data = array(
                        'title'         => 'Password expired',
                        'alert'         => $msg,
                        'created'       => gmt_to_local( time(), $timezone, $isdaylightsaving),
                        'isread'        => 0,
                        'to'            => $id,
                        'from'          => -1, // -1 denotes system bots
                        'domainid'      => $params['domain_id'],
                    );
                    #print_r($data);
                    $this->ci->db->insert($this->talerts, $data);
                }
            }
        }
    }
	
    function alert_add_to_project( ) {
		$ids = $this->ci->input->post( 'chk' );
		$uid = $this->ci->input->post( 'gid' );
		$rs2 = $this->ci->mdl_projects->get_projectlist_by_id($ids);
		$projectnames = '<none>';
		if ($rs2) {
			$projectnames = '<ul>';
			foreach ($rs2 as $v) {
				$projectnames .= '<li>' . $v['project'] . '</li>';
			}
			$projectnames .= '</ul>';
			$m = "You're project access has been updated. Below are the list of projects: <br>$projectnames <br>Thanks,<br>Trimorph Team";
			$timezone = $this->ci->sitesettings->get_settings('timezone');
			$isdaylightsaving = $this->ci->sitesettings->get_settings('isdaylightsaving');
			$localtime = gmt_to_local( time(), $timezone, $isdaylightsaving );		
			$d = array('title' => 'Your Project access is updated.', 'alert' => $m, 'to' => $uid, 'created' => $localtime, 'isread' => 0);
			$this->ci->db->insert('alerts', $d);   		
		}
     
    }

    function recalculate_days($params = array()) {
        $default = array(
            'domain_id'		=> '',
            'addtime'           => '',
            'tablename'         => $this->tdomains,
        );
        $params = array_merge( $default, $params );
        foreach($this->frequency as $k => $v) {
            if ($params['addtime'] == $k) {
                $addtime = $v;
                break;
            }
        }

        $domain_id = $params['domain_id'];
$timezone = $this->ci->sitesettings->get_settings('timezone');
$isdaylightsaving = $this->ci->sitesettings->get_settings('isdaylightsaving');
$currenttime = gmt_to_local(time(), $timezone, $isdaylightsaving );
        
        $time = date('Y-m-d g:i:s a', $currenttime);
        $expirydate = strtotime($time . " $addtime");

        $data = array('expirydate' => $expirydate);
        $this->ci->db->where('domain_id', $domain_id);
        $this->ci->db->update($params['tablename'], $data);
        
    }

    function send_email_notification($params = array()) {
        $msg = $this->ci->sitesettings->get_settings('account_expired_message');
        $admin_email = $this->ci->sitesettings->get_settings('admin_email');
        $noreply_email = $this->ci->sitesettings->get_settings('noreply_email');
		$url = 'admin/domain/';
		if (isset($params['domaintype']) && $params['domaintype'] == 'personal' ) {
			$url = 'admin/mydomain/';
			$rs = $this->ci->mdl_domains->get_personaldomain_by_id($params['domain_id'], array('resulttype' => 'row_array')) ;		
		} else {
			$rs = $this->ci->mdl_domains->get_domain_by_id($params['domain_id'], array('resulttype' => 'row_array')) ;		
		}
		#print_r($rs);
        $this->ci->db->where( array('isread' => 0, 'domainid' => $params['domain_id']));
        $rs2 = $this->ci->db->get($this->talerts);
        if ($rs2->num_rows() > 0) {
            // wala
        } else {
            $msg2 = "";
            $msg2 = "<br /> Domain: " . $rs['project'] . " <br /> ";
            $msg2 .= "Type: " . $rs['acctype'] . " <br /> ";
            $msg2 .= "URL: " . $rs['url'] . " <br /> ";
            $msg2 .= "Username: " . $rs['username'] . " <br /> ";
            $link = anchor("$url/form/{$params['domain_id']}", "'{$rs['project']}'");
            $msg2 .= " <br /><br />Click this link to update the password: $link <br /><br />";
            $data = array(
                '{expirydate}'      => $params['expirydate'],
                '{domaindetails}'   => $msg2,
            );

            

            $msg = str_replace( array_keys($data), array_values($data), $msg );
            /*$config['protocol'] = 'sendmail';
            $config['mailpath'] = '/usr/sbin/sendmail';*/
            $config['charset'] = 'utf-8';
            $config['wordwrap'] = TRUE;
            $config['mailtype'] = 'html';
            $this->ci->load->library('email');
            $this->ci->email->initialize($config);
            $this->ci->email->from($noreply_email, 'noreply');
            $this->ci->email->to($admin_email);
            $this->ci->email->subject('Password expired');
            $this->ci->email->message($msg);
            $this->ci->email->send();
            #echo $this->ci->email->print_debugger();            
        }

    }

    function sendmail() {
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;
        $this->email->initialize($config);
        $this->load->library('email');
        $this->email->from('your@example.com', 'Your Name');
        $this->email->to('someone@example.com');
        $this->email->cc('another@another-example.com');
        $this->email->bcc('them@their-example.com');
        $this->email->subject('Email Test');
        $this->email->message('Testing the email class.');
        $this->email->send();
        echo $this->email->print_debugger();
    }

    function check_unread_alerts() {

        #$this->ci->db->where( array('isread' => 0, 'to' => $this->logged_userid , 'isread1 !=' => 1 ) );
        #$this->ci->db->or_where( array('to' => -1));
        #$this->ci->db->get($this->talerts)->result_array();
		if ($this->ci->dx_auth->is_role(array('member')) ) {
			$sql = "SELECT * FROM $this->talerts where 1=1 AND";
			$sql .= "(`to` = $this->logged_userid) AND `isread` = 0";
			$sql .= " ORDER BY alerts.created DESC";	
		} else {
			$sql = "SELECT * FROM $this->talerts where 1=1 AND";
			$sql .= "( `to` = -1 OR `to` = $this->logged_userid) AND `isread` = 0";		
			$sql .= " ORDER BY alerts.created DESC";	
		}
        $rs = $this->ci->db->query($sql);
        if ($rs->num_rows() > 0){
            return $rs->result_array();
        }
        return FALSE;
    }

	function check_user_alerts($uid) {
		if ($this->ci->dx_auth->is_role(array('owner','administrator'))) {
			$this->ci->db->where('to', $uid );
			$this->ci->db->or_where('to', -1);
			$this->ci->db->order_by('created', 'asc');
	        $rs = $this->ci->db->get('alerts')->result_array();		
		} else {
			$this->ci->db->where('to', $uid );
			$this->ci->db->order_by('created', 'desc');
	        $rs = $this->ci->db->get('alerts')->result_array();		
		}
		return $rs;
	}
	
    function _check_domain() {

    }

}