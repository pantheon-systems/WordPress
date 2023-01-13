<?php
/**
 * Social Share widget.
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ocean_Extra_Social_Share_Widget' ) ) {
	class Ocean_Extra_Social_Share_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			// Start up widget
			parent::__construct(
				'ocean_social_share',
				esc_html__( '&raquo; Social Share', 'ocean-extra' ),
				array(
					'classname'   => 'widget-oceanwp-social-share social-share',
					'description' => esc_html__( 'Display social sharing buttons on your sidebar.', 'ocean-extra' ),
					'customize_selective_refresh' => true,
				)
			);

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 1.3.8
		 *
		 * @param string $hook_suffix
		 */
		public function enqueue_scripts( $hook_suffix ) {
			if ( 'widgets.php' !== $hook_suffix ) {
				return;
			}

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 1.0.0
		 */
		public function social_array() {
			$post_id  	= get_the_ID();
			$post_url   = get_permalink( $post_id );
			$post_title = get_the_title();

			// Get SEO meta and use instead if they exist
			if ( defined( 'WPSEO_VERSION' ) ) {
				if ( $meta = get_post_meta( $post_id, '_yoast_wpseo_twitter-title', true ) ) {
					$post_title = $meta;
				}
			}

			// Array
			$return = apply_filters( 'ocean_social_share_buttons', array(
				'twitter' => array(
					'name' 	=> 'Twitter',
					'title' => esc_html__( 'Share on Twitter', 'ocean-extra' ),
					'icon'  => '<svg class="owpss-icon" aria-labelledby="owpss-twitter-icon" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path d="M23.954 4.569c-.885.389-1.83.654-2.825.775 1.014-.611 1.794-1.574 2.163-2.723-.951.555-2.005.959-3.127 1.184-.896-.959-2.173-1.559-3.591-1.559-2.717 0-4.92 2.203-4.92 4.917 0 .39.045.765.127 1.124C7.691 8.094 4.066 6.13 1.64 3.161c-.427.722-.666 1.561-.666 2.475 0 1.71.87 3.213 2.188 4.096-.807-.026-1.566-.248-2.228-.616v.061c0 2.385 1.693 4.374 3.946 4.827-.413.111-.849.171-1.296.171-.314 0-.615-.03-.916-.086.631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105-.39 0-.779-.023-1.17-.067 2.189 1.394 4.768 2.209 7.557 2.209 9.054 0 13.999-7.496 13.999-13.986 0-.209 0-.42-.015-.63.961-.689 1.8-1.56 2.46-2.548l-.047-.02z"/>
							</svg>',
					'url'  	=> 'https://twitter.com/share?text='. wp_strip_all_tags( $post_title ) .'&amp;url='. rawurlencode( esc_url( $post_url ) ),
				),
				'facebook' => array(
					'name' 	=> 'Facebook',
					'title' => esc_html__( 'Share on Facebook', 'ocean-extra' ),
					'icon'  => '<svg class="owpss-icon" aria-labelledby="owpss-facebook-icon" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path d="M5.677,12.998V8.123h3.575V6.224C9.252,2.949,11.712,0,14.736,0h3.94v4.874h-3.94
								c-0.432,0-0.934,0.524-0.934,1.308v1.942h4.874v4.874h-4.874V24H9.252V12.998H5.677z"/>
							</svg>',
					'url'  	=> 'https://www.facebook.com/sharer.php?u='. rawurlencode( esc_url( $post_url ) ),
				),
				'googleplus' => array(
					'name' 	=> 'Google+',
					'title' => esc_html__( 'Share on Google+', 'ocean-extra' ),
					'icon'  => '<svg class="owpss-icon" aria-labelledby="owpss-googleplus-icon" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path d="M7.636,10.929V13.5h4.331c-0.175,1.104-1.309,3.236-4.331,3.236c-2.607,0-4.735-2.121-4.735-4.736
								s2.127-4.736,4.735-4.736c1.484,0,2.476,0.621,3.044,1.157l2.073-1.961C11.422,5.239,9.698,4.5,7.636,4.5C3.415,4.5,0,7.854,0,12
								s3.415,7.5,7.636,7.5c4.407,0,7.331-3.043,7.331-7.329c0-0.493-0.055-0.868-0.12-1.243H7.636z"/>
								<path d="M21.818,10.929V8.786h-2.182v2.143h-2.182v2.143h2.182v2.143h2.182v-2.143H24c0,0.022,0-2.143,0-2.143
								H21.818z"/>
							</svg>',
					'url'  	=> 'https://plus.google.com/share?url='. rawurlencode( esc_url( $post_url ) ),
				),
				'pinterest' => array(
					'name' 	=> 'Pinterest',
					'title' => esc_html__( 'Share on Pinterest', 'ocean-extra' ),
					'icon'  => '<svg class="owpss-icon" aria-labelledby="owpss-pinterest-icon" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path d="M13.757,17.343c-1.487,0-2.886-0.804-3.365-1.717c0,0-0.8,3.173-0.969,3.785
								c-0.596,2.165-2.35,4.331-2.487,4.508c-0.095,0.124-0.305,0.085-0.327-0.078c-0.038-0.276-0.485-3.007,0.041-5.235
								c0.264-1.118,1.772-7.505,1.772-7.505s-0.44-0.879-0.44-2.179c0-2.041,1.183-3.565,2.657-3.565c1.252,0,1.857,0.94,1.857,2.068
								c0,1.26-0.802,3.142-1.216,4.888c-0.345,1.461,0.734,2.653,2.174,2.653c2.609,0,4.367-3.352,4.367-7.323
								c0-3.018-2.032-5.278-5.731-5.278c-4.177,0-6.782,3.116-6.782,6.597c0,1.2,0.355,2.047,0.909,2.701
								c0.255,0.301,0.29,0.422,0.198,0.767c-0.067,0.254-0.218,0.864-0.281,1.106c-0.092,0.349-0.375,0.474-0.69,0.345
								c-1.923-0.785-2.82-2.893-2.82-5.262c0-3.912,3.3-8.604,9.844-8.604c5.259,0,8.72,3.805,8.72,7.89
								C21.188,13.307,18.185,17.343,13.757,17.343z"/>
							</svg>',
					'url'  	=> 'https://www.pinterest.com/pin/create/button/?url='. rawurlencode( esc_url( $post_url ) ) .'&amp;media='. wp_get_attachment_url( get_post_thumbnail_id( $post_id ) ) .'&amp;description='. ( !is_admin() ? urlencode( wp_trim_words( strip_shortcodes( get_the_content( $post_id ) ), 40 ) ) : ''),
				),
				'linkedin' => array(
					'name' 	=> 'LinkedIn',
					'title' => esc_html__( 'Share on LinkedIn', 'ocean-extra' ),
					'icon'  => '<svg class="owpss-icon" aria-labelledby="owpss-linkedin-icon" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path d="M6.52,22h-4.13V8.667h4.13V22z M4.436,6.92
								c-1.349,0-2.442-1.101-2.442-2.46C1.994,3.102,3.087,2,4.436,2s2.442,1.102,2.442,2.46C6.877,5.819,5.784,6.92,4.436,6.92z
								M21.994,22h-4.109c0,0,0-5.079,0-6.999c0-1.919-0.73-2.991-2.249-2.991c-1.652,0-2.515,1.116-2.515,2.991c0,2.054,0,6.999,0,6.999
								h-3.96V8.667h3.96v1.796c0,0,1.191-2.202,4.02-2.202c2.828,0,4.853,1.727,4.853,5.298C21.994,17.129,21.994,22,21.994,22z"/>
							</svg>',
					'url'  	=> 'https://www.linkedin.com/shareArticle?mini=true&amp;url='. rawurlencode( esc_url( $post_url ) ) .'&amp;title='. wp_strip_all_tags( $post_title ) .'&amp;summary='. ( !is_admin() ? urlencode( wp_trim_words( strip_shortcodes( get_the_content( $post_id ) ), 40 ) ) .'&amp;source='. esc_url( home_url( '/' ) ) : ''),
				),
				'viber' => array(
					'name' 	=> 'Viber',
					'title' => esc_html__( 'Share on Viber', 'ocean-extra' ),
					'icon'  => '<svg class="owpss-icon" aria-labelledby="owpss-viber-icon" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path d="M14.957,5.825c0.764,0.163,1.349,0.453,1.849,0.921c0.643,0.608,0.996,1.343,1.151,2.4
								c0.105,0.689,0.062,0.96-0.182,1.184c-0.229,0.209-0.651,0.217-0.907,0.019c-0.186-0.139-0.244-0.286-0.287-0.685
								c-0.05-0.53-0.143-0.902-0.302-1.246c-0.341-0.731-0.942-1.111-1.957-1.235c-0.477-0.058-0.62-0.112-0.775-0.294
								c-0.283-0.337-0.174-0.883,0.217-1.084c0.147-0.074,0.209-0.081,0.535-0.062C14.5,5.755,14.798,5.79,14.957,5.825z M14.131,2.902
								c2.353,0.344,4.175,1.436,5.369,3.209c0.671,0.999,1.089,2.171,1.233,3.429c0.05,0.461,0.05,1.3-0.004,1.44
								c-0.051,0.131-0.213,0.309-0.353,0.383c-0.151,0.078-0.473,0.07-0.651-0.023c-0.298-0.151-0.388-0.391-0.388-1.041
								c0-1.002-0.26-2.059-0.709-2.88c-0.512-0.937-1.256-1.711-2.163-2.249c-0.779-0.465-1.93-0.809-2.981-0.894
								c-0.38-0.031-0.589-0.108-0.733-0.275c-0.221-0.252-0.244-0.592-0.058-0.875C12.895,2.813,13.205,2.763,14.131,2.902z
								M5.002,0.514c0.136,0.047,0.345,0.155,0.465,0.232c0.736,0.488,2.787,3.108,3.458,4.416c0.384,0.747,0.512,1.3,0.392,1.711
								C9.193,7.314,8.988,7.547,8.069,8.286C7.701,8.584,7.356,8.89,7.301,8.971C7.162,9.172,7.049,9.567,7.049,9.846
								c0.004,0.646,0.423,1.819,0.973,2.721c0.426,0.7,1.19,1.598,1.946,2.287c0.888,0.813,1.671,1.366,2.555,1.804
								c1.136,0.565,1.83,0.708,2.337,0.472c0.128-0.058,0.264-0.135,0.306-0.17c0.039-0.035,0.337-0.399,0.663-0.801
								c0.628-0.79,0.771-0.917,1.202-1.065c0.547-0.186,1.105-0.135,1.667,0.151c0.427,0.221,1.357,0.797,1.957,1.215
								c0.791,0.553,2.481,1.931,2.71,2.206c0.403,0.495,0.473,1.13,0.202,1.831c-0.287,0.739-1.403,2.125-2.182,2.717
								c-0.705,0.534-1.206,0.739-1.865,0.77c-0.543,0.027-0.768-0.019-1.461-0.306c-5.442-2.241-9.788-5.585-13.238-10.179
								c-1.802-2.4-3.175-4.888-4.113-7.47c-0.547-1.505-0.574-2.16-0.124-2.93c0.194-0.325,1.019-1.13,1.62-1.579
								c1-0.743,1.461-1.018,1.83-1.095C4.285,0.371,4.723,0.414,5.002,0.514z M13.864,0.096c1.334,0.166,2.411,0.487,3.593,1.065
								c1.163,0.569,1.907,1.107,2.892,2.086c0.923,0.925,1.434,1.626,1.977,2.713c0.756,1.517,1.186,3.321,1.26,5.306
								c0.027,0.677,0.008,0.828-0.147,1.022c-0.294,0.375-0.942,0.313-1.163-0.108c-0.07-0.139-0.089-0.259-0.112-0.801
								c-0.039-0.832-0.097-1.37-0.213-2.013c-0.458-2.52-1.667-4.532-3.597-5.976c-1.609-1.208-3.272-1.796-5.45-1.924
								c-0.737-0.043-0.864-0.07-1.031-0.197c-0.31-0.244-0.326-0.817-0.027-1.084c0.182-0.166,0.31-0.19,0.942-0.17
								C13.116,0.027,13.6,0.065,13.864,0.096z"/>
							</svg>',
					'url'  	=> 'viber://forward?text='. rawurlencode( esc_url( $post_url ) ),
				),
				'vk' => array(
					'name' 	=> 'VK',
					'title' => esc_html__( 'Share on VK', 'ocean-extra' ),
					'icon'  => '<svg class="owpss-icon" aria-labelledby="owpss-vk-icon" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path d="M11.701 18.771h1.437s.433-.047.654-.284c.21-.221.21-.63.21-.63s-.031-1.927.869-2.21c.887-.281 2.012 1.86 3.211 2.683.916.629 1.605.494 1.605.494l3.211-.044s1.682-.105.887-1.426c-.061-.105-.451-.975-2.371-2.76-2.012-1.861-1.742-1.561.676-4.787 1.469-1.965 2.07-3.166 1.875-3.676-.166-.48-1.26-.361-1.26-.361l-3.602.031s-.27-.031-.465.09c-.195.119-.314.391-.314.391s-.572 1.529-1.336 2.82c-1.623 2.729-2.268 2.879-2.523 2.699-.604-.391-.449-1.58-.449-2.432 0-2.641.404-3.75-.781-4.035-.39-.091-.681-.15-1.685-.166-1.29-.014-2.378.01-2.995.311-.405.203-.72.652-.539.675.24.03.779.146 1.064.537.375.506.359 1.636.359 1.636s.211 3.116-.494 3.503c-.495.262-1.155-.28-2.595-2.756-.735-1.26-1.291-2.67-1.291-2.67s-.105-.256-.299-.406c-.227-.165-.557-.225-.557-.225l-3.435.03s-.51.016-.689.24c-.166.195-.016.615-.016.615s2.686 6.287 5.732 9.453c2.79 2.902 5.956 2.715 5.956 2.715l-.05-.055z"/>
							</svg>',
					'url'  	=> 'https://vk.com/share.php?url='. rawurlencode( esc_url( $post_url ) ),
				),
				'reddit' => array(
					'name' 	=> 'Reddit',
					'title' => esc_html__( 'Share on Reddit', 'ocean-extra' ),
					'icon'  => '<svg class="owpss-icon" aria-labelledby="owpss-reddit-icon" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path d="M23.999,11.786c0-1.576-1.294-2.858-2.885-2.858c-0.689,0-1.321,0.241-1.817,0.641
								c-1.759-1.095-3.991-1.755-6.383-1.895l1.248-3.91l3.43,0.8c0.09,1.237,1.134,2.217,2.405,2.217c1.33,0,2.412-1.072,2.412-2.391
								c0-1.318-1.082-2.39-2.412-2.39c-0.93,0-1.739,0.525-2.141,1.291l-3.985-0.93c-0.334-0.078-0.671,0.112-0.775,0.436L11.547,7.65
								C8.969,7.712,6.546,8.375,4.658,9.534c-0.49-0.38-1.105-0.607-1.774-0.607C1.293,8.927,0,10.209,0,11.785
								c0,0.974,0.495,1.836,1.249,2.351c-0.031,0.227-0.048,0.455-0.048,0.686c0,1.97,1.156,3.803,3.254,5.16
								C6.468,21.283,9.13,22,11.952,22s5.485-0.716,7.496-2.018c2.099-1.357,3.254-3.19,3.254-5.16c0-0.21-0.014-0.419-0.041-0.626
								C23.464,13.689,23.999,12.798,23.999,11.786 M19.997,3.299c0.607,0,1.102,0.49,1.102,1.091c0,0.602-0.494,1.092-1.102,1.092
								s-1.102-0.49-1.102-1.092C18.896,3.789,19.389,3.299,19.997,3.299 M6.805,13.554c0-0.888,0.752-1.633,1.648-1.633
								c0.897,0,1.625,0.745,1.625,1.633c0,0.889-0.728,1.61-1.625,1.61C7.557,15.163,6.805,14.442,6.805,13.554 M15.951,18.288
								c-0.836,0.827-2.124,1.229-3.939,1.229c-0.004,0-0.008-0.001-0.013-0.001c-0.004,0-0.008,0.001-0.013,0.001
								c-1.815,0-3.103-0.402-3.938-1.229c-0.256-0.254-0.256-0.665,0-0.919c0.256-0.253,0.671-0.253,0.927,0
								c0.576,0.571,1.561,0.849,3.01,0.849c0.005,0,0.009,0.001,0.013,0.001c0.005,0,0.009-0.001,0.013-0.001
								c1.45,0,2.435-0.278,3.012-0.849c0.256-0.254,0.671-0.253,0.927,0C16.206,17.623,16.206,18.034,15.951,18.288 M15.569,15.163
								c-0.897,0-1.651-0.721-1.651-1.61s0.754-1.633,1.651-1.633s1.625,0.745,1.625,1.633C17.193,14.442,16.466,15.163,15.569,15.163"/>
							</svg>',
					'url'  	=> 'https://www.reddit.com/submit?url='. rawurlencode( esc_url( $post_url ) ) .'&amp;title='. wp_strip_all_tags( $post_title ),
				),
				'tumblr' => array(
					'name' 	=> 'Tumblr',
					'title' => esc_html__( 'Share on Tumblr', 'ocean-extra' ),
					'icon'  => '<svg class="owpss-icon" aria-labelledby="owpss-tumblr-icon" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path d="M19.44,22.887c-1.034,0.487-1.97,0.828-2.808,1.024
								c-0.838,0.195-1.744,0.293-2.718,0.293c-1.106,0-2.083-0.14-2.933-0.418c-0.851-0.279-1.575-0.677-2.175-1.194
								c-0.6-0.518-1.017-1.067-1.248-1.649c-0.231-0.581-0.347-1.425-0.347-2.53V9.93H4.56V6.482c0.947-0.309,1.759-0.751,2.434-1.327
								C7.67,4.58,8.212,3.889,8.62,3.081C9.029,2.274,9.311,1.247,9.464,0h3.429v6.131h5.747V9.93h-5.747v6.208
								c0,1.403,0.074,2.304,0.223,2.702c0.149,0.399,0.426,0.718,0.829,0.954c0.536,0.322,1.148,0.483,1.838,0.483
								c1.225,0,2.444-0.399,3.657-1.196V22.887L19.44,22.887z"/>
							</svg>',
					'url'  	=> 'https://www.tumblr.com/widgets/share/tool?canonicalUrl='. rawurlencode( esc_url( $post_url ) ),
				),
				'viadeo' => array(
					'name' 	=> 'Viadeo',
					'title' => esc_html__( 'Share on Viadeo', 'ocean-extra' ),
					'icon'  => '<svg class="owpss-icon" aria-labelledby="owpss-viadeo-icon" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<path d="M21.046,0.546c-1.011,2.159-2.882,2.557-2.882,2.557c-1.87,0.476-2.525,1.202-2.525,1.202
								c-1.871,1.889-0.396,4.181-0.396,4.181c4.039-0.922,5.514-4.259,5.514-4.259c-0.181,2.242-4.986,4.887-4.986,4.887
								c1.592,1.565,3.111,1.374,4.112,0.775c1.328-0.795,1.968-2.537,1.968-2.537C23.142,3.484,21.046,0.546,21.046,0.546z
								M14.424,7.082c0.044,0.662,0.772,12.464-5.445,14.829c0,0,0.571,0.108,1.216,0.079c0,0,7.912-5.015,4.283-14.745
								C14.478,7.244,14.463,7.185,14.424,7.082z M11.113,0c1.988,3.356,3.067,6.364,3.311,7.081V7.052C13.936,1.88,11.113,0,11.113,0z"/>
								<path d="M16.465,15.438c0,1.192-0.283,2.301-0.85,3.332c-0.566,1.031-1.328,1.825-2.295,2.385
								c-0.962,0.559-2.022,0.839-3.169,0.839c-1.153,0-2.207-0.28-3.169-0.839C6.02,20.595,5.253,19.8,4.687,18.769
								c-0.566-1.03-0.85-2.139-0.85-3.332c0-1.845,0.62-3.42,1.861-4.725c1.24-1.3,2.725-1.953,4.454-1.953
								c0.82,0,1.587,0.152,2.3,0.447c0.073-0.756,0.337-1.457,0.625-2.032c-0.899-0.329-1.87-0.491-2.92-0.491
								c-2.496,0-4.561,0.923-6.197,2.772c-1.485,1.673-2.232,3.656-2.232,5.932c0,2.301,0.786,4.313,2.354,6.031
								C5.655,23.141,7.677,24,10.152,24c2.466,0,4.488-0.859,6.056-2.581c1.573-1.722,2.354-3.734,2.354-6.031
								c0-1.232-0.215-2.375-0.645-3.425c-0.723,0.447-1.406,0.677-1.973,0.8C16.295,13.578,16.465,14.471,16.465,15.438z"/>
							</svg>',
					'url'  	=> 'https://partners.viadeo.com/share?url='. rawurlencode( esc_url( $post_url ) ),
				),
			) );

			return $return;
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 * @since 1.0.0
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {

			// Get social share and
			$social_share = isset( $instance['social_share'] ) ? $instance['social_share'] : '';

			// Return if no social defined
			if ( ! $social_share ) {
				return;
			}

			// Return if no content or search page
			if ( empty( get_the_content() )
				|| is_search() ) {
				return;
			}

			// Define vars
			$title         	  = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
			$style   		  = isset( $instance['style'] ) ? $instance['style'] : '';
			$border_radius 	  = isset( $instance['border_radius'] ) ? $instance['border_radius'] : '';
			$twitter_username = isset( $instance['twitter_username'] ) ? $instance['twitter_username'] : '';
			$social_name      = isset( $instance['social_name'] ) ? $instance['social_name'] : 0;

			// Sanitize vars
			$border_radius = $border_radius ? $border_radius  : '';

			// Inline style
			$add_style = '';
			if ( $border_radius && 'simple' != $style ) {
				$add_style .= 'border-radius:'. esc_attr( $border_radius ) .';';
			}
			if ( $add_style ) {
				$add_style = ' style="' . esc_attr( $add_style ) . '"';
			}

			// Before widget hook
			echo $args['before_widget'];

				// Display title
				if ( $title ) {
					echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
				}

				// Display the social share. ?>
				<ul class="owp-social-share style-<?php echo esc_attr( $style ); ?> name-<?php echo $social_name ? 'shown' : 'hidden'; ?>">
					<?php
					// Original Array
					$social_array = $this->social_array();

					// Loop through each item in the array
					foreach( $social_share as $social_key ) {
						$name    = $social_array[$social_key]['name'];
						$title   = $social_array[$social_key]['title'];
						$url     = $social_array[$social_key]['url'];
						$icon    = $social_array[$social_key]['icon'];


						if ( $social_key == 'twitter' && !empty( $twitter_username ) ) {
							$url = $url . '&amp;via='.$twitter_username;
						}

						echo '<li class="'. esc_attr( $social_key ) .'">';

							echo '<a href="'. $url .'" title="'. esc_attr( $title ) .'" '. wp_kses_post( $add_style ) . ' onclick="owpShareOnClick( this.href );return false;">';

								echo '<span class="owp-icon-wrap">';
									echo $icon;
								echo '</span>';

								if( $social_name ) {
									echo '<span class="owp-social-name">' . $name . '</span>';
								}

							echo '</a>';

						echo '</li>';
					} ?>
				</ul>

				<?php $this->colors( $args, $instance ); ?>

			<?php
			// After widget hook
			echo $args['after_widget'];

		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 * @since 1.0.0
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			// Sanitize data
			$instance = $old_instance;
			$instance['title']           	= ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : null;
			$instance['style'] 				= ! empty( $new_instance['style'] ) ? strip_tags( $new_instance['style'] ) : 'light';
			$instance['border_radius']   	= ! empty( $new_instance['border_radius'] ) ? strip_tags( $new_instance['border_radius'] ) : '';
			$instance['border_color']   	= ! empty( $new_instance['border_color'] ) ? sanitize_hex_color( $new_instance['border_color'] ) : '';
			$instance['bg_color']   	    = ! empty( $new_instance['bg_color'] ) ? sanitize_hex_color( $new_instance['bg_color'] ) : '';
			$instance['color']   	        = ! empty( $new_instance['color'] ) ? sanitize_hex_color( $new_instance['color'] ) : '';
			$instance['twitter_username']   = ! empty( $new_instance['twitter_username'] ) ? sanitize_text_field( $new_instance['twitter_username'] ) : '';
			$instance['social_name']        = empty( $new_instance[ 'social_name' ] ) ? 0 : 1;
			$instance['social_share'] 		= $new_instance['social_share'];
			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 * @since 1.0.0
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {

			$instance = wp_parse_args( ( array ) $instance, array(
				'title'           	 => esc_attr__( 'Please share this', 'ocean-extra' ),
				'style' 	  		 => esc_html__( 'Minimal', 'ocean-extra' ),
				'border_radius'   	 => '',
				'border_color'   	 => '',
				'bg_color'   	     => '',
				'color'   	         => '',
				'twitter_username'   => '',
				'social_name'        => '',
				'social_share' 	 	 => array('twitter', 'facebook', 'googleplus', 'pinterest', 'linkedin', 'viber', 'vk', 'reddit', 'tumblr', 'viadeo'),
			) ); ?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'ocean-extra' ); ?>:</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php esc_html_e( 'Style:', 'ocean-extra' ); ?></label>
				<select class='widefat' name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>">
					<option value="minimal" <?php selected( $instance['style'], 'minimal' ) ?>><?php esc_html_e( 'Minimal', 'ocean-extra' ); ?></option>
					<option value="colored" <?php selected( $instance['style'], 'colored' ) ?>><?php esc_html_e( 'Colored', 'ocean-extra' ); ?></option>
					<option value="dark" <?php selected( $instance['style'], 'dark' ) ?>><?php esc_html_e( 'Dark', 'ocean-extra' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'border_radius' ) ); ?>"><?php esc_html_e( 'Border Radius', 'ocean-extra' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'border_radius' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_radius' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['border_radius'] ); ?>" />
				<small><?php esc_html_e( 'Example:', 'ocean-extra' ); ?> 4px</small>
			</p>

			<p>
				<label class="color-label" for="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>"><?php esc_html_e( 'Minimal Style: Border Color', 'ocean-extra' ); ?></label>
				<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_color' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['border_color'] ); ?>" />
			</p>

			<p>
				<label class="color-label" for="<?php echo esc_attr( $this->get_field_id( 'bg_color' ) ); ?>"><?php esc_html_e( 'Minimal Style: Background Color', 'ocean-extra' ); ?></label>
				<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'bg_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bg_color' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['bg_color'] ); ?>" />
			</p>

			<p>
				<label class="color-label" for="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>"><?php esc_html_e( 'Minimal Style: Color', 'ocean-extra' ); ?></label>
				<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'color' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['color'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'twitter_username' ) ); ?>"><?php esc_html_e( 'Twitter Username', 'ocean-extra' ); ?>:</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'twitter_username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'twitter_username' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['twitter_username'] ); ?>" />
			</p>

			<p>
				<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'social_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'social_name' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['social_name'], 1 ); ?> />
				<label for="<?php echo $this->get_field_id( 'social_name' ); ?>"><?php esc_html_e( 'Show Social Name', 'ocean-extra' ); ?></label>
			</p>

			<h3><?php esc_html_e( 'Social Share','ocean-extra' ); ?></h3>
			<?php
			// Social array
			$display_share = $this->social_array();
			// Loop through social share to display inputs
			foreach( $display_share as $key => $val ) { ?>
			<p>
				<input class="checkbox" id="<?php echo $this->get_field_id("social_share") . $key; ?>" name="<?php echo $this->get_field_name("social_share"); ?>[]" type="checkbox" value="<?php echo $key; ?>" <?php checked(in_array($key, (array) $instance["social_share"])); ?> />
				<label for="<?php echo $this->get_field_id("social_share") . $key; ?>"><?php echo $val['name']; ?></label>
			</p>
			<?php }

		}

		/**
		 * Colors
		 *
		 * @since 1.3.8
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function colors( $args, $instance ) {
			// get the widget ID
			$id = $args['widget_id'];

			// Define vars
			$border_color       = isset( $instance['border_color'] ) ? sanitize_hex_color( $instance['border_color'] ) : '';
			$bg_color           = isset( $instance['bg_color'] ) ? sanitize_hex_color( $instance['bg_color'] ) : '';
			$color              = isset( $instance['color'] ) ? sanitize_hex_color( $instance['color'] ) : '';

			if ( $bg_color
				|| $color
				|| $border_color ) : ?>
				<style>
					#<?php echo $id; ?>.widget-oceanwp-social-share ul li a {
						<?php if ( $bg_color ) { echo 'background-color:' . $bg_color; } ?>;
						<?php if ( $color ) { echo 'color:' . $color; } ?>;
						<?php if ( $border_color ) { echo 'border-color:' . $border_color; } ?>;
					}
				</style>
			<?php endif; ?>

		<?php
		}

        /**
         * Scripts
         */
        public function scripts() {
            // Load only if the widget is used
            if ( is_active_widget( '', '', 'ocean_social_share' ) ) {
            	wp_enqueue_script( 'oe-social-share', OE_URL . 'includes/widgets/js/share.min.js', array( 'jquery' ), false, true );
            }
        }

	}
}
register_widget( 'Ocean_Extra_Social_Share_Widget' );