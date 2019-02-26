<?php

class WPML_TM_Dashboard_Display_Filter {

	const PARENT_TAXONOMY_CONTAINER         = 'parent-taxonomy-container';
	const PARENT_SELECT_ID                  = 'parent-filter-control';
	const PARENT_SELECT_NAME                = 'filter[parent_type]';
	const PARENT_OR_TAXONOMY_ITEM_CONTAINER = 'parent-taxonomy-item-container';

	private $active_languages = array();
	private $translation_filter;
	private $post_types;
	private $post_statuses;
	private $source_language_code;
	private $priorities;

	/** @var wpdb $wpdb */
	private $wpdb;

	public function __construct(
		$active_languages,
		$source_language_code,
		$translation_filter,
		$post_types,
		$post_statuses,
		array $priorities,
		wpdb $wpdb
	) {
		$this->active_languages     = $active_languages;
		$this->translation_filter   = $translation_filter;
		$this->source_language_code = $source_language_code;
		$this->post_types           = $post_types;
		$this->post_statuses        = $post_statuses;
		$this->priorities           = $priorities;
		$this->wpdb                 = $wpdb;
	}

	private function from_lang_select() {
		$select_attributes_id    = 'icl_language_selector';
		$select_attributes_name  = 'filter[from_lang]';
		$select_attributes_label = __( 'in', 'wpml-translation-management' );

		?>
		<label for="<?php echo esc_attr( $select_attributes_id ); ?>">
			<?php echo esc_html( $select_attributes_label ); ?>
		</label>
		<?php
		if ( $this->source_language_code ) {
			$tooltip = $this->get_from_language_filter_lock_message_if_required();
			?>
			<input type="hidden" id="<?php echo esc_attr( $select_attributes_id ); ?>" name="<?php echo esc_attr( $select_attributes_name ); ?>" value="<?php echo esc_attr( $this->get_language_from() ); ?>"/>
			<span class="wpml-tm-filter-disabled js-wpml-popover-tooltip" title="<?php echo esc_attr( $tooltip ); ?>"><?php echo esc_html( $this->active_languages[ $this->get_language_from() ]['display_name'] ); ?></span>
			<?php
		} else {
			?>
			<select id="<?php echo esc_attr( $select_attributes_id ); ?>" name="<?php echo esc_attr( $select_attributes_name ); ?>" title="<?php echo esc_attr( $select_attributes_label ); ?>">
				<?php
				foreach ( $this->active_languages as $lang ) {
					$selected = '';

					if ( $this->get_language_from() && $lang['code'] == $this->get_language_from() ) {
						$selected = 'selected="selected"';
					}
					?>
					<option value="<?php echo esc_attr( $lang['code'] ); ?>" <?php echo $selected; ?>>
						<?php
						echo esc_html( $lang['display_name'] ); ?>
					</option>
					<?php
				}
				?>
			</select>
			<?php
		}
		?>
		<?php
	}

	private function get_language_from() {
		$languages_from = $this->source_language_code;
		if ( ! $languages_from ) {
			$languages_from = $this->get_language_from_filter();
		}

		return $languages_from;
	}

	private function get_language_from_filter() {
		if ( array_key_exists( 'from_lang', $this->translation_filter ) && $this->translation_filter['from_lang'] ) {
			return $this->translation_filter['from_lang'];
		}

		return null;
	}

	private function to_lang_select() {
		?>
		<label for="filter_to_lang">
			<?php esc_html_e( 'translated to', 'wpml-translation-management' ); ?>
		</label>
		<select id="filter_to_lang" name="filter[to_lang]">
			<option value=""><?php esc_html_e( 'Any language', 'wpml-translation-management' ) ?></option>
			<?php
			foreach ( $this->active_languages as $lang ) {
				$selected = selected( $this->translation_filter['to_lang'], $lang['code'], false );
				?>
				<option value="<?php echo esc_attr( $lang['code'] ); ?>" <?php echo $selected; ?>>
					<?php echo esc_html( $lang['display_name'] ); ?>
				</option>
				<?php
			}
			?>
		</select>
		<?php
	}

