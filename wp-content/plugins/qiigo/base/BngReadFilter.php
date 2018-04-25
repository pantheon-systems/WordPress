<?php 

require_once dirname(__FILE__) . '/Classes/PHPExcel/Reader/IReadFilter.php';


class BngReadFilter implements PHPExcel_Reader_IReadFilter {
    
    public function readCell($column, $row, $worksheetName = '') {
        
        if ($row >= 2 && $row <= 41) {
           if ( !in_array($column,range('A','C')) ) {
             return true;
           }
        }

        return false;
    }
}

?>