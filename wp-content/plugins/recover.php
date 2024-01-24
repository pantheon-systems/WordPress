<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('PTNRecover')) :
	class PTNRecover {
		public static $default_secret_key = 'bvSecretKey';

		public static function defaultSecret($settings) {
			$secret = self::getDefaultSecret($settings);
			if (empty($secret)) {
				$secret = PTNAccount::randString(32);
				self::updateDefaultSecret($settings, $secret);
			}
			return $secret;
		}

		public static function deleteDefaultSecret($settings) {
			$settings->deleteOption(self::$default_secret_key);
		}

		public static function getDefaultSecret($settings) {
			return $settings->getOption(self::$default_secret_key);
		}

		public static function updateDefaultSecret($settings, $secret) {
			$settings->updateOption(self::$default_secret_key, $secret);
		}

		public static function validate($pubkey) {
			if ($pubkey && strlen($pubkey) >= 32) {
				return true;
			} else {
				return false;
			}
		}

		public static function find($settings, $pubkey) {
			if (!self::validate($pubkey)) {
				return null;
			}
			$secret = self::getDefaultSecret($settings);
			if (!empty($secret) && (strlen($secret) >= 32)) {
				$account = new PTNAccount($settings, $pubkey, $secret);
			}
			return $account;
		}
	}
endif;