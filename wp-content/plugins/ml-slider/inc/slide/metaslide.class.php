<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

/**
 * Slide class represting a single slide. This is extended by type specific
 * slides (eg, MetaImageSlide, MetaYoutubeSlide (pro only), etc)
 */
class MetaSlide {

    public $slide = 0;
    public $slider = 0;
    public $settings = array(); // slideshow settings


    /**
     * Constructor
     */
    public function __construct() {

        add_action( 'wp_ajax_update_slide_image', array( $this, 'ajax_update_slide_image' ) );

    }

    /**
     * Set the slide
     *
     * @param int $id ID
     */
    public function set_slide( $id ) {
        $this->slide = get_post( $id );
    }

    /**
     * Set the slide (that this slide belongs to)
     *
     * @param int $id ID
     */
    public function set_slider( $id ) {
        $this->slider = get_post( $id );
        $this->settings = get_post_meta( $id, 'ml-slider_settings', true );
    }


    /**
     * Return the HTML for the slide
     *
     * @param  int $slide_id  Slide ID
     * @param  int $slider_id Slider ID
     * @return array complete array of slides
     */
    public function get_slide( $slide_id, $slider_id ) {
        $this->set_slider( $slider_id );
        $this->set_slide( $slide_id );
        return $this->get_slide_html();
    }

    /**
     * Save the slide
     *
     * @param  int    $slide_id  Slide ID
     * @param  int    $slider_id Slider ID
     * @param  string $fields    SLider fields
     */
    public function save_slide( $slide_id, $slider_id, $fields ) {
        $this->set_slider( $slider_id );
        $this->set_slide( $slide_id );
        $this->save( $fields );
    }


    /**
     * Updates the slide meta value to a new image.
     *
     * @param int $slide_id The id of the slide being updated
     * @param int $image_id The id of the new image to use
     *
     * @return array|WP_error The status message and if success, the thumbnail link
     */
    protected function update_slide_image($slide_id, $image_id) {
        /*
        * Verifies that the $image_id is an actual image
        */        
        if (!($image_url = wp_get_attachment_image_url($image_id))) {
            return new WP_Error('update_failed', __('The requested image does not exist. Please try again.', 'ml-slider'), array('status' => 409));
        }

        /*
        * Verifies that the $slide_id is an actual slide (it currently has an image)
        */
        if (!($image_id_old = intval(get_post_meta($slide_id, '_thumbnail_id', true)))) {
            return new WP_Error('update_failed', __('The requested slide does not exist or something is wrong with the current image. Please try again or remove this slide.', 'ml-slider'), array('status' => 409));
        }
        
        /*
        * Updates and verifies that it worked. Checks that either the image is the same,
        * or that the update was successful
        */
        if (($image_id === $image_id_old) || update_post_meta($slide_id, '_thumbnail_id', $image_id, $image_id_old)) {
            return array(
                'message' => __('The image was successfully updated.', 'ml-slider'),
                'img_url' => $image_url
            );
        }
        
        return new WP_Error('update_failed', __('There was an error updating the image. Please try again', 'ml-slider'), array('status' => 409));
    }

