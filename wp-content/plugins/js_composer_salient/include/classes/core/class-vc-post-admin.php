<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Ability to interact with post data.
 *
 * @since 4.4
 */
class Vc_Post_Admin {
	/**
	 * Add hooks required to save, update and manipulate post
	 */
	public function init() {
		// Called in BE
		add_action( 'save_post', array(
			$this,
			'save',
		) );

		// Called in FE
		add_action( 'wp_ajax_vc_save', array(
			$this,
			'saveAjaxFe',
		) );
	}

	/**
	 * @throws \Exception
	 */
	public function saveAjaxFe() {
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();

		$post_id = intval( vc_post_param( 'post_id' ) );
		if ( $post_id > 0 ) {
			ob_start();

			// Update post_content, title and etc.
			// post_title
			// content
			// post_status
			if ( isset( $_POST['content'] ) ) {
				$post = get_post( $post_id );
				/*nectar addition*/
				if( vc_post_param( 'nectar_post_type' ) == 'portfolio' ) {
						update_post_meta( $post_id, '_nectar_portfolio_extra_content', stripslashes( vc_post_param( 'content' ) ) );
				} else {
						$post->post_content = stripslashes( vc_post_param( 'content' ) );
				}
				/*nectar addition end*/
				$post_status = vc_post_param( 'post_status' );
				$post_title = vc_post_param( 'post_title' );
				if ( null !== $post_title ) {
					$post->post_title = $post_title;
				}
				kses_remove_filters();
				remove_filter( 'content_save_pre', 'balanceTags', 50 );
				if ( $post_status && 'publish' === $post_status ) {
					if ( vc_user_access()->wpAll( array(
						get_post_type_object( $post->post_type )->cap->publish_posts,
						$post_id,
					) )->get() ) {
						if ( 'private' !== $post->post_status && 'future' !== $post->post_status ) {
							$post->post_status = 'publish';
						}
					} else {
						$post->post_status = 'pending';
					}
				}

				wp_update_post( $post );
				$this->setPostMeta( $post_id );
			}

			visual_composer()->buildShortcodesCustomCss( $post_id );
			wp_cache_flush();
			ob_clean();

			wp_send_json_success();
		}

		wp_send_json_error();
	}

	/**
	 * Save generated shortcodes, html and WPBakery Page Builder status in posts meta.
	 *
	 * @access public
	 * @since 4.4
	 *
	 * @param $post_id - current post id
	 *
	 * @return void
	 */
	public function save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || vc_is_inline() ) {
			return;
		}

		$this->setPostMeta( $post_id );
	}

	/**
	 * Saves VC Backend editor meta box visibility status.
	 *
	 * If post param 'wpb_vc_js_status' set to true, then methods adds/updated post
	 * meta option with tag '_wpb_vc_js_status'.
	 * @since 4.4
	 *
	 * @param $post_id
	 */
	public function setJsStatus( $post_id ) {
		$value = vc_post_param( 'wpb_vc_js_status' );
		if ( null !== $value ) {
			// Add value
			if ( '' === get_post_meta( $post_id, '_wpb_vc_js_status' ) ) {
				add_post_meta( $post_id, '_wpb_vc_js_status', $value, true );
			} // Update value
			elseif ( get_post_meta( $post_id, '_wpb_vc_js_status', true ) != $value ) {
				update_post_meta( $post_id, '_wpb_vc_js_status', $value );
			} // Delete value
			elseif ( '' === $value ) {
				delete_post_meta( $post_id, '_wpb_vc_js_status', get_post_meta( $post_id, '_wpb_vc_js_status', true ) );
			}
		}
	}

	/**
	 * Saves VC interface version which is used for building post content.
	 * @deprecated not needed anywhere
	 * @since 4.4
	 * @todo check is it used everywhere and is it needed?!
	 * @param $post_id
	 */
	public function setInterfaceVersion( $post_id ) {
		_deprecated_function( '\Vc_Post_Admin::setInterfaceVersion', '4.4', '' );
		if ( null !== ( $value = vc_post_param( 'wpb_vc_js_interface_version' ) ) ) {
			update_post_meta( $post_id, '_wpb_vc_js_interface_version', $value );
		}
	}

	/**
	 * Set Post Settings for VC.
	 *
	 * It is possible to add any data to post settings by adding filter with tag 'vc_hooks_vc_post_settings'.
	 * @since 4.4
	 * vc_filter: vc_hooks_vc_post_settings - hook to override post meta settings for WPBakery Page Builder (used in grid for
	 *     example)
	 *
	 * @param $post_id
	 */
	public function setSettings( $post_id ) {
		$settings = array();
		$settings = apply_filters( 'vc_hooks_vc_post_settings', $settings, $post_id, get_post( $post_id ) );
		if ( is_array( $settings ) && ! empty( $settings ) ) {
			update_post_meta( $post_id, '_vc_post_settings', $settings );
		} else {
			delete_post_meta( $post_id, '_vc_post_settings' );
		}
	}

	/**
	 * @param $post_id
	 */
	protected function setPostMeta( $post_id ) {
		if ( ! vc_user_access()->wpAny( array(
			'edit_post',
			$post_id,
		) )->get() ) {
			return;
		}
		$this->setJsStatus( $post_id );
		if ( ! ( isset( $_POST['wp-preview'] ) && 'dopreview' === $_POST['wp-preview'] ) ) {

			$this->setSettings( $post_id );
		}
		/**
		 * vc_filter: vc_base_save_post_custom_css
		 * @since 4.4
		 */
		$post_custom_css = apply_filters( 'vc_base_save_post_custom_css', vc_post_param( 'vc_post_custom_css' ) );
		if ( null !== $post_custom_css && empty( $post_custom_css ) ) {
			delete_post_meta( $post_id, '_wpb_post_custom_css' );
		} elseif ( null !== $post_custom_css ) {
			$post_custom_css = strip_tags( $post_custom_css );
			update_post_meta( $post_id, '_wpb_post_custom_css', $post_custom_css );
		}
		visual_composer()->buildShortcodesCustomCss( $post_id );
	}
}
