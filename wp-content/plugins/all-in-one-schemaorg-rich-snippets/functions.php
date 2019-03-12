<?php
/**
 * Template Name: Plugin Functions
 *
 */
//add_filter( 'bsf_meta_boxes', 'bsf_review_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
add_action( 'init', 'bsf_initialize_bsf_meta_boxes', 9999 );
// Register an action for submitting rating
add_action( 'wp_ajax_nopriv_bsf_submit_rating', 'bsf_add_rating' );
add_action( 'wp_ajax_bsf_submit_rating', 'bsf_add_rating' );
// Register an action for updating rating
add_action( 'wp_ajax_nopriv_bsf_update_rating', 'bsf_update_rating' );
add_action( 'wp_ajax_bsf_update_rating', 'bsf_update_rating' );
// Include the Ajax library on the front end
add_action( 'wp_head', 'add_ajax_library' );
/**
 * Initialize the metabox class.
 */
/* FUNCTION to check for posts having snippets */
add_action( 'wp','aiosrs_check_snippet_existence' );
function aiosrs_check_snippet_existence(){	
	global $post;

	if( !isset( $post->ID ) ) {
		return;
	}
	
	$type = get_post_meta($post->ID, '_bsf_post_type', true);
	if($type){		
		add_action('wp_enqueue_scripts', 'aiosrs_enque');
	}

}
function aiosrs_enque( ) {
	wp_enqueue_style('rating_style', plugin_dir_url(__FILE__) . 'css/jquery.rating.css');
	wp_enqueue_script('jquery_rating', plugin_dir_url(__FILE__) . 'js/jquery.rating.min.js', array('jquery'));
	wp_register_style( 'bsf_style', plugins_url('/css/style.css', __FILE__) );
	wp_enqueue_style('bsf_style');
}

