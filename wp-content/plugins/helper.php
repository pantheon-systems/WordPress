<?php

if (!class_exists('PTNHelper')) :
	class PTNHelper {
		public static function safePregMatch($pattern, $subject, &$matches = null, $flags = 0, $offset = 0) {
			if (!is_string($pattern) || !is_string($subject)) {
				return false;
			}
			return preg_match($pattern, $subject, $matches, $flags, $offset);
		}
	}
endif;