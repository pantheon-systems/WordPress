<?php

namespace Qiigo\Plugin\Integration\CRM {
	class Base {
		protected static function ReportError($sys, $error) {
			$from = get_option('gen_from_email');
			$to = get_option('gen_error_email');
			$subject = 'There was a problem inserting a lead into '.$sys;
			
			$body = 'The error returned was:'."<br />\n<br />\n".htmlspecialchars($error);
			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: ' . $from . "\r\n";
			
			\wp_mail($to, $subject, $body, $headers);
		}
	}
}