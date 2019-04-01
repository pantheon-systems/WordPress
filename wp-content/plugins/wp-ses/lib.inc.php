<?php


/**
 * This was copied from WPMU wp-includes/wpmu-functions.php, then updated
 * from 3.0.0, the wp function is is_email
 */
if (!function_exists('validate_email')) {
	function validate_email($email, $check_domain = true) {
            $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i'; 
            // Would it be better to use http://php.net/manual/en/function.filter-var.php ?
		if (preg_match($regex, $email)) {
			if ($check_domain && function_exists('checkdnsrr')) {
				list (, $domain) = explode('@', $email);
				if (checkdnsrr($domain . '.', 'MX') || checkdnsrr($domain . '.', 'A')) {
					return true;
				}
				return false;
			}
			return true;
		}
		return false;
	} // End of validate_email() function definition
}

if (!function_exists('has_HTML')) {
	function has_HTML($str) {
		if (strlen($str) != strlen(strip_tags($str))) {
			return true;
		} else {
			return false;
		}
	}
}
?>