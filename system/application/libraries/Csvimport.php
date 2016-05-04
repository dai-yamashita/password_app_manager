<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * cooked by armando ortega
*/
class Csvimport {
    private $fp;
    private $parse_header;
    private $header;
    private $delimiter;
    private $enclosure;
    private $length;
    //--------------------------------------------------------------------

    function Csvimport( ) {
        # code...
		ini_set('memory_limit', '250M');
    }

    function init($file_name, $parse_header=false, $delimiter=",", $enclosure = '"' ) {		
        $this->fp               = fopen($file_name, "r");
        $this->parse_header     = $parse_header;
        $this->delimiter 		= $delimiter;
        $this->enclosure 		= $enclosure;
        $this->length			= 0;
		$this->delimiter  		= str_replace('\\r', "\x0D", $delimiter);
		$this->delimiter  		= str_replace('\\n', "\x0A", $delimiter);
		$this->delimiter  		= str_replace('\\t', "\x09", $delimiter);		
        if ($this->parse_header) {
            $this->header = fgetcsv($this->fp, $this->length, $this->delimiter, $this->enclosure);
        }
		#echo "f=$file_name";
		#pre($this->header);
    }

    //--------------------------------------------------------------------------

    function __destruct() {
        if ($this->fp) {
            fclose($this->fp);
        }
    }

    function getheaders() {
        return $this->header;
    }
    // -------------------------------------------------------------------------

    function get( $num_of_lines = 0, $use_index = TRUE ) {
        if ( !$num_of_lines ) $num_of_lines = $this->length;
        if ( $use_index == TRUE ) $this->header = (is_array($this->header) ? array_keys($this->header) : array() ) ;
        $linenum = 0;
        $tmpdata = array();
		
        //loop through one row at a time
        if ($this->parse_header) {		
            while (($rows = fgetcsv($this->fp, $this->length, $this->delimiter, $this->enclosure)) !== FALSE) { #pre($rows) ;
                //if ($linenum >= $num_of_lines) break;
                if ( count($rows) > 1 ) { // escape ta sa empty lines
                    set_time_limit(0);					
                    $tmpdata[$linenum++] = array_combine( $this->header, $rows);
                }
            }
        }
        else {
            while (($rows = fgetcsv($this->fp, $this->length, $this->delimiter, $this->enclosure )) !== FALSE) {
                //if ($linenum >= $num_of_lines) break;
                if ( count($rows) > 1 ) {
                    set_time_limit(0);
                    $tmpdata[$linenum++] = $rows ;
                }
            }
        }
		#pre($tmpdata);
        return $tmpdata ;
    }

    // -------------------------------------------------------------------------
    function array_to_csv( $arraydata, $delimiter = ",", $enclosure = '"', $linebreak = "\n" ) {
        if (!is_array($arraydata)) {
            trigger_error('You must submit a valid array of csv data', E_USER_ERROR);
        }

        $delimiter  = str_replace('\\r', "\x0D", $delimiter);
        $delimiter  = str_replace('\\n', "\x0A", $delimiter);
        $delimiter  = str_replace('\\t', "\x09", $delimiter);

        $innerSeparator = $enclosure.$enclosure;
        $fieldSeparator = $enclosure. $delimiter . $enclosure; //","
        $rowSeparator = $enclosure. $linebreak . $enclosure;   //"\n"
        $csv=array();

        // Format the header line
        $csv[] = join($fieldSeparator, array_keys($arraydata[0]));

        foreach ($arraydata as $row) {
            foreach($row as $k=>$v) {
                $row[$k]=str_replace($enclosure, $innerSeparator, $v);
            }
            $csv[] = join($fieldSeparator, array_values($row));
        }

        return $enclosure . join($rowSeparator, $csv) . $enclosure;

    }

}    
