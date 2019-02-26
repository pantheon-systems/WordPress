<?php 
// Metabox for review
function bsf_metaboxes( array $meta_boxes ) {
	// Start with an underscore to hide fields from custom fields list
	$prefix 		= '_bsf_';
	$post_types 	= get_post_types('','names');

	if ( !get_option( 'bsf_woocom_init_setting' ) ) {
		$woo_settings 	= true;
	}else {
		$woo_settings 	= get_option( 'bsf_woocom_setting' );
	}
	
	if( empty( $woo_settings ) ) { 

		$woocommerce_post_type = array( "product", "product_variation", "shop_order" , "shop_order_refund", "shop_coupon", "shop_webhook" );
		$required_post_type = array_diff( $post_types, $woocommerce_post_type );
		
	} 
	else {

		$exclude_custom_post_type = apply_filters( 'bsf_exclude_custom_post_type', array() );
		$required_post_type = array_diff( $post_types, $exclude_custom_post_type );

	}

	$meta_boxes[] = array(
		'id'         => 'review_metabox',
		'title'      => __('Configure Rich Snippet','rich-snippets'),
		'pages'      => $required_post_type,//$post_types, //array( 'post','page' ), // Custom Post types
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name'    => __('','rich-snippets'),
				'desc'    => __('','rich-snippets'),
				'id'      => $prefix . 'post_type',
				'class'   => 'snippet-type',
				'type'    => 'select',
				'options' => array(
					array( 'name' => __('Select what this post is about','rich-snippets'), 'value' => '0', ),
					array( 'name' => __('Item Review','rich-snippets'), 'value' => '1', ),
					array( 'name' => __('Event','rich-snippets'), 'value' => '2', ),
					array( 'name' => __('Person','rich-snippets'), 'value' => '5', ),
					array( 'name' => __('Product','rich-snippets'), 'value' => '6', ),
					array( 'name' => __('Recipe','rich-snippets'), 'value' => '7', ),
					array( 'name' => __('Software Application','rich-snippets'), 'value' => '8', ),
					array( 'name' => __('Video','rich-snippets'), 'value' => '9', ),
					array( 'name' => __('Article','rich-snippets'), 'value' => '10', ),
					array( 'name' => __('Service','rich-snippets'), 'value' => '11', ),

				),
			),
			// Meta Settings for Item Review		
			array(
				'name' => __('Rich Snippets - Item Review','rich-snippets'),
				'desc' => __('Please provide the following information.','rich-snippets'),
				'id'   => $prefix . 'review_title',
				'class' => 'review',
				'type' => 'title',
			),
			array(
				'name' => __('Reviewer&rsquo;s Name ','rich-snippets'),
				'desc' => __('Enter the name of Item Reviewer or The Post Author.','rich-snippets'),
				'id'   => $prefix . 'item_reviewer',
				'class' => 'review',
				'type' => 'text_medium',
			),			
			array(
				'name' => __('Item to be reviewed','rich-snippets'),
				'desc' => __('Enter the name of the item, you are writing review about.','rich-snippets'),
				'id'   => $prefix . 'item_name',
				'class' => 'review',
				'type' => 'text',
			),
			array(
				'name'    => __('Your Rating','rich-snippets'),
				'desc'    => __('&nbsp;&nbsp;Rate this item (1-5)','rich-snippets'),
				'id'      => $prefix . 'rating',
				'class'   => 'star review',
				'type'    => 'radio',
				'options' => array(
					array( 'name' => __('','rich-snippets'), 'value' => '1', ),
					array( 'name' => __('','rich-snippets'), 'value' => '2', ),
					array( 'name' => __('','rich-snippets'), 'value' => '3', ),
					array( 'name' => __('','rich-snippets'), 'value' => '4', ),
					array( 'name' => __('','rich-snippets'), 'value' => '5', ),
				),
			),
			// Meta Settings for Events		
			array(
				'name' => __('Rich Snippets - Events','rich-snippets'),
				'desc' => __('Please provide the following information.','rich-snippets'),
				'id'   => $prefix . 'events',
				'class' => 'events',
				'type' => 'title',
			),
			array(
				'name' => __('Event Title ','rich-snippets'),
				'desc' => __('What would be the name of the event?','rich-snippets'),
				'id'   => $prefix . 'event_title',
				'class' => 'events',
				'type' => 'text',
			),
			array(
				'name' => __('Location ','rich-snippets'),
				'desc' => __('Location Name Here','rich-snippets'),
				'id'   => $prefix . 'event_organization',
				'class' => 'events',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Street Address','rich-snippets'),
				'id'   => $prefix . 'event_street',
				'class' => 'events',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Locality','rich-snippets'),
				'id'   => $prefix . 'event_local',
				'class' => 'events',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Region','rich-snippets'),
				'id'   => $prefix . 'event_region',
				'class' => 'events',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Postal Code','rich-snippets'),
				'id'   => $prefix . 'event_postal_code',
				'class' => 'events',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Description ','rich-snippets'),
				'desc' => __('Describe the event in short.','rich-snippets'),
				'id'   => $prefix . 'event_desc',
				'class' => 'events',
				'type' => 'textarea_small',
			),
			array(
				'name' => __('Start Date ','rich-snippets'),
				'desc' => __('Provide the Event Start Date.','rich-snippets'),
				'id'   => $prefix . 'event_start_date',
				'class' => 'events',
				'type' => 'text_date',
			),
			array(
				'name' => __('End Date ','rich-snippets'),
				'desc' => __('Provide the Event End Date.','rich-snippets'),
				'id'   => $prefix . 'event_end_date',
				'class' => 'events',
				'type' => 'text_date',
			),
			array(
				'name' => __('Offer Price','rich-snippets'),
				'desc' => __('Enter the ticket Price.','rich-snippets'),
				'id'   => $prefix . 'event_price',
				'class' => 'events',
				'type' => 'text_small',
			),
			array(
				'name' => __('Currency ','rich-snippets'),
				'desc' => __('Enter the Currency Code(e.g USD, INR, AUD, EUR, GBP). <a href="http://www.science.co.il/International/Currency-Codes.asp" target="_blank"> Know you currency code</a>','rich-snippets'),
				'id'   => $prefix . 'event_cur',
				'class' => 'events',
				'type' => 'text_small',
			),
			array(
				'name' => __('Url ','rich-snippets'),
				'desc' => __('Url of buy ticket page','rich-snippets'),
				'id'   => $prefix . 'event_ticket_url',
				'class' => 'events',
				'type' => 'text',
			),
			// Meta Settings for Music		
			array(
				'name' => __('Rich Snippets - Music','rich-snippets'),
				'desc' => __('Please provide the following information.','rich-snippets'),
				'id'   => $prefix . 'music',
				'class' => 'music',
				'type' => 'title',
			),
			// Meta Settings for Organization
			array(
				'name' => __('Rich Snippets - Organization','rich-snippets'),
				'desc' => __('Please provide the following information.','rich-snippets'),
				'id'   => $prefix . 'organization',
				'class' => 'organization',
				'type' => 'title',
			),
			array(
				'name' => __('Organization Name ','rich-snippets'),
				'desc' => __('Enter the Company or Organization Name.','rich-snippets'),
				'id'   => $prefix . 'organization_name',
				'class' => 'organization',
				'type' => 'text',
			),
			array(
				'name' => __('Website URL ','rich-snippets'),
				'desc' => __('Enter the Organization homepage url.','rich-snippets'),
				'id'   => $prefix . 'organization_url',
				'class' => 'organization',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Contact No. ','rich-snippets'),
				'desc' => __('Enter the Telephone No.','rich-snippets'),
				'id'   => $prefix . 'organization_tel',
				'class' => 'organization',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Address ','rich-snippets'),
				'desc' => __('Street Name.','rich-snippets'),
				'id'   => $prefix . 'organization_street',
				'class' => 'organization',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Locality.','rich-snippets'),
				'id'   => $prefix . 'organization_local',
				'class' => 'organization',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Region.','rich-snippets'),
				'id'   => $prefix . 'organization_region',
				'class' => 'organization',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Postal Code','rich-snippets'),
				'id'   => $prefix . 'organization_zip',
				'class' => 'organization',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Country Name','rich-snippets'),
				'id'   => $prefix . 'organization_country',
				'class' => 'organization',
				'type' => 'text_medium',
			),
			array(
				'name' => __('GEO Location','rich-snippets'),
				'desc' => __('Latitude. <a href="http://universimmedia.pagesperso-orange.fr/geo/loc.htm" target="_blank">Find Here.</a>','rich-snippets'),
				'id'   => $prefix . 'organization_latitide',
				'class' => 'organization',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Longitude.','rich-snippets'),
				'id'   => $prefix . 'organization_longitude',
				'class' => 'organization',
				'type' => 'text_medium',
			),
			// Meta Settings for People
			array(
				'name' => __('Rich Snippets - Person','rich-snippets'),
				'desc' => __('Please provide the following information.','rich-snippets'),
				'id'   => $prefix . 'people',
				'class' => 'people',
				'type' => 'title',
			),
			array(
				'name' => __('Person&lsquo;s Name','rich-snippets'),
				'desc' => __('Enter the relative person&lsquo;s name.','rich-snippets'),
				'id'   => $prefix . 'people_fn',
				'class' => 'people',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Nickname','rich-snippets'),
				'desc' => __('Enter the nickname (if any).','rich-snippets'),
				'id'   => $prefix . 'people_nickname',
				'class' => 'people',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Photograph','rich-snippets'),
				'desc' => __('Upload the photo or select from media library. Medium size is recommended (300px X 300px)','rich-snippets'),
				'id'   => $prefix . 'people_photo',
				'class' => 'people',
				'type' => 'file',
			),
			array(
				'name' => __('Job Title ','rich-snippets'),
				'desc' => __('Enter job title.','rich-snippets'),
				'id'   => $prefix . 'people_job_title',
				'class' => 'people',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Homepage URL','rich-snippets'),
				'desc' => __('Enter homepage URL(if any).','rich-snippets'),
				'id'   => $prefix . 'people_website',
				'class' => 'people',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Company / Organization','rich-snippets'),
				'desc' => __('Enter Company or Organization name in affiliation.','rich-snippets'),
				'id'   => $prefix . 'people_company',
				'class' => 'people',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Address','rich-snippets'),
				'desc' => __('Enter Street','rich-snippets'),
				'id'   => $prefix . 'people_street',
				'class' => 'people',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Enter Locality','rich-snippets'),
				'id'   => $prefix . 'people_local',
				'class' => 'people',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Region','rich-snippets'),
				'id'   => $prefix . 'people_region',
				'class' => 'people',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Postal Code','rich-snippets'),
				'id'   => $prefix . 'people_postal',
				'class' => 'people',
				'type' => 'text_medium',
			),

			// Meta Settings for Products
			array(
				'name' => __('Rich Snippets - Products','rich-snippets'),
				'desc' => __('Please provide the following information.','rich-snippets'),
				'id'   => $prefix . 'product',
				'class' => 'product',
				'type' => 'title',
			),
			array(
				'name' => __('Your Rating','rich-snippets'),
				'desc' => __('Rate this product or aggregate rating.','rich-snippets'),
				'id'   => $prefix . 'product_rating',
				'class' => 'star product',
				'type' => 'radio',
				'options' => array(
					array( 'name' => __('','rich-snippets'), 'value' => '1', ),
					array( 'name' => __('','rich-snippets'), 'value' => '2', ),
					array( 'name' => __('','rich-snippets'), 'value' => '3', ),
					array( 'name' => __('','rich-snippets'), 'value' => '4', ),
					array( 'name' => __('','rich-snippets'), 'value' => '5', ),
				),
			),
			array(
				'name' => __('Brand Name','rich-snippets'),
				'desc' => __('Enter the products brand name','rich-snippets'),
				'id'   => $prefix . 'product_brand',
				'class' => 'product',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Product Name','rich-snippets'),
				'desc' => __('Enter the product name.','rich-snippets'),
				'id'   => $prefix . 'product_name',
				'class' => 'product',
				'type' => 'text_medium',
			),			
			array(
				'name' => __('Product Image','rich-snippets'),
				'desc' => __('Upload the product image or select from library. Medium size is recommended (300px X 300px)','rich-snippets'),
				'id'   => $prefix . 'product_image',
				'class' => 'product',
				'type' => 'file',
			),
			array(
				'name' => __('Product Price','rich-snippets'),
				'desc' => __('Enter the product Price.','rich-snippets'),
				'id'   => $prefix . 'product_price',
				'class' => 'product',
				'type' => 'text_small',
			),
			array(
				'name' => __('Currency ','rich-snippets'),
				'desc' => __('Enter the Currency Code(e.g USD, INR, AUD, EUR, GBP). <a href="http://www.science.co.il/International/Currency-Codes.asp" target="_blank"> Know you currency code</a>','rich-snippets'),
				'id'   => $prefix . 'product_cur',
				'class' => 'product',
				'type' => 'text_small',
			),
			array(
				'name' => __('Availability','rich-snippets'),
				'desc' => __('Select the products availability.','rich-snippets'),
				'id'   => $prefix . 'product_status',
				'class' => 'product',
				'type' => 'select',
				'options' => array(
					array('name' => __('__ Please Select __','rich-snippets'), 'value' => ''),
					array('name' => __('Out of Stock','rich-snippets'), 'value' => 'out_of_stock'),
					array('name' => __('In Stock','rich-snippets'), 'value' => 'in_stock'),
					array('name' => __('In Store Only','rich-snippets'), 'value' => 'instore_only'),
					array('name' => __('Pre-Order','rich-snippets'), 'value' => 'preorder'),
				),
			),			
			// Meta Settings for Recipes		
			array(
				'name' => __('Rich Snippets - Recipes','rich-snippets'),
				'desc' => __('Please provide the following information.','rich-snippets'),
				'id'   => $prefix . 'recipes',
				'class' => 'recipes',
				'type' => 'title',
			),
			array(
				'name' => __('Recipe Name ','rich-snippets'),
				'desc' => __('Enter the recipe name.','rich-snippets'),
				'id'   => $prefix . 'recipes_name',
				'class' => 'recipes',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Author Name ','rich-snippets'),
				'desc' => __('Enter the Author name.','rich-snippets'),
				'id'   => $prefix . 'authors_name',
				'class' => 'recipes',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Time Required ','rich-snippets'),
				'desc' => __('Preperation time  (Format: 1H30M. H - Hours, M - Minutes )','rich-snippets'),
				'id'   => $prefix . 'recipes_preptime',
				'class' => 'recipes',
				'type' => 'text_small',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Cook Time. (Format: 1H30M. H - Hours, M - Minutes )','rich-snippets'),
				'id'   => $prefix . 'recipes_cooktime',
				'class' => 'recipes',
				'type' => 'text_small',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Total Time  (Format: 1H30M. H - Hours, M - Minutes )','rich-snippets'),
				'id'   => $prefix . 'recipes_totaltime',
				'class' => 'recipes',
				'type' => 'text_small',
			),
			array(
				'name' => __('Description','rich-snippets'),
				'desc' => __('Describe the recipe in short.','rich-snippets'),
				'id'   => $prefix . 'recipes_desc',
				'class' => 'recipes',
				'type' => 'textarea_small',
			),
			array(
				'name' => __('Nutrition','rich-snippets'),
				'desc' => __('Nutrition','rich-snippets'),
				'id'   => $prefix . 'recipes_nutrition',
				'class' => 'recipes',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Ingredients','rich-snippets'),
				'desc' => __('Enter the ingredients used','rich-snippets'),
				'id'   => $prefix . 'recipes_ingredient',
				'class' => 'recipes',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Recipe Photo','rich-snippets'),
				'desc' => __('Upload or Select recipe photo. Medium size is recommended (300px X 300px)','rich-snippets'),
				'id'   => $prefix . 'recipes_photo',
				'class' => 'recipes',
				'type' => 'file',
			),
			// Meta Settings for Software Application
			array(
				'name' => __('Rich Snippets - Software Application','rich-snippets'),
				'desc' => __('Please provide the following information.','rich-snippets'),
				'id'   => $prefix . 'software',
				'class' => 'software',
				'type' => 'title',
			),
			array(
				'name' => __('Software Rating','rich-snippets'),
				'desc' => __('Rate this software.','rich-snippets'),
				'id'   => $prefix . 'software_rating',
				'class' => 'star software',
				'type' => 'radio',
				'options' => array(
					array( 'name' => __('','rich-snippets'), 'value' => '1', ),
					array( 'name' => __('','rich-snippets'), 'value' => '2', ),
					array( 'name' => __('','rich-snippets'), 'value' => '3', ),
					array( 'name' => __('','rich-snippets'), 'value' => '4', ),
					array( 'name' => __('','rich-snippets'), 'value' => '5', ),
				),
			),
			array(
				'name' => __('Price','rich-snippets'),
				'desc' => __('Enter the Price of Software','rich-snippets'),
				'id'   => $prefix . 'software_price',
				'class' => 'software',
				'type' => 'text_small',
			),
			array(
				'name' => __('Currency','rich-snippets'),
				'desc' => __('Enter the Currency Code(e.g USD, INR, AUD, EUR, GBP). <a href="http://www.science.co.il/International/Currency-Codes.asp" target="_blank"> Know you currency code</a>','rich-snippets'),
				'id'   => $prefix . 'software_cur',
				'class'=> 'software',
				'type' => 'text_small',
			),
			array(
				'name' => __('Software Name ','rich-snippets'),
				'desc' => __('Enter the Software Name.','rich-snippets'),
				'id'   => $prefix . 'software_name',
				'class' => 'software',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Operating System','rich-snippets'),
				'desc' => __('Enter the software Operating System.','rich-snippets'),
				'id'   => $prefix . 'software_os',
				'class' => 'software',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Application Category','rich-snippets'),
				'desc' => __('Like Game, Multimedia','rich-snippets'),
				'id'   => $prefix . 'software_cat',
				'class' => 'software',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Software Image','rich-snippets'),
				'desc' => __('Upload or select image of software. Medium size is recommended (300px X 300px)','rich-snippets'),
				'id'   => $prefix . 'software_image',
				'class' => 'software',
				'type' => 'file',
			),
			array(
				'name' => __('Landing Page','rich-snippets'),
				'desc' => __('Enter the landing page url for software','rich-snippets'),
				'id'   => $prefix . 'software_landing',
				'class' => 'software',
				'type' => 'text',
			),  
			// Meta Settings for Video
			array(
				'name' => __('Rich Snippets - Videos','rich-snippets'),
				'desc' => __('Please provide the following information.','rich-snippets'),
				'id'   => $prefix . 'video',
				'class' => 'video',
				'type' => 'title',
			),
			array(
				'name' => __('Video Title','rich-snippets'),
				'desc' => __('Enter the title for this video','rich-snippets'),
				'id'   => $prefix . 'video_title',
				'class' => 'video',
				'type' => 'text',
			),
			array(
				'name' => __('Video Description','rich-snippets'),
				'desc' => __('Enter the brief description for this video','rich-snippets'),
				'id'   => $prefix . 'video_desc',
				'class' => 'video',
				'type' => 'textarea_small',
			),
			array(
				'name' => __('Video Thumbnail','rich-snippets'),
				'desc' => __('Upload or select video thumbnail from gallery. Medium size is recommended (300px X 300px)','rich-snippets'),
				'id'   => $prefix . 'video_thumb',
				'class' => 'video',
				'type' => 'file',
			),
			array(
				'name' => __('Video','rich-snippets'),
				'desc' => __('Upload Video or enter the video file url<br>Example: <br> http://www.example.com/video123.flv<br>A URL pointing to the actual video media file. This file should be in .mpg, .mpeg, .mp4, .m4v, .mov, .wmv, .avi, .flv, or other video file format','rich-snippets'),
				'id'   => $prefix . 'video_url',
				'class' => 'video',
				'type' => 'file',
			),

			array(
				'name' => __('Embed Video','rich-snippets'),
				'desc' => __('A URL pointing to a player for the specific video. Usually this is the information in the src element of an &lt;embed&gt;tag. <br>Example: <br>Youtube: https://www.youtube.com/embed/CibazcCevOk <br>Dailymotion: http://www.dailymotion.com/swf/x1o2g<br><div class="bsf_vd_note"><h3>Add only one url either in "Video" or "Embed Video" field. Dont use both.</h3></div>','rich-snippets'),
				'id'   => $prefix . 'video_emb_url',
				'class' => 'video',
				'type' => 'text',
			),
			
			array(
				'name' => __('Video Duration','rich-snippets'),
				'desc' => __('Enter the duration for this video','rich-snippets'),
				'id'   => $prefix . 'video_duration',
				'class' => 'video',
				'type' => 'text_small',
			),
			array(
				'name' => __('Upload Date','rich-snippets'),
				'desc' => __('Provide the date when the video is uploaded','rich-snippets'),
				'id'   => $prefix . 'video_date',
				'class' => 'video',
				'type' => 'text_date',
			),
			// Meta Settings for Article
			array(
				'name' => __('Rich Snippets - Article','rich-snippets'),
				'desc' => __('Please provide the following information.','rich-snippets'),
				'id'   => $prefix . 'article',
				'class' => 'article',
				'type' => 'title',
			),
			array(
				'name' => __('Article Image','rich-snippets'),
				'desc' => __('Upload or select image from gallery. Medium size is recommended (300px X 300px)','rich-snippets'),
				'id'   => $prefix . 'article_image',
				'class' => 'article',
				'type' => 'file',
			),
			array(
				'name' => __('Article Name','rich-snippets'),
				'desc' => __('Enter the name for this article','rich-snippets'),
				'id'   => $prefix . 'article_name',
				'class' => 'article',
				'type' => 'text',
			),
			array(
				'name' => __('Short Description','rich-snippets'),
				'desc' => __('Enter the brief description about this article (About 30 Words)','rich-snippets'),
				'id'   => $prefix . 'article_desc',
				'class' => 'article',
				'type' => 'textarea_small',
			),
			array(
				'name' => __('Author','rich-snippets'),
				'desc' => __('Enter the author name for this article','rich-snippets'),
				'id'   => $prefix . 'article_author',
				'class' => 'article',
				'type' => 'text',
			),
			array(
				'name' => __('Publisher - Orgnization','rich-snippets'),
				'desc' => __('Enter the publisher name for this article','rich-snippets'),
				'id'   => $prefix . 'article_publisher',
				'class' => 'article',
				'type' => 'text',
			),
			array(
				'name' => __('Publisher Logo','rich-snippets'),
				'desc' => __('Upload or select image from gallery. Medium size is recommended (300px X 300px)','rich-snippets'),
				'id'   => $prefix . 'article_publisher_logo',
				'class' => 'article',
				'type' => 'file',
			),
			

			// Meta Settings for Service
			array(
				'name' => __('Rich Snippets - Service','rich-snippets'),
				'desc' => __('Please provide the following information.','rich-snippets'),
				'id'   => $prefix . 'service',
				'class' => 'service',
				'type' => 'title',
			),
			array(
				'name' => __('Image','rich-snippets'),
				'desc' => __('Upload or select image from gallery. Medium size is recommended (300px X 300px)','rich-snippets'),
				'id'   => $prefix . 'service_image',
				'class' => 'service',
				'type' => 'file',
			),
			array(
				'name' => __('Service Type','rich-snippets'),
				'desc' => __('Enter the service type','rich-snippets'),
				'id'   => $prefix . 'service_type',
				'class' => 'service',
				'type' => 'text',
			),
			array(
				'name' => __('Service Served Area','rich-snippets'),
				'desc' => __('Enter the area where service is available','rich-snippets'),
				'id'   => $prefix . 'service_area',
				'class' => 'service',
				'type' => 'text',
			),
			array(
				'name' => __('Short Description','rich-snippets'),
				'desc' => __('Enter the description about service (About 30 Words)','rich-snippets'),
				'id'   => $prefix . 'service_desc',
				'class' => 'service',
				'type' => 'textarea_small',
			),
			array(
				'name' => __('Provider Name','rich-snippets'),
				'desc' => __('Enter the service provider name','rich-snippets'),
				'id'   => $prefix . 'service_provider',
				'class' => 'service',
				'type' => 'text',
			),
			array(
				'name' => __('Location','rich-snippets'),
				'desc' => __('Street Address','rich-snippets'),
				'id'   => $prefix . 'service_street',
				'class' => 'service',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Locality','rich-snippets'),
				'id'   => $prefix . 'service_local',
				'class' => 'service',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Region','rich-snippets'),
				'id'   => $prefix . 'service_region',
				'class' => 'service',
				'type' => 'text_medium',
			),
			array(
				'name' => __('','rich-snippets'),
				'desc' => __('Postal Code','rich-snippets'),
				'id'   => $prefix . 'service_postal_code',
				'class' => 'service',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Provider Location Image','rich-snippets'),
				'desc' => __('Upload the provider location image or select from library. Medium size is recommended (300px X 300px)','rich-snippets'),
				'id'   => $prefix . 'provider_location_image',
				'class' => 'service',
				'type' => 'file',
			),
			array(
				'name' => __('Telephone','rich-snippets'),
				'desc' => __('Telephone number','rich-snippets'),
				'id'   => $prefix . 'service_telephone',
				'class' => 'service',
				'type' => 'text_medium',
			),
			array(
				'name' => __('Enable/Disable Rating','rich-snippets'),
				'desc' => __('.','rich-snippets'),
				'id'   => $prefix . 'service_rating_switch',
				'class' => 'service',
				'type' => 'select',
				'options' => array(
					array('name' => __('Disable','rich-snippets'), 'value' => ''),
					array('name' => __('Enable','rich-snippets'), 'value' => 'enable'),
				),
			),
		),
	);
	// Add other metaboxes as needed
	return $meta_boxes;
}
?>