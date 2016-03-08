<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Hkalerts
{
    var $frequency ;
    var $ci;
	
    function __construct() {
        $this->ci =& get_instance();
        $this->frequency = array(
            'bi-annualy'    => '+ 730 days',
            'annually'      => '+ 365 days',
            'half-yearly'   => '+ 180 days',
            'quarterly'     => '+ 90 days',
            'monthly'       => '+ 30 days',
            'bi-weekly'     => '+ 14 days',
            'weekly'        => '+ 7 days',
            'daily'         => '+ 1 day',
            'hourly'        => 1
            );
        $this->table1 = 'domains';
        $this->table2 = 'alerts';
    }

	function test() {
		redirect('http://google.com');
	}
	
	
}	