	private function translation_status_select() {
		?>
		<select id="filter_tstatus" name="filter[tstatus]" title="<?php esc_attr_e( 'Translation status', 'wpml-translation-management' ); ?>">
			<?php
			$option_status = array(
				-1                    => esc_html__( 'All translation statuses', 'wpml-translation-management' ),
				ICL_TM_NOT_TRANSLATED => esc_html__(
					'Not translated or needs updating',
					'wpml-translation-management'
				),
				ICL_TM_NEEDS_UPDATE   => esc_html__( 'Needs updating', 'wpml-translation-management' ),
				ICL_TM_IN_PROGRESS    => esc_html__( 'Translation in progress', 'wpml-translation-management' ),
				ICL_TM_COMPLETE       => esc_html__( 'Translation complete', 'wpml-translation-management' )
			);
			foreach ( $option_status as $status_key => $status_value ) {
				$selected = selected( $this->translation_filter['tstatus'], $status_key, false );

				?>
				<option value="<?php echo $status_key ?>" <?php echo $selected; ?>><?php echo $status_value ?></option>
				<?php
			}
			?>
		</select>
		<?php
	}

	private function get_from_language_filter_lock_message_if_required() {
		$basket_locked_string = null;
		if ( $this->source_language_code && isset( $this->active_languages[ $this->source_language_code ] ) ) {
			$language_name        = $this->active_languages[ $this->source_language_code ]['display_name'];
			$basket_locked_string = '<p>';
			$basket_locked_string .= sprintf(
				esc_html__(
					'Language filtering has been disabled because you already have items in %s in the basket.',
					'wpml-translation-management'
				),
				$language_name
			);
			$basket_locked_string .= '<br/>';
			$basket_locked_string .= esc_html__(
				'To re-enable it, please empty the basket or send it for translation.',
				'wpml-translation-management'
			);
			$basket_locked_string .= '</p>';

		}

		return $basket_locked_string;
	}

	private function display_post_type_select() {
		$selected_type = isset( $this->translation_filter['type'] ) ? $this->translation_filter['type'] : false;
		?>
		<select id="filter_type" name="filter[type]" title="<?php esc_attr_e( 'Element type', 'wpml-translation-management' ); ?>">
			<option value=""><?php esc_html_e( 'All types', 'wpml-translation-management' ) ?></option>
			<?php
			foreach ( $this->post_types as $post_type_key => $post_type ) {
				$filter_type_selected = selected( $selected_type, $post_type_key, false );
				$hierarchical         = is_post_type_hierarchical( $post_type_key ) ? 'true' : 'false';
				$taxonomy_string      = '';
				foreach ( get_object_taxonomies( $post_type_key, 'objects' ) as $taxonomy => $taxonomy_object ) {
					if ( $this->has_taxonomy_terms_in_any_language( $taxonomy ) ) {
						if ( $taxonomy_string ) {
							$taxonomy_string .= ',';
						}
						$taxonomy_string .= $taxonomy . '=' . $taxonomy_object->label;
					}
				}
				?>
				<option
					value="<?php echo $post_type_key ?>"
					data-parent="<?php echo $hierarchical; ?>"
					data-taxonomy="<?php echo $taxonomy_string; ?>"
					<?php echo $filter_type_selected; ?>
				>
					<?php echo $post_type->labels->singular_name != "" ? $post_type->labels->singular_name
						: $post_type->labels->name; ?>
				</option>
				<?php
			}
			?>
		</select>
		<?php
	}

	private function display_parent_taxonomy_controls() {
		?>

		<span id="<?php echo self::PARENT_TAXONOMY_CONTAINER; ?>" style="display:none;">
		        <label for="<?php echo self::PARENT_SELECT_ID; ?>">
			        <?php esc_html_e( 'parent', 'wpml-translation-management' ); ?>
		        </label>
		        <select
					id="<?php echo self::PARENT_SELECT_ID; ?>"
					name="<?php echo self::PARENT_SELECT_NAME; ?>"
					data-original="<?php echo isset( $this->translation_filter['parent_type'] ) ? $this->translation_filter['parent_type'] : 'any' ?>"
				>
		        </select>

		        <span name="<?php echo self::PARENT_OR_TAXONOMY_ITEM_CONTAINER; ?>" class="<?php echo self::PARENT_OR_TAXONOMY_ITEM_CONTAINER; ?>">
			        <input type="hidden" name="filter[parent_id]" value="<?php echo isset( $this->translation_filter['parent_id'] ) ? $this->translation_filter['parent_id'] : '' ?>"/>
		        </span>
	        </span>
		<?php
	}

