<?php
/**
 * View admin functions.
 *
 * @since 1.21.0
 * @package Strong_Testimonials
 */


/**
 * View list page.
 *
 * @since 1.21.0
 */
function wpmtst_views_admin() {
	if ( ! current_user_can( 'strong_testimonials_views' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

	?>
	<div class="wrap wpmtst2">

		<?php
		if ( isset( $_REQUEST['cancelled'] ) ) {
			$message = __( 'Changes cancelled.', 'strong-testimonials' );
		} elseif ( isset( $_REQUEST['defaults-restored'] ) ) {
			$message = __( 'Defaults restored.', 'strong-testimonials' );
		} elseif ( isset( $_REQUEST['view-saved'] ) ) {
			$message = __( 'View saved.', 'strong-testimonials' );
		} elseif( isset( $_REQUEST['view-deleted'] ) ) {
			$message = __( 'View deleted.', 'strong-testimonials' );
		} else {
			$message = '';
		}

		if ( $message ) {
			printf( '<div class="notice is-dismissible updated"><p>%s</p></div>', $message );
		}

		if ( isset( $_REQUEST['error'] ) ) {

			echo '<h1>' . __( 'Edit View', 'strong-testimonials' ) . '</h1>';
			$message = __( 'An error occurred.', 'strong-testimonials' ) . ' ' . sprintf( __( 'Please <a href="%s" target="_blank">open a support ticket</a>.', 'strong-testimonials' ), esc_url( 'https://support.strongplugins.com/new-ticket/' ) );
			wp_die( sprintf( '<div class="error strong-view-error"><p>%s</p></div>', $message ) );

		} elseif ( isset( $_REQUEST['action'] ) ) {

			if ( 'edit' == $_REQUEST['action'] && isset( $_REQUEST['id'] ) ) {
				wpmtst_view_settings( $_REQUEST['action'], $_REQUEST['id'] );
			}
			elseif ( 'duplicate' == $_REQUEST['action'] && isset( $_REQUEST['id'] ) ) {
				wpmtst_view_settings( $_REQUEST['action'], $_REQUEST['id'] );
			}
			elseif ( 'add' == $_REQUEST['action'] ) {
				wpmtst_view_settings( $_REQUEST['action'] );
			}
			else {
				echo '<p>' . __( 'Invalid request. Please try again.', 'strong-testimonials' ) . '</p>';
			}

		} else {

			/**
             * View list
             */
			?>
			<h1>
				<?php _e( 'Views', 'strong-testimonials' ); ?>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wpm-testimonial&page=testimonial-views&action=add' ) ); ?>" class="add-new-h2"><?php _e( 'Add New' ); ?></a>
                <a href="#tab-panel-wpmtst-help-views" class="add-new-h2 open-help-tab"><?php _e( 'Help' ); ?></a>
			</h1>

			<?php
			// Fetch views after heading and before intro in case we need to display any database errors.
			$views = wpmtst_get_views();

			// Add button to clear sort value.
			if ( isset( $_GET['orderby'] ) ) {
				?>
                <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" style="margin-bottom: 4px;">
                    <input type="hidden" name="action" value="clear-view-sort">
                    <input type="submit" value="clear sort" class="button">
                </form>
				<?php
			}

            // Display the table
			$views_table = new Strong_Views_List_Table();
			$views_table->prepare_list( wpmtst_unserialize_views( $views ) );
			$views_table->display();

		}
		?>
	</div><!-- .wrap -->
	<?php
}


/**
 * An individual view settings page.
 *
 * @since 1.21.0
 *
 * @param string $action
 * @param null   $view_id
 */
function wpmtst_view_settings( $action = '', $view_id = null ) {

	if ( ( 'edit' == $action || 'duplicate' == $action ) && ! $view_id ) return;

	global $view;
	add_thickbox();

	$fields     = wpmtst_get_custom_fields();
	$all_fields = wpmtst_get_all_fields();

	$testimonials_list = get_posts( array(
		'orderby'          => 'post_date',
		'order'            => 'ASC',
		'post_type'        => 'wpm-testimonial',
		'post_status'      => 'publish',
		'posts_per_page'   => -1,
		'suppress_filters' => true,
	) );

	$cat_count = wpmtst_get_cat_count();

	/**
	 * Show category filter if necessary.
	 *
	 * @since 2.2.0
	 */
	if ( $cat_count > 5 ) {
		wp_enqueue_script( 'wpmtst-view-category-filter-script' );
	}

	$default_view = apply_filters( 'wpmtst_view_default', get_option( 'wpmtst_view_default' ) );

	if ( 'edit' == $action ) {
		$view_array = wpmtst_get_view( $view_id );
		$view       = unserialize( $view_array['value'] );
		$view_name  = $view_array['name'];
	} elseif ( 'duplicate' == $action ) {
		$view_array = wpmtst_get_view( $view_id );
		$view       = unserialize( $view_array['value'] );
		$view_id    = 0;
		$view_name  = $view_array['name'] . ' - COPY';
	} else {
		$view_id   = 1;
		$view      = $default_view;
		$view_name = 'new';
	}

	/**
	 * Attempt to repair bug from 2.28.2
	 */
	if ( ! isset( $view['pagination_settings']['end_size'] ) || ! $view['pagination_settings']['end_size'] ) {
		$view['pagination_settings']['end_size'] = 1;
	}
	if ( ! isset( $view['pagination_settings']['mid_size'] ) || ! $view['pagination_settings']['mid_size'] ) {
		$view['pagination_settings']['mid_size'] = 2;
	}
	if ( ! isset( $view['pagination_settings']['per_page'] ) || ! $view['pagination_settings']['per_page'] ) {
		$view['pagination_settings']['per_page'] = 5;
	}

	$custom_list  = apply_filters( 'wpmtst_custom_pages_list', array(), $view );
	$pages_list   = apply_filters( 'wpmtst_pages_list', wpmtst_get_pages() );
	$posts_list   = apply_filters( 'wpmtst_posts_list', wpmtst_get_posts() );

	$view_options = apply_filters( 'wpmtst_view_options', get_option( 'wpmtst_view_options' ) );

	// Select default template if necessary
	if ( !$view['template'] ) {
		if ( 'form' == $view['mode'] ) {
			$view['template'] = 'default-form';
		}
		else {
			$view['template'] = 'default';
		}
	}

	$view_cats_array = apply_filters( 'wpmtst_l10n_cats', explode( ',', $view['category'] ) );

	// Assemble list of templates
	$templates = array(
		'display' => WPMST()->templates->get_templates( 'display' ),
		'form'    => WPMST()->templates->get_templates( 'form' ),
	);
	$template_keys  = WPMST()->templates->get_template_keys();
	$template_found = in_array( $view['template'], $template_keys );

	// Get list of image sizes
	$image_sizes = wpmtst_get_image_sizes();

	$url = admin_url( 'edit.php?post_type=wpm-testimonial&page=testimonial-views' );
	$url1 = $url . '&action=add';
	$url2 = $url . '&action=duplicate&id=' . $view_id;
	?>
	<h1>
		<?php 'edit' == $action ? _e( 'Edit View', 'strong-testimonials' ) : _e( 'Add View', 'strong-testimonials' ); ?>
		<a href="<?php echo esc_url( $url1 ); ?>" class="add-new-h2"><?php _e( 'Add New' ); ?></a>
        <a href="<?php echo esc_url( $url ); ?>" class="add-new-h2"><?php _e( 'Return To List', 'strong-testimonials' ); ?></a>
        <?php if ( 'edit' == $action ) : ?>
        <a href="<?php echo esc_url( $url2 ); ?>" class="add-new-h2"><?php _e( 'Duplicate This View', 'strong-testimonials' ); ?></a>
        <?php endif; ?>
	</h1>

	<form id="wpmtst-views-form" method="post" action="<?php echo get_admin_url() . 'admin-post.php'; ?>" autocomplete="off">

		<?php wp_nonce_field( 'view_form_submit', 'view_form_nonce', true, true ); ?>

		<input type="hidden" name="action" value="view_<?php echo $action; ?>_form">
		<input type="hidden" name="view[id]" value="<?php echo $view_id; ?>">
		<input type="hidden" name="view_original_mode" value="<?php echo $view['mode']; ?>">
		<input type="hidden" name="view[data][_form_id]" value="<?php echo $view['form_id']; ?>">

        <div class="table view-info">
			<?php include( 'partials/views/view-name.php' ); ?>
    		<?php include( 'partials/views/view-shortcode.php' ); ?>
	    	<?php include( 'partials/views/view-mode.php' ); ?>
        </div>

        <?php
		// TODO Generify both hook and include
		do_action( 'wpmtst_view_editor_before_group_select' );
		include( 'partials/views/group-query.php' );

		do_action( 'wpmtst_view_editor_before_group_slideshow' );
		include( 'partials/views/group-slideshow.php' );

		do_action( 'wpmtst_view_editor_before_group_fields' );
		include( 'partials/views/group-fields.php' );

		do_action( 'wpmtst_view_editor_before_group_form' );
		include( 'partials/views/group-form.php' );

		do_action( 'wpmtst_view_editor_before_group_extra' );
		include( 'partials/views/group-extra.php' );

		do_action( 'wpmtst_view_editor_before_group_style' );
		include( 'partials/views/group-style.php' );

		do_action( 'wpmtst_view_editor_before_group_compat' );
		include( 'partials/views/group-compat.php' );

        // For back-compat. General group no longer used.
		do_action( 'wpmtst_view_editor_before_group_general' );

		do_action( 'wpmtst_view_editor_after_groups' );
		?>

		<p class="wpmtst-submit">
			<?php submit_button( '', 'primary', 'submit-form', false ); ?>
			<?php submit_button( __( 'Cancel Changes', 'strong-testimonials' ), 'secondary', 'reset', false ); ?>
			<?php submit_button( __( 'Restore Defaults', 'strong-testimonials' ), 'secondary', 'restore-defaults', false ); ?>
		</p>

	</form>
	<?php
}


/**
 * Process form POST after editing.
 *
 * Thanks http://stackoverflow.com/a/20003981/51600
 *
 * @since 1.21.0
 */
function wpmtst_view_edit_form() {

	$goback = wp_get_referer();

	if ( ! empty( $_POST ) && check_admin_referer( 'view_form_submit', 'view_form_nonce' ) ) {

		$view_id    = $_POST['view']['id'];
		$view_name  = wpmtst_validate_view_name( $_POST['view']['name'], $view_id );

		if ( isset( $_POST['reset'] ) ) {

			// Undo changes
			$goback = remove_query_arg( array( 'defaults-restored', 'view-saved' ), $goback );
			$goback = add_query_arg( 'cancelled', true, $goback );

		} elseif ( isset( $_POST['restore-defaults'] ) ) {

			// Restore defaults
			$default_view = get_option( 'wpmtst_view_default' );

			$view = array(
				'id'   => $view_id,
				'name' => $view_name,
				'data' => $default_view
			);
			$success = wpmtst_save_view( $view ); // num_rows

			if ( $success ) {
				$goback = add_query_arg( 'defaults-restored', true, $goback );
			} else {
				$goback = add_query_arg( 'error', true, $goback );
			}

		} else {

			// Sanitize & validate
			$view = array(
				'id'   => $view_id,
				'name' => $view_name,
				'data' => wpmtst_sanitize_view( stripslashes_deep( $_POST['view']['data'] ) ),
			);
			$success = wpmtst_save_view( $view ); // num_rows

			if ( $success ) {
				$goback = remove_query_arg( array( 'defaults-restored', 'cancelled' ), $goback );
				$goback = add_query_arg( 'view-saved', true, $goback );
			} else {
				$goback = add_query_arg( 'error', true, $goback );
			}

		}

	} else {
		$goback = add_query_arg( 'error', true, $goback );
	}

	wp_redirect( $goback );
	exit;

}
add_action( 'admin_post_view_edit_form', 'wpmtst_view_edit_form' );


/**
 * Process form POST after adding.
 *
 * @since 1.21.0
 */
function wpmtst_view_add_form() {

	$goback = wp_get_referer();

	if ( ! empty( $_POST ) && check_admin_referer( 'view_form_submit', 'view_form_nonce' ) ) {

		$view_id   = 0;
		$view_name = wpmtst_validate_view_name( $_POST['view']['name'], $view_id );

		if ( isset( $_POST['restore-defaults'] ) ) {

			// Restore defaults
			$default_view = get_option( 'wpmtst_view_default' );

			$view = array(
				'id'   => $view_id,
				'name' => $view_name,
				'data' => $default_view,
			);
			$success = wpmtst_save_view( $view, 'add' ); // num_rows

			$query_arg = 'defaults-restored';

		} else {

			// Sanitize & validate
			$view = array(
				'id'   => 0,
				'name' => $view_name,
				'data' => wpmtst_sanitize_view( stripslashes_deep( $_POST['view']['data'] ) ),
			);
			$success = wpmtst_save_view( $view, 'add' ); // new id

			$query_arg = 'view-saved';

		}

		$goback = remove_query_arg( array( 'action', 'defaults-restored', 'cancelled' ), $goback );
		if ( $success ) {
			$goback = add_query_arg( array( 'action' => 'edit', 'id' => $success, $query_arg => true ), $goback );
		} else {
			$goback = add_query_arg( 'error', true, $goback );
		}

	} else {
		$goback = add_query_arg( 'error', true, $goback );
	}

	wp_redirect( $goback );
	exit;

}
add_action( 'admin_post_view_add_form', 'wpmtst_view_add_form' );
add_action( 'admin_post_view_duplicate_form', 'wpmtst_view_add_form' );


/**
 * --------------
 * VIEW FUNCTIONS
 * --------------
 */

/**
 * Fetch pages, bypass filters.
 *
 * @since 2.10.0
 *
 * @return array|null|object
 */
function wpmtst_get_pages() {
	global $wpdb;
	$query = "SELECT * FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish' ORDER BY post_title ASC";

	$pages = $wpdb->get_results( $query );

	return $pages;
}


/**
 * Fetch pages, bypass filters.
 *
 * @since 2.10.0
 *
 * @return array|null|object
 */
function wpmtst_get_posts() {
	global $wpdb;
	$query = "SELECT * FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_title ASC";

	$posts = $wpdb->get_results( $query );

	return $posts;
}

/**
 * Filter the custom fields.
 * Until WordPress abandons PHP 5.2
 *
 * @since 2.17.0 Remove [category] from custom because it's included in [optional].
 * @since 2.23.0 Remove checkboxes.
 *
 * @param $field
 *
 * @return bool
 */
function wpmtst_array_filter__custom_fields( $field ) {
	if ( 'category' == strtok( $field['input_type'], '-' ) ) {
		return false;
	}
	if ( 'checkbox' == $field['input_type'] ) {
		return false;
	}

	return true;
}


/**
 * Show a single client field's inputs.
 *
 * @since 1.21.0
 *
 * @param $key
 * @param $field
 * @param bool $adding
 */
function wpmtst_view_field_inputs( $key, $field, $adding = false ) {
	$custom_fields = array_filter( wpmtst_get_custom_fields(), 'wpmtst_array_filter__custom_fields' );

	$builtin_fields = wpmtst_get_builtin_fields();

	$all_fields = array(
		__( 'custom', 'strong-testimonials' )  => $custom_fields,
		__( 'builtin', 'strong-testimonials' ) => $builtin_fields
	);

	$allowed = array( 'custom', 'optional', 'builtin' );

	// TODO Move this to view defaults option.
	$types = array(
		'text'      => __( 'text', 'strong-testimonials' ),
		'link'      => __( 'link with another field', 'strong-testimonials' ),  // the original link type
		'link2'     => __( 'link (must be URL type)', 'strong-testimonials' ),  // @since 1.24.0
		'date'      => __( 'date', 'strong-testimonials' ),
		'category'  => __( 'category', 'strong-testimonials' ),
		'rating'    => __( 'rating', 'strong-testimonials' ),
		'shortcode' => __( 'shortcode', 'strong-testimonials' ),
	);

	if ( isset( $custom_fields[ $field['field'] ] ) ) {
		$field_label = $custom_fields[ $field['field'] ]['label'];
	} else {
	    $field_label = ucwords( str_replace( '_', ' ', $field['field'] ) );
	}

	/**
	 * Catch and highlight fields not found in custom fields; i.e. it has been deleted.
	 *
     * @since 2.17.0
	 */
	$all_field_names = array_merge( array_keys( $custom_fields), array( 'post_date', 'submit_date', 'category' ) );
	$label_class = '';
	if ( ! $adding && ! in_array( $field['field'], $all_field_names ) ) {
	    $field_label .= ' < ERROR - not found >';
	    $label_class = 'error';
	}
	?>
	<div id="field-<?php echo $key; ?>" class="field2">

		<div class="field3" data-key="<?php echo $key; ?>">

			<span class="link" title="<?php _e( 'click to open or close', 'strong-testimonials' ); ?>">

				<a href="#" class="field-description <?php echo $label_class; ?>"><?php echo $field_label; ?></a>

				<div class="controls2 left">
					<span class="handle ui-sortable-handle icon-wrap"
						  title="<?php _e( 'drag and drop to reorder', 'strong-testimonials' ); ?>"></span>
					<span class="delete icon-wrap"
						  title="<?php _e( 'remove this field', 'strong-testimonials' ); ?>"></span>
				</div>

				<div class="controls2 right">
					<span class="toggle icon-wrap"
						  title="<?php _e( 'click to open or close', 'strong-testimonials' ); ?>"></span>
				</div>

			</span>

			<div class="field-properties" style="display: none;">

                <!-- FIELD NAME -->
                <div class="field-property field-name">
                    <label for="client_section_<?php echo $key; ?>_field">
                        <?php _e( 'Name', 'strong-testimonials' ); ?>
                    </label>
                    <select id="client_section_<?php echo $key; ?>_field" name="view[data][client_section][<?php echo $key; ?>][field]" class="first-field">
                        <option value="">&mdash; select a field &mdash;</option>

                        <?php foreach ( $all_fields as $group_name => $group ) : ?>
                        <optgroup label="<?php echo $group_name; ?>">

                        <?php foreach ( $group as $key2 => $field2 ) : ?>
                        <?php if ( in_array( $field2['record_type'], $allowed ) && 'email' != $field2['input_type'] ) : ?>
                        <option value="<?php echo $field2['name']; ?>" data-type="<?php echo $field2['input_type']; ?>"
                            <?php selected( $field2['name'], $field['field'] ); ?>><?php echo $field2['name']; ?></option>
                        <?php endif; ?>
                        <?php endforeach; ?>

                        </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- FIELD TYPE -->
                <div class="field-property field-type field-dep" <?php if ( $adding ) echo ' style="display: none;"'; ?>>
                    <label for="client_section_<?php echo $key; ?>_type">
                        <?php _e( 'Display Type', 'strong-testimonials' ); ?>
                    </label>
                    <select id="client_section_<?php echo $key; ?>_type" name="view[data][client_section][<?php echo $key; ?>][type]">
                        <?php foreach ( $types as $type => $type_label ) : ?>
                        <option value="<?php echo $type; ?>" <?php selected( $type, $field['type'] ); ?>><?php echo $type_label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- FIELD META -->
                <div class="field-property-box field-meta field-dep" <?php if ( $adding ) echo ' style="display: none;"'; ?>>
                    <?php
                    if ( 'link' == $field['type'] || 'link2' == $field['type'] ) {
                        wpmtst_view_field_link( $key, $field['field'], $field['type'], $field );
                    }

                    if ( 'date' == $field['type'] ) {
                        wpmtst_view_field_date( $key, $field );
                    }
                    ?>
                </div>

                <!-- FIELD BEFORE -->
                <div class="field-property field-before field-dep" <?php if ( $adding ) echo ' style="display: none;"'; ?>>
                    <label for="client_section_<?php echo $key; ?>_before">
                        <?php _e( 'Before', 'strong-testimonials' ); ?>
                    </label>
                    <input id="client_section_<?php echo $key; ?>_before" type="text" name="view[data][client_section][<?php echo $key; ?>][before]" value="<?php echo isset( $field['before'] ) ? $field['before'] : ''; ?>">
                </div>

                <!-- FIELD CSS CLASS -->
                <div class="field-property field-css field-dep" <?php if ( $adding ) echo ' style="display: none;"'; ?>>
                    <label for="client_section_<?php echo $key; ?>_class">
                        <?php _e( 'CSS Class', 'strong-testimonials' ); ?>
                    </label>
                    <input id="client_section_<?php echo $key; ?>_class" type="text" name="view[data][client_section][<?php echo $key; ?>][class]" value="<?php echo $field['class']; ?>">
                </div>

            </div>

		</div>

	</div>
	<?php
}


/**
 * Show a single client link field inputs.
 *
 * @since 1.21.0
 *
 * @param $key
 * @param $field_name
 * @param $type
 * @param $field
 * @param bool|false $adding
 */
function wpmtst_view_field_link( $key, $field_name, $type, $field, $adding = false ) {
	if ( $field_name ) {
		$current_field = wpmtst_get_field_by_name( $field_name );
		if ( is_array( $current_field ) ) {
			$field = array_merge( $current_field, $field );
		}
	}

	$custom_fields = wpmtst_get_custom_fields();

	// Add placeholder link_text and label to field in case we need to populate link_text
	if ( ! isset( $field['link_text'] ) ) {
		$field['link_text'] = 'field';
	}
	if ( ! isset( $field['link_text_custom'] ) ) {
		$field['link_text_custom'] = '';
	}
	$field['label'] = wpmtst_get_field_label( $field );
	?>

	<?php // the link text ?>
	<div class="flex">
		<label for="view-fieldtext<?php echo $key; ?>"><?php _e( 'Link Text', 'strong-testimonials' ); ?></label>
		<select id="view-fieldtext<?php echo $key; ?>" name="view[data][client_section][<?php echo $key; ?>][link_text]" class="if selectgroup">
			<option value="value" <?php selected( $field['link_text'], 'value' ); ?>><?php _e( "this field's value", 'strong-testimonials' ); ?></option>
			<option value="label" <?php selected( $field['link_text'], 'label' ); ?>><?php _e( "this field's label", 'strong-testimonials' ); ?></option>
			<option value="custom" <?php selected( $field['link_text'], 'custom' ); ?>><?php _e( 'custom text', 'strong-testimonials' ); ?></option>
		</select>
	</div>

	<?php // the link text options ?>
	<?php // use the field label ?>
	<div class="flex then_fieldtext<?php echo $key; ?> then_label then_not_value then_not_custom" style="display: none;">
		<div class="nolabel">&nbsp;</div>
		<input type="text" id="view-fieldtext<?php echo $key; ?>-label" value="<?php echo $field['label']; ?>" readonly>
	</div>
	<?php // use custom text ?>
	<div class="flex then_fieldtext<?php echo $key; ?> then_custom then_not_value then_not_label" style="display: none;">
		<div class="nolabel">&nbsp;</div>
		<input type="text" id="view-fieldtext<?php echo $key; ?>-custom" name="view[data][client_section][<?php echo $key; ?>][link_text_custom]" value="<?php echo $field['link_text_custom']; ?>">
	</div>

	<?php // the URL ?>
	<?php if ( 'link' == $type ) : // URL = another field ?>
	<div class="flex">
		<label for="view-fieldurl<?php echo $key; ?>"><?php _e( 'URL Field', 'strong-testimonials' ); ?></label>
		<select id="view-fieldurl<?php echo $key; ?>" name="view[data][client_section][<?php echo $key; ?>][url]" class="field-type-select">
			<?php foreach ( $custom_fields as $key2 => $field2 ) : ?>
				<?php if ( 'url' == $field2['input_type'] ) : ?>
				<option value="<?php echo $field2['name']; ?>" <?php selected( $field2['name'], $field['url'] ); ?>><?php echo $field2['name']; ?></option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="flex">
		<?php // the URL options ?>
		<div class="nolabel"></div>
		<div class="new_tab">
			<input type="checkbox" id="view-fieldurl<?php echo $key; ?>-newtab"
				   name="view[data][client_section][<?php echo $key; ?>][new_tab]"
				   value="1" <?php checked( $field['new_tab'] ); ?>>
			<label for="view-fieldurl<?php echo $key; ?>-newtab">
				<?php _e( 'new tab', 'strong-testimonials' ); ?>
			</label>
		</div>

	</div>
	<?php else : // URL = this field ?>
		<input type="hidden" name="view[data][client_section][<?php echo $key; ?>][url]" value="<?php echo $field['name']; ?>">
	<?php endif; ?>

	<?php
}


/**
 * Show a single client date field inputs.
 *
 * @since 1.21.0
 *
 * @param $key
 * @param $field
 * @param bool $adding
 */
function wpmtst_view_field_date( $key, $field, $adding = false ) {
	?>
	<div class="flex">
		<label for="view-<?php echo $key; ?>-client-date-format"><span><?php _e( 'Format', 'strong-testimonials' ); ?></span></label>
		<input id="view-<?php echo $key; ?>-client-date-format" type="text" name="view[data][client_section][<?php echo $key; ?>][format]" class="field-type-date" value="<?php echo isset( $field['format'] ) ? $field['format'] : ''; ?>">
	</div>
	<div class="flex">
		<div class="nolabel">&nbsp;</div>
		<div class="help minor">
			<?php printf( '<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://codex.wordpress.org/Formatting_Date_and_Time' ),
				__( 'more about date formats', 'strong-testimonials' ) ); ?>
		</div>
	</div>
	<?php
}


/**
 * Delete a view.
 *
 * @since 1.21.0
 * @param $id
 * @return false|int
 */
function wpmtst_delete_view( $id ) {
	global $wpdb;
	$num_rows_deleted = $wpdb->delete( $wpdb->prefix . 'strong_views', array( 'id' => $id ) );
	return $num_rows_deleted;
}


/**
 * Admin action hook to delete a view.
 *
 * @since 1.21.0
 */
function wpmtst_action_delete_view() {
	if ( isset( $_REQUEST['action'] ) && 'delete-strong-view' == $_REQUEST['action'] && isset( $_REQUEST['id'] ) ) {
		$id = (int) $_GET['id'];
		check_admin_referer( 'delete-strong-view_' . $id );
		wpmtst_delete_view( $id );
		$goback = add_query_arg( 'view-deleted', true, wp_get_referer() );
		wp_redirect( $goback );
		exit;
	}
}

add_action( 'admin_action_delete-strong-view', 'wpmtst_action_delete_view' );


/**
 * Category selector in Display mode in view editor.
 *
 * @param $view_cats_array
 */
function wpmtst_category_checklist( $view_cats_array ) {
	?>
	<div class="view-category-list-panel short-panel">
		<div class="fc-search-wrap">
			<input type="search" class="fc-search-field"
				   placeholder="<?php _e( 'filter categories', 'strong-testimonials' ); ?>"/>
		</div>
		<ul class="view-category-list">
			<?php $args = array(
				'descendants_and_self' => 0,
				'selected_cats'        => $view_cats_array,
				'popular_cats'         => false,
				'walker'               => new Walker_Strong_Category_Checklist(),
				'taxonomy'             => "wpm-testimonial-category",
				'checked_ontop'        => true,
			); ?>
			<?php wp_terms_checklist( 0, $args ); ?>
		</ul>
	</div>
	<?php
}


/**
 * Category selector in Form mode in view editor.
 *
 * @param $view_cats_array
 */
function wpmtst_form_category_checklist( $view_cats_array ) {
	?>
	<div class="view-category-list-panel short-panel">
		<div class="fc-search-wrap">
			<input type="search" class="fc-search-field"
				   placeholder="<?php _e( 'filter categories', 'strong-testimonials' ); ?>"/>
		</div>
		<ul class="view-category-list">
			<?php $args = array(
				'descendants_and_self' => 0,
				'selected_cats'        => $view_cats_array,
				'popular_cats'         => false,
				'walker'               => new Walker_Strong_Form_Category_Checklist(),
				'taxonomy'             => "wpm-testimonial-category",
				'checked_ontop'        => true,
			); ?>
			<?php wp_terms_checklist( 0, $args ); ?>
		</ul>
	</div>
	<?php
}


/**
 * Save sticky view
 *
 * @since 2.22.0
 */
function wpmtst_save_view_sticky() {
	$id = $_REQUEST['id'];
	$stickies = get_option( 'wpmtst_sticky_views', array() );
	if ( in_array( $id, $stickies ) ) {
		$stickies = array_diff( $stickies, array( $id ) );
		$is_sticky = false;
	} else {
		$stickies[] = $id;
		$is_sticky = true;
	}
	update_option( 'wpmtst_sticky_views', $stickies );
	echo json_encode( $is_sticky );
	wp_die();
}
add_action( 'wp_ajax_wpmtst_save_view_sticky', 'wpmtst_save_view_sticky' );


/**
 * Return classes for toggling sections.
 *
 * @param $classes
 * @param $section
 *
 * @since 2.22.0
 *
 * @return string
 */
function wpmtst_view_section_filter( $classes, $section ) {
    if ( 'compat' == $section && wpmtst_divi_builder_active() ) {
        $classes = 'then_display then_form then_slideshow then_not_single_template';
	}

    return $classes;
}
add_filter( 'wpmtst_view_section', 'wpmtst_view_section_filter', 10, 2 );
