<?php
// Function to add review option for settings
function add_review_option()
{
	$review_opt = array(
		'review_title'	 =>	__('Summary','rich-snippets'),
		'item_reviewer'	=>	__('Reviewer','rich-snippets'),
		'review_date'	=>	__('Review Date','rich-snippets'),		
		'item_name'		=>	__('Reviewed Item','rich-snippets'),
		'item_rating'	  =>	__('Author Rating','rich-snippets')
	);
 	add_option('bsf_review',$review_opt);	
}

// Function to add event option for settings
function add_event_option()
{
	$event_opt = array(
		'snippet_title'	=>	__('Summary','rich-snippets'),
		'event_title'	  =>	__('Event','rich-snippets'),
		'event_location'   =>	__('Location','rich-snippets'),
		'event_desc'	   =>	__('Description','rich-snippets'),
		'start_time'	   =>	__('Starting on','rich-snippets'),
		'end_time'		 =>	__('Ending on','rich-snippets'),
		'events_price'		 =>	__('Offer Price','rich-snippets')
	);
	add_option('bsf_event',$event_opt);
}

// Function to add person option for settings
function add_person_option()
{
	$person_opt = array(
		'snippet_title'	=>	__('Summary','rich-snippets'),
		'person_name'	  =>	__('Name','rich-snippets'),
		'person_nickname'  =>	__('Nickname','rich-snippets'),
		'person_job_title' =>	__('Job Title','rich-snippets'),
		'person_website'   =>	__('Website','rich-snippets'),
		'person_company'   =>	__('Company','rich-snippets'),
		'person_address'   =>	__('Address','rich-snippets')
	);
	add_option('bsf_person',$person_opt);
}

// Function to add product option for settings
function add_product_option()
{
	$product_opt = array(
		'snippet_title'	=>	__('Summary','rich-snippets'),
		'product_rating'   =>	__('Author Rating','rich-snippets'),
		'product_brand'	=>	__('Brand Name','rich-snippets'),
		'product_name'	 =>	__('Product Name','rich-snippets'),
		'product_agr'	  =>	__('Aggregate Rating','rich-snippets'),
		'product_price'	=>	__('Price','rich-snippets'),
		'product_avail'	=>	__('Product Availability','rich-snippets')
	);
	add_option('bsf_product',$product_opt);
}

// Function to add recipe option for settings
function add_recipe_option()
{
	$recipe_opt = array(
		'snippet_title'	=>	__('Summary','rich-snippets'),
		'recipe_name'	  =>	__('Recipe Name','rich-snippets'),
		'author_name'	  =>	__('Author Name','rich-snippets'),
		'recipe_pub'	   =>	__('Published On','rich-snippets'),		
		'recipe_prep'	  =>	__('Preparation Time','rich-snippets'),
		'recipe_cook'	  =>	__('Cook Time','rich-snippets'),
		'recipe_time'	  =>	__('Total Time','rich-snippets'),
		'recipe_desc'	=>	__('Description','rich-snippets'),
		'recipe_nutrition'	=>	__('Nutrition','rich-snippets'),
		'recipe_ingredient'	=>	__('Ingredients','rich-snippets'),
		'recipe_rating'	=>	__('Average Rating','rich-snippets')
	);
	add_option('bsf_recipe',$recipe_opt);
}

// Function to add software option for settings
function add_software_option()
{	
	$software_opt = array(
		'snippet_title'	=>	__('Summary','rich-snippets'),
		'software_rating'  =>	__('Author Rating','rich-snippets'),
		'software_agr'	  =>	__('Aggregate Rating','rich-snippets'),
		'software_price'   =>	__('Price','rich-snippets'),
		'software_name'	=>	__('Software Name','rich-snippets'),
		'software_os'	  =>	__('Operating System','rich-snippets'),
		'software_website' =>	__('Landing Page','rich-snippets'),
	);
	add_option('bsf_software',$software_opt);
}

// Function to add video option for settings
function add_video_option()
{
	$video_opt = array(
		'snippet_title'	=>	__('Summary','rich-snippets'),
		'video_title'	  =>	__('Title','rich-snippets'),
		'video_desc'	   =>	__('Description','rich-snippets'),
		'video_time'	   =>	__('Duration','rich-snippets'),
		'video_date'	   =>	__('Upload Date','rich-snippets')
	);
	add_option('bsf_video',$video_opt);
}
// Function to add article option for settings
function add_article_option()
{
	$article_opt = array(
		'snippet_title'	=>	__('Summary','rich-snippets'),
		'article_name'	  =>	__('Article Name','rich-snippets'),
		'article_author'	  =>	__('Author','rich-snippets'),
		'article_desc'	  =>	__('Description','rich-snippets'),
		'article_image'	  =>	__('Image','rich-snippets'),
		'article_publisher'	  =>	__('Publisher Name','rich-snippets'),
		'article_publisher_logo'	  =>	__('Publisher Logo','rich-snippets')

	);
	add_option('bsf_article',$article_opt);
}
// Function to add article option for settings
function add_service_option()
{
	$service_opt = array(
		'snippet_title'	=>	__('Summary','rich-snippets'),
		'service_type'	  =>	__('Service Type','rich-snippets'),
		'service_area'	  =>	__('Area','rich-snippets'),
		'service_desc'	  =>	__('Description','rich-snippets'),
		'service_channel'	  =>	__('URL','rich-snippets'),
		'service_url_link'	  =>	__('Click Here For More Info','rich-snippets'),
		'service_rating'	  =>	__('User Rating','rich-snippets'),
		'service_provider_name'	  =>	__('Provider Name','rich-snippets'),
		'provider_location'   =>	__('Location','rich-snippets'),
		'service_telephone'	  =>	__('Provider telephone number','rich-snippets'),
		// 'service_price'	=>	__('Price Range','rich-snippets')
	);
	add_option('bsf_service',$service_opt);
}
// Function for customization
function add_color_option()
{
	$color_opt = array(
		'snippet_box_bg'	   =>	'#F5F5F5',
		'snippet_title_bg'	 =>	'#E4E4E4',
		'snippet_border'	   =>	'#ACACAC',
		'snippet_title_color'  =>	'#333333',
		'snippet_box_color'  	=>	'#333333',
	);
	add_option('bsf_custom',$color_opt);
}

// Function for customization
function add_woo_commerce_option()
{
	if ( !get_option( 'bsf_woocom_init_setting' ) ) {
		
		$woo_opt = false;
		
		if ( get_option( 'bsf_custom' ) ) {
			$woo_opt = true;
		}
		
		add_option('bsf_woocom_setting', $woo_opt);
		add_option('bsf_woocom_init_setting', 'done');
	}
}

?>