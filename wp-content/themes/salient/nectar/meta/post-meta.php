<?php 

	
	add_action('add_meta_boxes_post', 'nectar_metabox_posts');
	function nectar_metabox_posts(){
		
		
		$options = get_nectar_theme_options(); 
		if(!empty($options['transparent-header']) && $options['transparent-header'] == '1') {
			$disable_transparent_header = array( 
						'name' =>  esc_html__('Disable Transparency From Navigation', 'salient'),
						'desc' => esc_html__('You can use this option to force your navigation header to stay a solid color even if it qualifies to trigger the','salient') . '<a target="_blank" href="'. esc_url(admin_url('?page=Salient#16_section_group_li_a')) .'"> transparent effect</a> ' . esc_html__('you have activated in the Salient options panel.', 'salient'),
						'id' => '_disable_transparent_header',
						'type' => 'checkbox',
		                'std' => ''
					);
			$force_transparent_header_color = array( 
	      'name' => esc_html__('Transparent Header Navigation Color', 'salient'),
	      'desc' => esc_html__('Choose your header navigation logo & color scheme that will be used at the top of the page when the transparent effect is active. This option pulls from the settings "Header Starting Dark Logo" & "Header Dark Text Color" in the','salient') . ' <a target="_blank" href="'. esc_url(admin_url('?page=Salient#16_section_group_li_a')) .'">transparency tab</a>.',
	      'id' => '_force_transparent_header_color',
	      'type' => 'select',
	      'std' => 'light',
	      'options' => array(
	        "light" => "Light (default)",
	        "dark" => "Dark",
	      )
	    );
			
		} else {
			$disable_transparent_header = null;
			$force_transparent_header_color = null;
		}
		
		function nectar_metabox_post_meta_callback($post,$meta_box) {
			nectar_create_meta_box( $post, $meta_box["args"] );
		}
		
		if ( floatval(get_bloginfo('version')) < "3.6" ) { 

			#-----------------------------------------------------------------#
			# Gallery
			#-----------------------------------------------------------------# 
			$meta_box = array(
				'id' => 'nectar-metabox-post-gallery',
				'title' =>  esc_html__('Gallery Settings', 'salient'),
				'description' => esc_html__('Please use the sections that have appeared under the Featured Image block labeled "Second Slide, Third Slide..." etc to add images to your gallery.', 'salient'),
				'post_type' => 'post',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(
		    		
				)
			);
			//$callback = create_function( '$post,$meta_box', 'nectar_create_meta_box( $post, $meta_box["args"] );' );
		
			
			add_meta_box( $meta_box['id'], $meta_box['title'], 'nectar_metabox_post_meta_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
		} else {
			
		
			$meta_box = array(
				'id' => 'nectar-metabox-post-gallery',
				'title' =>  esc_html__('Gallery Configuration', 'salient'),
				'description' => 'Once you\'ve inserted a WordPress gallery using the "Add Media" button above, you can use the gallery slider checkbox below to transform your images into a slider.',
				'post_type' => 'post',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(
					array(
							'name' =>  esc_html__('Gallery Slider', 'salient'),
							'desc' => esc_html__('Would you like to turn your gallery into a slider?', 'salient'),
							'id' => '_nectar_gallery_slider',
							'type' => 'checkbox',
		                    'std' => 1
						)
				)
			);
			//$callback = create_function( '$post,$meta_box', 'nectar_create_meta_box( $post, $meta_box["args"] );' );
		    add_meta_box( $meta_box['id'], $meta_box['title'], 'nectar_metabox_post_meta_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );

		}
		
		
		#-----------------------------------------------------------------#
		# Quote
		#-----------------------------------------------------------------# 
	    $meta_box = array(
			'id' => 'nectar-metabox-post-quote',
			'title' =>  esc_html__('Quote Settings', 'salient'),
			'description' => '',
			'post_type' => 'post',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				array(
						'name' =>  esc_html__('Quote Author', 'salient'),
						'desc' => esc_html__('Please input the name of who your quote is from. Is left blank the post title will be used.', 'salient'),
						'id' => '_nectar_quote_author',
						'type' => 'text',
						'std' => ''
					),
				array(
						'name' =>  esc_html__('Quote Content', 'salient'),
						'desc' => esc_html__('Please type the text for your quote here.', 'salient'),
						'id' => '_nectar_quote',
						'type' => 'textarea',
	                    'std' => ''
					)
			)
		);
	    add_meta_box( $meta_box['id'], $meta_box['title'], 'nectar_metabox_post_meta_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
		
		#-----------------------------------------------------------------#
		# Link
		#-----------------------------------------------------------------# 
		$meta_box = array(
			'id' => 'nectar-metabox-post-link',
			'title' =>  esc_html__('Link Settings', 'salient'),
			'description' => '',
			'post_type' => 'post',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				array(
						'name' =>  esc_html__('Link URL', 'salient'),
						'desc' => esc_html__('Please input the URL for your link. I.e. http://www.themenectar.com', 'salient'),
						'id' => '_nectar_link',
						'type' => 'text',
						'std' => ''
					)
			)
		);
	    add_meta_box( $meta_box['id'], $meta_box['title'], 'nectar_metabox_post_meta_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
	    
		#-----------------------------------------------------------------#
		# Video
		#-----------------------------------------------------------------# 
	    $meta_box = array(
			'id' => 'nectar-metabox-post-video',
			'title' => esc_html__('Video Settings', 'salient'),
			'description' => '',
			'post_type' => 'post',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				array( 
					'name' => esc_html__('MP4 File URL', 'salient'),
					'desc' => esc_html__('Please upload the .m4v video file.', 'salient'),
					'id' => '_nectar_video_m4v',
					'type' => 'media', 
					'std' => ''
				),
				array( 
						'name' => esc_html__('OGV File URL', 'salient'),
						'desc' => esc_html__('Please upload the .ogv video file', 'salient'),
						'id' => '_nectar_video_ogv',
						'type' => 'media',
						'std' => ''
					),
				array( 
						'name' => esc_html__('Preview Image', 'salient'),
						'desc' => esc_html__('Image should be at least 680px wide. Click the "Upload" button to begin uploading your image, followed by "Select File" once you have made your selection. Only applies to self hosted videos.', 'salient'),
						'id' => '_nectar_video_poster',
						'type' => 'file',
						'std' => ''
					),
				array(
						'name' => esc_html__('Embedded Code', 'salient'),
						'desc' => esc_html__('If the video is an embed rather than self hosted, enter in a Vimeo or Youtube embed code here.', 'salient'),
						'id' => '_nectar_video_embed',
						'type' => 'textarea',
						'std' => ''
					)
			)
		);
		add_meta_box( $meta_box['id'], $meta_box['title'], 'nectar_metabox_post_meta_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
		
		#-----------------------------------------------------------------#
		# Audio
		#-----------------------------------------------------------------# 
		$meta_box = array(
			'id' => 'nectar-metabox-post-audio',
			'title' =>  esc_html__('Audio Settings', 'salient'),
			'description' => '',
			'post_type' => 'post',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				array( 
					'name' => esc_html__('MP3 File URL', 'salient'),
					'desc' => esc_html__('Please enter in the URL to the .mp3 file', 'salient'),
					'id' => '_nectar_audio_mp3',
					'type' => 'text',
					'std' => ''
				),
				array( 
						'name' => esc_html__('OGA File URL', 'salient'),
						'desc' => esc_html__('Please enter in the URL to the .ogg or .oga file', 'salient'),
						'id' => '_nectar_audio_ogg',
						'type' => 'text',
						'std' => ''
					)
			)
		);
		add_meta_box( $meta_box['id'], $meta_box['title'], 'nectar_metabox_post_meta_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
		
		

		#-----------------------------------------------------------------#
		# Post Configuration
		#-----------------------------------------------------------------# 
		if(!empty($options['blog_masonry_type']) && $options['blog_masonry_type'] == 'meta_overlaid' ||
			!empty($options['blog_masonry_type']) && $options['blog_masonry_type'] == 'classic_enhanced') {
			$meta_box = array(
				'id' => 'nectar-metabox-post-config',
				'title' =>  esc_html__('Post Configuration', 'salient'),
				'description' => esc_html__('Configure the various options for how your post will display', 'salient'),
				'post_type' => 'post',
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(
					array( 
						'name' => esc_html__('Masonry Item Sizing', 'salient'),
						'desc' => esc_html__('This will only be used if you choose to display your portfolio in the masonry format', 'salient'),
						'id' => '_post_item_masonry_sizing',
						'type' => 'select',
						'std' => 'tall_regular',
						'options' => array(
							"regular" => "Regular",
					  		"wide_tall" => "Regular Alt",
					  		"large_featured" => "Large Featured",
						)
					)
				)
			);
			add_meta_box( $meta_box['id'], $meta_box['title'], 'nectar_metabox_post_meta_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
		}
		

		
		#-----------------------------------------------------------------#
		# Header Settings
		#-----------------------------------------------------------------#
		if(!empty($options['blog_header_type']) && $options['blog_header_type'] == 'fullscreen') {
			$header_height = null;

			$bg_overlay = array(
				'name' =>  esc_html__('Background Overlay', 'salient'),
				'desc' => esc_html__('This will add a slight overlay onto your header which will allow lighter text to be easily visible on light images ', 'salient'),
				'id' => '_nectar_header_overlay',
				'type' => 'checkbox',
                'std' => 1
			);
			$bg_bottom_shad = array(
				'name' =>  esc_html__('Bottom Shadow', 'salient'),
				'desc' => esc_html__('This will add a subtle shadow at the bottom of your header', 'salient'),
				'id' => '_nectar_header_bottom_shadow',
				'type' => 'checkbox',
                'std' => 1
			);

		} else {
			$header_height = array( 
					'name' => esc_html__('Page Header Height', 'salient'),
					'desc' => esc_html__('How tall do you want your header? Don\'t include "px" in the string. e.g. 350 This only applies when you are using an image/bg color.', 'salient'),
					'id' => '_nectar_header_bg_height',
					'type' => 'text',
					'std' => ''
				);
			$bg_overlay = null;
			$bg_bottom_shad = null;
		}

	    $meta_box = array(
			'id' => 'nectar-metabox-page-header',
			'title' => esc_html__('Post Header Settings', 'salient'),
			'description' => esc_html__('Here you can configure how your page header will appear. ', 'salient'),
			'post_type' => 'post',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				array( 
						'name' => esc_html__('Page Header Image', 'salient'),
						'desc' => esc_html__('The image should be between 1600px - 2000px wide and have a minimum height of 475px for best results.', 'salient'),
						'id' => '_nectar_header_bg',
						'type' => 'file',
						'std' => ''
					),
				array(
						'name' =>  esc_html__('Parallax Header?', 'salient'),
						'desc' => esc_html__('If you would like your header to have a parallax scroll effect check this box.', 'salient'),
						'id' => '_nectar_header_parallax',
						'type' => 'checkbox',
		                'std' => 1
					),	
				$header_height,
				array( 
						'name' => esc_html__('Background Alignment', 'salient'),
						'desc' => esc_html__('Please choose how you would like your header background to be aligned', 'salient'),
						'id' => '_nectar_page_header_bg_alignment',
						'type' => 'select',
						'std' => 'top',
						'options' => array(
							"top" => "Top",
					  	"center" => "Center",
					  	"bottom" => "Bottom"
						)
					),
				array( 
						'name' => esc_html__('Page Header Background Color', 'salient'),
						'desc' => esc_html__('Set your desired page header background color if not using an image', 'salient'),
						'id' => '_nectar_header_bg_color',
						'type' => 'color',
						'std' => ''
					),
				array( 
						'name' => esc_html__('Page Header Font Color', 'salient'),
						'desc' => esc_html__('Set your desired page header font color - will only be used if using a header bg image/color', 'salient'),
						'id' => '_nectar_header_font_color',
						'type' => 'color',
						'std' => ''
					),
				$bg_overlay,
				$bg_bottom_shad,
				$disable_transparent_header,
				$force_transparent_header_color	
			)
		);
		add_meta_box( $meta_box['id'], $meta_box['title'], 'nectar_metabox_post_meta_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
			
		
		
	}

	
	
	


?>