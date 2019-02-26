<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Vc_Automap_Model' ) ) {
	/**
	 * Shortcode as model for automapper. Provides crud functionality for storing data for shortcodes that mapped by ATM
	 *
	 * @see Vc_Automapper
	 * @since 4.1
	 */
	class Vc_Automap_Model {
		/**
		 * @var string
		 */
		protected static $option_name = 'vc_automapped_shortcodes';
		/**
		 * @var
		 */
		protected static $option_data;
		/**
		 * @var array|bool
		 */
		public $id = false;
		public $tag;
		/**
		 * @var mixed
		 */
		protected $data;
		/**
		 * @var array
		 */
		protected $vars = array(
			'tag',
			'name',
			'category',
			'description',
			'params',
		);
		public $name;

		/**
		 * @param $d
		 */
		function __construct( $d ) {
			$this->loadOptionData();
			$this->id = is_array( $d ) && isset( $d['id'] ) ? $d['id'] : $d;
			if ( is_array( $d ) ) {
				$this->data = stripslashes_deep( $d );
			}
			foreach ( $this->vars as $var ) {
				$this->$var = $this->get( $var );
			}
		}

		/**
		 * @return array
		 */
		static function findAll() {
			self::loadOptionData();
			$records = array();
			foreach ( self::$option_data as $id => $record ) {
				$record['id'] = $id;
				$model = new self( $record );
				if ( $model ) {
					$records[] = $model;
				}
			}

			return $records;
		}

		/**
		 * @return array|mixed|void
		 */
		final protected static function loadOptionData() {
			if ( is_null( self::$option_data ) ) {
				self::$option_data = get_option( self::$option_name );
			}
			if ( ! self::$option_data ) {
				self::$option_data = array();
			}

			return self::$option_data;
		}

		/**
		 * @param $key
		 *
		 * @return null
		 */
		function get( $key ) {
			if ( is_null( $this->data ) ) {
				$this->data = isset( self::$option_data[ $this->id ] ) ? self::$option_data[ $this->id ] : array();
			}

			return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
		}

		/**
		 * @param $attr
		 * @param null $value
		 */
		function set( $attr, $value = null ) {
			if ( is_array( $attr ) ) {
				foreach ( $attr as $key => $value ) {
					$this->set( $key, $value );
				}
			} elseif ( ! is_null( $value ) ) {
				$this->$attr = $value;
			}
		}

		/**
		 * @return bool
		 */
		function save() {
			if ( ! $this->isValid() ) {
				return false;
			}
			foreach ( $this->vars as $var ) {
				$this->data[ $var ] = $this->$var;
			}

			return $this->saveOption();
		}

		/**
		 * @return bool
		 */
		function delete() {
			return $this->deleteOption();
		}

		/**
		 * @return bool
		 */
		public function isValid() {
			if ( ! is_string( $this->name ) || empty( $this->name ) ) {
				return false;
			}
			if ( ! preg_match( '/^\S+$/', $this->tag ) ) {
				return false;
			}

			return true;
		}

		/**
		 * @return bool
		 */
		protected function saveOption() {
			self::$option_data[ $this->id ] = $this->data;

			return update_option( self::$option_name, self::$option_data );
		}

		/**
		 * @return bool
		 */
		protected function deleteOption() {
			unset( self::$option_data[ $this->id ] );

			return update_option( self::$option_name, self::$option_data );
		}
	}
}

