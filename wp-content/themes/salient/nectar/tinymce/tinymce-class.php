<?php


#-----------------------------------------------------------------
# Enqueue scripts
#-----------------------------------------------------------------

function enqueue_generator_scripts(){

	wp_enqueue_style('tinymce',get_template_directory_uri() . '/nectar/tinymce/shortcode_generator/css/tinymce.css'); 
	wp_enqueue_style('chosen',get_template_directory_uri() . '/nectar/tinymce/shortcode_generator/css/chosen/chosen.css'); 
	wp_enqueue_style('font-awesome',get_template_directory_uri() . '/css/font-awesome.min.css'); 
	wp_enqueue_style('steadysets', get_template_directory_uri() . '/css/steadysets.css');
	wp_enqueue_style('linecon', get_template_directory_uri() . '/css/linecon.css');
	wp_enqueue_style('linea', get_template_directory_uri() . '/css/fonts/svg/font/style.css');
	wp_enqueue_style('iconsmind', get_template_directory_uri() . '/css/iconsmind.css');
	wp_enqueue_script('chosen',get_template_directory_uri() . '/nectar/tinymce/shortcode_generator/js/chosen/chosen.jquery.min.js','jquery','1.0 ', TRUE);
	
	wp_enqueue_style('magnific',get_template_directory_uri() . '/nectar/tinymce/shortcode_generator/css/magnific-popup.css'); 
	wp_enqueue_script('magnific',get_template_directory_uri() . '/nectar/tinymce/shortcode_generator/js/magnific-popup.js','jquery','0.9.7 ', TRUE);
	
	wp_enqueue_script('nectar-shortcode-generator-popup',get_template_directory_uri() . '/nectar/tinymce/shortcode_generator/js/popup.js','jquery','0.9.7 ', TRUE);
	wp_enqueue_script('nectar-shortcode-generator',get_template_directory_uri() . '/nectar/tinymce/nectar-shortcode-generator.js','jquery','0.9.7 ', TRUE);
	
}

add_action('admin_enqueue_scripts','enqueue_generator_scripts');


 
add_action('admin_footer','nectar_shortcode_content_display');


function nectar_shortcode_content_display() {
		
//Shortcodes Definitions
#-----------------------------------------------------------------
# Columns
#-----------------------------------------------------------------

//Half
$nectar_shortcodes['header_1'] = array( 
	'type'=>'heading', 
	'title'=>esc_html__('Columns', 'salient')
);

$nectar_shortcodes['one_half'] = array( 
	'type'=>'checkbox', 
	'title'=>esc_html__('One Half (1/2)', 'salient' ), 
	'attr'=>array( 
		'boxed'=>array('type'=>'custom', 'title'=>esc_html__('Boxed Column','salient')),
		'centered_text'=>array('type'=>'custom', 'title'=>esc_html__('Centered Text','salient')),
		'last'=>array( 'type'=>'custom', 'title'=>esc_html__('Last Column','salient'), 'desc' => esc_html__('Check this for the last column in a row. i.e. when the columns add up to 1.', 'salient')),
		'animation'=>array(
			'type'=>'select', 
			'half_width' => 'true',
			'title'  => esc_html__('Animation','salient'),
			'values' => array(
			     "none" => "None",
			     "fade-in" => "Fade In",
		  		 "fade-in-from-left" => "Fade In From Left",
		  		 "fade-in-right" => "Fade In From Right",
		  		 "fade-in-from-bottom" => "Fade In From Bottom",
		  		 "grow-in" => "Grow In"
			)
		),
		'delay'=>array(
			'type'=>'text', 
			'second_half_width' => 'true',
			'title'=>esc_html__('Animation Delay', 'salient'),
			'desc' => esc_html__('Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in "one by one" effect in horizontal columns. ', 'salient'),
		)
	)
);


//Thirds
$nectar_shortcodes['one_third'] = array( 
	'type'=>'checkbox', 
	'title'=>esc_html__('One Third Column (1/3)', 'salient' ), 
	'attr'=>array( 
		'boxed'=>array('type'=>'custom', 'title'=>esc_html__('Boxed Column','salient')),
		'centered_text'=>array('type'=>'custom', 'title'=>esc_html__('Centered Text','salient')),
		'last'=>array( 'type'=>'custom', 'title'=>esc_html__('Last Column','salient'), 'desc' => esc_html__('Check this for the last column in a row. i.e. when the columns add up to 1.', 'salient')),
		'animation'=>array(
			'type'=>'select', 
			'half_width' => 'true',
			'title'  => esc_html__('Animation','salient'),
			'values' => array(
			     "none" => esc_html__("None",'salient'),
			     "fade-in" => esc_html__("Fade In",'salient'),
		  		 "fade-in-from-left" => esc_html__("Fade In From Left",'salient'),
		  		 "fade-in-right" => esc_html__("Fade In From Right",'salient'),
		  		 "fade-in-from-bottom" => esc_html__("Fade In From Bottom",'salient'),
		  		 "grow-in" => esc_html__("Grow In",'salient')
			)
		),
		'delay'=>array(
			'type'=>'text', 
			'second_half_width' => 'true',
			'title'=>esc_html__('Animation Delay', 'salient'),
			'desc' => esc_html__('Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in "one by one" effect in horizontal columns. ', 'salient'),
		)
	)
);

$nectar_shortcodes['two_thirds'] = array( 
	'type'=>'checkbox', 
	'title'=>esc_html__('Two Thirds Column (2/3)', 'salient' ), 
	'attr'=>array( 
		'boxed'=>array('type'=>'custom', 'title'=>esc_html__('Boxed Column','salient')),
		'centered_text'=>array('type'=>'custom', 'title'=>esc_html__('Centered Text','salient')),
		'last'=>array( 'type'=>'custom', 'title'=>esc_html__('Last Column','salient'), 'desc' => esc_html__('Check this for the last column in a row. i.e. when the columns add up to 1.', 'salient')),
		'animation'=>array(
			'type'=>'select', 
			'half_width' => 'true',
			'title'  => esc_html__('Animation','salient'),
			'values' => array(
			     "none" => esc_html__("None",'salient'),
			     "fade-in" => esc_html__("Fade In",'salient'),
		  		 "fade-in-from-left" => esc_html__("Fade In From Left",'salient'),
		  		 "fade-in-right" => esc_html__("Fade In From Right",'salient'),
		  		 "fade-in-from-bottom" => esc_html__("Fade In From Bottom",'salient'),
		  		 "grow-in" => esc_html__("Grow In",'salient')
			)
		),
		'delay'=>array(
			'type'=>'text', 
			'second_half_width' => 'true',
			'title'=>esc_html__('Animation Delay', 'salient'),
			'desc' => esc_html__('Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in "one by one" effect in horizontal columns. ', 'salient'),
		)
	)
);


//Fourths
$nectar_shortcodes['one_fourth'] = array( 
	'type'=>'checkbox', 
	'title'=>esc_html__('One Fourth Column (1/4)', 'salient' ), 
	'attr'=>array( 
		'boxed'=>array('type'=>'custom', 'title'=>esc_html__('Boxed Column','salient')),
		'centered_text'=>array('type'=>'custom', 'title'=>esc_html__('Centered Text','salient')),
		'last'=>array( 'type'=>'custom', 'title'=>esc_html__('Last Column','salient'), 'desc' => esc_html__('Check this for the last column in a row. i.e. when the columns add up to 1.', 'salient')),
		'animation'=>array(
			'type'=>'select', 
			'half_width' => 'true',
			'title'  => esc_html__('Animation','salient'),
			'values' => array(
			    "none" => esc_html__("None",'salient'),
			     "fade-in" => esc_html__("Fade In",'salient'),
		  		 "fade-in-from-left" => esc_html__("Fade In From Left",'salient'),
		  		 "fade-in-right" => esc_html__("Fade In From Right",'salient'),
		  		 "fade-in-from-bottom" => esc_html__("Fade In From Bottom",'salient'),
		  		 "grow-in" => esc_html__("Grow In",'salient')
			)
		),
		'delay'=>array(
			'type'=>'text', 
			'second_half_width' => 'true',
			'title'=>esc_html__('Animation Delay', 'salient'),
			'desc' => esc_html__('Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in "one by one" effect in horizontal columns. ', 'salient'),
		)
	)
);

$nectar_shortcodes['three_fourths'] = array( 
	'type'=>'checkbox', 
	'title'=>esc_html__('Three Fourths Column (3/4)', 'salient' ), 
	'attr'=>array( 
		'boxed'=>array('type'=>'custom', 'title'=>esc_html__('Boxed Column','salient')),
		'centered_text'=>array('type'=>'custom', 'title'=>esc_html__('Centered Text','salient')),
		'last'=>array( 'type'=>'custom', 'title'=>esc_html__('Last Column','salient'), 'desc' => esc_html__('Check this for the last column in a row. i.e. when the columns add up to 1.', 'salient')),
		'animation'=>array(
			'type'=>'select', 
			'half_width' => 'true',
			'title'  => esc_html__('Animation','salient'),
			'values' => array(
			    "none" => esc_html__("None",'salient'),
			     "fade-in" => esc_html__("Fade In",'salient'),
		  		 "fade-in-from-left" => esc_html__("Fade In From Left",'salient'),
		  		 "fade-in-right" => esc_html__("Fade In From Right",'salient'),
		  		 "fade-in-from-bottom" => esc_html__("Fade In From Bottom",'salient'),
		  		 "grow-in" => esc_html__("Grow In",'salient')
			)
		),
		'delay'=>array(
			'type'=>'text', 
			'second_half_width' => 'true',
			'title'=>esc_html__('Animation Delay', 'salient'),
			'desc' => esc_html__('Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in "one by one" effect in horizontal columns. ', 'salient'),
		)
	)
);


//Sixths
$nectar_shortcodes['one_sixth'] = array( 
	'type'=>'checkbox', 
	'title'=>esc_html__('One Sixth Column (1/6)', 'salient' ), 
	'attr'=>array( 
		'boxed'=>array('type'=>'custom', 'title'=>esc_html__('Boxed Column','salient')),
		'centered_text'=>array('type'=>'custom', 'title'=>esc_html__('Centered Text','salient')),
		'last'=>array( 'type'=>'custom', 'title'=>esc_html__('Last Column','salient'), 'desc' => esc_html__('Check this for the last column in a row. i.e. when the columns add up to 1.', 'salient')),
		'animation'=>array(
			'type'=>'select', 
			'half_width' => 'true',
			'title'  => esc_html__('Animation','salient'),
			'values' => array(
			    "none" => esc_html__("None",'salient'),
			     "fade-in" => esc_html__("Fade In",'salient'),
		  		 "fade-in-from-left" => esc_html__("Fade In From Left",'salient'),
		  		 "fade-in-right" => esc_html__("Fade In From Right",'salient'),
		  		 "fade-in-from-bottom" => esc_html__("Fade In From Bottom",'salient'),
		  		 "grow-in" => esc_html__("Grow In",'salient')
			)
		),
		'delay'=>array(
			'type'=>'text', 
			'second_half_width' => 'true',
			'title'=>esc_html__('Animation Delay', 'salient'),
			'desc' => esc_html__('Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in "one by one" effect in horizontal columns. ', 'salient'),
		)
	)
);

$nectar_shortcodes['five_sixths'] = array( 
	'type'=>'checkbox', 
	'title'=>esc_html__('Five Sixths Column (5/6)', 'salient' ), 
	'attr'=>array( 
		'boxed'=>array('type'=>'custom', 'title'=>esc_html__('Boxed Column','salient')),
		'centered_text'=>array('type'=>'custom', 'title'=>esc_html__('Centered Text','salient')),
		'last'=>array( 'type'=>'custom', 'title'=>esc_html__('Last Column','salient'), 'desc' => esc_html__('Check this for the last column in a row. i.e. when the columns add up to 1.', 'salient')),
		'animation'=>array(
			'type'=>'select', 
			'half_width' => 'true',
			'title'  => esc_html__('Animation','salient'),
			'values' => array(
			     "none" => esc_html__("None",'salient'),
			     "fade-in" => esc_html__("Fade In",'salient'),
		  		 "fade-in-from-left" => esc_html__("Fade In From Left",'salient'),
		  		 "fade-in-right" => esc_html__("Fade In From Right",'salient'),
		  		 "fade-in-from-bottom" => esc_html__("Fade In From Bottom",'salient'),
		  		 "grow-in" => esc_html__("Grow In",'salient')
			)
		),
		'delay'=>array(
			'type'=>'text', 
			'second_half_width' => 'true',
			'title'=>esc_html__('Animation Delay', 'salient'),
			'desc' => esc_html__('Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in "one by one" effect in horizontal columns. ', 'salient'),
		)
	)
);

$nectar_shortcodes['one_whole'] = array( 
	'type'=>'checkbox', 
	'title'=>esc_html__('One Whole Column (1/1)', 'salient' ), 
	'attr'=>array( 
		'boxed'=>array('type'=>'custom', 'title'=>esc_html__('Boxed Column','salient')),
		'centered_text'=>array('type'=>'custom', 'title'=>esc_html__('Centered Text','salient')),
		'animation'=>array(
			'type'=>'select', 
			'half_width' => 'true',
			'title'  => esc_html__('Animation','salient'),
			'values' => array(
			     "none" => esc_html__("None",'salient'),
			     "fade-in" => esc_html__("Fade In",'salient'),
		  		 "fade-in-from-left" => esc_html__("Fade In From Left",'salient'),
		  		 "fade-in-right" => esc_html__("Fade In From Right",'salient'),
		  		 "fade-in-from-bottom" => esc_html__("Fade In From Bottom",'salient'),
		  		 "grow-in" => esc_html__("Grow In",'salient')
			)
		),
		'delay'=>array(
			'type'=>'text', 
			'second_half_width' => 'true',
			'title'=>esc_html__('Animation Delay', 'salient'),
			'desc' => esc_html__('Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in "one by one" effect in horizontal columns. ', 'salient'),
		)
	)
);


#-----------------------------------------------------------------
# Elements 
#-----------------------------------------------------------------

$nectar_shortcodes['header_6'] = array( 
	'type'=>'heading', 
	'title'=>esc_html__('Elements', 'salient' )
); 

//nectar Slider
global $nectar_options;
$nectar_disable_nectar_slider = (!empty($nectar_options['disable_nectar_slider_pt']) && $nectar_options['disable_nectar_slider_pt'] == '1') ? true : false; 
	
	if($nectar_disable_nectar_slider != true) {

		$slider_locations = get_terms('slider-locations');
		$locations = array();

		foreach ($slider_locations as $location) {
			$locations[$location->slug] = $location->name;
		}

		if (empty($locations)) {
			$location_desc = 
		      '<div class="alert">' .
			 esc_html__('You currently don\'t have any Slider Locations setup. Please create some and add assign slides to them before using this!','salient'). 
			'<br/><br/>
			<a href="' . esc_url(admin_url('edit.php?post_type=nectar_slider')) . '">'. esc_html__('Link to Nectar Slider', 'salient') . '</a>
			</div>';
		} else { $location_desc = ''; }

		$nectar_shortcodes['nectar_slider'] = array( 
			'type'=>'regular', 
			'title'=>esc_html__('Nectar Slider', 'salient' ), 
			'attr'=>array( 
				'location'=>array(
					'type'=>'select', 
					'desc' => $location_desc,
					'title'  => esc_html__('Select Slider','salient'),
					'values' => $locations
				),
				
				'slider_height'=>array(
					'type'=>'text', 
					'title'=>esc_html__('Slider Height', 'salient'),
					'desc' => esc_html__('Don\'nt include "px" in your string. e.g. 650', 'salient'),
				),
				
				'flexible_slider_height'=>array('type'=>'checkbox',  'desc' => 'Would you like the height of your slider to constantly scale in porportion to the screen size?', 'title'=>esc_html__('Flexible Slider Height', 'salient')),
				'full_width'=>array('type'=>'checkbox',  'desc' => 'Would you like this slider to display the full width of the page?', 'title'=>esc_html__('Display Full Width?', 'salient')),
				'arrow_navigation'=>array('type'=>'checkbox',  'desc' => 'Would you like this slider to display arrows on the right and left sides?', 'title'=>esc_html__('Display Arrow Navigation', 'salient')),
				'bullet_navigation'=>array('type'=>'checkbox',  'desc' => 'Would you like this slider to display bullets on the bottom?', 'title'=>esc_html__('Display Bullet Navigation', 'salient')),
				'bullet_navigation_style'=>array(
					'type'=>'select', 
					'desc' => 'Please select your overall bullet navigation style here.',
					'title'  => esc_html__('Bullet Navigation Style','salient'),
					'values' => array(
						'See Through & Solid On Active' => 'see_through',
						'Solid & Scale On Active' => 'scale'
					)
				),
				'desktop_swipe'=>array('type'=>'checkbox',  'desc' => 'Would you like this slider to have swipe interaction on desktop?', 'title'=>esc_html__('Enable Swipe on Desktop?', 'salient')),
				'parallax'=>array('type'=>'checkbox',  'desc' => 'will only activate if the slider is the <b>top level element</b> in the page', 'title'=>esc_html__('Parallax Slider?', 'salient')),
				'loop'=>array('type'=>'checkbox',  'desc' => 'Would you like your slider to loop infinitely?', 'title'=>esc_html__('Loop Slider?', 'salient')),
				'fullscreen'=>array('type'=>'checkbox',  'desc' => 'This will only become active when used in combination with the full width option', 'title'=>esc_html__('Fullscreen Slider?', 'salient')),
				'slider_transition'=>array(
					'type'=>'select', 
					'desc' => 'Please select your slider transition here',
					'title'  => esc_html__('Slider Transition','salient'),
					'values' => array(
						'slide' => 'slide',
						'fade' => 'fade'
					)
				),
				'slider_button_styling'=>array(
					'type'=>'select', 
					'desc' => 'Slider Next/Prev Button Styling',
					'title'  => esc_html__('Slider Transition','salient'),
					'values' => array(
						'Standard With Slide Count On Hover' => 'btn_with_count',
						'Next/Prev Slide Preview On Hover' => 'btn_with_preview'
					)
				),
				'button_sizing'=>array(
					'type'=>'select', 
					'desc' => 'Please select your desired button sizing',
					'title'  => esc_html__('Button Sizing','salient'),
					'values' => array(
						"regular" => "regular",
						"large" => "large",
						"jumbo" => "jumbo"
					)
				),
				'autorotate'=>array('type'=>'text',  'desc' => 'If you would like this slider to autorotate, enter the rotation speed in <b>miliseconds</b> here. i.e 5000', 'title'=>esc_html__('Autorotate?', 'salient'))
			)
		);

}
 
//Full Width Section
$nectar_shortcodes['full_width_section'] = array( 
	'type'=>'custom', 
	'title'=>esc_html__('Full Width Section', 'salient' ), 
	'attr'=>array( 
	    'color' =>array('type'=>'custom', 'title'  => esc_html__('Background Color','salient')),
		'image'=>array('type'=>'custom', 'title'  => esc_html__('Background Image','salient')),
		'bg_pos'=>array(
			'type'=>'select', 
			'title'  => esc_html__('Background Position','salient'),
			'values' => array(
			     "left top" => esc_html__("Left Top",'salient'),
		  		 "left center" => esc_html__("Left Center",'salient'),
		  		 "left bottom" => esc_html__("Left Bottom",'salient'),
		  		 "center top" => esc_html__("Center Top",'salient'),
		  		 "center center" => esc_html__("Center Center",'salient'),
		  		 "center bottom" => esc_html__("Center Bottom",'salient'),
		  		 "right top" => esc_html__("Right Top",'salient'),
		  		 "right center" => esc_html__("Right Center",'salient'),
		  		 "right bottom" => esc_html__("Right Bottom",'salient')
			)
		),
		'bg_repeat'=>array(
			'type'=>'select', 
			'title'  => esc_html__('Background Repeat','salient'),
			'values' => array(
			    "no-repeat" => esc_html__("No-Repeat",'salient'),
		  		 "repeat" => esc_html__("Repeat",'salient')
			)
		),
		'parallax_bg'=>array('type'=>'checkbox', 'title'=>esc_html__('Parallax Background', 'salient')),
		'text_color'=>array(
			'type'=>'select', 
			'title'  => esc_html__('Text Color','salient'),
			'values' => array(
		  		 "light_text" => esc_html__("Light",'salient'),
		  		 "dark_text" => esc_html__("Dark",'salient')
			)
		),
		
		'top_padding'=>array(
			'type'=>'text', 
			'title'=>esc_html__('Top Padding', 'salient'),
			'desc' => esc_html__('Don\'nt include "px" in your string. e.g. 40', 'salient'),
		),
		'bottom_padding'=>array(
			'type'=>'text', 
			'title'=>esc_html__('Bottom Padding', 'salient'),
			'desc' => esc_html__('Don\'nt include "px" in your string. e.g. 40', 'salient'),
		),
		
	)
);


//Image with Animation
$nectar_shortcodes['image_with_animation'] = array( 
	'type'=>'custom', 
	'title'=>esc_html__('Image With Animation', 'salient' ), 
	'attr'=>array( 
		'image'=>array('type'=>'custom', 'title'  => esc_html__('Image','salient')),
		'animation'=>array(
			'type'=>'select', 
			'title'  => esc_html__('Image Animation','salient'),
			'values' => array(
			    "fade-in" => esc_html__("Fade In",'salient'),
		  		 "fade-in-from-left" => esc_html__("Fade In From Left",'salient'),
		  		 "fade-in-right" => esc_html__("Fade In From Right",'salient'),
		  		 "fade-in-from-bottom" => esc_html__("Fade In From Bottom",'salient'),
		  		 "grow-in" => esc_html__("Grow In",'salient')
			)
		),
		'delay'=>array(
			'type'=>'text', 
			'title'=>esc_html__('Delay', 'salient'),
			'desc' => esc_html__('Enter delay (in milliseconds) if needed e.g. 150. This parameter comes in handy when creating the animate in "one by one" effect in horizontal columns. ', 'salient'),
		),
	)
);

//Heading
$nectar_shortcodes['heading'] = array( 
	'type'=>'simple', 
	'title'=>esc_html__('Centered Heading', 'salient' ), 
	'attr'=>array( 
		'subtitle'=>array('type'=>'text', 'title'=>esc_html__('Subtitle', 'salient')) 
	)
);

//Divider
$nectar_shortcodes['divider'] = array( 
	'type'=>'regular', 
	'title'=>esc_html__('Divider', 'salient' ), 
	'attr'=>array( 
		'line_type'=>array(
			'type'=>'select', 
			'title'  => esc_html__('Display Line?','salient'),
			'values' => array(
			     "no-line" => esc_html__("No Line",'salient'),
		  		 "full-width" => esc_html__("Full Width Line",'salient'),
		  		 "small" => esc_html__("Small Line",'salient')
			)
		),
		'custom_height'=>array(
			'type'=>'text', 
			'desc' => 'If you would like to control the specifc number of pixels your divider is, enter it here. <b>Don\'t enter "px", just the numnber e.g. "20"</b>', 
			'title'=>esc_html__('Custom Dividing Height', 'salient')
		)
	)
);

//Divider
$nectar_shortcodes['nectar_dropcap'] = array( 
	'type'=>'simple', 
	'title'=>esc_html__('Dropcap', 'salient' ),
	'attr'=>array(
		'text_color' =>array(
			'type'=>'custom', 
			'title'  => esc_html__('Color','salient')
		)
	)
);

//Milestone 
$nectar_shortcodes['milestone'] = array( 
	'type'=>'regular', 
	'title'=>esc_html__('Milestone', 'salient'), 
	'attr'=>array( 
		'number'=>array('type'=>'text', 'desc' => 'The number/count of your milestone e.g. "13"', 'title'=>esc_html__('Milestone Number', 'salient')),
		'subject'=>array('type'=>'text', 'desc' => 'The subject of your milestones e.g. "Projects Completed"', 'title'=>esc_html__('Milestone Subject', 'salient')),
		'symbol'=>array('type'=>'text', 'desc' => 'An optional symbol to place next to the number counted to. e.g. "%" or "+"', 'title'=>esc_html__('Milestone Symbol', 'salient')),
		'symbol_position'=>array(
			'type'=>'select', 
			'title'  => esc_html__('Milestone Symbol Position','salient'),
			'values' => array(
			    "after" => "after",
		 		"before" => "before",
			)
		),
		'color'=>array(
			'type'=>'select', 
			'title'  => esc_html__('Color','salient'),
			'values' => array(
			     "default" => "Default",
			     "accent-color" => esc_html__("Accent-Color",'salient'),
		  		 "extra-color-1" => esc_html__("Extra-Color-1",'salient'),
		  		 "extra-color-2" => esc_html__("Extra-Color-2",'salient'),
		  		 "extra-color-3" => esc_html__("Extra-Color-3",'salient')
			)
		),
		'number_font_size'=>array('type'=>'text', 'desc' => 'Enter your size in pixels, the default is 62.', 'title'=>esc_html__('Milestone Number Font Size', 'salient')),
		'symbol_font_size'=>array('type'=>'text', 'desc' => 'Enter your size in pixels', 'title'=>esc_html__('Milestone Symbol Font Size', 'salient')),
		'symbol_alignment'=>array(
			'type'=>'select', 
			'title'  => esc_html__('Color','salient'),
			'values' => array(
			     "default" => "Default",
			     "superscript" => esc_html__("Superscript",'salient')
			)
		)
	)
);



//Icon
$linea = array(
	'type'=>'icons', 
	'title'=>'Linea', 
	'values'=> array(
	  'arrows_anticlockwise.svg' => 'icon-arrows-anticlockwise',
	  'arrows_anticlockwise_dashed.svg' => 'icon-arrows-anticlockwise-dashed',
	  'arrows_button_down.svg' => 'icon-arrows-button-down',
	  'arrows_button_off.svg' => 'icon-arrows-button-off',
	  'arrows_button_on.svg' => 'icon-arrows-button-on',
	  'arrows_button_up.svg' => 'icon-arrows-button-up',
	  'arrows_check.svg' => 'icon-arrows-check',
	  'arrows_circle_check.svg' => 'icon-arrows-circle-check',
	  'arrows_circle_down.svg' => 'icon-arrows-circle-down',
	  'arrows_circle_downleft.svg' => 'icon-arrows-circle-downleft',
	  'arrows_circle_downright.svg' => 'icon-arrows-circle-downright',
	  'arrows_circle_left.svg' => 'icon-arrows-circle-left',
	  'arrows_circle_minus.svg' => 'icon-arrows-circle-minus',
	  'arrows_circle_plus.svg' => 'icon-arrows-circle-plus',
	  'arrows_circle_remove.svg' => 'icon-arrows-circle-remove',
	  'arrows_circle_right.svg' => 'icon-arrows-circle-right',
	  'arrows_circle_up.svg' => 'icon-arrows-circle-up',
	  'arrows_circle_upleft.svg' => 'icon-arrows-circle-upleft',
	  'arrows_circle_upright.svg' => 'icon-arrows-circle-upright',
	  'arrows_clockwise.svg' => 'icon-arrows-clockwise',
	  'arrows_clockwise_dashed.svg' => 'icon-arrows-clockwise-dashed',
	  'arrows_compress.svg' => 'icon-arrows-compress',
	  'arrows_deny.svg' => 'icon-arrows-deny',
	  'arrows_diagonal.svg' => 'icon-arrows-diagonal',
	  'arrows_diagonal2.svg' => 'icon-arrows-diagonal2',
	  'arrows_down.svg' => 'icon-arrows-down',
	  'arrows_downleft.svg' => 'icon-arrows-down-double',
	  'arrows_downright.svg' => 'icon-arrows-downleft',
	  'arrows_down_double-34.svg' => 'icon-arrows-downright',
	  'arrows_drag_down.svg' => 'icon-arrows-drag-down',
	  'arrows_drag_down_dashed.svg' => 'icon-arrows-drag-down-dashed',
	  'arrows_drag_horiz.svg' => 'icon-arrows-drag-horiz',
	  'arrows_drag_left.svg' => 'icon-arrows-drag-left',
	  'arrows_drag_left_dashed.svg' => 'icon-arrows-drag-left-dashed',
	  'arrows_drag_right.svg' => 'icon-arrows-drag-right',
	  'arrows_drag_right_dashed.svg' => 'icon-arrows-drag-right-dashed',
	  'arrows_drag_up.svg' => 'icon-arrows-drag-up',
	  'arrows_drag_up_dashed.svg' => 'icon-arrows-drag-up-dashed',
	  'arrows_drag_vert.svg' => 'icon-arrows-drag-vert',
	  'arrows_exclamation.svg' => 'icon-arrows-exclamation',
	  'arrows_expand.svg' => 'icon-arrows-expand',
	  'arrows_expand_diagonal1.svg' => 'icon-arrows-expand-diagonal1',
	  'arrows_expand_horizontal1.svg' => 'icon-arrows-expand-horizontal1',
	  'arrows_expand_vertical1.svg' => 'icon-arrows-expand-vertical1',
	  'arrows_fit_horizontal.svg' => 'icon-arrows-fit-horizontal',
	  'arrows_fit_vertical.svg' => 'icon-arrows-fit-vertical',
	  'arrows_glide.svg' => 'icon-arrows-glide',
	  'arrows_glide_horizontal.svg' => 'icon-arrows-glide-horizontal',
	  'arrows_glide_vertical.svg' => 'icon-arrows-glide-vertical',
	  'arrows_hamburger 2.svg' => 'icon-arrows-hamburger1',
	  'arrows_hamburger1.svg' => 'icon-arrows-hamburger-2',
	  'arrows_horizontal.svg' => 'icon-arrows-horizontal',
	  'arrows_info.svg' => 'icon-arrows-info',
	  'arrows_keyboard_alt.svg' => 'icon-arrows-keyboard-alt',
	  'arrows_keyboard_cmd-29.svg' => 'icon-arrows-keyboard-cmd',
	  'arrows_keyboard_delete.svg' => 'icon-arrows-keyboard-delete',
	  'arrows_keyboard_down-28.svg' => 'icon-arrows-keyboard-down',
	  'arrows_keyboard_left.svg' => 'icon-arrows-keyboard-left',
	  'arrows_keyboard_return.svg' => 'icon-arrows-keyboard-return',
	  'arrows_keyboard_right.svg' => 'icon-arrows-keyboard-right',
	  'arrows_keyboard_shift.svg' => 'icon-arrows-keyboard-shift',
	  'arrows_keyboard_tab.svg' => 'icon-arrows-keyboard-tab',
	  'arrows_keyboard_up.svg' => 'icon-arrows-keyboard-up',
	  'arrows_left.svg' => 'icon-arrows-left',
	  'arrows_left_double-32.svg' => 'icon-arrows-left-double-32',
	  'arrows_minus.svg' => 'icon-arrows-minus',
	  'arrows_move.svg' => 'icon-arrows-move',
	  'arrows_move2.svg' => 'icon-arrows-move2',
	  'arrows_move_bottom.svg' => 'icon-arrows-move-bottom',
	  'arrows_move_left.svg' => 'icon-arrows-move-left',
	  'arrows_move_right.svg' => 'icon-arrows-move-right',
	  'arrows_move_top.svg' => 'icon-arrows-move-top',
	  'arrows_plus.svg' => 'icon-arrows-plus',
	  'arrows_question.svg' => 'icon-arrows-question',
	  'arrows_remove.svg' => 'icon-arrows-remove',
	  'arrows_right.svg' => 'icon-arrows-right',
	  'arrows_right_double-31.svg' => 'icon-arrows-right-double',
	  'arrows_rotate.svg' => 'icon-arrows-rotate',
	  'arrows_rotate_anti.svg' => 'icon-arrows-rotate-anti',
	  'arrows_rotate_anti_dashed.svg' => 'icon-arrows-rotate-anti-dashed',
	  'arrows_rotate_dashed.svg' => 'icon-arrows-rotate-dashed',
	  'arrows_shrink.svg' => 'icon-arrows-shrink',
	  'arrows_shrink_diagonal1.svg' => 'icon-arrows-shrink-diagonal1',
	  'arrows_shrink_diagonal2.svg' => 'icon-arrows-shrink-diagonal2',
	  'arrows_shrink_horizonal2.svg' => 'icon-arrows-shrink-horizonal2',
	  'arrows_shrink_horizontal1.svg' => 'icon-arrows-shrink-horizontal1',
	  'arrows_shrink_vertical1.svg' => 'icon-arrows-shrink-vertical1',
	  'arrows_shrink_vertical2.svg' => 'icon-arrows-shrink-vertical2',
	  'arrows_sign_down.svg' => 'icon-arrows-sign-down',
	  'arrows_sign_left.svg' => 'icon-arrows-sign-left',
	  'arrows_sign_right.svg' => 'icon-arrows-sign-right',
	  'arrows_sign_up.svg' => 'icon-arrows-sign-up',
	  'arrows_slide_down1.svg' => 'icon-arrows-slide-down1',
	  'arrows_slide_down2.svg' => 'icon-arrows-slide-down2',
	  'arrows_slide_left1.svg' => 'icon-arrows-slide-left1',
	  'arrows_slide_left2.svg' => 'icon-arrows-slide-left2',
	  'arrows_slide_right1.svg' => 'icon-arrows-slide-right1',
	  'arrows_slide_right2.svg' => 'icon-arrows-slide-right2',
	  'arrows_slide_up1.svg' => 'icon-arrows-slide-up1',
	  'arrows_slide_up2.svg' => 'icon-arrows-slide-up2',
	  'arrows_slim_down.svg' => 'icon-arrows-slim-down',
	  'arrows_slim_down_dashed.svg' => 'icon-arrows-slim-down-dashed',
	  'arrows_slim_left.svg' => 'icon-arrows-slim-left',
	  'arrows_slim_left_dashed.svg' => 'icon-arrows-slim-left-dashed',
	  'arrows_slim_right.svg' => 'icon-arrows-slim-right',
	  'arrows_slim_right_dashed.svg' => 'icon-arrows-slim-right-dashed',
	  'arrows_slim_up.svg' => 'icon-arrows-slim-up',
	  'arrows_slim_up_dashed.svg' => 'icon-arrows-slim-up-dashed',
	  'arrows_squares.svg' => 'icon-arrows-squares',
	  'arrows_square_check.svg' => 'icon-arrows-square-check',
	  'arrows_square_down.svg' => 'icon-arrows-square-down',
	  'arrows_square_downleft.svg' => 'icon-arrows-square-downleft',
	  'arrows_square_downright.svg' => 'icon-arrows-square-downright',
	  'arrows_square_left.svg' => 'icon-arrows-square-left',
	  'arrows_square_minus.svg' => 'icon-arrows-square-minus',
	  'arrows_square_plus.svg' => 'icon-arrows-square-plus',
	  'arrows_square_remove.svg' => 'icon-arrows-square-remove',
	  'arrows_square_right.svg' => 'icon-arrows-square-right',
	  'arrows_square_up.svg' => 'icon-arrows-square-up',
	  'arrows_square_upleft.svg' => 'icon-arrows-square-upleft',
	  'arrows_square_upright.svg' => 'icon-arrows-square-upright',
	  'arrows_stretch_diagonal1.svg' => 'icon-arrows-stretch-diagonal1',
	  'arrows_stretch_diagonal2.svg' => 'icon-arrows-stretch-diagonal2',
	  'arrows_stretch_diagonal3.svg' => 'icon-arrows-stretch-diagonal3',
	  'arrows_stretch_diagonal4.svg' => 'icon-arrows-stretch-diagonal4',
	  'arrows_stretch_horizontal1.svg' => 'icon-arrows-stretch-horizontal1',
	  'arrows_stretch_horizontal2.svg' => 'icon-arrows-stretch-horizontal2',
	  'arrows_stretch_vertical1.svg' => 'icon-arrows-stretch-vertical1',
	  'arrows_stretch_vertical2.svg' => 'icon-arrows-stretch-vertical2',
	  'arrows_switch_horizontal.svg' => 'icon-arrows-switch-horizontal',
	  'arrows_switch_vertical.svg' => 'icon-arrows-switch-vertical',
	  'arrows_up.svg' => 'icon-arrows-up',
	  'arrows_upright.svg' => 'icon-arrows-upright',
	  'arrows_up_double.svg' => 'icon-arrows-up-double-33',
	  'arrows_vertical.svg' => 'icon-arrows-vertical',
	  'basic_accelerator.svg' => 'icon-basic-accelerator',
	  'basic_alarm.svg' => 'icon-basic-alarm',
	  'basic_anchor.svg' => 'icon-basic-anchor',
	  'basic_anticlockwise.svg' => 'icon-basic-anticlockwise',
	  'basic_archive.svg' => 'icon-basic-archive',
	  'basic_archive_full.svg' => 'icon-basic-archive-full',
	  'basic_ban.svg' => 'icon-basic-ban',
	  'basic_battery_charge.svg' => 'icon-basic-battery-charge',
	  'basic_battery_empty.svg' => 'icon-basic-battery-empty',
	  'basic_battery_full.svg' => 'icon-basic-battery-full',
	  'basic_battery_half.svg' => 'icon-basic-battery-half',
	  'basic_bolt.svg' => 'icon-basic-bolt',
	  'basic_book.svg' => 'icon-basic-book',
	  'basic_bookmark.svg' => 'icon-basic-book-pen',
	  'basic_book_pen.svg' => 'icon-basic-book-pencil',
	  'basic_book_pencil.svg' => 'icon-basic-bookmark',
	  'basic_calculator.svg' => 'icon-basic-calculator',
	  'basic_calendar.svg' => 'icon-basic-calendar',
	  'basic_cards_diamonds.svg' => 'icon-basic-cards-diamonds',
	  'basic_cards_hearts.svg' => 'icon-basic-cards-hearts',
	  'basic_case.svg' => 'icon-basic-case',
	  'basic_chronometer.svg' => 'icon-basic-chronometer',
	  'basic_clessidre.svg' => 'icon-basic-clessidre',
	  'basic_clock.svg' => 'icon-basic-clock',
	  'basic_clockwise.svg' => 'icon-basic-clockwise',
	  'basic_cloud.svg' => 'icon-basic-cloud',
	  'basic_clubs.svg' => 'icon-basic-clubs',
	  'basic_compass.svg' => 'icon-basic-compass',
	  'basic_cup.svg' => 'icon-basic-cup',
	  'basic_diamonds.svg' => 'icon-basic-diamonds',
	  'basic_display.svg' => 'icon-basic-display',
	  'basic_download.svg' => 'icon-basic-download',
	  'basic_elaboration_bookmark_checck.svg' => 'icon-basic-elaboration-bookmark-checck',
	  'basic_elaboration_bookmark_minus.svg' => 'icon-basic-elaboration-bookmark-minus',
	  'basic_elaboration_bookmark_plus.svg' => 'icon-basic-elaboration-bookmark-plus',
	  'basic_elaboration_bookmark_remove.svg' => 'icon-basic-elaboration-bookmark-remove',
	  'basic_elaboration_briefcase_check.svg' => 'icon-basic-elaboration-briefcase-check',
	  'basic_elaboration_briefcase_download.svg' => 'icon-basic-elaboration-briefcase-download',
	  'basic_elaboration_briefcase_flagged.svg' => 'icon-basic-elaboration-briefcase-flagged',
	  'basic_elaboration_briefcase_minus.svg' => 'icon-basic-elaboration-briefcase-minus',
	  'basic_elaboration_briefcase_plus.svg' => 'icon-basic-elaboration-briefcase-plus',
	  'basic_elaboration_briefcase_refresh.svg' => 'icon-basic-elaboration-briefcase-refresh',
	  'basic_elaboration_briefcase_remove.svg' => 'icon-basic-elaboration-briefcase-remove',
	  'basic_elaboration_briefcase_search.svg' => 'icon-basic-elaboration-briefcase-search',
	  'basic_elaboration_briefcase_star.svg' => 'icon-basic-elaboration-briefcase-star',
	  'basic_elaboration_briefcase_upload.svg' => 'icon-basic-elaboration-briefcase-upload',
	  'basic_elaboration_browser_check.svg' => 'icon-basic-elaboration-browser-check',
	  'basic_elaboration_browser_download.svg' => 'icon-basic-elaboration-browser-download',
	  'basic_elaboration_browser_minus.svg' => 'icon-basic-elaboration-browser-minus',
	  'basic_elaboration_browser_plus.svg' => 'icon-basic-elaboration-browser-plus',
	  'basic_elaboration_browser_refresh.svg' => 'icon-basic-elaboration-browser-refresh',
	  'basic_elaboration_browser_remove.svg' => 'icon-basic-elaboration-browser-remove',
	  'basic_elaboration_browser_search.svg' => 'icon-basic-elaboration-browser-search',
	  'basic_elaboration_browser_star.svg' => 'icon-basic-elaboration-browser-star',
	  'basic_elaboration_browser_upload.svg' => 'icon-basic-elaboration-browser-upload',
	  'basic_elaboration_calendar_check.svg' => 'icon-basic-elaboration-calendar-check',
	  'basic_elaboration_calendar_cloud.svg' => 'icon-basic-elaboration-calendar-cloud',
	  'basic_elaboration_calendar_download.svg' => 'icon-basic-elaboration-calendar-download',
	  'basic_elaboration_calendar_empty.svg' => 'icon-basic-elaboration-calendar-empty',
	  'basic_elaboration_calendar_flagged.svg' => 'icon-basic-elaboration-calendar-flagged',
	  'basic_elaboration_calendar_heart.svg' => 'icon-basic-elaboration-calendar-heart',
	  'basic_elaboration_calendar_minus.svg' => 'icon-basic-elaboration-calendar-minus',
	  'basic_elaboration_calendar_next.svg' => 'icon-basic-elaboration-calendar-next',
	  'basic_elaboration_calendar_noaccess.svg' => 'icon-basic-elaboration-calendar-noaccess',
	  'basic_elaboration_calendar_pencil.svg' => 'icon-basic-elaboration-calendar-pencil',
	  'basic_elaboration_calendar_plus.svg' => 'icon-basic-elaboration-calendar-plus',
	  'basic_elaboration_calendar_previous.svg' => 'icon-basic-elaboration-calendar-previous',
	  'basic_elaboration_calendar_refresh.svg' => 'icon-basic-elaboration-calendar-refresh',
	  'basic_elaboration_calendar_remove.svg' => 'icon-basic-elaboration-calendar-remove',
	  'basic_elaboration_calendar_search.svg' => 'icon-basic-elaboration-calendar-search',
	  'basic_elaboration_calendar_star.svg' => 'icon-basic-elaboration-calendar-star',
	  'basic_elaboration_calendar_upload.svg' => 'icon-basic-elaboration-calendar-upload',
	  'basic_elaboration_cloud_check.svg' => 'icon-basic-elaboration-cloud-check',
	  'basic_elaboration_cloud_download.svg' => 'icon-basic-elaboration-cloud-download',
	  'basic_elaboration_cloud_minus.svg' => 'icon-basic-elaboration-cloud-minus',
	  'basic_elaboration_cloud_noaccess.svg' => 'icon-basic-elaboration-cloud-noaccess',
	  'basic_elaboration_cloud_plus.svg' => 'icon-basic-elaboration-cloud-plus',
	  'basic_elaboration_cloud_refresh.svg' => 'icon-basic-elaboration-cloud-refresh',
	  'basic_elaboration_cloud_remove.svg' => 'icon-basic-elaboration-cloud-remove',
	  'basic_elaboration_cloud_search.svg' => 'icon-basic-elaboration-cloud-search',
	  'basic_elaboration_cloud_upload.svg' => 'icon-basic-elaboration-cloud-upload',
	  'basic_elaboration_document_check.svg' => 'icon-basic-elaboration-document-check',
	  'basic_elaboration_document_cloud.svg' => 'icon-basic-elaboration-document-cloud',
	  'basic_elaboration_document_download.svg' => 'icon-basic-elaboration-document-download',
	  'basic_elaboration_document_flagged.svg' => 'icon-basic-elaboration-document-flagged',
	  'basic_elaboration_document_graph.svg' => 'icon-basic-elaboration-document-graph',
	  'basic_elaboration_document_heart.svg' => 'icon-basic-elaboration-document-heart',
	  'basic_elaboration_document_minus.svg' => 'icon-basic-elaboration-document-minus',
	  'basic_elaboration_document_next.svg' => 'icon-basic-elaboration-document-next',
	  'basic_elaboration_document_noaccess.svg' => 'icon-basic-elaboration-document-noaccess',
	  'basic_elaboration_document_note.svg' => 'icon-basic-elaboration-document-note',
	  'basic_elaboration_document_pencil.svg' => 'icon-basic-elaboration-document-pencil',
	  'basic_elaboration_document_picture.svg' => 'icon-basic-elaboration-document-picture',
	  'basic_elaboration_document_plus.svg' => 'icon-basic-elaboration-document-plus',
	  'basic_elaboration_document_previous.svg' => 'icon-basic-elaboration-document-previous',
	  'basic_elaboration_document_refresh.svg' => 'icon-basic-elaboration-document-refresh',
	  'basic_elaboration_document_remove.svg' => 'icon-basic-elaboration-document-remove',
	  'basic_elaboration_document_search.svg' => 'icon-basic-elaboration-document-search',
	  'basic_elaboration_document_star.svg' => 'icon-basic-elaboration-document-star',
	  'basic_elaboration_document_upload.svg' => 'icon-basic-elaboration-document-upload',
	  'basic_elaboration_folder_check.svg' => 'icon-basic-elaboration-folder-check',
	  'basic_elaboration_folder_cloud.svg' => 'icon-basic-elaboration-folder-cloud',
	  'basic_elaboration_folder_document.svg' => 'icon-basic-elaboration-folder-document',
	  'basic_elaboration_folder_download.svg' => 'icon-basic-elaboration-folder-download',
	  'basic_elaboration_folder_flagged.svg' => 'icon-basic-elaboration-folder-flagged',
	  'basic_elaboration_folder_graph.svg' => 'icon-basic-elaboration-folder-graph',
	  'basic_elaboration_folder_heart.svg' => 'icon-basic-elaboration-folder-heart',
	  'basic_elaboration_folder_minus.svg' => 'icon-basic-elaboration-folder-minus',
	  'basic_elaboration_folder_next.svg' => 'icon-basic-elaboration-folder-next',
	  'basic_elaboration_folder_noaccess.svg' => 'icon-basic-elaboration-folder-noaccess',
	  'basic_elaboration_folder_note.svg' => 'icon-basic-elaboration-folder-note',
	  'basic_elaboration_folder_pencil.svg' => 'icon-basic-elaboration-folder-pencil',
	  'basic_elaboration_folder_picture.svg' => 'icon-basic-elaboration-folder-picture',
	  'basic_elaboration_folder_plus.svg' => 'icon-basic-elaboration-folder-plus',
	  'basic_elaboration_folder_previous.svg' => 'icon-basic-elaboration-folder-previous',
	  'basic_elaboration_folder_refresh.svg' => 'icon-basic-elaboration-folder-refresh',
	  'basic_elaboration_folder_remove.svg' => 'icon-basic-elaboration-folder-remove',
	  'basic_elaboration_folder_search.svg' => 'icon-basic-elaboration-folder-search',
	  'basic_elaboration_folder_star.svg' => 'icon-basic-elaboration-folder-star',
	  'basic_elaboration_folder_upload.svg' => 'icon-basic-elaboration-folder-upload',
	  'basic_elaboration_mail_check.svg' => 'icon-basic-elaboration-mail-check',
	  'basic_elaboration_mail_cloud.svg' => 'icon-basic-elaboration-mail-cloud',
	  'basic_elaboration_mail_document.svg' => 'icon-basic-elaboration-mail-document',
	  'basic_elaboration_mail_download.svg' => 'icon-basic-elaboration-mail-download',
	  'basic_elaboration_mail_flagged.svg' => 'icon-basic-elaboration-mail-flagged',
	  'basic_elaboration_mail_heart.svg' => 'icon-basic-elaboration-mail-heart',
	  'basic_elaboration_mail_next.svg' => 'icon-basic-elaboration-mail-next',
	  'basic_elaboration_mail_noaccess.svg' => 'icon-basic-elaboration-mail-noaccess',
	  'basic_elaboration_mail_note.svg' => 'icon-basic-elaboration-mail-note',
	  'basic_elaboration_mail_pencil.svg' => 'icon-basic-elaboration-mail-pencil',
	  'basic_elaboration_mail_picture.svg' => 'icon-basic-elaboration-mail-picture',
	  'basic_elaboration_mail_previous.svg' => 'icon-basic-elaboration-mail-previous',
	  'basic_elaboration_mail_refresh.svg' => 'icon-basic-elaboration-mail-refresh',
	  'basic_elaboration_mail_remove.svg' => 'icon-basic-elaboration-mail-remove',
	  'basic_elaboration_mail_search.svg' => 'icon-basic-elaboration-mail-search',
	  'basic_elaboration_mail_star.svg' => 'icon-basic-elaboration-mail-star',
	  'basic_elaboration_mail_upload.svg' => 'icon-basic-elaboration-mail-upload',
	  'basic_elaboration_message_check.svg' => 'icon-basic-elaboration-message-check',
	  'basic_elaboration_message_dots.svg' => 'icon-basic-elaboration-message-dots',
	  'basic_elaboration_message_happy.svg' => 'icon-basic-elaboration-message-happy',
	  'basic_elaboration_message_heart.svg' => 'icon-basic-elaboration-message-heart',
	  'basic_elaboration_message_minus.svg' => 'icon-basic-elaboration-message-minus',
	  'basic_elaboration_message_note.svg' => 'icon-basic-elaboration-message-note',
	  'basic_elaboration_message_plus.svg' => 'icon-basic-elaboration-message-plus',
	  'basic_elaboration_message_refresh.svg' => 'icon-basic-elaboration-message-refresh',
	  'basic_elaboration_message_remove.svg' => 'icon-basic-elaboration-message-remove',
	  'basic_elaboration_message_sad.svg' => 'icon-basic-elaboration-message-sad',
	  'basic_elaboration_smartphone_cloud.svg' => 'icon-basic-elaboration-smartphone-cloud',
	  'basic_elaboration_smartphone_heart.svg' => 'icon-basic-elaboration-smartphone-heart',
	  'basic_elaboration_smartphone_noaccess.svg' => 'icon-basic-elaboration-smartphone-noaccess',
	  'basic_elaboration_smartphone_note.svg' => 'icon-basic-elaboration-smartphone-note',
	  'basic_elaboration_smartphone_pencil.svg' => 'icon-basic-elaboration-smartphone-pencil',
	  'basic_elaboration_smartphone_picture.svg' => 'icon-basic-elaboration-smartphone-picture',
	  'basic_elaboration_smartphone_refresh.svg' => 'icon-basic-elaboration-smartphone-refresh',
	  'basic_elaboration_smartphone_search.svg' => 'icon-basic-elaboration-smartphone-search',
	  'basic_elaboration_tablet_cloud.svg' => 'icon-basic-elaboration-tablet-cloud',
	  'basic_elaboration_tablet_heart.svg' => 'icon-basic-elaboration-tablet-heart',
	  'basic_elaboration_tablet_noaccess.svg' => 'icon-basic-elaboration-tablet-noaccess',
	  'basic_elaboration_tablet_note.svg' => 'icon-basic-elaboration-tablet-note',
	  'basic_elaboration_tablet_pencil.svg' => 'icon-basic-elaboration-tablet-pencil',
	  'basic_elaboration_tablet_picture.svg' => 'icon-basic-elaboration-tablet-picture',
	  'basic_elaboration_tablet_refresh.svg' => 'icon-basic-elaboration-tablet-refresh',
	  'basic_elaboration_tablet_search.svg' => 'icon-basic-elaboration-tablet-search',
	  'basic_elaboration_todolist_2.svg' => 'icon-basic-elaboration-todolist-2',
	  'basic_elaboration_todolist_check.svg' => 'icon-basic-elaboration-todolist-check',
	  'basic_elaboration_todolist_cloud.svg' => 'icon-basic-elaboration-todolist-cloud',
	  'basic_elaboration_todolist_download.svg' => 'icon-basic-elaboration-todolist-download',
	  'basic_elaboration_todolist_flagged.svg' => 'icon-basic-elaboration-todolist-flagged',
	  'basic_elaboration_todolist_minus.svg' => 'icon-basic-elaboration-todolist-minus',
	  'basic_elaboration_todolist_noaccess.svg' => 'icon-basic-elaboration-todolist-noaccess',
	  'basic_elaboration_todolist_pencil.svg' => 'icon-basic-elaboration-todolist-pencil',
	  'basic_elaboration_todolist_plus.svg' => 'icon-basic-elaboration-todolist-plus',
	  'basic_elaboration_todolist_refresh.svg' => 'icon-basic-elaboration-todolist-refresh',
	  'basic_elaboration_todolist_remove.svg' => 'icon-basic-elaboration-todolist-remove',
	  'basic_elaboration_todolist_search.svg' => 'icon-basic-elaboration-todolist-search',
	  'basic_elaboration_todolist_star.svg' => 'icon-basic-elaboration-todolist-star',
	  'basic_elaboration_todolist_upload.svg' => 'icon-basic-elaboration-todolist-upload',
	  'basic_exclamation.svg' => 'icon-basic-exclamation',
	  'basic_eye.svg' => 'icon-basic-eye',
	  'basic_eye_closed.svg' => 'icon-basic-eye-closed',
	  'basic_female.svg' => 'icon-basic-female',
	  'basic_flag1.svg' => 'icon-basic-flag1',
	  'basic_flag2.svg' => 'icon-basic-flag2',
	  'basic_floppydisk.svg' => 'icon-basic-floppydisk',
	  'basic_folder.svg' => 'icon-basic-folder',
	  'basic_folder_multiple.svg' => 'icon-basic-folder-multiple',
	  'basic_gear.svg' => 'icon-basic-gear',
	  'basic_geolocalize-01.svg' => 'icon-basic-geolocalize-01',
	  'basic_geolocalize-05.svg' => 'icon-basic-geolocalize-05',
	  'basic_globe.svg' => 'icon-basic-globe',
	  'basic_gunsight.svg' => 'icon-basic-gunsight',
	  'basic_hammer.svg' => 'icon-basic-hammer',
	  'basic_headset.svg' => 'icon-basic-headset',
	  'basic_heart.svg' => 'icon-basic-heart',
	  'basic_heart_broken.svg' => 'icon-basic-heart-broken',
	  'basic_helm.svg' => 'icon-basic-helm',
	  'basic_home.svg' => 'icon-basic-home',
	  'basic_info.svg' => 'icon-basic-info',
	  'basic_ipod.svg' => 'icon-basic-ipod',
	  'basic_joypad.svg' => 'icon-basic-joypad',
	  'basic_key.svg' => 'icon-basic-key',
	  'basic_keyboard.svg' => 'icon-basic-keyboard',
	  'basic_laptop.svg' => 'icon-basic-laptop',
	  'basic_life_buoy.svg' => 'icon-basic-life-buoy',
	  'basic_lightbulb.svg' => 'icon-basic-lightbulb',
	  'basic_link.svg' => 'icon-basic-link',
	  'basic_lock.svg' => 'icon-basic-lock',
	  'basic_lock_open.svg' => 'icon-basic-lock-open',
	  'basic_magic_mouse.svg' => 'icon-basic-magic-mouse',
	  'basic_magnifier.svg' => 'icon-basic-magnifier',
	  'basic_magnifier_minus.svg' => 'icon-basic-magnifier-minus',
	  'basic_magnifier_plus.svg' => 'icon-basic-magnifier-plus',
	  'basic_mail.svg' => 'icon-basic-mail',
	  'basic_mail_multiple.svg' => 'icon-basic-mail-multiple',
	  'basic_mail_open.svg' => 'icon-basic-mail-open',
	  'basic_mail_open_text.svg' => 'icon-basic-mail-open-text',
	  'basic_male.svg' => 'icon-basic-male',
	  'basic_map.svg' => 'icon-basic-map',
	  'basic_message.svg' => 'icon-basic-message',
	  'basic_message_multiple.svg' => 'icon-basic-message-multiple',
	  'basic_message_txt.svg' => 'icon-basic-message-txt',
	  'basic_mixer2.svg' => 'icon-basic-mixer2',
	  'basic_mouse.svg' => 'icon-basic-mouse',
	  'basic_notebook.svg' => 'icon-basic-notebook',
	  'basic_notebook_pen.svg' => 'icon-basic-notebook-pen',
	  'basic_notebook_pencil.svg' => 'icon-basic-notebook-pencil',
	  'basic_paperplane.svg' => 'icon-basic-paperplane',
	  'basic_pencil_ruler.svg' => 'icon-basic-pencil-ruler',
	  'basic_pencil_ruler_pen .svg' => 'icon-basic-pencil-ruler-pen',
	  'basic_photo.svg' => 'icon-basic-photo',
	  'basic_picture.svg' => 'icon-basic-picture',
	  'basic_picture_multiple.svg' => 'icon-basic-picture-multiple',
	  'basic_pin1.svg' => 'icon-basic-pin1',
	  'basic_pin2.svg' => 'icon-basic-pin2',
	  'basic_postcard.svg' => 'icon-basic-postcard',
	  'basic_postcard_multiple.svg' => 'icon-basic-postcard-multiple',
	  'basic_printer.svg' => 'icon-basic-printer',
	  'basic_question.svg' => 'icon-basic-question',
	  'basic_rss.svg' => 'icon-basic-rss',
	  'basic_server.svg' => 'icon-basic-server',
	  'basic_server2.svg' => 'icon-basic-server2',
	  'basic_server_cloud.svg' => 'icon-basic-server-cloud',
	  'basic_server_download.svg' => 'icon-basic-server-download',
	  'basic_server_upload.svg' => 'icon-basic-server-upload',
	  'basic_settings.svg' => 'icon-basic-settings',
	  'basic_share.svg' => 'icon-basic-share',
	  'basic_sheet.svg' => 'icon-basic-sheet',
	  'basic_sheet_multiple .svg' => 'icon-basic-sheet-multiple',
	  'basic_sheet_pen.svg' => 'icon-basic-sheet-pen',
	  'basic_sheet_pencil.svg' => 'icon-basic-sheet-pencil',
	  'basic_sheet_txt .svg' => 'icon-basic-sheet-txt',
	  'basic_signs.svg' => 'icon-basic-signs',
	  'basic_smartphone.svg' => 'icon-basic-smartphone',
	  'basic_spades.svg' => 'icon-basic-spades',
	  'basic_spread.svg' => 'icon-basic-spread',
	  'basic_spread_bookmark.svg' => 'icon-basic-spread-bookmark',
	  'basic_spread_text.svg' => 'icon-basic-spread-text',
	  'basic_spread_text_bookmark.svg' => 'icon-basic-spread-text-bookmark',
	  'basic_star.svg' => 'icon-basic-star',
	  'basic_tablet.svg' => 'icon-basic-tablet',
	  'basic_target.svg' => 'icon-basic-target',
	  'basic_todo.svg' => 'icon-basic-todo',
	  'basic_todolist_pen.svg' => 'icon-basic-todo-pen',
	  'basic_todolist_pencil.svg' => 'icon-basic-todo-pencil',
	  'basic_todo_pen .svg' => 'icon-basic-todo-txt',
	  'basic_todo_pencil.svg' => 'icon-basic-todolist-pen',
	  'basic_todo_txt.svg' => 'icon-basic-todolist-pencil',
	  'basic_trashcan.svg' => 'icon-basic-trashcan',
	  'basic_trashcan_full.svg' => 'icon-basic-trashcan-full',
	  'basic_trashcan_refresh.svg' => 'icon-basic-trashcan-refresh',
	  'basic_trashcan_remove.svg' => 'icon-basic-trashcan-remove',
	  'basic_upload.svg' => 'icon-basic-upload',
	  'basic_usb.svg' => 'icon-basic-usb',
	  'basic_video.svg' => 'icon-basic-video',
	  'basic_watch.svg' => 'icon-basic-watch',
	  'basic_webpage.svg' => 'icon-basic-webpage',
	  'basic_webpage_img_txt.svg' => 'icon-basic-webpage-img-txt',
	  'basic_webpage_multiple.svg' => 'icon-basic-webpage-multiple',
	  'basic_webpage_txt.svg' => 'icon-basic-webpage-txt',
	  'basic_world.svg' => 'icon-basic-world',
	  'ecommerce_bag.svg' => 'icon-ecommerce-bag',
	  'ecommerce_bag_check.svg' => 'icon-ecommerce-bag-check',
	  'ecommerce_bag_cloud.svg' => 'icon-ecommerce-bag-cloud',
	  'ecommerce_bag_download.svg' => 'icon-ecommerce-bag-download',
	  'ecommerce_bag_minus.svg' => 'icon-ecommerce-bag-minus',
	  'ecommerce_bag_plus.svg' => 'icon-ecommerce-bag-plus',
	  'ecommerce_bag_refresh.svg' => 'icon-ecommerce-bag-refresh',
	  'ecommerce_bag_remove.svg' => 'icon-ecommerce-bag-remove',
	  'ecommerce_bag_search.svg' => 'icon-ecommerce-bag-search',
	  'ecommerce_bag_upload.svg' => 'icon-ecommerce-bag-upload',
	  'ecommerce_banknote.svg' => 'icon-ecommerce-banknote',
	  'ecommerce_banknotes.svg' => 'icon-ecommerce-banknotes',
	  'ecommerce_basket.svg' => 'icon-ecommerce-basket',
	  'ecommerce_basket_check.svg' => 'icon-ecommerce-basket-check',
	  'ecommerce_basket_cloud.svg' => 'icon-ecommerce-basket-cloud',
	  'ecommerce_basket_download.svg' => 'icon-ecommerce-basket-download',
	  'ecommerce_basket_minus.svg' => 'icon-ecommerce-basket-minus',
	  'ecommerce_basket_plus.svg' => 'icon-ecommerce-basket-plus',
	  'ecommerce_basket_refresh.svg' => 'icon-ecommerce-basket-refresh',
	  'ecommerce_basket_remove.svg' => 'icon-ecommerce-basket-remove',
	  'ecommerce_basket_search.svg' => 'icon-ecommerce-basket-search',
	  'ecommerce_basket_upload.svg' => 'icon-ecommerce-basket-upload',
	  'ecommerce_bath.svg' => 'icon-ecommerce-bath',
	  'ecommerce_cart.svg' => 'icon-ecommerce-cart',
	  'ecommerce_cart_check.svg' => 'icon-ecommerce-cart-check',
	  'ecommerce_cart_cloud.svg' => 'icon-ecommerce-cart-cloud',
	  'ecommerce_cart_content.svg' => 'icon-ecommerce-cart-content',
	  'ecommerce_cart_download.svg' => 'icon-ecommerce-cart-download',
	  'ecommerce_cart_minus.svg' => 'icon-ecommerce-cart-minus',
	  'ecommerce_cart_plus.svg' => 'icon-ecommerce-cart-plus',
	  'ecommerce_cart_refresh.svg' => 'icon-ecommerce-cart-refresh',
	  'ecommerce_cart_remove.svg' => 'icon-ecommerce-cart-remove',
	  'ecommerce_cart_search.svg' => 'icon-ecommerce-cart-search',
	  'ecommerce_cart_upload.svg' => 'icon-ecommerce-cart-upload',
	  'ecommerce_cent.svg' => 'icon-ecommerce-cent',
	  'ecommerce_colon.svg' => 'icon-ecommerce-colon',
	  'ecommerce_creditcard.svg' => 'icon-ecommerce-creditcard',
	  'ecommerce_diamond.svg' => 'icon-ecommerce-diamond',
	  'ecommerce_dollar.svg' => 'icon-ecommerce-dollar',
	  'ecommerce_euro.svg' => 'icon-ecommerce-euro',
	  'ecommerce_franc.svg' => 'icon-ecommerce-franc',
	  'ecommerce_gift.svg' => 'icon-ecommerce-gift',
	  'ecommerce_graph1.svg' => 'icon-ecommerce-graph1',
	  'ecommerce_graph2.svg' => 'icon-ecommerce-graph2',
	  'ecommerce_graph3.svg' => 'icon-ecommerce-graph3',
	  'ecommerce_graph_decrease.svg' => 'icon-ecommerce-graph-decrease',
	  'ecommerce_graph_increase.svg' => 'icon-ecommerce-graph-increase',
	  'ecommerce_guarani.svg' => 'icon-ecommerce-guarani',
	  'ecommerce_kips.svg' => 'icon-ecommerce-kips',
	  'ecommerce_lira.svg' => 'icon-ecommerce-lira',
	  'ecommerce_megaphone.svg' => 'icon-ecommerce-megaphone',
	  'ecommerce_money.svg' => 'icon-ecommerce-money',
	  'ecommerce_naira.svg' => 'icon-ecommerce-naira',
	  'ecommerce_pesos.svg' => 'icon-ecommerce-pesos',
	  'ecommerce_pound.svg' => 'icon-ecommerce-pound',
	  'ecommerce_receipt.svg' => 'icon-ecommerce-receipt',
	  'ecommerce_receipt_bath.svg' => 'icon-ecommerce-receipt-bath',
	  'ecommerce_receipt_cent.svg' => 'icon-ecommerce-receipt-cent',
	  'ecommerce_receipt_dollar.svg' => 'icon-ecommerce-receipt-dollar',
	  'ecommerce_receipt_euro.svg' => 'icon-ecommerce-receipt-euro',
	  'ecommerce_receipt_franc.svg' => 'icon-ecommerce-receipt-franc',
	  'ecommerce_receipt_guarani.svg' => 'icon-ecommerce-receipt-guarani',
	  'ecommerce_receipt_kips.svg' => 'icon-ecommerce-receipt-kips',
	  'ecommerce_receipt_lira.svg' => 'icon-ecommerce-receipt-lira',
	  'ecommerce_receipt_naira.svg' => 'icon-ecommerce-receipt-naira',
	  'ecommerce_receipt_pesos.svg' => 'icon-ecommerce-receipt-pesos',
	  'ecommerce_receipt_pound.svg' => 'icon-ecommerce-receipt-pound',
	  'ecommerce_receipt_rublo.svg' => 'icon-ecommerce-receipt-rublo',
	  'ecommerce_receipt_rupee.svg' => 'icon-ecommerce-receipt-rupee',
	  'ecommerce_receipt_tugrik.svg' => 'icon-ecommerce-receipt-tugrik',
	  'ecommerce_receipt_won.svg' => 'icon-ecommerce-receipt-won',
	  'ecommerce_receipt_yen.svg' => 'icon-ecommerce-receipt-yen',
	  'ecommerce_receipt_yen2.svg' => 'icon-ecommerce-receipt-yen2',
	  'ecommerce_recept_colon.svg' => 'icon-ecommerce-recept-colon',
	  'ecommerce_rublo.svg' => 'icon-ecommerce-rublo',
	  'ecommerce_rupee.svg' => 'icon-ecommerce-rupee',
	  'ecommerce_safe.svg' => 'icon-ecommerce-safe',
	  'ecommerce_sale.svg' => 'icon-ecommerce-sale',
	  'ecommerce_sales.svg' => 'icon-ecommerce-sales',
	  'ecommerce_ticket.svg' => 'icon-ecommerce-ticket',
	  'ecommerce_tugriks.svg' => 'icon-ecommerce-tugriks',
	  'ecommerce_wallet.svg' => 'icon-ecommerce-wallet',
	  'ecommerce_won.svg' => 'icon-ecommerce-won',
	  'ecommerce_yen.svg' => 'icon-ecommerce-yen',
	  'ecommerce_yen2.svg' => 'icon-ecommerce-yen2',
	  'music_beginning_button.svg' => 'icon-music-beginning-button',
	  'music_bell.svg' => 'icon-music-bell',
	  'music_cd.svg' => 'icon-music-cd',
	  'music_diapason.svg' => 'icon-music-diapason',
	  'music_eject_button.svg' => 'icon-music-eject-button',
	  'music_end_button.svg' => 'icon-music-end-button',
	  'music_fastforward_button.svg' => 'icon-music-fastforward-button',
	  'music_headphones.svg' => 'icon-music-headphones',
	  'music_ipod.svg' => 'icon-music-ipod',
	  'music_loudspeaker.svg' => 'icon-music-loudspeaker',
	  'music_microphone.svg' => 'icon-music-microphone',
	  'music_microphone_old.svg' => 'icon-music-microphone-old',
	  'music_mixer.svg' => 'icon-music-mixer',
	  'music_mute.svg' => 'icon-music-mute',
	  'music_note_multiple.svg' => 'icon-music-note-multiple',
	  'music_note_single.svg' => 'icon-music-note-single',
	  'music_pause_button.svg' => 'icon-music-pause-button',
	  'music_playlist.svg' => 'icon-music-play-button',
	  'music_play_button.svg' => 'icon-music-playlist',
	  'music_radio_ghettoblaster.svg' => 'icon-music-radio-ghettoblaster',
	  'music_radio_portable.svg' => 'icon-music-radio-portable',
	  'music_record.svg' => 'icon-music-record',
	  'music_recordplayer.svg' => 'icon-music-recordplayer',
	  'music_repeat_button.svg' => 'icon-music-repeat-button',
	  'music_rewind_button.svg' => 'icon-music-rewind-button',
	  'music_shuffle_button.svg' => 'icon-music-shuffle-button',
	  'music_stop_button.svg' => 'icon-music-stop-button',
	  'music_tape.svg' => 'icon-music-tape',
	  'music_volume_down.svg' => 'icon-music-volume-down',
	  'music_volume_up.svg' => 'icon-music-volume-up',
	  'software_add_vectorpoint.svg' => 'icon-software-add-vectorpoint',
	  'software_box_oval.svg' => 'icon-software-box-oval',
	  'software_box_polygon.svg' => 'icon-software-box-polygon',
	  'software_box_rectangle.svg' => 'icon-software-box-rectangle',
	  'software_box_roundedrectangle.svg' => 'icon-software-box-roundedrectangle',
	  'software_character.svg' => 'icon-software-character',
	  'software_crop.svg' => 'icon-software-crop',
	  'software_eyedropper.svg' => 'icon-software-eyedropper',
	  'software_font_allcaps.svg' => 'icon-software-font-allcaps',
	  'software_font_baseline_shift.svg' => 'icon-software-font-baseline-shift',
	  'software_font_horizontal_scale.svg' => 'icon-software-font-horizontal-scale',
	  'software_font_kerning.svg' => 'icon-software-font-kerning',
	  'software_font_leading.svg' => 'icon-software-font-leading',
	  'software_font_size.svg' => 'icon-software-font-size',
	  'software_font_smallcapital.svg' => 'icon-software-font-smallcapital',
	  'software_font_smallcaps.svg' => 'icon-software-font-smallcaps',
	  'software_font_strikethrough.svg' => 'icon-software-font-strikethrough',
	  'software_font_tracking.svg' => 'icon-software-font-tracking',
	  'software_font_underline.svg' => 'icon-software-font-underline',
	  'software_font_vertical_scale.svg' => 'icon-software-font-vertical-scale',
	  'software_horizontal_align_center.svg' => 'icon-software-horizontal-align-center',
	  'software_horizontal_align_left.svg' => 'icon-software-horizontal-align-left',
	  'software_horizontal_align_right.svg' => 'icon-software-horizontal-align-right',
	  'software_horizontal_distribute_center.svg' => 'icon-software-horizontal-distribute-center',
	  'software_horizontal_distribute_left.svg' => 'icon-software-horizontal-distribute-left',
	  'software_horizontal_distribute_right.svg' => 'icon-software-horizontal-distribute-right',
	  'software_indent_firstline.svg' => 'icon-software-indent-firstline',
	  'software_indent_left.svg' => 'icon-software-indent-left',
	  'software_indent_right.svg' => 'icon-software-indent-right',
	  'software_lasso.svg' => 'icon-software-lasso',
	  'software_layers1.svg' => 'icon-software-layers1',
	  'software_layers2.svg' => 'icon-software-layers2',
	  'software_layout-8boxes.svg' => 'icon-software-layout',
	  'software_layout.svg' => 'icon-software-layout-2columns',
	  'software_layout_2columns.svg' => 'icon-software-layout-3columns',
	  'software_layout_3columns.svg' => 'icon-software-layout-4boxes',
	  'software_layout_4boxes.svg' => 'icon-software-layout-4columns',
	  'software_layout_4columns.svg' => 'icon-software-layout-4lines',
	  'software_layout_4lines.svg' => 'icon-software-layout-8boxes',
	  'software_layout_header.svg' => 'icon-software-layout-header',
	  'software_layout_header_2columns.svg' => 'icon-software-layout-header-2columns',
	  'software_layout_header_3columns.svg' => 'icon-software-layout-header-3columns',
	  'software_layout_header_4boxes.svg' => 'icon-software-layout-header-4boxes',
	  'software_layout_header_4columns.svg' => 'icon-software-layout-header-4columns',
	  'software_layout_header_complex.svg' => 'icon-software-layout-header-complex',
	  'software_layout_header_complex2.svg' => 'icon-software-layout-header-complex2',
	  'software_layout_header_complex3.svg' => 'icon-software-layout-header-complex3',
	  'software_layout_header_complex4.svg' => 'icon-software-layout-header-complex4',
	  'software_layout_header_sideleft.svg' => 'icon-software-layout-header-sideleft',
	  'software_layout_header_sideright.svg' => 'icon-software-layout-header-sideright',
	  'software_layout_sidebar_left.svg' => 'icon-software-layout-sidebar-left',
	  'software_layout_sidebar_right.svg' => 'icon-software-layout-sidebar-right',
	  'software_magnete.svg' => 'icon-software-magnete',
	  'software_pages.svg' => 'icon-software-pages',
	  'software_paintbrush.svg' => 'icon-software-paintbrush',
	  'software_paintbucket.svg' => 'icon-software-paintbucket',
	  'software_paintroller.svg' => 'icon-software-paintroller',
	  'software_paragraph.svg' => 'icon-software-paragraph',
	  'software_paragraph_align_left.svg' => 'icon-software-paragraph-align-left',
	  'software_paragraph_align_right.svg' => 'icon-software-paragraph-align-right',
	  'software_paragraph_center.svg' => 'icon-software-paragraph-center',
	  'software_paragraph_justify_all.svg' => 'icon-software-paragraph-justify-all',
	  'software_paragraph_justify_center.svg' => 'icon-software-paragraph-justify-center',
	  'software_paragraph_justify_left.svg' => 'icon-software-paragraph-justify-left',
	  'software_paragraph_justify_right.svg' => 'icon-software-paragraph-justify-right',
	  'software_paragraph_space_after.svg' => 'icon-software-paragraph-space-after',
	  'software_paragraph_space_before.svg' => 'icon-software-paragraph-space-before',
	  'software_pathfinder_exclude.svg' => 'icon-software-pathfinder-exclude',
	  'software_pathfinder_intersect.svg' => 'icon-software-pathfinder-intersect',
	  'software_pathfinder_subtract.svg' => 'icon-software-pathfinder-subtract',
	  'software_pathfinder_unite.svg' => 'icon-software-pathfinder-unite',
	  'software_pen.svg' => 'icon-software-pen',
	  'software_pencil.svg' => 'icon-software-pen-add',
	  'software_pen_add.svg' => 'icon-software-pen-remove',
	  'software_pen_remove.svg' => 'icon-software-pencil',
	  'software_polygonallasso.svg' => 'icon-software-polygonallasso',
	  'software_reflect_horizontal.svg' => 'icon-software-reflect-horizontal',
	  'software_reflect_vertical.svg' => 'icon-software-reflect-vertical',
	  'software_remove_vectorpoint.svg' => 'icon-software-remove-vectorpoint',
	  'software_scale_expand.svg' => 'icon-software-scale-expand',
	  'software_scale_reduce.svg' => 'icon-software-scale-reduce',
	  'software_selection_oval.svg' => 'icon-software-selection-oval',
	  'software_selection_polygon.svg' => 'icon-software-selection-polygon',
	  'software_selection_rectangle.svg' => 'icon-software-selection-rectangle',
	  'software_selection_roundedrectangle.svg' => 'icon-software-selection-roundedrectangle',
	  'software_shape_oval.svg' => 'icon-software-shape-oval',
	  'software_shape_polygon.svg' => 'icon-software-shape-polygon',
	  'software_shape_rectangle.svg' => 'icon-software-shape-rectangle',
	  'software_shape_roundedrectangle.svg' => 'icon-software-shape-roundedrectangle',
	  'software_slice.svg' => 'icon-software-slice',
	  'software_transform_bezier.svg' => 'icon-software-transform-bezier',
	  'software_vector_box.svg' => 'icon-software-vector-box',
	  'software_vector_composite.svg' => 'icon-software-vector-composite',
	  'software_vector_line.svg' => 'icon-software-vector-line',
	  'software_vertical_align_bottom.svg' => 'icon-software-vertical-align-bottom',
	  'software_vertical_align_center.svg' => 'icon-software-vertical-align-center',
	  'software_vertical_align_top.svg' => 'icon-software-vertical-align-top',
	  'software_vertical_distribute_bottom.svg' => 'icon-software-vertical-distribute-bottom',
	  'software_vertical_distribute_center.svg' => 'icon-software-vertical-distribute-center',
	  'software_vertical_distribute_top.svg' => 'icon-software-vertical-distribute-top',
	  'weather_aquarius.svg' => 'icon-weather-aquarius',
	  'weather_aries.svg' => 'icon-weather-aries',
	  'weather_cancer.svg' => 'icon-weather-cancer',
	  'weather_capricorn.svg' => 'icon-weather-capricorn',
	  'weather_cloud.svg' => 'icon-weather-cloud',
	  'weather_cloud_drop.svg' => 'icon-weather-cloud-drop',
	  'weather_cloud_lightning.svg' => 'icon-weather-cloud-lightning',
	  'weather_cloud_snowflake.svg' => 'icon-weather-cloud-snowflake',
	  'weather_downpour_fullmoon.svg' => 'icon-weather-downpour-fullmoon',
	  'weather_downpour_halfmoon.svg' => 'icon-weather-downpour-halfmoon',
	  'weather_downpour_sun.svg' => 'icon-weather-downpour-sun',
	  'weather_drop.svg' => 'icon-weather-drop',
	  'weather_first_quarter .svg' => 'icon-weather-first-quarter',
	  'weather_fog.svg' => 'icon-weather-fog',
	  'weather_fog_fullmoon.svg' => 'icon-weather-fog-fullmoon',
	  'weather_fog_halfmoon.svg' => 'icon-weather-fog-halfmoon',
	  'weather_fog_sun.svg' => 'icon-weather-fog-sun',
	  'weather_fullmoon.svg' => 'icon-weather-fullmoon',
	  'weather_gemini.svg' => 'icon-weather-gemini',
	  'weather_hail.svg' => 'icon-weather-hail',
	  'weather_hail_fullmoon.svg' => 'icon-weather-hail-fullmoon',
	  'weather_hail_halfmoon.svg' => 'icon-weather-hail-halfmoon',
	  'weather_hail_sun.svg' => 'icon-weather-hail-sun',
	  'weather_last_quarter.svg' => 'icon-weather-last-quarter',
	  'weather_leo.svg' => 'icon-weather-leo',
	  'weather_libra.svg' => 'icon-weather-libra',
	  'weather_lightning.svg' => 'icon-weather-lightning',
	  'weather_mistyrain.svg' => 'icon-weather-mistyrain',
	  'weather_mistyrain_fullmoon.svg' => 'icon-weather-mistyrain-fullmoon',
	  'weather_mistyrain_halfmoon.svg' => 'icon-weather-mistyrain-halfmoon',
	  'weather_mistyrain_sun.svg' => 'icon-weather-mistyrain-sun',
	  'weather_moon.svg' => 'icon-weather-moon',
	  'weather_moondown_full.svg' => 'icon-weather-moondown-full',
	  'weather_moondown_half.svg' => 'icon-weather-moondown-half',
	  'weather_moonset_full.svg' => 'icon-weather-moonset-full',
	  'weather_moonset_half.svg' => 'icon-weather-moonset-half',
	  'weather_move2.svg' => 'icon-weather-move2',
	  'weather_newmoon.svg' => 'icon-weather-newmoon',
	  'weather_pisces.svg' => 'icon-weather-pisces',
	  'weather_rain.svg' => 'icon-weather-rain',
	  'weather_rain_fullmoon.svg' => 'icon-weather-rain-fullmoon',
	  'weather_rain_halfmoon.svg' => 'icon-weather-rain-halfmoon',
	  'weather_rain_sun.svg' => 'icon-weather-rain-sun',
	  'weather_sagittarius.svg' => 'icon-weather-sagittarius',
	  'weather_scorpio.svg' => 'icon-weather-scorpio',
	  'weather_snow.svg' => 'icon-weather-snow',
	  'weather_snowflake.svg' => 'icon-weather-snowflake',
	  'weather_snow_fullmoon.svg' => 'icon-weather-snow-fullmoon',
	  'weather_snow_halfmoon.svg' => 'icon-weather-snow-halfmoon',
	  'weather_snow_sun.svg' => 'icon-weather-snow-sun',
	  'weather_star.svg' => 'icon-weather-star',
	  'weather_storm-11.svg' => 'icon-weather-storm-11',
	  'weather_storm-32.svg' => 'icon-weather-storm-32',
	  'weather_storm_fullmoon.svg' => 'icon-weather-storm-fullmoon',
	  'weather_storm_halfmoon.svg' => 'icon-weather-storm-halfmoon',
	  'weather_storm_sun.svg' => 'icon-weather-storm-sun',
	  'weather_sun.svg' => 'icon-weather-sun',
	  'weather_sundown.svg' => 'icon-weather-sundown',
	  'weather_sunset.svg' => 'icon-weather-sunset',
	  'weather_taurus.svg' => 'icon-weather-taurus',
	  'weather_tempest.svg' => 'icon-weather-tempest',
	  'weather_tempest_fullmoon.svg' => 'icon-weather-tempest-fullmoon',
	  'weather_tempest_halfmoon.svg' => 'icon-weather-tempest-halfmoon',
	  'weather_tempest_sun.svg' => 'icon-weather-tempest-sun',
	  'weather_variable_fullmoon.svg' => 'icon-weather-variable-fullmoon',
	  'weather_variable_halfmoon.svg' => 'icon-weather-variable-halfmoon',
	  'weather_variable_sun.svg' => 'icon-weather-variable-sun',
	  'weather_virgo.svg' => 'icon-weather-virgo',
	  'weather_waning_cresent.svg' => 'icon-weather-waning-cresent',
	  'weather_waning_gibbous.svg' => 'icon-weather-waning-gibbous',
	  'weather_waxing_cresent.svg' => 'icon-weather-waxing-cresent',
	  'weather_waxing_gibbous.svg' => 'icon-weather-waxing-gibbous',
	  'weather_wind.svg' => 'icon-weather-wind',
	  'weather_windgust.svg' => 'icon-weather-windgust',
	  'weather_wind_E.svg' => 'icon-weather-wind-e',
	  'weather_wind_fullmoon.svg' => 'icon-weather-wind-fullmoon',
	  'weather_wind_halfmoon.svg' => 'icon-weather-wind-halfmoon',
	  'weather_wind_N.svg' => 'icon-weather-wind-n',
	  'weather_wind_NE.svg' => 'icon-weather-wind-ne',
	  'weather_wind_NW.svg' => 'icon-weather-wind-nw',
	  'weather_wind_S.svg' => 'icon-weather-wind-s',
	  'weather_wind_SE.svg' => 'icon-weather-wind-se',
	  'weather_wind_sun.svg' => 'icon-weather-wind-sun',
	  'weather_wind_SW.svg' => 'icon-weather-wind-sw',
	  'weather_wind_W.svg' => 'icon-weather-wind-w',
	)
);

$fa_icons =  array(
  'fa-glass' => 'fa fa-glass',
  'fa-music' => 'fa fa-music',
  'fa-search' => 'fa fa-search',
  'fa-envelope-o' => 'fa fa-envelope-o',
  'fa-heart' => 'fa fa-heart',
  'fa-star' => 'fa fa-star',
  'fa-star-o' => 'fa fa-star-o',
  'fa-user' => 'fa fa-user',
  'fa-film' => 'fa fa-film',
  'fa-th-large' => 'fa fa-th-large',
  'fa-th' => 'fa fa-th',
  'fa-th-list' => 'fa fa-th-list',
  'fa-check' => 'fa fa-check',
  'fa-times' => 'fa fa-times',
  'fa-search-plus' => 'fa fa-search-plus',
  'fa-search-minus' => 'fa fa-search-minus',
  'fa-power-off' => 'fa fa-power-off',
  'fa-signal' => 'fa fa-signal',
  'fa-cog' => 'fa fa-cog',
  'fa-trash-o' => 'fa fa-trash-o',
  'fa-home' => 'fa fa-home',
  'fa-file-o' => 'fa fa-file-o',
  'fa-clock-o' => 'fa fa-clock-o',
  'fa-road' => 'fa fa-road',
  'fa-download' => 'fa fa-download',
  'fa-arrow-circle-o-down' => 'fa fa-arrow-circle-o-down',
  'fa-arrow-circle-o-up' => 'fa fa-arrow-circle-o-up',
  'fa-inbox' => 'fa fa-inbox',
  'fa-play-circle-o' => 'fa fa-play-circle-o',
  'fa-repeat' => 'fa fa-repeat',
  'fa-refresh' => 'fa fa-refresh',
  'fa-list-alt' => 'fa fa-list-alt',
  'fa-lock' => 'fa fa-lock',
  'fa-flag' => 'fa fa-flag',
  'fa-headphones' => 'fa fa-headphones',
  'fa-volume-off' => 'fa fa-volume-off',
  'fa-volume-down' => 'fa fa-volume-down',
  'fa-volume-up' => 'fa fa-volume-up',
  'fa-qrcode' => 'fa fa-qrcode',
  'fa-barcode' => 'fa fa-barcode',
  'fa-tag' => 'fa fa-tag',
  'fa-tags' => 'fa fa-tags',
  'fa-book' => 'fa fa-book',
  'fa-bookmark' => 'fa fa-bookmark',
  'fa-print' => 'fa fa-print',
  'fa-camera' => 'fa fa-camera',
  'fa-font' => 'fa fa-font',
  'fa-bold' => 'fa fa-bold',
  'fa-italic' => 'fa fa-italic',
  'fa-text-height' => 'fa fa-text-height',
  'fa-text-width' => 'fa fa-text-width',
  'fa-align-left' => 'fa fa-align-left',
  'fa-align-center' => 'fa fa-align-center',
  'fa-align-right' => 'fa fa-align-right',
  'fa-align-justify' => 'fa fa-align-justify',
  'fa-list' => 'fa fa-list',
  'fa-outdent' => 'fa fa-outdent',
  'fa-indent' => 'fa fa-indent',
  'fa-video-camera' => 'fa fa-video-camera',
  'fa-picture-o' => 'fa fa-picture-o',
  'fa-pencil' => 'fa fa-pencil',
  'fa-map-marker' => 'fa fa-map-marker',
  'fa-adjust' => 'fa fa-adjust',
  'fa-tint' => 'fa fa-tint',
  'fa-pencil-square-o' => 'fa fa-pencil-square-o',
  'fa-share-square-o' => 'fa fa-share-square-o',
  'fa-check-square-o' => 'fa fa-check-square-o',
  'fa-arrows' => 'fa fa-arrows',
  'fa-step-backward' => 'fa fa-step-backward',
  'fa-fast-backward' => 'fa fa-fast-backward',
  'fa-backward' => 'fa fa-backward',
  'fa-play' => 'fa fa-play',
  'fa-pause' => 'fa fa-pause',
  'fa-stop' => 'fa fa-stop',
  'fa-forward' => 'fa fa-forward',
  'fa-fast-forward' => 'fa fa-fast-forward',
  'fa-step-forward' => 'fa fa-step-forward',
  'fa-eject' => 'fa fa-eject',
  'fa-chevron-left' => 'fa fa-chevron-left',
  'fa-chevron-right' => 'fa fa-chevron-right',
  'fa-plus-circle' => 'fa fa-plus-circle',
  'fa-minus-circle' => 'fa fa-minus-circle',
  'fa-times-circle' => 'fa fa-times-circle',
  'fa-check-circle' => 'fa fa-check-circle',
  'fa-question-circle' => 'fa fa-question-circle',
  'fa-info-circle' => 'fa fa-info-circle',
  'fa-crosshairs' => 'fa fa-crosshairs',
  'fa-times-circle-o' => 'fa fa-times-circle-o',
  'fa-check-circle-o' => 'fa fa-check-circle-o',
  'fa-ban' => 'fa fa-ban',
  'fa-arrow-left' => 'fa fa-arrow-left',
  'fa-arrow-right' => 'fa fa-arrow-right',
  'fa-arrow-up' => 'fa fa-arrow-up',
  'fa-arrow-down' => 'fa fa-arrow-down',
  'fa-share' => 'fa fa-share',
  'fa-expand' => 'fa fa-expand',
  'fa-compress' => 'fa fa-compress',
  'fa-plus' => 'fa fa-plus',
  'fa-minus' => 'fa fa-minus',
  'fa-asterisk' => 'fa fa-asterisk',
  'fa-exclamation-circle' => 'fa fa-exclamation-circle',
  'fa-gift' => 'fa fa-gift',
  'fa-leaf' => 'fa fa-leaf',
  'fa-fire' => 'fa fa-fire',
  'fa-eye' => 'fa fa-eye',
  'fa-eye-slash' => 'fa fa-eye-slash',
  'fa-exclamation-triangle' => 'fa fa-exclamation-triangle',
  'fa-plane' => 'fa fa-plane',
  'fa-calendar' => 'fa fa-calendar',
  'fa-random' => 'fa fa-random',
  'fa-comment' => 'fa fa-comment',
  'fa-magnet' => 'fa fa-magnet',
  'fa-chevron-up' => 'fa fa-chevron-up',
  'fa-chevron-down' => 'fa fa-chevron-down',
  'fa-retweet' => 'fa fa-retweet',
  'fa-shopping-cart' => 'fa fa-shopping-cart',
  'fa-folder' => 'fa fa-folder',
  'fa-folder-open' => 'fa fa-folder-open',
  'fa-arrows-v' => 'fa fa-arrows-v',
  'fa-arrows-h' => 'fa fa-arrows-h',
  'fa-bar-chart' => 'fa fa-bar-chart',
  'fa-twitter-square' => 'fa fa-twitter-square',
  'fa-facebook-square' => 'fa fa-facebook-square',
  'fa-camera-retro' => 'fa fa-camera-retro',
  'fa-key' => 'fa fa-key',
  'fa-cogs' => 'fa fa-cogs',
  'fa-comments' => 'fa fa-comments',
  'fa-thumbs-o-up' => 'fa fa-thumbs-o-up',
  'fa-thumbs-o-down' => 'fa fa-thumbs-o-down',
  'fa-star-half' => 'fa fa-star-half',
  'fa-heart-o' => 'fa fa-heart-o',
  'fa-sign-out' => 'fa fa-sign-out',
  'fa-linkedin-square' => 'fa fa-linkedin-square',
  'fa-thumb-tack' => 'fa fa-thumb-tack',
  'fa-external-link' => 'fa fa-external-link',
  'fa-sign-in' => 'fa fa-sign-in',
  'fa-trophy' => 'fa fa-trophy',
  'fa-github-square' => 'fa fa-github-square',
  'fa-upload' => 'fa fa-upload',
  'fa-lemon-o' => 'fa fa-lemon-o',
  'fa-phone' => 'fa fa-phone',
  'fa-square-o' => 'fa fa-square-o',
  'fa-bookmark-o' => 'fa fa-bookmark-o',
  'fa-phone-square' => 'fa fa-phone-square',
  'fa-twitter' => 'fa fa-twitter',
  'fa-facebook' => 'fa fa-facebook',
  'fa-github' => 'fa fa-github',
  'fa-unlock' => 'fa fa-unlock',
  'fa-credit-card' => 'fa fa-credit-card',
  'fa-rss' => 'fa fa-rss',
  'fa-hdd-o' => 'fa fa-hdd-o',
  'fa-bullhorn' => 'fa fa-bullhorn',
  'fa-bell' => 'fa fa-bell',
  'fa-certificate' => 'fa fa-certificate',
  'fa-hand-o-right' => 'fa fa-hand-o-right',
  'fa-hand-o-left' => 'fa fa-hand-o-left',
  'fa-hand-o-up' => 'fa fa-hand-o-up',
  'fa-hand-o-down' => 'fa fa-hand-o-down',
  'fa-arrow-circle-left' => 'fa fa-arrow-circle-left',
  'fa-arrow-circle-right' => 'fa fa-arrow-circle-right',
  'fa-arrow-circle-up' => 'fa fa-arrow-circle-up',
  'fa-arrow-circle-down' => 'fa fa-arrow-circle-down',
  'fa-globe' => 'fa fa-globe',
  'fa-wrench' => 'fa fa-wrench',
  'fa-tasks' => 'fa fa-tasks',
  'fa-filter' => 'fa fa-filter',
  'fa-briefcase' => 'fa fa-briefcase',
  'fa-arrows-alt' => 'fa fa-arrows-alt',
  'fa-users' => 'fa fa-users',
  'fa-link' => 'fa fa-link',
  'fa-cloud' => 'fa fa-cloud',
  'fa-flask' => 'fa fa-flask',
  'fa-scissors' => 'fa fa-scissors',
  'fa-files-o' => 'fa fa-files-o',
  'fa-paperclip' => 'fa fa-paperclip',
  'fa-floppy-o' => 'fa fa-floppy-o',
  'fa-square' => 'fa fa-square',
  'fa-bars' => 'fa fa-bars',
  'fa-list-ul' => 'fa fa-list-ul',
  'fa-list-ol' => 'fa fa-list-ol',
  'fa-strikethrough' => 'fa fa-strikethrough',
  'fa-underline' => 'fa fa-underline',
  'fa-table' => 'fa fa-table',
  'fa-magic' => 'fa fa-magic',
  'fa-truck' => 'fa fa-truck',
  'fa-pinterest' => 'fa fa-pinterest',
  'fa-pinterest-square' => 'fa fa-pinterest-square',
  'fa-google-plus-square' => 'fa fa-google-plus-square',
  'fa-google-plus' => 'fa fa-google-plus',
  'fa-money' => 'fa fa-money',
  'fa-caret-down' => 'fa fa-caret-down',
  'fa-caret-up' => 'fa fa-caret-up',
  'fa-caret-left' => 'fa fa-caret-left',
  'fa-caret-right' => 'fa fa-caret-right',
  'fa-columns' => 'fa fa-columns',
  'fa-sort' => 'fa fa-sort',
  'fa-sort-desc' => 'fa fa-sort-desc',
  'fa-sort-asc' => 'fa fa-sort-asc',
  'fa-envelope' => 'fa fa-envelope',
  'fa-linkedin' => 'fa fa-linkedin',
  'fa-undo' => 'fa fa-undo',
  'fa-gavel' => 'fa fa-gavel',
  'fa-tachometer' => 'fa fa-tachometer',
  'fa-comment-o' => 'fa fa-comment-o',
  'fa-comments-o' => 'fa fa-comments-o',
  'fa-bolt' => 'fa fa-bolt',
  'fa-sitemap' => 'fa fa-sitemap',
  'fa-umbrella' => 'fa fa-umbrella',
  'fa-clipboard' => 'fa fa-clipboard',
  'fa-lightbulb-o' => 'fa fa-lightbulb-o',
  'fa-exchange' => 'fa fa-exchange',
  'fa-cloud-download' => 'fa fa-cloud-download',
  'fa-cloud-upload' => 'fa fa-cloud-upload',
  'fa-user-md' => 'fa fa-user-md',
  'fa-stethoscope' => 'fa fa-stethoscope',
  'fa-suitcase' => 'fa fa-suitcase',
  'fa-bell-o' => 'fa fa-bell-o',
  'fa-coffee' => 'fa fa-coffee',
  'fa-cutlery' => 'fa fa-cutlery',
  'fa-file-text-o' => 'fa fa-file-text-o',
  'fa-building-o' => 'fa fa-building-o',
  'fa-hospital-o' => 'fa fa-hospital-o',
  'fa-ambulance' => 'fa fa-ambulance',
  'fa-medkit' => 'fa fa-medkit',
  'fa-fighter-jet' => 'fa fa-fighter-jet',
  'fa-beer' => 'fa fa-beer',
  'fa-h-square' => 'fa fa-h-square',
  'fa-plus-square' => 'fa fa-plus-square',
  'fa-angle-double-left' => 'fa fa-angle-double-left',
  'fa-angle-double-right' => 'fa fa-angle-double-right',
  'fa-angle-double-up' => 'fa fa-angle-double-up',
  'fa-angle-double-down' => 'fa fa-angle-double-down',
  'fa-angle-left' => 'fa fa-angle-left',
  'fa-angle-right' => 'fa fa-angle-right',
  'fa-angle-up' => 'fa fa-angle-up',
  'fa-angle-down' => 'fa fa-angle-down',
  'fa-desktop' => 'fa fa-desktop',
  'fa-laptop' => 'fa fa-laptop',
  'fa-tablet' => 'fa fa-tablet',
  'fa-mobile' => 'fa fa-mobile',
  'fa-circle-o' => 'fa fa-circle-o',
  'fa-quote-left' => 'fa fa-quote-left',
  'fa-quote-right' => 'fa fa-quote-right',
  'fa-spinner' => 'fa fa-spinner',
  'fa-circle' => 'fa fa-circle',
  'fa-reply' => 'fa fa-reply',
  'fa-github-alt' => 'fa fa-github-alt',
  'fa-folder-o' => 'fa fa-folder-o',
  'fa-folder-open-o' => 'fa fa-folder-open-o',
  'fa-smile-o' => 'fa fa-smile-o',
  'fa-frown-o' => 'fa fa-frown-o',
  'fa-meh-o' => 'fa fa-meh-o',
  'fa-gamepad' => 'fa fa-gamepad',
  'fa-keyboard-o' => 'fa fa-keyboard-o',
  'fa-flag-o' => 'fa fa-flag-o',
  'fa-flag-checkered' => 'fa fa-flag-checkered',
  'fa-terminal' => 'fa fa-terminal',
  'fa-code' => 'fa fa-code',
  'fa-reply-all' => 'fa fa-reply-all',
  'fa-star-half-o' => 'fa fa-star-half-o',
  'fa-location-arrow' => 'fa fa-location-arrow',
  'fa-crop' => 'fa fa-crop',
  'fa-code-fork' => 'fa fa-code-fork',
  'fa-chain-broken' => 'fa fa-chain-broken',
  'fa-question' => 'fa fa-question',
  'fa-info' => 'fa fa-info',
  'fa-exclamation' => 'fa fa-exclamation',
  'fa-superscript' => 'fa fa-superscript',
  'fa-subscript' => 'fa fa-subscript',
  'fa-eraser' => 'fa fa-eraser',
  'fa-puzzle-piece' => 'fa fa-puzzle-piece',
  'fa-microphone' => 'fa fa-microphone',
  'fa-microphone-slash' => 'fa fa-microphone-slash',
  'fa-shield' => 'fa fa-shield',
  'fa-calendar-o' => 'fa fa-calendar-o',
  'fa-fire-extinguisher' => 'fa fa-fire-extinguisher',
  'fa-rocket' => 'fa fa-rocket',
  'fa-maxcdn' => 'fa fa-maxcdn',
  'fa-chevron-circle-left' => 'fa fa-chevron-circle-left',
  'fa-chevron-circle-right' => 'fa fa-chevron-circle-right',
  'fa-chevron-circle-up' => 'fa fa-chevron-circle-up',
  'fa-chevron-circle-down' => 'fa fa-chevron-circle-down',
  'fa-html5' => 'fa fa-html5',
  'fa-css3' => 'fa fa-css3',
  'fa-anchor' => 'fa fa-anchor',
  'fa-unlock-alt' => 'fa fa-unlock-alt',
  'fa-bullseye' => 'fa fa-bullseye',
  'fa-ellipsis-h' => 'fa fa-ellipsis-h',
  'fa-ellipsis-v' => 'fa fa-ellipsis-v',
  'fa-rss-square' => 'fa fa-rss-square',
  'fa-play-circle' => 'fa fa-play-circle',
  'fa-ticket' => 'fa fa-ticket',
  'fa-minus-square' => 'fa fa-minus-square',
  'fa-minus-square-o' => 'fa fa-minus-square-o',
  'fa-level-up' => 'fa fa-level-up',
  'fa-level-down' => 'fa fa-level-down',
  'fa-check-square' => 'fa fa-check-square',
  'fa-pencil-square' => 'fa fa-pencil-square',
  'fa-external-link-square' => 'fa fa-external-link-square',
  'fa-share-square' => 'fa fa-share-square',
  'fa-compass' => 'fa fa-compass',
  'fa-caret-square-o-down' => 'fa fa-caret-square-o-down',
  'fa-caret-square-o-up' => 'fa fa-caret-square-o-up',
  'fa-caret-square-o-right' => 'fa fa-caret-square-o-right',
  'fa-eur' => 'fa fa-eur',
  'fa-gbp' => 'fa fa-gbp',
  'fa-usd' => 'fa fa-usd',
  'fa-inr' => 'fa fa-inr',
  'fa-jpy' => 'fa fa-jpy',
  'fa-rub' => 'fa fa-rub',
  'fa-krw' => 'fa fa-krw',
  'fa-btc' => 'fa fa-btc',
  'fa-file' => 'fa fa-file',
  'fa-file-text' => 'fa fa-file-text',
  'fa-sort-alpha-asc' => 'fa fa-sort-alpha-asc',
  'fa-sort-alpha-desc' => 'fa fa-sort-alpha-desc',
  'fa-sort-amount-asc' => 'fa fa-sort-amount-asc',
  'fa-sort-amount-desc' => 'fa fa-sort-amount-desc',
  'fa-sort-numeric-asc' => 'fa fa-sort-numeric-asc',
  'fa-sort-numeric-desc' => 'fa fa-sort-numeric-desc',
  'fa-thumbs-up' => 'fa fa-thumbs-up',
  'fa-thumbs-down' => 'fa fa-thumbs-down',
  'fa-youtube-square' => 'fa fa-youtube-square',
  'fa-youtube' => 'fa fa-youtube',
  'fa-xing' => 'fa fa-xing',
  'fa-xing-square' => 'fa fa-xing-square',
  'fa-youtube-play' => 'fa fa-youtube-play',
  'fa-dropbox' => 'fa fa-dropbox',
  'fa-stack-overflow' => 'fa fa-stack-overflow',
  'fa-instagram' => 'fa fa-instagram',
  'fa-flickr' => 'fa fa-flickr',
  'fa-adn' => 'fa fa-adn',
  'fa-bitbucket' => 'fa fa-bitbucket',
  'fa-bitbucket-square' => 'fa fa-bitbucket-square',
  'fa-tumblr' => 'fa fa-tumblr',
  'fa-tumblr-square' => 'fa fa-tumblr-square',
  'fa-long-arrow-down' => 'fa fa-long-arrow-down',
  'fa-long-arrow-up' => 'fa fa-long-arrow-up',
  'fa-long-arrow-left' => 'fa fa-long-arrow-left',
  'fa-long-arrow-right' => 'fa fa-long-arrow-right',
  'fa-apple' => 'fa fa-apple',
  'fa-windows' => 'fa fa-windows',
  'fa-android' => 'fa fa-android',
  'fa-linux' => 'fa fa-linux',
  'fa-dribbble' => 'fa fa-dribbble',
  'fa-skype' => 'fa fa-skype',
  'fa-foursquare' => 'fa fa-foursquare',
  'fa-trello' => 'fa fa-trello',
  'fa-female' => 'fa fa-female',
  'fa-male' => 'fa fa-male',
  'fa-gratipay' => 'fa fa-gratipay',
  'fa-sun-o' => 'fa fa-sun-o',
  'fa-moon-o' => 'fa fa-moon-o',
  'fa-archive' => 'fa fa-archive',
  'fa-bug' => 'fa fa-bug',
  'fa-vk' => 'fa fa-vk',
  'fa-weibo' => 'fa fa-weibo',
  'fa-renren' => 'fa fa-renren',
  'fa-pagelines' => 'fa fa-pagelines',
  'fa-stack-exchange' => 'fa fa-stack-exchange',
  'fa-arrow-circle-o-right' => 'fa fa-arrow-circle-o-right',
  'fa-arrow-circle-o-left' => 'fa fa-arrow-circle-o-left',
  'fa-caret-square-o-left' => 'fa fa-caret-square-o-left',
  'fa-dot-circle-o' => 'fa fa-dot-circle-o',
  'fa-wheelchair' => 'fa fa-wheelchair',
  'fa-vimeo-square' => 'fa fa-vimeo-square',
  'fa-try' => 'fa fa-try',
  'fa-plus-square-o' => 'fa fa-plus-square-o',
  'fa-space-shuttle' => 'fa fa-space-shuttle',
  'fa-slack' => 'fa fa-slack',
  'fa-envelope-square' => 'fa fa-envelope-square',
  'fa-wordpress' => 'fa fa-wordpress',
  'fa-openid' => 'fa fa-openid',
  'fa-university' => 'fa fa-university',
  'fa-graduation-cap' => 'fa fa-graduation-cap',
  'fa-yahoo' => 'fa fa-yahoo',
  'fa-google' => 'fa fa-google',
  'fa-reddit' => 'fa fa-reddit',
  'fa-reddit-square' => 'fa fa-reddit-square',
  'fa-stumbleupon-circle' => 'fa fa-stumbleupon-circle',
  'fa-stumbleupon' => 'fa fa-stumbleupon',
  'fa-delicious' => 'fa fa-delicious',
  'fa-digg' => 'fa fa-digg',
  'fa-pied-piper-pp' => 'fa fa-pied-piper-pp',
  'fa-pied-piper-alt' => 'fa fa-pied-piper-alt',
  'fa-drupal' => 'fa fa-drupal',
  'fa-joomla' => 'fa fa-joomla',
  'fa-language' => 'fa fa-language',
  'fa-fax' => 'fa fa-fax',
  'fa-building' => 'fa fa-building',
  'fa-child' => 'fa fa-child',
  'fa-paw' => 'fa fa-paw',
  'fa-spoon' => 'fa fa-spoon',
  'fa-cube' => 'fa fa-cube',
  'fa-cubes' => 'fa fa-cubes',
  'fa-behance' => 'fa fa-behance',
  'fa-behance-square' => 'fa fa-behance-square',
  'fa-steam' => 'fa fa-steam',
  'fa-steam-square' => 'fa fa-steam-square',
  'fa-recycle' => 'fa fa-recycle',
  'fa-car' => 'fa fa-car',
  'fa-taxi' => 'fa fa-taxi',
  'fa-tree' => 'fa fa-tree',
  'fa-spotify' => 'fa fa-spotify',
  'fa-deviantart' => 'fa fa-deviantart',
  'fa-soundcloud' => 'fa fa-soundcloud',
  'fa-database' => 'fa fa-database',
  'fa-file-pdf-o' => 'fa fa-file-pdf-o',
  'fa-file-word-o' => 'fa fa-file-word-o',
  'fa-file-excel-o' => 'fa fa-file-excel-o',
  'fa-file-powerpoint-o' => 'fa fa-file-powerpoint-o',
  'fa-file-image-o' => 'fa fa-file-image-o',
  'fa-file-archive-o' => 'fa fa-file-archive-o',
  'fa-file-audio-o' => 'fa fa-file-audio-o',
  'fa-file-video-o' => 'fa fa-file-video-o',
  'fa-file-code-o' => 'fa fa-file-code-o',
  'fa-vine' => 'fa fa-vine',
  'fa-codepen' => 'fa fa-codepen',
  'fa-jsfiddle' => 'fa fa-jsfiddle',
  'fa-life-ring' => 'fa fa-life-ring',
  'fa-circle-o-notch' => 'fa fa-circle-o-notch',
  'fa-rebel' => 'fa fa-rebel',
  'fa-empire' => 'fa fa-empire',
  'fa-git-square' => 'fa fa-git-square',
  'fa-git' => 'fa fa-git',
  'fa-hacker-news' => 'fa fa-hacker-news',
  'fa-tencent-weibo' => 'fa fa-tencent-weibo',
  'fa-qq' => 'fa fa-qq',
  'fa-weixin' => 'fa fa-weixin',
  'fa-paper-plane' => 'fa fa-paper-plane',
  'fa-paper-plane-o' => 'fa fa-paper-plane-o',
  'fa-history' => 'fa fa-history',
  'fa-circle-thin' => 'fa fa-circle-thin',
  'fa-header' => 'fa fa-header',
  'fa-paragraph' => 'fa fa-paragraph',
  'fa-sliders' => 'fa fa-sliders',
  'fa-share-alt' => 'fa fa-share-alt',
  'fa-share-alt-square' => 'fa fa-share-alt-square',
  'fa-bomb' => 'fa fa-bomb',
  'fa-futbol-o' => 'fa fa-futbol-o',
  'fa-tty' => 'fa fa-tty',
  'fa-binoculars' => 'fa fa-binoculars',
  'fa-plug' => 'fa fa-plug',
  'fa-slideshare' => 'fa fa-slideshare',
  'fa-twitch' => 'fa fa-twitch',
  'fa-yelp' => 'fa fa-yelp',
  'fa-newspaper-o' => 'fa fa-newspaper-o',
  'fa-wifi' => 'fa fa-wifi',
  'fa-calculator' => 'fa fa-calculator',
  'fa-paypal' => 'fa fa-paypal',
  'fa-google-wallet' => 'fa fa-google-wallet',
  'fa-cc-visa' => 'fa fa-cc-visa',
  'fa-cc-mastercard' => 'fa fa-cc-mastercard',
  'fa-cc-discover' => 'fa fa-cc-discover',
  'fa-cc-amex' => 'fa fa-cc-amex',
  'fa-cc-paypal' => 'fa fa-cc-paypal',
  'fa-cc-stripe' => 'fa fa-cc-stripe',
  'fa-bell-slash' => 'fa fa-bell-slash',
  'fa-bell-slash-o' => 'fa fa-bell-slash-o',
  'fa-trash' => 'fa fa-trash',
  'fa-copyright' => 'fa fa-copyright',
  'fa-at' => 'fa fa-at',
  'fa-eyedropper' => 'fa fa-eyedropper',
  'fa-paint-brush' => 'fa fa-paint-brush',
  'fa-birthday-cake' => 'fa fa-birthday-cake',
  'fa-area-chart' => 'fa fa-area-chart',
  'fa-pie-chart' => 'fa fa-pie-chart',
  'fa-line-chart' => 'fa fa-line-chart',
  'fa-lastfm' => 'fa fa-lastfm',
  'fa-lastfm-square' => 'fa fa-lastfm-square',
  'fa-toggle-off' => 'fa fa-toggle-off',
  'fa-toggle-on' => 'fa fa-toggle-on',
  'fa-bicycle' => 'fa fa-bicycle',
  'fa-bus' => 'fa fa-bus',
  'fa-ioxhost' => 'fa fa-ioxhost',
  'fa-angellist' => 'fa fa-angellist',
  'fa-cc' => 'fa fa-cc',
  'fa-ils' => 'fa fa-ils',
  'fa-meanpath' => 'fa fa-meanpath',
  'fa-buysellads' => 'fa fa-buysellads',
  'fa-connectdevelop' => 'fa fa-connectdevelop',
  'fa-dashcube' => 'fa fa-dashcube',
  'fa-forumbee' => 'fa fa-forumbee',
  'fa-leanpub' => 'fa fa-leanpub',
  'fa-sellsy' => 'fa fa-sellsy',
  'fa-shirtsinbulk' => 'fa fa-shirtsinbulk',
  'fa-simplybuilt' => 'fa fa-simplybuilt',
  'fa-skyatlas' => 'fa fa-skyatlas',
  'fa-cart-plus' => 'fa fa-cart-plus',
  'fa-cart-arrow-down' => 'fa fa-cart-arrow-down',
  'fa-diamond' => 'fa fa-diamond',
  'fa-ship' => 'fa fa-ship',
  'fa-user-secret' => 'fa fa-user-secret',
  'fa-motorcycle' => 'fa fa-motorcycle',
  'fa-street-view' => 'fa fa-street-view',
  'fa-heartbeat' => 'fa fa-heartbeat',
  'fa-venus' => 'fa fa-venus',
  'fa-mars' => 'fa fa-mars',
  'fa-mercury' => 'fa fa-mercury',
  'fa-transgender' => 'fa fa-transgender',
  'fa-transgender-alt' => 'fa fa-transgender-alt',
  'fa-venus-double' => 'fa fa-venus-double',
  'fa-mars-double' => 'fa fa-mars-double',
  'fa-venus-mars' => 'fa fa-venus-mars',
  'fa-mars-stroke' => 'fa fa-mars-stroke',
  'fa-mars-stroke-v' => 'fa fa-mars-stroke-v',
  'fa-mars-stroke-h' => 'fa fa-mars-stroke-h',
  'fa-neuter' => 'fa fa-neuter',
  'fa-genderless' => 'fa fa-genderless',
  'fa-facebook-official' => 'fa fa-facebook-official',
  'fa-pinterest-p' => 'fa fa-pinterest-p',
  'fa-whatsapp' => 'fa fa-whatsapp',
  'fa-server' => 'fa fa-server',
  'fa-user-plus' => 'fa fa-user-plus',
  'fa-user-times' => 'fa fa-user-times',
  'fa-bed' => 'fa fa-bed',
  'fa-viacoin' => 'fa fa-viacoin',
  'fa-train' => 'fa fa-train',
  'fa-subway' => 'fa fa-subway',
  'fa-medium' => 'fa fa-medium',
  'fa-y-combinator' => 'fa fa-y-combinator',
  'fa-optin-monster' => 'fa fa-optin-monster',
  'fa-opencart' => 'fa fa-opencart',
  'fa-expeditedssl' => 'fa fa-expeditedssl',
  'fa-battery-full' => 'fa fa-battery-full',
  'fa-battery-three-quarters' => 'fa fa-battery-three-quarters',
  'fa-battery-half' => 'fa fa-battery-half',
  'fa-battery-quarter' => 'fa fa-battery-quarter',
  'fa-battery-empty' => 'fa fa-battery-empty',
  'fa-mouse-pointer' => 'fa fa-mouse-pointer',
  'fa-i-cursor' => 'fa fa-i-cursor',
  'fa-object-group' => 'fa fa-object-group',
  'fa-object-ungroup' => 'fa fa-object-ungroup',
  'fa-sticky-note' => 'fa fa-sticky-note',
  'fa-sticky-note-o' => 'fa fa-sticky-note-o',
  'fa-cc-jcb' => 'fa fa-cc-jcb',
  'fa-cc-diners-club' => 'fa fa-cc-diners-club',
  'fa-clone' => 'fa fa-clone',
  'fa-balance-scale' => 'fa fa-balance-scale',
  'fa-hourglass-o' => 'fa fa-hourglass-o',
  'fa-hourglass-start' => 'fa fa-hourglass-start',
  'fa-hourglass-half' => 'fa fa-hourglass-half',
  'fa-hourglass-end' => 'fa fa-hourglass-end',
  'fa-hourglass' => 'fa fa-hourglass',
  'fa-hand-rock-o' => 'fa fa-hand-rock-o',
  'fa-hand-paper-o' => 'fa fa-hand-paper-o',
  'fa-hand-scissors-o' => 'fa fa-hand-scissors-o',
  'fa-hand-lizard-o' => 'fa fa-hand-lizard-o',
  'fa-hand-spock-o' => 'fa fa-hand-spock-o',
  'fa-hand-pointer-o' => 'fa fa-hand-pointer-o',
  'fa-hand-peace-o' => 'fa fa-hand-peace-o',
  'fa-trademark' => 'fa fa-trademark',
  'fa-registered' => 'fa fa-registered',
  'fa-creative-commons' => 'fa fa-creative-commons',
  'fa-gg' => 'fa fa-gg',
  'fa-gg-circle' => 'fa fa-gg-circle',
  'fa-tripadvisor' => 'fa fa-tripadvisor',
  'fa-odnoklassniki' => 'fa fa-odnoklassniki',
  'fa-odnoklassniki-square' => 'fa fa-odnoklassniki-square',
  'fa-get-pocket' => 'fa fa-get-pocket',
  'fa-wikipedia-w' => 'fa fa-wikipedia-w',
  'fa-safari' => 'fa fa-safari',
  'fa-chrome' => 'fa fa-chrome',
  'fa-firefox' => 'fa fa-firefox',
  'fa-opera' => 'fa fa-opera',
  'fa-internet-explorer' => 'fa fa-internet-explorer',
  'fa-television' => 'fa fa-television',
  'fa-contao' => 'fa fa-contao',
  'fa-500px' => 'fa fa-500px',
  'fa-amazon' => 'fa fa-amazon',
  'fa-calendar-plus-o' => 'fa fa-calendar-plus-o',
  'fa-calendar-minus-o' => 'fa fa-calendar-minus-o',
  'fa-calendar-times-o' => 'fa fa-calendar-times-o',
  'fa-calendar-check-o' => 'fa fa-calendar-check-o',
  'fa-industry' => 'fa fa-industry',
  'fa-map-pin' => 'fa fa-map-pin',
  'fa-map-signs' => 'fa fa-map-signs',
  'fa-map-o' => 'fa fa-map-o',
  'fa-map' => 'fa fa-map',
  'fa-commenting' => 'fa fa-commenting',
  'fa-commenting-o' => 'fa fa-commenting-o',
  'fa-houzz' => 'fa fa-houzz',
  'fa-vimeo' => 'fa fa-vimeo',
  'fa-black-tie' => 'fa fa-black-tie',
  'fa-fonticons' => 'fa fa-fonticons',
  'fa-reddit-alien' => 'fa fa-reddit-alien',
  'fa-edge' => 'fa fa-edge',
  'fa-credit-card-alt' => 'fa fa-credit-card-alt',
  'fa-codiepie' => 'fa fa-codiepie',
  'fa-modx' => 'fa fa-modx',
  'fa-fort-awesome' => 'fa fa-fort-awesome',
  'fa-usb' => 'fa fa-usb',
  'fa-product-hunt' => 'fa fa-product-hunt',
  'fa-mixcloud' => 'fa fa-mixcloud',
  'fa-scribd' => 'fa fa-scribd',
  'fa-pause-circle' => 'fa fa-pause-circle',
  'fa-pause-circle-o' => 'fa fa-pause-circle-o',
  'fa-stop-circle' => 'fa fa-stop-circle',
  'fa-stop-circle-o' => 'fa fa-stop-circle-o',
  'fa-shopping-bag' => 'fa fa-shopping-bag',
  'fa-shopping-basket' => 'fa fa-shopping-basket',
  'fa-hashtag' => 'fa fa-hashtag',
  'fa-bluetooth' => 'fa fa-bluetooth',
  'fa-bluetooth-b' => 'fa fa-bluetooth-b',
  'fa-percent' => 'fa fa-percent',
  'fa-gitlab' => 'fa fa-gitlab',
  'fa-wpbeginner' => 'fa fa-wpbeginner',
  'fa-wpforms' => 'fa fa-wpforms',
  'fa-envira' => 'fa fa-envira',
  'fa-universal-access' => 'fa fa-universal-access',
  'fa-wheelchair-alt' => 'fa fa-wheelchair-alt',
  'fa-question-circle-o' => 'fa fa-question-circle-o',
  'fa-blind' => 'fa fa-blind',
  'fa-audio-description' => 'fa fa-audio-description',
  'fa-volume-control-phone' => 'fa fa-volume-control-phone',
  'fa-braille' => 'fa fa-braille',
  'fa-assistive-listening-systems' => 'fa fa-assistive-listening-systems',
  'fa-american-sign-language-interpreting' => 'fa fa-american-sign-language-interpreting',
  'fa-deaf' => 'fa fa-deaf',
  'fa-glide' => 'fa fa-glide',
  'fa-glide-g' => 'fa fa-glide-g',
  'fa-sign-language' => 'fa fa-sign-language',
  'fa-low-vision' => 'fa fa-low-vision',
  'fa-viadeo' => 'fa fa-viadeo',
  'fa-viadeo-square' => 'fa fa-viadeo-square',
  'fa-snapchat' => 'fa fa-snapchat',
  'fa-snapchat-ghost' => 'fa fa-snapchat-ghost',
  'fa-snapchat-square' => 'fa fa-snapchat-square',
  'fa-pied-piper' => 'fa fa-pied-piper',
  'fa-first-order' => 'fa fa-first-order',
  'fa-yoast' => 'fa fa-yoast',
  'fa-themeisle' => 'fa fa-themeisle',
  'fa-google-plus-official' => 'fa fa-google-plus-official',
  'fa-font-awesome' => 'fa fa-font-awesome',
  'fa-handshake-o' => 'fa fa-handshake-o',
  'fa-envelope-open' => 'fa fa-envelope-open',
  'fa-envelope-open-o' => 'fa fa-envelope-open-o',
  'fa-linode' => 'fa fa-linode',
  'fa-address-book' => 'fa fa-address-book',
  'fa-address-book-o' => 'fa fa-address-book-o',
  'fa-address-card' => 'fa fa-address-card',
  'fa-address-card-o' => 'fa fa-address-card-o',
  'fa-user-circle' => 'fa fa-user-circle',
  'fa-user-circle-o' => 'fa fa-user-circle-o',
  'fa-user-o' => 'fa fa-user-o',
  'fa-id-badge' => 'fa fa-id-badge',
  'fa-id-card' => 'fa fa-id-card',
  'fa-id-card-o' => 'fa fa-id-card-o',
  'fa-quora' => 'fa fa-quora',
  'fa-free-code-camp' => 'fa fa-free-code-camp',
  'fa-telegram' => 'fa fa-telegram',
  'fa-thermometer-full' => 'fa fa-thermometer-full',
  'fa-thermometer-three-quarters' => 'fa fa-thermometer-three-quarters',
  'fa-thermometer-half' => 'fa fa-thermometer-half',
  'fa-thermometer-quarter' => 'fa fa-thermometer-quarter',
  'fa-thermometer-empty' => 'fa fa-thermometer-empty',
  'fa-shower' => 'fa fa-shower',
  'fa-bath' => 'fa fa-bath',
  'fa-podcast' => 'fa fa-podcast',
  'fa-window-maximize' => 'fa fa-window-maximize',
  'fa-window-minimize' => 'fa fa-window-minimize',
  'fa-window-restore' => 'fa fa-window-restore',
  'fa-window-close' => 'fa fa-window-close',
  'fa-window-close-o' => 'fa fa-window-close-o',
  'fa-bandcamp' => 'fa fa-bandcamp',
  'fa-grav' => 'fa fa-grav',
  'fa-etsy' => 'fa fa-etsy',
  'fa-imdb' => 'fa fa-imdb',
  'fa-ravelry' => 'fa fa-ravelry',
  'fa-eercast' => 'fa fa-eercast',
  'fa-microchip' => 'fa fa-microchip',
  'fa-snowflake-o' => 'fa fa-snowflake-o',
  'fa-superpowers' => 'fa fa-superpowers',
  'fa-wpexplorer' => 'fa fa-wpexplorer',
  'fa-meetup' => 'fa fa-meetup'
);
		
			
		
		
		$steadysets_icons = array(
			'type'=>'icons', 
			'title'=>'Steadysets', 
			'values'=> array(
				  'steadysets-icon-type' => 'steadysets-icon-type',
				  'steadysets-icon-box' => 'steadysets-icon-box',
				  'steadysets-icon-archive' => 'steadysets-icon-archive',
				  'steadysets-icon-envelope' => 'steadysets-icon-envelope',
				  'steadysets-icon-email' => 'steadysets-icon-email',
				  'steadysets-icon-files' => 'steadysets-icon-files',
				  'steadysets-icon-uniE606' => 'steadysets-icon-uniE606',
				  'steadysets-icon-connection-empty' => 'steadysets-icon-connection-empty',
				  'steadysets-icon-connection-25' => 'steadysets-icon-connection-25',
				  'steadysets-icon-connection-50' => 'steadysets-icon-connection-50',
				  'steadysets-icon-connection-75' => 'steadysets-icon-connection-75',
				  'steadysets-icon-connection-full' => 'steadysets-icon-connection-full',
				  'steadysets-icon-microphone' => 'steadysets-icon-microphone',
				  'steadysets-icon-microphone-off' => 'steadysets-icon-microphone-off',
				  'steadysets-icon-book' => 'steadysets-icon-book',
				  'steadysets-icon-cloud' => 'steadysets-icon-cloud',
				  'steadysets-icon-book2' => 'steadysets-icon-book2',
				  'steadysets-icon-star' => 'steadysets-icon-star',
				  'steadysets-icon-phone-portrait' => 'steadysets-icon-phone-portrait',
				  'steadysets-icon-phone-landscape' => 'steadysets-icon-phone-landscape',
				  'steadysets-icon-tablet' => 'steadysets-icon-tablet',
				  'steadysets-icon-tablet-landscape' => 'steadysets-icon-tablet-landscape',
				  'steadysets-icon-laptop' => 'steadysets-icon-laptop',
				  'steadysets-icon-uniE617' => 'steadysets-icon-uniE617',
				  'steadysets-icon-barbell' => 'steadysets-icon-barbell',
				  'steadysets-icon-stopwatch' => 'steadysets-icon-stopwatch',
				  'steadysets-icon-atom' => 'steadysets-icon-atom',
				  'steadysets-icon-syringe' => 'steadysets-icon-syringe',
				  'steadysets-icon-pencil' => 'steadysets-icon-pencil',
				  'steadysets-icon-chart' => 'steadysets-icon-chart',
				  'steadysets-icon-bars' => 'steadysets-icon-bars',
				  'steadysets-icon-cube' => 'steadysets-icon-cube',
				  'steadysets-icon-image' => 'steadysets-icon-image',
				  'steadysets-icon-crop' => 'steadysets-icon-crop',
				  'steadysets-icon-graph' => 'steadysets-icon-graph',
				  'steadysets-icon-select' => 'steadysets-icon-select',
				  'steadysets-icon-bucket' => 'steadysets-icon-bucket',
				  'steadysets-icon-mug' => 'steadysets-icon-mug',
				  'steadysets-icon-clipboard' => 'steadysets-icon-clipboard',
				  'steadysets-icon-lab' => 'steadysets-icon-lab',
				  'steadysets-icon-bones' => 'steadysets-icon-bones',
				  'steadysets-icon-pill' => 'steadysets-icon-pill',
				  'steadysets-icon-bolt' => 'steadysets-icon-bolt',
				  'steadysets-icon-health' => 'steadysets-icon-health',
				  'steadysets-icon-map-marker' => 'steadysets-icon-map-marker',
				  'steadysets-icon-stack' => 'steadysets-icon-stack',
				  'steadysets-icon-newspaper' => 'steadysets-icon-newspaper',
				  'steadysets-icon-uniE62F' => 'steadysets-icon-uniE62F',
				  'steadysets-icon-coffee' => 'steadysets-icon-coffee',
				  'steadysets-icon-bill' => 'steadysets-icon-bill',
				  'steadysets-icon-sun' => 'steadysets-icon-sun',
				  'steadysets-icon-vcard' => 'steadysets-icon-vcard',
				  'steadysets-icon-shorts' => 'steadysets-icon-shorts',
				  'steadysets-icon-drink' => 'steadysets-icon-drink',
				  'steadysets-icon-diamond' => 'steadysets-icon-diamond',
				  'steadysets-icon-bag' => 'steadysets-icon-bag',
				  'steadysets-icon-calculator' => 'steadysets-icon-calculator',
				  'steadysets-icon-credit-cards' => 'steadysets-icon-credit-cards',
				  'steadysets-icon-microwave-oven' => 'steadysets-icon-microwave-oven',
				  'steadysets-icon-camera' => 'steadysets-icon-camera',
				  'steadysets-icon-share' => 'steadysets-icon-share',
				  'steadysets-icon-bullhorn' => 'steadysets-icon-bullhorn',
				  'steadysets-icon-user' => 'steadysets-icon-user',
				  'steadysets-icon-users' => 'steadysets-icon-users',
				  'steadysets-icon-user2' => 'steadysets-icon-user2',
				  'steadysets-icon-users2' => 'steadysets-icon-users2',
				  'steadysets-icon-unlocked' => 'steadysets-icon-unlocked',
				  'steadysets-icon-unlocked2' => 'steadysets-icon-unlocked2',
				  'steadysets-icon-lock' => 'steadysets-icon-lock',
				  'steadysets-icon-forbidden' => 'steadysets-icon-forbidden',
				  'steadysets-icon-switch' => 'steadysets-icon-switch',
				  'steadysets-icon-meter' => 'steadysets-icon-meter',
				  'steadysets-icon-flag' => 'steadysets-icon-flag',
				  'steadysets-icon-home' => 'steadysets-icon-home',
				  'steadysets-icon-printer' => 'steadysets-icon-printer',
				  'steadysets-icon-clock' => 'steadysets-icon-clock',
				  'steadysets-icon-calendar' => 'steadysets-icon-calendar',
				  'steadysets-icon-comment' => 'steadysets-icon-comment',
				  'steadysets-icon-chat-3' => 'steadysets-icon-chat-3',
				  'steadysets-icon-chat-2' => 'steadysets-icon-chat-2',
				  'steadysets-icon-chat-1' => 'steadysets-icon-chat-1',
				  'steadysets-icon-chat' => 'steadysets-icon-chat',
				  'steadysets-icon-zoom-out' => 'steadysets-icon-zoom-out',
				  'steadysets-icon-zoom-in' => 'steadysets-icon-zoom-in',
				  'steadysets-icon-search' => 'steadysets-icon-search',
				  'steadysets-icon-trashcan' => 'steadysets-icon-trashcan',
				  'steadysets-icon-tag' => 'steadysets-icon-tag',
				  'steadysets-icon-download' => 'steadysets-icon-download',
				  'steadysets-icon-paperclip' => 'steadysets-icon-paperclip',
				  'steadysets-icon-checkbox' => 'steadysets-icon-checkbox',
				  'steadysets-icon-checkbox-checked' => 'steadysets-icon-checkbox-checked',
				  'steadysets-icon-checkmark' => 'steadysets-icon-checkmark',
				  'steadysets-icon-refresh' => 'steadysets-icon-refresh',
				  'steadysets-icon-reload' => 'steadysets-icon-reload',
				  'steadysets-icon-arrow-right' => 'steadysets-icon-arrow-right',
				  'steadysets-icon-arrow-down' => 'steadysets-icon-arrow-down',
				  'steadysets-icon-arrow-up' => 'steadysets-icon-arrow-up',
				  'steadysets-icon-arrow-left' => 'steadysets-icon-arrow-left',
				  'steadysets-icon-settings' => 'steadysets-icon-settings',
				  'steadysets-icon-battery-full' => 'steadysets-icon-battery-full',
				  'steadysets-icon-battery-75' => 'steadysets-icon-battery-75',
				  'steadysets-icon-battery-50' => 'steadysets-icon-battery-50',
				  'steadysets-icon-battery-25' => 'steadysets-icon-battery-25',
				  'steadysets-icon-battery-empty' => 'steadysets-icon-battery-empty',
				  'steadysets-icon-battery-charging' => 'steadysets-icon-battery-charging',
				  'steadysets-icon-uniE669' => 'steadysets-icon-uniE669',
				  'steadysets-icon-grid' => 'steadysets-icon-grid',
				  'steadysets-icon-list' => 'steadysets-icon-list',
				  'steadysets-icon-wifi-low' => 'steadysets-icon-wifi-low',
				  'steadysets-icon-folder-check' => 'steadysets-icon-folder-check',
				  'steadysets-icon-folder-settings' => 'steadysets-icon-folder-settings',
				  'steadysets-icon-folder-add' => 'steadysets-icon-folder-add',
				  'steadysets-icon-folder' => 'steadysets-icon-folder',
				  'steadysets-icon-window' => 'steadysets-icon-window',
				  'steadysets-icon-windows' => 'steadysets-icon-windows',
				  'steadysets-icon-browser' => 'steadysets-icon-browser',
				  'steadysets-icon-file-broken' => 'steadysets-icon-file-broken',
				  'steadysets-icon-align-justify' => 'steadysets-icon-align-justify',
				  'steadysets-icon-align-center' => 'steadysets-icon-align-center',
				  'steadysets-icon-align-right' => 'steadysets-icon-align-right',
				  'steadysets-icon-align-left' => 'steadysets-icon-align-left',
				  'steadysets-icon-file' => 'steadysets-icon-file',
				  'steadysets-icon-file-add' => 'steadysets-icon-file-add',
				  'steadysets-icon-file-settings' => 'steadysets-icon-file-settings',
				  'steadysets-icon-mute' => 'steadysets-icon-mute',
				  'steadysets-icon-heart' => 'steadysets-icon-heart',
				  'steadysets-icon-enter' => 'steadysets-icon-enter',
				  'steadysets-icon-volume-decrease' => 'steadysets-icon-volume-decrease',
				  'steadysets-icon-wifi-mid' => 'steadysets-icon-wifi-mid',
				  'steadysets-icon-volume' => 'steadysets-icon-volume',
				  'steadysets-icon-bookmark' => 'steadysets-icon-bookmark',
				  'steadysets-icon-screen' => 'steadysets-icon-screen',
				  'steadysets-icon-map' => 'steadysets-icon-map',
				  'steadysets-icon-measure' => 'steadysets-icon-measure',
				  'steadysets-icon-eyedropper' => 'steadysets-icon-eyedropper',
				  'steadysets-icon-support' => 'steadysets-icon-support',
				  'steadysets-icon-phone' => 'steadysets-icon-phone',
				  'steadysets-icon-email2' => 'steadysets-icon-email2',
				  'steadysets-icon-volume-increase' => 'steadysets-icon-volume-increase',
				  'steadysets-icon-wifi-full' => 'steadysets-icon-wifi-full'
			)
		);	

$iconsmind_icons = array(
			'type'=>'icons', 
			'title'=>'Iconsmind', 
			'values'=>array(
			  'iconsmind-Aquarius' => 'iconsmind-Aquarius',
			  'iconsmind-Aquarius-2' => 'iconsmind-Aquarius-2',
			  'iconsmind-Aries' => 'iconsmind-Aries',
			  'iconsmind-Aries-2' => 'iconsmind-Aries-2',
			  'iconsmind-Cancer' => 'iconsmind-Cancer',
			  'iconsmind-Cancer-2' => 'iconsmind-Cancer-2',
			  'iconsmind-Capricorn' => 'iconsmind-Capricorn',
			  'iconsmind-Capricorn-2' => 'iconsmind-Capricorn-2',
			  'iconsmind-Gemini' => 'iconsmind-Gemini',
			  'iconsmind-Gemini-2' => 'iconsmind-Gemini-2',
			  'iconsmind-Leo' => 'iconsmind-Leo',
			  'iconsmind-Leo-2' => 'iconsmind-Leo-2',
			  'iconsmind-Libra' => 'iconsmind-Libra',
			  'iconsmind-Libra-2' => 'iconsmind-Libra-2',
			  'iconsmind-Pisces' => 'iconsmind-Pisces',
			  'iconsmind-Pisces-2' => 'iconsmind-Pisces-2',
			  'iconsmind-Sagittarus' => 'iconsmind-Sagittarus',
			  'iconsmind-Sagittarus-2' => 'iconsmind-Sagittarus-2',
			  'iconsmind-Scorpio' => 'iconsmind-Scorpio',
			  'iconsmind-Scorpio-2' => 'iconsmind-Scorpio-2',
			  'iconsmind-Taurus' => 'iconsmind-Taurus',
			  'iconsmind-Taurus-2' => 'iconsmind-Taurus-2',
			  'iconsmind-Virgo' => 'iconsmind-Virgo',
			  'iconsmind-Virgo-2' => 'iconsmind-Virgo-2',
			  'iconsmind-Add-Window' => 'iconsmind-Add-Window',
			  'iconsmind-Approved-Window' => 'iconsmind-Approved-Window',
			  'iconsmind-Block-Window' => 'iconsmind-Block-Window',
			  'iconsmind-Close-Window' => 'iconsmind-Close-Window',
			  'iconsmind-Code-Window' => 'iconsmind-Code-Window',
			  'iconsmind-Delete-Window' => 'iconsmind-Delete-Window',
			  'iconsmind-Download-Window' => 'iconsmind-Download-Window',
			  'iconsmind-Duplicate-Window' => 'iconsmind-Duplicate-Window',
			  'iconsmind-Error-404Window' => 'iconsmind-Error-404Window',
			  'iconsmind-Favorite-Window' => 'iconsmind-Favorite-Window',
			  'iconsmind-Font-Window' => 'iconsmind-Font-Window',
			  'iconsmind-Full-ViewWindow' => 'iconsmind-Full-ViewWindow',
			  'iconsmind-Height-Window' => 'iconsmind-Height-Window',
			  'iconsmind-Home-Window' => 'iconsmind-Home-Window',
			  'iconsmind-Info-Window' => 'iconsmind-Info-Window',
			  'iconsmind-Loading-Window' => 'iconsmind-Loading-Window',
			  'iconsmind-Lock-Window' => 'iconsmind-Lock-Window',
			  'iconsmind-Love-Window' => 'iconsmind-Love-Window',
			  'iconsmind-Maximize-Window' => 'iconsmind-Maximize-Window',
			  'iconsmind-Minimize-Maximize-Close-Window' => 'iconsmind-Minimize-Maximize-Close-Window',
			  'iconsmind-Minimize-Window' => 'iconsmind-Minimize-Window',
			  'iconsmind-Navigation-LeftWindow' => 'iconsmind-Navigation-LeftWindow',
			  'iconsmind-Navigation-RightWindow' => 'iconsmind-Navigation-RightWindow',
			  'iconsmind-Network-Window' => 'iconsmind-Network-Window',
			  'iconsmind-New-Tab' => 'iconsmind-New-Tab',
			  'iconsmind-One-Window' => 'iconsmind-One-Window',
			  'iconsmind-Refresh-Window' => 'iconsmind-Refresh-Window',
			  'iconsmind-Remove-Window' => 'iconsmind-Remove-Window',
			  'iconsmind-Restore-Window' => 'iconsmind-Restore-Window',
			  'iconsmind-Save-Window' => 'iconsmind-Save-Window',
			  'iconsmind-Settings-Window' => 'iconsmind-Settings-Window',
			  'iconsmind-Share-Window' => 'iconsmind-Share-Window',
			  'iconsmind-Sidebar-Window' => 'iconsmind-Sidebar-Window',
			  'iconsmind-Split-FourSquareWindow' => 'iconsmind-Split-FourSquareWindow',
			  'iconsmind-Split-Horizontal' => 'iconsmind-Split-Horizontal',
			  'iconsmind-Split-Horizontal2Window' => 'iconsmind-Split-Horizontal2Window',
			  'iconsmind-Split-Vertical' => 'iconsmind-Split-Vertical',
			  'iconsmind-Split-Vertical2' => 'iconsmind-Split-Vertical2',
			  'iconsmind-Split-Window' => 'iconsmind-Split-Window',
			  'iconsmind-Time-Window' => 'iconsmind-Time-Window',
			  'iconsmind-Touch-Window' => 'iconsmind-Touch-Window',
			  'iconsmind-Two-Windows' => 'iconsmind-Two-Windows',
			  'iconsmind-Upload-Window' => 'iconsmind-Upload-Window',
			  'iconsmind-URL-Window' => 'iconsmind-URL-Window',
			  'iconsmind-Warning-Window' => 'iconsmind-Warning-Window',
			  'iconsmind-Width-Window' => 'iconsmind-Width-Window',
			  'iconsmind-Window-2' => 'iconsmind-Window-2',
			  'iconsmind-Windows-2' => 'iconsmind-Windows-2',
			  'iconsmind-Autumn' => 'iconsmind-Autumn',
			  'iconsmind-Celsius' => 'iconsmind-Celsius',
			  'iconsmind-Cloud-Hail' => 'iconsmind-Cloud-Hail',
			  'iconsmind-Cloud-Moon' => 'iconsmind-Cloud-Moon',
			  'iconsmind-Cloud-Rain' => 'iconsmind-Cloud-Rain',
			  'iconsmind-Cloud-Snow' => 'iconsmind-Cloud-Snow',
			  'iconsmind-Cloud-Sun' => 'iconsmind-Cloud-Sun',
			  'iconsmind-Clouds-Weather' => 'iconsmind-Clouds-Weather',
			  'iconsmind-Cloud-Weather' => 'iconsmind-Cloud-Weather',
			  'iconsmind-Drop' => 'iconsmind-Drop',
			  'iconsmind-Dry' => 'iconsmind-Dry',
			  'iconsmind-Fahrenheit' => 'iconsmind-Fahrenheit',
			  'iconsmind-Fog-Day' => 'iconsmind-Fog-Day',
			  'iconsmind-Fog-Night' => 'iconsmind-Fog-Night',
			  'iconsmind-Full-Moon' => 'iconsmind-Full-Moon',
			  'iconsmind-Half-Moon' => 'iconsmind-Half-Moon',
			  'iconsmind-No-Drop' => 'iconsmind-No-Drop',
			  'iconsmind-Rainbow' => 'iconsmind-Rainbow',
			  'iconsmind-Rainbow-2' => 'iconsmind-Rainbow-2',
			  'iconsmind-Rain-Drop' => 'iconsmind-Rain-Drop',
			  'iconsmind-Sleet' => 'iconsmind-Sleet',
			  'iconsmind-Snow' => 'iconsmind-Snow',
			  'iconsmind-Snowflake' => 'iconsmind-Snowflake',
			  'iconsmind-Snowflake-2' => 'iconsmind-Snowflake-2',
			  'iconsmind-Snowflake-3' => 'iconsmind-Snowflake-3',
			  'iconsmind-Snow-Storm' => 'iconsmind-Snow-Storm',
			  'iconsmind-Spring' => 'iconsmind-Spring',
			  'iconsmind-Storm' => 'iconsmind-Storm',
			  'iconsmind-Summer' => 'iconsmind-Summer',
			  'iconsmind-Sun' => 'iconsmind-Sun',
			  'iconsmind-Sun-CloudyRain' => 'iconsmind-Sun-CloudyRain',
			  'iconsmind-Sunrise' => 'iconsmind-Sunrise',
			  'iconsmind-Sunset' => 'iconsmind-Sunset',
			  'iconsmind-Temperature' => 'iconsmind-Temperature',
			  'iconsmind-Temperature-2' => 'iconsmind-Temperature-2',
			  'iconsmind-Thunder' => 'iconsmind-Thunder',
			  'iconsmind-Thunderstorm' => 'iconsmind-Thunderstorm',
			  'iconsmind-Twister' => 'iconsmind-Twister',
			  'iconsmind-Umbrella-2' => 'iconsmind-Umbrella-2',
			  'iconsmind-Umbrella-3' => 'iconsmind-Umbrella-3',
			  'iconsmind-Wave' => 'iconsmind-Wave',
			  'iconsmind-Wave-2' => 'iconsmind-Wave-2',
			  'iconsmind-Windsock' => 'iconsmind-Windsock',
			  'iconsmind-Wind-Turbine' => 'iconsmind-Wind-Turbine',
			  'iconsmind-Windy' => 'iconsmind-Windy',
			  'iconsmind-Winter' => 'iconsmind-Winter',
			  'iconsmind-Winter-2' => 'iconsmind-Winter-2',
			  'iconsmind-Cinema' => 'iconsmind-Cinema',
			  'iconsmind-Clapperboard-Close' => 'iconsmind-Clapperboard-Close',
			  'iconsmind-Clapperboard-Open' => 'iconsmind-Clapperboard-Open',
			  'iconsmind-D-Eyeglasses' => 'iconsmind-D-Eyeglasses',
			  'iconsmind-D-Eyeglasses2' => 'iconsmind-D-Eyeglasses2',
			  'iconsmind-Director' => 'iconsmind-Director',
			  'iconsmind-Film' => 'iconsmind-Film',
			  'iconsmind-Film-Strip' => 'iconsmind-Film-Strip',
			  'iconsmind-Film-Video' => 'iconsmind-Film-Video',
			  'iconsmind-Flash-Video' => 'iconsmind-Flash-Video',
			  'iconsmind-HD-Video' => 'iconsmind-HD-Video',
			  'iconsmind-Movie' => 'iconsmind-Movie',
			  'iconsmind-Old-TV' => 'iconsmind-Old-TV',
			  'iconsmind-Reel' => 'iconsmind-Reel',
			  'iconsmind-Tripod-andVideo' => 'iconsmind-Tripod-andVideo',
			  'iconsmind-TV' => 'iconsmind-TV',
			  'iconsmind-Video' => 'iconsmind-Video',
			  'iconsmind-Video-2' => 'iconsmind-Video-2',
			  'iconsmind-Video-3' => 'iconsmind-Video-3',
			  'iconsmind-Video-4' => 'iconsmind-Video-4',
			  'iconsmind-Video-5' => 'iconsmind-Video-5',
			  'iconsmind-Video-6' => 'iconsmind-Video-6',
			  'iconsmind-Video-Len' => 'iconsmind-Video-Len',
			  'iconsmind-Video-Len2' => 'iconsmind-Video-Len2',
			  'iconsmind-Video-Photographer' => 'iconsmind-Video-Photographer',
			  'iconsmind-Video-Tripod' => 'iconsmind-Video-Tripod',
			  'iconsmind-Affiliate' => 'iconsmind-Affiliate',
			  'iconsmind-Background' => 'iconsmind-Background',
			  'iconsmind-Billing' => 'iconsmind-Billing',
			  'iconsmind-Control' => 'iconsmind-Control',
			  'iconsmind-Control-2' => 'iconsmind-Control-2',
			  'iconsmind-Crop-2' => 'iconsmind-Crop-2',
			  'iconsmind-Dashboard' => 'iconsmind-Dashboard',
			  'iconsmind-Duplicate-Layer' => 'iconsmind-Duplicate-Layer',
			  'iconsmind-Filter-2' => 'iconsmind-Filter-2',
			  'iconsmind-Gear' => 'iconsmind-Gear',
			  'iconsmind-Gear-2' => 'iconsmind-Gear-2',
			  'iconsmind-Gears' => 'iconsmind-Gears',
			  'iconsmind-Gears-2' => 'iconsmind-Gears-2',
			  'iconsmind-Information' => 'iconsmind-Information',
			  'iconsmind-Layer-Backward' => 'iconsmind-Layer-Backward',
			  'iconsmind-Layer-Forward' => 'iconsmind-Layer-Forward',
			  'iconsmind-Library' => 'iconsmind-Library',
			  'iconsmind-Loading' => 'iconsmind-Loading',
			  'iconsmind-Loading-2' => 'iconsmind-Loading-2',
			  'iconsmind-Loading-3' => 'iconsmind-Loading-3',
			  'iconsmind-Magnifi-Glass' => 'iconsmind-Magnifi-Glass',
			  'iconsmind-Magnifi-Glass2' => 'iconsmind-Magnifi-Glass2',
			  'iconsmind-Magnifi-Glass22' => 'iconsmind-Magnifi-Glass22',
			  'iconsmind-Mouse-Pointer' => 'iconsmind-Mouse-Pointer',
			  'iconsmind-On-off' => 'iconsmind-On-off',
			  'iconsmind-On-Off-2' => 'iconsmind-On-Off-2',
			  'iconsmind-On-Off-3' => 'iconsmind-On-Off-3',
			  'iconsmind-Preview' => 'iconsmind-Preview',
			  'iconsmind-Pricing' => 'iconsmind-Pricing',
			  'iconsmind-Profile' => 'iconsmind-Profile',
			  'iconsmind-Project' => 'iconsmind-Project',
			  'iconsmind-Rename' => 'iconsmind-Rename',
			  'iconsmind-Repair' => 'iconsmind-Repair',
			  'iconsmind-Save' => 'iconsmind-Save',
			  'iconsmind-Scroller' => 'iconsmind-Scroller',
			  'iconsmind-Scroller-2' => 'iconsmind-Scroller-2',
			  'iconsmind-Share' => 'iconsmind-Share',
			  'iconsmind-Statistic' => 'iconsmind-Statistic',
			  'iconsmind-Support' => 'iconsmind-Support',
			  'iconsmind-Switch' => 'iconsmind-Switch',
			  'iconsmind-Upgrade' => 'iconsmind-Upgrade',
			  'iconsmind-User' => 'iconsmind-User',
			  'iconsmind-Wrench' => 'iconsmind-Wrench',
			  'iconsmind-Air-Balloon' => 'iconsmind-Air-Balloon',
			  'iconsmind-Airship' => 'iconsmind-Airship',
			  'iconsmind-Bicycle' => 'iconsmind-Bicycle',
			  'iconsmind-Bicycle-2' => 'iconsmind-Bicycle-2',
			  'iconsmind-Bike-Helmet' => 'iconsmind-Bike-Helmet',
			  'iconsmind-Bus' => 'iconsmind-Bus',
			  'iconsmind-Bus-2' => 'iconsmind-Bus-2',
			  'iconsmind-Cable-Car' => 'iconsmind-Cable-Car',
			  'iconsmind-Car' => 'iconsmind-Car',
			  'iconsmind-Car-2' => 'iconsmind-Car-2',
			  'iconsmind-Car-3' => 'iconsmind-Car-3',
			  'iconsmind-Car-Wheel' => 'iconsmind-Car-Wheel',
			  'iconsmind-Gaugage' => 'iconsmind-Gaugage',
			  'iconsmind-Gaugage-2' => 'iconsmind-Gaugage-2',
			  'iconsmind-Helicopter' => 'iconsmind-Helicopter',
			  'iconsmind-Helicopter-2' => 'iconsmind-Helicopter-2',
			  'iconsmind-Helmet' => 'iconsmind-Helmet',
			  'iconsmind-Jeep' => 'iconsmind-Jeep',
			  'iconsmind-Jeep-2' => 'iconsmind-Jeep-2',
			  'iconsmind-Jet' => 'iconsmind-Jet',
			  'iconsmind-Motorcycle' => 'iconsmind-Motorcycle',
			  'iconsmind-Plane' => 'iconsmind-Plane',
			  'iconsmind-Plane-2' => 'iconsmind-Plane-2',
			  'iconsmind-Road' => 'iconsmind-Road',
			  'iconsmind-Road-2' => 'iconsmind-Road-2',
			  'iconsmind-Rocket' => 'iconsmind-Rocket',
			  'iconsmind-Sailing-Ship' => 'iconsmind-Sailing-Ship',
			  'iconsmind-Scooter' => 'iconsmind-Scooter',
			  'iconsmind-Scooter-Front' => 'iconsmind-Scooter-Front',
			  'iconsmind-Ship' => 'iconsmind-Ship',
			  'iconsmind-Ship-2' => 'iconsmind-Ship-2',
			  'iconsmind-Skateboard' => 'iconsmind-Skateboard',
			  'iconsmind-Skateboard-2' => 'iconsmind-Skateboard-2',
			  'iconsmind-Taxi' => 'iconsmind-Taxi',
			  'iconsmind-Taxi-2' => 'iconsmind-Taxi-2',
			  'iconsmind-Taxi-Sign' => 'iconsmind-Taxi-Sign',
			  'iconsmind-Tractor' => 'iconsmind-Tractor',
			  'iconsmind-traffic-Light' => 'iconsmind-traffic-Light',
			  'iconsmind-Traffic-Light2' => 'iconsmind-Traffic-Light2',
			  'iconsmind-Train' => 'iconsmind-Train',
			  'iconsmind-Train-2' => 'iconsmind-Train-2',
			  'iconsmind-Tram' => 'iconsmind-Tram',
			  'iconsmind-Truck' => 'iconsmind-Truck',
			  'iconsmind-Yacht' => 'iconsmind-Yacht',
			  'iconsmind-Double-Tap' => 'iconsmind-Double-Tap',
			  'iconsmind-Drag' => 'iconsmind-Drag',
			  'iconsmind-Drag-Down' => 'iconsmind-Drag-Down',
			  'iconsmind-Drag-Left' => 'iconsmind-Drag-Left',
			  'iconsmind-Drag-Right' => 'iconsmind-Drag-Right',
			  'iconsmind-Drag-Up' => 'iconsmind-Drag-Up',
			  'iconsmind-Finger-DragFourSides' => 'iconsmind-Finger-DragFourSides',
			  'iconsmind-Finger-DragTwoSides' => 'iconsmind-Finger-DragTwoSides',
			  'iconsmind-Five-Fingers' => 'iconsmind-Five-Fingers',
			  'iconsmind-Five-FingersDrag' => 'iconsmind-Five-FingersDrag',
			  'iconsmind-Five-FingersDrag2' => 'iconsmind-Five-FingersDrag2',
			  'iconsmind-Five-FingersTouch' => 'iconsmind-Five-FingersTouch',
			  'iconsmind-Flick' => 'iconsmind-Flick',
			  'iconsmind-Four-Fingers' => 'iconsmind-Four-Fingers',
			  'iconsmind-Four-FingersDrag' => 'iconsmind-Four-FingersDrag',
			  'iconsmind-Four-FingersDrag2' => 'iconsmind-Four-FingersDrag2',
			  'iconsmind-Four-FingersTouch' => 'iconsmind-Four-FingersTouch',
			  'iconsmind-Hand-Touch' => 'iconsmind-Hand-Touch',
			  'iconsmind-Hand-Touch2' => 'iconsmind-Hand-Touch2',
			  'iconsmind-Hand-TouchSmartphone' => 'iconsmind-Hand-TouchSmartphone',
			  'iconsmind-One-Finger' => 'iconsmind-One-Finger',
			  'iconsmind-One-FingerTouch' => 'iconsmind-One-FingerTouch',
			  'iconsmind-Pinch' => 'iconsmind-Pinch',
			  'iconsmind-Press' => 'iconsmind-Press',
			  'iconsmind-Rotate-Gesture' => 'iconsmind-Rotate-Gesture',
			  'iconsmind-Rotate-Gesture2' => 'iconsmind-Rotate-Gesture2',
			  'iconsmind-Rotate-Gesture3' => 'iconsmind-Rotate-Gesture3',
			  'iconsmind-Scroll' => 'iconsmind-Scroll',
			  'iconsmind-Scroll-Fast' => 'iconsmind-Scroll-Fast',
			  'iconsmind-Spread' => 'iconsmind-Spread',
			  'iconsmind-Star-Track' => 'iconsmind-Star-Track',
			  'iconsmind-Tap' => 'iconsmind-Tap',
			  'iconsmind-Three-Fingers' => 'iconsmind-Three-Fingers',
			  'iconsmind-Three-FingersDrag' => 'iconsmind-Three-FingersDrag',
			  'iconsmind-Three-FingersDrag2' => 'iconsmind-Three-FingersDrag2',
			  'iconsmind-Three-FingersTouch' => 'iconsmind-Three-FingersTouch',
			  'iconsmind-Thumb' => 'iconsmind-Thumb',
			  'iconsmind-Two-Fingers' => 'iconsmind-Two-Fingers',
			  'iconsmind-Two-FingersDrag' => 'iconsmind-Two-FingersDrag',
			  'iconsmind-Two-FingersDrag2' => 'iconsmind-Two-FingersDrag2',
			  'iconsmind-Two-FingersScroll' => 'iconsmind-Two-FingersScroll',
			  'iconsmind-Two-FingersTouch' => 'iconsmind-Two-FingersTouch',
			  'iconsmind-Zoom-Gesture' => 'iconsmind-Zoom-Gesture',
			  'iconsmind-Alarm-Clock' => 'iconsmind-Alarm-Clock',
			  'iconsmind-Alarm-Clock2' => 'iconsmind-Alarm-Clock2',
			  'iconsmind-Calendar-Clock' => 'iconsmind-Calendar-Clock',
			  'iconsmind-Clock' => 'iconsmind-Clock',
			  'iconsmind-Clock-2' => 'iconsmind-Clock-2',
			  'iconsmind-Clock-3' => 'iconsmind-Clock-3',
			  'iconsmind-Clock-4' => 'iconsmind-Clock-4',
			  'iconsmind-Clock-Back' => 'iconsmind-Clock-Back',
			  'iconsmind-Clock-Forward' => 'iconsmind-Clock-Forward',
			  'iconsmind-Hour' => 'iconsmind-Hour',
			  'iconsmind-Old-Clock' => 'iconsmind-Old-Clock',
			  'iconsmind-Over-Time' => 'iconsmind-Over-Time',
			  'iconsmind-Over-Time2' => 'iconsmind-Over-Time2',
			  'iconsmind-Sand-watch' => 'iconsmind-Sand-watch',
			  'iconsmind-Sand-watch2' => 'iconsmind-Sand-watch2',
			  'iconsmind-Stopwatch' => 'iconsmind-Stopwatch',
			  'iconsmind-Stopwatch-2' => 'iconsmind-Stopwatch-2',
			  'iconsmind-Time-Backup' => 'iconsmind-Time-Backup',
			  'iconsmind-Time-Fire' => 'iconsmind-Time-Fire',
			  'iconsmind-Time-Machine' => 'iconsmind-Time-Machine',
			  'iconsmind-Timer' => 'iconsmind-Timer',
			  'iconsmind-Watch' => 'iconsmind-Watch',
			  'iconsmind-Watch-2' => 'iconsmind-Watch-2',
			  'iconsmind-Watch-3' => 'iconsmind-Watch-3',
			  'iconsmind-A-Z' => 'iconsmind-A-Z',
			  'iconsmind-Bold-Text' => 'iconsmind-Bold-Text',
			  'iconsmind-Bulleted-List' => 'iconsmind-Bulleted-List',
			  'iconsmind-Font-Color' => 'iconsmind-Font-Color',
			  'iconsmind-Font-Name' => 'iconsmind-Font-Name',
			  'iconsmind-Font-Size' => 'iconsmind-Font-Size',
			  'iconsmind-Font-Style' => 'iconsmind-Font-Style',
			  'iconsmind-Font-StyleSubscript' => 'iconsmind-Font-StyleSubscript',
			  'iconsmind-Font-StyleSuperscript' => 'iconsmind-Font-StyleSuperscript',
			  'iconsmind-Function' => 'iconsmind-Function',
			  'iconsmind-Italic-Text' => 'iconsmind-Italic-Text',
			  'iconsmind-Line-SpacingText' => 'iconsmind-Line-SpacingText',
			  'iconsmind-Lowercase-Text' => 'iconsmind-Lowercase-Text',
			  'iconsmind-Normal-Text' => 'iconsmind-Normal-Text',
			  'iconsmind-Numbering-List' => 'iconsmind-Numbering-List',
			  'iconsmind-Strikethrough-Text' => 'iconsmind-Strikethrough-Text',
			  'iconsmind-Sum' => 'iconsmind-Sum',
			  'iconsmind-Text-Box' => 'iconsmind-Text-Box',
			  'iconsmind-Text-Effect' => 'iconsmind-Text-Effect',
			  'iconsmind-Text-HighlightColor' => 'iconsmind-Text-HighlightColor',
			  'iconsmind-Text-Paragraph' => 'iconsmind-Text-Paragraph',
			  'iconsmind-Under-LineText' => 'iconsmind-Under-LineText',
			  'iconsmind-Uppercase-Text' => 'iconsmind-Uppercase-Text',
			  'iconsmind-Wrap-Text' => 'iconsmind-Wrap-Text',
			  'iconsmind-Z-A' => 'iconsmind-Z-A',
			  'iconsmind-Aerobics' => 'iconsmind-Aerobics',
			  'iconsmind-Aerobics-2' => 'iconsmind-Aerobics-2',
			  'iconsmind-Aerobics-3' => 'iconsmind-Aerobics-3',
			  'iconsmind-Archery' => 'iconsmind-Archery',
			  'iconsmind-Archery-2' => 'iconsmind-Archery-2',
			  'iconsmind-Ballet-Shoes' => 'iconsmind-Ballet-Shoes',
			  'iconsmind-Baseball' => 'iconsmind-Baseball',
			  'iconsmind-Basket-Ball' => 'iconsmind-Basket-Ball',
			  'iconsmind-Bodybuilding' => 'iconsmind-Bodybuilding',
			  'iconsmind-Bowling' => 'iconsmind-Bowling',
			  'iconsmind-Bowling-2' => 'iconsmind-Bowling-2',
			  'iconsmind-Box' => 'iconsmind-Box',
			  'iconsmind-Chess' => 'iconsmind-Chess',
			  'iconsmind-Cricket' => 'iconsmind-Cricket',
			  'iconsmind-Dumbbell' => 'iconsmind-Dumbbell',
			  'iconsmind-Football' => 'iconsmind-Football',
			  'iconsmind-Football-2' => 'iconsmind-Football-2',
			  'iconsmind-Footprint' => 'iconsmind-Footprint',
			  'iconsmind-Footprint-2' => 'iconsmind-Footprint-2',
			  'iconsmind-Goggles' => 'iconsmind-Goggles',
			  'iconsmind-Golf' => 'iconsmind-Golf',
			  'iconsmind-Golf-2' => 'iconsmind-Golf-2',
			  'iconsmind-Gymnastics' => 'iconsmind-Gymnastics',
			  'iconsmind-Hokey' => 'iconsmind-Hokey',
			  'iconsmind-Jump-Rope' => 'iconsmind-Jump-Rope',
			  'iconsmind-Life-Jacket' => 'iconsmind-Life-Jacket',
			  'iconsmind-Medal' => 'iconsmind-Medal',
			  'iconsmind-Medal-2' => 'iconsmind-Medal-2',
			  'iconsmind-Medal-3' => 'iconsmind-Medal-3',
			  'iconsmind-Parasailing' => 'iconsmind-Parasailing',
			  'iconsmind-Pilates' => 'iconsmind-Pilates',
			  'iconsmind-Pilates-2' => 'iconsmind-Pilates-2',
			  'iconsmind-Pilates-3' => 'iconsmind-Pilates-3',
			  'iconsmind-Ping-Pong' => 'iconsmind-Ping-Pong',
			  'iconsmind-Rafting' => 'iconsmind-Rafting',
			  'iconsmind-Running' => 'iconsmind-Running',
			  'iconsmind-Running-Shoes' => 'iconsmind-Running-Shoes',
			  'iconsmind-Skate-Shoes' => 'iconsmind-Skate-Shoes',
			  'iconsmind-Ski' => 'iconsmind-Ski',
			  'iconsmind-Skydiving' => 'iconsmind-Skydiving',
			  'iconsmind-Snorkel' => 'iconsmind-Snorkel',
			  'iconsmind-Soccer-Ball' => 'iconsmind-Soccer-Ball',
			  'iconsmind-Soccer-Shoes' => 'iconsmind-Soccer-Shoes',
			  'iconsmind-Swimming' => 'iconsmind-Swimming',
			  'iconsmind-Tennis' => 'iconsmind-Tennis',
			  'iconsmind-Tennis-Ball' => 'iconsmind-Tennis-Ball',
			  'iconsmind-Trekking' => 'iconsmind-Trekking',
			  'iconsmind-Trophy' => 'iconsmind-Trophy',
			  'iconsmind-Trophy-2' => 'iconsmind-Trophy-2',
			  'iconsmind-Volleyball' => 'iconsmind-Volleyball',
			  'iconsmind-weight-Lift' => 'iconsmind-weight-Lift',
			  'iconsmind-Speach-Bubble' => 'iconsmind-Speach-Bubble',
			  'iconsmind-Speach-Bubble2' => 'iconsmind-Speach-Bubble2',
			  'iconsmind-Speach-Bubble3' => 'iconsmind-Speach-Bubble3',
			  'iconsmind-Speach-Bubble4' => 'iconsmind-Speach-Bubble4',
			  'iconsmind-Speach-Bubble5' => 'iconsmind-Speach-Bubble5',
			  'iconsmind-Speach-Bubble6' => 'iconsmind-Speach-Bubble6',
			  'iconsmind-Speach-Bubble7' => 'iconsmind-Speach-Bubble7',
			  'iconsmind-Speach-Bubble8' => 'iconsmind-Speach-Bubble8',
			  'iconsmind-Speach-Bubble9' => 'iconsmind-Speach-Bubble9',
			  'iconsmind-Speach-Bubble10' => 'iconsmind-Speach-Bubble10',
			  'iconsmind-Speach-Bubble11' => 'iconsmind-Speach-Bubble11',
			  'iconsmind-Speach-Bubble12' => 'iconsmind-Speach-Bubble12',
			  'iconsmind-Speach-Bubble13' => 'iconsmind-Speach-Bubble13',
			  'iconsmind-Speach-BubbleAsking' => 'iconsmind-Speach-BubbleAsking',
			  'iconsmind-Speach-BubbleComic' => 'iconsmind-Speach-BubbleComic',
			  'iconsmind-Speach-BubbleComic2' => 'iconsmind-Speach-BubbleComic2',
			  'iconsmind-Speach-BubbleComic3' => 'iconsmind-Speach-BubbleComic3',
			  'iconsmind-Speach-BubbleComic4' => 'iconsmind-Speach-BubbleComic4',
			  'iconsmind-Speach-BubbleDialog' => 'iconsmind-Speach-BubbleDialog',
			  'iconsmind-Speach-Bubbles' => 'iconsmind-Speach-Bubbles',
			  'iconsmind-Aim' => 'iconsmind-Aim',
			  'iconsmind-Ask' => 'iconsmind-Ask',
			  'iconsmind-Bebo' => 'iconsmind-Bebo',
			  'iconsmind-Behance' => 'iconsmind-Behance',
			  'iconsmind-Betvibes' => 'iconsmind-Betvibes',
			  'iconsmind-Bing' => 'iconsmind-Bing',
			  'iconsmind-Blinklist' => 'iconsmind-Blinklist',
			  'iconsmind-Blogger' => 'iconsmind-Blogger',
			  'iconsmind-Brightkite' => 'iconsmind-Brightkite',
			  'iconsmind-Delicious' => 'iconsmind-Delicious',
			  'iconsmind-Deviantart' => 'iconsmind-Deviantart',
			  'iconsmind-Digg' => 'iconsmind-Digg',
			  'iconsmind-Diigo' => 'iconsmind-Diigo',
			  'iconsmind-Doplr' => 'iconsmind-Doplr',
			  'iconsmind-Dribble' => 'iconsmind-Dribble',
			  'iconsmind-Email' => 'iconsmind-Email',
			  'iconsmind-Evernote' => 'iconsmind-Evernote',
			  'iconsmind-Facebook' => 'iconsmind-Facebook',
			  'iconsmind-Facebook-2' => 'iconsmind-Facebook-2',
			  'iconsmind-Feedburner' => 'iconsmind-Feedburner',
			  'iconsmind-Flickr' => 'iconsmind-Flickr',
			  'iconsmind-Formspring' => 'iconsmind-Formspring',
			  'iconsmind-Forsquare' => 'iconsmind-Forsquare',
			  'iconsmind-Friendfeed' => 'iconsmind-Friendfeed',
			  'iconsmind-Friendster' => 'iconsmind-Friendster',
			  'iconsmind-Furl' => 'iconsmind-Furl',
			  'iconsmind-Google' => 'iconsmind-Google',
			  'iconsmind-Google-Buzz' => 'iconsmind-Google-Buzz',
			  'iconsmind-Google-Plus' => 'iconsmind-Google-Plus',
			  'iconsmind-Gowalla' => 'iconsmind-Gowalla',
			  'iconsmind-ICQ' => 'iconsmind-ICQ',
			  'iconsmind-ImDB' => 'iconsmind-ImDB',
			  'iconsmind-Instagram' => 'iconsmind-Instagram',
			  'iconsmind-Last-FM' => 'iconsmind-Last-FM',
			  'iconsmind-Like' => 'iconsmind-Like',
			  'iconsmind-Like-2' => 'iconsmind-Like-2',
			  'iconsmind-Linkedin' => 'iconsmind-Linkedin',
			  'iconsmind-Linkedin-2' => 'iconsmind-Linkedin-2',
			  'iconsmind-Livejournal' => 'iconsmind-Livejournal',
			  'iconsmind-Metacafe' => 'iconsmind-Metacafe',
			  'iconsmind-Mixx' => 'iconsmind-Mixx',
			  'iconsmind-Myspace' => 'iconsmind-Myspace',
			  'iconsmind-Newsvine' => 'iconsmind-Newsvine',
			  'iconsmind-Orkut' => 'iconsmind-Orkut',
			  'iconsmind-Picasa' => 'iconsmind-Picasa',
			  'iconsmind-Pinterest' => 'iconsmind-Pinterest',
			  'iconsmind-Plaxo' => 'iconsmind-Plaxo',
			  'iconsmind-Plurk' => 'iconsmind-Plurk',
			  'iconsmind-Posterous' => 'iconsmind-Posterous',
			  'iconsmind-QIK' => 'iconsmind-QIK',
			  'iconsmind-Reddit' => 'iconsmind-Reddit',
			  'iconsmind-Reverbnation' => 'iconsmind-Reverbnation',
			  'iconsmind-RSS' => 'iconsmind-RSS',
			  'iconsmind-Sharethis' => 'iconsmind-Sharethis',
			  'iconsmind-Shoutwire' => 'iconsmind-Shoutwire',
			  'iconsmind-Skype' => 'iconsmind-Skype',
			  'iconsmind-Soundcloud' => 'iconsmind-Soundcloud',
			  'iconsmind-Spurl' => 'iconsmind-Spurl',
			  'iconsmind-Stumbleupon' => 'iconsmind-Stumbleupon',
			  'iconsmind-Technorati' => 'iconsmind-Technorati',
			  'iconsmind-Tumblr' => 'iconsmind-Tumblr',
			  'iconsmind-Twitter' => 'iconsmind-Twitter',
			  'iconsmind-Twitter-2' => 'iconsmind-Twitter-2',
			  'iconsmind-Unlike' => 'iconsmind-Unlike',
			  'iconsmind-Unlike-2' => 'iconsmind-Unlike-2',
			  'iconsmind-Ustream' => 'iconsmind-Ustream',
			  'iconsmind-Viddler' => 'iconsmind-Viddler',
			  'iconsmind-Vimeo' => 'iconsmind-Vimeo',
			  'iconsmind-Wordpress' => 'iconsmind-Wordpress',
			  'iconsmind-Xanga' => 'iconsmind-Xanga',
			  'iconsmind-Xing' => 'iconsmind-Xing',
			  'iconsmind-Yahoo' => 'iconsmind-Yahoo',
			  'iconsmind-Yahoo-Buzz' => 'iconsmind-Yahoo-Buzz',
			  'iconsmind-Yelp' => 'iconsmind-Yelp',
			  'iconsmind-Youtube' => 'iconsmind-Youtube',
			  'iconsmind-Zootool' => 'iconsmind-Zootool',
			  'iconsmind-Bisexual' => 'iconsmind-Bisexual',
			  'iconsmind-Cancer2' => 'iconsmind-Cancer2',
			  'iconsmind-Couple-Sign' => 'iconsmind-Couple-Sign',
			  'iconsmind-David-Star' => 'iconsmind-David-Star',
			  'iconsmind-Family-Sign' => 'iconsmind-Family-Sign',
			  'iconsmind-Female-2' => 'iconsmind-Female-2',
			  'iconsmind-Gey' => 'iconsmind-Gey',
			  'iconsmind-Heart' => 'iconsmind-Heart',
			  'iconsmind-Homosexual' => 'iconsmind-Homosexual',
			  'iconsmind-Inifity' => 'iconsmind-Inifity',
			  'iconsmind-Lesbian' => 'iconsmind-Lesbian',
			  'iconsmind-Lesbians' => 'iconsmind-Lesbians',
			  'iconsmind-Love' => 'iconsmind-Love',
			  'iconsmind-Male-2' => 'iconsmind-Male-2',
			  'iconsmind-Men' => 'iconsmind-Men',
			  'iconsmind-No-Smoking' => 'iconsmind-No-Smoking',
			  'iconsmind-Paw' => 'iconsmind-Paw',
			  'iconsmind-Quotes' => 'iconsmind-Quotes',
			  'iconsmind-Quotes-2' => 'iconsmind-Quotes-2',
			  'iconsmind-Redirect' => 'iconsmind-Redirect',
			  'iconsmind-Retweet' => 'iconsmind-Retweet',
			  'iconsmind-Ribbon' => 'iconsmind-Ribbon',
			  'iconsmind-Ribbon-2' => 'iconsmind-Ribbon-2',
			  'iconsmind-Ribbon-3' => 'iconsmind-Ribbon-3',
			  'iconsmind-Sexual' => 'iconsmind-Sexual',
			  'iconsmind-Smoking-Area' => 'iconsmind-Smoking-Area',
			  'iconsmind-Trace' => 'iconsmind-Trace',
			  'iconsmind-Venn-Diagram' => 'iconsmind-Venn-Diagram',
			  'iconsmind-Wheelchair' => 'iconsmind-Wheelchair',
			  'iconsmind-Women' => 'iconsmind-Women',
			  'iconsmind-Ying-Yang' => 'iconsmind-Ying-Yang',
			  'iconsmind-Add-Bag' => 'iconsmind-Add-Bag',
			  'iconsmind-Add-Basket' => 'iconsmind-Add-Basket',
			  'iconsmind-Add-Cart' => 'iconsmind-Add-Cart',
			  'iconsmind-Bag-Coins' => 'iconsmind-Bag-Coins',
			  'iconsmind-Bag-Items' => 'iconsmind-Bag-Items',
			  'iconsmind-Bag-Quantity' => 'iconsmind-Bag-Quantity',
			  'iconsmind-Bar-Code' => 'iconsmind-Bar-Code',
			  'iconsmind-Basket-Coins' => 'iconsmind-Basket-Coins',
			  'iconsmind-Basket-Items' => 'iconsmind-Basket-Items',
			  'iconsmind-Basket-Quantity' => 'iconsmind-Basket-Quantity',
			  'iconsmind-Bitcoin' => 'iconsmind-Bitcoin',
			  'iconsmind-Car-Coins' => 'iconsmind-Car-Coins',
			  'iconsmind-Car-Items' => 'iconsmind-Car-Items',
			  'iconsmind-CartQuantity' => 'iconsmind-CartQuantity',
			  'iconsmind-Cash-Register' => 'iconsmind-Cash-Register',
			  'iconsmind-Cash-register2' => 'iconsmind-Cash-register2',
			  'iconsmind-Checkout' => 'iconsmind-Checkout',
			  'iconsmind-Checkout-Bag' => 'iconsmind-Checkout-Bag',
			  'iconsmind-Checkout-Basket' => 'iconsmind-Checkout-Basket',
			  'iconsmind-Full-Basket' => 'iconsmind-Full-Basket',
			  'iconsmind-Full-Cart' => 'iconsmind-Full-Cart',
			  'iconsmind-Fyll-Bag' => 'iconsmind-Fyll-Bag',
			  'iconsmind-Home' => 'iconsmind-Home',
			  'iconsmind-Password-2shopping' => 'iconsmind-Password-2shopping',
			  'iconsmind-Password-shopping' => 'iconsmind-Password-shopping',
			  'iconsmind-QR-Code' => 'iconsmind-QR-Code',
			  'iconsmind-Receipt' => 'iconsmind-Receipt',
			  'iconsmind-Receipt-2' => 'iconsmind-Receipt-2',
			  'iconsmind-Receipt-3' => 'iconsmind-Receipt-3',
			  'iconsmind-Receipt-4' => 'iconsmind-Receipt-4',
			  'iconsmind-Remove-Bag' => 'iconsmind-Remove-Bag',
			  'iconsmind-Remove-Basket' => 'iconsmind-Remove-Basket',
			  'iconsmind-Remove-Cart' => 'iconsmind-Remove-Cart',
			  'iconsmind-Shop' => 'iconsmind-Shop',
			  'iconsmind-Shop-2' => 'iconsmind-Shop-2',
			  'iconsmind-Shop-3' => 'iconsmind-Shop-3',
			  'iconsmind-Shop-4' => 'iconsmind-Shop-4',
			  'iconsmind-Shopping-Bag' => 'iconsmind-Shopping-Bag',
			  'iconsmind-Shopping-Basket' => 'iconsmind-Shopping-Basket',
			  'iconsmind-Shopping-Cart' => 'iconsmind-Shopping-Cart',
			  'iconsmind-Tag-2' => 'iconsmind-Tag-2',
			  'iconsmind-Tag-3' => 'iconsmind-Tag-3',
			  'iconsmind-Tag-4' => 'iconsmind-Tag-4',
			  'iconsmind-Tag-5' => 'iconsmind-Tag-5',
			  'iconsmind-This-SideUp' => 'iconsmind-This-SideUp',
			  'iconsmind-Broke-Link2' => 'iconsmind-Broke-Link2',
			  'iconsmind-Coding' => 'iconsmind-Coding',
			  'iconsmind-Consulting' => 'iconsmind-Consulting',
			  'iconsmind-Copyright' => 'iconsmind-Copyright',
			  'iconsmind-Idea-2' => 'iconsmind-Idea-2',
			  'iconsmind-Idea-3' => 'iconsmind-Idea-3',
			  'iconsmind-Idea-4' => 'iconsmind-Idea-4',
			  'iconsmind-Idea-5' => 'iconsmind-Idea-5',
			  'iconsmind-Internet' => 'iconsmind-Internet',
			  'iconsmind-Internet-2' => 'iconsmind-Internet-2',
			  'iconsmind-Link-2' => 'iconsmind-Link-2',
			  'iconsmind-Management' => 'iconsmind-Management',
			  'iconsmind-Monitor-Analytics' => 'iconsmind-Monitor-Analytics',
			  'iconsmind-Monitoring' => 'iconsmind-Monitoring',
			  'iconsmind-Optimization' => 'iconsmind-Optimization',
			  'iconsmind-Search-People' => 'iconsmind-Search-People',
			  'iconsmind-Tag' => 'iconsmind-Tag',
			  'iconsmind-Target' => 'iconsmind-Target',
			  'iconsmind-Target-Market' => 'iconsmind-Target-Market',
			  'iconsmind-Testimonal' => 'iconsmind-Testimonal',
			  'iconsmind-Computer-Secure' => 'iconsmind-Computer-Secure',
			  'iconsmind-Eye-Scan' => 'iconsmind-Eye-Scan',
			  'iconsmind-Finger-Print' => 'iconsmind-Finger-Print',
			  'iconsmind-Firewall' => 'iconsmind-Firewall',
			  'iconsmind-Key-Lock' => 'iconsmind-Key-Lock',
			  'iconsmind-Laptop-Secure' => 'iconsmind-Laptop-Secure',
			  'iconsmind-Layer-1532' => 'iconsmind-Layer-1532',
			  'iconsmind-Lock' => 'iconsmind-Lock',
			  'iconsmind-Lock-2' => 'iconsmind-Lock-2',
			  'iconsmind-Lock-3' => 'iconsmind-Lock-3',
			  'iconsmind-Password' => 'iconsmind-Password',
			  'iconsmind-Password-Field' => 'iconsmind-Password-Field',
			  'iconsmind-Police' => 'iconsmind-Police',
			  'iconsmind-Safe-Box' => 'iconsmind-Safe-Box',
			  'iconsmind-Security-Block' => 'iconsmind-Security-Block',
			  'iconsmind-Security-Bug' => 'iconsmind-Security-Bug',
			  'iconsmind-Security-Camera' => 'iconsmind-Security-Camera',
			  'iconsmind-Security-Check' => 'iconsmind-Security-Check',
			  'iconsmind-Security-Settings' => 'iconsmind-Security-Settings',
			  'iconsmind-Securiy-Remove' => 'iconsmind-Securiy-Remove',
			  'iconsmind-Shield' => 'iconsmind-Shield',
			  'iconsmind-Smartphone-Secure' => 'iconsmind-Smartphone-Secure',
			  'iconsmind-SSL' => 'iconsmind-SSL',
			  'iconsmind-Tablet-Secure' => 'iconsmind-Tablet-Secure',
			  'iconsmind-Type-Pass' => 'iconsmind-Type-Pass',
			  'iconsmind-Unlock' => 'iconsmind-Unlock',
			  'iconsmind-Unlock-2' => 'iconsmind-Unlock-2',
			  'iconsmind-Unlock-3' => 'iconsmind-Unlock-3',
			  'iconsmind-Ambulance' => 'iconsmind-Ambulance',
			  'iconsmind-Astronaut' => 'iconsmind-Astronaut',
			  'iconsmind-Atom' => 'iconsmind-Atom',
			  'iconsmind-Bacteria' => 'iconsmind-Bacteria',
			  'iconsmind-Band-Aid' => 'iconsmind-Band-Aid',
			  'iconsmind-Bio-Hazard' => 'iconsmind-Bio-Hazard',
			  'iconsmind-Biotech' => 'iconsmind-Biotech',
			  'iconsmind-Brain' => 'iconsmind-Brain',
			  'iconsmind-Chemical' => 'iconsmind-Chemical',
			  'iconsmind-Chemical-2' => 'iconsmind-Chemical-2',
			  'iconsmind-Chemical-3' => 'iconsmind-Chemical-3',
			  'iconsmind-Chemical-4' => 'iconsmind-Chemical-4',
			  'iconsmind-Chemical-5' => 'iconsmind-Chemical-5',
			  'iconsmind-Clinic' => 'iconsmind-Clinic',
			  'iconsmind-Cube-Molecule' => 'iconsmind-Cube-Molecule',
			  'iconsmind-Cube-Molecule2' => 'iconsmind-Cube-Molecule2',
			  'iconsmind-Danger' => 'iconsmind-Danger',
			  'iconsmind-Danger-2' => 'iconsmind-Danger-2',
			  'iconsmind-DNA' => 'iconsmind-DNA',
			  'iconsmind-DNA-2' => 'iconsmind-DNA-2',
			  'iconsmind-DNA-Helix' => 'iconsmind-DNA-Helix',
			  'iconsmind-First-Aid' => 'iconsmind-First-Aid',
			  'iconsmind-Flask' => 'iconsmind-Flask',
			  'iconsmind-Flask-2' => 'iconsmind-Flask-2',
			  'iconsmind-Helix-2' => 'iconsmind-Helix-2',
			  'iconsmind-Hospital' => 'iconsmind-Hospital',
			  'iconsmind-Hurt' => 'iconsmind-Hurt',
			  'iconsmind-Medical-Sign' => 'iconsmind-Medical-Sign',
			  'iconsmind-Medicine' => 'iconsmind-Medicine',
			  'iconsmind-Medicine-2' => 'iconsmind-Medicine-2',
			  'iconsmind-Medicine-3' => 'iconsmind-Medicine-3',
			  'iconsmind-Microscope' => 'iconsmind-Microscope',
			  'iconsmind-Neutron' => 'iconsmind-Neutron',
			  'iconsmind-Nuclear' => 'iconsmind-Nuclear',
			  'iconsmind-Physics' => 'iconsmind-Physics',
			  'iconsmind-Plasmid' => 'iconsmind-Plasmid',
			  'iconsmind-Plaster' => 'iconsmind-Plaster',
			  'iconsmind-Pulse' => 'iconsmind-Pulse',
			  'iconsmind-Radioactive' => 'iconsmind-Radioactive',
			  'iconsmind-Safety-PinClose' => 'iconsmind-Safety-PinClose',
			  'iconsmind-Safety-PinOpen' => 'iconsmind-Safety-PinOpen',
			  'iconsmind-Spermium' => 'iconsmind-Spermium',
			  'iconsmind-Stethoscope' => 'iconsmind-Stethoscope',
			  'iconsmind-Temperature2' => 'iconsmind-Temperature2',
			  'iconsmind-Test-Tube' => 'iconsmind-Test-Tube',
			  'iconsmind-Test-Tube2' => 'iconsmind-Test-Tube2',
			  'iconsmind-Virus' => 'iconsmind-Virus',
			  'iconsmind-Virus-2' => 'iconsmind-Virus-2',
			  'iconsmind-Virus-3' => 'iconsmind-Virus-3',
			  'iconsmind-X-ray' => 'iconsmind-X-ray',
			  'iconsmind-Auto-Flash' => 'iconsmind-Auto-Flash',
			  'iconsmind-Camera' => 'iconsmind-Camera',
			  'iconsmind-Camera-2' => 'iconsmind-Camera-2',
			  'iconsmind-Camera-3' => 'iconsmind-Camera-3',
			  'iconsmind-Camera-4' => 'iconsmind-Camera-4',
			  'iconsmind-Camera-5' => 'iconsmind-Camera-5',
			  'iconsmind-Camera-Back' => 'iconsmind-Camera-Back',
			  'iconsmind-Crop' => 'iconsmind-Crop',
			  'iconsmind-Daylight' => 'iconsmind-Daylight',
			  'iconsmind-Edit' => 'iconsmind-Edit',
			  'iconsmind-Eye' => 'iconsmind-Eye',
			  'iconsmind-Film2' => 'iconsmind-Film2',
			  'iconsmind-Film-Cartridge' => 'iconsmind-Film-Cartridge',
			  'iconsmind-Filter' => 'iconsmind-Filter',
			  'iconsmind-Flash' => 'iconsmind-Flash',
			  'iconsmind-Flash-2' => 'iconsmind-Flash-2',
			  'iconsmind-Fluorescent' => 'iconsmind-Fluorescent',
			  'iconsmind-Gopro' => 'iconsmind-Gopro',
			  'iconsmind-Landscape' => 'iconsmind-Landscape',
			  'iconsmind-Len' => 'iconsmind-Len',
			  'iconsmind-Len-2' => 'iconsmind-Len-2',
			  'iconsmind-Len-3' => 'iconsmind-Len-3',
			  'iconsmind-Macro' => 'iconsmind-Macro',
			  'iconsmind-Memory-Card' => 'iconsmind-Memory-Card',
			  'iconsmind-Memory-Card2' => 'iconsmind-Memory-Card2',
			  'iconsmind-Memory-Card3' => 'iconsmind-Memory-Card3',
			  'iconsmind-No-Flash' => 'iconsmind-No-Flash',
			  'iconsmind-Panorama' => 'iconsmind-Panorama',
			  'iconsmind-Photo' => 'iconsmind-Photo',
			  'iconsmind-Photo-2' => 'iconsmind-Photo-2',
			  'iconsmind-Photo-3' => 'iconsmind-Photo-3',
			  'iconsmind-Photo-Album' => 'iconsmind-Photo-Album',
			  'iconsmind-Photo-Album2' => 'iconsmind-Photo-Album2',
			  'iconsmind-Photo-Album3' => 'iconsmind-Photo-Album3',
			  'iconsmind-Photos' => 'iconsmind-Photos',
			  'iconsmind-Portrait' => 'iconsmind-Portrait',
			  'iconsmind-Retouching' => 'iconsmind-Retouching',
			  'iconsmind-Retro-Camera' => 'iconsmind-Retro-Camera',
			  'iconsmind-secound' => 'iconsmind-secound',
			  'iconsmind-secound2' => 'iconsmind-secound2',
			  'iconsmind-Selfie' => 'iconsmind-Selfie',
			  'iconsmind-Shutter' => 'iconsmind-Shutter',
			  'iconsmind-Signal' => 'iconsmind-Signal',
			  'iconsmind-Snow2' => 'iconsmind-Snow2',
			  'iconsmind-Sport-Mode' => 'iconsmind-Sport-Mode',
			  'iconsmind-Studio-Flash' => 'iconsmind-Studio-Flash',
			  'iconsmind-Studio-Lightbox' => 'iconsmind-Studio-Lightbox',
			  'iconsmind-Timer2' => 'iconsmind-Timer2',
			  'iconsmind-Tripod-2' => 'iconsmind-Tripod-2',
			  'iconsmind-Tripod-withCamera' => 'iconsmind-Tripod-withCamera',
			  'iconsmind-Tripod-withGopro' => 'iconsmind-Tripod-withGopro',
			  'iconsmind-Add-User' => 'iconsmind-Add-User',
			  'iconsmind-Add-UserStar' => 'iconsmind-Add-UserStar',
			  'iconsmind-Administrator' => 'iconsmind-Administrator',
			  'iconsmind-Alien' => 'iconsmind-Alien',
			  'iconsmind-Alien-2' => 'iconsmind-Alien-2',
			  'iconsmind-Assistant' => 'iconsmind-Assistant',
			  'iconsmind-Baby' => 'iconsmind-Baby',
			  'iconsmind-Baby-Cry' => 'iconsmind-Baby-Cry',
			  'iconsmind-Boy' => 'iconsmind-Boy',
			  'iconsmind-Business-Man' => 'iconsmind-Business-Man',
			  'iconsmind-Business-ManWoman' => 'iconsmind-Business-ManWoman',
			  'iconsmind-Business-Mens' => 'iconsmind-Business-Mens',
			  'iconsmind-Business-Woman' => 'iconsmind-Business-Woman',
			  'iconsmind-Checked-User' => 'iconsmind-Checked-User',
			  'iconsmind-Chef' => 'iconsmind-Chef',
			  'iconsmind-Conference' => 'iconsmind-Conference',
			  'iconsmind-Cool-Guy' => 'iconsmind-Cool-Guy',
			  'iconsmind-Criminal' => 'iconsmind-Criminal',
			  'iconsmind-Dj' => 'iconsmind-Dj',
			  'iconsmind-Doctor' => 'iconsmind-Doctor',
			  'iconsmind-Engineering' => 'iconsmind-Engineering',
			  'iconsmind-Farmer' => 'iconsmind-Farmer',
			  'iconsmind-Female' => 'iconsmind-Female',
			  'iconsmind-Female-22' => 'iconsmind-Female-22',
			  'iconsmind-Find-User' => 'iconsmind-Find-User',
			  'iconsmind-Geek' => 'iconsmind-Geek',
			  'iconsmind-Genius' => 'iconsmind-Genius',
			  'iconsmind-Girl' => 'iconsmind-Girl',
			  'iconsmind-Headphone' => 'iconsmind-Headphone',
			  'iconsmind-Headset' => 'iconsmind-Headset',
			  'iconsmind-ID-2' => 'iconsmind-ID-2',
			  'iconsmind-ID-3' => 'iconsmind-ID-3',
			  'iconsmind-ID-Card' => 'iconsmind-ID-Card',
			  'iconsmind-King-2' => 'iconsmind-King-2',
			  'iconsmind-Lock-User' => 'iconsmind-Lock-User',
			  'iconsmind-Love-User' => 'iconsmind-Love-User',
			  'iconsmind-Male' => 'iconsmind-Male',
			  'iconsmind-Male-22' => 'iconsmind-Male-22',
			  'iconsmind-MaleFemale' => 'iconsmind-MaleFemale',
			  'iconsmind-Man-Sign' => 'iconsmind-Man-Sign',
			  'iconsmind-Mens' => 'iconsmind-Mens',
			  'iconsmind-Network' => 'iconsmind-Network',
			  'iconsmind-Nurse' => 'iconsmind-Nurse',
			  'iconsmind-Pac-Man' => 'iconsmind-Pac-Man',
			  'iconsmind-Pilot' => 'iconsmind-Pilot',
			  'iconsmind-Police-Man' => 'iconsmind-Police-Man',
			  'iconsmind-Police-Woman' => 'iconsmind-Police-Woman',
			  'iconsmind-Professor' => 'iconsmind-Professor',
			  'iconsmind-Punker' => 'iconsmind-Punker',
			  'iconsmind-Queen-2' => 'iconsmind-Queen-2',
			  'iconsmind-Remove-User' => 'iconsmind-Remove-User',
			  'iconsmind-Robot' => 'iconsmind-Robot',
			  'iconsmind-Speak' => 'iconsmind-Speak',
			  'iconsmind-Speak-2' => 'iconsmind-Speak-2',
			  'iconsmind-Spy' => 'iconsmind-Spy',
			  'iconsmind-Student-Female' => 'iconsmind-Student-Female',
			  'iconsmind-Student-Male' => 'iconsmind-Student-Male',
			  'iconsmind-Student-MaleFemale' => 'iconsmind-Student-MaleFemale',
			  'iconsmind-Students' => 'iconsmind-Students',
			  'iconsmind-Superman' => 'iconsmind-Superman',
			  'iconsmind-Talk-Man' => 'iconsmind-Talk-Man',
			  'iconsmind-Teacher' => 'iconsmind-Teacher',
			  'iconsmind-Waiter' => 'iconsmind-Waiter',
			  'iconsmind-WomanMan' => 'iconsmind-WomanMan',
			  'iconsmind-Woman-Sign' => 'iconsmind-Woman-Sign',
			  'iconsmind-Wonder-Woman' => 'iconsmind-Wonder-Woman',
			  'iconsmind-Worker' => 'iconsmind-Worker',
			  'iconsmind-Anchor' => 'iconsmind-Anchor',
			  'iconsmind-Army-Key' => 'iconsmind-Army-Key',
			  'iconsmind-Balloon' => 'iconsmind-Balloon',
			  'iconsmind-Barricade' => 'iconsmind-Barricade',
			  'iconsmind-Batman-Mask' => 'iconsmind-Batman-Mask',
			  'iconsmind-Binocular' => 'iconsmind-Binocular',
			  'iconsmind-Boom' => 'iconsmind-Boom',
			  'iconsmind-Bucket' => 'iconsmind-Bucket',
			  'iconsmind-Button' => 'iconsmind-Button',
			  'iconsmind-Cannon' => 'iconsmind-Cannon',
			  'iconsmind-Chacked-Flag' => 'iconsmind-Chacked-Flag',
			  'iconsmind-Chair' => 'iconsmind-Chair',
			  'iconsmind-Coffee-Machine' => 'iconsmind-Coffee-Machine',
			  'iconsmind-Crown' => 'iconsmind-Crown',
			  'iconsmind-Crown-2' => 'iconsmind-Crown-2',
			  'iconsmind-Dice' => 'iconsmind-Dice',
			  'iconsmind-Dice-2' => 'iconsmind-Dice-2',
			  'iconsmind-Domino' => 'iconsmind-Domino',
			  'iconsmind-Door-Hanger' => 'iconsmind-Door-Hanger',
			  'iconsmind-Drill' => 'iconsmind-Drill',
			  'iconsmind-Feather' => 'iconsmind-Feather',
			  'iconsmind-Fire-Hydrant' => 'iconsmind-Fire-Hydrant',
			  'iconsmind-Flag' => 'iconsmind-Flag',
			  'iconsmind-Flag-2' => 'iconsmind-Flag-2',
			  'iconsmind-Flashlight' => 'iconsmind-Flashlight',
			  'iconsmind-Footprint2' => 'iconsmind-Footprint2',
			  'iconsmind-Gas-Pump' => 'iconsmind-Gas-Pump',
			  'iconsmind-Gift-Box' => 'iconsmind-Gift-Box',
			  'iconsmind-Gun' => 'iconsmind-Gun',
			  'iconsmind-Gun-2' => 'iconsmind-Gun-2',
			  'iconsmind-Gun-3' => 'iconsmind-Gun-3',
			  'iconsmind-Hammer' => 'iconsmind-Hammer',
			  'iconsmind-Identification-Badge' => 'iconsmind-Identification-Badge',
			  'iconsmind-Key' => 'iconsmind-Key',
			  'iconsmind-Key-2' => 'iconsmind-Key-2',
			  'iconsmind-Key-3' => 'iconsmind-Key-3',
			  'iconsmind-Lamp' => 'iconsmind-Lamp',
			  'iconsmind-Lego' => 'iconsmind-Lego',
			  'iconsmind-Life-Safer' => 'iconsmind-Life-Safer',
			  'iconsmind-Light-Bulb' => 'iconsmind-Light-Bulb',
			  'iconsmind-Light-Bulb2' => 'iconsmind-Light-Bulb2',
			  'iconsmind-Luggafe-Front' => 'iconsmind-Luggafe-Front',
			  'iconsmind-Luggage-2' => 'iconsmind-Luggage-2',
			  'iconsmind-Magic-Wand' => 'iconsmind-Magic-Wand',
			  'iconsmind-Magnet' => 'iconsmind-Magnet',
			  'iconsmind-Mask' => 'iconsmind-Mask',
			  'iconsmind-Menorah' => 'iconsmind-Menorah',
			  'iconsmind-Mirror' => 'iconsmind-Mirror',
			  'iconsmind-Movie-Ticket' => 'iconsmind-Movie-Ticket',
			  'iconsmind-Office-Lamp' => 'iconsmind-Office-Lamp',
			  'iconsmind-Paint-Brush' => 'iconsmind-Paint-Brush',
			  'iconsmind-Paint-Bucket' => 'iconsmind-Paint-Bucket',
			  'iconsmind-Paper-Plane' => 'iconsmind-Paper-Plane',
			  'iconsmind-Post-Sign' => 'iconsmind-Post-Sign',
			  'iconsmind-Post-Sign2ways' => 'iconsmind-Post-Sign2ways',
			  'iconsmind-Puzzle' => 'iconsmind-Puzzle',
			  'iconsmind-Razzor-Blade' => 'iconsmind-Razzor-Blade',
			  'iconsmind-Scale' => 'iconsmind-Scale',
			  'iconsmind-Screwdriver' => 'iconsmind-Screwdriver',
			  'iconsmind-Sewing-Machine' => 'iconsmind-Sewing-Machine',
			  'iconsmind-Sheriff-Badge' => 'iconsmind-Sheriff-Badge',
			  'iconsmind-Stroller' => 'iconsmind-Stroller',
			  'iconsmind-Suitcase' => 'iconsmind-Suitcase',
			  'iconsmind-Teddy-Bear' => 'iconsmind-Teddy-Bear',
			  'iconsmind-Telescope' => 'iconsmind-Telescope',
			  'iconsmind-Tent' => 'iconsmind-Tent',
			  'iconsmind-Thread' => 'iconsmind-Thread',
			  'iconsmind-Ticket' => 'iconsmind-Ticket',
			  'iconsmind-Time-Bomb' => 'iconsmind-Time-Bomb',
			  'iconsmind-Tourch' => 'iconsmind-Tourch',
			  'iconsmind-Vase' => 'iconsmind-Vase',
			  'iconsmind-Video-GameController' => 'iconsmind-Video-GameController',
			  'iconsmind-Conservation' => 'iconsmind-Conservation',
			  'iconsmind-Eci-Icon' => 'iconsmind-Eci-Icon',
			  'iconsmind-Environmental' => 'iconsmind-Environmental',
			  'iconsmind-Environmental-2' => 'iconsmind-Environmental-2',
			  'iconsmind-Environmental-3' => 'iconsmind-Environmental-3',
			  'iconsmind-Fire-Flame' => 'iconsmind-Fire-Flame',
			  'iconsmind-Fire-Flame2' => 'iconsmind-Fire-Flame2',
			  'iconsmind-Flowerpot' => 'iconsmind-Flowerpot',
			  'iconsmind-Forest' => 'iconsmind-Forest',
			  'iconsmind-Green-Energy' => 'iconsmind-Green-Energy',
			  'iconsmind-Green-House' => 'iconsmind-Green-House',
			  'iconsmind-Landscape2' => 'iconsmind-Landscape2',
			  'iconsmind-Leafs' => 'iconsmind-Leafs',
			  'iconsmind-Leafs-2' => 'iconsmind-Leafs-2',
			  'iconsmind-Light-BulbLeaf' => 'iconsmind-Light-BulbLeaf',
			  'iconsmind-Palm-Tree' => 'iconsmind-Palm-Tree',
			  'iconsmind-Plant' => 'iconsmind-Plant',
			  'iconsmind-Recycling' => 'iconsmind-Recycling',
			  'iconsmind-Recycling-2' => 'iconsmind-Recycling-2',
			  'iconsmind-Seed' => 'iconsmind-Seed',
			  'iconsmind-Trash-withMen' => 'iconsmind-Trash-withMen',
			  'iconsmind-Tree' => 'iconsmind-Tree',
			  'iconsmind-Tree-2' => 'iconsmind-Tree-2',
			  'iconsmind-Tree-3' => 'iconsmind-Tree-3',
			  'iconsmind-Audio' => 'iconsmind-Audio',
			  'iconsmind-Back-Music' => 'iconsmind-Back-Music',
			  'iconsmind-Bell' => 'iconsmind-Bell',
			  'iconsmind-Casette-Tape' => 'iconsmind-Casette-Tape',
			  'iconsmind-CD-2' => 'iconsmind-CD-2',
			  'iconsmind-CD-Cover' => 'iconsmind-CD-Cover',
			  'iconsmind-Cello' => 'iconsmind-Cello',
			  'iconsmind-Clef' => 'iconsmind-Clef',
			  'iconsmind-Drum' => 'iconsmind-Drum',
			  'iconsmind-Earphones' => 'iconsmind-Earphones',
			  'iconsmind-Earphones-2' => 'iconsmind-Earphones-2',
			  'iconsmind-Electric-Guitar' => 'iconsmind-Electric-Guitar',
			  'iconsmind-Equalizer' => 'iconsmind-Equalizer',
			  'iconsmind-First' => 'iconsmind-First',
			  'iconsmind-Guitar' => 'iconsmind-Guitar',
			  'iconsmind-Headphones' => 'iconsmind-Headphones',
			  'iconsmind-Keyboard3' => 'iconsmind-Keyboard3',
			  'iconsmind-Last' => 'iconsmind-Last',
			  'iconsmind-Loud' => 'iconsmind-Loud',
			  'iconsmind-Loudspeaker' => 'iconsmind-Loudspeaker',
			  'iconsmind-Mic' => 'iconsmind-Mic',
			  'iconsmind-Microphone' => 'iconsmind-Microphone',
			  'iconsmind-Microphone-2' => 'iconsmind-Microphone-2',
			  'iconsmind-Microphone-3' => 'iconsmind-Microphone-3',
			  'iconsmind-Microphone-4' => 'iconsmind-Microphone-4',
			  'iconsmind-Microphone-5' => 'iconsmind-Microphone-5',
			  'iconsmind-Microphone-6' => 'iconsmind-Microphone-6',
			  'iconsmind-Microphone-7' => 'iconsmind-Microphone-7',
			  'iconsmind-Mixer' => 'iconsmind-Mixer',
			  'iconsmind-Mp3-File' => 'iconsmind-Mp3-File',
			  'iconsmind-Music-Note' => 'iconsmind-Music-Note',
			  'iconsmind-Music-Note2' => 'iconsmind-Music-Note2',
			  'iconsmind-Music-Note3' => 'iconsmind-Music-Note3',
			  'iconsmind-Music-Note4' => 'iconsmind-Music-Note4',
			  'iconsmind-Music-Player' => 'iconsmind-Music-Player',
			  'iconsmind-Mute' => 'iconsmind-Mute',
			  'iconsmind-Next-Music' => 'iconsmind-Next-Music',
			  'iconsmind-Old-Radio' => 'iconsmind-Old-Radio',
			  'iconsmind-On-Air' => 'iconsmind-On-Air',
			  'iconsmind-Piano' => 'iconsmind-Piano',
			  'iconsmind-Play-Music' => 'iconsmind-Play-Music',
			  'iconsmind-Radio' => 'iconsmind-Radio',
			  'iconsmind-Record' => 'iconsmind-Record',
			  'iconsmind-Record-Music' => 'iconsmind-Record-Music',
			  'iconsmind-Rock-andRoll' => 'iconsmind-Rock-andRoll',
			  'iconsmind-Saxophone' => 'iconsmind-Saxophone',
			  'iconsmind-Sound' => 'iconsmind-Sound',
			  'iconsmind-Sound-Wave' => 'iconsmind-Sound-Wave',
			  'iconsmind-Speaker' => 'iconsmind-Speaker',
			  'iconsmind-Stop-Music' => 'iconsmind-Stop-Music',
			  'iconsmind-Trumpet' => 'iconsmind-Trumpet',
			  'iconsmind-Voice' => 'iconsmind-Voice',
			  'iconsmind-Volume-Down' => 'iconsmind-Volume-Down',
			  'iconsmind-Volume-Up' => 'iconsmind-Volume-Up',
			  'iconsmind-Back' => 'iconsmind-Back',
			  'iconsmind-Back-2' => 'iconsmind-Back-2',
			  'iconsmind-Eject' => 'iconsmind-Eject',
			  'iconsmind-Eject-2' => 'iconsmind-Eject-2',
			  'iconsmind-End' => 'iconsmind-End',
			  'iconsmind-End-2' => 'iconsmind-End-2',
			  'iconsmind-Next' => 'iconsmind-Next',
			  'iconsmind-Next-2' => 'iconsmind-Next-2',
			  'iconsmind-Pause' => 'iconsmind-Pause',
			  'iconsmind-Pause-2' => 'iconsmind-Pause-2',
			  'iconsmind-Power-2' => 'iconsmind-Power-2',
			  'iconsmind-Power-3' => 'iconsmind-Power-3',
			  'iconsmind-Record2' => 'iconsmind-Record2',
			  'iconsmind-Record-2' => 'iconsmind-Record-2',
			  'iconsmind-Repeat' => 'iconsmind-Repeat',
			  'iconsmind-Repeat-2' => 'iconsmind-Repeat-2',
			  'iconsmind-Shuffle' => 'iconsmind-Shuffle',
			  'iconsmind-Shuffle-2' => 'iconsmind-Shuffle-2',
			  'iconsmind-Start' => 'iconsmind-Start',
			  'iconsmind-Start-2' => 'iconsmind-Start-2',
			  'iconsmind-Stop' => 'iconsmind-Stop',
			  'iconsmind-Stop-2' => 'iconsmind-Stop-2',
			  'iconsmind-Compass' => 'iconsmind-Compass',
			  'iconsmind-Compass-2' => 'iconsmind-Compass-2',
			  'iconsmind-Compass-Rose' => 'iconsmind-Compass-Rose',
			  'iconsmind-Direction-East' => 'iconsmind-Direction-East',
			  'iconsmind-Direction-North' => 'iconsmind-Direction-North',
			  'iconsmind-Direction-South' => 'iconsmind-Direction-South',
			  'iconsmind-Direction-West' => 'iconsmind-Direction-West',
			  'iconsmind-Edit-Map' => 'iconsmind-Edit-Map',
			  'iconsmind-Geo' => 'iconsmind-Geo',
			  'iconsmind-Geo2' => 'iconsmind-Geo2',
			  'iconsmind-Geo3' => 'iconsmind-Geo3',
			  'iconsmind-Geo22' => 'iconsmind-Geo22',
			  'iconsmind-Geo23' => 'iconsmind-Geo23',
			  'iconsmind-Geo24' => 'iconsmind-Geo24',
			  'iconsmind-Geo2-Close' => 'iconsmind-Geo2-Close',
			  'iconsmind-Geo2-Love' => 'iconsmind-Geo2-Love',
			  'iconsmind-Geo2-Number' => 'iconsmind-Geo2-Number',
			  'iconsmind-Geo2-Star' => 'iconsmind-Geo2-Star',
			  'iconsmind-Geo32' => 'iconsmind-Geo32',
			  'iconsmind-Geo33' => 'iconsmind-Geo33',
			  'iconsmind-Geo34' => 'iconsmind-Geo34',
			  'iconsmind-Geo3-Close' => 'iconsmind-Geo3-Close',
			  'iconsmind-Geo3-Love' => 'iconsmind-Geo3-Love',
			  'iconsmind-Geo3-Number' => 'iconsmind-Geo3-Number',
			  'iconsmind-Geo3-Star' => 'iconsmind-Geo3-Star',
			  'iconsmind-Geo-Close' => 'iconsmind-Geo-Close',
			  'iconsmind-Geo-Love' => 'iconsmind-Geo-Love',
			  'iconsmind-Geo-Number' => 'iconsmind-Geo-Number',
			  'iconsmind-Geo-Star' => 'iconsmind-Geo-Star',
			  'iconsmind-Global-Position' => 'iconsmind-Global-Position',
			  'iconsmind-Globe' => 'iconsmind-Globe',
			  'iconsmind-Globe-2' => 'iconsmind-Globe-2',
			  'iconsmind-Location' => 'iconsmind-Location',
			  'iconsmind-Location-2' => 'iconsmind-Location-2',
			  'iconsmind-Map' => 'iconsmind-Map',
			  'iconsmind-Map2' => 'iconsmind-Map2',
			  'iconsmind-Map-Marker' => 'iconsmind-Map-Marker',
			  'iconsmind-Map-Marker2' => 'iconsmind-Map-Marker2',
			  'iconsmind-Map-Marker3' => 'iconsmind-Map-Marker3',
			  'iconsmind-Road2' => 'iconsmind-Road2',
			  'iconsmind-Satelite' => 'iconsmind-Satelite',
			  'iconsmind-Satelite-2' => 'iconsmind-Satelite-2',
			  'iconsmind-Street-View' => 'iconsmind-Street-View',
			  'iconsmind-Street-View2' => 'iconsmind-Street-View2',
			  'iconsmind-Android-Store' => 'iconsmind-Android-Store',
			  'iconsmind-Apple-Store' => 'iconsmind-Apple-Store',
			  'iconsmind-Box2' => 'iconsmind-Box2',
			  'iconsmind-Dropbox' => 'iconsmind-Dropbox',
			  'iconsmind-Google-Drive' => 'iconsmind-Google-Drive',
			  'iconsmind-Google-Play' => 'iconsmind-Google-Play',
			  'iconsmind-Paypal' => 'iconsmind-Paypal',
			  'iconsmind-Skrill' => 'iconsmind-Skrill',
			  'iconsmind-X-Box' => 'iconsmind-X-Box',
			  'iconsmind-Add' => 'iconsmind-Add',
			  'iconsmind-Back2' => 'iconsmind-Back2',
			  'iconsmind-Broken-Link' => 'iconsmind-Broken-Link',
			  'iconsmind-Check' => 'iconsmind-Check',
			  'iconsmind-Check-2' => 'iconsmind-Check-2',
			  'iconsmind-Circular-Point' => 'iconsmind-Circular-Point',
			  'iconsmind-Close' => 'iconsmind-Close',
			  'iconsmind-Cursor' => 'iconsmind-Cursor',
			  'iconsmind-Cursor-Click' => 'iconsmind-Cursor-Click',
			  'iconsmind-Cursor-Click2' => 'iconsmind-Cursor-Click2',
			  'iconsmind-Cursor-Move' => 'iconsmind-Cursor-Move',
			  'iconsmind-Cursor-Move2' => 'iconsmind-Cursor-Move2',
			  'iconsmind-Cursor-Select' => 'iconsmind-Cursor-Select',
			  'iconsmind-Down' => 'iconsmind-Down',
			  'iconsmind-Download' => 'iconsmind-Download',
			  'iconsmind-Downward' => 'iconsmind-Downward',
			  'iconsmind-Endways' => 'iconsmind-Endways',
			  'iconsmind-Forward' => 'iconsmind-Forward',
			  'iconsmind-Left' => 'iconsmind-Left',
			  'iconsmind-Link' => 'iconsmind-Link',
			  'iconsmind-Next2' => 'iconsmind-Next2',
			  'iconsmind-Orientation' => 'iconsmind-Orientation',
			  'iconsmind-Pointer' => 'iconsmind-Pointer',
			  'iconsmind-Previous' => 'iconsmind-Previous',
			  'iconsmind-Redo' => 'iconsmind-Redo',
			  'iconsmind-Refresh' => 'iconsmind-Refresh',
			  'iconsmind-Reload' => 'iconsmind-Reload',
			  'iconsmind-Remove' => 'iconsmind-Remove',
			  'iconsmind-Repeat2' => 'iconsmind-Repeat2',
			  'iconsmind-Reset' => 'iconsmind-Reset',
			  'iconsmind-Rewind' => 'iconsmind-Rewind',
			  'iconsmind-Right' => 'iconsmind-Right',
			  'iconsmind-Rotation' => 'iconsmind-Rotation',
			  'iconsmind-Rotation-390' => 'iconsmind-Rotation-390',
			  'iconsmind-Spot' => 'iconsmind-Spot',
			  'iconsmind-Start-ways' => 'iconsmind-Start-ways',
			  'iconsmind-Synchronize' => 'iconsmind-Synchronize',
			  'iconsmind-Synchronize-2' => 'iconsmind-Synchronize-2',
			  'iconsmind-Undo' => 'iconsmind-Undo',
			  'iconsmind-Up' => 'iconsmind-Up',
			  'iconsmind-Upload' => 'iconsmind-Upload',
			  'iconsmind-Upward' => 'iconsmind-Upward',
			  'iconsmind-Yes' => 'iconsmind-Yes',
			  'iconsmind-Barricade2' => 'iconsmind-Barricade2',
			  'iconsmind-Crane' => 'iconsmind-Crane',
			  'iconsmind-Dam' => 'iconsmind-Dam',
			  'iconsmind-Drill2' => 'iconsmind-Drill2',
			  'iconsmind-Electricity' => 'iconsmind-Electricity',
			  'iconsmind-Explode' => 'iconsmind-Explode',
			  'iconsmind-Factory' => 'iconsmind-Factory',
			  'iconsmind-Fuel' => 'iconsmind-Fuel',
			  'iconsmind-Helmet2' => 'iconsmind-Helmet2',
			  'iconsmind-Helmet-2' => 'iconsmind-Helmet-2',
			  'iconsmind-Laser' => 'iconsmind-Laser',
			  'iconsmind-Mine' => 'iconsmind-Mine',
			  'iconsmind-Oil' => 'iconsmind-Oil',
			  'iconsmind-Petrol' => 'iconsmind-Petrol',
			  'iconsmind-Pipe' => 'iconsmind-Pipe',
			  'iconsmind-Power-Station' => 'iconsmind-Power-Station',
			  'iconsmind-Refinery' => 'iconsmind-Refinery',
			  'iconsmind-Saw' => 'iconsmind-Saw',
			  'iconsmind-Shovel' => 'iconsmind-Shovel',
			  'iconsmind-Solar' => 'iconsmind-Solar',
			  'iconsmind-Wheelbarrow' => 'iconsmind-Wheelbarrow',
			  'iconsmind-Windmill' => 'iconsmind-Windmill',
			  'iconsmind-Aa' => 'iconsmind-Aa',
			  'iconsmind-Add-File' => 'iconsmind-Add-File',
			  'iconsmind-Address-Book' => 'iconsmind-Address-Book',
			  'iconsmind-Address-Book2' => 'iconsmind-Address-Book2',
			  'iconsmind-Add-SpaceAfterParagraph' => 'iconsmind-Add-SpaceAfterParagraph',
			  'iconsmind-Add-SpaceBeforeParagraph' => 'iconsmind-Add-SpaceBeforeParagraph',
			  'iconsmind-Airbrush' => 'iconsmind-Airbrush',
			  'iconsmind-Aligator' => 'iconsmind-Aligator',
			  'iconsmind-Align-Center' => 'iconsmind-Align-Center',
			  'iconsmind-Align-JustifyAll' => 'iconsmind-Align-JustifyAll',
			  'iconsmind-Align-JustifyCenter' => 'iconsmind-Align-JustifyCenter',
			  'iconsmind-Align-JustifyLeft' => 'iconsmind-Align-JustifyLeft',
			  'iconsmind-Align-JustifyRight' => 'iconsmind-Align-JustifyRight',
			  'iconsmind-Align-Left' => 'iconsmind-Align-Left',
			  'iconsmind-Align-Right' => 'iconsmind-Align-Right',
			  'iconsmind-Alpha' => 'iconsmind-Alpha',
			  'iconsmind-AMX' => 'iconsmind-AMX',
			  'iconsmind-Anchor2' => 'iconsmind-Anchor2',
			  'iconsmind-Android' => 'iconsmind-Android',
			  'iconsmind-Angel' => 'iconsmind-Angel',
			  'iconsmind-Angel-Smiley' => 'iconsmind-Angel-Smiley',
			  'iconsmind-Angry' => 'iconsmind-Angry',
			  'iconsmind-Apple' => 'iconsmind-Apple',
			  'iconsmind-Apple-Bite' => 'iconsmind-Apple-Bite',
			  'iconsmind-Argentina' => 'iconsmind-Argentina',
			  'iconsmind-Arrow-Around' => 'iconsmind-Arrow-Around',
			  'iconsmind-Arrow-Back' => 'iconsmind-Arrow-Back',
			  'iconsmind-Arrow-Back2' => 'iconsmind-Arrow-Back2',
			  'iconsmind-Arrow-Back3' => 'iconsmind-Arrow-Back3',
			  'iconsmind-Arrow-Barrier' => 'iconsmind-Arrow-Barrier',
			  'iconsmind-Arrow-Circle' => 'iconsmind-Arrow-Circle',
			  'iconsmind-Arrow-Cross' => 'iconsmind-Arrow-Cross',
			  'iconsmind-Arrow-Down' => 'iconsmind-Arrow-Down',
			  'iconsmind-Arrow-Down2' => 'iconsmind-Arrow-Down2',
			  'iconsmind-Arrow-Down3' => 'iconsmind-Arrow-Down3',
			  'iconsmind-Arrow-DowninCircle' => 'iconsmind-Arrow-DowninCircle',
			  'iconsmind-Arrow-Fork' => 'iconsmind-Arrow-Fork',
			  'iconsmind-Arrow-Forward' => 'iconsmind-Arrow-Forward',
			  'iconsmind-Arrow-Forward2' => 'iconsmind-Arrow-Forward2',
			  'iconsmind-Arrow-From' => 'iconsmind-Arrow-From',
			  'iconsmind-Arrow-Inside' => 'iconsmind-Arrow-Inside',
			  'iconsmind-Arrow-Inside45' => 'iconsmind-Arrow-Inside45',
			  'iconsmind-Arrow-InsideGap' => 'iconsmind-Arrow-InsideGap',
			  'iconsmind-Arrow-InsideGap45' => 'iconsmind-Arrow-InsideGap45',
			  'iconsmind-Arrow-Into' => 'iconsmind-Arrow-Into',
			  'iconsmind-Arrow-Join' => 'iconsmind-Arrow-Join',
			  'iconsmind-Arrow-Junction' => 'iconsmind-Arrow-Junction',
			  'iconsmind-Arrow-Left' => 'iconsmind-Arrow-Left',
			  'iconsmind-Arrow-Left2' => 'iconsmind-Arrow-Left2',
			  'iconsmind-Arrow-LeftinCircle' => 'iconsmind-Arrow-LeftinCircle',
			  'iconsmind-Arrow-Loop' => 'iconsmind-Arrow-Loop',
			  'iconsmind-Arrow-Merge' => 'iconsmind-Arrow-Merge',
			  'iconsmind-Arrow-Mix' => 'iconsmind-Arrow-Mix',
			  'iconsmind-Arrow-Next' => 'iconsmind-Arrow-Next',
			  'iconsmind-Arrow-OutLeft' => 'iconsmind-Arrow-OutLeft',
			  'iconsmind-Arrow-OutRight' => 'iconsmind-Arrow-OutRight',
			  'iconsmind-Arrow-Outside' => 'iconsmind-Arrow-Outside',
			  'iconsmind-Arrow-Outside45' => 'iconsmind-Arrow-Outside45',
			  'iconsmind-Arrow-OutsideGap' => 'iconsmind-Arrow-OutsideGap',
			  'iconsmind-Arrow-OutsideGap45' => 'iconsmind-Arrow-OutsideGap45',
			  'iconsmind-Arrow-Over' => 'iconsmind-Arrow-Over',
			  'iconsmind-Arrow-Refresh' => 'iconsmind-Arrow-Refresh',
			  'iconsmind-Arrow-Refresh2' => 'iconsmind-Arrow-Refresh2',
			  'iconsmind-Arrow-Right' => 'iconsmind-Arrow-Right',
			  'iconsmind-Arrow-Right2' => 'iconsmind-Arrow-Right2',
			  'iconsmind-Arrow-RightinCircle' => 'iconsmind-Arrow-RightinCircle',
			  'iconsmind-Arrow-Shuffle' => 'iconsmind-Arrow-Shuffle',
			  'iconsmind-Arrow-Squiggly' => 'iconsmind-Arrow-Squiggly',
			  'iconsmind-Arrow-Through' => 'iconsmind-Arrow-Through',
			  'iconsmind-Arrow-To' => 'iconsmind-Arrow-To',
			  'iconsmind-Arrow-TurnLeft' => 'iconsmind-Arrow-TurnLeft',
			  'iconsmind-Arrow-TurnRight' => 'iconsmind-Arrow-TurnRight',
			  'iconsmind-Arrow-Up' => 'iconsmind-Arrow-Up',
			  'iconsmind-Arrow-Up2' => 'iconsmind-Arrow-Up2',
			  'iconsmind-Arrow-Up3' => 'iconsmind-Arrow-Up3',
			  'iconsmind-Arrow-UpinCircle' => 'iconsmind-Arrow-UpinCircle',
			  'iconsmind-Arrow-XLeft' => 'iconsmind-Arrow-XLeft',
			  'iconsmind-Arrow-XRight' => 'iconsmind-Arrow-XRight',
			  'iconsmind-ATM' => 'iconsmind-ATM',
			  'iconsmind-At-Sign' => 'iconsmind-At-Sign',
			  'iconsmind-Baby-Clothes' => 'iconsmind-Baby-Clothes',
			  'iconsmind-Baby-Clothes2' => 'iconsmind-Baby-Clothes2',
			  'iconsmind-Bag' => 'iconsmind-Bag',
			  'iconsmind-Bakelite' => 'iconsmind-Bakelite',
			  'iconsmind-Banana' => 'iconsmind-Banana',
			  'iconsmind-Bank' => 'iconsmind-Bank',
			  'iconsmind-Bar-Chart' => 'iconsmind-Bar-Chart',
			  'iconsmind-Bar-Chart2' => 'iconsmind-Bar-Chart2',
			  'iconsmind-Bar-Chart3' => 'iconsmind-Bar-Chart3',
			  'iconsmind-Bar-Chart4' => 'iconsmind-Bar-Chart4',
			  'iconsmind-Bar-Chart5' => 'iconsmind-Bar-Chart5',
			  'iconsmind-Bat' => 'iconsmind-Bat',
			  'iconsmind-Bathrobe' => 'iconsmind-Bathrobe',
			  'iconsmind-Battery-0' => 'iconsmind-Battery-0',
			  'iconsmind-Battery-25' => 'iconsmind-Battery-25',
			  'iconsmind-Battery-50' => 'iconsmind-Battery-50',
			  'iconsmind-Battery-75' => 'iconsmind-Battery-75',
			  'iconsmind-Battery-100' => 'iconsmind-Battery-100',
			  'iconsmind-Battery-Charge' => 'iconsmind-Battery-Charge',
			  'iconsmind-Bear' => 'iconsmind-Bear',
			  'iconsmind-Beard' => 'iconsmind-Beard',
			  'iconsmind-Beard-2' => 'iconsmind-Beard-2',
			  'iconsmind-Beard-3' => 'iconsmind-Beard-3',
			  'iconsmind-Bee' => 'iconsmind-Bee',
			  'iconsmind-Beer' => 'iconsmind-Beer',
			  'iconsmind-Beer-Glass' => 'iconsmind-Beer-Glass',
			  'iconsmind-Bell2' => 'iconsmind-Bell2',
			  'iconsmind-Belt' => 'iconsmind-Belt',
			  'iconsmind-Belt-2' => 'iconsmind-Belt-2',
			  'iconsmind-Belt-3' => 'iconsmind-Belt-3',
			  'iconsmind-Berlin-Tower' => 'iconsmind-Berlin-Tower',
			  'iconsmind-Beta' => 'iconsmind-Beta',
			  'iconsmind-Big-Bang' => 'iconsmind-Big-Bang',
			  'iconsmind-Big-Data' => 'iconsmind-Big-Data',
			  'iconsmind-Bikini' => 'iconsmind-Bikini',
			  'iconsmind-Bilk-Bottle2' => 'iconsmind-Bilk-Bottle2',
			  'iconsmind-Bird' => 'iconsmind-Bird',
			  'iconsmind-Bird-DeliveringLetter' => 'iconsmind-Bird-DeliveringLetter',
			  'iconsmind-Birthday-Cake' => 'iconsmind-Birthday-Cake',
			  'iconsmind-Bishop' => 'iconsmind-Bishop',
			  'iconsmind-Blackboard' => 'iconsmind-Blackboard',
			  'iconsmind-Black-Cat' => 'iconsmind-Black-Cat',
			  'iconsmind-Block-Cloud' => 'iconsmind-Block-Cloud',
			  'iconsmind-Blood' => 'iconsmind-Blood',
			  'iconsmind-Blouse' => 'iconsmind-Blouse',
			  'iconsmind-Blueprint' => 'iconsmind-Blueprint',
			  'iconsmind-Board' => 'iconsmind-Board',
			  'iconsmind-Bone' => 'iconsmind-Bone',
			  'iconsmind-Bones' => 'iconsmind-Bones',
			  'iconsmind-Book' => 'iconsmind-Book',
			  'iconsmind-Bookmark' => 'iconsmind-Bookmark',
			  'iconsmind-Books' => 'iconsmind-Books',
			  'iconsmind-Books-2' => 'iconsmind-Books-2',
			  'iconsmind-Boot' => 'iconsmind-Boot',
			  'iconsmind-Boot-2' => 'iconsmind-Boot-2',
			  'iconsmind-Bottom-ToTop' => 'iconsmind-Bottom-ToTop',
			  'iconsmind-Bow' => 'iconsmind-Bow',
			  'iconsmind-Bow-2' => 'iconsmind-Bow-2',
			  'iconsmind-Bow-3' => 'iconsmind-Bow-3',
			  'iconsmind-Box-Close' => 'iconsmind-Box-Close',
			  'iconsmind-Box-Full' => 'iconsmind-Box-Full',
			  'iconsmind-Box-Open' => 'iconsmind-Box-Open',
			  'iconsmind-Box-withFolders' => 'iconsmind-Box-withFolders',
			  'iconsmind-Bra' => 'iconsmind-Bra',
			  'iconsmind-Brain2' => 'iconsmind-Brain2',
			  'iconsmind-Brain-2' => 'iconsmind-Brain-2',
			  'iconsmind-Brazil' => 'iconsmind-Brazil',
			  'iconsmind-Bread' => 'iconsmind-Bread',
			  'iconsmind-Bread-2' => 'iconsmind-Bread-2',
			  'iconsmind-Bridge' => 'iconsmind-Bridge',
			  'iconsmind-Broom' => 'iconsmind-Broom',
			  'iconsmind-Brush' => 'iconsmind-Brush',
			  'iconsmind-Bug' => 'iconsmind-Bug',
			  'iconsmind-Building' => 'iconsmind-Building',
			  'iconsmind-Butterfly' => 'iconsmind-Butterfly',
			  'iconsmind-Cake' => 'iconsmind-Cake',
			  'iconsmind-Calculator' => 'iconsmind-Calculator',
			  'iconsmind-Calculator-2' => 'iconsmind-Calculator-2',
			  'iconsmind-Calculator-3' => 'iconsmind-Calculator-3',
			  'iconsmind-Calendar' => 'iconsmind-Calendar',
			  'iconsmind-Calendar-2' => 'iconsmind-Calendar-2',
			  'iconsmind-Calendar-3' => 'iconsmind-Calendar-3',
			  'iconsmind-Calendar-4' => 'iconsmind-Calendar-4',
			  'iconsmind-Camel' => 'iconsmind-Camel',
			  'iconsmind-Can' => 'iconsmind-Can',
			  'iconsmind-Can-2' => 'iconsmind-Can-2',
			  'iconsmind-Canada' => 'iconsmind-Canada',
			  'iconsmind-Candle' => 'iconsmind-Candle',
			  'iconsmind-Candy' => 'iconsmind-Candy',
			  'iconsmind-Candy-Cane' => 'iconsmind-Candy-Cane',
			  'iconsmind-Cap' => 'iconsmind-Cap',
			  'iconsmind-Cap-2' => 'iconsmind-Cap-2',
			  'iconsmind-Cap-3' => 'iconsmind-Cap-3',
			  'iconsmind-Cardigan' => 'iconsmind-Cardigan',
			  'iconsmind-Cardiovascular' => 'iconsmind-Cardiovascular',
			  'iconsmind-Castle' => 'iconsmind-Castle',
			  'iconsmind-Cat' => 'iconsmind-Cat',
			  'iconsmind-Cathedral' => 'iconsmind-Cathedral',
			  'iconsmind-Cauldron' => 'iconsmind-Cauldron',
			  'iconsmind-CD' => 'iconsmind-CD',
			  'iconsmind-Charger' => 'iconsmind-Charger',
			  'iconsmind-Checkmate' => 'iconsmind-Checkmate',
			  'iconsmind-Cheese' => 'iconsmind-Cheese',
			  'iconsmind-Cheetah' => 'iconsmind-Cheetah',
			  'iconsmind-Chef-Hat' => 'iconsmind-Chef-Hat',
			  'iconsmind-Chef-Hat2' => 'iconsmind-Chef-Hat2',
			  'iconsmind-Chess-Board' => 'iconsmind-Chess-Board',
			  'iconsmind-Chicken' => 'iconsmind-Chicken',
			  'iconsmind-Chile' => 'iconsmind-Chile',
			  'iconsmind-Chimney' => 'iconsmind-Chimney',
			  'iconsmind-China' => 'iconsmind-China',
			  'iconsmind-Chinese-Temple' => 'iconsmind-Chinese-Temple',
			  'iconsmind-Chip' => 'iconsmind-Chip',
			  'iconsmind-Chopsticks' => 'iconsmind-Chopsticks',
			  'iconsmind-Chopsticks-2' => 'iconsmind-Chopsticks-2',
			  'iconsmind-Christmas' => 'iconsmind-Christmas',
			  'iconsmind-Christmas-Ball' => 'iconsmind-Christmas-Ball',
			  'iconsmind-Christmas-Bell' => 'iconsmind-Christmas-Bell',
			  'iconsmind-Christmas-Candle' => 'iconsmind-Christmas-Candle',
			  'iconsmind-Christmas-Hat' => 'iconsmind-Christmas-Hat',
			  'iconsmind-Christmas-Sleigh' => 'iconsmind-Christmas-Sleigh',
			  'iconsmind-Christmas-Snowman' => 'iconsmind-Christmas-Snowman',
			  'iconsmind-Christmas-Sock' => 'iconsmind-Christmas-Sock',
			  'iconsmind-Christmas-Tree' => 'iconsmind-Christmas-Tree',
			  'iconsmind-Chrome' => 'iconsmind-Chrome',
			  'iconsmind-Chrysler-Building' => 'iconsmind-Chrysler-Building',
			  'iconsmind-City-Hall' => 'iconsmind-City-Hall',
			  'iconsmind-Clamp' => 'iconsmind-Clamp',
			  'iconsmind-Claps' => 'iconsmind-Claps',
			  'iconsmind-Clothing-Store' => 'iconsmind-Clothing-Store',
			  'iconsmind-Cloud' => 'iconsmind-Cloud',
			  'iconsmind-Cloud2' => 'iconsmind-Cloud2',
			  'iconsmind-Cloud3' => 'iconsmind-Cloud3',
			  'iconsmind-Cloud-Camera' => 'iconsmind-Cloud-Camera',
			  'iconsmind-Cloud-Computer' => 'iconsmind-Cloud-Computer',
			  'iconsmind-Cloud-Email' => 'iconsmind-Cloud-Email',
			  'iconsmind-Cloud-Laptop' => 'iconsmind-Cloud-Laptop',
			  'iconsmind-Cloud-Lock' => 'iconsmind-Cloud-Lock',
			  'iconsmind-Cloud-Music' => 'iconsmind-Cloud-Music',
			  'iconsmind-Cloud-Picture' => 'iconsmind-Cloud-Picture',
			  'iconsmind-Cloud-Remove' => 'iconsmind-Cloud-Remove',
			  'iconsmind-Clouds' => 'iconsmind-Clouds',
			  'iconsmind-Cloud-Secure' => 'iconsmind-Cloud-Secure',
			  'iconsmind-Cloud-Settings' => 'iconsmind-Cloud-Settings',
			  'iconsmind-Cloud-Smartphone' => 'iconsmind-Cloud-Smartphone',
			  'iconsmind-Cloud-Tablet' => 'iconsmind-Cloud-Tablet',
			  'iconsmind-Cloud-Video' => 'iconsmind-Cloud-Video',
			  'iconsmind-Clown' => 'iconsmind-Clown',
			  'iconsmind-CMYK' => 'iconsmind-CMYK',
			  'iconsmind-Coat' => 'iconsmind-Coat',
			  'iconsmind-Cocktail' => 'iconsmind-Cocktail',
			  'iconsmind-Coconut' => 'iconsmind-Coconut',
			  'iconsmind-Coffee' => 'iconsmind-Coffee',
			  'iconsmind-Coffee-2' => 'iconsmind-Coffee-2',
			  'iconsmind-Coffee-Bean' => 'iconsmind-Coffee-Bean',
			  'iconsmind-Coffee-toGo' => 'iconsmind-Coffee-toGo',
			  'iconsmind-Coffin' => 'iconsmind-Coffin',
			  'iconsmind-Coin' => 'iconsmind-Coin',
			  'iconsmind-Coins' => 'iconsmind-Coins',
			  'iconsmind-Coins-2' => 'iconsmind-Coins-2',
			  'iconsmind-Coins-3' => 'iconsmind-Coins-3',
			  'iconsmind-Colombia' => 'iconsmind-Colombia',
			  'iconsmind-Colosseum' => 'iconsmind-Colosseum',
			  'iconsmind-Column' => 'iconsmind-Column',
			  'iconsmind-Column-2' => 'iconsmind-Column-2',
			  'iconsmind-Column-3' => 'iconsmind-Column-3',
			  'iconsmind-Comb' => 'iconsmind-Comb',
			  'iconsmind-Comb-2' => 'iconsmind-Comb-2',
			  'iconsmind-Communication-Tower' => 'iconsmind-Communication-Tower',
			  'iconsmind-Communication-Tower2' => 'iconsmind-Communication-Tower2',
			  'iconsmind-Compass2' => 'iconsmind-Compass2',
			  'iconsmind-Compass-22' => 'iconsmind-Compass-22',
			  'iconsmind-Computer' => 'iconsmind-Computer',
			  'iconsmind-Computer-2' => 'iconsmind-Computer-2',
			  'iconsmind-Computer-3' => 'iconsmind-Computer-3',
			  'iconsmind-Confused' => 'iconsmind-Confused',
			  'iconsmind-Contrast' => 'iconsmind-Contrast',
			  'iconsmind-Cookie-Man' => 'iconsmind-Cookie-Man',
			  'iconsmind-Cookies' => 'iconsmind-Cookies',
			  'iconsmind-Cool' => 'iconsmind-Cool',
			  'iconsmind-Costume' => 'iconsmind-Costume',
			  'iconsmind-Cow' => 'iconsmind-Cow',
			  'iconsmind-CPU' => 'iconsmind-CPU',
			  'iconsmind-Cranium' => 'iconsmind-Cranium',
			  'iconsmind-Credit-Card' => 'iconsmind-Credit-Card',
			  'iconsmind-Credit-Card2' => 'iconsmind-Credit-Card2',
			  'iconsmind-Credit-Card3' => 'iconsmind-Credit-Card3',
			  'iconsmind-Croissant' => 'iconsmind-Croissant',
			  'iconsmind-Crying' => 'iconsmind-Crying',
			  'iconsmind-Cupcake' => 'iconsmind-Cupcake',
			  'iconsmind-Danemark' => 'iconsmind-Danemark',
			  'iconsmind-Data' => 'iconsmind-Data',
			  'iconsmind-Data-Backup' => 'iconsmind-Data-Backup',
			  'iconsmind-Data-Block' => 'iconsmind-Data-Block',
			  'iconsmind-Data-Center' => 'iconsmind-Data-Center',
			  'iconsmind-Data-Clock' => 'iconsmind-Data-Clock',
			  'iconsmind-Data-Cloud' => 'iconsmind-Data-Cloud',
			  'iconsmind-Data-Compress' => 'iconsmind-Data-Compress',
			  'iconsmind-Data-Copy' => 'iconsmind-Data-Copy',
			  'iconsmind-Data-Download' => 'iconsmind-Data-Download',
			  'iconsmind-Data-Financial' => 'iconsmind-Data-Financial',
			  'iconsmind-Data-Key' => 'iconsmind-Data-Key',
			  'iconsmind-Data-Lock' => 'iconsmind-Data-Lock',
			  'iconsmind-Data-Network' => 'iconsmind-Data-Network',
			  'iconsmind-Data-Password' => 'iconsmind-Data-Password',
			  'iconsmind-Data-Power' => 'iconsmind-Data-Power',
			  'iconsmind-Data-Refresh' => 'iconsmind-Data-Refresh',
			  'iconsmind-Data-Save' => 'iconsmind-Data-Save',
			  'iconsmind-Data-Search' => 'iconsmind-Data-Search',
			  'iconsmind-Data-Security' => 'iconsmind-Data-Security',
			  'iconsmind-Data-Settings' => 'iconsmind-Data-Settings',
			  'iconsmind-Data-Sharing' => 'iconsmind-Data-Sharing',
			  'iconsmind-Data-Shield' => 'iconsmind-Data-Shield',
			  'iconsmind-Data-Signal' => 'iconsmind-Data-Signal',
			  'iconsmind-Data-Storage' => 'iconsmind-Data-Storage',
			  'iconsmind-Data-Stream' => 'iconsmind-Data-Stream',
			  'iconsmind-Data-Transfer' => 'iconsmind-Data-Transfer',
			  'iconsmind-Data-Unlock' => 'iconsmind-Data-Unlock',
			  'iconsmind-Data-Upload' => 'iconsmind-Data-Upload',
			  'iconsmind-Data-Yes' => 'iconsmind-Data-Yes',
			  'iconsmind-Death' => 'iconsmind-Death',
			  'iconsmind-Debian' => 'iconsmind-Debian',
			  'iconsmind-Dec' => 'iconsmind-Dec',
			  'iconsmind-Decrase-Inedit' => 'iconsmind-Decrase-Inedit',
			  'iconsmind-Deer' => 'iconsmind-Deer',
			  'iconsmind-Deer-2' => 'iconsmind-Deer-2',
			  'iconsmind-Delete-File' => 'iconsmind-Delete-File',
			  'iconsmind-Depression' => 'iconsmind-Depression',
			  'iconsmind-Device-SyncwithCloud' => 'iconsmind-Device-SyncwithCloud',
			  'iconsmind-Diamond' => 'iconsmind-Diamond',
			  'iconsmind-Digital-Drawing' => 'iconsmind-Digital-Drawing',
			  'iconsmind-Dinosaur' => 'iconsmind-Dinosaur',
			  'iconsmind-Diploma' => 'iconsmind-Diploma',
			  'iconsmind-Diploma-2' => 'iconsmind-Diploma-2',
			  'iconsmind-Disk' => 'iconsmind-Disk',
			  'iconsmind-Dog' => 'iconsmind-Dog',
			  'iconsmind-Dollar' => 'iconsmind-Dollar',
			  'iconsmind-Dollar-Sign' => 'iconsmind-Dollar-Sign',
			  'iconsmind-Dollar-Sign2' => 'iconsmind-Dollar-Sign2',
			  'iconsmind-Dolphin' => 'iconsmind-Dolphin',
			  'iconsmind-Door' => 'iconsmind-Door',
			  'iconsmind-Double-Circle' => 'iconsmind-Double-Circle',
			  'iconsmind-Doughnut' => 'iconsmind-Doughnut',
			  'iconsmind-Dove' => 'iconsmind-Dove',
			  'iconsmind-Down2' => 'iconsmind-Down2',
			  'iconsmind-Down-2' => 'iconsmind-Down-2',
			  'iconsmind-Down-3' => 'iconsmind-Down-3',
			  'iconsmind-Download2' => 'iconsmind-Download2',
			  'iconsmind-Download-fromCloud' => 'iconsmind-Download-fromCloud',
			  'iconsmind-Dress' => 'iconsmind-Dress',
			  'iconsmind-Duck' => 'iconsmind-Duck',
			  'iconsmind-DVD' => 'iconsmind-DVD',
			  'iconsmind-Eagle' => 'iconsmind-Eagle',
			  'iconsmind-Ear' => 'iconsmind-Ear',
			  'iconsmind-Eggs' => 'iconsmind-Eggs',
			  'iconsmind-Egypt' => 'iconsmind-Egypt',
			  'iconsmind-Eifel-Tower' => 'iconsmind-Eifel-Tower',
			  'iconsmind-Elbow' => 'iconsmind-Elbow',
			  'iconsmind-El-Castillo' => 'iconsmind-El-Castillo',
			  'iconsmind-Elephant' => 'iconsmind-Elephant',
			  'iconsmind-Embassy' => 'iconsmind-Embassy',
			  'iconsmind-Empire-StateBuilding' => 'iconsmind-Empire-StateBuilding',
			  'iconsmind-Empty-Box' => 'iconsmind-Empty-Box',
			  'iconsmind-End2' => 'iconsmind-End2',
			  'iconsmind-Envelope' => 'iconsmind-Envelope',
			  'iconsmind-Envelope-2' => 'iconsmind-Envelope-2',
			  'iconsmind-Eraser' => 'iconsmind-Eraser',
			  'iconsmind-Eraser-2' => 'iconsmind-Eraser-2',
			  'iconsmind-Eraser-3' => 'iconsmind-Eraser-3',
			  'iconsmind-Euro' => 'iconsmind-Euro',
			  'iconsmind-Euro-Sign' => 'iconsmind-Euro-Sign',
			  'iconsmind-Euro-Sign2' => 'iconsmind-Euro-Sign2',
			  'iconsmind-Evil' => 'iconsmind-Evil',
			  'iconsmind-Eye2' => 'iconsmind-Eye2',
			  'iconsmind-Eye-Blind' => 'iconsmind-Eye-Blind',
			  'iconsmind-Eyebrow' => 'iconsmind-Eyebrow',
			  'iconsmind-Eyebrow-2' => 'iconsmind-Eyebrow-2',
			  'iconsmind-Eyebrow-3' => 'iconsmind-Eyebrow-3',
			  'iconsmind-Eyeglasses-Smiley' => 'iconsmind-Eyeglasses-Smiley',
			  'iconsmind-Eyeglasses-Smiley2' => 'iconsmind-Eyeglasses-Smiley2',
			  'iconsmind-Eye-Invisible' => 'iconsmind-Eye-Invisible',
			  'iconsmind-Eye-Visible' => 'iconsmind-Eye-Visible',
			  'iconsmind-Face-Style' => 'iconsmind-Face-Style',
			  'iconsmind-Face-Style2' => 'iconsmind-Face-Style2',
			  'iconsmind-Face-Style3' => 'iconsmind-Face-Style3',
			  'iconsmind-Face-Style4' => 'iconsmind-Face-Style4',
			  'iconsmind-Face-Style5' => 'iconsmind-Face-Style5',
			  'iconsmind-Face-Style6' => 'iconsmind-Face-Style6',
			  'iconsmind-Factory2' => 'iconsmind-Factory2',
			  'iconsmind-Fan' => 'iconsmind-Fan',
			  'iconsmind-Fashion' => 'iconsmind-Fashion',
			  'iconsmind-Fax' => 'iconsmind-Fax',
			  'iconsmind-File' => 'iconsmind-File',
			  'iconsmind-File-Block' => 'iconsmind-File-Block',
			  'iconsmind-File-Bookmark' => 'iconsmind-File-Bookmark',
			  'iconsmind-File-Chart' => 'iconsmind-File-Chart',
			  'iconsmind-File-Clipboard' => 'iconsmind-File-Clipboard',
			  'iconsmind-File-ClipboardFileText' => 'iconsmind-File-ClipboardFileText',
			  'iconsmind-File-ClipboardTextImage' => 'iconsmind-File-ClipboardTextImage',
			  'iconsmind-File-Cloud' => 'iconsmind-File-Cloud',
			  'iconsmind-File-Copy' => 'iconsmind-File-Copy',
			  'iconsmind-File-Copy2' => 'iconsmind-File-Copy2',
			  'iconsmind-File-CSV' => 'iconsmind-File-CSV',
			  'iconsmind-File-Download' => 'iconsmind-File-Download',
			  'iconsmind-File-Edit' => 'iconsmind-File-Edit',
			  'iconsmind-File-Excel' => 'iconsmind-File-Excel',
			  'iconsmind-File-Favorite' => 'iconsmind-File-Favorite',
			  'iconsmind-File-Fire' => 'iconsmind-File-Fire',
			  'iconsmind-File-Graph' => 'iconsmind-File-Graph',
			  'iconsmind-File-Hide' => 'iconsmind-File-Hide',
			  'iconsmind-File-Horizontal' => 'iconsmind-File-Horizontal',
			  'iconsmind-File-HorizontalText' => 'iconsmind-File-HorizontalText',
			  'iconsmind-File-HTML' => 'iconsmind-File-HTML',
			  'iconsmind-File-JPG' => 'iconsmind-File-JPG',
			  'iconsmind-File-Link' => 'iconsmind-File-Link',
			  'iconsmind-File-Loading' => 'iconsmind-File-Loading',
			  'iconsmind-File-Lock' => 'iconsmind-File-Lock',
			  'iconsmind-File-Love' => 'iconsmind-File-Love',
			  'iconsmind-File-Music' => 'iconsmind-File-Music',
			  'iconsmind-File-Network' => 'iconsmind-File-Network',
			  'iconsmind-File-Pictures' => 'iconsmind-File-Pictures',
			  'iconsmind-File-Pie' => 'iconsmind-File-Pie',
			  'iconsmind-File-Presentation' => 'iconsmind-File-Presentation',
			  'iconsmind-File-Refresh' => 'iconsmind-File-Refresh',
			  'iconsmind-Files' => 'iconsmind-Files',
			  'iconsmind-File-Search' => 'iconsmind-File-Search',
			  'iconsmind-File-Settings' => 'iconsmind-File-Settings',
			  'iconsmind-File-Share' => 'iconsmind-File-Share',
			  'iconsmind-File-TextImage' => 'iconsmind-File-TextImage',
			  'iconsmind-File-Trash' => 'iconsmind-File-Trash',
			  'iconsmind-File-TXT' => 'iconsmind-File-TXT',
			  'iconsmind-File-Upload' => 'iconsmind-File-Upload',
			  'iconsmind-File-Video' => 'iconsmind-File-Video',
			  'iconsmind-File-Word' => 'iconsmind-File-Word',
			  'iconsmind-File-Zip' => 'iconsmind-File-Zip',
			  'iconsmind-Financial' => 'iconsmind-Financial',
			  'iconsmind-Finger' => 'iconsmind-Finger',
			  'iconsmind-Fingerprint' => 'iconsmind-Fingerprint',
			  'iconsmind-Fingerprint-2' => 'iconsmind-Fingerprint-2',
			  'iconsmind-Firefox' => 'iconsmind-Firefox',
			  'iconsmind-Fire-Staion' => 'iconsmind-Fire-Staion',
			  'iconsmind-Fish' => 'iconsmind-Fish',
			  'iconsmind-Fit-To' => 'iconsmind-Fit-To',
			  'iconsmind-Fit-To2' => 'iconsmind-Fit-To2',
			  'iconsmind-Flag2' => 'iconsmind-Flag2',
			  'iconsmind-Flag-22' => 'iconsmind-Flag-22',
			  'iconsmind-Flag-3' => 'iconsmind-Flag-3',
			  'iconsmind-Flag-4' => 'iconsmind-Flag-4',
			  'iconsmind-Flamingo' => 'iconsmind-Flamingo',
			  'iconsmind-Folder' => 'iconsmind-Folder',
			  'iconsmind-Folder-Add' => 'iconsmind-Folder-Add',
			  'iconsmind-Folder-Archive' => 'iconsmind-Folder-Archive',
			  'iconsmind-Folder-Binder' => 'iconsmind-Folder-Binder',
			  'iconsmind-Folder-Binder2' => 'iconsmind-Folder-Binder2',
			  'iconsmind-Folder-Block' => 'iconsmind-Folder-Block',
			  'iconsmind-Folder-Bookmark' => 'iconsmind-Folder-Bookmark',
			  'iconsmind-Folder-Close' => 'iconsmind-Folder-Close',
			  'iconsmind-Folder-Cloud' => 'iconsmind-Folder-Cloud',
			  'iconsmind-Folder-Delete' => 'iconsmind-Folder-Delete',
			  'iconsmind-Folder-Download' => 'iconsmind-Folder-Download',
			  'iconsmind-Folder-Edit' => 'iconsmind-Folder-Edit',
			  'iconsmind-Folder-Favorite' => 'iconsmind-Folder-Favorite',
			  'iconsmind-Folder-Fire' => 'iconsmind-Folder-Fire',
			  'iconsmind-Folder-Hide' => 'iconsmind-Folder-Hide',
			  'iconsmind-Folder-Link' => 'iconsmind-Folder-Link',
			  'iconsmind-Folder-Loading' => 'iconsmind-Folder-Loading',
			  'iconsmind-Folder-Lock' => 'iconsmind-Folder-Lock',
			  'iconsmind-Folder-Love' => 'iconsmind-Folder-Love',
			  'iconsmind-Folder-Music' => 'iconsmind-Folder-Music',
			  'iconsmind-Folder-Network' => 'iconsmind-Folder-Network',
			  'iconsmind-Folder-Open' => 'iconsmind-Folder-Open',
			  'iconsmind-Folder-Open2' => 'iconsmind-Folder-Open2',
			  'iconsmind-Folder-Organizing' => 'iconsmind-Folder-Organizing',
			  'iconsmind-Folder-Pictures' => 'iconsmind-Folder-Pictures',
			  'iconsmind-Folder-Refresh' => 'iconsmind-Folder-Refresh',
			  'iconsmind-Folder-Remove' => 'iconsmind-Folder-Remove',
			  'iconsmind-Folders' => 'iconsmind-Folders',
			  'iconsmind-Folder-Search' => 'iconsmind-Folder-Search',
			  'iconsmind-Folder-Settings' => 'iconsmind-Folder-Settings',
			  'iconsmind-Folder-Share' => 'iconsmind-Folder-Share',
			  'iconsmind-Folder-Trash' => 'iconsmind-Folder-Trash',
			  'iconsmind-Folder-Upload' => 'iconsmind-Folder-Upload',
			  'iconsmind-Folder-Video' => 'iconsmind-Folder-Video',
			  'iconsmind-Folder-WithDocument' => 'iconsmind-Folder-WithDocument',
			  'iconsmind-Folder-Zip' => 'iconsmind-Folder-Zip',
			  'iconsmind-Foot' => 'iconsmind-Foot',
			  'iconsmind-Foot-2' => 'iconsmind-Foot-2',
			  'iconsmind-Fork' => 'iconsmind-Fork',
			  'iconsmind-Formula' => 'iconsmind-Formula',
			  'iconsmind-Fountain-Pen' => 'iconsmind-Fountain-Pen',
			  'iconsmind-Fox' => 'iconsmind-Fox',
			  'iconsmind-Frankenstein' => 'iconsmind-Frankenstein',
			  'iconsmind-French-Fries' => 'iconsmind-French-Fries',
			  'iconsmind-Frog' => 'iconsmind-Frog',
			  'iconsmind-Fruits' => 'iconsmind-Fruits',
			  'iconsmind-Full-Screen' => 'iconsmind-Full-Screen',
			  'iconsmind-Full-Screen2' => 'iconsmind-Full-Screen2',
			  'iconsmind-Full-View' => 'iconsmind-Full-View',
			  'iconsmind-Full-View2' => 'iconsmind-Full-View2',
			  'iconsmind-Funky' => 'iconsmind-Funky',
			  'iconsmind-Funny-Bicycle' => 'iconsmind-Funny-Bicycle',
			  'iconsmind-Gamepad' => 'iconsmind-Gamepad',
			  'iconsmind-Gamepad-2' => 'iconsmind-Gamepad-2',
			  'iconsmind-Gay' => 'iconsmind-Gay',
			  'iconsmind-Geek2' => 'iconsmind-Geek2',
			  'iconsmind-Gentleman' => 'iconsmind-Gentleman',
			  'iconsmind-Giraffe' => 'iconsmind-Giraffe',
			  'iconsmind-Glasses' => 'iconsmind-Glasses',
			  'iconsmind-Glasses-2' => 'iconsmind-Glasses-2',
			  'iconsmind-Glasses-3' => 'iconsmind-Glasses-3',
			  'iconsmind-Glass-Water' => 'iconsmind-Glass-Water',
			  'iconsmind-Gloves' => 'iconsmind-Gloves',
			  'iconsmind-Go-Bottom' => 'iconsmind-Go-Bottom',
			  'iconsmind-Gorilla' => 'iconsmind-Gorilla',
			  'iconsmind-Go-Top' => 'iconsmind-Go-Top',
			  'iconsmind-Grave' => 'iconsmind-Grave',
			  'iconsmind-Graveyard' => 'iconsmind-Graveyard',
			  'iconsmind-Greece' => 'iconsmind-Greece',
			  'iconsmind-Hair' => 'iconsmind-Hair',
			  'iconsmind-Hair-2' => 'iconsmind-Hair-2',
			  'iconsmind-Hair-3' => 'iconsmind-Hair-3',
			  'iconsmind-Halloween-HalfMoon' => 'iconsmind-Halloween-HalfMoon',
			  'iconsmind-Halloween-Moon' => 'iconsmind-Halloween-Moon',
			  'iconsmind-Hamburger' => 'iconsmind-Hamburger',
			  'iconsmind-Hand' => 'iconsmind-Hand',
			  'iconsmind-Hands' => 'iconsmind-Hands',
			  'iconsmind-Handshake' => 'iconsmind-Handshake',
			  'iconsmind-Hanger' => 'iconsmind-Hanger',
			  'iconsmind-Happy' => 'iconsmind-Happy',
			  'iconsmind-Hat' => 'iconsmind-Hat',
			  'iconsmind-Hat-2' => 'iconsmind-Hat-2',
			  'iconsmind-Haunted-House' => 'iconsmind-Haunted-House',
			  'iconsmind-HD' => 'iconsmind-HD',
			  'iconsmind-HDD' => 'iconsmind-HDD',
			  'iconsmind-Heart2' => 'iconsmind-Heart2',
			  'iconsmind-Heels' => 'iconsmind-Heels',
			  'iconsmind-Heels-2' => 'iconsmind-Heels-2',
			  'iconsmind-Hello' => 'iconsmind-Hello',
			  'iconsmind-Hipo' => 'iconsmind-Hipo',
			  'iconsmind-Hipster-Glasses' => 'iconsmind-Hipster-Glasses',
			  'iconsmind-Hipster-Glasses2' => 'iconsmind-Hipster-Glasses2',
			  'iconsmind-Hipster-Glasses3' => 'iconsmind-Hipster-Glasses3',
			  'iconsmind-Hipster-Headphones' => 'iconsmind-Hipster-Headphones',
			  'iconsmind-Hipster-Men' => 'iconsmind-Hipster-Men',
			  'iconsmind-Hipster-Men2' => 'iconsmind-Hipster-Men2',
			  'iconsmind-Hipster-Men3' => 'iconsmind-Hipster-Men3',
			  'iconsmind-Hipster-Sunglasses' => 'iconsmind-Hipster-Sunglasses',
			  'iconsmind-Hipster-Sunglasses2' => 'iconsmind-Hipster-Sunglasses2',
			  'iconsmind-Hipster-Sunglasses3' => 'iconsmind-Hipster-Sunglasses3',
			  'iconsmind-Holly' => 'iconsmind-Holly',
			  'iconsmind-Home2' => 'iconsmind-Home2',
			  'iconsmind-Home-2' => 'iconsmind-Home-2',
			  'iconsmind-Home-3' => 'iconsmind-Home-3',
			  'iconsmind-Home-4' => 'iconsmind-Home-4',
			  'iconsmind-Honey' => 'iconsmind-Honey',
			  'iconsmind-Hong-Kong' => 'iconsmind-Hong-Kong',
			  'iconsmind-Hoodie' => 'iconsmind-Hoodie',
			  'iconsmind-Horror' => 'iconsmind-Horror',
			  'iconsmind-Horse' => 'iconsmind-Horse',
			  'iconsmind-Hospital2' => 'iconsmind-Hospital2',
			  'iconsmind-Host' => 'iconsmind-Host',
			  'iconsmind-Hot-Dog' => 'iconsmind-Hot-Dog',
			  'iconsmind-Hotel' => 'iconsmind-Hotel',
			  'iconsmind-Hub' => 'iconsmind-Hub',
			  'iconsmind-Humor' => 'iconsmind-Humor',
			  'iconsmind-Ice-Cream' => 'iconsmind-Ice-Cream',
			  'iconsmind-Idea' => 'iconsmind-Idea',
			  'iconsmind-Inbox' => 'iconsmind-Inbox',
			  'iconsmind-Inbox-Empty' => 'iconsmind-Inbox-Empty',
			  'iconsmind-Inbox-Forward' => 'iconsmind-Inbox-Forward',
			  'iconsmind-Inbox-Full' => 'iconsmind-Inbox-Full',
			  'iconsmind-Inbox-Into' => 'iconsmind-Inbox-Into',
			  'iconsmind-Inbox-Out' => 'iconsmind-Inbox-Out',
			  'iconsmind-Inbox-Reply' => 'iconsmind-Inbox-Reply',
			  'iconsmind-Increase-Inedit' => 'iconsmind-Increase-Inedit',
			  'iconsmind-Indent-FirstLine' => 'iconsmind-Indent-FirstLine',
			  'iconsmind-Indent-LeftMargin' => 'iconsmind-Indent-LeftMargin',
			  'iconsmind-Indent-RightMargin' => 'iconsmind-Indent-RightMargin',
			  'iconsmind-India' => 'iconsmind-India',
			  'iconsmind-Internet-Explorer' => 'iconsmind-Internet-Explorer',
			  'iconsmind-Internet-Smiley' => 'iconsmind-Internet-Smiley',
			  'iconsmind-iOS-Apple' => 'iconsmind-iOS-Apple',
			  'iconsmind-Israel' => 'iconsmind-Israel',
			  'iconsmind-Jacket' => 'iconsmind-Jacket',
			  'iconsmind-Jamaica' => 'iconsmind-Jamaica',
			  'iconsmind-Japan' => 'iconsmind-Japan',
			  'iconsmind-Japanese-Gate' => 'iconsmind-Japanese-Gate',
			  'iconsmind-Jeans' => 'iconsmind-Jeans',
			  'iconsmind-Joystick' => 'iconsmind-Joystick',
			  'iconsmind-Juice' => 'iconsmind-Juice',
			  'iconsmind-Kangoroo' => 'iconsmind-Kangoroo',
			  'iconsmind-Kenya' => 'iconsmind-Kenya',
			  'iconsmind-Keyboard' => 'iconsmind-Keyboard',
			  'iconsmind-Keypad' => 'iconsmind-Keypad',
			  'iconsmind-King' => 'iconsmind-King',
			  'iconsmind-Kiss' => 'iconsmind-Kiss',
			  'iconsmind-Knee' => 'iconsmind-Knee',
			  'iconsmind-Knife' => 'iconsmind-Knife',
			  'iconsmind-Knight' => 'iconsmind-Knight',
			  'iconsmind-Koala' => 'iconsmind-Koala',
			  'iconsmind-Korea' => 'iconsmind-Korea',
			  'iconsmind-Lantern' => 'iconsmind-Lantern',
			  'iconsmind-Laptop' => 'iconsmind-Laptop',
			  'iconsmind-Laptop-2' => 'iconsmind-Laptop-2',
			  'iconsmind-Laptop-3' => 'iconsmind-Laptop-3',
			  'iconsmind-Laptop-Phone' => 'iconsmind-Laptop-Phone',
			  'iconsmind-Laptop-Tablet' => 'iconsmind-Laptop-Tablet',
			  'iconsmind-Laughing' => 'iconsmind-Laughing',
			  'iconsmind-Leaning-Tower' => 'iconsmind-Leaning-Tower',
			  'iconsmind-Left2' => 'iconsmind-Left2',
			  'iconsmind-Left-2' => 'iconsmind-Left-2',
			  'iconsmind-Left-3' => 'iconsmind-Left-3',
			  'iconsmind-Left-ToRight' => 'iconsmind-Left-ToRight',
			  'iconsmind-Leg' => 'iconsmind-Leg',
			  'iconsmind-Leg-2' => 'iconsmind-Leg-2',
			  'iconsmind-Lemon' => 'iconsmind-Lemon',
			  'iconsmind-Leopard' => 'iconsmind-Leopard',
			  'iconsmind-Letter-Close' => 'iconsmind-Letter-Close',
			  'iconsmind-Letter-Open' => 'iconsmind-Letter-Open',
			  'iconsmind-Letter-Sent' => 'iconsmind-Letter-Sent',
			  'iconsmind-Library2' => 'iconsmind-Library2',
			  'iconsmind-Lighthouse' => 'iconsmind-Lighthouse',
			  'iconsmind-Line-Chart' => 'iconsmind-Line-Chart',
			  'iconsmind-Line-Chart2' => 'iconsmind-Line-Chart2',
			  'iconsmind-Line-Chart3' => 'iconsmind-Line-Chart3',
			  'iconsmind-Line-Chart4' => 'iconsmind-Line-Chart4',
			  'iconsmind-Line-Spacing' => 'iconsmind-Line-Spacing',
			  'iconsmind-Linux' => 'iconsmind-Linux',
			  'iconsmind-Lion' => 'iconsmind-Lion',
			  'iconsmind-Lollipop' => 'iconsmind-Lollipop',
			  'iconsmind-Lollipop-2' => 'iconsmind-Lollipop-2',
			  'iconsmind-Loop' => 'iconsmind-Loop',
			  'iconsmind-Love2' => 'iconsmind-Love2',
			  'iconsmind-Mail' => 'iconsmind-Mail',
			  'iconsmind-Mail-2' => 'iconsmind-Mail-2',
			  'iconsmind-Mail-3' => 'iconsmind-Mail-3',
			  'iconsmind-Mail-Add' => 'iconsmind-Mail-Add',
			  'iconsmind-Mail-Attachement' => 'iconsmind-Mail-Attachement',
			  'iconsmind-Mail-Block' => 'iconsmind-Mail-Block',
			  'iconsmind-Mailbox-Empty' => 'iconsmind-Mailbox-Empty',
			  'iconsmind-Mailbox-Full' => 'iconsmind-Mailbox-Full',
			  'iconsmind-Mail-Delete' => 'iconsmind-Mail-Delete',
			  'iconsmind-Mail-Favorite' => 'iconsmind-Mail-Favorite',
			  'iconsmind-Mail-Forward' => 'iconsmind-Mail-Forward',
			  'iconsmind-Mail-Gallery' => 'iconsmind-Mail-Gallery',
			  'iconsmind-Mail-Inbox' => 'iconsmind-Mail-Inbox',
			  'iconsmind-Mail-Link' => 'iconsmind-Mail-Link',
			  'iconsmind-Mail-Lock' => 'iconsmind-Mail-Lock',
			  'iconsmind-Mail-Love' => 'iconsmind-Mail-Love',
			  'iconsmind-Mail-Money' => 'iconsmind-Mail-Money',
			  'iconsmind-Mail-Open' => 'iconsmind-Mail-Open',
			  'iconsmind-Mail-Outbox' => 'iconsmind-Mail-Outbox',
			  'iconsmind-Mail-Password' => 'iconsmind-Mail-Password',
			  'iconsmind-Mail-Photo' => 'iconsmind-Mail-Photo',
			  'iconsmind-Mail-Read' => 'iconsmind-Mail-Read',
			  'iconsmind-Mail-Removex' => 'iconsmind-Mail-Removex',
			  'iconsmind-Mail-Reply' => 'iconsmind-Mail-Reply',
			  'iconsmind-Mail-ReplyAll' => 'iconsmind-Mail-ReplyAll',
			  'iconsmind-Mail-Search' => 'iconsmind-Mail-Search',
			  'iconsmind-Mail-Send' => 'iconsmind-Mail-Send',
			  'iconsmind-Mail-Settings' => 'iconsmind-Mail-Settings',
			  'iconsmind-Mail-Unread' => 'iconsmind-Mail-Unread',
			  'iconsmind-Mail-Video' => 'iconsmind-Mail-Video',
			  'iconsmind-Mail-withAtSign' => 'iconsmind-Mail-withAtSign',
			  'iconsmind-Mail-WithCursors' => 'iconsmind-Mail-WithCursors',
			  'iconsmind-Mans-Underwear' => 'iconsmind-Mans-Underwear',
			  'iconsmind-Mans-Underwear2' => 'iconsmind-Mans-Underwear2',
			  'iconsmind-Marker' => 'iconsmind-Marker',
			  'iconsmind-Marker-2' => 'iconsmind-Marker-2',
			  'iconsmind-Marker-3' => 'iconsmind-Marker-3',
			  'iconsmind-Martini-Glass' => 'iconsmind-Martini-Glass',
			  'iconsmind-Master-Card' => 'iconsmind-Master-Card',
			  'iconsmind-Maximize' => 'iconsmind-Maximize',
			  'iconsmind-Megaphone' => 'iconsmind-Megaphone',
			  'iconsmind-Mexico' => 'iconsmind-Mexico',
			  'iconsmind-Milk-Bottle' => 'iconsmind-Milk-Bottle',
			  'iconsmind-Minimize' => 'iconsmind-Minimize',
			  'iconsmind-Money' => 'iconsmind-Money',
			  'iconsmind-Money-2' => 'iconsmind-Money-2',
			  'iconsmind-Money-Bag' => 'iconsmind-Money-Bag',
			  'iconsmind-Monitor' => 'iconsmind-Monitor',
			  'iconsmind-Monitor-2' => 'iconsmind-Monitor-2',
			  'iconsmind-Monitor-3' => 'iconsmind-Monitor-3',
			  'iconsmind-Monitor-4' => 'iconsmind-Monitor-4',
			  'iconsmind-Monitor-5' => 'iconsmind-Monitor-5',
			  'iconsmind-Monitor-Laptop' => 'iconsmind-Monitor-Laptop',
			  'iconsmind-Monitor-phone' => 'iconsmind-Monitor-phone',
			  'iconsmind-Monitor-Tablet' => 'iconsmind-Monitor-Tablet',
			  'iconsmind-Monitor-Vertical' => 'iconsmind-Monitor-Vertical',
			  'iconsmind-Monkey' => 'iconsmind-Monkey',
			  'iconsmind-Monster' => 'iconsmind-Monster',
			  'iconsmind-Morocco' => 'iconsmind-Morocco',
			  'iconsmind-Mouse' => 'iconsmind-Mouse',
			  'iconsmind-Mouse-2' => 'iconsmind-Mouse-2',
			  'iconsmind-Mouse-3' => 'iconsmind-Mouse-3',
			  'iconsmind-Moustache-Smiley' => 'iconsmind-Moustache-Smiley',
			  'iconsmind-Museum' => 'iconsmind-Museum',
			  'iconsmind-Mushroom' => 'iconsmind-Mushroom',
			  'iconsmind-Mustache' => 'iconsmind-Mustache',
			  'iconsmind-Mustache-2' => 'iconsmind-Mustache-2',
			  'iconsmind-Mustache-3' => 'iconsmind-Mustache-3',
			  'iconsmind-Mustache-4' => 'iconsmind-Mustache-4',
			  'iconsmind-Mustache-5' => 'iconsmind-Mustache-5',
			  'iconsmind-Navigate-End' => 'iconsmind-Navigate-End',
			  'iconsmind-Navigat-Start' => 'iconsmind-Navigat-Start',
			  'iconsmind-Nepal' => 'iconsmind-Nepal',
			  'iconsmind-Netscape' => 'iconsmind-Netscape',
			  'iconsmind-New-Mail' => 'iconsmind-New-Mail',
			  'iconsmind-Newspaper' => 'iconsmind-Newspaper',
			  'iconsmind-Newspaper-2' => 'iconsmind-Newspaper-2',
			  'iconsmind-No-Battery' => 'iconsmind-No-Battery',
			  'iconsmind-Noose' => 'iconsmind-Noose',
			  'iconsmind-Note' => 'iconsmind-Note',
			  'iconsmind-Notepad' => 'iconsmind-Notepad',
			  'iconsmind-Notepad-2' => 'iconsmind-Notepad-2',
			  'iconsmind-Office' => 'iconsmind-Office',
			  'iconsmind-Old-Camera' => 'iconsmind-Old-Camera',
			  'iconsmind-Old-Cassette' => 'iconsmind-Old-Cassette',
			  'iconsmind-Old-Sticky' => 'iconsmind-Old-Sticky',
			  'iconsmind-Old-Sticky2' => 'iconsmind-Old-Sticky2',
			  'iconsmind-Old-Telephone' => 'iconsmind-Old-Telephone',
			  'iconsmind-Open-Banana' => 'iconsmind-Open-Banana',
			  'iconsmind-Open-Book' => 'iconsmind-Open-Book',
			  'iconsmind-Opera' => 'iconsmind-Opera',
			  'iconsmind-Opera-House' => 'iconsmind-Opera-House',
			  'iconsmind-Orientation2' => 'iconsmind-Orientation2',
			  'iconsmind-Orientation-2' => 'iconsmind-Orientation-2',
			  'iconsmind-Ornament' => 'iconsmind-Ornament',
			  'iconsmind-Owl' => 'iconsmind-Owl',
			  'iconsmind-Paintbrush' => 'iconsmind-Paintbrush',
			  'iconsmind-Palette' => 'iconsmind-Palette',
			  'iconsmind-Panda' => 'iconsmind-Panda',
			  'iconsmind-Pantheon' => 'iconsmind-Pantheon',
			  'iconsmind-Pantone' => 'iconsmind-Pantone',
			  'iconsmind-Pants' => 'iconsmind-Pants',
			  'iconsmind-Paper' => 'iconsmind-Paper',
			  'iconsmind-Parrot' => 'iconsmind-Parrot',
			  'iconsmind-Pawn' => 'iconsmind-Pawn',
			  'iconsmind-Pen' => 'iconsmind-Pen',
			  'iconsmind-Pen-2' => 'iconsmind-Pen-2',
			  'iconsmind-Pen-3' => 'iconsmind-Pen-3',
			  'iconsmind-Pen-4' => 'iconsmind-Pen-4',
			  'iconsmind-Pen-5' => 'iconsmind-Pen-5',
			  'iconsmind-Pen-6' => 'iconsmind-Pen-6',
			  'iconsmind-Pencil' => 'iconsmind-Pencil',
			  'iconsmind-Pencil-Ruler' => 'iconsmind-Pencil-Ruler',
			  'iconsmind-Penguin' => 'iconsmind-Penguin',
			  'iconsmind-Pentagon' => 'iconsmind-Pentagon',
			  'iconsmind-People-onCloud' => 'iconsmind-People-onCloud',
			  'iconsmind-Pepper' => 'iconsmind-Pepper',
			  'iconsmind-Pepper-withFire' => 'iconsmind-Pepper-withFire',
			  'iconsmind-Petronas-Tower' => 'iconsmind-Petronas-Tower',
			  'iconsmind-Philipines' => 'iconsmind-Philipines',
			  'iconsmind-Phone' => 'iconsmind-Phone',
			  'iconsmind-Phone-2' => 'iconsmind-Phone-2',
			  'iconsmind-Phone-3' => 'iconsmind-Phone-3',
			  'iconsmind-Phone-3G' => 'iconsmind-Phone-3G',
			  'iconsmind-Phone-4G' => 'iconsmind-Phone-4G',
			  'iconsmind-Phone-Simcard' => 'iconsmind-Phone-Simcard',
			  'iconsmind-Phone-SMS' => 'iconsmind-Phone-SMS',
			  'iconsmind-Phone-Wifi' => 'iconsmind-Phone-Wifi',
			  'iconsmind-Pi' => 'iconsmind-Pi',
			  'iconsmind-Pie-Chart' => 'iconsmind-Pie-Chart',
			  'iconsmind-Pie-Chart2' => 'iconsmind-Pie-Chart2',
			  'iconsmind-Pie-Chart3' => 'iconsmind-Pie-Chart3',
			  'iconsmind-Pipette' => 'iconsmind-Pipette',
			  'iconsmind-Piramids' => 'iconsmind-Piramids',
			  'iconsmind-Pizza' => 'iconsmind-Pizza',
			  'iconsmind-Pizza-Slice' => 'iconsmind-Pizza-Slice',
			  'iconsmind-Plastic-CupPhone' => 'iconsmind-Plastic-CupPhone',
			  'iconsmind-Plastic-CupPhone2' => 'iconsmind-Plastic-CupPhone2',
			  'iconsmind-Plate' => 'iconsmind-Plate',
			  'iconsmind-Plates' => 'iconsmind-Plates',
			  'iconsmind-Plug-In' => 'iconsmind-Plug-In',
			  'iconsmind-Plug-In2' => 'iconsmind-Plug-In2',
			  'iconsmind-Poland' => 'iconsmind-Poland',
			  'iconsmind-Police-Station' => 'iconsmind-Police-Station',
			  'iconsmind-Polo-Shirt' => 'iconsmind-Polo-Shirt',
			  'iconsmind-Portugal' => 'iconsmind-Portugal',
			  'iconsmind-Post-Mail' => 'iconsmind-Post-Mail',
			  'iconsmind-Post-Mail2' => 'iconsmind-Post-Mail2',
			  'iconsmind-Post-Office' => 'iconsmind-Post-Office',
			  'iconsmind-Pound' => 'iconsmind-Pound',
			  'iconsmind-Pound-Sign' => 'iconsmind-Pound-Sign',
			  'iconsmind-Pound-Sign2' => 'iconsmind-Pound-Sign2',
			  'iconsmind-Power' => 'iconsmind-Power',
			  'iconsmind-Power-Cable' => 'iconsmind-Power-Cable',
			  'iconsmind-Prater' => 'iconsmind-Prater',
			  'iconsmind-Present' => 'iconsmind-Present',
			  'iconsmind-Presents' => 'iconsmind-Presents',
			  'iconsmind-Printer' => 'iconsmind-Printer',
			  'iconsmind-Projector' => 'iconsmind-Projector',
			  'iconsmind-Projector-2' => 'iconsmind-Projector-2',
			  'iconsmind-Pumpkin' => 'iconsmind-Pumpkin',
			  'iconsmind-Punk' => 'iconsmind-Punk',
			  'iconsmind-Queen' => 'iconsmind-Queen',
			  'iconsmind-Quill' => 'iconsmind-Quill',
			  'iconsmind-Quill-2' => 'iconsmind-Quill-2',
			  'iconsmind-Quill-3' => 'iconsmind-Quill-3',
			  'iconsmind-Ram' => 'iconsmind-Ram',
			  'iconsmind-Redhat' => 'iconsmind-Redhat',
			  'iconsmind-Reload2' => 'iconsmind-Reload2',
			  'iconsmind-Reload-2' => 'iconsmind-Reload-2',
			  'iconsmind-Remote-Controll' => 'iconsmind-Remote-Controll',
			  'iconsmind-Remote-Controll2' => 'iconsmind-Remote-Controll2',
			  'iconsmind-Remove-File' => 'iconsmind-Remove-File',
			  'iconsmind-Repeat3' => 'iconsmind-Repeat3',
			  'iconsmind-Repeat-22' => 'iconsmind-Repeat-22',
			  'iconsmind-Repeat-3' => 'iconsmind-Repeat-3',
			  'iconsmind-Repeat-4' => 'iconsmind-Repeat-4',
			  'iconsmind-Resize' => 'iconsmind-Resize',
			  'iconsmind-Retro' => 'iconsmind-Retro',
			  'iconsmind-RGB' => 'iconsmind-RGB',
			  'iconsmind-Right2' => 'iconsmind-Right2',
			  'iconsmind-Right-2' => 'iconsmind-Right-2',
			  'iconsmind-Right-3' => 'iconsmind-Right-3',
			  'iconsmind-Right-ToLeft' => 'iconsmind-Right-ToLeft',
			  'iconsmind-Robot2' => 'iconsmind-Robot2',
			  'iconsmind-Roller' => 'iconsmind-Roller',
			  'iconsmind-Roof' => 'iconsmind-Roof',
			  'iconsmind-Rook' => 'iconsmind-Rook',
			  'iconsmind-Router' => 'iconsmind-Router',
			  'iconsmind-Router-2' => 'iconsmind-Router-2',
			  'iconsmind-Ruler' => 'iconsmind-Ruler',
			  'iconsmind-Ruler-2' => 'iconsmind-Ruler-2',
			  'iconsmind-Safari' => 'iconsmind-Safari',
			  'iconsmind-Safe-Box2' => 'iconsmind-Safe-Box2',
			  'iconsmind-Santa-Claus' => 'iconsmind-Santa-Claus',
			  'iconsmind-Santa-Claus2' => 'iconsmind-Santa-Claus2',
			  'iconsmind-Santa-onSled' => 'iconsmind-Santa-onSled',
			  'iconsmind-Scarf' => 'iconsmind-Scarf',
			  'iconsmind-Scissor' => 'iconsmind-Scissor',
			  'iconsmind-Scotland' => 'iconsmind-Scotland',
			  'iconsmind-Sea-Dog' => 'iconsmind-Sea-Dog',
			  'iconsmind-Search-onCloud' => 'iconsmind-Search-onCloud',
			  'iconsmind-Security-Smiley' => 'iconsmind-Security-Smiley',
			  'iconsmind-Serbia' => 'iconsmind-Serbia',
			  'iconsmind-Server' => 'iconsmind-Server',
			  'iconsmind-Server-2' => 'iconsmind-Server-2',
			  'iconsmind-Servers' => 'iconsmind-Servers',
			  'iconsmind-Share-onCloud' => 'iconsmind-Share-onCloud',
			  'iconsmind-Shark' => 'iconsmind-Shark',
			  'iconsmind-Sheep' => 'iconsmind-Sheep',
			  'iconsmind-Shirt' => 'iconsmind-Shirt',
			  'iconsmind-Shoes' => 'iconsmind-Shoes',
			  'iconsmind-Shoes-2' => 'iconsmind-Shoes-2',
			  'iconsmind-Short-Pants' => 'iconsmind-Short-Pants',
			  'iconsmind-Shuffle2' => 'iconsmind-Shuffle2',
			  'iconsmind-Shuffle-22' => 'iconsmind-Shuffle-22',
			  'iconsmind-Singapore' => 'iconsmind-Singapore',
			  'iconsmind-Skeleton' => 'iconsmind-Skeleton',
			  'iconsmind-Skirt' => 'iconsmind-Skirt',
			  'iconsmind-Skull' => 'iconsmind-Skull',
			  'iconsmind-Sled' => 'iconsmind-Sled',
			  'iconsmind-Sled-withGifts' => 'iconsmind-Sled-withGifts',
			  'iconsmind-Sleeping' => 'iconsmind-Sleeping',
			  'iconsmind-Slippers' => 'iconsmind-Slippers',
			  'iconsmind-Smart' => 'iconsmind-Smart',
			  'iconsmind-Smartphone' => 'iconsmind-Smartphone',
			  'iconsmind-Smartphone-2' => 'iconsmind-Smartphone-2',
			  'iconsmind-Smartphone-3' => 'iconsmind-Smartphone-3',
			  'iconsmind-Smartphone-4' => 'iconsmind-Smartphone-4',
			  'iconsmind-Smile' => 'iconsmind-Smile',
			  'iconsmind-Smoking-Pipe' => 'iconsmind-Smoking-Pipe',
			  'iconsmind-Snake' => 'iconsmind-Snake',
			  'iconsmind-Snow-Dome' => 'iconsmind-Snow-Dome',
			  'iconsmind-Snowflake2' => 'iconsmind-Snowflake2',
			  'iconsmind-Snowman' => 'iconsmind-Snowman',
			  'iconsmind-Socks' => 'iconsmind-Socks',
			  'iconsmind-Soup' => 'iconsmind-Soup',
			  'iconsmind-South-Africa' => 'iconsmind-South-Africa',
			  'iconsmind-Space-Needle' => 'iconsmind-Space-Needle',
			  'iconsmind-Spain' => 'iconsmind-Spain',
			  'iconsmind-Spam-Mail' => 'iconsmind-Spam-Mail',
			  'iconsmind-Speaker2' => 'iconsmind-Speaker2',
			  'iconsmind-Spell-Check' => 'iconsmind-Spell-Check',
			  'iconsmind-Spell-CheckABC' => 'iconsmind-Spell-CheckABC',
			  'iconsmind-Spider' => 'iconsmind-Spider',
			  'iconsmind-Spiderweb' => 'iconsmind-Spiderweb',
			  'iconsmind-Spoder' => 'iconsmind-Spoder',
			  'iconsmind-Spoon' => 'iconsmind-Spoon',
			  'iconsmind-Sports-Clothings1' => 'iconsmind-Sports-Clothings1',
			  'iconsmind-Sports-Clothings2' => 'iconsmind-Sports-Clothings2',
			  'iconsmind-Sports-Shirt' => 'iconsmind-Sports-Shirt',
			  'iconsmind-Spray' => 'iconsmind-Spray',
			  'iconsmind-Squirrel' => 'iconsmind-Squirrel',
			  'iconsmind-Stamp' => 'iconsmind-Stamp',
			  'iconsmind-Stamp-2' => 'iconsmind-Stamp-2',
			  'iconsmind-Stapler' => 'iconsmind-Stapler',
			  'iconsmind-Star' => 'iconsmind-Star',
			  'iconsmind-Starfish' => 'iconsmind-Starfish',
			  'iconsmind-Start2' => 'iconsmind-Start2',
			  'iconsmind-St-BasilsCathedral' => 'iconsmind-St-BasilsCathedral',
			  'iconsmind-St-PaulsCathedral' => 'iconsmind-St-PaulsCathedral',
			  'iconsmind-Structure' => 'iconsmind-Structure',
			  'iconsmind-Student-Hat' => 'iconsmind-Student-Hat',
			  'iconsmind-Student-Hat2' => 'iconsmind-Student-Hat2',
			  'iconsmind-Suit' => 'iconsmind-Suit',
			  'iconsmind-Sum2' => 'iconsmind-Sum2',
			  'iconsmind-Sunglasses' => 'iconsmind-Sunglasses',
			  'iconsmind-Sunglasses-2' => 'iconsmind-Sunglasses-2',
			  'iconsmind-Sunglasses-3' => 'iconsmind-Sunglasses-3',
			  'iconsmind-Sunglasses-Smiley' => 'iconsmind-Sunglasses-Smiley',
			  'iconsmind-Sunglasses-Smiley2' => 'iconsmind-Sunglasses-Smiley2',
			  'iconsmind-Sunglasses-W' => 'iconsmind-Sunglasses-W',
			  'iconsmind-Sunglasses-W2' => 'iconsmind-Sunglasses-W2',
			  'iconsmind-Sunglasses-W3' => 'iconsmind-Sunglasses-W3',
			  'iconsmind-Surprise' => 'iconsmind-Surprise',
			  'iconsmind-Sushi' => 'iconsmind-Sushi',
			  'iconsmind-Sweden' => 'iconsmind-Sweden',
			  'iconsmind-Swimming-Short' => 'iconsmind-Swimming-Short',
			  'iconsmind-Swimmwear' => 'iconsmind-Swimmwear',
			  'iconsmind-Switzerland' => 'iconsmind-Switzerland',
			  'iconsmind-Sync' => 'iconsmind-Sync',
			  'iconsmind-Sync-Cloud' => 'iconsmind-Sync-Cloud',
			  'iconsmind-Tablet' => 'iconsmind-Tablet',
			  'iconsmind-Tablet-2' => 'iconsmind-Tablet-2',
			  'iconsmind-Tablet-3' => 'iconsmind-Tablet-3',
			  'iconsmind-Tablet-Orientation' => 'iconsmind-Tablet-Orientation',
			  'iconsmind-Tablet-Phone' => 'iconsmind-Tablet-Phone',
			  'iconsmind-Tablet-Vertical' => 'iconsmind-Tablet-Vertical',
			  'iconsmind-Tactic' => 'iconsmind-Tactic',
			  'iconsmind-Taj-Mahal' => 'iconsmind-Taj-Mahal',
			  'iconsmind-Teapot' => 'iconsmind-Teapot',
			  'iconsmind-Tee-Mug' => 'iconsmind-Tee-Mug',
			  'iconsmind-Telephone' => 'iconsmind-Telephone',
			  'iconsmind-Telephone-2' => 'iconsmind-Telephone-2',
			  'iconsmind-Temple' => 'iconsmind-Temple',
			  'iconsmind-Thailand' => 'iconsmind-Thailand',
			  'iconsmind-The-WhiteHouse' => 'iconsmind-The-WhiteHouse',
			  'iconsmind-Three-ArrowFork' => 'iconsmind-Three-ArrowFork',
			  'iconsmind-Thumbs-DownSmiley' => 'iconsmind-Thumbs-DownSmiley',
			  'iconsmind-Thumbs-UpSmiley' => 'iconsmind-Thumbs-UpSmiley',
			  'iconsmind-Tie' => 'iconsmind-Tie',
			  'iconsmind-Tie-2' => 'iconsmind-Tie-2',
			  'iconsmind-Tie-3' => 'iconsmind-Tie-3',
			  'iconsmind-Tiger' => 'iconsmind-Tiger',
			  'iconsmind-Time-Clock' => 'iconsmind-Time-Clock',
			  'iconsmind-To-Bottom' => 'iconsmind-To-Bottom',
			  'iconsmind-To-Bottom2' => 'iconsmind-To-Bottom2',
			  'iconsmind-Token' => 'iconsmind-Token',
			  'iconsmind-To-Left' => 'iconsmind-To-Left',
			  'iconsmind-Tomato' => 'iconsmind-Tomato',
			  'iconsmind-Tongue' => 'iconsmind-Tongue',
			  'iconsmind-Tooth' => 'iconsmind-Tooth',
			  'iconsmind-Tooth-2' => 'iconsmind-Tooth-2',
			  'iconsmind-Top-ToBottom' => 'iconsmind-Top-ToBottom',
			  'iconsmind-To-Right' => 'iconsmind-To-Right',
			  'iconsmind-To-Top' => 'iconsmind-To-Top',
			  'iconsmind-To-Top2' => 'iconsmind-To-Top2',
			  'iconsmind-Tower' => 'iconsmind-Tower',
			  'iconsmind-Tower-2' => 'iconsmind-Tower-2',
			  'iconsmind-Tower-Bridge' => 'iconsmind-Tower-Bridge',
			  'iconsmind-Transform' => 'iconsmind-Transform',
			  'iconsmind-Transform-2' => 'iconsmind-Transform-2',
			  'iconsmind-Transform-3' => 'iconsmind-Transform-3',
			  'iconsmind-Transform-4' => 'iconsmind-Transform-4',
			  'iconsmind-Tree2' => 'iconsmind-Tree2',
			  'iconsmind-Tree-22' => 'iconsmind-Tree-22',
			  'iconsmind-Triangle-ArrowDown' => 'iconsmind-Triangle-ArrowDown',
			  'iconsmind-Triangle-ArrowLeft' => 'iconsmind-Triangle-ArrowLeft',
			  'iconsmind-Triangle-ArrowRight' => 'iconsmind-Triangle-ArrowRight',
			  'iconsmind-Triangle-ArrowUp' => 'iconsmind-Triangle-ArrowUp',
			  'iconsmind-T-Shirt' => 'iconsmind-T-Shirt',
			  'iconsmind-Turkey' => 'iconsmind-Turkey',
			  'iconsmind-Turn-Down' => 'iconsmind-Turn-Down',
			  'iconsmind-Turn-Down2' => 'iconsmind-Turn-Down2',
			  'iconsmind-Turn-DownFromLeft' => 'iconsmind-Turn-DownFromLeft',
			  'iconsmind-Turn-DownFromRight' => 'iconsmind-Turn-DownFromRight',
			  'iconsmind-Turn-Left' => 'iconsmind-Turn-Left',
			  'iconsmind-Turn-Left3' => 'iconsmind-Turn-Left3',
			  'iconsmind-Turn-Right' => 'iconsmind-Turn-Right',
			  'iconsmind-Turn-Right3' => 'iconsmind-Turn-Right3',
			  'iconsmind-Turn-Up' => 'iconsmind-Turn-Up',
			  'iconsmind-Turn-Up2' => 'iconsmind-Turn-Up2',
			  'iconsmind-Turtle' => 'iconsmind-Turtle',
			  'iconsmind-Tuxedo' => 'iconsmind-Tuxedo',
			  'iconsmind-Ukraine' => 'iconsmind-Ukraine',
			  'iconsmind-Umbrela' => 'iconsmind-Umbrela',
			  'iconsmind-United-Kingdom' => 'iconsmind-United-Kingdom',
			  'iconsmind-United-States' => 'iconsmind-United-States',
			  'iconsmind-University' => 'iconsmind-University',
			  'iconsmind-Up2' => 'iconsmind-Up2',
			  'iconsmind-Up-2' => 'iconsmind-Up-2',
			  'iconsmind-Up-3' => 'iconsmind-Up-3',
			  'iconsmind-Upload2' => 'iconsmind-Upload2',
			  'iconsmind-Upload-toCloud' => 'iconsmind-Upload-toCloud',
			  'iconsmind-Usb' => 'iconsmind-Usb',
			  'iconsmind-Usb-2' => 'iconsmind-Usb-2',
			  'iconsmind-Usb-Cable' => 'iconsmind-Usb-Cable',
			  'iconsmind-Vector' => 'iconsmind-Vector',
			  'iconsmind-Vector-2' => 'iconsmind-Vector-2',
			  'iconsmind-Vector-3' => 'iconsmind-Vector-3',
			  'iconsmind-Vector-4' => 'iconsmind-Vector-4',
			  'iconsmind-Vector-5' => 'iconsmind-Vector-5',
			  'iconsmind-Vest' => 'iconsmind-Vest',
			  'iconsmind-Vietnam' => 'iconsmind-Vietnam',
			  'iconsmind-View-Height' => 'iconsmind-View-Height',
			  'iconsmind-View-Width' => 'iconsmind-View-Width',
			  'iconsmind-Visa' => 'iconsmind-Visa',
			  'iconsmind-Voicemail' => 'iconsmind-Voicemail',
			  'iconsmind-VPN' => 'iconsmind-VPN',
			  'iconsmind-Wacom-Tablet' => 'iconsmind-Wacom-Tablet',
			  'iconsmind-Walkie-Talkie' => 'iconsmind-Walkie-Talkie',
			  'iconsmind-Wallet' => 'iconsmind-Wallet',
			  'iconsmind-Wallet-2' => 'iconsmind-Wallet-2',
			  'iconsmind-Warehouse' => 'iconsmind-Warehouse',
			  'iconsmind-Webcam' => 'iconsmind-Webcam',
			  'iconsmind-Wifi' => 'iconsmind-Wifi',
			  'iconsmind-Wifi-2' => 'iconsmind-Wifi-2',
			  'iconsmind-Wifi-Keyboard' => 'iconsmind-Wifi-Keyboard',
			  'iconsmind-Window' => 'iconsmind-Window',
			  'iconsmind-Windows' => 'iconsmind-Windows',
			  'iconsmind-Windows-Microsoft' => 'iconsmind-Windows-Microsoft',
			  'iconsmind-Wine-Bottle' => 'iconsmind-Wine-Bottle',
			  'iconsmind-Wine-Glass' => 'iconsmind-Wine-Glass',
			  'iconsmind-Wink' => 'iconsmind-Wink',
			  'iconsmind-Wireless' => 'iconsmind-Wireless',
			  'iconsmind-Witch' => 'iconsmind-Witch',
			  'iconsmind-Witch-Hat' => 'iconsmind-Witch-Hat',
			  'iconsmind-Wizard' => 'iconsmind-Wizard',
			  'iconsmind-Wolf' => 'iconsmind-Wolf',
			  'iconsmind-Womans-Underwear' => 'iconsmind-Womans-Underwear',
			  'iconsmind-Womans-Underwear2' => 'iconsmind-Womans-Underwear2',
			  'iconsmind-Worker-Clothes' => 'iconsmind-Worker-Clothes',
			  'iconsmind-Wreath' => 'iconsmind-Wreath',
			  'iconsmind-Zebra' => 'iconsmind-Zebra',
			  'iconsmind-Zombie' => 'iconsmind-Zombie',
			)
		);	

$linecons = array(
			'type'=>'icons', 
			'title'=>'Linecons', 
			'values'=> array(
				  'linecon-icon-heart' => 'linecon-icon-heart',
				  'linecon-icon-cloud' => 'linecon-icon-cloud',
				  'linecon-icon-star' => 'linecon-icon-star',
				  'linecon-icon-tv' => 'linecon-icon-tv',
				  'linecon-icon-sound' => 'linecon-icon-sound',
				  'linecon-icon-video' => 'linecon-icon-video',
				  'linecon-icon-trash' => 'linecon-icon-trash',
				  'linecon-icon-user' => 'linecon-icon-user',
				  'linecon-icon-key' => 'linecon-icon-key',
				  'linecon-icon-search' => 'linecon-icon-search',
				  'linecon-icon-eye' => 'linecon-icon-eye',
				  'linecon-icon-bubble' => 'linecon-icon-bubble',
				  'linecon-icon-stack' => 'linecon-icon-stack',
				  'linecon-icon-cup' => 'linecon-icon-cup',
				  'linecon-icon-phone' => 'linecon-icon-phone',
				  'linecon-icon-news' => 'linecon-icon-news',
				  'linecon-icon-mail' => 'linecon-icon-mail',
				  'linecon-icon-like' => 'linecon-icon-like',
				  'linecon-icon-photo' => 'linecon-icon-photo',
				  'linecon-icon-note' => 'linecon-icon-note',
				  'linecon-icon-food' => 'linecon-icon-food',
				  'linecon-icon-t-shirt' => 'linecon-icon-t-shirt',
				  'linecon-icon-fire' => 'linecon-icon-fire',
				  'linecon-icon-clip' => 'linecon-icon-clip',
				  'linecon-icon-shop' => 'linecon-icon-shop',
				  'linecon-icon-calendar' => 'linecon-icon-calendar',
				  'linecon-icon-wallet' => 'linecon-icon-wallet',
				  'linecon-icon-vynil' => 'linecon-icon-vynil',
				  'linecon-icon-truck' => 'linecon-icon-truck',
				  'linecon-icon-world' => 'linecon-icon-world',
				  'linecon-icon-clock' => 'linecon-icon-clock',
				  'linecon-icon-paperplane' => 'linecon-icon-paperplane',
				  'linecon-icon-params' => 'linecon-icon-params',
				  'linecon-icon-banknote' => 'linecon-icon-banknote',
				  'linecon-icon-data' => 'linecon-icon-data',
				  'linecon-icon-music' => 'linecon-icon-music',
				  'linecon-icon-megaphone' => 'linecon-icon-megaphone',
				  'linecon-icon-study' => 'linecon-icon-study',
				  'linecon-icon-lab' => 'linecon-icon-lab',
				  'linecon-icon-location' => 'linecon-icon-location',
				  'linecon-icon-display' => 'linecon-icon-display',
				  'linecon-icon-diamond' => 'linecon-icon-diamond',
				  'linecon-icon-pen' => 'linecon-icon-pen',
				  'linecon-icon-bulb' => 'linecon-icon-bulb',
				  'linecon-icon-lock' => 'linecon-icon-lock',
				  'linecon-icon-tag' => 'linecon-icon-tag',
				  'linecon-icon-camera' => 'linecon-icon-camera',
				  'linecon-icon-settings' => 'linecon-icon-settings'
			)
		);
		
$nectar_shortcodes['icon'] = array( 
	'type'=>'regular', 
	'title'=>esc_html__('Icon', 'salient'), 
	'attr'=>array(
		'size'=>array(
			'type'=>'radio', 
			'title'=>esc_html__('Icon Style', 'salient'), 
			'desc' => esc_html__('Tiny is recommended to be used inline with regular text. Small is recommended to be used inline right before heading text. Regular can be used in a variety of places. Large is recommended to be used at the top of columns.', 'salient'),
			'opt'=>array(
				'tiny'=>esc_html__('Tiny','salient'),
				'small'=>esc_html__('Small Circle','salient'),
				'regular'=>esc_html__('Regular','salient'),
				'large'=>esc_html__('Large Circle','salient'),
				'large-2'=>esc_html__('Large Circle Alt','salient'),
			)
		),
		'color'=>array(
			'type'=>'select', 
			'title'  => esc_html__('Color','salient'),
			'values' => array(
			     "accent-color" => esc_html__("Accent-Color",'salient'),
		  		 "extra-color-1" => esc_html__("Extra-Color-1",'salient'),
		  		 "extra-color-2" => esc_html__("Extra-Color-2",'salient'),
		  		 "extra-color-3" => esc_html__("Extra-Color-3",'salient'),
		  		 "extra-color-gradient-1" => esc_html__("Extra-Color-Gradient-1",'salient'),
		  		 "extra-color-gradient-2" => esc_html__("Extra-Color-Gradient-2",'salient')
			)
		),
		'icons' => array(
			'type'=>'icons', 
			'title'=>'Icon', 
			'values'=> $fa_icons
		),
		'icon_size'=>array('type'=>'text', 'title'=>esc_html__('Icon Size', 'salient'), 'desc' => esc_html__('Don\'nt include "px" in your string. e.g. 40 - the default is 64', 'salient')),  
		'enable_animation'=>array('type'=>'checkbox', 'title'=>esc_html__('Enable Animation','salient'), 'desc' => esc_html__('This will cause the icon to appear to draw itself', 'salient')),
		'animation_delay'=>array('type'=>'text', 'title'=>esc_html__('Animation Delay', 'salient'), 'desc' => esc_html__('Enter time in milliseconds e.g. 400', 'salient')),  
		'animation_speed'=>array(
			'type'=>'select', 
			'title'  => esc_html__('Animation Speed','salient'),
			'values' => array(
			     "slow" => esc_html__("Slow",'salient'),
		  		 "medium" => esc_html__("Medium",'salient'),
		  		 "fast" => esc_html__("Fast",'salient')
			)
		),
		'steadysets' => $steadysets_icons,
		'linecons' => $linecons,
		'linea' => $linea,
		'iconsmind' => $iconsmind_icons
		
	) 
);


//Button
$nectar_shortcodes['button'] = array( 
	'type'=>'radios', 
	'title'=>esc_html__('Button', 'salient'), 
	'attr'=>array(
		'size'=>array(
			'type'=>'radio', 
			'title'=>esc_html__('Size', 'salient'), 
			'opt'=>array(
				'small'=> esc_html__('Small', 'salient'), 
				'medium'=> esc_html__('Medium', 'salient'), 
				'large'=> esc_html__('Large', 'salient'),
				'jumbo'=> esc_html__('Jumbo', 'salient'),
				'extra_jumbo'=> esc_html__('Extra Jumbo', 'salient')
			)
		),
		'url'=>array(
			'type'=>'text', 
			'title'=>'Link URL'
		),
		'text'=>array(
			'type'=>'text', 
			'title'=>esc_html__('Text', 'salient')
		),
		'open_new_tab'=>array('type'=>'checkbox', 'title'=>esc_html__('Open Link In New Tab?','salient')),
		'color'=>array(
			'type'=>'regular-select', 
			'title'  => esc_html__('Style','salient'),
			'values' => array(
			     "accent-color" => esc_html__("Regular + Accent Color", 'salient'), 
		  		 "extra-color-1" => esc_html__("Regular + Extra Color-1", 'salient'), 
		  		 "extra-color-2" => esc_html__("Regular + Extra Color-2", 'salient'), 
		  		 "extra-color-3" => esc_html__("Regular + Extra Color-3", 'salient'), 
		  		 "extra-color-gradient-1" => esc_html__("Regular + Color Gradient 1", 'salient'), 
		  		 "extra-color-gradient-2" => esc_html__("Regular + Color Gradient 2", 'salient'), 
		  		 "accent-color-tilt" => esc_html__("Regular W/ Tilt + Accent Color", 'salient'), 
		  		 "extra-color-1-tilt" => esc_html__("Regular W/ Tilt + Extra Color 1", 'salient'), 
		  		 "extra-color-2-tilt" => esc_html__("Regular W/ Tilt + Extra Color 2", 'salient'), 
		  		 "extra-color-3-tilt" => esc_html__("Regular W/ Tilt + Extra Color 3", 'salient'), 
		  		 "see-through" => esc_html__("See-Through", 'salient'), 
		  		 "see-through-2" => esc_html__("See-Through + Solid On Hover", 'salient'),
		  		 "see-through-3" => esc_html__("See-Through + Solid On Hover Alt", 'salient'),
		  		 "see-through-extra-color-gradient-1" => esc_html__("See-Through + Color Gradient 1", 'salient'),
		  		 "see-through-extra-color-gradient-2" => esc_html__("See-Through + Color Gradient 2", 'salient'),
		  		 "see-through-3d" => esc_html__("See-Through + 3D On Hover", 'salient'), 
			)
		),
		'color_override' =>array('type'=>'custom', 'title'  => esc_html__('Button Color Override','salient')),
		'hover_color_override' =>array('type'=>'custom', 'title'  => esc_html__('Button Hover Color Override','salient')),
		'hover_text_color_override'=>array(
			'type'=>'regular-select', 
			'title'  => esc_html__('Hover Text Color','salient'),
			'values' => array(
			     "#fff" => "Light",
		  		 "#000" => "Dark",
			)
		),
		'icons' => array(
			'type'=>'button-icons', 
			'title'=>'Icon', 
			'values'=> $fa_icons
		),
		'steadysets' => $steadysets_icons,
		'linecons' => $linecons,
		'linea' => $linea,
		'iconsmind' => $iconsmind_icons
	) 
);


//Toggle
$nectar_shortcodes['toggles'] = array( 
	'type'=>'dynamic', 
	'title'=>esc_html__('Toggle Panels', 'salient' ), 
	'attr'=>array(
		'toggles'=>array('type'=>'custom')
	)
);

//Tabbed Sections
$nectar_shortcodes['tabbed_section'] = array( 
	'type'=>'dynamic',  
	'title'=>esc_html__('Tabbed Section', 'salient' ), 
	'attr'=>array(
		'tabs'=>array('type'=>'custom')
	)
);
 

//Testimonial Slider
$nectar_shortcodes['testimonial_slider'] = array( 
	'type'=>'dynamic',  
	'title'=>esc_html__('Testimonial Slider', 'salient' ), 
	'attr'=>array(
		'testimonials'=>array('type'=>'custom')
	)
);


//Bar Graph
/*
$nectar_shortcodes['bar_graph'] = array( 
	'type'=>'dynamic', 
	'title'=>esc_html__('Bar Graph', 'salient' ), 
	'attr'=>array(
		'bar_graph'=>array('type'=>'custom')
	)
); */

//Clients
$nectar_shortcodes['clients'] = array( 
	'type'=>'dynamic', 
	'title'=>esc_html__('Clients', 'salient' ), 
	'attr'=>array(
		'clients'=>array('type'=>'custom', 'title'  => esc_html__('Image','salient'))
	)
);
 

//Pricing Table
$nectar_shortcodes['pricing_table'] = array( 
	'type'=>'direct_to_editor', 
	'title'=>esc_html__('Pricing Table', 'salient' ), 
	'attr'=>array( 
		'columns'=>array(
			'type'=>'radio', 
			'title'=>esc_html__('Columns', 'salient'), 
			'desc' => esc_html__('How many columns would you like?', 'salient'),
			'opt'=>array(
				'2'=>'Two',
				'3'=>'Three',
				'4'=>'Four',
				'5'=>'Five'
			)
		)
	)
);

//Team Member
$nectar_shortcodes['team_member'] = array( 
	'type'=>'regular', 
	'title'=>esc_html__('Team Member', 'salient' ), 
	'attr'=>array( 
		'image'=>array('type'=>'custom', 'title'  => esc_html__('Image','salient')),
		'name'=>array('type'=>'text', 'title'=>esc_html__('Name', 'salient')),
		'job_position'=>array('type'=>'text', 'title'=>esc_html__('Job Position', 'salient')),
		'description'=>array('type'=>'textarea', 'title'=> esc_html__('Description', 'salient')),
		'social'=>array('type'=>'textarea', 'title'=>esc_html__('Social Media', 'salient'), 'desc' => esc_html__('Enter any social media links with a comma separated list. e.g. Facebook,http://facebook.com, Twitter,http://twitter.com', 'salient')),  
		'link_element'=>array(
			'type'=>'regular-select', 
			'title'  => esc_html__('Team Member Link Type','salient'),
			'values' => array(
			     "none" => "None",
		  		 "image" => "Image",
		  		 "name" => "Name",
		  		 "both" => "Both"
			)
		),
		'link_url'=>array('type'=>'text', 'title'=>esc_html__('Team Member Link URL', 'salient'),'desc' => esc_html__('Will only be used if Link Type is not set to "None".','salient')),
		'color'=>array(
			'type'=>'select', 
			'title'  => esc_html__('Link Color','salient'),
			'values' => array(
			     "accent-color" => esc_html__("Accent-Color",'salient'),
		  		 "extra-color-1" => esc_html__("Extra-Color-1",'salient'),
		  		 "extra-color-2" => esc_html__("Extra-Color-2",'salient'),
		  		 "extra-color-3" => esc_html__("Extra-Color-3",'salient')
			)
		)
	)
);

//Carousel
$nectar_shortcodes['carousel'] = array( 
	'type'=>'direct_to_editor', 
	'title'=>esc_html__('Carousel', 'salient' ), 
	'attr'=>array(
		'carousel_title'=>array(
			'type'=>'text', 
			'title'=> esc_html__('Carousel Title', 'salient')
		),
		'scroll_speed'=>array(
			'type'=>'text', 
			'title'=> esc_html__('Scroll Speed', 'salient'),
			'desc' => esc_html__('Enter in milliseconds (default is 700)', 'salient'),
		),
		'autorotate'=>array(
			'type'=>'checkbox', 
			'title'=> esc_html__('Autorotate', 'salient'),
			'desc' => esc_html__('Would you like the carousel the transition automatically?', 'salient'),
		),
		'easing'=>array(
			'type'=>'select', 
			'title'=> esc_html__('Easing', 'salient'), 
			'values'=>array(
				'linear'=>'linear',
				'swing'=>'swing',
				'easeInQuad'=>'easeInQuad',
				'easeOutQuad' => 'easeOutQuad',
				'easeInOutQuad'=>'easeInOutQuad',
				'easeInCubic'=>'easeInCubic',
				'easeOutCubic'=>'easeOutCubic',
				'easeInOutCubic'=>'easeInOutCubic',
				'easeInQuart'=>'easeInQuart',
				'easeOutQuart'=>'easeOutQuart',
				'easeInOutQuart'=>'easeInOutQuart',
				'easeInQuint'=>'easeInQuint',
				'easeOutQuint'=>'easeOutQuint',
				'easeInOutQuint'=>'easeInOutQuint',
				'easeInExpo'=>'easeInExpo',
				'easeOutExpo'=>'easeOutExpo',
				'easeInOutExpo'=>'easeInOutExpo',
				'easeInSine'=>'easeInSine',
				'easeOutSine'=>'easeOutSine',
				'easeInOutSine'=>'easeInOutSine',
				'easeInCirc'=>'easeInCirc',
				'easeOutCirc'=>'easeOutCirc',
				'easeInOutCirc'=>'easeInOutCirc',
				'easeInElastic'=>'easeInElastic',
				'easeOutElastic'=>'easeOutElastic',
				'easeInOutElastic'=>'easeInOutElastic',
				'easeInBack'=>'easeInBack',
				'easeOutBack'=>'easeOutBack',
				'easeInOutBack'=>'easeInOutBack',
				'easeInBounce'=>'easeInBounce',
				'easeOutBounce'=>'easeOutBounce',
				'easeInOutBounce'=>'easeInOutBounce',
			),
			'desc' => '<a href="http://jqueryui.com/resources/demos/effect/easing.html" target="_blank">'. esc_html__("Click here",'salient') .'</a> ' . esc_html__("to see examples of these.", 'salient')
		),
	)
);


$nectar_shortcodes['social_buttons'] = array( 
	'type'=>'regular', 
	'title'=>esc_html__('Social Buttons', 'salient' ), 
	'attr'=>array( 
		'full_width_icons'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Display full width?', 'salient'),
			'desc' => esc_html__('This will make your social icons expand to fit edge to edge in whatever space they\'re placed.', 'salient')
		),
		'hide_share_count'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Hide Share Count?', 'salient'),
			'desc' => esc_html__('This will remove your share counts from displaying to the user', 'salient')
		),

		'nectar_love'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Nectar Love', 'salient'),
			'desc' => esc_html__('Check to enable', 'salient')
		),
		'facebook'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Facebook', 'salient'),
			'desc' => esc_html__('Check to enable', 'salient')
		),
		'twitter'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Twitter', 'salient'),
			'desc' => esc_html__('Check to enable', 'salient')
		),
		'pinterest'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Pinterest', 'salient'),
			'desc' => esc_html__('Check to enable', 'salient')
		),
		'google_plus'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Google+', 'salient'),
			'desc' => esc_html__('Check to enable', 'salient')
		),
		'linkedin'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('LinkedIn', 'salient'),
			'desc' => esc_html__('Check to enable', 'salient')
		)
	)
);
	 
//Video
$nectar_shortcodes['video'] = array(  
	'type'=>'regular', 
	'title'=>esc_html__('Video', 'salient' ),  
	'attr'=>array( 
		  'mp4'=>array('type'=>'text', 'title'=>esc_html__('MP4 File URL', 'salient'), 'desc' => esc_html__('Only supply the formats you desire, this shortcode is just a shortcut to place the default WordPress video player.', 'salient') ),
		  'webm'=>array('type'=>'text', 'title'=>esc_html__('WEBM File URL', 'salient')),
			'ogv'=>array('type'=>'text', 'title'=>esc_html__('OGV FILE URL', 'salient')),
			'poster' => array(
				'type'  =>'custom', 
				'title' => esc_html__('Preview Image','salient'), 
				'desc'  => esc_html__('The preview image should be the same dimensions as your video.','salient')
			)
	 )
);

//Audio
$nectar_shortcodes['audio'] = array( 
	'type'=>'regular', 
	'title'=>esc_html__('Audio', 'salient' ), 
	'attr'=>array( 
		'mp3'=>array('type'=>'text', 'title'=>esc_html__('MP3 File URL', 'salient')),
		'ogg'=>array('type'=>'text', 'title'=>esc_html__('OGA File URL', 'salient'))
	)
);


#-----------------------------------------------------------------
# Recent Posts/Projects 
#-----------------------------------------------------------------


$nectar_shortcodes['header_7'] = array( 
	'type'=>'heading', 
	'title'=>esc_html__('Portfolio/Blog', 'salient' )
);



//Portfolio
$portfolio_types = get_terms('project-type');

$types_options = array("all" => "All");

foreach ($portfolio_types as $type) {
	$types_options[$type->slug] = $type->name;
}


$nectar_shortcodes['nectar_portfolio'] = array( 
	'type'=>'regular', 
	'title'=>esc_html__('Portfolio', 'salient' ), 
	'attr'=>array( 
		'layout'=>array(
			'type'=>'radio', 
			'title'=>esc_html__('Layout', 'salient'), 
			'opt'=>array(
				'3'=>'3 Columns',
				'4'=>'4 Columns',
				'fullwidth'=>'Fullwidth'
			)
		),
		'constrain_max_cols'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Constrain Max Columns to 4?', 'salient'),
			'desc' => esc_html__("This will change the max columns to 4 (default is 5 for fullwidth). Activating this will make it easier to create a grid with no empty spaces at the end of the list on all screen sizes.", 'salient')
		),
		'category' => array(
			'type' => 'multi-select',
			'title' => esc_html__('Portfolio Categories','salient'),
			'desc' => esc_html__('Please select the categories you would like to display for your portfolio. You can select multiple categories too (ctrl + click on PC and command + click on Mac).','salient'),
			'values' => $types_options
		),
		'starting_category' => array(
			'type' => 'regular-select',
			'title' => esc_html__('Starting Category','salient'),
			'desc' => esc_html__('Please select the category you would like you\'re portfolio to start filtered on','salient'),
			'values' => $types_options
		),
		'project_style' => array(
			'type' => 'regular-select',
			'title' => esc_html__('Project Style','salient'),
			'desc' => esc_html__('Please select the style you would like your projects to display in.','salient'),
			'values' => array(
			   '1' => esc_html__('Meta below thumb w/ links on hover','salient'),
			   '2' => esc_html__('Meta on hover + entire thumb link','salient'),
			   '3' => esc_html__('Title overlaid w/ zoom effect on hover','salient'),
			   '4' => esc_html__('Meta from bottom on hover + entire thumb link','salient')
			)
		),
		
		'masonry_style'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Masonry Style', 'salient'),
			'desc' => esc_html__('This will allow your portfolio items to display in a masonry layout as opposed to a fixed grid. You can define your masonry sizes in each project. If using the full width layout, will only be active with the alternative project style.', 'salient')
		),
		
		'enable_sortable'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Enable Sortable', 'salient'),
			'desc' => esc_html__('Checking this box will allow your portfolio to display sortable filters', 'salient')
		),

		'horizontal_filters'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Horizontal Filters', 'salient'),
			'desc' => esc_html__('This will allow your filters to display horizontally instead of in a dropdown. (Only used if you enable sortable above.)', 'salient')
		),
		'enable_pagination'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Enable Pagination', 'salient'),
			'desc' => esc_html__('Would you like to enable pagination for this portfolio?', 'salient')
		),
		'pagination_type'=>array(
			'type'=>'regular-select', 
			'title'=>esc_html__('Pagination Type', 'salient'), 
			'values'=>array(
				'default' => esc_html__('Default', 'salient'), 
			    'infinite_scroll' => esc_html__('Infinite Scroll', 'salient')
			)
		),
		'projects_per_page'=>array(
			'type'=>'text', 
			'title'=>esc_html__('Projects Per Page', 'salient'),
			'desc' => esc_html__('How many projects would you like to display per page? If pagination is not enabled, will simply show this number of projects. Enter as a number example "20"', 'salient')
		),
		'lightbox_only'=>array( 
			'type'=>'checkbox', 
			'title'=>esc_html__('Lightbox Only?', 'salient'), 
			'desc' => esc_html__('This will remove the single project page from being accessible thus rendering your portfolio into only a gallery.', 'salient')
		)
	)
);





$nectar_shortcodes['recent_projects'] = array( 
	'type'=>'direct_to_editor', 
	'title'=>esc_html__('Recent Projects', 'salient' ), 
	'attr'=>array( 
		'full_width'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Full Width Carousel?', 'salient'),
			'desc' => esc_html__('This will make your carousel extend the full width of the page. Won\'t work in a column shortcode!', 'salient')
		),
		'heading'=>array(
			'type'=>'text', 
			'title'=>esc_html__('Heading Text', 'salient'),
			'desc' => esc_html__('Enter any text you would like for the heading of your carousel', 'salient')
		),
		'page_link_text'=>array(
			'type'=>'text', 
			'title'=>esc_html__('Page Link Text', 'salient'),
			'desc' => esc_html__('This will be the text that is in a link leading users to your desired page (will be omitted for full width carousels and an icon will be used instead)', 'salient')
		),
		'page_link_url'=>array(
			'type'=>'text', 
			'title'=>esc_html__('Page Link URL', 'salient'),
			'desc' => esc_html__('Enter portfolio page URL you would like to link to. Remember to include "http://"!', 'salient')
		),
		
		'hide_controls'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Hide Carousel Controls?', 'salient'),
			'desc' => esc_html__('Checking this box will remove the controls from your carousel', 'salient')
		),
		
		'number_to_display'=>array(
			'type'=>'text', 
			'title'=>esc_html__('Number of Projects To Show', 'salient'),
			'desc' => esc_html__('Enter as a number example "6"', 'salient')
		),
		'category' => array(
			'type' => 'multi-select',
			'title' => esc_html__('Category To Display From','salient'),
			'values' => $types_options
		),
		'project_style' => array(
			'type' => 'regular-select',
			'title' => esc_html__('Project Style','salient'),
			'desc' => esc_html__('Please select the style you would like your projects to display in.','salient'),
			'values' => array(
			   '1' => esc_html__('Meta below thumb w/ links on hover','salient'),
			   '2' => esc_html__('Meta on hover + entire thumb link','salient'),
			   '3' => esc_html__('Title overlaid w/ zoom effect on hover','salient'),
			   '4' => esc_html__('Meta from bottom on hover + entire thumb link','salient')
			)
		),
		'lightbox_only'=>array( 
			'type'=>'checkbox', 
			'title'=>esc_html__('Lightbox Only?', 'salient'), 
			'desc' => esc_html__('This will remove the single project page from being accessible thus rendering your portfolio into only a gallery.', 'salient')
		)
	)
);




//Blog
$blog_types = get_categories();

$blog_options = array("all" => "All");

foreach ($blog_types as $type) {
	$blog_options[$type->slug] = $type->name;
}


$nectar_shortcodes['nectar_blog'] = array( 
	'type'=>'regular', 
	'title'=>esc_html__('Blog', 'salient' ), 
	'attr'=>array( 
		'layout'=>array(
			'type'=>'regular-select', 
			'title'=>esc_html__('Layout', 'salient'), 
			'values'=>array(
				'std-blog-sidebar' => esc_html__('Standard Blog W/ Sidebar', 'salient'), 
			    'std-blog-fullwidth' => esc_html__('Standard Blog No Sidebar', 'salient'),
			    'masonry-blog-sidebar' => esc_html__('Masonry Blog W/ Sidebar', 'salient'),
			    'masonry-blog-fullwidth' => esc_html__('Masonry Blog No Sidebar', 'salient'),
			    'masonry-blog-full-screen-width' => esc_html__('Masonry Blog Fullwidth', 'salient')
			)
		),
		'category' => array(
			'type' => 'multi-select',
			'title' => esc_html__('Blog Categories', 'salient'),
			'desc' => esc_html__('Please select the categories you would like to display for your blog. You can select multiple categories too (ctrl + click on PC and command + click on Mac).', 'salient'),
			'values' => $blog_options
		),
		'enable_pagination'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Enable Pagination', 'salient'),
			'desc' => esc_html__('Would you like to enable pagination?', 'salient')
		),
		'pagination_type'=>array(
			'type'=>'regular-select', 
			'title'=>esc_html__('Pagination Type', 'salient'), 
			'values'=>array(
				'default' => esc_html__('Default', 'salient'), 
			    'infinite_scroll' => esc_html__('Infinite Scroll', 'salient')
			)
		),
		'posts_per_page'=>array(
			'type'=>'text', 
			'title'=>esc_html__('Posts Per Page', 'salient'),
			'desc' => esc_html__('How many posts would you like to display per page? If pagination is not enabled, will simply show this number of posts. Enter as a number example "10"', 'salient')
		)
	)
);




$nectar_shortcodes['recent_posts'] = array( 
	'type'=>'direct_to_editor', 
	'title'=>esc_html__('Recent Posts', 'salient' ), 
	'attr'=>array( 
		'title_labels'=>array(
			'type'=>'checkbox', 
			'title'=>esc_html__('Enable Title Labels?', 'salient'),
			'desc' => esc_html__('These labels are defined by you in the "Blog Options" tab of your theme options panel.', 'salient')
		),
		'category' => array(
			'type' => 'multi-select',
			'title' => esc_html__('Category To Display From', 'salient'),
			'values' => $blog_options
		)
	)
);
	
	


		//Shortcode html
		$html_options = null;
		
		$shortcode_html = '
		
		<div id="nectar-sc-heading">
		
		<div id="nectar-sc-generator" class="mfp-hide mfp-with-anim">
		    					
			<div class="shortcode-content">
				<div id="nectar-sc-header">
					<div class="label"><strong>'.esc_html__('Nectar Shortcodes', 'salient').'</strong></div>			
					<div class="content"><select id="nectar-shortcodes" data-placeholder="' . esc_html__("Choose a shortcode", 'salient') .'">
				    <option></option>';
					
					foreach( $nectar_shortcodes as $shortcode => $nectar_options ){
						
						if(strpos($shortcode,'header') !== false) {
							$shortcode_html .= '<optgroup label="'.$nectar_options['title'].'">';
						}
						else {
							$shortcode_html .= '<option value="'.$shortcode.'">'.$nectar_options['title'].'</option>';
							$html_options .= '<div class="shortcode-options" id="options-'.$shortcode.'" data-name="'.$shortcode.'" data-type="'.$nectar_options['type'].'">';
							
							if( !empty($nectar_options['attr']) ){
								 foreach( $nectar_options['attr'] as $name => $attr_option ){
									$html_options .= nectar_option_element( $name, $attr_option, $nectar_options['type'], $shortcode );
								 }
							}
			
							$html_options .= '</div>'; 
						}
						
					} 
			
			$shortcode_html .= '</select></div></div>'; 	
		
	
		 echo $shortcode_html . $html_options; ?>
			
			<div id="shortcode-content">
				
				<div class="label"><label id="option-label" for="shortcode-content"><?php echo __( 'Content: ', 'salient' ); ?> </label></div>
				<div class="content"><textarea id="shortcode_content"></textarea></div>
			
			    <div class="hr"></div>
			    
			</div>
		
			<code class="shortcode_storage"><span id="shortcode-storage-o" style=""></span><span id="shortcode-storage-d"></span><span id="shortcode-storage-c" style=""></span></code>
			<a class="btn" id="add-shortcode"><?php echo __( 'Add Shortcode', 'salient' ); ?></a>
			
		</div>

	</div>	
		
	<?php 
}



//Option Element Function
	
function nectar_option_element( $name, $attr_option, $type, $shortcode ){
	
	$option_element = null;
	
	(isset($attr_option['desc']) && !empty($attr_option['desc'])) ? $desc = '<p class="description">'.$attr_option['desc'].'</p>' : $desc = '';
	
	if(isset($attr_option['half_width']) && $attr_option['half_width'] == 'true') $option_element .= '<div class="column-wrap"> <div class="half_width">';
	if(isset($attr_option['second_half_width']) && $attr_option['second_half_width'] == 'true') $option_element .= '<div class="second_half_width">';
		
	switch( $attr_option['type'] ){
		
	case 'radio':
	    
		$option_element .= '<div class="label"><strong>'.$attr_option['title'].': </strong></div><div class="content">';
	    foreach( $attr_option['opt'] as $val => $title ){
	    
		(isset($attr_option['def']) && !empty($attr_option['def'])) ? $def = $attr_option['def'] : $def = '';
		
		 $option_element .= '
			<label for="shortcode-option-'.$shortcode.'-'.$name.'-'.$val.'">'.$title.'</label>
		    <input class="attr" type="radio" data-attrname="'.$name.'" name="'.$shortcode.'-'.$name.'" value="'.$val.'" id="shortcode-option-'.$shortcode.'-'.$name.'-'.$val.'"'. ( $val == $def ? ' checked="checked"':'').'>';
	    }
		
		$option_element .= $desc . '</div>';
		
	    break;
		
	case 'checkbox':
		
		$option_element .= '<div class="label"><label for="' . $name . '"><strong>' . $attr_option['title'] . ': </strong></label></div>    <div class="content"> <input type="checkbox" class="' . $name . '" id="' . $name . '" />'. $desc. '</div> ';
		
		break;	
	
	case 'select':

		$option_element .= '
		<div class="label"><label for="'.$name.'"><strong>'.$attr_option['title'].': </strong></label></div>
		
		<div class="content"><select id="'.$name.'">';
			$values = $attr_option['values'];
			foreach( $values as $value ){
		    	$option_element .= '<option value="'.$value.'">'.$value.'</option>';
			}
		$option_element .= '</select>' . $desc . '</div>';

		break;
	
	case 'regular-select':
		
		if($attr_option['title'] == 'Starting Category') { $option_element .= '<div class="starting_category">'; }
		
		$option_element .= '
		<div class="label"><label for="'.$name.'"><strong>'.$attr_option['title'].': </strong></label></div>
		
		<div class="content"><select id="'.$name.'">';
			$values = $attr_option['values'];
			foreach( $values as $k => $v ){
		    	$option_element .= '<option value="'.$k.'">'.$v.'</option>';
			}
		$option_element .= '</select>' . $desc . '</div>';
		
		if($attr_option['title'] == 'Starting Category') { $option_element .= '</div>'; }
		
		break;
	
	case 'multi-select':
		
		$option_element .= '
		<div class="label"><label for="'.$name.'"><strong>'.$attr_option['title'].': </strong></label></div>
		
		<div class="content"><select multiple="multiple" id="'.$name.'">';
			$values = $attr_option['values'];
			foreach( $values as $k => $v ){
		    	$option_element .= '<option value="'.$k.'">'.$v.'</option>';
			}
		$option_element .= '</select>' . $desc . '</div>';
		
		break;
		
	case 'icons':
		if($attr_option['title'] == 'Icon') {
			$first_select = '<div class="label"><label><strong>Font Set: </strong></label></div> <div class="content"><select name="icon-set-select" class="skip-processing"> <option value="icon">Font Awesome</option> <option value="iconsmind">Iconsmind</option> <option value="steadysets">Steadysets</option>  <option value="linecons">Linecons</option> <option value="linea">Linea</option> </select></div> <div class="clear"></div>';
		} else {
			$first_select = null;
		}
		
		$parsed_title = str_replace(" ","-",$attr_option['title']);
		 
		$option_element .= $first_select.'
		
		<div class="icon-option '.strtolower($parsed_title).'">';
			$values = $attr_option['values'];
			foreach( $values as $k => $value ){
				if($attr_option['title'] == 'Linea') {
					$option_element .= '<i data-svg-val="'.$k.'" class="'.$value.'"></i>';
				} else {
					$option_element .= '<i class="'.$value.'"></i>';
				}
				
			}
		$option_element .= $desc . '</div>';
		
		break;

	
	case 'button-icons':
		if($attr_option['title'] == 'Icon') {
			$first_select = '<div class="label"><label><strong>Font Set: </strong></label></div> <div class="content"><select name="icon-set-select" class="skip-processing"> <option value="none">None</option> <option value="default-arrow">Default Arrow</option> <option value="icon">Font Awesome</option> <option value="iconsmind">Iconsmind</option> <option value="steadysets">Steadysets</option>  <option value="linecons">Linecons</option>  </select></div> <div class="clear no-line"></div>';
		} else {
			$first_select = null;
		}
		
		$parsed_title = str_replace(" ","-",$attr_option['title']);
		 
		$option_element .= $first_select.'
		
		<div class="icon-option '.strtolower($parsed_title).'">';
			$values = $attr_option['values'];
			foreach( $values as $value ){
		    	$option_element .= '<i class="'.$value.'"></i>';
			}
		$option_element .= $desc . '</div>';
		
		break;	
		
	case 'custom':
 
		if( $name == 'tabs' ){
			$option_element .= '
			<div class="shortcode-dynamic-items" id="options-item" data-name="item">
				<div class="shortcode-dynamic-item">
					<div class="label"><label><strong>Title: </strong></label></div>
					<div class="content"><input class="shortcode-dynamic-item-input" type="text" name="" value="" /></div>
					<div class="label"><label><strong>Tab Content: </strong></label></div>
					<div class="content"><textarea class="shortcode-dynamic-item-text" type="text" name="" /></textarea></div>
				</div>
			</div>
			<a href="#" class="btn blue remove-list-item">'.esc_html__('Remove Tab', 'salient' ). '</a> <a href="#" class="btn blue add-list-item">'.esc_html__('Add Tab', 'salient' ).'</a>';
			
		}

		if( $name == 'toggles' ){
			$option_element .= '
			
			<div class="shortcode-dynamic-items" id="options-item" data-name="item">
			
				<div class="label"><label><strong>Turn into accordion?</strong>:</label></div>
				<div class="content">
					<input id="shortcode-option-carousel" class="accordion" type="checkbox" name="accordion">
				</div>
				<div class="clear"></div>

				<div class="shortcode-dynamic-item">
					<div class="label"><label><strong>Title: </strong></label></div>
					<div class="content"><input class="shortcode-dynamic-item-input" type="text" name="" value="" /></div>
					<div class="label"><label><strong>Tab Content: </strong></label></div>
					<div class="content"><textarea class="shortcode-dynamic-item-text" type="text" name="" /></textarea></div>
					<div class="label"><label><strong>Color: </strong></label></div>
					<div class="content">
						<select class="dynamic-select" id="color">
							<option value="Accent-Color">Accent-Color</option>
							<option value="Extra-Color-1">Extra-Color-1</option>
							<option value="Extra-Color-2">Extra-Color-2</option>
							<option value="Extra-Color-3">Extra-Color-3</option>
						</select>
					</div>
				</div>
			</div>
			<a href="#" class="btn blue remove-list-item">'.esc_html__('Remove Toggle', 'salient' ). '</a> <a href="#" class="btn blue add-list-item">'.esc_html__('Add Toggle', 'salient' ).'</a>';
			
		}  
		
		elseif( $name == 'bar_graph' ){
			$option_element .= '
			<div class="shortcode-dynamic-items" id="options-item" data-name="item">
				<div class="shortcode-dynamic-item">
					<div class="label"><label><strong>Title: </strong></label></div>
					<div class="content"><input class="shortcode-dynamic-item-input" type="text" name="" value="" /></div>
					<div class="label"><label><strong>Bar Percent: </strong></label></div>
					<div class="content dd-percent"><input class="shortcode-dynamic-item-input percent" data-slider="true"  data-slider-range="1,100" data-slider-step="1" type="text" name=""  value="" /></div><div class="clear no-border"></div>
					<div class="label"><label><strong>Color: </strong></label></div>
					<div class="content">
						<select class="dynamic-select" id="color">
							<option value="Accent-Color">Accent-Color</option>
							<option value="Extra-Color-1">Extra-Color-1</option>
							<option value="Extra-Color-2">Extra-Color-2</option>
							<option value="Extra-Color-3">Extra-Color-3</option>
						</select>
					</div>
				</div>
			</div>
			<a href="#" class="btn blue remove-list-item">'.esc_html__('Remove Bar', 'salient' ). '</a> <a href="#" class="btn blue add-list-item">'.esc_html__('Add Bar', 'salient' ).'</a>';
			
		} 
		
		elseif( $name == 'testimonials' ){
			$option_element .= '
			
			<div class="label"><label for="shortcode-option-autorotate"><strong>Autorotate?: </strong></label></div>
			<div class="content"><input class="attr" type="text" data-attrname="autorotate" value="" />If you would like this to autorotate, enter the rotation speed in <b>miliseconds</b> here. i.e 5000</div>
			
			<div class="clear"></div>
			
			<div class="label"><label for="shortcode-option-autorotate"><strong>Disable height animation?: </strong></label></div>
			<div class="content"><input type="checkbox" class="disable_height_animation" value="" />Your testimonial slider will animate the height of itself to match the height of the testimonial being shown - this will remove that and simply set the height equal to the tallest testimonial to allow your content below to remain stagnant instead of moving up/down.</div>
			
			<div class="clear"></div>
			
			<div class="shortcode-dynamic-items testimonials" id="options-item" data-name="testimonial">
				<div class="shortcode-dynamic-item">
					<div class="label"><label><strong>Name: </strong></label></div>
					<div class="content"><input class="shortcode-dynamic-item-input" type="text" name="" value="" /></div>
					<div class="label"><label><strong>Quote: </strong></label></div>
					<div class="content"><textarea class="quote" name="quote"></textarea></div>
				</div>
			</div>

			<a href="#" class="btn blue remove-list-item">'.esc_html__('Remove Testimonial', 'salient' ). '</a> <a href="#" class="btn blue add-list-item">'.esc_html__('Add Testimonial', 'salient' ).'</a>';
			
		} 
		
		elseif( $name == 'image' ){
			$option_element .= '
				<div class="shortcode-dynamic-item" id="options-item" data-name="image-upload">
					<div class="label"><label><strong> '.$attr_option['title'].' </strong></label></div>
					<div class="content">
					
					 <input type="hidden" id="options-item"  />
			         <img class="redux-opts-screenshot" id="image_url" src="" />
			         <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);"class="redux-opts-upload button-secondary" rel-id="">' . esc_html__('Upload', 'salient') . '</a>
			         <a href="javascript:void(0);" class="redux-opts-upload-remove" style="display: none;">' . esc_html__('Remove Upload', 'salient') . '</a>';
					
					if(!empty($desc)) $option_element .= $desc;
					
					$option_element .='
					</div>
				</div>';
		}

		elseif( $name == 'poster' ){
			$option_element .= '
				<div class="shortcode-dynamic-item" id="options-item" data-name="image-upload">
					<div class="label"><label><strong> '.$attr_option['title'].' </strong></label></div>
					<div class="content">
					
					 <input type="hidden" id="options-item"  />
			         <img class="redux-opts-screenshot" id="poster" src="" />
			         <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);"class="redux-opts-upload button-secondary" rel-id="">' . esc_html__('Upload', 'salient') . '</a>
			         <a href="javascript:void(0);" class="redux-opts-upload-remove" style="display: none;">' . esc_html__('Remove Upload', 'salient') . '</a>';
					
					if(!empty($desc)) $option_element .= $desc;
					
					$option_element .='
					</div>
				</div>';
		}

		elseif( $name == 'color'){
			
			if(get_bloginfo('version') >= '3.5') {
	           $option_element .= '
	           <div class="label"><label><strong>Background Color: </strong></label></div>
			   <div class="content"><input type="text" value="" class="popup-colorpicker sc-gen" style="width: 70px;" data-default-color=""/></div>';
	        } else {
	           $option_element .='You\'re using an outdated version of WordPress. Please update to use this feature.';
	        }	
				
		}

		elseif( $name == 'text_color'){
			
			if(get_bloginfo('version') >= '3.5') {
	           $option_element .= '
	           <div class="label"><label><strong>Color: </strong></label></div>
			   <div class="content"><input type="text" value="" class="popup-colorpicker simple sc-gen" style="width: 70px;" data-default-color=""/></div>';
	        } else {
	           $option_element .='You\'re using an outdated version of WordPress. Please update to use this feature.';
	        }	
				
		}

		elseif( $name == 'color_override'){
			
			if(get_bloginfo('version') >= '3.5') {
	           $option_element .= '
	           <div class="label"><label><strong>Color Override:</strong></label></div>
			   <div class="content"><input type="text" value="" class="popup-colorpicker sc-gen" style="width: 70px;" data-default-color=""/></div>';
	        } else {
	           $option_element .='You\'re using an outdated version of WordPress. Please update to use this feature.';
	        }	
				
		}
		
		elseif( $name == 'hover_color_override'){
			
			if(get_bloginfo('version') >= '3.5') {
	           $option_element .= '
	           <div class="label"><label><strong>Hover BG Color:</strong></label></div>
			   <div class="content"><input type="text" value="" class="popup-colorpicker sc-gen" style="width: 70px;" data-default-color=""/></div>';
	        } else {
	           $option_element .='You\'re using an outdated version of WordPress. Please update to use this feature.';
	        }	
				
		}
		
		elseif( $name == 'clients' ){
			$option_element .= '
			<div class="shortcode-dynamic-items clients" id="options-item" data-name="item">
			    
				<div class="label"><label><strong>Columns</strong>:</label></div>
				<div class="content">
					<label for="shortcode-option-button-2-col" class="inline">Two</label>
					<input id="shortcode-option-button-2-col" class="attr" type="radio" value="2" name="client_columns[]" data-attrname="columns">
					<label for="shortcode-option-button-3-col" class="inline">Three</label>
					<input id="shortcode-option-button-3-col" class="attr" type="radio" value="3" name="client_columns[]" data-attrname="columns">
					<label for="shortcode-option-button-4-col" class="inline">Four</label>
					<input id="shortcode-option-button-4-col" class="attr" type="radio" value="4" name="client_columns[]" data-attrname="columns">
					<label for="shortcode-option-button-5-col" class="inline">Five</label>
					<input id="shortcode-option-button-5-col" class="attr" type="radio" value="5" name="client_columns[]" data-attrname="columns">
					<label for="shortcode-option-button-6-col" class="inline">Six</label>
					<input id="shortcode-option-button-6-col" class="attr" type="radio" value="6" name="client_columns[]" data-attrname="columns">
				</div>
				
				<div class="clear"></div>
				
				<div class="label"><label><strong>Fade In One by One?</strong>:</label></div>
				<div class="content">
					<input id="shortcode-option-carousel" class="fade_in_animation" type="checkbox" name="fade_in_animation">
				</div>
				
				<div class="clear"></div>
				
				<div class="label"><label><strong>Turn Into Carousel?</strong>:</label></div>
				<div class="content">
					<input id="shortcode-option-carousel" class="carousel" type="checkbox" name="carousel">
				</div>
				
				<div class="clear"></div>
				
				<div class="shortcode-dynamic-item">
					<div class="label"><label><strong>Client Image: </strong></label></div>
					<div class="content">
					
					 <input type="hidden" id="options-item"  />
			         <img class="redux-opts-screenshot" id="redux-opts-screenshot-" src="" />
			         <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);"class="redux-opts-upload button-secondary" rel-id="">' . esc_html__('Upload', 'salient') . '</a>
			         <a href="javascript:void(0);" class="redux-opts-upload-remove" style="display: none;">' . esc_html__('Remove Upload', 'salient') . '</a>
					
					</div>
					<div class="clear"></div>
					<div class="label"><label><strong>Client URL</strong> (optional):</label></div>
					<div class="content"><input class="shortcode-dynamic-item-input" type="text" name="" value="" /></div>
					
				</div>
			</div>
			<a href="#" class="btn blue remove-list-item">'.esc_html__('Remove Client', 'salient' ). '</a> <a href="#" class="btn blue add-list-item">'.esc_html__('Add Client', 'salient' ).'</a>';
			
		} 
		
		elseif( $type == 'checkbox' ){
			$option_element .= '<div class="label"><label for="' . $name . '"><strong>' . $attr_option['title'] . ': </strong></label></div>    <div class="content"> <input type="checkbox" class="' . $name . '" id="' . $name . '" />' . $desc . '</div> ';
		} 
	
		
		break;
		
	case 'textarea':
		$option_element .= '
		<div class="label"><label for="shortcode-option-'.$name.'"><strong>'.$attr_option['title'].': </strong></label></div>
		<div class="content"><textarea data-attrname="'.$name.'"></textarea> ' . $desc . '</div>';
		break;
			
	case 'text':
	default:
	    $option_element .= '
		<div class="label"><label for="shortcode-option-'.$name.'"><strong>'.$attr_option['title'].': </strong></label></div>
		<div class="content"><input class="attr" type="text" data-attrname="'.$name.'" value="" />' . $desc . '</div>';
	    break;
    }
	
	$option_element .= '<div class="clear"></div>';
    
	if(isset($attr_option['half_width']) && $attr_option['half_width'] == 'true' || isset($attr_option['second_half_width']) && $attr_option['second_half_width'] == 'true') $option_element .= '</div>';
	if(isset($attr_option['second_half_width']) && $attr_option['second_half_width'] == 'true') $option_element .= '<div class="clear no-line"></div> </div>';
	
    return $option_element;
}



?>