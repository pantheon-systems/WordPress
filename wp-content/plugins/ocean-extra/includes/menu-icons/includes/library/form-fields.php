<?php

/**
 * Form Fields
 */
abstract class OE_Form_Field {

	/**
	 * Holds field & argument defaults
	 *
	 */
	protected static $defaults = array(
		'field' => array(
			'id'          => '',
			'type'        => 'text',
			'value'       => null,
			'default'     => null,
			'attributes'  => array(),
			'description' => '',
			'choices'     => array(),
		),
		'args'  => array(
			'keys'               => array(),
			'inline_description' => false,
		),
	);

	/**
	 * Holds field attributes
	 *
	 */
	protected static $types = array(
		'text'            => 'OE_Form_Field_Text',
		'number'          => 'OE_Form_Field_Text',
		'url'             => 'OE_Form_Field_Text',
		'color'           => 'OE_Form_Field_Text',
		'date'            => 'OE_Form_Field_Text',
		'hidden'          => 'OE_Form_Field_Text',
		'checkbox'        => 'OE_Form_Field_Checkbox',
		'radio'           => 'OE_Form_Field_Radio',
		'textarea'        => 'OE_Form_Field_Textarea',
		'select'          => 'OE_Form_Field_Select',
		'select_multiple' => 'OE_Form_Field_Select_Multiple',
		'select_pages'    => 'OE_Form_Field_Select_Pages',
		'special'         => 'OE_Form_Field_Special',
	);

	/**
	 * Holds forbidden attributes
	 *
	 */
	protected static $forbidden_attributes = array(
		'id',
		'name',
		'value',
		'checked',
		'multiple',
	);

	/**
	 * Holds allowed html tags
	 *
	 */
	protected $allowed_html = array(
		'a'      => array(
			'href'   => true,
			'target' => true,
			'title'  => true,
		),
		'code'   => true,
		'em'     => true,
		'p'      => array( 'class' => true ),
		'span'   => array( 'class' => true ),
		'strong' => true,
	);

	/**
	 * Holds constructed field
	 *
	 */
	protected $field;

	/**
	 * Holds field attributes
	 *
	 */
	protected $attributes = array();

	/**
	 * Loader
	 *
	*/
	final public static function load( $url_path = null ) {
		// Set URL path for assets
		if ( ! is_null( $url_path ) ) {
			self::$url_path = $url_path;
		} else {
			self::$url_path = plugin_dir_url( __FILE__ );
		}

		// Supported field types
		self::$types = apply_filters(
			'form_field_types',
			self::$types
		);
	}

	/**
	 * Create field
	 *
	 */
	final public static function create( array $field, $args = array() ) {
		$field = wp_parse_args( $field, self::$defaults['field'] );
		if ( ! isset( self::$types[ $field['type'] ] )
			|| ! is_subclass_of( self::$types[ $field['type'] ], __CLASS__ )
		) {
			trigger_error(
				sprintf(
					esc_html__( '%1$s: Type %2$s is not supported, reverting to text.', 'ocean-extra' ),
					__CLASS__,
					esc_html( $field['type'] )
				),
				E_USER_WARNING
			);
			$field['type'] = 'text';
		}

		if ( is_null( $field['value'] ) && ! is_null( $field['default'] ) ) {
			$field['value'] = $field['default'];
		}

		foreach ( self::$forbidden_attributes as $key ) {
			unset( $field['attributes'][ $key ] );
		}

		$args  = (object) wp_parse_args( $args, self::$defaults['args'] );
		$class = self::$types[ $field['type'] ];

		return new $class( $field, $args );
	}

	/**
	 * Constructor
	 *
	 */
	public function __construct( $field, $args ) {
		$this->field = $field;
		$this->args  = $args;

		if ( ! is_array( $this->args->keys ) ) {
			$this->args->keys = array();
		}
		$this->args->keys[] = $field['id'];

		$this->attributes['id']   = $this->create_id();
		$this->attributes['name'] = $this->create_name();

		$this->attributes = wp_parse_args(
			$this->attributes,
			(array) $field['attributes']
		);

		$this->set_properties();
	}

