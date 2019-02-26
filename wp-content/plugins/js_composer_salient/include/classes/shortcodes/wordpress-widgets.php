<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder shortcodes
 *
 * @package WPBakeryPageBuilder
 *
 */
class WPBakeryShortCode_VC_Wp_Search extends WPBakeryShortCode {
}

class WPBakeryShortCode_VC_Wp_Meta extends WPBakeryShortCode {
}

class WPBakeryShortCode_VC_Wp_Recentcomments extends WPBakeryShortCode {
}

class WPBakeryShortCode_VC_Wp_Calendar extends WPBakeryShortCode {
}

class WPBakeryShortCode_VC_Wp_Pages extends WPBakeryShortCode {
}

class WPBakeryShortCode_VC_Wp_Tagcloud extends WPBakeryShortCode {
}

class WPBakeryShortCode_VC_Wp_Custommenu extends WPBakeryShortCode {
}

class WPBakeryShortCode_VC_Wp_Text extends WPBakeryShortCode {
	/**
	 * This actually fixes #1537 by converting 'text' to 'content'
	 * @since 4.4
	 *
	 * @param $atts
	 *
	 * @return mixed
	 */
	public static function convertTextAttributeToContent( $atts ) {
		if ( isset( $atts['text'] ) ) {
			if ( ! isset( $atts['content'] ) || empty( $atts['content'] ) ) {
				$atts['content'] = $atts['text'];
			}
		}

		return $atts;
	}
}

class WPBakeryShortCode_VC_Wp_Posts extends WPBakeryShortCode {
}

class WPBakeryShortCode_VC_Wp_Links extends WPBakeryShortCode {
}

class WPBakeryShortCode_VC_Wp_Categories extends WPBakeryShortCode {
}

class WPBakeryShortCode_VC_Wp_Archives extends WPBakeryShortCode {
}

class WPBakeryShortCode_VC_Wp_Rss extends WPBakeryShortCode {
}
