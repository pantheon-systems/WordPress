<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @param $settings
 * @param $value
 *
 * @since 4.2
 * @return string
 */
function vc_loop_form_field( $settings, $value ) {
	$query_builder = new VcLoopSettings( $value );
	$params = $query_builder->getContent();
	$loop_info = '';
	if ( is_array( $params ) ) {
		foreach ( $params as $key => $param ) {
			$param_value = vc_loop_get_value( $param );
			if ( ! empty( $param_value ) ) {
				$loop_info .= ' <b>' . $query_builder->getLabel( $key ) . '</b>: ' . $param_value . ';';
			}
		}
	}

	return '<div class="vc_loop">'
	       . '<input name="' . $settings['param_name'] . '" class="wpb_vc_param_value  ' . $settings['param_name'] . ' ' . $settings['type'] . '_field" type="hidden" value="' . $value . '"/>'
	       . '<a href="#" class="button vc_loop-build ' . $settings['param_name'] . '_button" data-settings="' . rawurlencode( json_encode( $settings['settings'] ) ) . '">' . __( 'Build query', 'js_composer' ) . '</a>'
	       . '<div class="vc_loop-info">' . $loop_info . '</div>'
	       . '</div>';
}

/**
 * @param $param
 *
 * @since 4.2
 * @return string
 */
function vc_loop_get_value( $param ) {
	$value = array();
	$selected_values = (array) $param['value'];
	if ( isset( $param['options'] ) && is_array( $param['options'] ) ) {
		foreach ( $param['options'] as $option ) {
			if ( is_array( $option ) && isset( $option['value'] ) ) {
				if ( in_array( ( ( '-' === $option['action'] ? '-' : '' ) . $option['value'] ), $selected_values ) ) {
					$value[] = $option['action'] . $option['name'];
				}
			} elseif ( is_array( $option ) && isset( $option[0] ) ) {
				if ( in_array( $option[0], $selected_values ) ) {
					$value[] = $option[1];
				}
			} elseif ( in_array( $option, $selected_values ) ) {
				$value[] = $option;
			}
		}
	} else {
		$value[] = $param['value'];
	}

	return implode( ', ', $value );
}

/**
 * Parses loop settings and creates WP_Query according to manual
 * @since 4.2
 * @link http://codex.wordpress.org/Class_Reference/WP_Query
 */
class VcLoopQueryBuilder {
	/**
	 * @since 4.2
	 * @var array
	 */
	protected $args = array(
		'post_status' => 'publish', // show only published posts #1098
	);

	/**
	 * @since 4.2
	 *
	 * @param $data
	 */
	function __construct( $data ) {
		foreach ( $data as $key => $value ) {
			$method = 'parse_' . $key;
			if ( method_exists( $this, $method ) ) {
				$this->$method( $value );
			}
		}
	}

	/**
	 * Pages count
	 * @since 4.2
	 *
	 * @param $value
	 */
	protected function parse_size( $value ) {
		$this->args['posts_per_page'] = 'All' === $value ? - 1 : (int) $value;
	}

	/**
	 * Sorting field
	 * @since 4.2
	 *
	 * @param $value
	 */
	protected function parse_order_by( $value ) {
		$this->args['orderby'] = $value;
	}

	/**
	 * Sorting order
	 * @since 4.2
	 *
	 * @param $value
	 */
	protected function parse_order( $value ) {
		$this->args['order'] = $value;
	}

	/**
	 * By post types
	 * @since 4.2
	 *
	 * @param $value
	 */
	protected function parse_post_type( $value ) {
		$this->args['post_type'] = $this->stringToArray( $value );
	}

	/**
	 * By author
	 * @since 4.2
	 *
	 * @param $value
	 */
	protected function parse_authors( $value ) {
		$this->args['author'] = $value;
	}

	/**
	 * By categories
	 * @since 4.2
	 *
	 * @param $value
	 */
	protected function parse_categories( $value ) {
		$this->args['cat'] = $value;
	}

