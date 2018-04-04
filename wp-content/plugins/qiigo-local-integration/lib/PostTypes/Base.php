<?php

namespace Qiigo\Plugin\LocalIntegration\PostTypes {
	class Base {
		public static function RegisterCPT( $key, $opts ) {
			register_post_type( $key, $opts );
		}
	}
}