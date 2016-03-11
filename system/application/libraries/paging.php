<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Paging
{
	var $paging;

	function __construct() {
		
	}
	
	function get_paging_template() {
            $this->paging['first_link']	= 'First';
            $this->paging['first_tag_open']= '<li>';
            $this->paging['first_tag_close']= '</li>';

            $this->paging['next_link'] = 'Next &raquo;';
            $this->paging['next_tag_open'] = '<li class="next">';
            $this->paging['next_tag_close'] = '</li>';

            $this->paging['prev_link'] = '&laquo; Previous';
            $this->paging['prev_tag_open'] = '<li class="next">';
            $this->paging['prev_tag_close'] = '</li>';

            $this->paging['num_tag_open']		= '<li>';
            $this->paging['num_tag_close']	= '</li>';

            $this->paging['cur_tag_open']		= '<li class="active">';
            $this->paging['cur_tag_close']	= '</li>';

            $this->paging['last_link']	= 'Last &raquo;';
            $this->paging['last_tag_open']= '<li>';
            $this->paging['last_tag_close']= '</li>';
            return $this->paging;
	}
        
}