	/**
	 * By taxonomies
	 * @since 4.2
	 *
	 * @param $value
	 */
	protected function parse_tax_query( $value ) {
		$terms = $this->stringToArray( $value );
		if ( empty( $this->args['tax_query'] ) ) {
			$this->args['tax_query'] = array( 'relation' => 'AND' );
		}
		$negative_term_list = array();
		foreach ( $terms as $term ) {
			if ( (int) $term < 0 ) {
				$negative_term_list[] = abs( $term );
			}
		}

		$not_in = array();
		$in = array();

		$terms = get_terms( VcLoopSettings::getTaxonomies(),
			array( 'include' => array_map( 'abs', $terms ) ) );
		foreach ( $terms as $t ) {
			if ( in_array( (int) $t->term_id, $negative_term_list ) ) {
				$not_in[ $t->taxonomy ][] = $t->term_id;
			} else {
				$in[ $t->taxonomy ][] = $t->term_id;
			}
		}

		foreach ( $in as $taxonomy => $terms ) {
			$this->args['tax_query'][] = array(
				'field' => 'term_id',
				'taxonomy' => $taxonomy,
				'terms' => $terms,
				'operator' => 'IN',
			);
		}
		foreach ( $not_in as $taxonomy => $terms ) {
			$this->args['tax_query'][] = array(
				'field' => 'term_id',
				'taxonomy' => $taxonomy,
				'terms' => $terms,
				'operator' => 'NOT IN',
			);
		}
	}

	/**
	 * By tags ids
	 * @since 4.2
	 *
	 * @param $value
	 */
	protected function parse_tags( $value ) {
		$in = $not_in = array();
		$tags_ids = $this->stringToArray( $value );
		foreach ( $tags_ids as $tag ) {
			$tag = (int) $tag;
			if ( $tag < 0 ) {
				$not_in[] = abs( $tag );
			} else {
				$in[] = $tag;
			}
		}
		$this->args['tag__in'] = $in;
		$this->args['tag__not_in'] = $not_in;
	}

	/**
	 * By posts ids
	 * @since 4.2
	 *
	 * @param $value
	 */
	protected function parse_by_id( $value ) {
		$in = $not_in = array();
		$ids = $this->stringToArray( $value );
		foreach ( $ids as $id ) {
			$id = (int) $id;
			if ( $id < 0 ) {
				$not_in[] = abs( $id );
			} else {
				$in[] = $id;
			}
		}
		$this->args['post__in'] = $in;
		$this->args['post__not_in'] = $not_in;
	}

	/**
	 * @since 4.2
	 *
	 * @param $id
	 */
	public function excludeId( $id ) {
		if ( ! isset( $this->args['post__not_in'] ) ) {
			$this->args['post__not_in'] = array();
		}
		if ( is_array( $id ) ) {
			$this->args['post__not_in'] = array_merge( $this->args['post__not_in'], $id );
		} else {
			$this->args['post__not_in'][] = $id;
		}
	}

	/**
	 * Converts string to array. Filters empty arrays values
	 * @since 4.2
	 *
	 * @param $value
	 *
	 * @return array
	 */
	protected function stringToArray( $value ) {
		$valid_values = array();
		$list = preg_split( '/\,[\s]*/', $value );
		foreach ( $list as $v ) {
			if ( strlen( $v ) > 0 ) {
				$valid_values[] = $v;
			}
		}

		return $valid_values;
	}

	/**
	 * @return array
	 */
	public function build() {
		return array( $this->args, new WP_Query( $this->args ) );
	}
}

/**
 * Class VcLoopSettings
 * @since 4.2
 */
class VcLoopSettings {
	// Available parts of loop for WP_Query object.
	/**
	 * @since 4.2
	 * @var array
	 */
	protected $content = array();
	/**
	 * @since 4.2
	 * @var array
	 */
	protected $parts;
	/**
	 * @since 4.2
	 * @var array
	 */
	protected $query_parts = array(
		'size',
		'order_by',
		'order',
		'post_type',
		'authors',
		'categories',
		'tags',
		'tax_query',
		'by_id',
	);

