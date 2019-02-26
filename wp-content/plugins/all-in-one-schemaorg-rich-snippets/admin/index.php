<?php
// Start object buffering to supress warning messages
ob_start();
if ( is_admin() )
{
	add_action( 'admin_footer', 'add_footer_script' );
}
//enqueues the scripts and styles in admin dashboard
function bsf_admin_styles() {
	wp_enqueue_style( 'star_style' );	
	wp_enqueue_style( 'meta_style' );		
	wp_enqueue_script( 'bsf_jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'bsf_jquery_star' );
}
function add_the_script() {
   wp_enqueue_script('postbox');
   wp_enqueue_style( 'admin_style' );		
}
add_action('admin_print_scripts', 'add_the_script');
//The Main Admin Dashboard for Rich Snippets Plugin
function rich_snippet_dashboard() {
	$plugins_url = plugins_url();
	if ( !get_option( 'bsf_woocom_init_setting' ) ) {
		$args_woocom = true;
	}else {
		$args_woocom = get_option('bsf_woocom_setting');
	}
	
	$args_review = get_option('bsf_review');
	$args_event = get_option('bsf_event');
	$args_person = get_option('bsf_person');
	$args_product = get_option('bsf_product');
	$args_recipe = get_option('bsf_recipe');
	$args_soft = get_option('bsf_software');	
	$args_video = get_option('bsf_video');	
	$args_article = get_option('bsf_article');
	$args_service = get_option('bsf_service');

	$woo_setting = '';
	if( !empty( $args_woocom ) ) { 
		$woo_setting = "Checked";
	} 
	
	if(isset($args_event["event_desc"]) ) { 
		$event_desc = $args_event["event_desc"]; 
	}
	else {
		$event_desc = null;
	}

	if(isset($args_recipe["author_name"])) {
		$author_name = $args_recipe["author_name"];
	}
	else {
		$author_name = null;
	}

	if(isset($args_recipe["recipe_desc"])) {
		$recipe_desc = $args_recipe["recipe_desc"];
	}
	else {
		$recipe_desc = null;
	}

	if(isset($args_service["provider_location"])) {
		$provider_location = $args_service["provider_location"];
	}
	else {
		$provider_location = null;
	}

	$args_color = get_option('bsf_custom');	
	echo '<div class="wrap">';
	echo '<div id="star-icons-32" class="icon32"></div><h2>'.__("All in One Schema.org Rich Snippets - Dashboard","rich-snippets").'</h2>';
	echo '<div id="post-body" class="columns-2">';
	echo '<div class="clear"></div>';
	echo '<div id="bsf-postbox-container-2" class="postbox-container"><div id="tab-container" class="tab-container">';
	echo '<ul class="nav-tab-wrapper bsf-tab-wraper">
			<li><a href="#tab-1" class="nav-tab">'.__("Configuration","rich-snippets").'</a></li>
			<li><a href="#tab-4" class="nav-tab">'.__("Customization","rich-snippets").'</a></li>
			
			<li><a href="#tab-3" class="nav-tab">'.__("FAQs","rich-snippets").'</a></li>
			<li><a href="#tab-5" class="nav-tab">'.__("Getting Started","rich-snippets").'</a></li>
		 </ul>
		 <div class="clear"></div>
		 <div class="panel-container bsf-panel">
			 <div id="tab-1">
				<div id="poststuff">
					<div class="schema-notice-head">
					<p>'.__("Choose the schema markup and update the strings you want to display on the front-end.", "rich-snippets").'</p>
					</div>
					<div id="postbox-container-2" class="postbox-container">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox closed">
								<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
								<h3 class="hndle"><span>'.__("Item Review","rich-snippets").'</span></h3>
								<div class="inside">
									<div class="table">
										<p>'.__("Strings to be displayed on frontend for <strong>Item Review Rich Snippets &mdash;</strong>","rich-snippets").'</p>
										<form id="bsf_review_form" method="post">
										'.wp_nonce_field( 'snippet_review_form_action', 'snippet_review_nonce_field' ).'
											<table class="bsf_metabox">
												<tbody>
													<tr>
														<td align="right"><strong><label>'.__("Rich Snippet Title :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="review_title" value="'.stripslashes( $args_review["review_title"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Reviewer :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="item_reviewer" value="'.stripslashes( $args_review["item_reviewer"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Review Date :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="review_date" value="'.stripslashes( $args_review["review_date"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Item Name :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="item_name" value="'.stripslashes( $args_review["item_name"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Item Ratings :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="item_rating" value="'.stripslashes( $args_review["item_rating"] ).'"/></td>
													</tr>
													<tr><td colspan="2"></td></tr>
													<tr>
														<td></td>
														<td><input type="submit" class="button-primary" name="item_submit" value="'.__("Update ").'"/>&nbsp;&nbsp;&nbsp;<a class="button-primary" href="?page=rich_snippet_dashboard&amp;action=reset&options=review">'.__('Reset ','rich-snippets').'</a></td>
													</tr>
												</tbody>
											</table>
										</form>
									</div>
								</div>
							</div>
							<div class="postbox closed">
								<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
								<h3 class="hndle"><span>'.__("Events","rich-snippets").'</span></h3>
								<div class="inside">
									<div class="table">
										<p>'.__("Strings to be displayed on frontend for <strong>Events Rich Snippets &mdash;</strong>","rich-snippets").'</p>
										<form id="bsf_event_form" method="post">
										'.wp_nonce_field( 'snippet_event_form_action', 'snippet_event_nonce_field' ).'
											<table class="bsf_metabox">
												<tbody>
													<tr>
														<td align="right"><strong><label>'.__("Rich Snippet Title :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="snippet_title" value="'.stripslashes( $args_event["snippet_title"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Event Title :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="event_title" value="'.stripslashes( $args_event["event_title"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Event Location :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="event_location" value="'.stripslashes( $args_event["event_location"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Start Time :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="start_time" value="'.stripslashes( $args_event["start_time"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("End Time :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="end_time" value="'.stripslashes( $args_event["end_time"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Description :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="event_desc" value="'.stripslashes( $event_desc ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Ticket Promotion :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="events_price" value="'.stripslashes( $args_event["events_price"] ).'"/></td>
													</tr>
													<tr><td colspan="2"></td></tr>
													<tr>
														<td></td>
														<td><input type="submit" class="button-primary" name="event_submit" value="'.__("Update ").'"/>&nbsp;&nbsp;&nbsp;<a class="button-primary" href="?page=rich_snippet_dashboard&amp;action=reset&options=event">'.__('Reset ','rich-snippets').'</a></td>
													</tr>
												</tbody>
											</table>
										</form>
									</div>
								</div>
							</div>
							<div class="postbox closed">
								<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
								<h3 class="hndle"><span>'.__("Person","rich-snippets").'</span></h3>
								<div class="inside">
									<div class="table">								
										<p>'.__("Strings to be displayed on frontend for <strong>Person's Rich Snippets &mdash;</strong>","rich-snippets").'</p>
										<form id="bsf_person_form" method="post">
										'.wp_nonce_field( 'snippet_person_form_action', 'snippet_person_nonce_field' ).'
											<table class="bsf_metabox">
												<tbody>
													<tr>
														<td align="right"><strong><label>'.__("Rich Snippet Title :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="snippet_title" value="'.stripslashes( $args_person["snippet_title"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Person's Name :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="person_name" value="'.stripslashes( $args_person["person_name"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Nickname :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="person_nickname" value="'.stripslashes( $args_person["person_nickname"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Job Title :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="person_job_title" value="'.stripslashes( $args_person["person_job_title"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Homepage :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="person_website" value="'.stripslashes( $args_person["person_website"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Company Name :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="person_company" value="'.stripslashes( $args_person["person_company"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Address :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="person_address" value="'.stripslashes( $args_person["person_address"] ).'"/></td>
													</tr>
													<tr><td colspan="2"></td></tr>
													<tr>
														<td></td>
														<td><input type="submit" class="button-primary" name="person_submit" value="'.__("Update ").'"/>&nbsp;&nbsp;&nbsp;<a class="button-primary" href="?page=rich_snippet_dashboard&amp;action=reset&options=person">'.__('Reset ','rich-snippets').'</a></td>
													</tr>
												</tbody>
											</table>
										</form>
									</div>
								</div>
							</div>
							<div class="postbox closed">
								<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
								<h3 class="hndle"><span>'.__("Product","rich-snippets").'</span></h3>
								<div class="inside">
									<div class="table">								
										<p>'.__("Strings to be displayed on frontend for <strong>Product Rich Snippets &mdash;</strong>","rich-snippets").'</p>
										<form id="bsf_product_form" method="post">
										'.wp_nonce_field( 'snippet_product_form_action', 'snippet_product_nonce_field' ).'
											<table class="bsf_metabox">
												<tbody>
													<tr>
														<td align="right"><strong><label>'.__("Rich Snippet Title :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="snippet_title" value="'.stripslashes( $args_product["snippet_title"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Author Rating :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="product_rating" value="'.stripslashes( $args_product["product_rating"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Brand Name :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="product_brand" value="'.stripslashes( $args_product["product_brand"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Product Name :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="product_name" value="'.stripslashes( $args_product["product_name"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("User Rating :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="product_agr" value="'.stripslashes( $args_product["product_agr"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Price :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="product_price" value="'.stripslashes( $args_product["product_price"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Product Availability :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="product_avail" value="'.stripslashes( $args_product["product_avail"] ).'"/></td>
													</tr>
													<tr><td colspan="2"></td></tr>
													<tr>
														<td></td>
														<td><input type="submit" class="button-primary" name="product_submit" value="'.__("Update ").'"/>&nbsp;&nbsp;&nbsp;<a class="button-primary" href="?page=rich_snippet_dashboard&amp;action=reset&options=product">'.__('Reset ','rich-snippets').'</a></td>
													</tr>
												</tbody>
											</table>
										</form>
									</div>
								</div>
							</div>
							<div class="postbox closed">
								<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
								<h3 class="hndle"><span>'.__("Recipe","rich-snippets").'</span></h3>
								<div class="inside">
									<div class="table">								
										<p>'.__("Strings to be displayed on frontend for <strong>Recipe Rich Snippets &mdash;</strong>","rich-snippets").'</p>
										<form id="bsf_recipe_form" method="post">
										'.wp_nonce_field( 'snippet_recipe_form_action', 'snippet_recipe_nonce_field' ).'
											<table class="bsf_metabox">
												<tbody>
													<tr>
														<td align="right"><strong><label>'.__("Rich Snippet Title :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="snippet_title" value="'.stripslashes( $args_recipe["snippet_title"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Recipe Name :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="recipe_name" value="'.stripslashes( $args_recipe["recipe_name"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Author Name :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="author_name" value="'.stripslashes( $author_name ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Published On : ","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="recipe_pub" value="'.stripslashes( $args_recipe["recipe_pub"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Preparation Time:","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="recipe_prep" value="'.stripslashes( $args_recipe["recipe_prep"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Cook Time :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="recipe_cook" value="'.stripslashes( $args_recipe["recipe_cook"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Total Time :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="recipe_time" value="'.stripslashes( $args_recipe["recipe_time"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Description :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="recipe_desc" value="'.stripslashes( $recipe_desc ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Average Rating:","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="recipe_rating" value="'.stripslashes( $args_recipe["recipe_rating"] ).'"/></td>
													</tr>
													<tr><td colspan="2"></td></tr>
													<tr>
														<td></td>
														<td><input type="submit" class="button-primary" name="recipe_submit" value="'.__("Update ").'"/>&nbsp;&nbsp;&nbsp;<a class="button-primary" href="?page=rich_snippet_dashboard&amp;action=reset&options=recipe">'.__('Reset ','rich-snippets').'</a></td>
													</tr>
												</tbody>
											</table>
										</form>
									</div>
								</div>
							</div>
							<div class="postbox closed">
								<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
								<h3 class="hndle"><span>'.__("Software Application","rich-snippets").'</span></h3>
								<div class="inside">
									<div class="table">								
										<p>'.__("Strings to be displayed on frontend for <strong>Software Application Rich Snippets &mdash;</strong>","rich-snippets").'</p>
										<form id="bsf_software_form" method="post">
										'.wp_nonce_field( 'snippet_soft_app_form_action', 'snippet_soft_app_nonce_field' ).'
											<table class="bsf_metabox">
												<tbody>
													<tr>
														<td align="right"><strong><label>'.__("Rich Snippet Title :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="snippet_title" value="'.stripslashes( $args_soft["snippet_title"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Author Rating :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="software_rating" value="'.stripslashes( $args_soft["software_rating"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("User Rating :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="software_agr" value="'.stripslashes( $args_soft["software_agr"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Software Price :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="software_price" value="'.stripslashes( $args_soft["software_price"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Software Name:","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="software_name" value="'.stripslashes( $args_soft["software_name"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Operating System :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="software_os" value="'.stripslashes( $args_soft["software_os"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Landing Page:","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="software_website" value="'.stripslashes( $args_soft["software_website"] ).'"/></td>
													</tr>
													<tr><td colspan="2"></td></tr>
													<tr>
														<td></td>
														<td><input type="submit" class="button-primary" name="software_submit" value="'.__("Update ").'"/>&nbsp;&nbsp;&nbsp;<a class="button-primary" href="?page=rich_snippet_dashboard&amp;action=reset&options=software">'.__('Reset ','rich-snippets').'</a></td>
													</tr>
												</tbody>
											</table>
										</form>
									</div>
								</div>
							</div>
							
							
							<div class="postbox closed">
								<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
								<h3 class="hndle"><span>'.__("Video","rich-snippets").'</span></h3>
								<div class="inside">
									<div class="table">								
										<p>'.__("Strings to be displayed on frontend for <strong>Video Rich Snippets &mdash;</strong>","rich-snippets").'</p>
										<form id="bsf_video_form" method="post">
										'.wp_nonce_field( 'snippet_video_form_action', 'snippet_video_nonce_field' ).'
											<table class="bsf_metabox">
												<tbody>
													<tr>
														<td align="right"><strong><label>'.__("Rich Snippet Title :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="snippet_title" value="'.stripslashes( $args_video["snippet_title"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Video Title :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="video_title" value="'.stripslashes( $args_video["video_title"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Description :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="video_desc" value="'.stripslashes( $args_video["video_desc"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Video Duration :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="video_time" value="'.stripslashes( $args_video["video_time"] ).'"/></td>
													</tr>													
													<tr>
														<td align="right"><strong><label>'.__("Video Upload Date :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="video_date" value="'.stripslashes( $args_video["video_date"] ).'"/></td>
													</tr>
													<tr><td colspan="2"></td></tr>
													<tr>
														<td></td>
														<td><input type="submit" class="button-primary" name="video_submit" value="'.__("Update ").'"/>&nbsp;&nbsp;&nbsp;<a class="button-primary" href="?page=rich_snippet_dashboard&amp;action=reset&options=video">'.__('Reset ','rich-snippets').'</a></td>
													</tr>
												</tbody>
											</table>
										</form>
									</div>
								</div>
							</div>
							<div class="postbox closed">
								<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
								<h3 class="hndle"><span>'.__("Article","rich-snippets").'</span></h3>
								<div class="inside">
									<div class="table">								
										<p>'.__("Strings to be displayed on frontend for <strong>Article Rich Snippets &mdash;</strong>","rich-snippets").'</p>
										<form id="bsf_article_form" method="post">
										'.wp_nonce_field( 'snippet_article_form_action', 'snippet_article_nonce_field' ).'
											<table class="bsf_metabox">
												<tbody>
													<tr>
														<td align="right"><strong><label>'.__("Rich Snippet Title :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="snippet_title" value="'.stripslashes( $args_article["snippet_title"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Article Name :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="article_name" value="'.stripslashes( $args_article["article_name"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Author :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="article_author" value="'.stripslashes( $args_article["article_author"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Description :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="article_desc" value="'.stripslashes( $args_article["article_desc"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Image :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="article_image" value="'.stripslashes( $args_article["article_image"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Publisher :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="article_publisher" value="'.stripslashes( $args_article["article_publisher"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Publisher Logo :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="article_publisher_logo" value="'.stripslashes( $args_article["article_publisher_logo"] ).'"/></td>
													</tr>
													<tr><td colspan="2"></td></tr>
													<tr>
														<td></td>
														<td><input type="submit" class="button-primary" name="article_submit" value="'.__("Update ").'"/>&nbsp;&nbsp;&nbsp;<a class="button-primary" href="?page=rich_snippet_dashboard&amp;action=reset&options=article">'.__('Reset ','rich-snippets').'</a></td>
													</tr>
												</tbody>
											</table>
										</form>
									</div>
								</div>
							</div>
							<div class="postbox closed">
								<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
								<h3 class="hndle"><span>'.__("Service","rich-snippets").'</span></h3>
								<div class="inside">
									<div class="table">								
										<p>'.__("Strings to be displayed on frontend for <strong>Service Rich Snippets &mdash;</strong>","rich-snippets").'</p>
										<form id="bsf_service_form" method="post">
										'.wp_nonce_field( 'snippet_service_form_action', 'snippet_service_nonce_field' ).'
											<table class="bsf_metabox">
												<tbody>
													<tr>
														<td align="right"><strong><label>'.__("Rich Snippet Title :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="snippet_title" value="'.stripslashes( $args_service["snippet_title"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Service Type :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="service_type" value="'.stripslashes( $args_service["service_type"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Area :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="service_area" value="'.stripslashes( $args_service["service_area"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Description :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="service_desc" value="'.stripslashes( $args_service["service_desc"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Provider Name :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="service_provider_name" value="'.stripslashes( $args_service["service_provider_name"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Provider Location :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="provider_location" value="'.stripslashes( $provider_location ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("URL :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="service_channel" value="'.stripslashes( $args_service["service_channel"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("URL Text :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="service_url_link" value="'.stripslashes( $args_service["service_url_link"] ).'"/></td>
													</tr>
													<tr>
														<td align="right"><strong><label>'.__("Service Rating :","rich-snippets").'</label></strong></td>
														<td><input class="bsf_text_medium" type="text" name="service_rating" value="'.stripslashes( $args_service["service_rating"] ).'"/></td>
													</tr>
													<tr><td colspan="2"></td></tr>
													<tr>
														<td></td>
														<td><input type="submit" class="button-primary" name="service_submit" value="'.__("Update ").'"/>&nbsp;&nbsp;&nbsp;<a class="button-primary" href="?page=rich_snippet_dashboard&amp;action=reset&options=service">'.__('Reset ','rich-snippets').'</a></td>
													</tr>
												</tbody>
											</table>
										</form>
									</div>
								</div>
							</div>
						<!-- Post blox -->		
							
						</div>
					<div class="schema-notice">
						<p>'.__("Need some more schema types with automation to implement schema markup? Get the latest and premium schema markup plugin to automate the process of adding schema markup on your entire website. <br><a href='https://wpschema.com/?utm_source=allinone&utm_campaign=repo&utm_medium=configure' target='_blank'> Know more about Schema Pro.</a>", "rich-snippets").'</p>
					</div>
					</div>
				</div>	
			 </div>
	
			 
			 <div id="tab-5">
				<div id="poststuff">
					<div id="postbox-container-17" class="postbox-container">
						<div class="meta-box-sortables ui-sortable bsf-even-even">
							<div class="bsf-postbox">
								<h3 class="bsf-hndle" style="margin-top:0;"><span>'.__("Welcome to All In One Schema Rich Snippets","rich-snippets").'</span></h3>
								<div class="inside">
									<p>Thank you for choosing All-in-one Schema Rich Snippets - the most popular WordPress schema markup plugin!</p>

										<p>All-in-one Schema Rich Snippets helps you add different schema content types to your site so that you can communicate precise information about your web pages to search engines and get rich snippets.</p>
										<div class="bsf-xs-separator"></div>
										<h3>Supported types of Schemas:</h3>
										<ul class="schema-types">
											 <div class="schema-type-col-2">
												<li>Review</li>
												<li>Event</li>
												<li>Services</li>
											 </div>
											<div class="schema-type-col-2">
												<li>Person</li>
												<li>Product</li>
											</div>
											<div class="schema-type-col-2">
												<li>Video</li>
												<li>Articles</li>
											</div>
											<div class="schema-type-col-2">
												<li>Recipe</li>
												<li>Software Application</li>
											</div>
										</ul>
										<div class="bsf-xs-separator"></div>
								</div>
							</div>
						</div>
						<div class="meta-box-sortables ui-sortable bsf-even-odd">
									<h3 class="bsf-hndle"><span>'.__("How it works","rich-snippets").'</span></h3>
									<div class="inside">
										<ol class="bsf-li-counter">
											<li>'.__("Configure The Settings","rich-snippets").'
											<p>'.__("Go to the “Rich Snippets” option in your WordPress dashboard. Under the Configuration tab, select your desired schema type and update the strings you want to display on the front-end. You can use the Customization tab to manage how your rich snippet content box will look.","rich-snippets").'</p></li>
											<li>'.__("Add Markup To Pages","rich-snippets").'<p>'.__("Edit the posts or pages where you wish to add rich snippets and scroll down to the “Configure Rich Snippet” meta box to add schema markup.","rich-snippets").'</p></li>
											<li>'.__("Test Your Rich Snippets","rich-snippets").'<p>'.__("Google Structured Data Testing is a widely used online tool to test structured data. Open the <a href='https://search.google.com/structured-data/testing-tool/u/0/' target='_blank'>Google Structured Testing Tool</a> and fetch your website URL to test the schema markup you’ve just implemented on your webpages.","rich-snippets").'</p></li>
										</ol>
									</div>
						</div>
						<div class="meta-box-sortables ui-sortable bsf-even-even">
									<h3 class="bsf-hndle"><span>'.__("Want to Automate Your Schema Markup?","rich-snippets").'</span></h3>
									<div class="inside">
										<h3>'.__("Consider Schema Pro","rich-snippets").'</h3>
										<p>'.__("Schema Pro is an advanced schema markup plugin that automates the process of adding schema markup on multiple pages with just a few clicks. Schema Pro uses  JSON-LD markup, which is the latest technology recommended by Google. With it, you can kick those front-end content boxes to the curb and<b> get rich snippets without displaying any new human-readable content</b> on your site.","rich-snippets").'</p>
										<h3></h3>
										<div class="bsf-schema-desc">
											<div class="bsf-schema-features-1">
												<div class="bsf-schema-features-wrap">
													<div class="bsf-schema-features-icon">
													<img src="'.plugins_url("/all-in-one-schemaorg-rich-snippets/images/click.png").'"/>
													</div>
													<div class="bsf-schema-features-cont">
													<h3>Schema Markup Automation</h3>
													<p>Schema Pro automates the process of adding schema markup on your website. Just configure your markup one time and you can easily apply it to hundreds or thousands of pages.</p>
													</div>
												</div>
											</div>
											<div class="bsf-schema-features-1">
												<div class="bsf-schema-features-wrap">
													<div class="bsf-schema-features-icon">
													<img src="'.plugins_url("/all-in-one-schemaorg-rich-snippets/images/seo.png").'"/>
													</div>
													<div class="bsf-schema-features-cont">
													<h3>Complete Schema Implementation</h3>
													<p>Schema Pro gives you the full benefits of schema markup with both organization level and content type schemas. Implement organization level markup site-wide and content type on specific pages.</p>
													</div>
												</div>
											</div>
											<div class="bsf-schema-features-1">
												<div class="bsf-schema-features-wrap">
													<div class="bsf-schema-features-icon">
													<img src="'.plugins_url("/all-in-one-schemaorg-rich-snippets/images/website.png").'"/>
													</div>
													<div class="bsf-schema-features-cont">
													<h3>Advanced Targeting Rules</h3>
													<p>Schema Pro lets you use pinpoint inclusion/exclusion rules to apply different schema content types on both a post type or individual post level.</p>
													</div>
												</div>
											</div>
											<div class="bsf-schema-features-1">
												<div class="bsf-schema-features-wrap">
													<div class="bsf-schema-features-icon">
													<img src="'.plugins_url("/all-in-one-schemaorg-rich-snippets/images/custom.png").'"/>
													</div>
													<div class="bsf-schema-features-cont">
													<h3>Custom Field Support<h3>
													<p>Schema Pro comes with all the necessary fields for each content type, as well as support for your own custom fields. It lets you map existing custom fields or create new ones to suit your needs.</p>
													</div>
												</div>
											</div>
											<div class="bsf-schema-features-1">
												<div class="bsf-schema-features-wrap">
													<div class="bsf-schema-features-icon">
													<img src="'.plugins_url("/all-in-one-schemaorg-rich-snippets/images/quick.png").'"/>
													</div>
													<div class="bsf-schema-features-cont">
													<h3>Accuracy and Testing</h3>
													<p>Schema Pro lets you implement accurate markup and analyse your schema implementation instantly so you can rest assured that you’ve implemented it right.</p>
													</div>
												</div>
											</div>
											<div class="bsf-schema-features-1">
												<div class="bsf-schema-features-wrap">
													<div class="bsf-schema-features-icon">
													<img src="'.plugins_url("/all-in-one-schemaorg-rich-snippets/images/acf.png").'"/>
													</div>
													<div class="bsf-schema-features-cont">
													<h3>Compatibility with Yoast SEO,  ACF, PODS </h3>
													<p>Schema Pro is compatible with popular third-party plugins. It can inherit the settings from Yoast SEO and fetch custom fields that you&#39;ve created using the ACF or PODS plugins.</p>
													</div>
												</div>
											</div>
										</div>
									</div>
						</div>
						<div class="meta-box-sortables ui-sortable bsf-even-odd">
									<h3 class="bsf-hndle"><span>'.__("With Schema Pro, you can…","rich-snippets").'</span></h3>
									<div class="inside">
										<ol class="bsf-li-counter">
											<li>Automate schema markup for your entire website.</li>
											<li>Implement schema markup faster and more accurately.</li>
											<li>Target different post types with different Schema types.</li>
										</ol>
									</div>
						</div>
						<div class="meta-box-sortables ui-sortable bsf-even-even">
									<h3 class="bsf-hndle"><span>'.__("Testimonials","rich-snippets").'</span></h3>
									<div class="inside">
										<div class="bsf-schema">
											<div class="bsf-testimonial-wrap">
												<div class="bsf-schema-features-wrap">
													<div class="bsf-schema-testimonial-icon">
													<img src="'.plugins_url("/all-in-one-schemaorg-rich-snippets/images/adam-circle.jpg").'"/>
													</div>
													<div class="bsf-schema-features-cont">
													<p>I have used every Schema Plugin for WordPress over the last few years, hundreds of dollars invested, and Schema Pro blows them all out of the water. It’s the only schema plugin you need.</p>
													<b>Adam Preiser,</b> <span>Founder of WPCrafter.com</span>
													</div>
												</div>
											</div>
											<div class="bsf-ls-separator"></div>
											<div class="bsf-testimonial-wrap">
												<div class="bsf-schema-features-wrap">
													<div class="bsf-schema-testimonial-icon">
													<img src="'.plugins_url("/all-in-one-schemaorg-rich-snippets/images/kylevan.png").'"/>
													</div>
													<div class="bsf-schema-features-cont">
													<p>Schema Pro has unlocked a powerful set of tools that produced results almost immediately. As a non-coder, a solution like this allows me to set up and stand out against the competition- and it couldn&#39;t be any easier to use!</p>
													<b>Kyle Van Deusen,</b> <span> Owner at ogalweb.com</span>
													</div>
												</div>
											</div>
										</div>
									</div>
						</div>
						<div class="meta-box-sortables ui-sortable bsf-even-odd testimonial-wraper">
									<div class="inside">
										<div class="bsf-schema">
											<div class="bsf-schema-button-wrap">
												<a href="https://wpschema.com/pricing/?utm_source=allinone&utm_campaign=repo&utm_medium=welcome" target="_blank" class="bsf-btn bsf-btn-lg btn-btn-purple">Get Schema Pro</a>
											</div>
											<div class="bsf-schema-button-wrap">
												<a href="https://wpschema.com/?utm_source=allinone&utm_campaign=repo&utm_medium=welcome" target="_blank" class="bsf-btn bsf-btn-lg btn-btn-grey">See All Features</a>
											</div>
										</div>
									</div>
						</div>
					</div>
				</div>
			</div>
	
			 <div id="tab-3">
				<div id="poststuff">
					<div id="postbox-container-5" class="postbox-container">
						<div class="meta-box-sortables ui-sortable">
								<div class="postbox closed">
									<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
										<h3 class="hndle">'.__("Where can I test my schema markup implementation?","rich-snippets").'</h3>
										<div class="inside">
										<p>'.__("You can use the standard Google Structured Data Testing Tool to test your schema markup implementation. You can also take a look at the preview of how your search result might look.","rich-snippets").' <a href="http://www.google.com/webmasters/tools/richsnippets" target="_blank">Click Here.</a></p>
										</div>
								</div>
								<div class="postbox closed">
									<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
										<h3 class="hndle">'.__("Do I have to fill in all the details?","rich-snippets").'</h3>
										<div class="inside">
										<p>'.__("No. But, every schema type has some fields that HAVE to be filled as stated by Google. Therefore, it is recommended to fill these required fields in the schema markup you are implementing.","rich-snippets").'</p>
										</div>
								</div>
								<div class="postbox closed">
									<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
										<h3 class="hndle">'.__("Why does the plugin create extra content in the frontend? Can I hide it?","rich-snippets").'</h3>
										<div class="inside">
										<p>We understand that you don&#39;t like the content that gets displayed on your page / post. However, as per the strong recommendation of Google, MicroData should be clearly visible to the user.</p>
										<p>Here is a reference link of what Google says. <a href="https://sites.google.com/site/webmasterhelpforum/en/faq-rich-snippets#display" target="_blank"> https://sites.google.com/site/webmasterhelpforum/en/faq-rich-snippets#display</a></p>
										<p> If you still do not want your schema markup to affect your frontend design, you can use <a href="https://wpschema.com/?utm_source=allinone&utm_campaign=repo&utm_medium=faq" target="_blank">Schema Pro</a> - our advanced Schema markup plugin that is built with the latest JSON- LD technology which does not require a content box to be displayed on the front-end.</p>
										</div>
								</div>
								<div class="postbox closed">
									<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
										<h3 class="hndle">'.__("Does the plugin work with other plugins like WordPress SEO, WooCommerce, etc?","rich-snippets").'</h3>
										<div class="inside">
										<p>Well, the plugin works perfectly with most of the other plugins as the only thing "All in One Schema.org Rich Snippets" does is - it gives you power to add Rich Snippets MicroData to your pages and posts easily. <br><br>If you come across a conflict with any other plugin, please do not hesitate to report an issue.</p>
										</div>
								</div>
								<div class="postbox closed">
									<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
										<h3 class="hndle">'.__("How long will it take to show up rich snippets for my search results?","rich-snippets").'</h3>
										<div class="inside">
										<p>We cannot assure the time it will take to display a rich snippet for your search results. This is completely dependent on when your website is crawled by the search engine. However, there are many more factors, such as your website authority that contribute to the time taken for your website to be crawled and a rich snippet displayed.</p>
										<p>If rich snippets are not appearing in your search results as of yet, most probably they might start appearing as soon as Google or other search engines find your website more authoritative.</p>
										<p>Meanwhile - you can validate and see the preview of your rich snippets on <a target="_blank" href="http://www.google.com/webmasters/tools/richsnippets">[ Google Structured Data Testing Tool ]</a> .</p>
										</div>
								</div>
								<div class="postbox closed">
									<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
										<h3 class="hndle">'.__("I don't see the feature I want. How can I get it?","rich-snippets").'</h3>
										<div class="inside">
										<p>'.__("<a href='https://wpschema.com/contact/' target='_blank'>Get in touch</a> with us to ask if this feature is in our development roadmap. If it is not in our roadmap, and if you still think this feature would make the plugin better, we have a couple of options for you:","rich-snippets").'</p>
										<ol>
											<li>'.__("Code the new feature if you are a developer and submit your code. If we include this feature in our releases, credits will be given to you.","rich-snippets").'</li>
											<li>'.__("Offer a sponsorship to get this feature done for all plugin users OR request a professional customisation service.","rich-snippets").'</li>
										</ol>
										</div>
								</div>
							</div>
						</div>
					</div>
				 </div>
				<!-- Tab 4-->
				<div id="tab-4">
					<div id="poststuff">
						<div id="postbox-container-11" class="postbox-container">
							<div class="meta-box-sortables ui-sortable">
								<div class="postbox">
									<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
										<h3 class="hndle">'.__("<span>Customize the look and feel of rich snippet box</span>","rich-snippets").'</h3>
										<div class="inside">
											<form id="bsf_css_editor" method="post" onsubmit="return false;" action="">
											'.wp_nonce_field( 'snippet_color_form_action', 'snippet_color_nonce_field' ).'
											<table class="bsf_metabox">
												<tr>
													<th> <label for="snippet_box_bg"> '.__('Box Background ', 'rich-snippets').' </label> </th>
													<td> <input type="text" name="snippet_box_bg" id="snippet_box_bg" value="'.stripslashes( $args_color["snippet_box_bg"] ).'"  class="snippet_box_bg" /> </td>
												</tr>
												<tr>
													<th> <label for="snippet_title_bg"> '.__('Title Background', 'rich-snippets').' </label> </th>
													<td> <input type="text" name="snippet_title_bg" id="snippet_title_bg" value="'.stripslashes( $args_color["snippet_title_bg"] ).'"  class="snippet_title_bg" /> </td>
												</tr>
												<tr>
													<th> <label for="snippet_border"> '.__('Border Color', 'rich-snippets').' </label> </th>
													<td> <input type="text" name="snippet_border" id="snippet_border" value="'.stripslashes( $args_color["snippet_border"] ).'"  class="snippet_border" /> </td>
												</tr>
												<tr>
													<th> <label for="snippet_title_color"> '.__('Title Color', 'rich-snippets').' </label> </th>
													<td> <input type="text" name="snippet_title_color" id="snippet_title_color" value="'.stripslashes( $args_color["snippet_title_color"] ).'"  class="snippet_title_color" /> </td>
												</tr>
												<tr>
													<th> <label for="snippet_box_color"> '.__('Snippet Text Color', 'rich-snippets').' </label> </th>
													<td> <input type="text" name="snippet_box_color" id="snippet_box_color" value="'.stripslashes( $args_color["snippet_box_color"] ).'"  class="snippet_box_color" /> </td>
												</tr>
												<tr>
													<td></td>
													<td><input id="submit_colors" class="button-primary" type="submit" value="Update Colors" />&nbsp;&nbsp;&nbsp;<a class="button-primary" href="?page=rich_snippet_dashboard&amp;action=reset&options=color">'.__('Reset ','rich-snippets').'</a></td>
												</tr>
											</table>
											</form>
										</div>
									</div>
									<div class="schema-notice">
										<p>'.__("Don&#39;t want to add a content box on the front-end? Get the latest and premium schema markup plugin that adds Google recommended JSON-LD structured data format without the content box. <a href='https://wpschema.com/?utm_source=allinone&utm_campaign=repo&utm_medium=customize' target='_blank'> Know more about Schema Pro.</a>", "rich-snippets").'</p>
									</div>
								</div>
							</div>
					</div>
				</div>
			</div>
						
		 </div>
		 </div>
		<div class="postbox-container" id="bsf-postbox-container-1" >
		<div id="side-sortables" class="meta-box-sortables ui-sortable">
		<div class="postbox bsf-woocommerce-setting closed">
			<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
			<h3 class="get_in_touch">'.__("WooCommerce Configuration","rich-snippets").'</h3>
			<div class="inside">
			<form id="bsf_css_editor" method="post" action="">
			'.wp_nonce_field( 'snippet_woocommerce_form_action', 'snippet_woocommerce_nonce_field' ).'
				<p> '.__( 'WooCommerce comes with Schema.org code by default and using our plugin on WooCommerce product pages does will add duplicate schema so it is not recommended. If you could still like to enable our plugin on WooCommerce products, please enable this option.', 'rich-snippets' ).' </p>
				<table class="bsf_metabox" > <input type="hidden" name="site_url" value="'.site_url().'" /> </p>
					<tr>
						<td>
							<input type="checkbox" name="woocommerce_option" id="woocommerce_option" value="1" '.$woo_setting.' />
							<label for="woocommerce_option">Enable schema on WooCommerce products</label>
						</td>
					</tr>
					<tr>
						<td>
							<input style="margin-top:10px;" type="submit" class="button-primary" name="setting_submit" value="'.__("Update ").'"/>
						</td>
					</tr>
				</table>
			</form>
			</div>
		</div>'.get_support(1).'
	</div>';
	echo '
<script src="'.plugin_dir_url( __FILE__ ).'js/jquery.easytabs.min.js'.'"></script>
<script src="'.plugin_dir_url( __FILE__ ).'js/jquery.hashchange.min.js'.'"></script>
<script language="javascript">
	jQuery("#tab-container").easytabs();
	jQuery("#postbox-container-1").css({"width":"87%","padding-right":"2%"});
	jQuery("#postbox-container-2").css("width","35%");
	jQuery("#postbox-container-3").css({"width":"87%","padding-right":"2%"});
	jQuery("#postbox-container-4").css("width","35%");
	jQuery("#postbox-container-5").css({"width":"87%","padding-right":"2%"});
	jQuery("#postbox-container-6").css("width","35%");
	jQuery("#postbox-container-7").css("width","35%");
	jQuery("#postbox-container-8").css("width","35%");
	jQuery("#postbox-container-9").css("width","35%");
	jQuery("#postbox-container-10").css("width","35%");
	jQuery("#postbox-container-11").css({"width":"87%","padding-right":"2%"});
	jQuery(".postbox h3").click( function() {
   		jQuery(jQuery(this).parent().get(0)).toggleClass("closed");
   	});
	jQuery(".handlediv").click( function() {
   		jQuery(jQuery(this).parent().get(0)).toggleClass("closed");
   	});
</script>';
}
// Update options

if(isset($_POST['setting_submit']))
{
	if ( ! isset( $_POST['snippet_woocommerce_nonce_field'] ) || ! wp_verify_nonce( $_POST['snippet_woocommerce_nonce_field'], 'snippet_woocommerce_form_action' ) 
		) {
		   print 'Sorry, your nonce did not verify.';
		   exit;
		} 
	else {
		$args = null;
		if(isset($_POST["woocommerce_option"])) 
		{
			$args = true;
		}	
		else {
			$args = false;
		}	
		update_option( 'bsf_woocom_init_setting', 'done' );
		$status = update_option('bsf_woocom_setting',$args);
		displayStatus($status);
	}
}
if(isset($_POST['item_submit']))
{
	if ( ! isset( $_POST['snippet_review_nonce_field'] ) || ! wp_verify_nonce( $_POST['snippet_review_nonce_field'], 'snippet_review_form_action' ) 
		) {
		   print 'Sorry, your nonce did not verify.';
		   exit;
		} 
	else {
		foreach(array('review_title','item_reviewer','review_date','item_name','item_rating') as $option)
		{
			if(isset($_POST[$option])) 
			{
				$args[$option] = esc_attr( $_POST[$option] );
			}		
		}
		$status = update_option('bsf_review',$args);
		displayStatus($status);
	}
}
if(isset($_POST['event_submit']))
{
	if ( ! isset( $_POST['snippet_event_nonce_field'] ) || ! wp_verify_nonce( $_POST['snippet_event_nonce_field'], 'snippet_event_form_action' ) 
		) {
		   print 'Sorry, your nonce did not verify.';
		   exit;
		} 
	else {
		foreach(array('snippet_title','event_title','event_location','event_performer','start_time','end_time','event_desc','events_price') as $option)
		{
			if(isset($_POST[$option])) 
			{
				$args[$option] = esc_attr( $_POST[$option] );
			}		
		}
		$status = update_option('bsf_event',$args);
		displayStatus($status);
	}
}
if(isset($_POST['person_submit']))
{
	if ( ! isset( $_POST['snippet_person_nonce_field'] ) || ! wp_verify_nonce( $_POST['snippet_person_nonce_field'], 'snippet_person_form_action' ) 
		) {
		   print 'Sorry, your nonce did not verify.';
		   exit;
		} 
	else {
		foreach(array('snippet_title','person_name','person_nickname','person_job_title','person_website','person_company','person_address') as $option)
		{
			if(isset($_POST[$option])) 
			{
				$args[$option] = esc_attr( $_POST[$option] );
			}		
		}
		$status = update_option('bsf_person',$args);
		displayStatus($status);
	}
}
if(isset($_POST['product_submit']))
{
	if ( ! isset( $_POST['snippet_product_nonce_field'] ) || ! wp_verify_nonce( $_POST['snippet_product_nonce_field'], 'snippet_product_form_action' ) 
		) {
		   print 'Sorry, your nonce did not verify.';
		   exit;
		} 
	else {
		foreach(array('snippet_title','product_rating','product_brand','product_name','product_agr','product_price','product_avail') as $option)
		{
			if(isset($_POST[$option])) 
			{
				$args[$option] = esc_attr( $_POST[$option] );
			}		
		}
		$status = update_option('bsf_product',$args);
		displayStatus($status);
	}
}
if(isset($_POST['recipe_submit']))
{
	if ( ! isset( $_POST['snippet_recipe_nonce_field'] ) || ! wp_verify_nonce( $_POST['snippet_recipe_nonce_field'], 'snippet_recipe_form_action' ) 
		) {
		   print 'Sorry, your nonce did not verify.';
		   exit;
		} 
	else {
		foreach(array('snippet_title','recipe_name','author_name','recipe_pub','recipe_prep','recipe_cook','recipe_time','recipe_desc','recipe_rating') as $option)
		{
			if(isset($_POST[$option])) 
			{
				$args[$option] = esc_attr( $_POST[$option] );
			}		
		}
		$status = update_option('bsf_recipe',$args);
		displayStatus($status);
	}
}
if(isset($_POST['software_submit']))
{
	if ( ! isset( $_POST['snippet_soft_app_nonce_field'] ) || ! wp_verify_nonce( $_POST['snippet_soft_app_nonce_field'], 'snippet_soft_app_form_action' ) 
		) {
		   print 'Sorry, your nonce did not verify.';
		   exit;
		} 
	else {
		foreach(array('snippet_title','software_rating','software_agr','software_price','software_name','software_os','software_website') as $option)
		{
			if(isset($_POST[$option])) 
			{
				$args[$option] = esc_attr( $_POST[$option] );
			}		
		}
		$status = update_option('bsf_software',$args);
		displayStatus($status);
	}
}
if(isset($_POST['video_submit']))
{
	if ( ! isset( $_POST['snippet_video_nonce_field'] ) || ! wp_verify_nonce( $_POST['snippet_video_nonce_field'], 'snippet_video_form_action' ) 
		) {
		   print 'Sorry, your nonce did not verify.';
		   exit;
		} 
	else {
		foreach(array('snippet_title','video_title','video_desc','video_time','video_date') as $option)
		{
			if(isset($_POST[$option])) 
			{
				$args[$option] = esc_attr( $_POST[$option] );
			}		
		}
		$status = update_option('bsf_video',$args);
		displayStatus($status);
	}
}
if(isset($_POST['article_submit']))
{
	if ( ! isset( $_POST['snippet_article_nonce_field'] ) || ! wp_verify_nonce( $_POST['snippet_article_nonce_field'], 'snippet_article_form_action' ) 
		) {
		   print 'Sorry, your nonce did not verify.';
		   exit;
		} 
	else {
		foreach(array('snippet_title','article_name','article_author','article_desc','article_image','article_publisher','article_publisher_logo') as $option)
		{
			if(isset($_POST[$option])) 
			{
				$args[$option] = esc_attr( $_POST[$option] );
			}		
		}
		$status = update_option('bsf_article',$args);
		displayStatus($status);
	}
}
if(isset($_POST['service_submit']))
{
	if ( ! isset( $_POST['snippet_service_nonce_field'] ) || ! wp_verify_nonce( $_POST['snippet_service_nonce_field'], 'snippet_service_form_action' ) 
		) {
		   print 'Sorry, your nonce did not verify.';
		   exit;
		} 
	else {
		foreach(array('snippet_title','service_type','service_area','service_desc','service_provider_name','provider_location','service_rating','service_channel','service_url_link') as $option)
		{
			if(isset($_POST[$option])) 
			{
				$args[$option] = esc_attr( $_POST[$option] );
			}		
		}
		$status = update_option('bsf_service',$args);
		displayStatus($status);
	}
}
function displayStatus($status) {
	if($status)
	{
		echo '<div class="updated"><p>' . __('Success! Your changes were successfully saved!', 'rich-snippets') . '</p></div>';
	} 
	else
	{
		echo '<div class="error"><p>' . __('Sorry, Your changes are not saved!', 'rich-snippets') . '</p></div>';
	}	
}
if(isset($_GET['action']))
{
	if(esc_attr( $_GET['action'] ) == 'reset' )
	{
		$option_to_reset = esc_attr( $_GET['options'] );
		if($option_to_reset == 'review')
			delete_option('bsf_review');
		if($option_to_reset == 'event')
			delete_option('bsf_event');
		if($option_to_reset == 'person')			
			delete_option('bsf_person');
			
		if($option_to_reset == 'product')
			delete_option('bsf_product');
		if($option_to_reset == 'recipe')			
			delete_option('bsf_recipe');
		if($option_to_reset == 'software')			
			delete_option('bsf_software');
		if($option_to_reset == 'video')
			delete_option('bsf_video');
		
		if($option_to_reset == 'article')
			delete_option('bsf_article');
		if($option_to_reset == 'service')
			delete_option('bsf_service');
		
		if($option_to_reset == 'color')
			delete_option('bsf_custom');
		
		bsf_reset_options($option_to_reset);
	}
}
function bsf_reset_options($option_to_reset)
{
	require_once(dirname(__FILE__) .'/../settings.php');
	if($option_to_reset == 'review')	
		add_review_option();
	if($option_to_reset == 'event')
		add_event_option();
	if($option_to_reset == 'person')
		add_person_option();
	if($option_to_reset == 'product')
		add_product_option();
	if($option_to_reset == 'recipe')
		add_recipe_option();
	if($option_to_reset == 'software')
		add_software_option();
	if($option_to_reset == 'video')
		add_video_option();
	if($option_to_reset == 'article')
		add_article_option();
	if($option_to_reset == 'service')
		add_service_option();
	if($option_to_reset == 'color')
		add_color_option();
	
	header("location:?page=rich_snippet_dashboard");
}
function add_footer_script()
{?>
	<script type="text/javascript">
        jQuery("#submit_colors").click(function()
        {
            var data = jQuery("#bsf_css_editor").serialize();
            var form_data = "action=bsf_submit_color&" + data;
          //alert(form_data);
            jQuery.post(ajaxurl, form_data,
                function (response) {
                    alert(response);
                }
            );
        });
	   jQuery("#support_form").submit(function()
        {
            var data = jQuery("#support_form").serialize();
            var form_data = "action=bsf_submit_request&" + data;
         // alert(form_data);
            jQuery.post(ajaxurl, form_data,
                function (response) {
                    alert(response);
					jQuery("#support_form .bsf_text_medium, #support_form .bsf_textarea_small").val("");
                }
            );
        });
    </script>
<?php }
function get_support()
{
	$html = '
		<div class="postbox bsf-contact closed">
			<button type="button" class="handlediv" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Frontend Options</span><span class="toggle-indicator" aria-hidden="true"></span></button>
			<h3 class="get_in_touch">'.__("Get in touch with the Plugin Developers","rich-snippets").'</h3>
			<div class="inside">
			<form name="support" id="support_form" action="" method="post" onsubmit="return false;">
				<p> '.__( 'Just fill out the form below and your message will be emailed to the Plugin Developers.', 'rich-snippets' ).' </p>
				<table class="bsf_metabox" > <input type="hidden" name="site_url" value="'.site_url().'" /> </p>
					<tr><td><label for="name"><strong>'.__( 'Your Name:', 'rich-snippets').'<span style="color:red;"> *</span></strong> </label></td>
						<td><input required="required" type="text" class="bsf_text_medium" name="name" /></td></tr>
					<tr><td><label for="email"><strong>'.__( 'Your Email:', 'rich-snippets').'<span style="color:red;"> *</span></strong> </label></td>
						<td><input required="required" type="text" class="bsf_text_medium" name="email" /></td></tr>
					<tr><td><label for="post_url"><strong>'.__( 'Reference URL:', 'rich-snippets').'<span style="color:red;"> *</span></strong> </label></td>
						<td><input required="required" type="text" class="bsf_text_medium" name="post_url" /></td></tr>
					<tr><td><label for="subject"><strong>'.__( 'Subject:', 'rich-snippets').'</strong> </label></td>
						<td>
							<select class="select_full" name="subject"> 
								<option value="question"> I have a question </option>
								<option value="bug"> I found a bug </option>
								<option value="help"> I need help </option>
								<option value="professional">  I need professional service </option>
								<option value="contribute"> I want to contribute my code</option>
								<option value="other">  Other </option>								
							</select>
						</td><td></td></tr>
					<tr><td class="bsf_label"><label for="message"><strong>'.__( 'Your Query in Brief:', 'rich-snippets').'</strong> </label></td>
						<td rowspan="4"><textarea class="bsf_textarea_small" name="message" required></textarea> </td></tr>
						<tr></tr> <tr></tr> <tr></tr>
					<tr><td></td>
						<td><input id="submit_request" class="button-primary" type="submit" value="Submit Request" /> <span id="status"></span></td></tr>
				</table>
			</form>
			</div>
		</div>
		</div>
		</div>
		</div>
	';
	return $html;
}
?>