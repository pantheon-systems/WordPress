<?php

class WSAL_AS_Filters_IpFilter extends WSAL_AS_Filters_AbstractFilter {
    
    public function GetName(){
        return __('IP');
    }
    
    public function IsApplicable($query){
        
        $query = explode(':', $query);
        
        if(count($query) > 1){
            // maybe IPv6?
            
            // TODO do IPv6 validation
        }
        
        $query = explode('.', $query[0]);
        
        if(count($query) > 1){
            // maybe IPv4?
            foreach($query as $part)
                if(!is_numeric($part) || $part < 0 || $part > 255)
                    return false;
            return true;
        }
        
        return false; // all validations failed
    }
    
    public function GetPrefixes(){
        return array(
            'ip',
        );
    }
    
    public function GetWidgets(){
        $wgt = new WSAL_AS_Filters_AutoCompleteWidget($this, 'ip', 'IP');
        $wgt->SetDataLoader(array($this, 'GetMatchingIPs'));
        return array($wgt);
    }
    
    public function GetMatchingIPs(WSAL_AS_Filters_AutoCompleteWidget $wgt){
        $tmp = new WSAL_Models_Meta();
        $ips = $tmp->getAdapter()->GetMatchingIPs();
        foreach ($ips as $ip) {
            $wgt->Add($ip, $ip);
        }
    }
    
    public function ModifyQuery($query, $prefix, $value){
        $query->addMetaJoin();
        switch($prefix){
            case 'ip':
                $query->addCondition(
                        '( meta.name = "ClientIP" AND TRIM(BOTH "\"" FROM meta.value) = TRIM(BOTH "\"" FROM %s) )',
                        json_encode($value)
                    );
                break;
            default:
                throw new Exception('Unsupported filter "' . $prefix . '".');
        }
    }
}
