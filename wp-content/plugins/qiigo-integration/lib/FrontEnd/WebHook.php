<?php

namespace Qiigo\Plugin\Integration\FrontEnd {
	class WebHook {
		protected static $fields = array(
				'action' => 'Action',
				'lead_source' => 'Lead Source',
				'name' => 'Name',
				'first_name' => 'First Name',
				'last_name' => 'Last Name',
				'phone' => 'Telephone Number',
				'email' => 'Email',
				'city' => 'City',
				'state' => 'State/Province',
				'zip' => 'Zip/Postal Code',
				'country' => 'Country',
				'country_code' => 'Country Code',
				'comments' => 'Comments & Questions',
				'company' => 'Company Name',
				'footage' =>  'Square Footage',
				'email_opt_in' =>  'Email Opt-In',
				'lead_estimate_date_preferred' => 'Estimate Date Preferred',
			);
			
		public static function Run() {
			$post = static::GetPost();
			static::Log('Formatted Post', $post);
			
			if( !isset($post['action']) || !isset($post['lead_source']) || !isset($post['zip']) ) {
				static::Log('[ERROR] Missing required fields in post; action, lead_source and zip.');
				return;
			}
			
			$c = (isset($post['country_code']) ? $post['country_code'] : null);
			$loc = \Qiigo\Plugin\Integration\PostTypes\Location::Find($post['zip'], $c);
			$loc_data = array();
			if( !isset($loc) ) {
				// Load defaults
				$loc_data['email'] = get_option('gen_default_email');
				$loc_data['office'] = get_option('ev_default_office');
				$loc_data['country'] = get_option('gen_default_country');
			} else {
				$loc_data['email'] = \get_field('email', $loc->ID);
				$loc_data['office'] = \get_field('office', $loc->ID);
				$loc_data['country'] = \get_field('country', $loc->ID);
			}
			
			$email_to = null;
			
			
			switch(strtolower($post['action'])) {
			case 'fc':
				\Qiigo\Plugin\Integration\CRM\FranConnect::Submit($post, $loc_data);
				$email_to = null;
				break;
			case 'eval':
				$post['action'] = 'add_lead';
				\Qiigo\Plugin\Integration\CRM\ExtraView::Submit($post, $loc_data);
				$email_to = $loc_data['email'];
				$office = $loc_data['office'];
				$post_data = array(
                                // CRM specific                               
                                'statevar' => 'custom',
                                'ev_lead_action' => $post['action'],
                                // Location Specific
                                'office' =>  urldecode($loc_data['office']),
                                'master_country' => 'US',
                                // Submission Specific
                                'lead_source' => 'Website '. urldecode($post['lead_source']),
                                'email' =>  urldecode($post['email']),
                                'name' => ((isset($post['name'])) ? $post['name'] : urldecode($post['first_name']).' '.urldecode($post['last_name'])),
                                'telephone' =>  urldecode($post['phone']),
                                'addr_city' => urldecode($post['city']),
                                'addr_state' =>  urldecode($post['state']),
                                'addr_postal_code' =>  urldecode($post['zip']),
                                'comments' =>   urldecode($post['comments'])   
                        );
				break;
			case 'evaf':
				$post['action'] = 'add_franchisee';
				\Qiigo\Plugin\Integration\CRM\ExtraView::Submit($post, $loc_data);
				$email_to = $loc_data['email'];
				$post_data = array(
                                // CRM specific                                
                                'statevar' => 'custom',
                                'ev_lead_action' => $post['action'],
                                // Location Specific
                                'office' =>  urldecode($loc_data['office']),
                                'master_country' => 'US',
                                // Submission Specific
                                'lead_source' => 'Website '. urldecode($post['lead_source']),
                                'email' =>  urldecode($post['email']),
                                'name' => ((isset($post['name'])) ? $post['name'] : urldecode($post['first_name']).' '.urldecode($post['last_name'])),
                                'telephone' =>  urldecode($post['phone']),
                                'addr_city' => urldecode($post['city']),
                                'addr_state' =>  urldecode($post['state']),
                                'addr_postal_code' =>  urldecode($post['zip']),
                                'comments' =>   urldecode($post['comments'])   
                        );
				break;
			default:
				static::Log('Invalid action: '.$post['action']);
				break;
			}
			
			if( isset($email_to)  /*&&  urldecode($post['zip']) == '00000'*/) {				
				$new_email_to = 'rlange@extraview.com';//rlange@extraview.com;
				static::SendEmail($email_to, $post, $loc_data);
				//static::SendEmailOthers($new_email_to,$post, $post_data);
			}
		}
		
		protected static function SendEmail($to, $data, $loc) {
			$fields = static::$fields;
			//$subject = 'New Lead from Website - '.$data['lead_source'];
			$subject = 'New Lead from Website - '.urldecode($data['lead_source']);
			
			\ob_start();
			
			require_once(dirname(dirname(dirname(__FILE__))).DS.'templates'.DS.'email.phtml');
			
			$message = \ob_get_contents();
			\ob_end_clean();
			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			//$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'Content-type: text/html; charset="utf-8"' . "\r\n";
			$headers .= 'From: ' . get_option('gen_from_email') . "\r\n";
			
			\wp_mail($to, $subject, $message, $headers);
		}
		//Email sending to //rlange@extraview.com
		protected static function SendEmailOthers($to, $data, $post_data) { 
			$fields = static::$fields; 
			$subject = 'API DATA';
			\ob_start(); 
			require_once(dirname(dirname(dirname(__FILE__))).DS.'templates'.DS.'email_new.phtml'); 
			$message = \ob_get_contents(); 
			\ob_end_clean(); 
	
			$headers  = 'MIME-Version: 1.0' . "\r\n"; 
			$headers .= 'Content-type: text/plain; charset="utf-8"' . "\r\n"; 
			$headers .= 'From: ' . get_option('gen_from_email') . "\r\n"; 
			$message_encode = rtrim((base64_encode($message))); 	
			\wp_mail($to, $subject, $message_encode, $headers); 
		}
		public static function Log($message, $object = null) {
			echo $message."\n".print_r($object,true);
		}
		
		protected static function GetPost() {
			$ret = array();
			
			foreach( static::$fields as $k => $v ) {
				if( isset($_POST[$k]) )
					$ret[$k] = $_POST[$k];
			}
			
			return $ret;
		}
	}
}