function bsf_initialize_bsf_meta_boxes() {
	if ( ! class_exists( 'bsf_Meta_Box' ) )
		require_once plugin_dir_path( __FILE__ ).'init.php';
}
//Function to display the rich snippet output below the content
function display_rich_snippet($content) {
	global $post;
	
	$args_color = get_option('bsf_custom');
	$id = $post->ID;
	$type = get_post_meta($id, '_bsf_post_type', true);

	if($type == '1')
	{
		global $post;
	
		$args_review = get_option('bsf_review');

		$review = '';
		$review .= '<div id="snippet-box" class="snippet-type-'.esc_attr($type).'" style="background:'.esc_attr($args_color["snippet_box_bg"] ).'; color:'.esc_attr($args_color["snippet_box_color"] ).'; border:1px solid '.esc_attr($args_color["snippet_border"] ).';">';
				
		if($args_review['review_title'] != "")
			$review .= '<div class="snippet-title" style="background:'.esc_attr($args_color["snippet_title_bg"] ).'; color:'.esc_attr($args_color["snippet_title_color"] ).'; border-bottom:1px solid '.esc_attr($args_color["snippet_border"] ).';">'.esc_attr(stripslashes( $args_review['review_title'] ) ).'</div>';
		$review .= '<div class="snippet-markup" itemscope itemtype="http://schema.org/Review">';
		$item = get_post_meta( $post->ID, '_bsf_item_name', true );
		$rating = get_post_meta( $post->ID, '_bsf_rating', true );
		$reviewer = get_post_meta( $post->ID, '_bsf_item_reviewer', true);
		$post_date = get_the_date('Y-m-d');
		if(trim($reviewer) != "")
		{
			if($args_review['item_reviewer'] != "")
				$review .= '<span itemprop="author" itemscope itemtype="http://schema.org/Person">';
				$review .= "<div class='snippet-label'>".esc_attr(stripslashes( $args_review['item_reviewer'] ) )."</div>";
			$review .= " <div class='snippet-data'><span itemprop='name'>".esc_attr( stripslashes( $reviewer ) )."</span></div></span>";
		}
		if(isset($args_review['review_date']))
		{
			if( $args_review['review_date'] != "")
				$review .= "<div class='snippet-label'>".esc_attr(stripslashes( $args_review['review_date'] ) ) ."</div>";
			$review .= "<div class='snippet-data'> <time itemprop='datePublished' datetime='".get_the_time( 'c' )."'>".esc_attr( $post_date )."</time></div>";
		}
		if(trim($item) != "")
		{
			if( $args_review['item_name'] != "")
				$review .= "<div class='snippet-label'>".esc_attr(stripslashes( $args_review['item_name'] ) )."</div>";
			$review .= "<div class='snippet-data'> <span itemprop='itemReviewed'>".esc_attr($item )."</span></div>";
		}
		if(trim($rating) != "")
		{
			if( $args_review['item_rating'] != "")
				$review .= "<div class='snippet-label'>".esc_attr(stripslashes(  $args_review['item_rating'] ) )."</div>";
			
			$review .= "<div class='snippet-data'> <span itemprop='reviewRating' itemscope itemtype='http://schema.org/Rating'><span class='rating-value' itemprop='ratingValue'>".esc_attr( $rating )."</span></span><span class='star-img'>";
			for($i = 1; $i<=ceil($rating); $i++)
			{
				$review .= '<img src="'.plugin_dir_url(__FILE__) .'images/1star.png" alt="1star">';
			}
			for($j = 0; $j<=5-ceil($rating); $j++)
			{
				if($j)
					$review .= '<img src="'.plugin_dir_url(__FILE__) .'images/gray.png" alt="gray">'; 
			}
			$review .= '</span></div>';
		}
		$review .= "</div> 
			</div><div style='clear:both;'></div>";
			
		return ( is_single() || is_page() ) ? $content.$review : $content;
	} 
	else if($type == '2')
	{
		global $post;
		$args_event = get_option('bsf_event');
		
		$event = '';
		
		$event .= '<div id="snippet-box" class="snippet-type-'.esc_attr($type).'" style="background:'.esc_attr( $args_color["snippet_box_bg"] ).'; color:'.esc_attr( $args_color["snippet_box_color"] ).'; border:1px solid '.esc_attr( $args_color["snippet_border"] ).';">';
		
		if($args_event['snippet_title'] != "")
			$event .= '<div class="snippet-title" style="background:'.esc_attr( $args_color["snippet_title_bg"] ).'; color:'.esc_attr( $args_color["snippet_title_color"] ).'; border-bottom:1px solid '.esc_attr( $args_color["snippet_border"] ).';">'.esc_attr( stripslashes(  $args_event['snippet_title'] ) ).'</div>';
		$event .= '<div itemscope itemtype="http://schema.org/Event">';
		$event_title = get_post_meta( $post->ID, '_bsf_event_title', true );
		$event_org = get_post_meta( $post->ID, '_bsf_event_organization', true );
		$event_street = get_post_meta( $post->ID, '_bsf_event_street', true );	
		$event_local = get_post_meta( $post->ID, '_bsf_event_local', true );	
		$event_region = get_post_meta( $post->ID, '_bsf_event_region', true );
		$event_postal_code = get_post_meta( $post->ID, '_bsf_event_postal_code', true );
		$event_image = get_post_meta($post->ID, '_bsf_event_image', true);
		//$event_performer = get_post_meta( $post->ID, '_bsf_event_performer', true );
		$event_start_date = get_post_meta( $post->ID, '_bsf_event_start_date', true );	
		$event_end_date = get_post_meta( $post->ID, '_bsf_event_end_date', true );	
		$event_description = get_post_meta( $post->ID, '_bsf_event_desc', true );
		$event_ticket_url = get_post_meta( $post->ID, '_bsf_event_ticket_url', true );	
		$event_price = get_post_meta( $post->ID, '_bsf_event_price', true );	
		$event_cur = get_post_meta( $post->ID, '_bsf_event_cur', true );

		if(trim($event_image) != "")
		{
			$event .= '<div class="snippet-image"><img width="180" src="'.esc_url( $event_image ).'" itemprop="image" alt="event" /></div>';
		}
		else
		{
			$event .= '<script type="text/javascript">
				jQuery(document).ready(function() {
                    jQuery(".snippet-label-img").addClass("snippet-clear");
                });
			</script>';
		}
		$event .= '<div class="aio-info">';
		
		if(trim($event_title) != "")
		{
			if( $args_event['event_title'])
				$event .= '<div class="snippet-label-img">'.esc_attr(stripslashes( $args_event['event_title']) ).'</div>';
			$event .=' <div class="snippet-data-img">​<span itemprop="name">'.esc_attr( $event_title ).'</span></div>
			<meta itemprop="url" content="'.esc_attr( $event_ticket_url ).'">
			<div class="snippet-clear"></div>';
		}
		if(trim($event_org) != "")
		{
			if( $args_event['event_location'] != "")
				$event .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_event['event_location'] ) ).'</div>';
			$event .=' <div class="snippet-data-img"> 
				​<span itemprop="location" itemscope itemtype="http://schema.org/Place">
							<span itemprop="name">'.esc_attr( $event_org ).'</span>,';
		}
		if(trim($event_street) != "")
			$event .= '<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
							  <span itemprop="streetAddress">'.esc_attr( $event_street ).'</span>,';
		if(trim($event_local) != "")
			$event .= '<span itemprop="addressLocality">'.esc_attr( $event_local ).'</span>,';
		if(trim($event_region) != "")
			$event .= '<span itemprop="addressRegion">'.esc_attr( $event_region ).'</span>';
		if(trim($event_postal_code) != "")
			$event .= '-<span itemprop="postalCode">'.esc_attr( $event_postal_code ).'</span>';
		$event .= '</span>';

		$event .='</span>
			</div><div class="snippet-clear"></div>';

		if(trim($event_start_date) != "")
		{
			if( $args_event['start_time'] != "")
				$event .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_event['start_time'] ) ).'</div>';
	
			$event .= ' <div class="snippet-data-img"> <span itemprop="startDate" datetime="'.esc_attr( $event_start_date ).'T00:00-00:00">'.esc_attr( $event_start_date ).'</span></div><div class="snippet-clear"></div>';
		}
		if(trim($event_end_date) != "")
		{
			if( $args_event['end_time'] != "")
				$event .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_event['end_time'] ) ).'</div>';
			$event .= ' <div class="snippet-data-img"> <span itemprop="endDate" datetime="'.esc_attr( $event_end_date ).'T00:00-00:00">'.esc_attr( $event_end_date ).'</span></div><div class="snippet-clear"></div>';
		}
		if(trim($event_description) != "")
		{
			if( $args_event['event_desc'] != "")
				$event .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_event['event_desc'] ) ).'</div>';
			$event .= ' <div class="snippet-data-img"> <span itemprop="description">'.esc_attr( htmlspecialchars_decode( $event_description ) ).'</span></div><div class="snippet-clear"></div>';
		}

		if(trim($event_price) != "")
		{
			if($args_event['events_price'] != "")
				$event .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_event['events_price'] ) ).'</div>';
			$event .= '<div class="snippet-data-img"> <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<span itemprop="priceCurrency">'.esc_attr( $event_cur ).'</span><span itemprop="price">'.' '.esc_attr( $event_price ).'</span><br><a itemprop="url" href="'.esc_url( $event_ticket_url ).'">Buy Tickets</a></div><div class="snippet-clear"></div>';
		}

		$event .= '</div>
			</div></div>
			<meta itemprop="description" content="Event">
			<div class="snippet-clear"></div>';

		return ( is_single() || is_page() ) ? $content.$event : $content;
	}
	else if($type == '4')
	{
		global $post;
		$organization = '';
		$organization .= '<div class="snippet-title">Organization Brief :</div>';
		$organization .= '<div xmlns:v="http://rdf.data-vocabulary.org/#" typeof="v:Organization">';
		$org_name = get_post_meta( $post->ID,'_bsf_organization_name', true );
		$org_url = get_post_meta( $post->ID,'_bsf_organization_url', true );
		$org_tel = get_post_meta( $post->ID,'_bsf_organization_tel', true );
		$org_street = get_post_meta( $post->ID,'_bsf_organization_street', true );
		$org_local = get_post_meta( $post->ID,'_bsf_organization_local', true );
		$org_region = get_post_meta( $post->ID,'_bsf_organization_region', true );
		$org_zip = get_post_meta( $post->ID,'_bsf_organization_zip', true );
		$org_country = get_post_meta( $post->ID,'_bsf_organization_country', true );
		$org_latitude = get_post_meta( $post->ID,'_bsf_organization_latitude', true );
		$org_longitude = get_post_meta( $post->ID,'_bsf_organization_longitude', true );
		if(trim($org_name) != "")
			$organization .= 'Organization Name : <span property="v:name">'.esc_attr( $org_nam ).'</span></div>';
		if(trim($org_url) != "")
			$organization .= 'Website : <a href="'.esc_url( $org_url ).'" rel="v:url">'.esc_attr( $org_url ).'</a></div>';
		if(trim($org_tel) != "")
			$organization .= 'Telephone No. : <span property="v:tel">'.esc_attr( $org_tel ).'</span></div>';
		if(trim($org_street) != "")
			$organization .= 'Address : 
			<span rel="v:address">
				<span typeof="v:Address">
					<span property="v:street-address">'.esc_attr( $org_street ).'</span>';
		if(trim($org_local) != "")
			$organization .= '<span property="v:locality">'.esc_attr( $org_local ).'</span>';
		if(trim($org_region) != "")
			$organization .= '<span property="v:region">'.esc_attr( $org_region ).'</span>';
		if(trim($org_zip) != "")
			$organization .= '<span property="v:postal-code">'.esc_attr( $org_zip ).'</span>';
		if(trim($org_country) != "")
			$organization .= '<span property="v:country-name">'.esc_attr( $org_country ).'</span>
					</span>
				</span>';
		if(trim($org_latitude) != "")
			$organization .= 'GEO Location :
			<span rel="v:geo">
				<span typeof="v:Geo">
						 <span property="v:latitude" content="'.esc_attr( $org_latitude ).'">'.esc_attr( $org_latitude ).'</span> - ';
		if(trim($org_longitude) != "")
			$organization .= '<span property="v:longitude" content="'.esc_attr( $org_longitude ).'">'.esc_attr( $org_longitude ).'</span>
				</span>
			</span>';
		$organization .= '</div><div style="clear:both;"></div>';

		return ( is_single() || is_page() ) ? $content.$organization : $content;
	}
	else if($type == '5')
	{
		global $post;
		
		$args_person = get_option('bsf_person');
		
		$people = '';
		
		$people .= '<div id="snippet-box" class="snippet-type-'.esc_attr($type).'" style="background:'.esc_attr( $args_color["snippet_box_bg"] ).'; color:'.esc_attr( $args_color["snippet_box_color"] ).'; border:1px solid '.esc_attr( $args_color["snippet_border"] ).';">';
		
		if($args_person['snippet_title'] != "")
			$people .= '<div class="snippet-title" style="background:'.esc_attr( $args_color["snippet_title_bg"] ).'; color:'.esc_attr( $args_color["snippet_title_color"] ).'; border-bottom:1px solid '.esc_attr( $args_color["snippet_border"] ).';">'.esc_attr( stripslashes(  $args_person['snippet_title'] ) ).'</div>';
		$people .= '<div itemscope itemtype="http://schema.org/Person"">';
		$people_fn = get_post_meta( $post->ID, '_bsf_people_fn', true );
		$people_nickname = get_post_meta( $post->ID, '_bsf_people_nickname', true );
		$people_photo = get_post_meta( $post->ID, '_bsf_people_photo', true );
		$people_job_title = get_post_meta( $post->ID, '_bsf_people_job_title', true );
		$people_website = get_post_meta( $post->ID, '_bsf_people_website', true );
		$people_company = get_post_meta( $post->ID, '_bsf_people_company', true );
		$people_local = get_post_meta( $post->ID, '_bsf_people_local', true );
		$people_region = get_post_meta( $post->ID, '_bsf_people_region', true );

		$people_street = get_post_meta( $post->ID, '_bsf_people_street', true );
		$people_postal = get_post_meta( $post->ID, '_bsf_people_postal', true );
		
		if(trim($people_photo) != "")
		{
			$people .= '<div class="snippet-image"><img width="180" src="'.esc_url( $people_photo ).'" itemprop="image" alt="Photo of'.esc_attr( $people_fn ).'" /></div>';	
		}
		else
		{
			$people .= '<script type="text/javascript">
				jQuery(document).ready(function() {
                    jQuery(".snippet-label-img").addClass("snippet-clear");
                });
			</script>';
		}
		$people .= '<div class="aio-info">';
		if(trim($people_fn) != "")
		{
			if($args_person['person_name'] != "")
				$people .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_person['person_name'] ) ).'</div> ';
				
			$people .= '<div class="snippet-data-img"><span itemprop="name">'.esc_attr( $people_fn ).'</span></div><div class="snippet-clear"></div>';
		}
		if(trim($people_nickname) != "")
		{
			if($args_person['person_nickname'] != "")
				$people .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_person['person_nickname'] ) ).'</div> ';
			$people .= '<div class="snippet-data-img"> (<span itemprop="additionalName">'.esc_attr( $people_nickname ).'</span>)</div><div class="snippet-clear"></div>';	
		}
		if(trim($people_website) != "")
		{
			if($args_person['person_website'] != "")
				$people .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_person['person_website'] ) ).'</div> ';
			$people .= '<div class="snippet-data-img"> <a href="'.esc_url( $people_website ).'" itemprop="url">'.esc_attr( $people_website ).'</a></div><div class="snippet-clear"></div>';	
		}
		if(trim($people_job_title) != "")
		{
			if($args_person['person_job_title'] != "")
				$people .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_person['person_job_title'] ) ).'</div> ';
			$people .= '<div class="snippet-data-img"> <span itemprop="jobTitle">'.esc_attr( $people_job_title ).'</span></div><div class="snippet-clear"></div>';	
		}
		if(trim($people_company) != "")
		{
			if($args_person['person_company'] != "")
				$people .= '<div itemprop="affiliation" itemscope itemtype="http://schema.org/Organization">';
				$people .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_person['person_company'] ) ).'</div> ';
			$people .= '<div class="snippet-data-img"> <span itemprop="name">'.esc_attr( $people_company ).'</span></div><div class="snippet-clear"></div>';	
			$people .= '</div>';
		}

		if(trim($people_street) != "")
		{
			if($args_person['person_address'] != "")
				$people .= '<div class="snippet-label-img">'.esc_attr( stripslashes(  $args_person['person_address'] ) ).'</div> ';
				$people .= '<div class="snippet-data-img">';
					$people .= '<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
						$people .= '<span itemprop="streetAddress">'.esc_attr( $people_street ).'</span>,<br>';	
						if(trim($people_local) != "")
						{
							$people .=	'<span itemprop="addressLocality">'.esc_attr( $people_local ).'</span>, ';	
						}
						if(trim($people_region) != "")
						{
							$people .= '<span itemprop="addressRegion">'.esc_attr( $people_region ).'</span>, ';
						}
						if(trim($people_postal) != "")
						{
							$people .= '<span itemprop="postalCode">'.esc_attr( $people_postal ).'</span>';
						}			
					$people .= '</span>';
				$people .= '</div>';
				$people .= '<div class="snippet-clear"></div>';	
		}
		$people .= '</div>
				</div></div><div class="snippet-clear"></div>';
		return ( is_single() || is_page() ) ? $content.$people : $content;
	}
	else if($type == '6')
	{
		global $post;
		$args_product = get_option('bsf_product');
		$product = '';
		$product .= '<div id="snippet-box" class="snippet-type-'.esc_attr($type).'" style="background:'.esc_attr( $args_color["snippet_box_bg"] ).'; color:'.esc_attr( $args_color["snippet_box_color"] ).'; border:1px solid '.esc_attr( $args_color["snippet_border"] ).';">';
		if($args_product['snippet_title'] != "")
			$product .= '<div class="snippet-title" style="background:'.esc_attr( $args_color["snippet_title_bg"] ).'; color:'.esc_attr( $args_color["snippet_title_color"] ).'; border-bottom:1px solid '.esc_attr( $args_color["snippet_border"] ).';">'.esc_attr( stripslashes(  $args_product['snippet_title'] ) );
		$product .= bsf_do_rating();
		
		$product .= '</div>';
		$product .= '<div  itemscope itemtype="http://schema.org/Product">';
		$product_rating = get_post_meta( $post->ID, '_bsf_product_rating', true);
		$product_brand = get_post_meta( $post->ID, '_bsf_product_brand', true);
		$product_name = get_post_meta( $post->ID, '_bsf_product_name', true);
		$product_image = get_post_meta($post->ID, '_bsf_product_image', true);
		$product_cat = get_post_meta($post->ID, '_bsf_product_cat', true);
		$product_price = get_post_meta($post->ID, '_bsf_product_price', true);
		$product_cur = get_post_meta($post->ID, '_bsf_product_cur', true);
		$product_status = get_post_meta($post->ID, '_bsf_product_status', true);
		if(trim($product_status) == "out_of_stock"){
			$product_status = 'OutOfStock';
			$availability = "Out of Stock";
		}
		else if(trim($product_status) == "in_stock"){
			$product_status = 'InStock';
			$availability = "Available in Stock";
		}
		else if(trim($product_status) == "instore_only"){
			$product_status = 'InStoreOnly';
			$availability = "Available in Store Only";
		}
		else if(trim($product_status) == "preorder"){
			$availability = "Pre-Order Only";
		}
		if(trim($product_image) != "")
		{
			$product .= '<div class="snippet-image"><img width="180" src="'.esc_url($product_image ).'" itemprop="image" alt="product image" /></div>';
		}
		else
		{
			$product .= '<script type="text/javascript">
				jQuery(document).ready(function() {
                    jQuery(".snippet-label-img").addClass("snippet-clear");
                });
			</script>';
		}
		$product .= '<div class="aio-info">';		
		if(trim($product_rating) != "")
		{
			if($args_product['product_brand'] != "")
				$product .= '<div class="snippet-label-img">'.$args_product['product_rating'].'</div>';		
			$product .= '<div class="snippet-data-img"><span class="star-img">';
							for($i = 1; $i<=ceil($product_rating); $i++)
							{
								$product .= '<img src="'.plugin_dir_url(__FILE__) .'images/1star.png" alt="1star">'; 
							}
							for($j = 0; $j<=5-ceil($product_rating); $j++)
							{
								if($j)
									$product .= '<img src="'.plugin_dir_url(__FILE__) .'images/gray.png" alt="gray">'; 
							}
			$product .= '</span></div><div class="snippet-clear"></div>';
		}

		$product .= '<div class="aggregate_sec" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
		if($args_product['product_agr'] != "")
		{
			$product .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_product['product_agr'] ) ).'</div>';
		}
		$product .= '<div class="snippet-data-img">';
		$product .= '<span itemprop="ratingValue">'.average_rating().'</span>';						
		$product .= ' based on <span class="rating-count" itemprop="reviewCount">'.rating_count().'</span> votes </span></div></div><div class="snippet-clear"></div>';
		

		if(trim($product_brand) != "")
		{
			if($args_product['product_brand'] != "")
				$product .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_product['product_brand'] ) ).'</div>';
			$product .= ' <div class="snippet-data-img"> <span itemprop="brand">'.esc_attr( $product_brand ).'</span></div><div class="snippet-clear"></div>';
		}
		if(trim($product_name) != "")
		{
			if($args_product['product_name'] != "")
				$product .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_product['product_name'] ) ).'</div>';
			$product .= ' <div class="snippet-data-img"> <span itemprop="name">'.esc_attr( $product_name ).'</span></div><div class="snippet-clear"></div>';
		}

		
		if(trim($product_price) != "")
		{
			if($args_product['product_price'] != "")
				$product .= '<div class="offer_sec" itemprop="offers" itemscope itemtype="http://schema.org/Offer"><div class="snippet-label-img">'.esc_attr( stripslashes( $args_product['product_price'] ) ).'</div>';
			$product .= '<div class="snippet-data-img"> 
			<span itemprop="priceCurrency">'.esc_attr( $product_cur ).'</span><span itemprop="price">'.' '.esc_attr( $product_price ).'</span></div>';
			
			if(trim($product_status) != "")
			{
				if($args_product['product_avail'] != "")
					$product .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_product['product_avail'] ) ).'</div>';
				$product .= ' <div class="snippet-data-img"> <span itemprop="availability" content="'.esc_attr( $product_status ).'">'.esc_attr( $availability ).'</span></span></div><div class="snippet-clear"></div>';		
			}
			$product .= '</div><div class="snippet-clear"></div>';
		}
		$product .= '</div>
			</div></div><div class="snippet-clear"></div>';

		return ( is_single() || is_page() ) ? $content.$product : $content;
	}
	else if($type == '7')
	{
		global $post;
		$recipe = '';
		
		$recipe .= '<div id="snippet-box" class="snippet-type-'.esc_attr($type).'" style="background:'.esc_attr( $args_color["snippet_box_bg"] ).'; color:'.esc_attr( $args_color["snippet_box_color"] ).'; border:1px solid '.esc_attr( $args_color["snippet_border"] ).';">';
		
		$args_recipe = get_option('bsf_recipe');
		
		if($args_recipe['snippet_title'] != "" )
		{
			$recipe .= '<div class="snippet-title" style="background:'.esc_attr( $args_color["snippet_title_bg"] ).'; color:'.esc_attr( $args_color["snippet_title_color"] ).'; border-bottom:1px solid '.esc_attr( $args_color["snippet_border"] ).';">'.esc_attr( stripslashes( $args_recipe['snippet_title'] ) );
			$recipe .= bsf_do_rating();
		}
		$recipe .= '</div>';
		$recipe .= '<div itemscope itemtype="http://schema.org/Recipe">';
		$recipes_name = get_post_meta( $post->ID, '_bsf_recipes_name', true );
		$authors_name = get_post_meta( $post->ID, '_bsf_authors_name', true );
		$recipes_preptime = get_post_meta( $post->ID, '_bsf_recipes_preptime', true );
		$recipes_cooktime = get_post_meta( $post->ID, '_bsf_recipes_cooktime', true );
		$recipes_totaltime = get_post_meta( $post->ID, '_bsf_recipes_totaltime', true );
		$recipes_photo = get_post_meta( $post->ID, '_bsf_recipes_photo', true );
		$recipes_desc = get_post_meta( $post->ID, '_bsf_recipes_desc', true );
		$recipes_nutrition = get_post_meta( $post->ID, '_bsf_recipes_nutrition', true );
		$recipes_ingredient = get_post_meta( $post->ID, '_bsf_recipes_ingredient', true );
		$count = rating_count();
		$agregate = average_rating();
		if(trim($recipes_photo) != "")
		{
			$recipe .= '<div class="snippet-image"><img width="180" itemprop="image" src="'.esc_url( $recipes_photo ).'" alt="recipe image"/></div>';
		}
		else
		{
			$recipe .= '<script type="text/javascript">
				jQuery(document).ready(function() {
                    jQuery(".snippet-label-img").addClass("snippet-clear");
                });
			</script>';
		}
		$recipe .= '<div class="aio-info">';
		if(trim($recipes_name) != "")
		{
			if($args_recipe['recipe_name'] != "")
				$recipe .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_recipe['recipe_name'] ) ).'</div>';
				
			$recipe .= '<div class="snippet-data-img"><span itemprop="name">'.esc_attr( $recipes_name ).'</span></div>
			<meta itemprop="description" content="'.esc_attr( htmlspecialchars_decode( $recipes_desc ) ).'" >
			<meta itemprop="recipeIngredient" content="'.esc_attr( $recipes_ingredient ).'" >
			<div itemprop="nutrition"
		    itemscope itemtype="http://schema.org/NutritionInformation">
		    <meta itemprop="calories" content="'.esc_attr( $recipes_nutrition ).'" ></div>
			<div class="snippet-clear"></div>';
		}
		if(trim($authors_name) != "")
		{
			if($args_recipe['author_name'] != "")
				$recipe .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_recipe['author_name'] ) ).'</div>';
				
			$recipe .= '<div class="snippet-data-img"><span itemprop="author">'.esc_attr( $authors_name ).'</span></div><div class="snippet-clear"></div>';
		}
		$recipe .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_recipe['recipe_pub'] ) ).' </div><div class="snippet-data-img"><time datetime="'.get_the_time( 'c' ).'" itemprop="datePublished">'.get_the_date('Y-m-d').'</time></div><div class="snippet-clear"></div>';
		if(trim($recipes_preptime) != "")
		{
			if($args_recipe['recipe_prep'] != "")
				$recipe .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_recipe['recipe_prep'] ) ).'</div>';
			$recipe .= '<div class="snippet-data-img"> <time datetime="PT'.esc_attr( $recipes_preptime ).'" itemprop="prepTime">'.esc_attr( $recipes_preptime ).'</time></div><div class="snippet-clear"></div>';
		}
		if(trim($recipes_cooktime) != "")
		{
			if($args_recipe['recipe_cook'] != "")
				$recipe .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_recipe['recipe_cook'] ) ).'</div>';
			$recipe .= '<div class="snippet-data-img"> <time datetime="PT'.esc_attr( $recipes_cooktime ).'" itemprop="cookTime">'.esc_attr( $recipes_cooktime ).'</time></div><div class="snippet-clear"></div> ';
		}
		if(trim($recipes_totaltime) != "")
		{
			if($args_recipe['recipe_time'] != "")
				$recipe .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_recipe['recipe_time'] ) ).'</div>';
			$recipe .= '<div class="snippet-data-img"> <time datetime="PT'.esc_attr( $recipes_totaltime ).'" itemprop="totalTime">'.esc_attr( $recipes_totaltime ).'</time></div><div class="snippet-clear"></div>';
		}
		if($args_recipe['recipe_rating'] != "" && $count > 0)
		{
			$recipe .= '<div class="snippet-label-img">'.esc_attr( stripslashes(  $args_recipe['recipe_rating'] ) ).'</div>';
			$recipe .= ' <div class="snippet-data-img"> <span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"><span itemprop="ratingValue" class="rating-value">'.esc_attr( $agregate ).'</span><span class="star-img">';
			for($i = 1; $i<=ceil($agregate); $i++)
			{
				$recipe .= '<img src="'.plugin_dir_url(__FILE__) .'images/1star.png" alt="1star">'; 
			}
			for($j = 0; $j<=5-ceil($agregate); $j++)
			{
				if($j)
					$recipe .= '<img src="'.plugin_dir_url(__FILE__) .'images/gray.png" alt="gray">'; 
			}
			$recipe .= '</span> Based on <span itemprop="reviewCount"><strong>'.esc_attr( $count ).'</strong> </span> Review(s)</span></div><div class="snippet-clear"></div>';
		}
		$recipe .= '</div>
				</div></div><div class="snippet-clear"></div>';

		return ( is_single() || is_page() ) ? $content.$recipe : $content;
	}
	else if($type == '8')
	{
		global $post;
		$args_soft = get_option('bsf_software');	
		$software = '';
		
		$software .= '<div id="snippet-box" class="snippet-type-'.esc_attr($type).'" style="background:'.esc_attr( $args_color["snippet_box_bg"] ).'; color:'.esc_attr( $args_color["snippet_box_color"] ).'; border:1px solid '.esc_attr( $args_color["snippet_border"] ).';">';
		if($args_soft['snippet_title'] != "" )
			$software .= '<div class="snippet-title" style="background:'.esc_attr( $args_color["snippet_title_bg"] ).'; color:'.esc_attr( $args_color["snippet_title_color"] ).'; border-bottom:1px solid '.esc_attr( $args_color["snippet_border"] ).';">'.esc_attr( stripslashes( $args_soft['snippet_title'] ) );
		
		$software .= bsf_do_rating();
		$software .= '</div>';

		$software .= '<div itemscope itemtype="http://schema.org/SoftwareApplication">';
		$software_rating = get_post_meta( $post->ID, '_bsf_software_rating', true);
		$software_name = get_post_meta( $post->ID, '_bsf_software_name', true );
		$software_desc = get_post_meta( $post->ID, '_bsf_software_desc', true );
		$software_landing = get_post_meta( $post->ID, '_bsf_software_landing', true );
		$software_image = get_post_meta( $post->ID, '_bsf_software_image', true );
		$software_price = get_post_meta( $post->ID, '_bsf_software_price', true );
		$software_cur = get_post_meta($post->ID, '_bsf_software_cur', true);		
		$software_os = get_post_meta( $post->ID, '_bsf_software_os', true );
		$software_cat = get_post_meta( $post->ID, '_bsf_software_cat', true );


		if(trim($software_image) != "")
		{
			$software .= '<div class="snippet-image"><img width="180" src="'.esc_url( $software_image ).'" itemprop="screenshot" alt="software image" /></div>';	
		}
		else
		{
			$software .= '<script type="text/javascript">
				jQuery(document).ready(function() {
                    jQuery(".snippet-label-img").addClass("snippet-clear");
                });
			</script>';
		}
		$software .= '<div class="aio-info">';		

		if(trim($software_rating) != "")
		{
			$software .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_soft['software_rating'] ) ).'</div>';		
			$software .= '<div class="snippet-data-img"><span class="star-img">';
							for($i = 1; $i<=ceil($software_rating); $i++)
							{
								$software .= '<img src="'.plugin_dir_url(__FILE__) .'images/1star.png" alt="1star">'; 
							}
							for($j = 0; $j<=5-ceil($software_rating); $j++)
							{
								if($j)
									$software .= '<img src="'.plugin_dir_url(__FILE__) .'images/gray.png" alt="gray">'; 
							}
			$software .= '</span></div><div class="snippet-clear"></div>';
		}

		
		$software .= '<div class="aggregate_sec" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
			$software .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_soft['software_agr'] ) ).'</div>';
		$software .= '<div class="snippet-data-img">';
		$software .= '<span itemprop="ratingValue">'.average_rating().'</span>';						
		$software .= ' based on <span class="rating-count" itemprop="reviewCount">'.rating_count().'</span> votes </span></div></div><div class="snippet-clear"></div>';
		
		if(trim($software_name) != "")
		{
			if($args_soft['software_name'] != "")
				$software .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_soft['software_name'] ) ).'</div>';
			$software .= ' <div class="snippet-data-img"> <span itemprop="name">'.esc_attr( $software_name ).'</span></div><div class="snippet-clear"></div>';
		}
		if(trim($software_os) != "")
		{
			if($args_soft['software_os'] != "")
				$software .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_soft['software_os'] ) ).'</div>';
			$software .= ' <div class="snippet-data-img"> <span itemprop="operatingSystem">'.esc_attr( $software_os ).'</span></div><div class="snippet-clear"></div>';		
		}
		if(trim($software_cat) != "")
		{
				$software .= '<div class="snippet-label-img">Software Category</div>';
			$software .= ' <div class="snippet-data-img"> <span itemprop="applicationCategory">'.esc_attr( $software_cat ).'</span></div><div class="snippet-clear"></div>';		
		}
		if(trim($software_price) != "")
		{
			if($args_soft['software_price'] != "")
				$software .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_soft['software_price'] ) ).'</div>';
			$software .= '<div class="snippet-data-img"> <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<span itemprop="priceCurrency">'.esc_attr( $software_cur ).'</span> <span itemprop="price"> '.esc_attr( $software_price ).'</span></div><div class="snippet-clear"></div>';
			
		}
		if(trim($software_desc) != "")
		{
			if($args_soft['software_desc'] != "")
				$software .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_soft['software_desc'] ) ).'</div>';
			$software .= ' <div class="snippet-data-img"> <span itemprop="description">'.esc_attr( htmlspecialchars_decode( $software_desc ) ).'</span></div><div class="snippet-clear"></div>';
		}
		if(trim($software_landing) != "")
		{
			if($args_soft['software_website'] != "")
				$software .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_soft['software_website'] ) ).'</div>';
			$software .= '<div class="snippet-data-img"> <a itemprop="featureList" href="'.esc_url( $software_landing ).'">'.esc_attr( $software_landing ).'</a></div><div class="snippet-clear"></div>';
		}
		$software .= '</div>
				</div></div><div class="snippet-clear"></div>';

		return ( is_single() || is_page() ) ? $content.$software : $content;
	}
	else if($type == '9')
	{
		global $post;
		$args_video = get_option('bsf_video');
		$video = '';
		
		$video .= '<div id="snippet-box" class="snippet-type-'.esc_attr($type).'" style="background:'.esc_attr( $args_color["snippet_box_bg"] ).'; color:'.esc_attr( $args_color["snippet_box_color"] ).'; border:1px solid '.esc_attr( $args_color["snippet_border"] ).';">';
		
		if($args_video['snippet_title'] != "" )
			$video .= '<div class="snippet-title" style="background:'.esc_attr( $args_color["snippet_title_bg"] ).'; color:'.esc_attr( $args_color["snippet_title_color"] ).'; border-bottom:1px solid '.esc_attr( $args_color["snippet_border"] ).';">'.esc_attr( stripslashes( $args_video['snippet_title'] ) ).'</div>';
		$video .= '<div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">';
		$video_title = get_post_meta( $post->ID, '_bsf_video_title', true );
		$video_desc = get_post_meta( $post->ID, '_bsf_video_desc', true );
		$video_thumb = get_post_meta( $post->ID, '_bsf_video_thumb', true );
		$video_url = get_post_meta( $post->ID, '_bsf_video_url', true );
		$video_emb_url = get_post_meta( $post->ID, '_bsf_video_emb_url', true );

		$video_duration = get_post_meta( $post->ID, '_bsf_video_duration', true );
		$video_date = get_post_meta( $post->ID, '_bsf_video_date', true );
		if(trim($video_url) != "")
		{
			$video .= '<div class="snippet-image"><a href="'.esc_url( $video_url ).'"><img width="180" src="'.esc_url( $video_thumb ).'" alt="'.esc_attr( $video_title ).'"></a></div>';	
		}
		elseif (trim($video_emb_url) != "") {
			$video .= '<div class="snippet-image"><a href="'.esc_url( $video_emb_url ).'"><img width="180" src="'.esc_url( $video_thumb ).'" " alt="'.esc_attr( $video_title ).'"></a></div>';	
		}
		else
		{
			$video .= '<script type="text/javascript">
				jQuery(document).ready(function() {
                    jQuery(".snippet-label-img").addClass("snippet-clear");
                });
			</script>';
		}
		$video .= '<div class="aio-info" style="padding-top:10px">';		
		if(trim($video_title) != "")
		{
			if($args_video['video_title'] != "" )
				$video .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_video['video_title'] ) ).'</div>';
				
			$video .= '<div class="snippet-data-img"><span itemprop="name">'.esc_attr( $video_title ).'</span></div><div class="snippet-clear"></div>';
		}
		if(trim($video_desc) != "")
		{
			if($args_video['video_desc'] != "" )
				$video .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_video['video_desc'] ) ).'</div>';
			$video .= '<div class="snippet-data-img"> <p itemprop="description">'.esc_attr( htmlspecialchars_decode( $video_desc ) ).'</p></div><div class="snippet-clear"></div>';
		}
		if(trim($video_thumb) != "")
			$video .= '<meta itemprop="thumbnailUrl" content="'.esc_attr( $video_thumb ).'">';
		if(trim($video_url) != ""){
			$video .= '<meta itemprop="contentUrl" content="'.esc_attr( $video_url ).'">';
		}
		elseif(trim($video_emb_url) != ""){
			$video .= '<meta itemprop="embedURL" content="'.esc_attr( $video_emb_url ).'">';
		}			
		if(trim($video_duration) != "")
			$video .= '<meta itemprop="duration" content="'.esc_attr( $video_duration ).'">';		
		if(trim($video_date) != "")
			$video .= '<meta itemprop="uploadDate" content="'.esc_attr( $video_date ).'">';		
		$video .= '</div>
				</div></div><div class="snippet-clear"></div>';

		return ( is_single() || is_page() ) ? $content.$video : $content;
	}
	else if($type == '10')
	{
		global $post;
		$article = '';
		$args_article = get_option('bsf_article');
		$article_title = get_post_meta( $post->ID, '_bsf_article_title', true );
		$article_name = get_post_meta( $post->ID, '_bsf_article_name', true );
		$article_desc = get_post_meta( $post->ID, '_bsf_article_desc', true );
		$article_image = get_post_meta( $post->ID, '_bsf_article_image', true );
		$article_author = get_post_meta( $post->ID, '_bsf_article_author', true );
		$article_publisher = get_post_meta( $post->ID, '_bsf_article_publisher', true );
		$article_publisher_logo = get_post_meta( $post->ID, '_bsf_article_publisher_logo', true );


			$article .= '<div id="snippet-box" class="snippet-type-'.esc_attr($type).'" style="background:'.esc_attr( $args_color["snippet_box_bg"] ).'; color:'.esc_attr( $args_color["snippet_box_color"] ).'; border:1px solid '.esc_attr( $args_color["snippet_border"] ).';">';
			if($args_article['snippet_title'] != "" )
			{
				$article .= '<div class="snippet-title" style="background:'.esc_attr( $args_color["snippet_title_bg"] ).'; color:'.esc_attr( $args_color["snippet_title_color"] ).'; border-bottom:1px solid '.esc_attr( $args_color["snippet_border"] ).';">'.esc_attr( stripslashes( $args_article['snippet_title'] ) );
				$article .= '</div>';
			}
			$article .= '<div itemscope itemtype="http://schema.org/Article">';
			if(trim($article_image) != "")
			{
				$article .= '<div class="snippet-image" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">';
				$article .= '<img width="180" src="'.esc_url( $article_image ).'" alt="'.esc_attr( $article_name ).'"/>';
				$article .=	'<meta itemprop="url" content="'.esc_attr( $article_image ).'">';
				$article .=	'</div>';
			}
			else
			{
				$article .= '<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery(".snippet-label-img").addClass("snippet-clear");
					});
				</script>';
			}
			$article .= '<div class="aio-info">';
			if(trim($article_name) != "")
			{
				if($args_article['article_name'] != "")
					$article .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_article['article_name'] ) ).'</div>';
					
				$article .= '<div class="snippet-data-img"><span itemprop="headline">'.esc_attr( $article_name ).'</span></div><div class="snippet-clear"></div>';
			}
			if(trim($article_desc) != "")
			{
				if($args_article['article_desc'] != "")
					$article .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_article['article_desc'] ) ).'</div>';
					
				$article .= '<div class="snippet-data-img"><span itemprop="description">'.esc_attr( htmlspecialchars_decode( $article_desc ) ).'</span></div><div class="snippet-clear"></div>';
			}
			if(trim($article_author) != "")
			{
				if($args_article['article_author'] != "")
					$article .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_article['article_author'] ) ).'</div>';
					
				$article .= '<div class="snippet-data-img" itemprop="author" itemscope itemtype="https://schema.org/Person">
							<span itemprop="name">'.esc_attr( $article_author ).'</span>
							</div>
							<div class="snippet-clear"></div>';


			}
			if(trim($article_publisher) != "")
			{
				if($args_article['article_publisher'] != "")
				
				$article .= '<div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">';	

				$article .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_article['article_publisher'] ) ).'</div>';
				
				$article .= '<div class="snippet-data-img">
							<span itemprop="name">'.esc_attr( $article_publisher ).'</span>
							</div>
							

							<div class="snippet-clear"></div>';
				if(trim($article_publisher_logo) != "")
				{
					if($args_article['article_publisher_logo'] != "")
						$article .= '<div class="snippet-label-img">'.esc_attr( stripslashes(  $args_article['article_publisher_logo'] ) ).'</div>';

					$article .= '<div class="snippet-data-img publisher-logo" itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">';
					$article .= '<img width="180" src="'.esc_url( $article_publisher_logo ).'" alt="'.esc_attr( $article_publisher ).'" />';
					$article .=	'<meta itemprop="url" content="'.esc_attr( $article_publisher_logo ).'">';
					$article .=	'</div>';
				}

				$article .=	'</div>';
			}
			

				$article .= '<meta itemscope itemprop="mainEntityOfPage"  itemType="https://schema.org/WebPage" itemid="'.get_permalink().'"/>';
				$article .= '<meta itemprop="datePublished" content="'.get_the_time( 'c').'"/>';
				$article .= '<meta itemprop="dateModified" content="'. get_the_modified_time( 'c' ).'"/>';

			
				$article .= '</div>
					</div></div><div class="snippet-clear"></div>';
		

		return ( is_single() || is_page() ) ? $content.$article : $content;
	}else if($type == '11')
	{
		global $post;
		$service = '';
		$args_service = get_option('bsf_service');
		$service_type = get_post_meta( $post->ID, '_bsf_service_type', true );
		$service_area = get_post_meta( $post->ID, '_bsf_service_area', true );
		$service_desc = get_post_meta( $post->ID, '_bsf_service_desc', true );
		$service_image = get_post_meta( $post->ID, '_bsf_service_image', true );
		$service_provider_name = get_post_meta( $post->ID, '_bsf_service_provider', true );
		$service_street = get_post_meta( $post->ID, '_bsf_service_street', true );	
		$service_local = get_post_meta( $post->ID, '_bsf_service_local', true );	
		$service_region = get_post_meta( $post->ID, '_bsf_service_region', true );
		$service_postal_code = get_post_meta( $post->ID, '_bsf_service_postal_code', true );
		$service_provider_location_image = get_post_meta( $post->ID, '_bsf_provider_location_image', true );
		$service_telephone = get_post_meta( $post->ID, '_bsf_service_telephone', true );
		$service_price = get_post_meta( $post->ID, '_bsf_service_price', true );	
		$service_cur = get_post_meta( $post->ID, '_bsf_service_cur', true );
		$service_rating = get_post_meta( $post->ID, '_bsf_service_rating', true );
		$service_rating_switch = get_post_meta( $post->ID, '_bsf_service_rating_switch', true );
		$service_channel = get_permalink( $post->ID );
		$service_url_link = $args_service['service_url_link'] != ''? $args_service['service_url_link'] : "Click Here For More Info";


			$service .= '<div id="snippet-box" class="snippet-type-'.esc_attr($type).'" style="background:'.esc_attr( $args_color["snippet_box_bg"] ).'; color:'.esc_attr( $args_color["snippet_box_color"] ).'; border:1px solid '.esc_attr( $args_color["snippet_border"] ).';">';
			if($args_service['snippet_title'] != "" )
			{
				$service .= '<div class="snippet-title" style="background:'.esc_attr( $args_color["snippet_title_bg"] ).'; color:'.esc_attr( $args_color["snippet_title_color"] ).'; border-bottom:1px solid '.esc_attr( $args_color["snippet_border"] ).';">'.esc_attr( stripslashes( $args_service['snippet_title'] ) );
				if ( $service_rating_switch == 'enable' ) {
					$service .= bsf_do_rating();
				}
				$service .= '</div>';
			}
			$service .= '<div itemscope itemtype="http://schema.org/Service">';
			if(trim($service_image) != "")
			{
				$service .= '<div class="snippet-image">';
				$service .= '<img itemprop="image" width="180" src="'.esc_url( $service_image ).'" alt="'.esc_attr( $service_type ).'"/>';
				$service .=	'</div>';
			}
			else
			{
				$service .= '<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery(".snippet-label-img").addClass("snippet-clear");
					});
				</script>';
			}
			$service .= '<div class="aio-info">';
			
			if( average_rating() > 0 ){
				if($args_service['service_rating'] != "")
				{	
					$service .= '<div class="aggregate_sec" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
					$service .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_service['service_rating'] ) ).'</div>';
					$service .= '<div class="snippet-data-img">';
					$service .= '<span itemprop="ratingValue">'.average_rating().'</span>';						
					$service .= ' based on <span class="rating-count" itemprop="reviewCount">'.rating_count().'</span> votes </span></div></div><div class="snippet-clear"></div>';
				}
			}
			

			if(trim($service_type) != "")
			{
				if($args_service['service_type'] != "")
					$service .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_service['service_type'] ) ).'</div>';

				$service .= '<div class="snippet-data-img">
							<span itemprop="serviceType">'.esc_attr( $service_type ).'</span>
							</div>
							<div class="snippet-clear"></div>';
			}

			if(trim($service_provider_name) != "")
			{
				if($args_service['service_provider_name'] != "")
					$service .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_service['service_provider_name'] ) ).'</div>';
					
				$service .= '<div class="snippet-data-img" itemprop="provider" itemscope itemtype="http://schema.org/LocalBusiness">
							<meta itemprop="image" content="'.esc_attr( $service_provider_location_image ).'"/>
							<span itemprop="name">'.esc_attr( $service_provider_name ).'</span>,';
							if(trim($service_street) != "")
							$service .= '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
							<span itemprop="streetAddress">'.esc_attr( $service_street ).'</span>,';
							if(trim($service_local) != "")
							$service .= '<span itemprop="addressLocality">'.esc_attr( $service_local ).'</span>,';
							if(trim($service_region) != "")
							$service .= '<span itemprop="addressRegion">'.esc_attr( $service_region ).'</span>-';
							if(trim($service_postal_code) != "")
							$service .= '<span itemprop="postalCode">'.esc_attr( $service_postal_code ).'</span>,<br/>';
							if(trim($service_telephone) != "")
							$service .= '<span itemprop="telephone"> Telephone No.'.esc_attr( $service_telephone ).'</span>';
							$service .= '</div>';
							$service .= '</div>
							<div class="snippet-clear"></div>';
			}

			if(trim($service_area) != "")
			{
				if($args_service['service_area'] != "")
					$service .= '<div class="snippet-label-img">'.esc_attr( stripslashes( $args_service['service_area'] ) ).'</div>';
					
				$service .= '<div class="snippet-data-img" itemprop="areaServed" itemscope itemtype="http://schema.org/State">
							<span itemprop="name">'.esc_attr( $service_area ).'</span>
							</div><div class="snippet-clear"></div>';
			}

			if(trim($service_desc) != "")
			{
				if($args_service['service_desc'] != "")
					$service .= '<div class="snippet-label-img">'.esc_attr( stripslashes(  $args_service['service_desc'] ) ).'</div>';
					
				$service .= '<div class="snippet-data-img"><span itemprop="description">'.esc_attr( htmlspecialchars_decode( $service_desc ) ).'</span></div><div class="snippet-clear"></div>';
			}
			
			if(trim($service_channel) != "")
			{
				if($args_service['service_channel'] != "")
					
				$service .= '<div class="snippet-data-img" itemprop="availableChannel" itemscope itemtype="https://schema.org/ServiceChannel">

							<meta itemprop="URL" href="'.esc_url( $service_channel ).'">
							</div><div class="snippet-clear"></div>';
			}

					
			$service .= '</div></div></div><div class="snippet-clear"></div>';

		return ( is_single() || is_page() ) ? $content.$service : $content;
	}	
	 else {
		return $content;
	}
}


