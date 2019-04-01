<?php
namespace AffWP\REST\v1;

/**
 * Base REST controller.
 *
 * @since 1.9
 * @abstract
 */
abstract class Controller {

	/**
	 * Object type.
	 *
	 * MUST be defined by extending classes.
	 *
	 * @since 1.9.5
	 * @access public
	 * @var string
	 */
	public $object_type = null;

	/**
	 * AffWP REST namespace.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $namespace = 'affwp/v1';

	/**
	 * The base of this controller's route.
	 *
	 * Should be defined and used by subclasses.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Registered REST fields.
	 *
	 * @since 1.9.5
	 * @access private
	 * @var array
	 */
	private $rest_fields = array();

	/**
	 * Constructor.
	 *
	 * Looks for a register_routes() method in the sub-class and hooks it up to 'rest_api_init'.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ), 15 );

		if ( null === $this->object_type ) {
			$message = sprintf( __( 'object_type must be defined by the extending class: %s', 'affiliate-wp' ), get_called_class() );
			_doing_it_wrong( 'object_type', $message, '1.9.5' );
		}
	}

	/**
	 * Converts an object or array of objects into a \WP_REST_Response object.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param object|array $response Object or array of objects.
	 * @return \WP_REST_Response REST response.
	 */
	public function response( $response ) {
		return rest_ensure_response( $response );
	}

	/**
	 * Retrieves the query parameters for collections.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param(),
			'number'  => array(
				'description'       => __( 'The number of items to query for. Use -1 for all.', 'affiliate-wp' ),
				'sanitize_callback' => 'absint',
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				},
			),
			'offset'  => array(
				'description'       => __( 'The number of items to offset in the query.', 'affiliate-wp' ),
				'sanitize_callback' => 'absint',
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				},
			),
			'order'   => array(
				'description'       => __( 'How to order results. Accepts ASC (ascending) or DESC (descending).', 'affiliate-wp' ),
				'validate_callback' => function( $param, $request, $key ) {
					return in_array( strtoupper( $param ), array( 'ASC', 'DESC' ) );
				},
			),
			'fields'  => array(
				'description'       => __( "Fields to limit the selection for. Accepts 'ids'. Default '*' for all.", 'affiliate-wp' ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => function( $param, $request, $key ) {
					return is_string( $param );
				},
			),
			'rest_id' => array(
				'description'       => __( 'The rest ID (site:object ID combination)', 'affiliate-wp' ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( $this, 'validate_rest_id' ),
			),

		);
	}

	/**
	 * Retrieves the magical context param.
	 *
	 * Ensures consistent description between endpoints, and populates enum from schema.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @see \WP_REST_Controller::get_context_param()
	 *
	 * @param array $args {
	 *     Optional. Parameter details. Default empty array.
	 *
	 *     @type string   $description       Parameter description.
	 *     @type string   $type              Parameter type. Accepts 'string', 'integer', 'array',
	 *                                       'object', etc. Default 'string'.
	 *     @type callable $sanitize_callback Parameter sanitization callback. Default 'sanitize_key'.
	 *     @type callable $validate_callback Parameter validation callback. Default empty.
	 * }
	 * @return array Context parameter details.
	 */
	public function get_context_param( $args = array() ) {
		$param_details = array(
			'description'       => __( 'Scope under which the request is made; determines fields present in response.', 'affiliate-wp' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => '',
		);

		return array_merge( $param_details, $args );
	}

	/**
	 * Retrieves an array of endpoint arguments from the item schema for the controller.
	 *
	 * Back-compat shim for WP_REST_Controller::get_endpoint_args_for_item_schema().
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $method Optional. HTTP method of the request. The arguments for `CREATABLE` requests are
	 *                       checked for required values and may fall-back to a given default, this is not done
	 *                       on `EDITABLE` requests. Default WP_REST_Server::CREATABLE.
	 * @return array Endpoint arguments.
	 */
	public function get_endpoint_args_for_item_schema( $method = WP_REST_Server::CREATABLE ) {

		$schema            = $this->get_item_schema();
		$schema_properties = ! empty( $schema['properties'] ) ? $schema['properties'] : array();
		$endpoint_args     = array();

		foreach ( $schema_properties as $field_id => $params ) {

			// Arguments specified as `readonly` are not allowed to be set.
			if ( ! empty( $params['readonly'] ) ) {
				continue;
			}

			if ( isset( $params['validate_callback'] ) ) {
				$endpoint_args[ $field_id ]['validate_callback'] = $params['validate_callback'];
			}

			if ( isset( $params['sanitize_callback'] ) ) {
				$endpoint_args[ $field_id ]['sanitize_callback'] = $params['sanitize_callback'];
			}

			if ( isset( $params['description'] ) ) {
				$endpoint_args[ $field_id ]['description'] = $params['description'];
			}

			if ( \WP_REST_Server::CREATABLE === $method && isset( $params['default'] ) ) {
				$endpoint_args[ $field_id ]['default'] = $params['default'];
			}

			if ( \WP_REST_Server::CREATABLE === $method && ! empty( $params['required'] ) ) {
				$endpoint_args[ $field_id ]['required'] = true;
			}

			foreach ( array( 'type', 'format', 'enum', 'items' ) as $schema_prop ) {
				if ( isset( $params[ $schema_prop ] ) ) {
					$endpoint_args[ $field_id ][ $schema_prop ] = $params[ $schema_prop ];
				}
			}

			// Merge in any options provided by the schema property.
			if ( isset( $params['arg_options'] ) ) {

				// Only use required / default from arg_options on CREATABLE endpoints.
				if ( \WP_REST_Server::CREATABLE !== $method ) {
					$params['arg_options'] = array_diff_key( $params['arg_options'], array( 'required' => '', 'default' => '' ) );
				}

				$endpoint_args[ $field_id ] = array_merge( $endpoint_args[ $field_id ], $params['arg_options'] );
			}
		}

		return $endpoint_args;
	}

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		return $this->add_additional_fields_schema( array() );
	}

