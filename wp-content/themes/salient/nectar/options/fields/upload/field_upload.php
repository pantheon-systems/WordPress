<?php
class Redux_Options_upload {

    /**
     * Field Constructor.
     *
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     * @since Redux_Options 1.0.0
    */
    function __construct($field = array(), $value ='', $parent = '') {
        $this->field = $field;
		$this->value = $value;
		$this->args = $parent->args;
		$this->url = $parent->url;
    }

    /**
     * Field Render Function.
     *
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @since Redux_Options 1.0.0
    */
    function render() {
        $class = (isset($this->field['class'])) ? $this->field['class'] : 'regular-text';        
        echo '<input type="hidden" id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" value="' . $this->value . '" class="' . $class . '" />';
        echo '<img class="redux-opts-screenshot" id="redux-opts-screenshot-' . $this->field['id'] . '" src="' . $this->value . '" />';
        if($this->value == '') {$remove = ' style="display:none;"'; $upload = ''; } else {$remove = ''; $upload = ' style="display:none;"'; }
        echo ' <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);"class="redux-opts-upload button-secondary"' . $upload . ' rel-id="' . $this->field['id'] . '">' . __('Upload', 'salient') . '</a>';
        echo ' <a href="javascript:void(0);" class="redux-opts-upload-remove"' . $remove . ' rel-id="' . $this->field['id'] . '">' . __('Remove Upload', 'salient') . '</a>';
        echo (isset($this->field['desc']) && !empty($this->field['desc'])) ? '<br/><span class="description">' . $this->field['desc'] . '</span>' : '';
    }

    /**
     * Enqueue Function.
     *
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     *
     * @since Redux_Options 1.0.0
    */
    function enqueue() {

        if ( floatval(get_bloginfo('version')) < "3.5" ) {
            wp_enqueue_script(
                'redux-opts-field-upload-js', 
                Redux_OPTIONS_URL . 'fields/upload/field_upload_3_4.js', 
                array('jquery', 'thickbox', 'media-upload'),
                time(),
                true
            );
            wp_enqueue_style('thickbox');// thanks to https://github.com/rzepak
        } else {
            wp_enqueue_script(
                'redux-opts-field-upload-js', 
                Redux_OPTIONS_URL . 'fields/upload/field_upload.js', 
                array('jquery'),
                time(),
                true
            );
           
        }
        wp_localize_script('redux-opts-field-upload-js', 'redux_upload', array('url' => $this->url.'fields/upload/blank.png'));
    }
}
