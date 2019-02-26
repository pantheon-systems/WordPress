<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/** @var $edit Vc_Backend_Editor */
// [add element box]
require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-add-element-box.php' );
$add_element_box = new Vc_Add_Element_Box();
$add_element_box->render();
// [/add element box]

// [rendering edit form]
visual_composer()->editForm()->render();
// [/rendering edit form]

// [rendering templates panel editor]
if ( vc_user_access()->part( 'templates' )->can()->get() ) {
	visual_composer()->templatesPanelEditor()->renderUITemplate();
}
// [/rendering templates panel editor]

// [preset panel editor render]
visual_composer()->presetPanelEditor()->renderUIPreset();
// [/preset panel editor render]


// [post settings]
if ( vc_user_access()->part( 'post_settings' )->can()->get() ) {
	require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-post-settings.php' );
	$post_settings = new Vc_Post_Settings( $editor );
	$post_settings->renderUITemplate();
}
// [/post settings]

// [shortcode edit layout]
require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-edit-layout.php' );
$edit_layout = new Vc_Edit_Layout();
$edit_layout->renderUITemplate();
// [/shortcode edit layout]

vc_include_template( 'editors/partials/backend-shortcodes-templates.tpl.php' );
