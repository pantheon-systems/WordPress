<?php

namespace Qiigo\Plugin\Integration\CRM {
	class FranConnect extends Base {
		public static function Submit($data, $loc_data) {
			$post_data = array(
				// CRM specific
				'area' => '14',
				'submit' => 'Add+Lead',
				'cid' => 'MRWSSYNCLEAD',
				'skey' => get_option('fc_key'),
				//'skey' => 'janpro',
				// Submission Specific
				'leadSource' => 'Website',
				'comments' => $data['lead_source'],
				'email' => $data['email'],
				'firstName' => $data['first_name'],
				'lastName' => $data['last_name'],
				'phone' => $data['phone'],
				'city' => $data['city'],
				'state' => $data['state'],
				'zip' => $data['zip']
			);
			
			$host = 'lansing2.franconnect.net';
			$site = get_option('fc_site');
			// $site = 'janpro';
			$url = 'https://'.$host.'/'.$site.'/services/FCDataReceiver?';
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
			
			//Todo modify regex to check for success from FranConnect
			/*if( !preg_match('//gi', $content) ) {
				static::ReportError('FranConnect', $content);
			}*/
			
			echo "\nReturn:\n".$content;
		}
	}
}