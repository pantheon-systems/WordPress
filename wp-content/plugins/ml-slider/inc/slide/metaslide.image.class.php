<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

/**
 * Generic Slider super class. Extended by library specific classes.
 */
class MetaImageSlide extends MetaSlide {

    /**
     * Register slide type
     */
    public function __construct() {
        parent::__construct();
        add_filter('metaslider_get_image_slide', array($this, 'get_slide' ), 10, 2);
        add_action('metaslider_save_image_slide', array($this, 'save_slide' ), 5, 3);
        add_action('wp_ajax_create_image_slide', array($this, 'ajax_create_image_slides'));
        add_action('wp_ajax_resize_image_slide', array($this, 'ajax_resize_slide'));
    }

    /**
     * Creates one or more slide.
     * Currently this is used via an ajax call, but plans to keep this only called
     * by PHP methods, such as in an import situation.
     *
     * @param int   $slider_id The id of the slider
     * @param array $data      The data information for the new slide
     *
     * @return int | WP_error The status message and if success, the count
     */
    protected function create_slides($slider_id, $data) {
        $errors = array();
        foreach ($data as $slide_data) {

            // TODO check type and create slides based on that type
            // $method = 'create_' . $slide['type'] . '_slide';
            // $this->slider->add_slide($this->{$method}());
            $message = $this->add_slide($slider_id, $slide_data);
            if (is_wp_error($message)) {
                array_push($errors, $message);
            }
        }

        // We don't bail on an error, so we need to send back a list of errors, if any
        if (count($errors)) {
            $error_response = new WP_Error('create_failed', 'We experienced some errors while adding slides.', array('status' => 409));
            foreach($errors as $message) {
                $error_response->add('create_failed', $message, array('status' => 409));
            }
            return $error_response;
        }

        return count($data);
    }

    /**
     * Adds a single slide.
     * TODO refactor and put this in a Slider class
     *
     * @param int   $slider_id The id of the slider
     * @param array $data      The data information for the new slide
     *
     * @return string | WP_error The status message and if success, echo string for now
     */
    protected function add_slide($slider_id, $data) {
        
        // For now this only handles images, so check it's an image
        if (!wp_attachment_is_image($data['id'])) {

            // TODO this is the old way to handle errors
            // Remove this later and handle errors using data returns
            echo "<tr><td colspan='2'>ID: {$data['id']} \"" . get_the_title( $data['id'] ) . "\" - " . __( "Failed to add slide. Slide is not an image.", 'ml-slider' ) . "</td></tr>";

            return new WP_Error('create_failed', __('This isn\'t an accepted image. Please try again.', 'ml-slider'), array('status' => 409));
        }

        $slide_id = $this->insert_slide($data['id'], $data['type'], $slider_id);
        if (is_wp_error($slide_id)) {
            return $slide_id;
		}
		
        
        // TODO refactor these and investigate why they are needed (legacy?)
        $this->set_slide($slide_id);
        $this->set_slider($slider_id);
        $this->tag_slide_to_slider();
        
        // set default inherit values
        $this->set_field_inherited('caption', true);
        $this->set_field_inherited('title', true);
        $this->set_field_inherited('alt', true);

        // TODO investigate if this is really needed
        $this->settings['width'] = 0;
        $this->settings['height'] = 0;

        // TODO refactor to have a view file and return data
        echo $this->get_admin_slide();
    }

