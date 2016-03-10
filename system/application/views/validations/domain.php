<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->form_validation->set_rules('domainname', 'domainname', 'required|trim');
$this->form_validation->set_rules('type', 'type', 'required|trim');

$this->form_validation->set_rules('password', 'password', 'required|trim|max_length[254]');

$this->form_validation->set_rules('url', 'url', 'trim');
$this->form_validation->set_rules('importance', 'importance', 'required|trim');
$this->form_validation->set_rules('pwlength', 'pwlength', 'trim');
$this->form_validation->set_rules('changefreq', 'change frequency', 'trim');
$this->form_validation->set_rules('mark', 'mark', 'trim');
$this->form_validation->set_rules('notes', 'notes', 'trim');