add_filter('the_content','display_rich_snippet', 90);


require_once(plugin_dir_path( __FILE__ ).'meta-boxes.php');
function get_the_ip() {
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    }
    else {
        return $_SERVER["REMOTE_ADDR"];
    }
}
function average_rating() {
	global $post;
	
	$data = get_post_meta($post->ID, 'post-rating', false);
	$post_id = $post->ID;
	if( !empty($data) )
	{
		$counter = 0;
	
		$average_rating = 0;    
		foreach($data as $d)
		{
			$rating = $d['user_rating'];
	
			$average_rating = $average_rating + $rating;
	
			$counter++;
		
		} 
		//round the average to the nearast 1/2 point
		return (round(($average_rating/$counter)*2,0)/2);  
	
	} else {
		//no ratings
		return 'no rating';
	}
}
function rating_count()
{
	global $post;
	
	$data = get_post_meta($post->ID, 'post-rating', false);
	return count($data);
}
function bsf_do_rating()
{ 
	global $post;
	$ip = get_the_ip();	
	
	$ip_array = array();
	
	$data = get_post_meta($post->ID, 'post-rating', false);
	if( !empty($data))
	{
		foreach($data as $d)
		{
			array_push($ip_array,$d['user_ip']);
		}
		if(!in_array($ip,$ip_array) )
		{
			return display_rating();
		}
		else if(in_array($ip,$ip_array) )
		{
			$rating = get_previous_rating($ip, $data);
			
			$stars = bsf_display_rating($rating);
			return $stars;
		}
	}
	else
	{
		return display_rating();
	}
}
function get_previous_rating($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && get_previous_rating($needle, $item, $strict))) {
            return !empty($item['user_rating']) ? esc_attr( $item['user_rating'] ) : '';
        }
    }
    return false;
}
function add_ajax_library() {
 
    $html = '<script type="text/javascript">';
        $html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";';
    $html .= '</script>';
 
    echo $html;
}
function bsf_add_rating()
{
	if(isset($_POST['star-review']))
		$stars = esc_attr( $_POST['star-review'] );
	else
		$stars = '0';
	
	$ip = esc_attr( $_POST['ip'] );
	
	$postid = esc_attr( $_POST['post_id'] );
	
	$user_rating = array('post_id' => $postid, 'user_ip' => $ip, 'user_rating' => $stars);
	
	echo false ==  add_post_meta($postid, 'post-rating', $user_rating) ? _e('Error adding your rating') : _e('Ratings added successfully !');
	die();
}
function bsf_update_rating()
{
	if(isset($_POST['star-review']))
		$stars = esc_attr( $_POST['star-review'] );
	else
		$stars = '0';
	
	$ip = esc_attr( $_POST['ip'] );
	
	$postid = esc_attr( $_POST['post_id'] );
	
	$prev_data = get_post_meta($postid,'post-rating',true);
	
	$user_rating = array('post_id' => $postid, 'user_ip' => $ip, 'user_rating' => $stars);
	
	echo false ==  update_post_meta($postid, 'post-rating', $user_rating, $prev_data) ? _e('Error updating your rating') : _e('Ratings updated successfully !');
	die();
}
function display_rating() {
	
		global $post;
        $rating = '<span class="ratings"><div class="star-blocks">';
		$rating .= '<form name="rating" method="post" action="'. get_permalink() .'" id="bsf-rating" onsubmit="return false;">';
        $rating .= '<input type="radio" name="star-review" class="star star-1" value="1"/>';
		$rating .= '<input type="radio" name="star-review" class="star star-2" value="2"/>';
		$rating .= '<input type="radio" name="star-review" class="star star-3" value="3"/>';
		$rating .= '<input type="radio" name="star-review" class="star star-4" value="4"/>';
		$rating .= '<input type="radio" name="star-review" class="star star-5" value="5"/>';
		$rating .= '<input type="hidden" name="ip" value="'.get_the_ip().'" />';
		$rating .= '<input type="hidden" name="post_id" value="'.$post->ID.'" />';		
		$rating .= '</form>';
        $rating .= '</div></span>';
		$script = '<script type="text/javascript">
				jQuery("#bsf-rating").click(function()
				{
					var data = jQuery("#bsf-rating").serialize();
					var form_data = "action=bsf_submit_rating&" + data;
				//	alert(form_data);
					jQuery.post(ajaxurl, form_data,
						function (response) {
							alert(response);
							window.location.href = window.location.href;
						}
					);
				});
			</script>
			';
	$rating .= $script;
    return $rating;
}
function bsf_display_rating($n) {
	
		global $post;
        $rating = '<span class="ratings"><div class="star-blocks">';
		$rating .= '<form name="rating" method="post" action="'. get_permalink() .'" id="bsf-rating" onsubmit="return false;">';
        $rating .= '<input type="radio" name="star-review" class="star star-1" value="1" '; $n == 1 ? $rating .=' checked="checked"/>' : $rating .='/>';
		$rating .= '<input type="radio" name="star-review" class="star star-2" value="2" '; $n == 2 ? $rating .=' checked="checked"/>' : $rating .='/>';
		$rating .= '<input type="radio" name="star-review" class="star star-3" value="3" '; $n == 3 ? $rating .=' checked="checked"/>' : $rating .='/>';
		$rating .= '<input type="radio" name="star-review" class="star star-4" value="4" '; $n == 4 ? $rating .=' checked="checked"/>' : $rating .='/>';
		$rating .= '<input type="radio" name="star-review" class="star star-5" value="5" '; $n == 5 ? $rating .=' checked="checked"/>' : $rating .='/>';
		$rating .= '<input type="hidden" name="ip" value="'.get_the_ip().'" />';
		$rating .= '<input type="hidden" name="post_id" value="'.$post->ID.'" />';		
		$rating .= '</form>';
        $rating .= '</div></span>';
		$script = '<script type="text/javascript">
				jQuery("#bsf-rating").click(function()
				{
					var data = jQuery("#bsf-rating").serialize();
					var form_data = "action=bsf_update_rating&" + data;
				//	alert(form_data);
					jQuery.post(ajaxurl, form_data,
						function (response) {
							alert(response);
							window.location.href = window.location.href;
						}
					);
				});
			</script>
			';
	$rating .= $script;
    return $rating;
}