	/**
	 * @since 4.2
	 *
	 * @param $value
	 * @param array $settings
	 */
	function __construct( $value, $settings = array() ) {
		$this->parts = array(
			'size' => __( 'Post count', 'js_composer' ),
			'order_by' => __( 'Order by', 'js_composer' ),
			'order' => __( 'Sort order', 'js_composer' ),
			'post_type' => __( 'Post types', 'js_composer' ),
			'authors' => __( 'Author', 'js_composer' ),
			'categories' => __( 'Categories', 'js_composer' ),
			'tags' => __( 'Tags', 'js_composer' ),
			'tax_query' => __( 'Taxonomies', 'js_composer' ),
			'by_id' => __( 'Individual posts/pages', 'js_composer' ),
		);
		$this->settings = $settings;
		// Parse loop string
		$data = $this->parseData( $value );
		foreach ( $this->query_parts as $part ) {
			$value = isset( $data[ $part ] ) ? $data[ $part ] : '';
			$locked = 'true' === $this->getSettings( $part, 'locked' );
			// Predefined value check.
			if ( ! is_null( $this->getSettings( $part, 'value' ) ) && $this->replaceLockedValue( $part )
			     && ( true === $locked || 0 === strlen( (string) $value ) )
			) {
				$value = $this->settings[ $part ]['value'];
			} elseif ( ! is_null( $this->getSettings( $part, 'value' ) ) && ! $this->replaceLockedValue( $part )
			           && ( true === $locked || 0 === strlen( (string) $value ) )
			) {
				$value = implode( ',', array_unique( explode( ',', $value . ',' . $this->settings[ $part ]['value'] ) ) );
			}
			// Find custom method for parsing
			if ( method_exists( $this, 'parse_' . $part ) ) {
				$method = 'parse_' . $part;
				$this->content[ $part ] = $this->$method( $value );
			} else {
				$this->content[ $part ] = $this->parseString( $value );
			}
			// Set locked if value is locked by settings
			if ( $locked ) {
				$this->content[ $part ]['locked'] = true;
			}
			//
			if ( 'true' === $this->getSettings( $part, 'hidden' ) ) {
				$this->content[ $part ]['hidden'] = true;
			}
		}
	}

	/**
	 * @param $part
	 *
	 * @since 4.2
	 * @return bool
	 */
	protected function replaceLockedValue( $part ) {
		return in_array( $part, array( 'size', 'order_by', 'order' ) );
	}

	/**
	 * @param $key
	 *
	 * @since 4.2
	 * @return mixed
	 */
	public function getLabel( $key ) {
		return isset( $this->parts[ $key ] ) ? $this->parts[ $key ] : $key;
	}

	/**
	 * @param $part
	 * @param $name
	 *
	 * @since 4.2
	 * @return null
	 */
	public function getSettings( $part, $name ) {
		$settings_exists = isset( $this->settings[ $part ] ) && is_array( $this->settings[ $part ] );

		return $settings_exists && isset( $this->settings[ $part ][ $name ] ) ? $this->settings[ $part ][ $name ] : null;
	}

	/**
	 * @param $value
	 *
	 * @since 4.2
	 * @return array
	 */
	public function parseString( $value ) {
		return array( 'value' => $value );
	}

	/**
	 * @param $value
	 * @param array $options
	 *
	 * @since 4.2
	 * @return array
	 */
	protected function parseDropDown( $value, $options = array() ) {
		return array( 'value' => $value, 'options' => $options );
	}

	/**
	 * @param $value
	 * @param array $options
	 *
	 * @since 4.2
	 * @return array
	 */
	protected function parseMultiSelect( $value, $options = array() ) {
		return array( 'value' => explode( ',', $value ), 'options' => $options );
	}

	/**
	 * @param $value
	 *
	 * @since 4.2
	 * @return array
	 */
	public function parse_order_by( $value ) {
		return $this->parseDropDown( $value, array(
			array( 'date', __( 'Date', 'js_composer' ) ),
			'ID',
			array( 'author', __( 'Author', 'js_composer' ) ),
			array( 'title', __( 'Title', 'js_composer' ) ),
			array( 'modified', __( 'Modified', 'js_composer' ) ),
			array( 'rand', __( 'Random', 'js_composer' ) ),
			array( 'comment_count', __( 'Comment count', 'js_composer' ) ),
			array( 'menu_order', __( 'Menu order', 'js_composer' ) ),
		) );
	}

