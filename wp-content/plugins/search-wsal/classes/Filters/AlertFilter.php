<?php

class WSAL_AS_Filters_AlertFilter extends WSAL_AS_Filters_AbstractFilter {
	
	public function GetName(){
		return __('Alert');
	}
	
	public function IsApplicable($query){
		return strtolower(substr(trim($query), 0, 5)) == 'alert';
	}
	
	public function GetPrefixes(){
		return array(
			'alert',
		);
	}
	
	public function GetWidgets(){
		$wgt = new WSAL_AS_Filters_SingleSelectWidget($this, 'alert', 'Alert');
		foreach (WpSecurityAuditLog::GetInstance()->alerts->GetCategorizedAlerts() as $catg => $alerts){
			$grp = $wgt->AddGroup($catg);
			foreach ($alerts as $alert)
				$grp->Add(str_pad($alert->type, 4, '0', STR_PAD_LEFT) . ' - ' . $alert->desc, $alert->type);
		}
		return array($wgt);
	}
	
	public function ModifyQuery($query, $prefix, $value){
		switch($prefix){
			case 'alert':
				$query->addCondition('alert_id = %s', $value);
				break;
			default:
				throw new Exception('Unsupported filter "' . $prefix . '".');
		}
	}
}