	/**
	 * Attribute
	 *
	 */
	public function __get( $key ) {
		foreach ( array( 'attributes', 'field' ) as $group ) {
			if ( isset( $this->{$group}[ $key ] ) ) {
				return $this->{$group}[ $key ];
			}
		}

		return null;
	}

	/**
	 * Create id/name attribute
	 *
	 */
	protected function create_id_name( $format ) {
		return call_user_func_array(
			'sprintf',
			array_merge(
				array( $format ),
				$this->args->keys
			)
		);
	}

	/**
	 * Create id attribute
	 *
	 */
	protected function create_id() {
		$format = implode( '-', $this->args->keys );

		return $this->create_id_name( $format );
	}

	/**
	 * Create name attribute
	 *
	 */
	protected function create_name() {
		$format  = '%s';
		$format .= str_repeat( '[%s]', ( count( $this->args->keys ) - 1 ) );

		return $this->create_id_name( $format );
	}

	/**
	 * Set field properties
	 *
	 */
	protected function set_properties() {}

	/**
	 * Build field attributes
	 *
	 */
	protected function build_attributes( $excludes = array() ) {
		$excludes   = array_filter( (array) $excludes );
		$attributes = '';

		foreach ( $this->attributes as $key => $value ) {
			if ( in_array( $key, $excludes, true ) ) {
				continue;
			}

			if ( 'class' === $key ) {
				$value = implode( ' ', (array) $value );
			}

			$attributes .= sprintf(
				' %s="%s"',
				esc_attr( $key ),
				esc_attr( $value )
			);
		}

		return $attributes;
	}

	/**
	 * Print field
	 *
	 */
	abstract public function render();

	/**
	 * Print field description
	 *
	 */
	public function description() {
		if ( ! empty( $this->field['description'] ) ) {
			$tag = ( ! empty( $this->args->inline_description ) ) ? 'span' : 'p';

			printf( // WPCS: XSS ok.
				'<%1$s class="description">%2$s</%1$s>',
				$tag,
				wp_kses( $this->field['description'], $this->allowed_html )
			);
		}
	}
}

/**
 * Field: text
 */
class OE_Form_Field_Text extends OE_Form_Field {

	protected $template = '<input type="%s" value="%s"%s />';

	protected function set_properties() {
		if ( ! is_string( $this->field['value'] ) ) {
			$this->field['value'] = '';
		}

		if ( in_array( $this->field['type'], array( 'text', 'url' ), true ) ) {
			if ( ! isset( $this->attributes['class'] ) ) {
				$this->attributes['class'] = array();
			}
			$this->attributes['class'] = array_unique(
				array_merge(
					array( 'regular-text' ),
					$this->attributes['class']
				)
			);
		}
	}

	public function render() {
		printf(  // WPCS: xss ok
			$this->template,
			esc_attr( $this->field['type'] ),
			esc_attr( $this->field['value'] ),
			$this->build_attributes()
		);
		$this->description();
	}
}

/**
 * Field: Textarea
 */
class OE_Form_Field_Textarea extends OE_Form_Field {

	protected $template = '<textarea%s>%s</textarea>';

	protected $attributes = array(
		'class' => 'widefat',
		'cols'  => 50,
		'rows'  => 5,
	);

	public function render() {
		printf( // WPCS: XSS ok.
			$this->template,
			$this->build_attributes(),
			esc_textarea( $this->field['value'] )
		);
	}
}

/**
 * Field: Checkbox
 */
class OE_Form_Field_Checkbox extends OE_Form_Field {

	protected $template = '<label><input type="%s" value="%s"%s%s /> %s</label><br />';

