<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => __( 'Tweetmeme Button', 'js_composer' ),
	'base' => 'vc_tweetmeme',
	'icon' => 'icon-wpb-tweetme',
	'category' => __( 'Social', 'js_composer' ),
	'description' => __( 'Tweet button', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'dropdown',
			'param_name' => 'type',
			'heading' => __( 'Choose a button', 'js_composer' ),
			'value' => array(
				__( 'Share a link', 'js_composer' ) => 'share',
				__( 'Follow', 'js_composer' ) => 'follow',
				__( 'Hashtag', 'js_composer' ) => 'hashtag',
				__( 'Mention', 'js_composer' ) => 'mention',
			),
			'description' => __( 'Select type of Twitter button.', 'js_composer' ),
		),

		//share type
		array(
			'type' => 'checkbox',
			'heading' => __( 'Share url: page URL', 'js_composer' ),
			'param_name' => 'share_use_page_url',
			'value' => array(
				__( 'Yes', 'js_composer' ) => 'page_url',
			),
			'std' => 'page_url',
			'dependency' => array(
				'element' => 'type',
				'value' => 'share',
			),
			'description' => __( 'Use the current page url to share?', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Share url: custom URL', 'js_composer' ),
			'param_name' => 'share_use_custom_url',
			'value' => '',
			'dependency' => array(
				'element' => 'share_use_page_url',
				'value_not_equal_to' => 'page_url',
			),
			'description' => __( 'Enter custom page url which you like to share on twitter?', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Tweet text: page title', 'js_composer' ),
			'param_name' => 'share_text_page_title',
			'value' => array(
				__( 'Yes', 'js_composer' ) => 'page_title',
			),
			'std' => 'page_title',
			'dependency' => array(
				'element' => 'type',
				'value' => 'share',
			),
			'description' => __( 'Use the current page title as tweet text?', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Tweet text: custom text', 'js_composer' ),
			'param_name' => 'share_text_custom_text',
			'value' => '',
			'dependency' => array(
				'element' => 'share_text_page_title',
				'value_not_equal_to' => 'page_title',
			),
			'description' => __( 'Enter the text to be used as a tweet?', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Via @', 'js_composer' ),
			'param_name' => 'share_via',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'share',
			),
			'description' => __( 'Enter your Twitter username.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Recommend @', 'js_composer' ),
			'param_name' => 'share_recommend',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'share',
			),
			'description' => __( 'Enter the Twitter username to be recommended.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Hashtag #', 'js_composer' ),
			'param_name' => 'share_hashtag',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'share',
			),
			'description' => __( 'Add a comma-separated list of hashtags to a Tweet using the hashtags parameter.', 'js_composer' ),
		),

		//follow type
		array(
			'type' => 'textfield',
			'heading' => __( 'User @', 'js_composer' ),
			'param_name' => 'follow_user',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'follow',
			),
			'description' => __( 'Enter username to follow.', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Show username', 'js_composer' ),
			'param_name' => 'follow_show_username',
			'value' => array(
				__( 'Yes', 'js_composer' ) => 'yes',
			),
			'std' => 'yes',
			'dependency' => array(
				'element' => 'type',
				'value' => 'follow',
			),
			'description' => __( 'Do you want to show username in button?', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Show followers count', 'js_composer' ),
			'param_name' => 'show_followers_count',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'follow',
			),
			'description' => __( 'Do you want to displat the follower count in button?', 'js_composer' ),
		),
		//hashtag type
		array(
			'type' => 'textfield',
			'heading' => __( 'Hashtag #', 'js_composer' ),
			'param_name' => 'hashtag_hash',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'hashtag',
			),
			'description' => __( 'Add hashtag to a Tweet using the hashtags parameter', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Tweet text: No default text', 'js_composer' ),
			'param_name' => 'hashtag_no_default',
			'value' => array(
				__( 'Yes', 'js_composer' ) => 'yes',
			),
			'std' => 'yes',
			'dependency' => array(
				'element' => 'type',
				'value' => 'hashtag',
			),
			'description' => __( 'Set no default text for tweet?', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Tweet text: custom', 'js_composer' ),
			'param_name' => 'hashtag_custom_tweet_text',
			'value' => '',
			'dependency' => array(
				'element' => 'hashtag_no_default',
				'value_not_equal_to' => 'yes',
			),
			'description' => __( 'Set custom text for tweet.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Recommend @', 'js_composer' ),
			'param_name' => 'hashtag_recommend_1',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'hashtag',
			),
			'description' => __( 'Enter username to be recommended.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Recommend @', 'js_composer' ),
			'param_name' => 'hashtag_recommend_2',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'hashtag',
			),
			'description' => __( 'Enter username to be recommended.', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Tweet url: No URL', 'js_composer' ),
			'param_name' => 'hashtag_no_url',
			'value' => array(
				__( 'Yes', 'js_composer' ) => 'yes',
			),
			'std' => 'yes',
			'dependency' => array(
				'element' => 'type',
				'value' => 'hashtag',
			),
			'description' => __( 'Do you want to set no url to be tweeted?', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Tweet url: custom', 'js_composer' ),
			'param_name' => 'hashtag_custom_tweet_url',
			'value' => '',
			'dependency' => array(
				'element' => 'hashtag_no_url',
				'value_not_equal_to' => 'yes',
			),
			'description' => __( 'Enter custom url to be used in the tweet.', 'js_composer' ),
		),
		//mention type
		array(
			'type' => 'textfield',
			'heading' => __( 'Tweet to @', 'js_composer' ),
			'param_name' => 'mention_tweet_to',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'mention',
			),
			'description' => __( 'Enter username where you want to send your tweet.', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Tweet text: No default text', 'js_composer' ),
			'param_name' => 'mention_no_default',
			'value' => array(
				__( 'Yes', 'js_composer' ) => 'yes',
			),
			'std' => 'yes',
			'dependency' => array(
				'element' => 'type',
				'value' => 'mention',
			),
			'description' => __( 'Set no default text of the tweet?', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Tweet text: custom', 'js_composer' ),
			'param_name' => 'mention_custom_tweet_text',
			'value' => '',
			'dependency' => array(
				'element' => 'mention_no_default',
				'value_not_equal_to' => 'yes',
			),
			'description' => __( 'Enter custom text for the tweet.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Recommend @', 'js_composer' ),
			'param_name' => 'mention_recommend_1',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'mention',
			),
			'description' => __( 'Enter username to recommend.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Recommend @', 'js_composer' ),
			'param_name' => 'mention_recommend_2',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'mention',
			),
			'description' => __( 'Enter username to recommend.', 'js_composer' ),
		),
		// general
		array(
			'type' => 'checkbox',
			'heading' => __( 'Use large button', 'js_composer' ),
			'param_name' => 'large_button',
			'value' => '',
			'description' => __( 'Do you like to display a larger Tweet button?', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => __( 'Opt-out of tailoring Twitter', 'js_composer' ),
			'param_name' => 'disable_tailoring',
			'value' => '',
			'description' => __( 'Tailored suggestions make building a great timeline. Would you like to disable this feature?', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Language', 'js_composer' ),
			'param_name' => 'lang',
			'value' => array(
				'Automatic' => '',
				'French - français' => 'fr',
				'English' => 'en',
				'Arabic - العربية' => 'ar',
				'Japanese - 日本語' => 'ja',
				'Spanish - Español' => 'es',
				'German - Deutsch' => 'de',
				'Italian - Italiano' => 'it',
				'Indonesian - Bahasa Indonesia' => 'id',
				'Portuguese - Português' => 'pt',
				'Korean - 한국어' => 'ko',
				'Turkish - Türkçe' => 'tr',
				'Russian - Русский' => 'ru',
				'Dutch - Nederlands' => 'nl',
				'Filipino - Filipino' => 'fil',
				'Malay - Bahasa Melayu' => 'msa',
				'Traditional Chinese - 繁體中文' => 'zh-tw',
				'Simplified Chinese - 简体中文' => 'zh-cn',
				'Hindi - हिन्दी' => 'hi',
				'Norwegian - Norsk' => 'no',
				'Swedish - Svenska' => 'sv',
				'Finnish - Suomi' => 'fi',
				'Danish - Dansk' => 'da',
				'Polish - Polski' => 'pl',
				'Hungarian - Magyar' => 'hu',
				'Farsi - فارسی' => 'fa',
				'Hebrew - עִבְרִית' => 'he',
				'Urdu - اردو' => 'ur',
				'Thai - ภาษาไทย' => 'th',
			),
			'description' => __( 'Select button display language or allow it to be automatically defined by user preferences.', 'js_composer' ),
		),
		vc_map_add_css_animation(),
		array(
			'type' => 'el_id',
			'heading' => __( 'Element ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( __( 'Enter element ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).', 'js_composer' ), 'http://www.w3schools.com/tags/att_global_id.asp' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
		array(
			'type' => 'css_editor',
			'heading' => __( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => __( 'Design Options', 'js_composer' ),
		),
	),
);