	/**
	 * @param $value
	 *
	 * @since 4.2
	 * @return array
	 */
	public function parse_order( $value ) {
		return $this->parseDropDown( $value, array(
			array( 'ASC', __( 'Ascending', 'js_composer' ) ),
			array( 'DESC', __( 'Descending', 'js_composer' ) ),
		) );
	}

	/**
	 * @param $value
	 *
	 * @since 4.2
	 * @return array
	 */
	public function parse_post_type( $value ) {
		$options = array();
		$args = array(
			'public' => true,
		);
		$post_types = get_post_types( $args );
		foreach ( $post_types as $post_type ) {
			if ( 'attachment' !== $post_type ) {
				$options[] = $post_type;
			}
		}

		return $this->parseMultiSelect( $value, $options );
	}

	/**
	 * @param $value
	 *
	 * @since 4.2
	 * @return array
	 */
	public function parse_authors( $value ) {
		$options = $not_in = array();
		if ( empty( $value ) ) {
			return $this->parseMultiSelect( $value, $options );
		}
		$list = explode( ',', $value );
		foreach ( $list as $id ) {
			if ( (int) $id < 0 ) {
				$not_in[] = abs( $id );
			}
		}
		$users = get_users( array( 'include' => array_map( 'abs', $list ) ) );
		foreach ( $users as $user ) {
			$options[] = array(
				'value' => (string) $user->ID,
				'name' => $user->data->user_nicename,
				'action' => in_array( (int) $user->ID, $not_in ) ? '-' : '+',
			);
		}

		return $this->parseMultiSelect( $value, $options );
	}

	/**
	 * @param $value
	 *
	 * @since 4.2
	 * @return array
	 */
	public function parse_categories( $value ) {
		$options = $not_in = array();
		if ( empty( $value ) ) {
			return $this->parseMultiSelect( $value, $options );
		}
		$list = explode( ',', $value );
		foreach ( $list as $id ) {
			if ( (int) $id < 0 ) {
				$not_in[] = abs( $id );
			}
		}
		$list = get_categories( array( 'include' => array_map( 'abs', $list ) ) );
		foreach ( $list as $obj ) {
			$options[] = array(
				'value' => (string) $obj->cat_ID,
				'name' => $obj->cat_name,
				'action' => in_array( (int) $obj->cat_ID, $not_in ) ? '-' : '+',
			);
		}

		return $this->parseMultiSelect( $value, $options );
	}

	/**
	 * @param $value
	 *
	 * @since 4.2
	 * @return array
	 */
	public function parse_tags( $value ) {
		$options = $not_in = array();
		if ( empty( $value ) ) {
			return $this->parseMultiSelect( $value, $options );
		}
		$list = explode( ',', $value );
		foreach ( $list as $id ) {
			if ( (int) $id < 0 ) {
				$not_in[] = abs( $id );
			}
		}
		$list = get_tags( array( 'include' => array_map( 'abs', $list ) ) );
		foreach ( $list as $obj ) {
			$options[] = array(
				'value' => (string) $obj->term_id,
				'name' => $obj->name,
				'action' => in_array( (int) $obj->term_id, $not_in ) ? '-' : '+',
			);
		}

		return $this->parseMultiSelect( $value, $options );
	}

	/**
	 * @param $value
	 *
	 * @since 4.2
	 * @return array
	 */
	public function parse_tax_query( $value ) {
		$options = $not_in = array();
		if ( empty( $value ) ) {
			return $this->parseMultiSelect( $value, $options );
		}
		$list = explode( ',', $value );
		foreach ( $list as $id ) {
			if ( (int) $id < 0 ) {
				$not_in[] = abs( $id );
			}
		}
		$list = get_terms( self::getTaxonomies(), array( 'include' => array_map( 'abs', $list ) ) );
		foreach ( $list as $obj ) {
			$options[] = array(
				'value' => (string) $obj->term_id,
				'name' => $obj->name,
				'action' => in_array( (int) $obj->term_id, $not_in ) ? '-' : '+',
			);
		}

		return $this->parseMultiSelect( $value, $options );
	}