	protected function set_properties() {
		$this->field['value'] = array_filter( (array) $this->field['value'] );
		$this->attributes['name'] .= '[]';
	}

	protected function checked( $value ) {
		return checked( in_array( $value, $this->field['value'], true ), true, false );
	}

	public function render() {
		foreach ( $this->field['choices'] as $value => $label ) {
			printf( // WPCS: XSS ok.
				$this->template,
				$this->field['type'],
				esc_attr( $value ),
				$this->checked( $value ),
				$this->build_attributes( 'id' ),
				esc_html( $label )
			);
		}
	}
}

/**
 * Field: Radio
 */
class OE_Form_Field_Radio extends OE_Form_Field_Checkbox {

	protected function set_properties() {
		if ( ! is_string( $this->field['value'] ) ) {
			$this->field['value'] = '';
		}
	}

	protected function checked( $value ) {
		return checked( $value, $this->field['value'], false );
	}
}

/**
 * Field: Select
 */
class OE_Form_Field_Select extends OE_Form_Field {

	protected $template = '<option value="%s"%s>%s</option>';

	protected function set_properties() {
		if ( ! is_string( $this->field['value'] ) ) {
			$this->field['value'] = '';
		}
	}

	protected function selected( $value ) {
		return selected( ( $value === $this->field['value'] ), true, false );
	}

	public function render() {
		?>
		<select<?php echo $this->build_attributes() // xss ok ?>>
			<?php foreach ( $this->field['choices'] as $index => $choice ) : ?>
				<?php
				if ( is_array( $choice ) ) {
					$value = $choice['value'];
					$label = $choice['label'];
				} else {
					$value = $index;
					$label = $choice;
				}
				?>
				<?php
					printf( // WPCS: XSS ok.
						$this->template,
						esc_attr( $value ),
						$this->selected( $value ),
						esc_html( $label )
					);
				?>
			<?php endforeach; ?>
		</select>
		<?php
	}
}

/**
 * Field: Multiple Select
 */
class OE_Form_Field_Select_Multiple extends OE_Form_Field_Select {

	protected function set_properties() {
		$this->field['value']         = array_filter( (array) $this->field['value'] );
		$this->attributes['name']    .= '[]';
		$this->attributes['multiple'] = 'multiple';
	}


	protected function selected( $value ) {
		return selected( in_array( $value, $this->field['value'], true ), true, false );
	}
}

/**
 * Field: Select Pages
 */
class OE_Form_Field_Select_Pages extends OE_Form_Field_Select {

	protected $wp_dropdown_pages_args = array(
		'depth'             => 0,
		'child_of'          => 0,
		'option_none_value' => '',
	);

	public function __construct( $field, $args ) {
		$this->wp_dropdown_pages_args['show_option_none'] = __( '&mdash; Select &mdash;', 'postmedia' );
		parent::__construct( $field, $args );
	}

	public function set_properties() {
		parent::set_properties();

		if ( empty( $this->args->wp_dropdown_pages_args ) ) {
			$this->args->wp_dropdown_pages_args = array();
		}

		// Apply defeaults
		$this->args->wp_dropdown_pages_args = wp_parse_args(
			$this->args->wp_dropdown_pages_args,
			$this->wp_dropdown_pages_args
		);

		// Force some args
		$this->args->wp_dropdown_pages_args = array_merge(
			$this->args->wp_dropdown_pages_args,
			array(
				'echo'     => true,
				'name'     => $this->attributes['name'],
				'id'       => $this->attributes['id'],
				'selected' => $this->field['value'],
			)
		);
	}

	public function render() {
		wp_dropdown_pages( $this->args->wp_dropdown_pages_args ); // WPCS: XSS ok.
	}
}

/**
 * Field: Special (Callback)
 */
class OE_Form_Field_Special extends OE_Form_Field {
	public function render() {
		call_user_func_array(
			$this->field['render_cb'],
			array( $this )
		);
	}
}