    /**
     * Ajax wrapper to create multiple slides.
     * TODO: depricate this in favor of only allowing single slide creation
     *
     * @return string The status message and if success, the count of slides added
     */
    public function ajax_create_image_slides() {

        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'metaslider_create_slide')) {
            return wp_send_json_error(array(
                'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }

        /**
         * TODO create a slide object class with data requirements and types
         *
         * @param  int $image_id Image ID
         * @return array
         */
        function make_image_slide_data($image_id) {
            return array(
                'type' => 'image',
                'id'   => absint($image_id)
            );
        }

        $result = $this->create_slides(
            absint($_POST['slider_id']), array_map('make_image_slide_data',$_POST['selection'])
        );

        // TODO for now leave these out since it "echos" the slide...
        // if (is_wp_error($result)) {
        // return wp_send_json_error(array(
        // 'messages' => $result->get_error_messages()
        // ), 409);
        // }
        // return wp_send_json_success($result, 200);
    }


    /**
     * Create a new slide and echo the admin HTML
     */
    public function ajax_resize_slide() {

        check_admin_referer( 'metaslider_resize' );

        $slider_id = absint( $_POST['slider_id'] );
        $slide_id = absint( $_POST['slide_id'] );

        $this->set_slide( $slide_id );
        $this->set_slider( $slider_id );

        $settings = get_post_meta( $slider_id, 'ml-slider_settings', true );

        // create a copy of the correct sized image
        $imageHelper = new MetaSliderImageHelper(
            $slide_id,
            $settings['width'],
            $settings['height'],
            isset( $settings['smartCrop'] ) ? $settings['smartCrop'] : 'false',
            $this->use_wp_image_editor()
        );

        $url = $imageHelper->get_image_url( true );

        echo $url . " (" . $settings['width'] . 'x' . $settings['height'] . ")";

        do_action( "metaslider_ajax_resize_image_slide", $slide_id, $slider_id, $settings );

        wp_die();
    }


    /**
     * Return the HTML used to display this slide in the admin screen
     *
     * @return string slide html
     */
    protected function get_admin_slide() {

        // get some slide settings
        $thumb       = $this->get_thumb();
        $slide_label = apply_filters("metaslider_image_slide_label", __("Image Slide", "ml-slider"), $this->slide, $this->settings);
        $attachment_id = $this->get_attachment_id();

        // slide row HTML
        $row  = "<tr id='slide-{$this->slide->ID}' class='slide image flex responsive nivo coin' data-attachment-id='{$attachment_id}'>
                    <td class='col-1'>
                        <div class='metaslider-ui-controls ui-sortable-handle'>
                        <h4 class='slide-details'>{$slide_label}</h4>";
                        if (metaslider_this_is_trash($this->slide)) {
                            $row .= '<div class="row-actions trash-btns">';
                            $row .= "<span class='untrash'>{$this->get_undelete_button_html()}</span>";
                            // $row .= ' | ';
                            // $row .= "<span class='delete'>{$this->get_perminant_delete_button_html()}</span>";
                            $row .= '</div>';
                        } else {
                            $row .= $this->get_delete_button_html();
                            $row .= $this->get_update_image_button_html();
                        }
            $row .= "</div>
                        <div class='metaslider-ui-inner'>
                            <button class='update-image image-button' data-button-text='" . __("Update slide image", "ml-slider") . "' title='" . __("Update slide image", "ml-slider") . "' data-slide-id='{$this->slide->ID}' data-attachment-id='{$attachment_id}'>
                                <div class='thumb' style='background-image: url({$thumb})'></div>
                            </button>
                        </div>
                    </td>
                    <td class='col-2'>
                        <div class='metaslider-ui-inner'>
                            " . $this->get_admin_slide_tabs_html() . "
                            <input type='hidden' name='attachment[{$this->slide->ID}][type]' value='image' />
                            <input type='hidden' class='menu_order' name='attachment[{$this->slide->ID}][menu_order]' value='{$this->slide->menu_order}' />
                            <input type='hidden' name='resize_slide_id' data-slide_id='{$this->slide->ID}' data-width='{$this->settings['width']}' data-height='{$this->settings['height']}' />
                        </div>
                    </td>
                </tr>";

        return $row;

    }

    /**
     * Build an array of tabs and their titles to use for the admin slide.
     */
    public function get_admin_tabs() {

        $slide_id = absint($this->slide->ID);
        $attachment_id = $this->get_attachment_id();
        $attachment = get_post($attachment_id);

        $url = esc_attr(get_post_meta($slide_id, 'ml-slider_url', true));
        $target = get_post_meta($slide_id, 'ml-slider_new_window', true) ? 'checked=checked' : '';
        
        // TODO: make this DRY
        // caption
        $caption = esc_textarea($this->slide->post_excerpt);
        $image_caption = html_entity_decode($attachment->post_excerpt, ENT_NOQUOTES, 'UTF-8');
		$inherit_image_caption_check = '';
		$inherit_image_caption_class = '';
		if ($this->is_field_inherited('caption')) {
			$inherit_image_caption_check = 'checked=checked';
			$inherit_image_caption_class = ' inherit-from-image';
		}
        // alt
        $alt = esc_attr(get_post_meta($slide_id, '_wp_attachment_image_alt', true));
        $image_alt = esc_attr(get_post_meta($attachment_id, '_wp_attachment_image_alt', true ));
		$inherit_image_alt_check = ''; 
		$inherit_image_alt_class = '';
		if ($this->is_field_inherited('alt')) {
			$inherit_image_alt_check = 'checked=checked';
			$inherit_image_alt_class = ' inherit-from-image';	
		}
        // title
        $title = esc_attr(get_post_meta($slide_id, 'ml-slider_title', true));
		$image_title = esc_attr($attachment->post_title);
		$inherit_image_title_check = '';
        $inherit_image_title_class = '';
		if ($this->is_field_inherited('title')) {
			$inherit_image_title_check = 'checked=checked';
			$inherit_image_title_class = ' inherit-from-image';
		}

        ob_start();
        include(METASLIDER_PATH . 'admin/views/slides/tabs/general.php');
        $general_tab = ob_get_clean();

        if (!$this->is_valid_image()) {
            $message = __("Warning: The image data does not exist. Please re-upload the image.", "ml-slider");

            $general_tab = "<div class='warning'>{$message}</div>" . $general_tab;
        }

        ob_start();
        include(METASLIDER_PATH .'admin/views/slides/tabs/seo.php');
        $seo_tab = ob_get_clean();

        $tabs = array(
            'general' => array(
                'title' => __("General", "ml-slider"),
                'content' => $general_tab
            ),
            'seo' => array(
                'title' => __("SEO", "ml-slider"),
                'content' => $seo_tab
            )
        );

        if (version_compare(get_bloginfo('version'), 3.9, '>=')) {

            $crop_position = get_post_meta($slide_id, 'ml-slider_crop_position', true); 

            if (!$crop_position) {
                $crop_position = 'center-center';
            }

            ob_start();
            include(METASLIDER_PATH .'admin/views/slides/tabs/crop.php');
            $crop_tab = ob_get_clean();
            
            $tabs['crop'] = array(
                'title' => __("Crop", "ml-slider"),
                'content' => $crop_tab
            );

        }

        return apply_filters("metaslider_image_slide_tabs", $tabs, $this->slide, $this->slider, $this->settings);

    }


    /**
     * Check to see if metadata exists for this image. Assume the image is
     * valid if metadata and a size exists for it (generated during initial
     * upload to WordPress).
     *
     * @return bool, true if metadata and size exists.
     */
    public function is_valid_image() {

        if ( get_post_type( $this->slide->ID ) === 'attachment' ) {
            $image_id = $this->slide->ID;
        } else {
            $image_id = get_post_thumbnail_id( $this->slide->ID );
        }

        $meta = wp_get_attachment_metadata( $image_id );

        $is_valid = isset( $meta['width'], $meta['height'] );

        return apply_filters( 'metaslider_is_valid_image', $is_valid, $this->slide );
    }


    /**
     * Disable/enable image editor
     *
     * @return bool
     */
    public function use_wp_image_editor() {

        return apply_filters( 'metaslider_use_image_editor', $this->is_valid_image(), $this->slide );

    }


    /**
     * Returns the HTML for the public slide
     *
     * @return string slide html
     */
    protected function get_public_slide() {

        // get the image url (and handle cropping)
        // disable wp_image_editor if metadata does not exist for the slide
        $imageHelper = new MetaSliderImageHelper(
            $this->slide->ID,
            $this->settings['width'],
            $this->settings['height'],
            isset($this->settings['smartCrop']) ? $this->settings['smartCrop'] : 'false',
            $this->use_wp_image_editor()
        );

        $thumb = $imageHelper->get_image_url();
        $attachment_id = get_post_thumbnail_id($this->slide->ID);
        $attachment = get_post($attachment_id);	
		if ($this->is_field_inherited('caption')) {
			$caption = $attachment->post_excerpt;
        } else {
			$caption = $this->slide->post_excerpt;
		}
        if ($this->detect_self_metaslider_shortcode($this->slide->post_excerpt)) {
            $caption = str_replace(array("[metaslider", "[ml-slider"), "[metaslider-disabled", $this->slide->post_excerpt);
		}

        if ($this->is_field_inherited('title')) {
            $title = $attachment->post_title;
        } else {
            $title = get_post_meta($this->slide->ID, 'ml-slider_title', true);
        }
		
        if ($this->is_field_inherited('alt')) {
            $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        } else {
            $alt = get_post_meta($this->slide->ID, '_wp_attachment_image_alt', true);
        }
        


        // store the slide details
        $slide = array(
            'id' => $this->slide->ID,
            'url' => get_post_meta($this->slide->ID, 'ml-slider_url', true),
            'title' => $title,
            'target' => get_post_meta($this->slide->ID, 'ml-slider_new_window', true) ? '_blank' : '_self',
            'src' => $thumb,
            'thumb' => $thumb, // backwards compatibility with Vantage
            'width' => $this->settings['width'],
            'height' => $this->settings['height'],
            'alt' => $alt,
            'caption' => html_entity_decode(do_shortcode($caption), ENT_NOQUOTES, 'UTF-8'),
            'caption_raw' => do_shortcode($caption),
            'class' => "slider-{$this->slider->ID} slide-{$this->slide->ID}",
            'rel' => "",
            'data-thumb' => ""
        );

        // fix slide URLs
        if (strpos($slide['url'], 'www.') === 0) {
            $slide['url'] = 'http://' . $slide['url'];
        }

        $slide = apply_filters('metaslider_image_slide_attributes', $slide, $this->slider->ID, $this->settings);

        // return the slide HTML
        switch ($this->settings['type']) {
            case "coin":
                return $this->get_coin_slider_markup($slide);
            case "flex":
                return $this->get_flex_slider_markup($slide);
            case "nivo":
                return $this->get_nivo_slider_markup($slide);
            case "responsive":
                return $this->get_responsive_slides_markup($slide);
            default:
                return $this->get_flex_slider_markup($slide);
        }

    }

    /**
     * Generate nivo slider markup
     *
     * @param  string $slide html
     * @return string slide html
     */
    private function get_nivo_slider_markup($slide) {

        $attributes = apply_filters('metaslider_nivo_slider_image_attributes', array(
                'src' => $slide['src'],
                'height' => $slide['height'],
                'width' => $slide['width'],
                'data-title' => htmlentities( $slide['caption_raw'], ENT_QUOTES, 'UTF-8' ),
                'data-thumb' => $slide['data-thumb'],
                'title' => $slide['title'],
                'alt' => $slide['alt'],
                'rel' => $slide['rel'],
                'class' => $slide['class']
            ), $slide, $this->slider->ID );

        $html = $this->build_image_tag($attributes);

        $anchor_attributes = apply_filters('metaslider_nivo_slider_anchor_attributes', array(
                'href' => $slide['url'],
                'target' => $slide['target']
            ), $slide, $this->slider->ID);

        if (strlen( $anchor_attributes['href'])) {
            $html = $this->build_anchor_tag($anchor_attributes, $html);
        }

        return apply_filters('metaslider_image_nivo_slider_markup', $html, $slide, $this->settings);

    }

    /**
     * Generate flex slider markup
     *
     * @param  string $slide html
     * @return string slide html
     */
    private function get_flex_slider_markup( $slide ) {

        $image_attributes = array(
            'src' => $slide['src'],
            'height' => $slide['height'],
            'width' => $slide['width'],
            'alt' => $slide['alt'],
            'rel' => $slide['rel'],
            'class' => $slide['class'],
            'title' => $slide['title']
        );

        if ( $this->settings['smartCrop'] == 'disabled_pad') {

            $image_attributes['style'] = $this->flex_smart_pad( $image_attributes, $slide );

        }

        $attributes = apply_filters( 'metaslider_flex_slider_image_attributes', $image_attributes, $slide, $this->slider->ID );

        $html = $this->build_image_tag( $attributes );

        $anchor_attributes = apply_filters( 'metaslider_flex_slider_anchor_attributes', array(
                'href' => $slide['url'],
                'target' => $slide['target']
            ), $slide, $this->slider->ID );

        if ( strlen( $anchor_attributes['href'] ) ) {
            $html = $this->build_anchor_tag( $anchor_attributes, $html );
        }

        // add caption
        if ( strlen( $slide['caption'] ) ) {
            $html .= '<div class="caption-wrap"><div class="caption">' . $slide['caption'] . '</div></div>';
        }

        $attributes = apply_filters( 'metaslider_flex_slider_list_item_attributes', array(
                'data-thumb' => isset($slide['data-thumb']) ? $slide['data-thumb'] : "",
                'style' => "display: none; width: 100%;",
                'class' => "slide-{$this->slide->ID} ms-image"
            ), $slide, $this->slider->ID );

        $li = "<li";

        foreach ( $attributes as $att => $val ) {
            if ( strlen( $val ) ) {
                $li .= " " . $att . '="' . esc_attr( $val ) . '"';
            }
        }

        $li .= ">" . $html . "</li>";

        $html = $li;


        return apply_filters( 'metaslider_image_flex_slider_markup', $html, $slide, $this->settings );

    }

    /**
     * Calculate the correct width (for vertical alignment) or top margin (for horizontal alignment)
     * so that images are never stretched above the height set in the slideshow settings
     *
     * @param  array $atts  Attributes
     * @param  array $slide Slide details
     * @return string
     */
    private function flex_smart_pad( $atts, $slide ) {

        if ( get_post_type( $slide['id'] ) === 'attachment' ) {
            $slide_id = $slide['id'];
        } else {
            $slide_id = get_post_thumbnail_id( $slide['id'] );
        }

        $meta = wp_get_attachment_metadata( $slide_id );

        if ( isset( $meta['width'], $meta['height'] ) ) {

            $image_width = $meta['width'];
            $image_height = $meta['height'];
            $container_width = $this->settings['width'];
            $container_height = $this->settings['height'];

            $new_image_height = $image_height * ( $container_width / $image_width );

            if ( $new_image_height < $container_height ) {
                $margin_top_in_px = ( $container_height - $new_image_height ) / 2;
                $margin_top_in_percent = ( $margin_top_in_px / $container_width ) * 100;
                return 'margin-top: ' . $margin_top_in_percent . '%';
            } else {
                return 'margin: 0 auto; width: ' . ( $container_height / $new_image_height ) * 100 . '%';
            }

        }

        return "";

    }


    /**
     * Generate coin slider markup
     *
     * @param  string $slide html
     * @return string slide html
     */
    private function get_coin_slider_markup( $slide ) {

        $attributes = apply_filters( 'metaslider_coin_slider_image_attributes', array(
                'src' => $slide['src'],
                'height' => $slide['height'],
                'width' => $slide['width'],
                'alt' => $slide['alt'],
                'rel' => $slide['rel'],
                'class' => $slide['class'],
                'title' => $slide['title'],
                'style' => 'display: none;'
            ), $slide, $this->slider->ID );

        $html = $this->build_image_tag( $attributes );

        if ( strlen( $slide['caption'] ) ) {
            $html .= "<span>{$slide['caption']}</span>";
        }

        $attributes = apply_filters( 'metaslider_coin_slider_anchor_attributes', array(
                'href' => strlen( $slide['url'] ) ? $slide['url'] : 'javascript:void(0)'
            ), $slide, $this->slider->ID );

        $html = $this->build_anchor_tag( $attributes, $html );

        return apply_filters( 'metaslider_image_coin_slider_markup', $html, $slide, $this->settings );

    }

    /**
     * Generate responsive slides markup
     *
     * @param  string $slide html
     * @return string slide html
     */
    private function get_responsive_slides_markup( $slide ) {

        $attributes = apply_filters( 'metaslider_responsive_slider_image_attributes', array(
                'src' => $slide['src'],
                'height' => $slide['height'],
                'width' => $slide['width'],
                'alt' => $slide['alt'],
                'rel' => $slide['rel'],
                'class' => $slide['class'],
                'title' => $slide['title']
            ), $slide, $this->slider->ID );

        $html = $this->build_image_tag( $attributes );

        if ( strlen( $slide['caption'] ) ) {
            $html .= '<div class="caption-wrap"><div class="caption">' . $slide['caption'] . '</div></div>';
        }

        $anchor_attributes = apply_filters( 'metaslider_responsive_slider_anchor_attributes', array(
                'href' => $slide['url'],
                'target' => $slide['target']
            ), $slide, $this->slider->ID );

        if ( strlen( $anchor_attributes['href'] ) ) {
            $html = $this->build_anchor_tag( $anchor_attributes, $html );
        }

        return apply_filters( 'metaslider_image_responsive_slider_markup', $html, $slide, $this->settings );

    }

    /**
     * Save
     *
     * @param  array $fields Fields to save
     */
    protected function save( $fields ) {

        // update the slide
        wp_update_post(array(
                'ID' => $this->slide->ID,
                'post_excerpt' => $fields['post_excerpt'],
                'menu_order' => $fields['menu_order']
            ));

        // store the URL as a meta field against the attachment
        $this->add_or_update_or_delete_meta($this->slide->ID, 'url', $fields['url']);

        $this->add_or_update_or_delete_meta($this->slide->ID, 'title', $fields['title']); 

		$this->add_or_update_or_delete_meta($this->slide->ID, 'crop_position', $fields['crop_position']);

        // store the inherit custom caption, title and alt settings
        $this->set_field_inherited('title', isset($fields['inherit_image_title']));
        $this->set_field_inherited('caption', isset($fields['inherit_image_caption']));
        $this->set_field_inherited('alt', isset($fields['inherit_image_alt']));

        if (isset($fields['alt'])) {
            update_post_meta($this->slide->ID, '_wp_attachment_image_alt', $fields['alt']);
        }

        // store the 'new window' setting
        $new_window = isset($fields['new_window']) ? 'true' : 'false'; 

        $this->add_or_update_or_delete_meta($this->slide->ID, 'new_window', $new_window);

    }

    /**
     * Gets the inheritn parameter of a field
     * 
     * @param string $field Field to check
     * @return bool
     */
    private function is_field_inherited($field) {
        return (bool) get_post_meta($this->slide->ID, 'ml-slider_inherit_image_' . $field, true);
    }

    /**
     * Sets the inherit parameter of a field. 
     * 
     * @param string $field Field to set
     * @param bool   $value Value is currently isset($fields['checkbox_parameter'])
     * @return mixed Returns meta_id if the meta doesn't exist, otherwise returns true on success and false on failure. NOTE: If the meta_value passed to this function is the same as the value that is already in the database, this function returns false.
     */
    private function set_field_inherited($field, $value) {

        // TODO eventually I would like to handle errors / successful updates to the database even if just sending it to a log file
        return update_post_meta($this->slide->ID, 'ml-slider_inherit_image_' . $field, (bool) $value);
    }

}