	/**
	 * @param $value
	 *
	 * @since 4.2
	 * @return array
	 */
	public function parse_by_id( $value ) {
		$options = $not_in = array();
		if ( empty( $value ) ) {
			return $this->parseMultiSelect( $value, $options );
		}
		$list = explode( ',', $value );
		foreach ( $list as $id ) {
			if ( (int) $id < 0 ) {
				$not_in[] = abs( $id );
			}
		}
		$list = get_posts( array( 'post_type' => 'any', 'include' => array_map( 'abs', $list ) ) );

		foreach ( $list as $obj ) {
			$options[] = array(
				'value' => (string) $obj->ID,
				'name' => $obj->post_title,
				'action' => in_array( (int) $obj->ID, $not_in ) ? '-' : '+',
			);
		}

		return $this->parseMultiSelect( $value, $options );
	}

	/**
	 * @since 4.2
	 */
	public function render() {
		echo json_encode( $this->content );
	}

	/**
	 * @since 4.2
	 * @return array
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * get list of taxonomies which has no tags and categories items.
	 * @since 4.2
	 * @static
	 * @return array
	 */
	public static function getTaxonomies() {
		$taxonomy_exclude = (array) apply_filters( 'get_categories_taxonomy', 'category' );
		$taxonomy_exclude[] = 'post_tag';
		$taxonomies = array();
		foreach ( get_taxonomies() as $taxonomy ) {
			if ( ! in_array( $taxonomy, $taxonomy_exclude ) ) {
				$taxonomies[] = $taxonomy;
			}
		}

		return $taxonomies;
	}

	/**
	 * @param $settings
	 *
	 * @since 4.2
	 * @return string
	 */
	public static function buildDefault( $settings ) {
		if ( ! isset( $settings['settings'] ) || ! is_array( $settings['settings'] ) ) {
			return '';
		}
		$value = '';
		foreach ( $settings['settings'] as $key => $val ) {
			if ( isset( $val['value'] ) ) {
				$value .= ( empty( $value ) ? '' : '|' ) . $key . ':' . $val['value'];
			}
		}

		return $value;
	}

	/**
	 * @param $query
	 * @param bool $exclude_id
	 *
	 * @since 4.2
	 * @return array
	 */
	public static function buildWpQuery( $query, $exclude_id = false ) {
		$data = self::parseData( $query );
		$query_builder = new VcLoopQueryBuilder( $data );
		if ( $exclude_id ) {
			$query_builder->excludeId( $exclude_id );
		}

		return $query_builder->build();
	}

	/**
	 * @param $value
	 *
	 * @since 4.2
	 * @return array
	 */
	public static function parseData( $value ) {
		$data = array();
		$values_pairs = preg_split( '/\|/', $value );
		foreach ( $values_pairs as $pair ) {
			if ( ! empty( $pair ) ) {
				list( $key, $value ) = preg_split( '/\:/', $pair );
				$data[ $key ] = $value;
			}
		}

		return $data;
	}
}

/**
 * Suggestion list for wp_query field
 * Class VcLoopSuggestions
 * @since 4.2
 */
class VcLoopSuggestions {
	/**
	 * @since 4.2
	 * @var array
	 */
	protected $content = array();
	/**
	 * @since 4.2
	 * @var array
	 */
	protected $exclude = array();
	/**
	 * @since 4.2
	 * @var
	 */
	protected $field;

	/**
	 * @param $field
	 * @param $query
	 * @param $exclude
	 *
	 * @since 4.2
	 */
	function __construct( $field, $query, $exclude ) {
		$this->exclude = explode( ',', $exclude );
		$method_name = 'get_' . preg_replace( '/_out$/', '', $field );
		if ( method_exists( $this, $method_name ) ) {
			$this->$method_name( $query );
		}
	}

	/**
	 * @param $query
	 *
	 * @since 4.2
	 */
	public function get_authors( $query ) {
		$args = ! empty( $query ) ? array(
			'search' => '*' . $query . '*',
			'search_columns' => array( 'user_nicename' ),
		) : array();
		if ( ! empty( $this->exclude ) ) {
			$args['exclude'] = $this->exclude;
		}
		$users = get_users( $args );
		foreach ( $users as $user ) {
			$this->content[] = array( 'value' => (string) $user->ID, 'name' => (string) $user->data->user_nicename );
		}
	}

