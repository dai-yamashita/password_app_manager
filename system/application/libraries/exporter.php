<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Exporter {

    var $ci;
    public $params;
    
// <editor-fold defaultstate="collapsed" desc="construct ">
    function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->library('csvimport');
        $this->ci->load->dbutil();

    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" export_csv ">
    function export_csv($params = array()) {
        $options = array(
                'filename'          => 'exportdata.csv',
                'sql_query'         => '',
                ) ;
        $this->params = array_merge( $options, $params );
        $this->ci->load->library('csvimport');
        $this->ci->load->helper('download');
        $this->ci->load->dbutil();
        $delimiter = $this->ci->input->post('delimiter');
        $enclosure = $this->ci->input->post('enclosure');
        $rs = $this->ci->db->query($this->params['sql_query']);
        if ($rs->num_rows() > 0) {
            $data = $rs->result_array();
            $result_csv = $this->ci->csvimport->array_to_csv($data, $delimiter, $enclosure);
            force_download( strtolower($this->params['filename']), $result_csv);
            exit;
        } else {
            $this->ci->session->set_flashdata('flash', 'emptydata');
            header('location:' . $_SERVER['HTTP_REFERER']);
        }

    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" export_xml ">
    function export_xml( $params=array()) {
        $this->ci->load->helper('download');
        $this->ci->load->dbutil();
        $options = array(
                'filename'	=> 'exportdata.xml',
                'sql_query'     => '',
                );
        $this->params = array_merge( $options, $params );
        $rs = $this->ci->db->query($this->params['sql_query']);
        if ($rs->num_rows() > 0) {
            $data = $rs->result_array();
            $xmlconfig = array (
                    'root'      => 'root',
                    'element'   => 'element',
                    'newline'   => "\n",
                    'tab'       => "\t"
            );
            $out = $this->ci->dbutil->xml_from_result($rs, $xmlconfig);
            force_download( strtolower($this->params['filename']), $out);
            exit;
        } else{
            $this->ci->session->set_flashdata('flash', 'emptydata');
            header('location:' . $_SERVER['HTTP_REFERER']);
        }

    }
// </editor-fold>


}