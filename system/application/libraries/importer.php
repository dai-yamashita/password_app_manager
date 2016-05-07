<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Importer {
    var $ci;
    public $params;

// <editor-fold defaultstate="collapsed" desc="construct ">
    function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->library('csvimport');
        $this->ci->load->dbutil();
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" import_csv ">
    function import_csv($params = array()) {
        $options = array(
                'filename'	=> 'importdata.csv',
                'tablename'	=> 'users',
                'primary_key'   => 'id',
                ) ;
        $this->params = array_merge( $options, $params );
        $delimiter = $this->ci->input->post('delimiter');
        $enclosure = $this->ci->input->post('enclosure');
        $this->ci->csvimport->init( $this->params['filename'], TRUE, $delimiter, $enclosure );
        $h = $this->ci->csvimport->getheaders();
        $rows = $this->ci->csvimport->get('', FALSE);
        $fields = $this->ci->db->list_fields($this->params['tablename']);
        #$fields = array_values($fields);
        $totalrows = count($rows);
        $u = array();
        if ($totalrows > 0) {
            foreach($h as $k => $v) {
                if ( in_array( $v, array_values($fields)) ) {
                    $u[] = $v;
                }
            }
            // start saving the data to DB
            for ($j = 0; $j < $totalrows; $j++) {
                foreach($u as $v => $value ) {
                    $rsdata[$v] = $rows[$j][$v];
                }
                unset($rsdata[$this->params['primary_key']]);// id is not important so remove it
                $this->ci->db->insert($this->params['tablename'], $rsdata);
                unset($rsdata);
            }
            return $totalrows;
        }
        return FALSE;
    }
    
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" import_xml ">
    function import_xml($params = array()) {
        $options = array(
                'filename'	=> 'importdata.xml',
                'tablename'	=> '',
                'primary_key'   => 'id',
                ) ;
        $this->params = array_merge( $options, $params );
        $this->ci->load->library('simplexml');
        $xml = file_get_contents($this->params['filename']);
        if ($xml) {
            $xmldata = $this->ci->simplexml->xml_parse($xml);
            // get the first element from xml
            $xmldata = $xmldata[key($xmldata)];
            $totalrows = count($xmldata);
            if ($totalrows > 0) {
                $fields = $this->ci->db->list_fields($this->params['tablename']);
                $elems = array_keys($xmldata[0]);
                #print_r($elems);
                // check the validity of the table field to the xmlfield
                foreach($elems as $k => $v) {
                    if ( in_array( $v, array_values($fields)) ) {
                        $u[] = $v;
                    }
                }

                for($j=0; $j < $totalrows; $j++) {
                    foreach($u as $v) {
                        $rsdata[$v] = $xmldata[$j][$v];
                    }
                    unset($rsdata[$this->params['primary_key']]);// id is not important so remove it
                    $this->ci->db->insert($this->params['tablename'], $rsdata);
                }
            }
            return $totalrows;
        }
        return FALSE;
    }

// </editor-fold>






}