	/**
	 * Retrieves the item's schema for display / public consumption purposes.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array Public item schema data.
	 */
	public function get_public_item_schema() {

		$schema = $this->get_item_schema();

		foreach ( $schema['properties'] as &$property ) {
			unset( $property['arg_options'] );
		}

		return $schema;
	}

	/**
	 * Retrieves the object type for the current endpoints.
	 *
	 * @since 1.9.5
	 * @access public
	 *
	 * @return string Object type.
	 */
	public function get_object_type() {
		return $this->object_type;
	}

	/**
	 * Processes an object for output to a response.
	 *
	 * @since 1.9.5
	 * @access public
	 *
	 * @param \AffWP\Base_Object|mixed $object  Object for output to the response.
	 * @param \WP_REST_Request         $request Full details about the request.
	 * @return mixed (Maybe) modified object for a response.
	 */
	protected function process_for_output( $object, $request ) {
		$object_type = $this->get_object_type();
		$addl_fields = array();

		foreach ( $this->get_additional_fields( $object_type ) as $field_name => $field_options ) {

			if ( ! $field_options['get_callback'] ) {
				continue;
			}

			$addl_fields[ $field_name ] = call_user_func( $field_options['get_callback'], $object, $field_name, $request, $object_type );
		}

		$object::fill_vars( $object, $addl_fields );

		return $this->response( (object) $object );
	}

