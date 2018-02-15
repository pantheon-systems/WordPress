<?php if(!class_exists('WSAL_Rep_Plugin')){ exit('You are not allowed to view this page.'); }
class WSAL_Rep_Util_O extends WSAL_Models_Occurrence
{
    public function GetTableName(){
        return $this->getConnector()->getAdapter("Occurrence")->GetTable();
    }
}