    /**
     * Ajax wrapper to update the slide image.
     *
     * @return String The status message and if success, the thumbnail link (JSON)
     */
    public function ajax_update_slide_image() {

        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'metaslider_update_slide_image')) {
            return wp_send_json_error(array(
                'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }
        
        $result = $this->update_slide_image(
            absint($_POST['slide_id']), absint($_POST['image_id'])
        );
        
        if (is_wp_error($result)) {
            return wp_send_json_error(array(
                'message' => $result->get_error_message()
            ), 409);
        }
        return wp_send_json_success($result, 200);
    }


    /**
     * Return the correct slide HTML based on whether we're viewing the slides in the
     * admin panel or on the front end.
     *
     * @return string slide html
     */
    public function get_slide_html() {

        $viewing_theme_editor = is_admin() && isset( $_GET['page'] ) && $_GET['page'] == 'metaslider-theme-editor';
        $viewing_preview = did_action('admin_post_metaslider_preview');
        $doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

        if ( $doing_ajax || $viewing_preview || $viewing_theme_editor ) {
            return $this->get_public_slide();
        }

        $capability = apply_filters( 'metaslider_capability', 'edit_others_posts' );

        if ( is_admin() && current_user_can( $capability ) ) {
            return $this->get_admin_slide();
        }

        return $this->get_public_slide();

    }

    /**
     * Check if a slide already exists in a slideshow
     *
     * @param  string $slider_id Slider ID
     * @param  string $slide_id  SLide ID
     * @return string
     */
    public function slide_exists_in_slideshow( $slider_id, $slide_id ) {

        return has_term( "{$slider_id}", 'ml-slider', $slide_id );

    }

    /**
     * Check if a slide has already been assigned to a slideshow
     *
     * @param  string $slider_id Slider ID
     * @param  string $slide_id  SLide ID
     * @return string
     */
    public function slide_is_unassigned_or_image_slide( $slider_id, $slide_id ) {

        $type = get_post_meta( $slide_id, 'ml-slider_type', true );

        return ! strlen( $type ) || $type == 'image';

    }


    /**
     * Build image HTML
     *
     * @param array $attributes Anchor attributes
     * @return string image HTML
     */
    public function build_image_tag( $attributes ) {

        $html = "<img";

        foreach ( $attributes as $att => $val ) {
            if ( strlen( $val ) ) {
                $html .= " " . $att . '="' . esc_attr( $val ) . '"';
            } else if ( $att == 'alt' ) {
                $html .= " " . $att . '=""'; // always include alt tag for HTML5 validation
            }
        }

        $html .= " />";

        return $html;

    }


    /**
     * Build image HTML
     *
     * @param array  $attributes Anchor attributes
     * @param string $content    Anchor contents
     * @return string image HTML
     */
    public function build_anchor_tag( $attributes, $content ) {

        $html = "<a";

        foreach ( $attributes as $att => $val ) {
            if ( strlen( $val ) ) {
                $html .= " " . $att . '="' . esc_attr( $val ) . '"';
            }
        }

        $html .= ">" . $content . "</a>";

        return $html;

    }


    /**
     * Create a new post for a slide. Tag a featured image to it.
     *
     * @since 3.4
     * @param string $media_id  - Media File ID to use for the slide
     * @param string $type      - the slide type identifier
     * @param int    $slider_id - the parent slideshow ID
     * @return int $slide_id - the ID of the newly created slide
     */
     public function insert_slide($media_id, $type, $slider_id) {
        
        // Store the post in the database (without translation)
        $slide_id = wp_insert_post(
            array(
                'post_title' => "Slider {$slider_id} - {$type}",
                'post_status' => 'publish',
                'post_type' => 'ml-slide'
            )
        );

        // Send back a friendlier error message
        if (is_wp_error($slide_id)) {
            return new WP_Error('create_failed', __('There was an error while updating the database. Please try again.', 'ml-slider'), array('status' => 409));
        }

        // Set the image to the slide
        set_post_thumbnail($slide_id, $media_id);

        $this->add_or_update_or_delete_meta($slide_id, 'type', $type);
        return $slide_id;
    }
        

    /**
     * Tag the slide attachment to the slider tax category
     */
    public function tag_slide_to_slider() {

        if ( ! term_exists( $this->slider->ID, 'ml-slider' ) ) {
            // create the taxonomy term, the term is the ID of the slider itself
            wp_insert_term( $this->slider->ID, 'ml-slider' );
        }

        // get the term thats name is the same as the ID of the slider
        $term = get_term_by( 'name', $this->slider->ID, 'ml-slider' );
        // tag this slide to the taxonomy term
        wp_set_post_terms( $this->slide->ID, $term->term_id, 'ml-slider', true );

        $this->update_menu_order();

    }


    /**
     * Ouput the slide tabs
     */
    public function get_admin_slide_tabs_html() {

        return $this->get_admin_slide_tab_titles_html() . $this->get_admin_slide_tab_contents_html();

    }


    /**
     * Generate the HTML for the tabs
     */
    public function get_admin_slide_tab_titles_html() {

        $tabs = $this->get_admin_tabs();

        $return = "<ul class='tabs'>";

        foreach ( $tabs as $id => $tab ) {

            $pos = array_search( $id, array_keys( $tabs ) );

            $selected = $pos == 0 ? "class='selected'" : "";

            $return .= "<li {$selected} ><a tabindex='0' href='#' data-tab_id='tab-{$pos}'>{$tab['title']}</a></li>";

        }

        $return .= "</ul>";

        return $return;

    }

    /**
     * Generate the HTML for the delete button
     *
     * @return string
     */
    public function get_delete_button_html() {
        return "<button class='delete-slide alignright' title='" . __("Delete Slide", "ml-slider") . "' data-slide-id='{$this->slide->ID}'><i><svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-x'><line x1='18' y1='6' x2='6' y2='18'/><line x1='6' y1='6' x2='18' y2='18'/></svg></i></button>";
    }

    /**
     * Generate the HTML for the undelete button
     *
     * @return string
     */
    public function get_undelete_button_html() {
        return "<a href='#' onclick='return false;' class='trash-view-restore' data-slide-id='{$this->slide->ID}'>" . __('Restore', 'default') . "</a>";
    }

    /**
     * Generate the HTML for the perminant button
     *
     * @return string
     */
    public function get_perminant_delete_button_html() {

        // TODO allow for a perminant delete button
        $url = wp_nonce_url(admin_url("post.php?ml-slide={$this->slide->ID}&action=delete"));
        return "<a href='{$url}' class='trash-view-perminant-delete' data-slide-id='{$this->slide->ID}'>" . __('Delete Permanently', 'default') . "</a>";
    }

    /**
     * Generates the HTML for the update slide image button
     *
     * @return string The html for the edit button on a slide image
     */
    public function get_update_image_button_html() {
        $attachment_id = $this->get_attachment_id();
        return "<button class='update-image alignright' data-button-text='" . __("Update slide image", "ml-slider") . "' title='" . __("Update slide image", "ml-slider") . "' data-slide-id='{$this->slide->ID}' data-attachment-id='{$attachment_id}'><i><svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-edit-2'><polygon points='16 3 21 8 8 21 3 21 3 16 16 3'/></svg></i></button>";
    }

    /**
     * Generate the HTML for the tab content
     */
    public function get_admin_slide_tab_contents_html() {

        $tabs = $this->get_admin_tabs();

        $return = "<div class='tabs-content'>";

        foreach ( $tabs as $id => $tab ) {

            $pos = array_search( $id, array_keys( $tabs ) );

            $hidden = $pos != 0 ? "style='display: none;'" : "";

            $return .= "<div class='tab tab-{$pos}' {$hidden}>{$tab['content']}</div>";

        }

        $return .= "</div>";

        return $return;
    }


    /**
     * Ensure slides are added to the slideshow in the correct order.
     *
     * Find the highest slide menu_order in the slideshow, increment, then
     * update the new slides menu_order.
     */
    public function update_menu_order() {

        $menu_order = 0;

        // get the slide with the highest menu_order so far
        $args = array(
            'force_no_custom_order' => true,
            'orderby' => 'menu_order',
            'order' => 'DESC',
            'post_type' => array('attachment', 'ml-slide'),
            'post_status' => array('inherit', 'publish'),
            'lang' => '', // polylang, ingore language filter
            'suppress_filters' => 1, // wpml, ignore language filter
            'posts_per_page' => 1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'ml-slider',
                    'field' => 'slug',
                    'terms' => $this->slider->ID
                )
            )
        );

        $query = new WP_Query( $args );

        while ( $query->have_posts() ) {
            $query->next_post();
            $menu_order = $query->post->menu_order;
        }

        wp_reset_query();

        // increment
        $menu_order = $menu_order + 1;

        // update the slide
        wp_update_post( array(
                'ID' => $this->slide->ID,
                'menu_order' => $menu_order
            )
        );

    }


    /**
     * If the meta doesn't exist, add it
     * If the meta exists, but the value is empty, delete it
     * If the meta exists, update it
     *
     * @param int    $post_id Post ID
     * @param string $name    SLider Name
     * @param int    $value   Slaider Value
     */
    public function add_or_update_or_delete_meta( $post_id, $name, $value ) {

        $key = "ml-slider_" . $name;

        if ( $value == 'false' || $value == "" || ! $value ) {
            if ( get_post_meta( $post_id, $key ) ) {
                delete_post_meta( $post_id, $key );
            }
        } else {
            if ( get_post_meta( $post_id, $key ) ) {
                update_post_meta( $post_id, $key, $value );
            } else {
                add_post_meta( $post_id, $key, $value, true );
            }
        }

    }


    /**
     * Detect a [metaslider] or [ml-slider] shortcode in the slide caption, which has an ID that matches the current slideshow ID
     *
     * @param string $content Content for shortcode
     * @return  boolean
     */
    protected function detect_self_metaslider_shortcode( $content ) {
        $pattern = get_shortcode_regex();

        if ( preg_match_all( '/'. $pattern .'/s', $content, $matches ) && array_key_exists( 2, $matches ) && ( in_array( 'metaslider', $matches[2] ) || in_array( 'ml-slider', $matches[2] ) ) ) {
            // caption contains [metaslider] shortcode
            if ( array_key_exists( 3, $matches ) && array_key_exists( 0, $matches[3] ) ) {
                // [metaslider] shortcode has attributes
                $attributes = shortcode_parse_atts( $matches[3][0] );

                if ( isset( $attributes['id'] ) && $attributes['id'] == $this->slider->ID ) {
                    // shortcode has ID attribute that matches the current slideshow ID
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the attachment ID
     *
     * @return Integer - the attachment ID
     */
    public function get_attachment_id() {
        if ('attachment' == $this->slide->post_type) {
            return $this->slide->ID;
        } else {
            return get_post_thumbnail_id($this->slide->ID);
        }
    }

    /**
     * Get the thumbnail for the slide
     */
    public function get_thumb() {

        if ( get_post_type( $this->slide->ID ) == 'attachment' ) {
            $image = wp_get_attachment_image_src( $this->slide->ID, 'thumbnail');
        } else {
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $this->slide->ID ) , 'thumbnail');
        }

        if ( isset( $image[0] ) ) {
            return $image[0];
        }

        return "";
    }
}