if ( ! class_exists( 'Vc_Automapper' ) ) {
	/**
	 * Automated shortcode mapping
	 *
	 * Automapper adds settings tab for VC settings tabs with ability to map custom shortcodes to VC editors,
	 * if shortcode is not mapped by default or developers haven't done this yet.
	 * No more shortcode copy/paste. Add any third party shortcode to the list of VC menu elements for reuse.
	 * Edit params, values and description.
	 *
	 * @since 4.1
	 */
	class Vc_Automapper {
		/**
		 * @var bool
		 */
		protected static $disabled = false;

		/**
		 *
		 */
		public function __construct() {
			$this->title = __( 'Shortcode Mapper', 'js_composer' );
		}

		/**
		 *
		 */
		public function addAjaxActions() {
			add_action( 'wp_ajax_vc_automapper', array(
				$this,
				'goAction',
			) );

			return $this;
		}

		/**
		 * Builds html for Automapper CRUD like administration block
		 *
		 * @return bool
		 */
		public function renderHtml() {
			if ( $this->disabled() ) {
				return false;
			}
			?>
			<div class="tab_intro">
				<p><?php _e( 'WPBakery Page Builder Shortcode Mapper adds custom 3rd party vendors shortcodes to the list of WPBakery Page Builder content elements menu (Note: to map shortcode it needs to be installed on site).', 'js_composer' ) ?></p>
			</div>
			<div class="vc_automapper-toolbar">
				<a href="#" class="button button-primary"
					id="vc_automapper-add-btn"><?php _e( 'Map Shortcode', 'js_composer' ) ?></a>
			</div>
			<ul class="vc_automapper-list">
			</ul>
			<?php $this->renderTemplates() ?>
			<?php
			return true;
		}

		/**
		 * @param $shortcode
		 */
		public function renderListItem( $shortcode ) {
			echo '<li class="vc_automapper-item" data-item-id="">' . '<label>' . $shortcode->name . '</label>' . '<span class="vc_automapper-item-controls">' . '<a href="#" class="vc_automapper-edit-btn" data-id="' . $shortcode->id . '" data-tag="' . $shortcode->tag . '"></a>' . '<a href="#" class="vc_automapper-delete-btn" data-id="' . $shortcode->id . '" data-tag="' . $shortcode->tag . '"></a>' . '</span></li>';
		}

		/**
		 *
		 */
		public function renderMapFormTpl() {
			?>
			<script type="text/html" id="vc_automapper-add-form-tpl">
				<label for="vc_atm-shortcode-string"
					class="vc_info"><?php _e( 'Shortcode string', 'js_composer' ) ?></label>

				<div class="vc_wrapper">
					<div class="vc_string">
						<input id="vc_atm-shortcode-string"
							placeholder="<?php _e( 'Please enter valid shortcode', 'js_composer' ) ?>"
							type="text" class="vc_atm-string">
					</div>
					<div class="vc_buttons">
						<a href="#" id="vc_atm-parse-string"
							class="button button-primary vc_parse-btn"><?php _e( 'Parse Shortcode', 'js_composer' ) ?></a>
						<a href="#" class="button vc_atm-cancel"><?php _e( 'Cancel', 'js_composer' ) ?></a>
					</div>
				</div>
				<span
					class="description"><?php _e( 'Enter valid shortcode (Example: [my_shortcode first_param="first_param_value"]My shortcode content[/my_shortcode]).', 'js_composer' ) ?></span>
			</script>
			<script type="text/html" id="vc_automapper-item-complex-tpl">
				<div class="widget-top">
					<div class="widget-title-action">
						<button type="button" class="widget-action hide-if-no-js" aria-expanded="true">
							<span class="screen-reader-text">Edit widget: Search</span>
							<span class="toggle-indicator" aria-hidden="true"></span>
						</button>
					</div>
					<div class="widget-title"><h4>{{ name }}<span class="in-widget-title"></span></h4></div>
				</div>
				<div class="widget-inside">
				</div>
			</script>
			<script type="text/html" id="vc_automapper-form-tpl">
				<input type="hidden" name="name" id="vc_atm-name" value="{{ name }}">

				<div class="vc_shortcode-preview" id="vc_shortcode-preview">
					{{{ shortcode_preview }}}
				</div>
				<div class="vc_line"></div>
				<div class="vc_wrapper">
					<h4 class="vc_h"><?php _e( 'General Information', 'js_composer' ) ?></h4>

					<div class="vc_field vc_tag">
						<label for="vc_atm-tag"><?php _e( 'Tag:', 'js_composer' ) ?></label>
						<input type="text" name="tag" id="vc_atm-tag" value="{{ tag }}">
					</div>
					<div class="vc_field vc_description">
						<label for="vc_atm-description"><?php _e( 'Description:', 'js_composer' ) ?></label>
						<textarea name="description" id="vc_atm-description">{{ description }}</textarea>
					</div>
					<div class="vc_field vc_category">
						<label for="vc_atm-category"><?php _e( 'Category:', 'js_composer' ) ?></label>
						<input type="text" name="category" id="vc_atm-category" value="{{ category }}">
						<span
							class="description"><?php _e( 'Comma separated categories names', 'js_composer' ) ?></span>
					</div>
					<div class="vc_field vc_is-container">
						<label for="vc_atm-is-container"><input type="checkbox" name="is_container"
								id="vc_atm-is-container"
								value=""> <?php _e( 'Include content param into shortcode', 'js_composer' ) ?>
						</label>
					</div>
				</div>
				<div class="vc_line"></div>
				<div class="vc_wrapper">
					<h4 class="vc_h"><?php _e( 'Shortcode Parameters', 'js_composer' ) ?></h4>
					<a href="#" id="vc_atm-add-param"
						class="button vc_add-param">+ <?php _e( 'Add Param', 'js_composer' ) ?></a>

					<div class="vc_params" id="vc_atm-params-list"></div>
				</div>
				<div class="vc_buttons">
					<a href="#" id="vc_atm-save"
						class="button button-primary"><?php _e( 'Save Changes', 'js_composer' ) ?></a>
					<a href="#" class="button vc_atm-cancel"><?php _e( 'Cancel', 'js_composer' ) ?></a>
					<a href="#" class="button vc_atm-delete"><?php _e( 'Delete', 'js_composer' ) ?></a>
				</div>
			</script>
			<script type="text/html" id="vc_atm-form-param-tpl">
				<div class="vc_controls vc_controls-row vc_clearfix"><a
						class="vc_control column_move vc_column-move vc_move-param" href="#"
						title="<?php _e( 'Drag row to reorder', 'js_composer' ) ?>" data-vc-control="move"><i
							class="vc-composer-icon vc-c-icon-dragndrop"></i></a><span class="vc_row_edit_clone_delete"><a
							class="vc_control column_delete vc_delete-param" href="#"
							title="<?php _e( 'Delete this param', 'js_composer' ) ?>"><i class="vc-composer-icon vc-c-icon-delete_empty"></i></a></span>
				</div>
				<div class="wpb_element_wrapper">
					<div class="vc_row vc_row-fluid wpb_row_container">
						<div class="wpb_vc_column wpb_sortable vc_col-sm-12 wpb_content_holder vc_empty-column">
							<div class="wpb_element_wrapper">
								<div class="vc_fields vc_clearfix">
									<div class="vc_param_name vc_param-field">
										<label><?php _e( 'Param name', 'js_composer' ) ?></label>
										<# if ( 'content' === param_name) { #>
											<span class="vc_content"><?php _e( 'Content', 'js_composer' ) ?></span>
											<input type="text" style="display: none;" name="param_name"
												value="{{ param_name }}"
												placeholder="<?php _e( 'Required value', 'js_composer' ) ?>"
												class="vc_param-name"
												data-system="true">
											<span class="description"
												style="display: none;"><?php _e( 'Use only letters, numbers and underscore.', 'js_composer' ) ?></span>
											<# } else { #>
												<input type="text" name="param_name" value="{{ param_name }}"
													placeholder="<?php _e( 'Required value', 'js_composer' ) ?>"
													class="vc_param-name">
												<span
													class="description"><?php _e( 'Please use only letters, numbers and underscore.', 'js_composer' ) ?></span>
												<# } #>
									</div>
									<div class="vc_heading vc_param-field">
										<label><?php _e( 'Heading', 'js_composer' ) ?></label>
										<input type="text" name="heading" value="{{ heading }}"
											placeholder="<?php _e( 'Input heading', 'js_composer' ) ?>"
										<# if ( 'hidden' === type) { #>
											disabled="disabled"
											<# } #>>
												<span
													class="description"><?php _e( 'Heading for field in shortcode edit form.', 'js_composer' ) ?></span>
									</div>
									<div class="vc_type vc_param-field">
										<label><?php _e( 'Field type', 'js_composer' ) ?></label>
										<select name="type">
											<option value=""><?php _e( 'Select field type', 'js_composer' ) ?></option>
											<option
												value="textfield"<?php echo '<# if (type === "textfield") { #> selected<# } #>' ?>><?php _e( 'Textfield', 'js_composer' ) ?></option>
											<option
												value="dropdown"<?php echo '<# if (type === "dropdown") { #> selected<# } #>' ?>><?php _e( 'Dropdown', 'js_composer' ) ?></option>
											<option
												value="textarea"<?php echo '<# if(type==="textarea") { #> selected="selected"<# } #>' ?>><?php _e( 'Textarea', 'js_composer' ) ?></option>
											<# if ( 'content' === param_name ) { #>
												<option
													value="textarea_html"<?php echo '<# if (type === "textarea_html") { #> selected<# } #>' ?>><?php _e( 'Textarea HTML', 'js_composer' ) ?></option>
												<# } #>
													<option
														value="hidden"<?php echo '<# if (type === "hidden") { #> selected<# } #>' ?>><?php _e( 'Hidden', 'js_composer' ) ?></option>

										</select>
										<span
											class="description"><?php _e( 'Field type for shortcode edit form.', 'js_composer' ) ?></span>
									</div>
									<div class="vc_value vc_param-field">
										<label><?php _e( 'Default value', 'js_composer' ) ?></label>
										<input type="text" name="value" value="{{ value }}" class="vc_param-value">
										<span
											class="description"><?php _e( 'Default value or list of values for dropdown type (Note: separate by comma).', 'js_composer' ) ?></span>
									</div>
									<div class="description vc_param-field">
										<label><?php _e( 'Description', 'js_composer' ) ?></label>
										<textarea name="description" placeholder=""
										<# if ( 'hidden' === type ) { #>
											disabled="disabled"
											<# } #> >{{ description
												}}</textarea>
												<span
													class="description"><?php _e( 'Enter description for parameter.', 'js_composer' ) ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</script>
			<?php
		}

		/**
		 *
		 */
		public function renderTemplates() {
			?>
			<script type="text/html" id="vc_automapper-item-tpl">
				<label class="vc_automapper-edit-btn">{{ name }}</label>
				<span class="vc_automapper-item-controls">
					<a href="#" class="vc_automapper-delete-btn" title="<?php _e( 'Delete', 'js_composer' ) ?>"></a>
					<a href="#" class="vc_automapper-edit-btn" title="<?php _e( 'Edit', 'js_composer' ) ?>"></a>
				</span>
			</script>
			<?php
			$this->renderMapFormTpl();
		}

		/**
		 * Action methods(CRUD)
		 */
		public function goAction() {
			vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'manage_options' )->validateDie()->part( 'settings' )->can( 'vc-automapper-tab' )->validateDie();

			$action = vc_post_param( 'vc_action' );
			$this->result( $this->$action() );
		}

		/**
		 * @return bool
		 */
		public function create() {
			$data = vc_post_param( 'data' );
			$shortcode = new Vc_Automap_Model( $data );

			return $shortcode->save();
		}

		/**
		 * @return bool
		 */
		public function update() {
			$id = vc_post_param( 'id' );
			$data = vc_post_param( 'data' );
			$shortcode = new Vc_Automap_Model( $id );
			if ( ! isset( $data['params'] ) ) {
				$data['params'] = array();
			}
			$shortcode->set( $data );

			return $shortcode->save();
		}

		/**
		 * @return bool
		 */
		public function delete() {
			$id = vc_post_param( 'id' );
			$shortcode = new Vc_Automap_Model( $id );

			return $shortcode->delete();
		}

		/**
		 * @return array
		 */
		public function read() {
			return Vc_Automap_Model::findAll();
		}

		/**
		 * Ajax result output
		 *
		 * @param $data
		 */
		function result( $data ) {
			echo is_array( $data ) || is_object( $data ) ? json_encode( $data ) : $data;
			die();
		}

		/**
		 * Setter/Getter for Disabling Automapper
		 * @static
		 *
		 * @param bool $disable
		 */
		public static function setDisabled( $disable = true ) {
			self::$disabled = $disable;
		}

		/**
		 * @return bool
		 */
		public static function disabled() {
			return self::$disabled;
		}

		/**
		 * Setter/Getter for Automapper title
		 *
		 * @static
		 *
		 * @param string $title
		 */
		public function setTitle( $title ) {
			$this->title = $title;
		}

		/**
		 * @return string|void
		 */
		public function title() {
			return $this->title;
		}

		/**
		 *
		 */
		public static function map() {
			$shortcodes = Vc_Automap_Model::findAll();
			foreach ( $shortcodes as $shortcode ) {
				vc_map( array(
					'name' => $shortcode->name,
					'base' => $shortcode->tag,
					'category' => vc_atm_build_categories_array( $shortcode->category ),
					'description' => $shortcode->description,
					'params' => vc_atm_build_params_array( $shortcode->params ),
					'show_settings_on_create' => ! empty( $shortcode->params ),
					'atm' => true,
					'icon' => 'icon-wpb-atm',
				) );
			}
		}
	}
}

// Helpers
if ( ! function_exists( 'vc_atm_build_categories_array' ) ) {
	/**
	 * @param $string
	 *
	 * @return array
	 */
	function vc_atm_build_categories_array( $string ) {
		return explode( ',', preg_replace( '/\,\s+/', ',', trim( $string ) ) );
	}
}
if ( ! function_exists( 'vc_atm_build_params_array' ) ) {
	/**
	 * @param $array
	 *
	 * @return array
	 */
	function vc_atm_build_params_array( $array ) {
		$params = array();
		if ( is_array( $array ) ) {
			foreach ( $array as $param ) {
				if ( 'dropdown' === $param['type'] ) {
					$param['value'] = explode( ',', preg_replace( '/\,\s+/', ',', trim( $param['value'] ) ) );
				}
				$param['save_always'] = true;
				$params[] = $param;
			}
		}

		return $params;
	}
}
