<?php
/**
 * Generates and emits surrogate keys based on the current request.
 *
 * @package Pantheon_Advanced_Page_Cache
 */

namespace Pantheon_Advanced_Page_Cache;

/**
 * Generates and emits surrogate keys based on the current request.
 */
class Emitter {

	/**
	 * Current instance when set.
	 *
	 * @var Emitter
	 */
	private static $instance;

	/**
	 * REST API surrogate keys to emit.
	 *
	 * @var array
	 */
	private $rest_api_surrogate_keys = array();

	/**
	 * REST API collection endpoints.
	 *
	 * @var array
	 */
	private $rest_api_collection_endpoints = array();

	/**
	 * Header key.
	 *
	 * @var string
	 */
	const HEADER_KEY = 'Surrogate-Key';

	/**
	 * Maximum header length.
	 *
	 * @var integer
	 */
	const HEADER_MAX_LENGTH = 32512;  // 32k output buffer default on nginx, minus 256 for header name.

	/**
	 * Get a copy of the current instance.
	 *
	 * @return Emitter
	 */
	private static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Render surrogate keys after the main query has run
	 */
	public static function action_wp() {

		$keys = self::get_main_query_surrogate_keys();
		if ( ! empty( $keys ) ) {
			// @codingStandardsIgnoreStart
			@header( self::HEADER_KEY . ': ' . implode( ' ', $keys ) );
			// @codingStandardsIgnoreEnd
		}
	}

	/**
	 * Register filters to sniff surrogate keys out of REST API responses.
	 */
	public static function action_rest_api_init() {
		foreach ( get_post_types(
			array(
				'show_in_rest' => true,
			),
			'objects'
		) as $post_type ) {
			add_filter( "rest_prepare_{$post_type->name}", array( __CLASS__, 'filter_rest_prepare_post' ), 10, 3 );
			$base = ! empty( $post_type->rest_base ) ? $post_type->rest_base : $post_type->name;
			self::get_instance()->rest_api_collection_endpoints[ '/wp/v2/' . $base ] = $post_type->name;
		}
		foreach ( get_taxonomies(
			array(
				'show_in_rest' => true,
			),
			'objects'
		) as $taxonomy ) {
			add_filter( "rest_prepare_{$taxonomy->name}", array( __CLASS__, 'filter_rest_prepare_term' ), 10, 3 );
			$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;
			self::get_instance()->rest_api_collection_endpoints[ '/wp/v2/' . $base ] = $taxonomy->name;
		}
		add_filter( 'rest_prepare_comment', array( __CLASS__, 'filter_rest_prepare_comment' ), 10, 3 );
		self::get_instance()->rest_api_collection_endpoints['/wp/v2/comments'] = 'comment';
		add_filter( 'rest_prepare_user', array( __CLASS__, 'filter_rest_prepare_user' ), 10, 3 );
		add_filter( 'rest_pre_get_setting', array( __CLASS__, 'filter_rest_pre_get_setting' ), 10, 2 );
		self::get_instance()->rest_api_collection_endpoints['/wp/v2/users'] = 'user';
	}

	/**
	 * Reset surrogate keys before a REST API response is generated.
	 *
	 * @param mixed           $result  Response to replace the requested version with.
	 * @param WP_REST_Server  $server  Server instance.
	 * @param WP_REST_Request $request Request used to generate the response.
	 */
	public static function filter_rest_pre_dispatch( $result, $server, $request ) {
		if ( isset( self::get_instance()->rest_api_collection_endpoints[ $request->get_route() ] ) ) {
			self::get_instance()->rest_api_surrogate_keys[] = 'rest-' . self::get_instance()->rest_api_collection_endpoints[ $request->get_route() ] . '-collection';
		}
		return $result;
	}

	/**
	 * Render surrogate keys after a REST API response is prepared
	 *
	 * @param WP_HTTP_Response $result  Result to send to the client. Usually a WP_REST_Response.
	 * @param WP_REST_Server   $server  Server instance.
	 */
	public static function filter_rest_post_dispatch( $result, $server ) {

		$keys = self::get_rest_api_surrogate_keys();
		if ( ! empty( $keys ) ) {
			$server->send_header( self::HEADER_KEY, implode( ' ', $keys ) );
		}
		return $result;
	}

	/**
	 * Determine which posts are present in a REST API response.
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post          $post     Post object.
	 * @param WP_REST_Request  $request  Request object.
	 */
	public static function filter_rest_prepare_post( $response, $post, $request ) {
		self::get_instance()->rest_api_surrogate_keys[] = 'rest-post-' . $post->ID;
		return $response;
	}

	/**
	 * Determine which terms are present in a REST API response.
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post          $term     Term object.
	 * @param WP_REST_Request  $request  Request object.
	 */
	public static function filter_rest_prepare_term( $response, $term, $request ) {
		self::get_instance()->rest_api_surrogate_keys[] = 'rest-term-' . $term->term_id;
		return $response;
	}

	/**
	 * Determine which comments are present in a REST API response.
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Comment       $comment  The original comment object.
	 * @param WP_REST_Request  $request  Request used to generate the response.
	 */
	public static function filter_rest_prepare_comment( $response, $comment, $request ) {
		self::get_instance()->rest_api_surrogate_keys[] = 'rest-comment-' . $comment->comment_ID;
		self::get_instance()->rest_api_surrogate_keys[] = 'rest-comment-post-' . $comment->comment_post_ID;
		return $response;
	}

	/**
	 * Determine which users are present in a REST API response.
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post          $user     User object.
	 * @param WP_REST_Request  $request  Request object.
	 */
	public static function filter_rest_prepare_user( $response, $user, $request ) {
		self::get_instance()->rest_api_surrogate_keys[] = 'rest-user-' . $user->ID;
		return $response;
	}

