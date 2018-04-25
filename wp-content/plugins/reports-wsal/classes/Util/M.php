<?php if(!class_exists('WSAL_Rep_Plugin')){ exit('You are not allowed to view this page.'); }
class WSAL_Rep_Util_M extends WSAL_Models_Meta{
    function GetTableName(){
    	return $this->getConnector()->getAdapter("Meta")->GetTable();
    }
}