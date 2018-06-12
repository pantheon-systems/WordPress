<?php

namespace Qiigo\Plugin\Integration\CRM {
	class ExtraView extends Base {
		public static function Submit($data, $loc_data) {
			$post_data = array(
				// CRM specific
				'user_id' => get_option('ev_username'),
				'password' => get_option('ev_password'),
				'statevar' => 'custom',
				'ev_lead_action' => $data['action'],
				// Location Specific
				'office' =>  urldecode($loc_data['office']),
				'master_country' => 'US',
				// Submission Specific
				'lead_source' => 'Website '. urldecode($data['lead_source']),
				'email' =>  urldecode($data['email']),
				'name' => ((isset($data['name'])) ? $data['name'] : urldecode($data['first_name']).' '.urldecode($data['last_name'])),
				'telephone' =>  urldecode($data['phone']),
				'addr_city' => urldecode($data['city']),
				'addr_state' =>  urldecode($data['state']),
				'addr_postal_code' =>  urldecode($data['zip'])
			);
			
			if( isset($data['email_opt_in']) && trim($data['email_opt_in']) != '' )
				$post_data['email_opt_in'] = 'Y';
			
			if( isset($data['lead_estimate_date_preferred']) && trim($data['lead_estimate_date_preferred']) != '')
				$post_data['lead_estimate_date_preferred'] = $data['lead_estimate_date_preferred'];

			$site = get_option('ev_site');
			// $site = 'jan-pro';
			$url = $site.'/ExtraView/ev_api.action?';
			$first = true;

			foreach($post_data as $name => $value) {
				if( !$first )
					$url .= '&';
				
				$url .= urlencode($name).'='.urlencode($value);
				$first = false;
			}
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$headers = array();
			$headers[] = 'User-Agent: PHP cURL';

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$content .= curl_exec($ch);
			curl_close($ch);
			
			echo "\n\nExtraView\n";
			echo "Posted Data:\n";
			print_r($post_data);
			
			//ID 28177954 added.
			if( preg_match('/ID(\s+)([0-9]+)(\s+)added/i', $content) != 1 ) {
				static::ReportError('ExtraView', $content);
			}
			
			file_put_contents('test.txt', $test);
			
			echo "\nReturn:\n".$content;
		}
	}
}
