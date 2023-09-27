<?php

if (! function_exists('blocksy_get_avatar_alt_for')) {
	function blocksy_get_avatar_alt_for($user_id) {
		global $simple_local_avatars;

		if ($simple_local_avatars) {
			$alt = $simple_local_avatars->get_simple_local_avatar_alt($user_id);

			if (! empty($alt)) {
				return $alt;
			}
		}

		return get_the_author_meta('display_name');
	}
}
