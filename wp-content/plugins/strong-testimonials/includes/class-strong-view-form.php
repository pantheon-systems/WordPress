<?php
/**
 * Class Strong_View_Form
 *
 * @since 2.16.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Strong_View_Form' ) ) :

class Strong_View_Form extends Strong_View {

	/**
	 * Strong_View_Form constructor.
	 *
	 * @param array $atts
	 */
	public function __construct( $atts = array() ) {
		parent::__construct( $atts );
	}

	/**
	 * Process the view.
	 *
	 * Used by main class to load the scripts and styles for this View.
	 */
	public function process() {
		$this->build_classes();
		$this->find_stylesheet();
		$this->load_dependent_scripts();
		$this->load_extra_stylesheets();
		$this->load_validator();

		// If we can preprocess, we can add the inline style in the <head>.
		add_action( 'wp_enqueue_scripts', array( $this, 'add_custom_style' ), 20 );
	}

	/**
	 * Build the view.
	 */
	public function build() {

		do_action( 'wpmtst_view_build_before', $this );

		if ( isset( $_GET['success'] ) ) {
			$this->find_stylesheet();
			$this->on_form_success();
			do_action( 'wpmtst_form_success', $this->atts );
			$this->html = wpmtst_get_success_message();
			return;
		}

		$this->build_classes();
		$this->find_stylesheet();
		$this->load_dependent_scripts();
		$this->load_extra_stylesheets();
		$this->custom_background();
		$this->load_validator();
		//$this->load_honeypots();

		/*
		 * If we cannot preprocess, add the inline style to the footer.
		 * If we were able to preprocess, this will not duplicate the code
		 * since `wpmtst-custom-style` was already enqueued (I think).
		 */
		add_action( 'wp_footer', array( $this, 'add_custom_style' ) );
		add_action( 'wp_footer', array( $this, 'load_honeypots' ) );

		$form_values = array( 'category' => $this->atts['category'] );

		$fields = wpmtst_get_form_fields( $this->atts['form_id'] );
		if ( $fields ) {
			foreach ( $fields as $key => $field ) {
				$form_values[ $field['name'] ] = '';
			}
		}

		$previous_values = WPMST()->form->get_form_values();
		if ( $previous_values ) {
			$form_values = array_merge( $form_values, $previous_values );
		}

		WPMST()->form->set_form_values( $form_values );

		/**
		 * Add filters here.
		 */

		/**
		 * Locate template.
		 */
		$this->template_file = WPMST()->templates->get_template_attr( $this->atts, 'template' );

		/**
		 * Allow add-ons to hijack the output generation.
		 */
		$atts = $this->atts;
		if ( has_filter( 'wpmtst_render_view_template' ) ) {
			$html = apply_filters( 'wpmtst_render_view_template', '', $this );
		} else {
			ob_start();
			/** @noinspection PhpIncludeInspection */
			include( $this->template_file );
			$html = ob_get_clean();
		}
		// TODO apply content filters

		/**
		 * Remove filters here.
		 */

		do_action( 'wpmtst_form_rendered', $this->atts );

		$this->html = apply_filters( 'strong_view_form_html', $html, $this );

	}

	/**
	 * Build class list based on view attributes.
	 */
	public function build_classes() {

		$container_class_list = array( 'strong-view-id-' . $this->atts['view'] );
		$container_class_list = array_merge( $container_class_list, $this->get_template_css_class() );

		if ( is_rtl() ) {
			$container_class_list[] = 'rtl';
		}

		if ( $this->atts['class'] ) {
			$container_class_list[] = $this->atts['class'];
		}

		$container_data_list = array();
		$container_data_list['state'] = 'idle';

		/**
		 * Filter classes.
		 */
		$this->atts['container_data']  = apply_filters( 'wpmtst_view_container_data', $container_data_list, $this->atts );
		$this->atts['container_class'] = join( ' ', apply_filters( 'wpmtst_view_container_class', $container_class_list, $this->atts ) );

		/**
		 * Store updated atts.
		 */
		WPMST()->set_atts( $this->atts );

	}

	/**
	 * Load validator script.
	 */
	public function load_validator() {

		// Assemble list of field properties for validation script.
		$form_id     = isset( $this->atts['form_id'] ) ? $this->atts['form_id'] : 1;
		$form_fields = wpmtst_get_form_fields( $form_id );
		$fields      = array();

		foreach ( $form_fields as $field ) {

			$fields[] = array(
				'name'     => $field['name'],
				'type'     => $field['input_type'],
				'required' => $field['required'],
			);

			// Load rating stylesheet if necessary.
			if ( isset( $field['input_type'] ) && 'rating' == $field['input_type'] ) {
				WPMST()->render->add_style( 'wpmtst-rating-form' );
			}

		}

		// Load validation scripts
		WPMST()->render->add_script( 'wpmtst-form-validation' );
		if ( wp_script_is( 'wpmtst-validation-lang', 'registered' ) ) {
			WPMST()->render->add_script( 'wpmtst-validation-lang' );
		}

		$form_options = get_option( 'wpmtst_form_options' );

		// Assemble script variable.
		// Remember: top level is converted to strings!
		$scroll = array(
			'onError'         => $form_options['scrolltop_error'] ? true : false,
			'onErrorOffset'   => $form_options['scrolltop_error_offset'],
			'onSuccess'       => $form_options['scrolltop_success'] ? true : false,
			'onSuccessOffset' => $form_options['scrolltop_success_offset'],
		);

		$args = array(
			'scroll' => $scroll,
			'fields' => $fields,
		);

		if ( $this->atts['form_ajax'] ) {
			$args['ajaxUrl'] = admin_url( 'admin-ajax.php' );
		}

		WPMST()->render->add_script_var( 'wpmtst-form-validation', 'strongForm', $args );
		WPMST()->render->add_script( 'wpmtst-controller' );
	}

	/**
	 * Load honeypots.
	 */
	public function load_honeypots() {
		$form_options = get_option( 'wpmtst_form_options' );

		if ( $form_options['honeypot_before'] ) {
			?>
			<script type="text/javascript">
              (function () { document.getElementById("wpmtst_if_visitor").value = '' })()
            </script>
			<?php
		}

		if ( $form_options['honeypot_after'] ) {
            ?>
            <script type="text/javascript">
              (function () {
                var myForm = document.getElementById("wpmtst-submission-form")
                myForm.addEventListener("submit", function(e){
                  var x = document.createElement("input")
                  x.type = "hidden"
                  x.name = "wpmtst_after"
                  x.value = 1
                  myForm.appendChild(x)
                });
              })()
            </script>
            <?php
		}
	}

	/**
	 * Load scripts on form success.
	 *
	 * When using normal form submission (not Ajax)
	 * and displaying a success message (not redirecting).
	 */
	public function on_form_success() {
		$form_options = get_option( 'wpmtst_form_options' );

		// Remember: top level is converted to strings!
		$args = array(
			'display' => array(
				'successMessage' => true,
			),
			'scroll'  => array(
				'onSuccess'       => $form_options['scrolltop_success'],
				'onSuccessOffset' => $form_options['scrolltop_success_offset'],
			),
		);

		WPMST()->render->add_script( 'wpmtst-form-validation' );
		WPMST()->render->add_script_var( 'wpmtst-form-validation', 'strongForm', $args );
	}

}

endif;