	/**
	 * Determine which settings are present in a REST API request
	 *
	 * @param mixed  $result Value to use for the requested setting. Can be a scalar
	 *                       matching the registered schema for the setting, or null to
	 *                       follow the default get_option() behavior.
	 * @param string $name   Setting name (as shown in REST API responses).
	 */
	public static function filter_rest_pre_get_setting( $result, $name ) {
		self::get_instance()->rest_api_surrogate_keys[] = 'rest-setting-' . $name;
		return $result;
	}

	/**
	 * Get the surrogate keys to be included in this view.
	 *
	 * Surrogate keys are generated based on the main WP_Query.
	 *
	 * @return array
	 */
	public static function get_main_query_surrogate_keys() {
		global $wp_query;

		$keys = array();
		if ( is_front_page() ) {
			$keys[] = 'front';
		}
		if ( is_home() ) {
			$keys[] = 'home';
		}
		if ( is_404() ) {
			$keys[] = '404';
		}
		if ( is_feed() ) {
			$keys[] = 'feed';
		}
		if ( is_date() ) {
			$keys[] = 'date';
		}
		if ( is_paged() ) {
			$keys[] = 'paged';
		}
		if ( is_search() ) {
			$keys[] = 'search';
			if ( $wp_query->found_posts ) {
				$keys[] = 'search-results';
			} else {
				$keys[] = 'search-no-results';
			}
		}

		if ( ! empty( $wp_query->posts ) ) {
			foreach ( $wp_query->posts as $p ) {
				$keys[] = 'post-' . $p->ID;
				if ( $wp_query->is_singular() || $wp_query->is_page() ) {
					if ( post_type_supports( $p->post_type, 'author' ) ) {
						$keys[] = 'post-user-' . $p->post_author;
					}
					foreach ( get_object_taxonomies( $p ) as $tax ) {
						$terms = get_the_terms( $p->ID, $tax );
						if ( $terms && ! is_wp_error( $terms ) ) {
							foreach ( $terms as $t ) {
								$keys[] = 'post-term-' . $t->term_id;
							}
						}
					}
				}
			}
		}

		if ( is_singular() || is_page() ) {
			$keys[] = 'single';
			if ( is_attachment() ) {
				$keys[] = 'attachment';
			}
		} elseif ( is_archive() ) {
			$keys[] = 'archive';
			if ( is_post_type_archive() ) {
				$keys[] = 'post-type-archive';
			} elseif ( is_author() ) {
				$user_id = get_queried_object_id();
				if ( $user_id ) {
					$keys[] = 'user-' . $user_id;
				}
			} elseif ( is_category() || is_tag() || is_tax() ) {
				$term_id = get_queried_object_id();
				if ( $term_id ) {
					$keys[] = 'term-' . $term_id;
				}
			}
		}

		// Don't emit surrogate keys in the admin, unless defined by the filter.
		if ( is_admin() ) {
			$keys = array();
		}

		/**
		 * Customize surrogate keys sent in the header.
		 *
		 * @param array $keys Existing surrogate keys generated by the plugin.
		 */
		$keys = array_unique( $keys );
		$keys = apply_filters( 'pantheon_wp_main_query_surrogate_keys', $keys );
		$keys = array_unique( $keys );
		$keys = self::filter_huge_surrogate_keys_list( $keys );
		return $keys;
	}

	/**
	 * Get the surrogate keys to be included in this view.
	 *
	 * Surrogate keys are generated based on filters added to REST API controllers.
	 *
	 * @return array
	 */
	public static function get_rest_api_surrogate_keys() {

		/**
		 * Customize surrogate keys sent in the REST API header.
		 *
		 * @param array $keys Existing surrogate keys generated by the plugin.
		 */
		$keys = self::get_instance()->rest_api_surrogate_keys;
		$keys = array_unique( $keys );
		$keys = apply_filters( 'pantheon_wp_rest_api_surrogate_keys', $keys );
		$keys = array_unique( $keys );
		$keys = self::filter_huge_surrogate_keys_list( $keys );
		return $keys;
	}

	/**
	 * Reset surrogate keys stored on the instance.
	 */
	public static function reset_rest_api_surrogate_keys() {
		self::get_instance()->rest_api_surrogate_keys = array();
	}

	/**
	 * Filter the surrogate keys to ensure that the length doesn't exceed what nginx can handle.
	 *
	 * @param array $keys Existing surrogate keys generated by the plugin.
	 *
	 * @return array
	 */
	public static function filter_huge_surrogate_keys_list( $keys ) {
		$output = implode( ' ', $keys );
		if ( strlen( $output ) <= self::HEADER_MAX_LENGTH ) {
			return $keys;
		}

		$keycats = array();
		foreach ( $keys as $k ) {
			$p = strrpos( $k, '-' );
			if ( false === $p ) {
				$keycats[ $k ][] = $k;
				continue;
			}
			$cat               = substr( $k, 0, $p + 1 );
			$keycats[ $cat ][] = $k;
		}

		// Sort by the output length of the key category.
		uasort(
			$keycats,
			function( $a, $b ) {
				$ca = strlen( implode( ' ', $a ) );
				$cb = strlen( implode( ' ', $b ) );
				if ( $ca === $cb ) {
					return 0;
				}
				return $ca > $cb ? -1 : 1;
			}
		);

		$cats = array_keys( $keycats );
		foreach ( $cats as $c ) {
			$keycats[ $c ] = array( $c . 'huge' );
			$keyout        = array();
			foreach ( $keycats as $v ) {
				$keyout = array_merge( $keyout, $v );
			}
			$output = implode( ' ', $keyout );
			if ( strlen( $output ) <= self::HEADER_MAX_LENGTH ) {
				return $keyout;
			}
		}

		return $keyout;
	}
}
