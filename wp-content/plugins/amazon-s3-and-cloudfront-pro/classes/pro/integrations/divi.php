<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Integrations;

class Divi extends Integration {

	/**
	 * Is installed?
	 *
	 * @return bool
	 */
	public function is_installed() {
		$theme_info = wp_get_theme();

		if ( ! empty( $theme_info ) && is_a( $theme_info, 'WP_Theme' ) && ( $theme_info->get( 'Name' ) ) === 'Divi' ) {
			return true;
		}

		if ( is_child_theme() ) {
			$parent_info = $theme_info->parent();

			if ( ! empty( $parent_info ) && is_a( $parent_info, 'WP_Theme' ) && esc_html( $parent_info->get( 'Name' ) ) === 'Divi' ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Init integration.
	 */
	public function init() {
		add_filter( 'et_fb_load_raw_post_content', function ( $content ) {
			$content = apply_filters( 'as3cf_filter_post_local_to_s3', $content ); // Backwards compatibility

			return apply_filters( 'as3cf_filter_post_local_to_provider', $content );
		} );

		// Global Modules reset their filtered background image URLs, so let's fix that.
		if ( defined( 'ET_BUILDER_LAYOUT_POST_TYPE' ) ) {
			add_filter( 'the_posts', array( $this, 'the_posts' ), 10, 2 );
		}
	}

	/**
	 * Handler for the 'the_posts' filter that runs local to provider URL filtering on Divi pages.
	 *
	 * @param array|\WP_Post $posts
	 * @param \WP_Query      $query
	 *
	 * @return array
	 */
	public function the_posts( $posts, $query ) {
		if (
			defined( 'ET_BUILDER_LAYOUT_POST_TYPE' ) &&
			! empty( $posts ) &&
			! empty( $query ) &&
			is_a( $query, 'WP_Query' ) &&
			! empty( $query->query_vars['post_type'] ) &&
			ET_BUILDER_LAYOUT_POST_TYPE === $query->query_vars['post_type']
		) {
			if ( is_array( $posts ) ) {
				foreach ( $posts as $idx => $post ) {
					$posts[ $idx ] = $this->the_posts( $post, $query );
				}
			} elseif ( is_a( $posts, 'WP_Post' ) ) {
				$content = apply_filters( 'as3cf_filter_post_local_to_s3', $posts->post_content ); // Backwards compatibility

				$posts->post_content = apply_filters( 'as3cf_filter_post_local_to_provider', $content );
			}
		}

		return $posts;
	}
}
