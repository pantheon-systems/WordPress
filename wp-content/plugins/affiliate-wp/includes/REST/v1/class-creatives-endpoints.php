<?php
namespace AffWP\Creative\REST\v1;

use \AffWP\REST\v1\Controller;

/**
 * Implements REST routes and endpoints for Creatives.
 *
 * @since 1.9
 *
 * @see AffWP\REST\Controller
 */
class Endpoints extends Controller {

	/**
	 * Object type.
	 *
	 * @since 1.9.5
	 * @access public
	 * @var string
	 */
	public $object_type = 'affwp_creative';

	/**
	 * Route base for creatives.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $rest_base = 'creatives';

	/**
	 * Registers Creative routes.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'args'                => $this->get_collection_params(),
				'permission_callback' => function( $request ) {
					return current_user_can( 'manage_creatives' );
				}
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>\d+)', array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => function( $request ) {
					return current_user_can( 'manage_creatives' );
				}
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		$this->register_field( 'id', array(
			'get_callback' => function( $object, $field_name, $request, $object_type ) {
				return $object->ID;
			}
		) );
	}

	/**
	 * Base endpoint to retrieve all creatives.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $request Request arguments.
	 * @return \WP_REST_Response|\WP_Error Creatives response, otherwise WP_Error.
	 */
	public function get_items( $request ) {

		$args = array();

		$args['number']       = isset( $request['number'] )       ? $request['number'] : 20;
		$args['offset']       = isset( $request['offset'] )       ? $request['offset'] : 0;
		$args['creative_id']  = isset( $request['creative_id'] )  ? $request['creative_id'] : 0;
		$args['status']       = isset( $request['status'] )       ? $request['status'] : '';
		$args['order']        = isset( $request['order'] )        ? $request['order'] : 'ASC';
		$args['orderby']      = isset( $request['orderby'] )      ? $request['orderby'] : '';
		$args['fields']       = isset( $request['fields'] )       ? $request['fields'] : '*';

		if ( is_array( $request['filter'] ) ) {
			$args = array_merge( $args, $request['filter'] );
			unset( $request['filter'] );
		}

		/**
		 * Filters the query arguments used to retrieve creatives in a REST request.
		 *
		 * @since 1.9
		 *
		 * @param array            $args    Arguments.
		 * @param \WP_REST_Request $request Request.
		 */
		$args = apply_filters( 'affwp_rest_creatives_query_args', $args, $request );

		$creatives = affiliate_wp()->creatives->get_creatives( $args );

		if ( empty( $creatives ) ) {
			$creatives = new \WP_Error(
				'no_creatives',
				'No creatives were found.',
				array( 'status' => 404 )
			);
		} else {
			$inst = $this;
			array_map( function( $creative ) use ( $inst, $request ) {
				$creative = $inst->process_for_output( $creative, $request );
				return $creative;
			}, $creatives );
		}

		return $this->response( $creatives );
	}

	/**
	 * Endpoint to retrieve a creative by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $request Request arguments.
	 * @return \AffWP\Creative|\WP_Error Creative object or \WP_Error object if not found.
	 */
	public function get_item( $request ) {
		if ( ! $creative = \affwp_get_creative( $request['id'] ) ) {
			$creative = new \WP_Error(
				'invalid_creative_id',
				'Invalid creative ID',
				array( 'status' => 404 )
			);
		} else {
			// Populate extra fields.
			$creative = $this->process_for_output( $creative, $request );
		}

		return $this->response( $creative );
	}

	/**
	 * Retrieves the collection parameters for creatives.
	 *
	 * @since 1.9
	 * @access public
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['context']['default'] = 'view';

		/*
		 * Pass top-level args as query vars:
		 * /creatives/?status=inactive&order=desc
		 */
		$params['creative_id'] = array(
			'description'       => __( 'The creative ID or array of IDs to query for.', 'affiliate-wp' ),
			'sanitize_callback' => 'absint',
			'validate_callback' => function( $param, $request, $key ) {
				return is_numeric( $param );
			},
		);

		$params['status'] = array(
			'description'       => __( "The creative status. Accepts 'active' or 'inactive'.", 'affiliate-wp' ),
			'validate_callback' => function( $param, $request, $key ) {
				return in_array( $param, array( 'active', 'inactive' ) );
			},
		);

		$params['orderby'] = array(
			'description'       => __( 'Creatives table column to order by.', 'affiliate-wp' ),
			'validate_callback' => function( $param, $request, $key ) {
				return array_key_exists( $param, affiliate_wp()->creatives->get_columns() );
			}
		);

		/*
		 * Pass any valid get_creatives() args via filter:
		 * /creatives/?filter[status]=inactive&filter[order]=desc
		 */
		$params['filter'] = array(
			'description' => __( 'Use any get_creatives() arguments to modify the response.', 'affiliate-wp' )
		);

		return $params;
	}

	/**
	 * Retrieves the schema for a single creative, conforming to JSON Schema.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {

		$schema = array(
			'$schema'    => 'http://json-schema.org/schema#',
			'title'      => $this->get_object_type(),
			'type'       => 'object',
			// Base properties for every creative.
			'properties' => array(
				'creative_id' => array(
					'description' => __( 'The unique creative ID.', 'affiliate-wp' ),
					'type'        => 'integer',
				),
				'name'        => array(
					'description' => __( 'Name of the creative.', 'affiliate-wp' ),
					'type'        => 'string',
				),
				'description' => array(
					'description' => __( 'Description for the creative.', 'affiliate-wp' ),
					'type'        => 'string',
				),
				'url'         => array(
					'description' => __( 'URL the creative points to.', 'affiliate-wp' ),
					'type'        => 'string',
				),
				'text'        => array(
					'description' => __( 'Text for the creative.', 'affiliatewp-rest-api' ),
					'type'        => 'string',
				),
				'image'       => array(
					'description' => __( 'ID of the media library image associated with the creative', 'affiliate-wp' ),
					'type'        => 'integer',
				),
				'status'      => array(
					'description' => __( 'The creative status.', 'affiliate-wp' ),
					'type'        => 'string',
				),
				'date'        => array(
					'description' => __( 'The date the creative was created.', 'affiliate-wp' ),
					'type'        => 'string',
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

}
