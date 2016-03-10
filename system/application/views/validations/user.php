<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
#$this->form_validation->set_rules('password', 'password', 'required|trim');
$this->form_validation->set_rules('pwlength', 'pwlength', 'trim');
$this->form_validation->set_rules('firstname', 'firstname', 'required|trim|max_length[254]');
$this->form_validation->set_rules('lastname', 'lastname', 'required|trim|max_length[254]');
$this->form_validation->set_rules('position', 'position', 'trim|max_length[254]');
$this->form_validation->set_rules('email', 'email', 'required|trim|valid_email');
$this->form_validation->set_rules('skypeid', 'skype', 'trim|max_length[254]');
#$this->form_validation->set_rules('deptid', 'department', 'required|trim');