	/**
	 * Registers a new field on an existing AffiliateWP object type.
	 *
	 * @since 1.9.5
	 * @access public
	 *
	 * @param string $field_name The attribute name.
	 * @param array  $args {
	 *     Optional. An array of arguments used to handle the registered field.
	 *
	 *     @type string|array|null $get_callback    Optional. The callback function used to retrieve the field
	 *                                              value. Default is 'null', the field will not be returned in
	 *                                              the response.
	 *     @type string|array|null $schema          Optional. The callback function used to create the schema for
	 *                                              this field. Default is 'null', no schema entry will be returned.
	 * }
	 * @return null|void Null if the object type could not be determined, otherwise void.
	 */
	public function register_field( $field_name, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'get_callback' => null,
			'schema'       => null,
		) );

		if ( ! $object_type = $this->get_object_type() ) {
			return;
		}

		if ( function_exists( 'register_rest_field' ) ) {
			register_rest_field( $object_type, $field_name, $args );
		}

		$this->rest_fields[ $object_type ][ $field_name ] = $args;
	}

	/**
	 * Retrieves all of the registered additional fields for a given object-type.
	 *
	 * @since 1.9.5
	 * @access protected
	 *
	 * @param string $object_type Optional. The object type.
	 * @return array Registered additional fields (if any), empty array if none or if the object type could
	 *               not be inferred.
	 */
	public function get_additional_fields( $object_type ) {
		$core_fields = array();

		if ( method_exists( '\WP_REST_Controller', 'get_additional_fields' ) ) {
			global $wp_rest_additional_fields;

			if ( isset( $wp_rest_additional_fields[ $object_type ] ) ) {
				$core_fields = $wp_rest_additional_fields[ $object_type ];
			}
		}

		if ( isset( $this->rest_fields[ $object_type ] ) ) {
			$fields = $this->rest_fields[ $object_type ];
		} else {
			$fields = array();
		}

		return array_merge( $fields, $core_fields );
	}

	/**
	 * Adds the schema from additional fields to a schema array.
	 *
	 * Back-compat shim for WP_REST_Controller::get_endpoint_args_for_item_schema().
	 *
	 * The type of object is inferred from the passed schema.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @param array $schema Schema array.
	 * @return array Modified Schema array.
	 */
	protected function add_additional_fields_schema( $schema ) {
		if ( empty( $schema['title'] ) ) {
			return $schema;
		}

		// Can't use $this->get_object_type otherwise we cause an inf loop.
		$object_type = $schema['title'];

		$additional_fields = $this->get_additional_fields( $object_type );

		foreach ( $additional_fields as $field_name => $field_options ) {
			if ( ! $field_options['schema'] ) {
				continue;
			}

			$schema['properties'][ $field_name ] = $field_options['schema'];
		}

		return $schema;
	}

	/**
	 * Validates a rest_id field.
	 *
	 * @since 2.2.2
	 *
	 * @param mixed            $value   Parameter value to validate
	 * @param \WP_REST_Request $request Request object.
	 * @param string           $param   The parameter name, used in error messages.
	 * @return bool True of the rest_id value is syntactically valid, otherwise false.
	 */
	public function validate_rest_id( $value, $request, $param ) {
		return affwp_validate_rest_id( $value );
	}

	/**
	 * Converts a given parameter value to an object in the expected format.
	 *
	 * @since 2.1.9
	 *
	 * @param array|string $value     String or array value.
	 * @param array        $whitelist Optional. Whitelist by which to compare `$value`. Default empty array.
	 * @param string       $default   Optional. Default value to use. Default empty.
	 * @return \stdClass Parameter as an object.
	 */
	protected function convert_param_to_object( $value, $whitelist = array(), $default = '' ) {
		if ( is_object( $value ) && isset( $value->raw ) ) {
			return $value;
		}

		if ( is_array( $value ) && isset( $value['raw'] ) ) {
			return (object) $value;
		}

		$object = new \stdClass;

		if ( empty( $value ) || ( ! empty( $whitelist ) && ! in_array( $value, $whitelist, true ) ) ) {
			$object->raw = $default;
		} else {
			$object->raw = $value;
		}

		if ( ! empty( $default ) ) {
			$object->inherited = $default;
		}

		return $object;
	}

}