	private function filter_title_textbox() {
		$title = isset( $this->translation_filter['title'] ) ? $this->translation_filter['title'] : '';
		?>
		<input type="text" id="filter_title" name="filter[title]"
			   value="<?php echo esc_attr( $title ); ?>"
			   placeholder="<?php esc_attr_e( 'Title', 'wpml-translation-management' ); ?>"
		/>
		<?php
	}

	private function display_post_statuses_select() {
		$filter_post_status = isset( $this->translation_filter['status'] ) ? $this->translation_filter['status']
			: false;

		?>
		<select id="filter_status" name="filter[status]" title="<?php esc_attr_e( 'Publish status', 'wpml-translation-management' ); ?>">
			<option value=""><?php esc_html_e( 'All statuses', 'wpml-translation-management' ) ?></option>
			<?php
			foreach ( $this->post_statuses as $post_status_k => $post_status ) {
				$post_status_selected = selected( $filter_post_status, $post_status_k, false );
				?>
				<option value="<?php echo $post_status_k ?>" <?php echo $post_status_selected; ?>>
					<?php echo $post_status ?>
				</option>
				<?php
			}
			?>
		</select>
		<?php
	}

	private function display_post_translation_priority_select() {
		$filter_translation_priority = isset( $this->translation_filter['translation_priority'] ) ? $this->translation_filter['translation_priority']
			: false;

		?>
		<select id="filter_translation_priority" name="filter[translation_priority]" title="<?php esc_attr_e( 'Priority', 'wpml-translation-management' ); ?>">
			<option value=""><?php esc_html_e( 'All Translation Priorities', 'wpml-translation-management' ) ?></option>
			<?php
			foreach ( $this->priorities as $priority ) {
				$translation_priority_selected = selected( $filter_translation_priority, $priority->term_id, false );
				?>
				<option value="<?php echo esc_attr( $priority->term_id ); ?>" <?php echo $translation_priority_selected; ?>>
					<?php echo esc_html( $priority->name ); ?>
				</option>
				<?php
			}
			?>
		</select>
		<?php
	}

	private function display_button() {
		$reset_url = $this->get_admin_page_url( array( 'page' => WPML_TM_FOLDER . '/menu/main.php', 'sm' => 'dashboard', 'action' => 'reset' ) );
		?>
		<input id="translation_dashboard_filter" name="translation_dashboard_filter"
			   class="button-secondary" type="submit"
			   value="<?php esc_attr_e( 'Filter', 'wpml-translation-management' ) ?>"/>

		<a type="reset" href="<?php echo esc_url( $reset_url ); ?>" class="wpml-reset-filter"><i class="otgs-ico-close"> </i><?php esc_html_e( 'Reset filter', 'wpml-translation-management' ); ?></a>

		<?php
	}

	public function display() {
		$form_url = $this->get_admin_page_url( array( 'page' => WPML_TM_FOLDER . '/menu/main.php', 'sm' => 'dashboard' ) );
		?>
		<form method="post" name="translation-dashboard-filter" class="wpml-tm-dashboard-filter" action="<?php echo esc_url( $form_url ); ?>">
			<input type="hidden" name="icl_tm_action" value="dashboard_filter"/>

			<?php

			do_action( 'display_basket_notification', 'tm_dashboard_top' );
			$this->heading( __( '1. Select items for translation', 'wpml-translation-management' ) );
			$this->display_post_type_select();
			$this->display_parent_taxonomy_controls();
			$this->from_lang_select();
			$this->to_lang_select();
			$this->translation_status_select();
			$this->display_post_statuses_select();
			$this->display_post_translation_priority_select();
			$this->filter_title_textbox();
			$this->display_button();

			?>
		</form>
		<?php
	}

	private function has_taxonomy_terms_in_any_language( $taxonomy ) {
		return $this->wpdb->get_var(
				$this->wpdb->prepare(
					"SELECT COUNT(translation_id) FROM {$this->wpdb->prefix}icl_translations WHERE element_type=%s",
					'tax_' . $taxonomy
				)
			) > 0;
	}

	private function heading( $text ) {
		?>
		<h3 class="wpml-tm-section-header"><?php echo esc_html( $text ) ?></h3>
		<?php
	}

	private function get_admin_page_url( array $query_args ) {
		$base_url = admin_url( 'admin.php' );

		return add_query_arg( $query_args, $base_url );
	}

}