	/**
	 * @param $query
	 *
	 * @since 4.2
	 */
	public function get_categories( $query ) {
		$args = ! empty( $query ) ? array( 'search' => $query ) : array();
		if ( ! empty( $this->exclude ) ) {
			$args['exclude'] = $this->exclude;
		}
		$categories = get_categories( $args );

		foreach ( $categories as $cat ) {
			$this->content[] = array( 'value' => (string) $cat->cat_ID, 'name' => $cat->cat_name );
		}
	}

	/**
	 * @param $query
	 *
	 * @since 4.2
	 */
	public function get_tags( $query ) {
		$args = ! empty( $query ) ? array( 'search' => $query ) : array();
		if ( ! empty( $this->exclude ) ) {
			$args['exclude'] = $this->exclude;
		}
		$tags = get_tags( $args );
		foreach ( $tags as $tag ) {
			$this->content[] = array( 'value' => (string) $tag->term_id, 'name' => $tag->name );
		}
	}

	/**
	 * @param $query
	 *
	 * @since 4.2
	 */
	public function get_tax_query( $query ) {
		$args = ! empty( $query ) ? array( 'search' => $query ) : array();
		if ( ! empty( $this->exclude ) ) {
			$args['exclude'] = $this->exclude;
		}
		$tags = get_terms( VcLoopSettings::getTaxonomies(), $args );
		foreach ( $tags as $tag ) {
			$this->content[] = array(
				'value' => $tag->term_id,
				'name' => $tag->name . ' (' . $tag->taxonomy . ')',
			);
		}
	}

	/**
	 * @param $query
	 *
	 * @since 4.2
	 */
	public function get_by_id( $query ) {
		$args = ! empty( $query ) ? array( 's' => $query, 'post_type' => 'any' ) : array( 'post_type' => 'any' );
		if ( ! empty( $this->exclude ) ) {
			$args['exclude'] = $this->exclude;
		}
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$this->content[] = array( 'value' => $post->ID, 'name' => $post->post_title );
		}
	}

	/**
	 * @since 4.2
	 */
	public function render() {
		echo json_encode( $this->content );
	}
}

/**
 * Build WP_Query object from query string.
 * String created by loop controllers
 *
 * @param $query
 * @param bool $exclude_id
 *
 * @since 4.2
 * @return string
 */
function vc_build_loop_query( $query, $exclude_id = false ) {
	return VcLoopSettings::buildWpQuery( $query, $exclude_id );
}

/**
 * @since 4.2
 */
function vc_get_loop_suggestion() {
	vc_user_access()
		->checkAdminNonce()
		->validateDie()
		->wpAny( 'edit_posts', 'edit_pages' )
		->validateDie();

	$loop_suggestions = new VcLoopSuggestions( vc_post_param( 'field' ), vc_post_param( 'query' ), vc_post_param( 'exclude' ) );
	$loop_suggestions->render();
	die();
}

/**
 * @since 4.2
 */
function vc_get_loop_settings_json() {
	vc_user_access()
		->checkAdminNonce()
		->validateDie()
		->wpAny( 'edit_posts', 'edit_pages' )
		->validateDie();

	$loop_settings = new VcLoopSettings( vc_post_param( 'value' ), vc_post_param( 'settings' ) );
	$loop_settings->render();
	die();
}

add_action( 'wp_ajax_wpb_get_loop_suggestion', 'vc_get_loop_suggestion' );
add_action( 'wp_ajax_wpb_get_loop_settings', 'vc_get_loop_settings_json' );

/**
 * @since 4.2
 */
function vc_loop_include_templates() {
	require_once vc_path_dir( 'TEMPLATES_DIR', 'params/loop/templates.html' );
}

add_action( 'admin_footer', 'vc_loop_include_templates' );

/**
 * @param $param
 *
 * @since 4.2
 * @return mixed
 */
function vc_set_loop_default_value( $param ) {
	if ( empty( $param['value'] ) && isset( $param['settings'] ) ) {
		$param['value'] = VcLoopSettings::buildDefault( $param );
	}

	return $param;
}

add_filter( 'vc_mapper_attribute_loop', 'vc_set_loop_default_value' );
