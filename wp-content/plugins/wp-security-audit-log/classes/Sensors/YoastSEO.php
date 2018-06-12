<?php
/**
 * Sensor: Yoast SEO
 *
 * Yoast SEO sensor file.
 *
 * @package Wsal
 * @since 3.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Support for Yoast SEO Plugin.
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_YoastSEO extends WSAL_AbstractSensor {

	/**
	 * Post ID.
	 *
	 * @var int
	 */
	private $post_id = 0;

	/**
	 * Post Object.
	 *
	 * @var WP_Post
	 */
	private $post;

	/**
	 * SEO Post Data.
	 *
	 * @var array
	 */
	private $post_seo_data = array(
		'_yoast_wpseo_title' => '',
		'_yoast_wpseo_metadesc' => '',
		'_yoast_wpseo_focuskw' => '',
		'_yst_is_cornerstone' => '',
		'_yoast_wpseo_meta-robots-noindex' => '',
		'_yoast_wpseo_meta-robots-nofollow' => '',
		'_yoast_wpseo_meta-robots-adv' => '',
		'_yoast_wpseo_canonical' => '',
	);

	/**
	 * Listening to events using hooks.
	 */
	public function HookEvents() {
		// Check if Yoast SEO file exists.
		if ( ! is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
			return false;
		}

		// If user can edit post then hook this function.
		if ( current_user_can( 'edit_posts' ) ) {
			add_action( 'admin_init', array( $this, 'event_admin_init' ) );
		}

		// Yoast SEO option alerts.
		add_action( 'updated_option', array( $this, 'yoast_options_trigger' ), 10, 3 );
	}

	/**
	 * Method: Admin Init Event.
	 */
	public function event_admin_init() {
		// Load old data, if applicable.
		$this->retrieve_post_data();

		// Check for settings change.
		$this->check_seo_data_change();
	}

	/**
	 * Method: Retrieve Post ID.
	 */
	protected function retrieve_post_data() {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		if ( isset( $post_array ) && isset( $post_array['post_ID'] )
			&& ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)
			&& ! ( isset( $post_array['action'] ) && 'autosave' === $post_array['action'] )
		) {
			$this->post_id = intval( $post_array['post_ID'] );
			$this->post = get_post( $this->post_id );
			$this->set_post_seo_data();
		}
	}

	/**
	 * Method: Set Post SEO Data.
	 */
	private function set_post_seo_data() {
		// Set post SEO meta data.
		$this->post_seo_data = array(
			'_yoast_wpseo_title' => get_post_meta( $this->post_id, '_yoast_wpseo_title', true ),
			'_yoast_wpseo_metadesc' => get_post_meta( $this->post_id, '_yoast_wpseo_metadesc', true ),
			'_yoast_wpseo_focuskw' => get_post_meta( $this->post_id, '_yoast_wpseo_focuskw', true ),
			'_yst_is_cornerstone' => get_post_meta( $this->post_id, '_yst_is_cornerstone', true ),
			'_yoast_wpseo_meta-robots-noindex' => get_post_meta( $this->post_id, '_yoast_wpseo_meta-robots-noindex', true ),
			'_yoast_wpseo_meta-robots-nofollow' => get_post_meta( $this->post_id, '_yoast_wpseo_meta-robots-nofollow', true ),
			'_yoast_wpseo_meta-robots-adv' => get_post_meta( $this->post_id, '_yoast_wpseo_meta-robots-adv', true ),
			'_yoast_wpseo_canonical' => get_post_meta( $this->post_id, '_yoast_wpseo_canonical', true ),
		);
	}

	/**
	 * Method: Get Post SEO Data.
	 *
	 * @param string $key – Meta Key.
	 * @return mix
	 */
	protected function get_post_seo_data( $key = '' ) {
		// If empty key then return false.
		if ( empty( $key ) ) {
			return false;
		}

		// Set prefix of meta data.
		$prefix = '_yoast_wpseo_';

		// Check prefix.
		if ( 'is_cornerstone' === $key ) {
			$prefix = '_yst_';
		}

		// Option to retrieve.
		$option = $prefix . $key;

		// If key exists and is not empty then return value.
		if ( isset( $this->post_seo_data[ $option ] ) && ! empty( $this->post_seo_data[ $option ] ) ) {
			return $this->post_seo_data[ $option ];
		} else {
			// Return false if key doesn't exists or its value is empty.
			return false;
		}
	}

	/**
	 * Get editor link.
	 *
	 * @param stdClass $post_id - Post id.
	 * @return array $editor_link - Name and value link.
	 */
	private function get_editor_link( $post_id ) {
		$value = get_edit_post_link( $post_id );
		$editor_link = array(
			'name' => 'EditorLinkPost',
			'value' => $value,
		);
		return $editor_link;
	}

	/**
	 * Method: Detect Post SEO Data Change.
	 */
	protected function check_seo_data_change() {
		// Set filter input args.
		$filter_input_args = array(
			'post_ID' => FILTER_VALIDATE_INT,
			'_wpnonce' => FILTER_SANITIZE_STRING,
			'action' => FILTER_SANITIZE_STRING,
			'yoast_wpseo_title' => FILTER_SANITIZE_STRING,
			'yoast_wpseo_metadesc' => FILTER_SANITIZE_STRING,
			'yoast_wpseo_focuskw' => FILTER_SANITIZE_STRING,
			'_yst_is_cornerstone' => FILTER_VALIDATE_INT,
			'yoast_wpseo_meta-robots-noindex' => FILTER_VALIDATE_INT,
			'yoast_wpseo_meta-robots-nofollow' => FILTER_VALIDATE_INT,
			'yoast_wpseo_meta-robots-adv' => array(
				'flags' => FILTER_REQUIRE_ARRAY,
			),
			'yoast_wpseo_canonical' => FILTER_VALIDATE_URL,
		);

		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		if ( isset( $post_array['post_ID'] )
			&& 'editpost' === $post_array['action']
			&& isset( $post_array['_wpnonce'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			// Check SEO data changes and alert if changed.
			$this->check_title_change( $post_array['yoast_wpseo_title'] ); // Title.
			$this->check_desc_change( $post_array['yoast_wpseo_metadesc'] ); // Meta description.
			$this->check_robots_index_change( $post_array['yoast_wpseo_meta-robots-noindex'] ); // Meta Robots Index.
			$this->check_robots_follow_change( $post_array['yoast_wpseo_meta-robots-nofollow'] ); // Meta Robots Follow.
			$this->check_robots_advanced_change( $post_array['yoast_wpseo_meta-robots-adv'] ); // Meta Robots Advanced.
			$this->check_canonical_url_change( $post_array['yoast_wpseo_canonical'] ); // Canonical URL.
			$this->check_focus_keys_change( $post_array['yoast_wpseo_focuskw'] ); // Focus keywords.
			$this->check_cornerstone_change( $post_array['_yst_is_cornerstone'] ); // Cornerstone.
		}
	}

	/**
	 * Method: Check SEO Title Change.
	 *
	 * @param string $title – Changed SEO Title.
	 */
	protected function check_title_change( $title ) {
		// Get old title value.
		$old_title = $this->get_post_seo_data( 'title' );

		// If old and new values are empty then don't log the alert.
		if ( empty( $old_title ) && empty( $title ) ) {
			return;
		}

		// If title is changed then log alert.
		if ( $old_title !== $title ) {
			$editor_link = $this->get_editor_link( $this->post_id );
			$this->plugin->alerts->Trigger(
				8801, array(
					'PostID' => $this->post->ID,
					'PostType' => $this->post->post_type,
					'PostTitle' => $this->post->post_title,
					'PostStatus' => $this->post->post_status,
					'PostDate' => $this->post->post_date,
					'PostUrl' => get_permalink( $this->post->ID ),
					'OldSEOTitle' => $old_title,
					'NewSEOTitle' => $title,
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}
	}

	/**
	 * Method: Check SEO Meta Description Change.
	 *
	 * @param string $desc – Changed SEO Meta Description.
	 */
	protected function check_desc_change( $desc ) {
		// Get old desc value.
		$old_desc = $this->get_post_seo_data( 'metadesc' );

		// If old and new values are empty then don't log the alert.
		if ( empty( $old_desc ) && empty( $desc ) ) {
			return;
		}

		// If desc is changed then log alert.
		if ( $old_desc !== $desc ) {
			$editor_link = $this->get_editor_link( $this->post_id );
			$this->plugin->alerts->Trigger(
				8802, array(
					'PostID' => $this->post->ID,
					'PostType' => $this->post->post_type,
					'PostTitle' => $this->post->post_title,
					'PostStatus' => $this->post->post_status,
					'PostDate' => $this->post->post_date,
					'PostUrl' => get_permalink( $this->post->ID ),
					'old_desc' => $old_desc,
					'new_desc' => $desc,
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}
	}

	/**
	 * Method: Check Meta Robots Index Change.
	 *
	 * @param string $index – Changed Meta Robots Index.
	 */
	protected function check_robots_index_change( $index ) {
		// Get old title value.
		$old_index = (int) $this->get_post_seo_data( 'meta-robots-noindex' );

		if ( 1 === $old_index ) {
			$old_index = 'No';
		} else {
			$old_index = 'Yes';
		}

		if ( 1 === $index ) {
			$index = 'No';
		} else {
			$index = 'Yes';
		}

		// If setting is changed then log alert.
		if ( $old_index !== $index ) {
			$editor_link = $this->get_editor_link( $this->post_id );
			$this->plugin->alerts->Trigger(
				8803, array(
					'PostID' => $this->post->ID,
					'PostType' => $this->post->post_type,
					'PostTitle' => $this->post->post_title,
					'PostStatus' => $this->post->post_status,
					'PostDate' => $this->post->post_date,
					'PostUrl' => get_permalink( $this->post->ID ),
					'OldStatus' => $old_index,
					'NewStatus' => $index,
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}
	}

	/**
	 * Method: Check Meta Robots Follow Change.
	 *
	 * @param string $follow – Changed Meta Robots Follow.
	 */
	protected function check_robots_follow_change( $follow ) {
		// Get old title value.
		$old_follow = (int) $this->get_post_seo_data( 'meta-robots-nofollow' );

		if ( 1 === $old_follow ) {
			$old_follow = 'Disabled';
		} else {
			$old_follow = 'Enabled';
		}

		if ( 1 === $follow ) {
			$follow = 'Disabled';
		} else {
			$follow = 'Enabled';
		}

		// If setting is changed then log alert.
		if ( $old_follow !== $follow ) {
			$editor_link = $this->get_editor_link( $this->post_id );
			$this->plugin->alerts->Trigger(
				8804, array(
					'PostID' => $this->post->ID,
					'PostType' => $this->post->post_type,
					'PostTitle' => $this->post->post_title,
					'PostStatus' => $this->post->post_status,
					'PostDate' => $this->post->post_date,
					'PostUrl' => get_permalink( $this->post->ID ),
					'NewStatus' => $follow,
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}
	}

	/**
	 * Method: Check Meta Robots Advanced Change.
	 *
	 * @param array $advanced – Advanced array.
	 */
	protected function check_robots_advanced_change( $advanced ) {
		// Convert to string.
		if ( is_array( $advanced ) ) {
			$advanced = implode( ',', $advanced );
		}

		// Get old title value.
		$old_adv = $this->get_post_seo_data( 'meta-robots-adv' );

		// If old and new values are empty then don't log the alert.
		if ( empty( $old_title ) && '-' === $advanced ) {
			return;
		}

		// If setting is changed then log alert.
		if ( $old_adv !== $advanced ) {
			$editor_link = $this->get_editor_link( $this->post_id );
			$this->plugin->alerts->Trigger(
				8805, array(
					'PostID' => $this->post->ID,
					'PostType' => $this->post->post_type,
					'PostTitle' => $this->post->post_title,
					'PostStatus' => $this->post->post_status,
					'PostDate' => $this->post->post_date,
					'PostUrl' => get_permalink( $this->post->ID ),
					'NewStatus' => $advanced,
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}
	}

	/**
	 * Method: Check Canonical URL Change.
	 *
	 * @param string $canonical_url – Changed Canonical URL.
	 */
	protected function check_canonical_url_change( $canonical_url ) {
		// Get old title value.
		$old_url = $this->get_post_seo_data( 'canonical' );

		// If title is changed then log alert.
		if ( $old_url !== $canonical_url ) {
			$editor_link = $this->get_editor_link( $this->post_id );
			$this->plugin->alerts->Trigger(
				8806, array(
					'PostID' => $this->post->ID,
					'PostType' => $this->post->post_type,
					'PostTitle' => $this->post->post_title,
					'PostStatus' => $this->post->post_status,
					'PostDate' => $this->post->post_date,
					'PostUrl' => get_permalink( $this->post->ID ),
					'OldCanonicalUrl' => $old_url,
					'NewCanonicalUrl' => $canonical_url,
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}
	}

	/**
	 * Method: Check Focus Keywords Change.
	 *
	 * @param string $focus_keys – Changed Focus Keywords.
	 */
	protected function check_focus_keys_change( $focus_keys ) {
		// Get old title value.
		$old_focus_keys = $this->get_post_seo_data( 'focuskw' );

		// If old and new values are empty then don't log the alert.
		if ( empty( $old_focus_keys ) && empty( $focus_keys ) ) {
			return;
		}

		// If title is changed then log alert.
		if ( $old_focus_keys !== $focus_keys ) {
			$editor_link = $this->get_editor_link( $this->post_id );
			$this->plugin->alerts->Trigger(
				8807, array(
					'PostID' => $this->post->ID,
					'PostType' => $this->post->post_type,
					'PostTitle' => $this->post->post_title,
					'PostStatus' => $this->post->post_status,
					'PostDate' => $this->post->post_date,
					'PostUrl' => get_permalink( $this->post->ID ),
					'old_keywords' => $old_focus_keys,
					'new_keywords' => $focus_keys,
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}
	}

	/**
	 * Method: Check Cornerstone Change.
	 *
	 * @param string $cornerstone – Changed Cornerstone.
	 */
	protected function check_cornerstone_change( $cornerstone ) {
		// Get old title value.
		$old_cornerstone = (int) $this->get_post_seo_data( 'is_cornerstone' );
		$cornerstone = (int) $cornerstone;

		if ( 1 === $cornerstone ) {
			$alert_status = 'Enabled';
		} else {
			$alert_status = 'Disabled';
		}

		// If setting is changed then log alert.
		if ( $old_cornerstone !== $cornerstone ) {
			$editor_link = $this->get_editor_link( $this->post_id );
			$this->plugin->alerts->Trigger(
				8808, array(
					'PostID' => $this->post->ID,
					'PostType' => $this->post->post_type,
					'PostTitle' => $this->post->post_title,
					'PostStatus' => $this->post->post_status,
					'PostDate' => $this->post->post_date,
					'PostUrl' => get_permalink( $this->post->ID ),
					'Status' => $alert_status,
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}
	}

	/**
	 * Method: Yoast SEO options trigger.
	 *
	 * @param string $option – Option name.
	 * @param mix    $old_value – Option old value.
	 * @param mix    $new_value – Option new value.
	 */
	public function yoast_options_trigger( $option, $old_value, $new_value ) {
		// Detect the SEO option.
		if ( 'wpseo_titles' === $option || 'wpseo' === $option ) {
			// WPSEO Title Alerts.
			if ( 'wpseo_titles' === $option ) {
				// Title Separator.
				if ( $old_value['separator'] !== $new_value['separator'] ) {
					$this->yoast_setting_change_alert( 'separator', $old_value['separator'], $new_value['separator'] );
				}

				// Homepage Title.
				if ( $old_value['title-home-wpseo'] !== $new_value['title-home-wpseo'] ) {
					$this->yoast_setting_change_alert( 'title-home-wpseo', $old_value['title-home-wpseo'], $new_value['title-home-wpseo'] );
				}

				// Homepage Meta Description.
				if ( $old_value['metadesc-home-wpseo'] !== $new_value['metadesc-home-wpseo'] ) {
					$this->yoast_setting_change_alert( 'metadesc-home-wpseo', $old_value['metadesc-home-wpseo'], $new_value['metadesc-home-wpseo'] );
				}

				// Company or Person.
				if ( $old_value['company_or_person'] !== $new_value['company_or_person'] ) {
					$this->yoast_setting_change_alert( 'company_or_person', $old_value['company_or_person'], $new_value['company_or_person'] );
				}

				// Get public post types.
				$post_types = get_post_types( array( 'public' => true ) );

				// For each post type check show, title, and description changes.
				foreach ( $post_types as $type ) {
					if ( isset( $old_value[ "noindex-$type" ] ) ) {
						// Show Post Type in search results.
						if ( $old_value[ "noindex-$type" ] !== $new_value[ "noindex-$type" ] ) {
							$this->yoast_setting_switch_alert( "noindex-$type", $new_value[ "noindex-$type" ] );
						}

						// Post Type Title Template.
						if ( $old_value[ "title-$type" ] !== $new_value[ "title-$type" ] ) {
							$this->yoast_setting_change_alert( "title-$type", $old_value[ "title-$type" ], $new_value[ "title-$type" ] );
						}

						// Post Type Meta Description Template.
						if ( $old_value[ "metadesc-$type" ] !== $new_value[ "metadesc-$type" ] ) {
							$this->yoast_setting_change_alert( "metadesc-$type", $old_value[ "metadesc-$type" ], $new_value[ "metadesc-$type" ] );
						}

						// Show Date.
						if ( $old_value[ "showdate-$type" ] !== $new_value[ "showdate-$type" ] ) {
							$this->yoast_setting_switch_alert( "showdate-$type", $new_value[ "showdate-$type" ] );
						}

						// Show Meta box.
						if ( $old_value[ "display-metabox-pt-$type" ] !== $new_value[ "display-metabox-pt-$type" ] ) {
							$this->yoast_setting_switch_alert( "display-metabox-pt-$type", $new_value[ "display-metabox-pt-$type" ] );
						}
					}
				}
			}

			// Webmaster URL alerts.
			if ( 'wpseo' === $option ) {
				// SEO analysis.
				if ( $old_value['keyword_analysis_active'] !== $new_value['keyword_analysis_active'] ) {
					$this->yoast_setting_switch_alert( 'keyword_analysis_active', $new_value['keyword_analysis_active'] );
				}

				// Readability analysis.
				if ( $old_value['content_analysis_active'] !== $new_value['content_analysis_active'] ) {
					$this->yoast_setting_switch_alert( 'content_analysis_active', $new_value['content_analysis_active'] );
				}

				// Cornerstone Content.
				if ( $old_value['enable_cornerstone_content'] !== $new_value['enable_cornerstone_content'] ) {
					$this->yoast_setting_switch_alert( 'enable_cornerstone_content', $new_value['enable_cornerstone_content'] );
				}

				// Text Link Counter.
				if ( $old_value['enable_text_link_counter'] !== $new_value['enable_text_link_counter'] ) {
					$this->yoast_setting_switch_alert( 'enable_text_link_counter', $new_value['enable_text_link_counter'] );
				}

				// XML Sitemaps.
				if ( $old_value['enable_xml_sitemap'] !== $new_value['enable_xml_sitemap'] ) {
					$this->yoast_setting_switch_alert( 'enable_xml_sitemap', $new_value['enable_xml_sitemap'] );
				}

				// Ryte integration.
				if ( $old_value['onpage_indexability'] !== $new_value['onpage_indexability'] ) {
					$this->yoast_setting_switch_alert( 'onpage_indexability', $new_value['onpage_indexability'] );
				}

				// Admin bar menu.
				if ( $old_value['enable_admin_bar_menu'] !== $new_value['enable_admin_bar_menu'] ) {
					$this->yoast_setting_switch_alert( 'enable_admin_bar_menu', $new_value['enable_admin_bar_menu'] );
				}

				// Advanced settings for authors.
				if ( $old_value['disableadvanced_meta'] !== $new_value['disableadvanced_meta'] ) {
					$this->yoast_setting_switch_alert( 'disableadvanced_meta', $new_value['disableadvanced_meta'] );
				}
			}
		}
	}

	/**
	 * Method: Trigger Yoast Setting Change Alerts.
	 *
	 * @param string $key – Setting key.
	 * @param string $old_value – Old setting value.
	 * @param string $new_value – New setting value.
	 */
	private function yoast_setting_change_alert( $key, $old_value, $new_value ) {
		// Return if key is empty.
		if ( empty( $key ) ) {
			return;
		}

		// Return if both old and new values are empty.
		if ( empty( $old_value ) && empty( $new_value ) ) {
			return;
		}

		// Alert arguments.
		$alert_args = array(
			'old' => $old_value, // Old value.
			'new' => $new_value, // New value.
		);

		// Find title-* in the key.
		if ( false !== strpos( $key, 'title-' ) ) {
			$seo_post_type = str_replace( 'title-', '', $key );
			$seo_post_type = ucfirst( $seo_post_type );
			$seo_post_type .= 's';

			// Set alert meta data.
			$alert_args['SEOPostType'] = $seo_post_type;
		}

		// Find metadesc-* in the key.
		if ( false !== strpos( $key, 'metadesc-' ) ) {
			$seo_post_type = str_replace( 'metadesc-', '', $key );
			$seo_post_type = ucfirst( $seo_post_type );
			$seo_post_type .= 's';

			// Set alert meta data.
			$alert_args['SEOPostType'] = $seo_post_type;
		}

		// Set alert code to null initially.
		$alert_code = null;

		// Detect alert code for setting.
		switch ( $key ) {
			case 'separator':
				$alert_code = 8809;
				break;

			case 'title-home-wpseo':
				$alert_code = 8810;
				break;

			case 'metadesc-home-wpseo':
				$alert_code = 8811;
				break;

			case 'company_or_person':
				$alert_code = 8812;
				break;

			case strpos( $key, 'title-' ):
				$alert_code = 8814;
				break;

			case strpos( $key, 'metadesc-' ):
				$alert_code = 8822;
				break;

			default:
				break;
		}

		// Trigger the alert.
		if ( ! empty( $alert_code ) ) {
			$this->plugin->alerts->Trigger( $alert_code, $alert_args );
		}
	}

	/**
	 * Method: Trigger Yoast Enable/Disable Setting Alerts.
	 *
	 * @param string $key – Setting index to alert.
	 * @param mix    $new_value – Setting new value.
	 */
	private function yoast_setting_switch_alert( $key, $new_value ) {
		// If key is empty, then return.
		if ( empty( $key ) ) {
			return;
		}

		// Check and set status.
		$status = (int) $new_value;

		// Alert arguments.
		$alert_args = array();

		// Find noindex-* in the key.
		if ( false !== strpos( $key, 'noindex-' ) ) {
			$seo_post_type = str_replace( 'noindex-', '', $key );
			$seo_post_type = ucfirst( $seo_post_type );
			$seo_post_type .= 's';

			// Set alert meta data.
			$alert_args['SEOPostType'] = $seo_post_type;
			$status = ( 1 === $status ) ? 0 : 1;
		}

		// Find showdate-* in the key.
		if ( false !== strpos( $key, 'showdate-' ) ) {
			$seo_post_type = str_replace( 'showdate-', '', $key );
			$seo_post_type = ucfirst( $seo_post_type );
			$seo_post_type .= 's';

			// Set alert meta data.
			$alert_args['SEOPostType'] = $seo_post_type;
		}

		// Find display-metabox-pt-* in the key.
		if ( false !== strpos( $key, 'display-metabox-pt-' ) ) {
			$seo_post_type = str_replace( 'display-metabox-pt-', '', $key );
			$seo_post_type = ucfirst( $seo_post_type );
			$seo_post_type .= 's';

			// Set alert meta data.
			$alert_args['SEOPostType'] = $seo_post_type;
		}

		$status = ( 1 === $status ) ? 'Enabled' : 'Disabled';
		$alert_args['Status'] = $status;

		// Set alert code to NULL initially.
		$alert_code = null;

		// Add switch case to set the alert code.
		switch ( $key ) {
			case strpos( $key, 'noindex-' ):
				$alert_code = 8813;
				break;

			case 'keyword_analysis_active':
				$alert_code = 8815;
				break;

			case 'content_analysis_active':
				$alert_code = 8816;
				break;

			case 'enable_cornerstone_content':
				$alert_code = 8817;
				break;

			case 'enable_text_link_counter':
				$alert_code = 8818;
				break;

			case 'enable_xml_sitemap':
				$alert_code = 8819;
				break;

			case 'onpage_indexability':
				$alert_code = 8820;
				break;

			case 'enable_admin_bar_menu':
				$alert_code = 8821;
				break;

			case strpos( $key, 'showdate-' ):
				$alert_code = 8823;
				break;

			case strpos( $key, 'display-metabox-pt-' ):
				$alert_code = 8824;
				break;

			case strpos( $key, 'disableadvanced_meta' ):
				$alert_code = 8825;
				break;

			default:
				break;
		}

		// Trigger the alert.
		if ( ! empty( $alert_code ) ) {
			$this->plugin->alerts->Trigger( $alert_code, $alert_args );
		}
	}
}

