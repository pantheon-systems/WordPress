<?php
/**
 * 
 */
if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
}

//if(!isset($_SESSION)) 
//    { 
//        session_start(); 
//    } 

class BngCustomTypes
{
	private $prefix;

	public function __construct($prefix)
	{
		$this->prefix = $prefix;

		// Register Post Types
		add_action( 'init', array($this, 'register_location_type') );

		add_action( 'admin_footer', array($this, 'css') );

		//Register Custom Metas
		add_action('cmb2_init',array($this,'bng_location_metaboxes') );
		//add_action( 'cmb2_render_text', array($this,'cmb2_render_callback_for_text'), 10, 5 );
		add_action( 'admin_menu' , array($this,'remove_post_custom_fields') );

		//Add Columns
		add_filter('manage_bng_type_location_posts_columns' , array($this,'location_cpt_columns'));
		add_filter("manage_edit-bng_type_location_sortable_columns", array($this,'location_sort'));
		add_action('manage_bng_type_location_posts_custom_column', array($this,'manage_location_columns'), 10, 2);

		add_filter('manage_post_posts_columns' , array($this,'location_cpt_columns_post'));
		add_filter("manage_edit-post_sortable_columns", array($this,'location_sort_post'));
		add_action('manage_post_posts_custom_column', array($this,'manage_location_columns_post'), 10, 2);

		add_filter('manage_page_posts_columns' , array($this,'location_cpt_columns_post'));
		add_filter("manage_edit-page_sortable_columns", array($this,'location_sort_post'));
		add_action('manage_page_posts_custom_column', array($this,'manage_location_columns_post'), 10, 2);


		//Script Ajax
		add_action( 'wp_enqueue_scripts', array($this , 'theme_enqueue_script') );
		add_action('wp_ajax_action_location_post', array($this,'action_location_post' ) );
		add_action('wp_ajax_nopriv_action_location_post', array($this,'action_location_post' ) );

		add_action('wp_ajax_delete_location_session', array($this,'delete_location_session' ) );
		add_action('wp_ajax_nopriv_delete_location_session', array($this,'delete_location_session' ) );

		//Delete Trash Default
		add_filter( 'post_row_actions', array($this,'remove_row_actions'), 10, 1 );
		add_filter( 'post_row_actions', array($this,'remove_row_actions_post_type'), 10, 1 );
		add_filter( 'get_sample_permalink_html', array($this,'wpse_125800_sample_permalink') );
		add_action( 'admin_head', array($this,'wpse_125800_custom_publish_box' ));

		/*add_action('restrict_manage_posts',array($this,'restrict_post_by_location_post'));
		add_filter( 'parse_query', array( $this, 'modify_filter_location_post' ) );*/

		add_action('restrict_manage_posts',array($this,'restrict_by_location'));
		add_filter( 'parse_query', array( $this, 'modify_filter_location' ) );
	}

	/*
		Register Post Type
	*/
	public function theme_enqueue_script() {
		wp_register_script( 'Location Script',  plugins_url('/assets/js/script.js', __DIR__),array ( 'jquery' ), 1.1, true );
		wp_localize_script( 'Location Script', 'bng_session', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script('Location Script');
	}
	

	public function register_location_type(){
		// Set UI labels for Custom Post Type
		$labels = array(
			'name'                => __( 'Location', 'bitsandgeeks' ),
			'singular_name'       => __( 'Location', 'bitsandgeeks' ),
			'menu_name'           => __( 'Qiigo Variables', 'bitsandgeeks' ),
			'parent_item_colon'   => __( 'Parent Location', 'bitsandgeeks' ),
			'all_items'           => __( 'All Locations ', 'bitsandgeeks' ),
			'view_item'           => __( 'View Location', 'bitsandgeeks' ),
			'add_new_item'        => __( 'New Location', 'bitsandgeeks' ),
			'add_new'             => __( 'New', 'bitsandgeeks' ),
			'edit_item'           => __( 'Edit Locations', 'bitsandgeeks' ),
			'update_item'         => __( 'Update Locations', 'bitsandgeeks' ),
			'search_items'        => __( 'Search Locations', 'bitsandgeeks' ),
			'not_found'           => __( 'Not Found', 'bitsandgeeks' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'bitsandgeeks' ),
		);
		
		// Set other options for Custom Post Type
		
		$args = array(
			'label'               => __( 'bng_type_location', 'bitsandgeeks' ),
			'description'         => __( 'Form Add Locations', 'bitsandgeeks' ),
			'labels'              => $labels,
			// Features this CPT supports in Post Editor
			'supports'            => array( ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'has_archive'         => true,
			'supports'            => array( 'title'),
			'rewrite'   => array( 'slug' => true),
	
		);
		
		// Registering your Custom Post Type 
		register_post_type( 'bng_type_location', $args );
	}
	public function bng_location_metaboxes(){
		$prefix = $this->prefix;
		$cmb_details = new_cmb2_box( array(
			'id'			=>	'bng_type_location',
			'title'			=>	__('Description','cmb2'),
			'object_types'	=>	array('bng_type_location'),
			'context'		=>	'normal',
			'priority'		=>	'high',
			'show_names'	=> true,
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Location Code','bitsandgeeks'),
			'id'			=>	'location_code',
			'description'   =>  '(required)',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				'required'		=>	'required',
				),
			));


		//
		$cmb_details->add_field(array(
			'name'			=>	__('Location Name','bitsandgeeks'),
			'id'			=>	'location_name',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));
		//


		$cmb_details->add_field(array(
			'name'			=>	__('Brand Assigned Code','bitsandgeeks'),
			'id'			=>	'brand_assigned_code',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Business License ID','bitsandgeeks'),
			'id'			=>	'business_license_id',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Formal Business Name','bitsandgeeks'),
			'id'			=>	'formal_business_name',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Business DBA','bitsandgeeks'),
			'id'			=>	'business_dba',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Address 1','bitsandgeeks'),
			'id'			=>	'address_1',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Address 2','bitsandgeeks'),
			'id'			=>	'address_2',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('City','bitsandgeeks'),
			'id'			=>	'city',
			'type'			=>	'text',
			'attributes'	=>	array(
				
				'style'			=>	'width:75%',
				),
			));
		$cmb_details->add_field(array(
			'name'			=>	__('State','bitsandgeeks'),
			'id'			=>	'state',
			'type'			=>	'text',
			'attributes'	=>	array(
				
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Zip','bitsandgeeks'),
			'id'			=>	'zip',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Primary Phone','cmb2'),
			'id'			=>	'primary_phone',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Tracking Phone','cmb2'),
			'id'			=>	'tracking_phone',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));
		
		$cmb_details->add_field(array(
			'name'			=>	__('Fax','cmb2'),
			'id'			=>	'fax',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));


		//
		$cmb_details->add_field(array(
			'name'			=>	__('Embed Google Map','cmb2'),
			'id'			=>	'google_map_embed',
			'type'			=>	'wysiwyg',
			'sanitization_cb' => false,
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('ZOPIM','bitsandgeeks'),
			'id'			=>	'zopim_code',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));
		//


		$cmb_details->add_field(array(
			'name'			=>	__('Facebook URL','cmb2'),
			'id'			=>	'facebook_url',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Google+ URL','cmb2'),
			'id'			=>	'google_url',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Youtube URL','cmb2'),
			'id'			=>	'youtube_url',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		//
		$cmb_details->add_field(array(
			'name'			=>	__('Linkedin URL','cmb2'),
			'id'			=>	'linkedin_url',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));
		//

		$cmb_details->add_field(array(
			'name'			=>	__('Pinterest URL','cmb2'),
			'id'			=>	'pinterest_url',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Twitter URL','cmb2'),
			'id'			=>	'twitter_url',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Instagram URL','cmb2'),
			'id'			=>	'instagram_url',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		$cmb_details->add_field(array(
			'name'			=>	__('Angieslist URL','cmb2'),
			'id'			=>	'angieslist_url',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));
		
		$cmb_details->add_field(array(
			'name'			=>	__('Location Path','bitsandgeeks'),
			'id'			=>	'location_path',
			'type'			=>	'text',
			'attributes'	=>	array(
				'style'			=>	'width:75%',
				),
			));

		for($i=1;$i<=5;$i++){
			$cmb_details->add_field(array(
				'name'			=>	__("Button {$i} URL",'bitsandgeeks'),
				'id'			=>	"button_{$i}_url",
				'type'			=>	'text',
				'attributes'	=>	array(
					'style'			=>	'width:75%',
					),
			));
		}

		for($i=1;$i<=10;$i++){
			$cmb_details->add_field(array(
				'name'			=>	__("Customvar{$i}",'bitsandgeeks'),
				'id'			=>	"customvar{$i}",
				'type'			=>	'text',
				'attributes'	=>	array(
					'style'			=>	'width:75%',
					),
			));	
		}
			
		/*
		
		$group_field_id = $cmb_details->add_field( array(
		    'id'          => 'group_fields',
		    'type'        => 'group',
		    'description' => __( 'Generates reusable form entries', 'bitsandgeeks' ),
		    // 'repeatable'  => false, // use false if you want non-repeatable group
		    'options'     => array(
		        'group_title'   => __( 'Attribute', 'bitsandgeeks' ), // since version 1.1.4, {#} gets replaced by row number
		        'add_button'    => __( 'Add Attribute', 'bitsandgeeks' ),
		        'remove_button' => __( 'Remove Attribute', 'bitsandgeeks' ),
		        'sortable'      => true, // beta
		        // 'closed'     => true, // true to have the groups closed by default
		    ),
		) );

		// Id's for group's fields only need to be unique for the group. Prefix is not needed.
		$cmb_details->add_group_field( $group_field_id, array(
		    'name' => 'Field ID',
		    'id'   => $prefix.'id_fields',
		    'type' => 'text',
		    // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
		) );

		$cmb_details->add_group_field( $group_field_id, array(
		    'name' => 'Field Value',
		    'id'   => $prefix.'value_fields',
		    'type' => 'text',
		) );*/

		$cmb_details_post = new_cmb2_box( array(
			'id'			=>	'post',
			'title'			=>	__('Description','cmb2'),
			'object_types'	=>	array('post','page'),
			'context'		=>	'normal',
			'priority'		=>	'high',
			'show_names'	=> true,
			));
		
		$cmb_details_post->add_field(array(
			'name'			=>	__('Location','cmb2'),
			'id'			=>	'location_post',
			'type'			=>	'select',
			'options'		=>	$this->get_locations_code(),
			'attributes'	=>	array(

				'style'			=>	'width:75%',
				),
			));
		/*$cmb_details_page = new_cmb2_box( array(
			'id'			=>	'page',
			'title'			=>	__('Description','cmb2'),
			'object_types'	=>	array('page'),
			'context'		=>	'normal',
			'priority'		=>	'high',
			'show_names'	=> true,
			));
		
		$cmb_details_page->add_field(array(
			'name'			=>	__('Location','cmb2'),
			'id'			=>	'location_post',
			'type'			=>	'text',
			'attributes'	=>	array(

				'style'			=>	'width:75%',
				),
			));*/

	}
	public function create_post_default(){
		// Create post object
		//is_null(get_page_by_title ('Default','OBJECT','post')) || is_null(get_page_by_title ('Default','OBJECT','page')) && 
		if ( is_null(get_page_by_title ('Default','OBJECT','bng_type_location')) ) {
			$my_post = array(
			  'post_title'    	=> 'Default',
			  'post_status'   	=> 'publish',
			  'post_author'   	=> 1,
			  'post_type' 		=> 'bng_type_location',
			);
			 
			// Insert the post into the database
			$post_id = wp_insert_post( $my_post );
			//Insert Meta key post
			add_post_meta($post_id, 'location_code', '');

			//
			add_post_meta($post_id, 'location_name', '');
			//

			add_post_meta($post_id, 'brand_assigned_code', '');
			add_post_meta($post_id, 'business_license_id', '');
			add_post_meta($post_id, 'formal_business_name', '');
			add_post_meta($post_id, 'business_dba', '');
			add_post_meta($post_id, 'address_1', '');
			add_post_meta($post_id, 'address_2', '');
			add_post_meta($post_id, 'city', '');
			add_post_meta($post_id, 'state', '');
			add_post_meta($post_id, 'zip', '');
			add_post_meta($post_id, 'primary_phone', '');
			add_post_meta($post_id, 'tracking_phone', '');
			add_post_meta($post_id, 'fax', '');

			//
			add_post_meta($post_id, 'google_map_embed', '');
			add_post_meta($post_id, 'zopim_code', '');
			//

			add_post_meta($post_id, 'facebook_url', '');
			add_post_meta($post_id, 'google_url', '');
			add_post_meta($post_id, 'youtube_url', '');

			//
			add_post_meta($post_id, 'linkedin_url', '');
			//

			add_post_meta($post_id, 'pinterest_url', '');
			add_post_meta($post_id, 'twitter_url', '');
			add_post_meta($post_id, 'instagram_url', '');
			add_post_meta($post_id, 'angieslist_url', '');
			add_post_meta($post_id, 'location_path', '');
			//add_post_meta($post_id, 'group_fields', '');
			for($i=1;$i<=5;$i++){
				add_post_meta($post_id, "button_{$i}_url", '');	
			}

			for($i=1;$i<=10;$i++){
				add_post_meta($post_id, "customvar{$i}", '');	
			}

			update_option('bng_location_default',$post_id);
		}
		
	
	}
	public function remove_post_custom_fields() {
		remove_meta_box( 'slugdiv', 'bng_type_location' ,'' );
	}
	
	// Remove Trash post
	public function remove_row_actions( $actions )
	{
	    global $post;
	    if( $post->ID == get_option('bng_location_default'))
	        unset( $actions['trash'] );
	        unset( $actions['inline hide-if-no-js'] );
	    return $actions;
	}
	//Save an option the Post Id Session
	public function action_location_post(){
		$_SESSION['location_save_session']=$_POST['bng_location_session'];
		update_option('bng_post_id',$_SESSION['location_save_session']);
		echo $_SESSION['location_save_session'];
		exit;
	}
	// Delete Session
	public function delete_location_session(){
		unset($_SESSION['location_save_session']);
		echo "delete";
		exit;
	}
	//Add Columns Post Type 
	public function location_cpt_columns($columns) {
		$new_columns = array(
			'id' => __('Post Id', 'bitsandgeeks'),
		);
	    return array_merge($columns, $new_columns);
	}
	public function location_cpt_columns_post($columns) {
		$new = array();
		foreach($columns as $key => $title) {
		    if ($key=='author') // Put the Location column before the Author column
		      $new['location'] = 'Location';
		    $new[$key] = $title;
		}
		return $new;
	}
	// Content column
	public function manage_location_columns($column_name, $id) {
		global $post;
		switch ($column_name) {
			case 'id':
				echo $post->ID ;
				break;
			default:
				break;
		} // end switch
	}
	public function manage_location_columns_post($column_name, $id) {
		global $post;
		switch ($column_name) {
			case 'location':
				$value = '-';
				if($location_post = get_post_meta($post->ID,'location_post', true)){
					$post = get_post($location_post);
					$value = $post->post_title;
				}
				echo $value;
				break;
			default:
				break;
		} // end switch
	}
	/*public function manage_location_columns_page($column_name, $id) {
		global $post;
		switch ($column_name) {
			case 'location':
				echo empty(get_post_meta($post->ID,'location_page'))? '-' : get_post_meta($post->ID,'location_page')[0] ;
				break;
			default:
				break;
		} // end switch
	}*/
	//Order Column 
	public function location_sort($columns) {
		$custom = array(
			'concertdate' 	=> 'concertdate',
			'id' 			=> 'id',
		);
		return wp_parse_args($custom, $columns);
	
	}
	public function location_sort_post($columns) {
		$custom = array(
			'concertdate' 	=> 'concertdate',
			'location' 			=> 'location',
		);
		return wp_parse_args($custom, $columns);

	}
	//Remove view posts
	public function remove_row_actions_post_type( $actions )
	{
	    if( get_post_type() === 'bng_type_location' )
	        unset( $actions['view'] );
	    return $actions;
	}
	public function myplugin_overview_post_title( $title, $postid ) {
		global $pagenow;

		$post_type = 'bng_type_location';

		// Only use the ID in the overview for the specified custom post type
		if ( $pagenow == 'edit.php' && ! empty( $_GET['post_type'] ) && $_GET['post_type'] == $post_type ) {
			return $postid;
		}

		return $title;
	}
	public function css(){
		?>
		<style>
			#cb-select-<?php echo get_option('bng_location_default');?> {
				display: none;
			}
		</style>
		<script>
			jQuery(document).ready(function($) {
				var id = $('#post_ID').val();
				if (id == <?php echo get_option('bng_location_default');?> ) {
					$('#delete-action').hide();
				}
			})
		</script>
		<?php
	}
	
    public function wpse_125800_custom_publish_box() {
        
		 if (get_post_type()==='bng_type_location') {

	        $style = '';
	        $style .= '<style type="text/css">';
	        $style .= '#edit-slug-box, #minor-publishing-actions, #visibility, .num-revisions, .curtime, #message';
	        $style .= '{display: none; }';
	        $style .= '</style>';

	        echo $style;
    	}
    }
    //remove permalink
	public function wpse_125800_sample_permalink( $return ) {
	    
	    if (get_post_type()==='bng_type_location') {
	    	$return = '';
	    }
	    return $return;
	}
	public function uninstall_unset_session(){
		unset($_SESSION['location_save_session']);
		$id=get_option('bng_location_default');
		wp_delete_post( $id );
	}
	
	/*public function restrict_post_by_location_post() {
	    global $typenow;
	    global $wp_query;
	    if ($typenow=='post') {	       
	        $search_location = (isset($_GET['search_location_post'])) ? $_GET['search_location_post'] : "";
	        echo "<input type='text' id='search_location_post' name='search_location_post' value='".$search_location."'>";
	    }
	}

	public function modify_filter_location_post( $query ) {
	    global $typenow;
	    global $pagenow;

	    if( $pagenow == 'edit.php' && $typenow == 'post' && $_GET['search_location_post'] ) {
	        $query->query_vars[ 'meta_key' ] = 'location_post';
	        $query->query_vars[ 'meta_value' ] = $_GET['search_location_post'];
	        $query->query_vars[ 'meta_compare' ] = 'LIKE';
	    }
	}*/
	private function get_locations_title(){
		remove_filter( 'parse_query', array( $this, 'modify_filter_location' ) );
	    $posts = get_posts(array('post_type' => 'bng_type_location','posts_per_page' => -1));
	    $locations = array();
	    foreach ($posts as $post) {
	    	$locations[$post->ID] = $post->post_title;
		}
	    add_filter( 'parse_query', array( $this, 'modify_filter_location' ) );
	    return $locations;
	}

	private function get_locations_code(){
		remove_filter( 'parse_query', array( $this, 'modify_filter_location' ) );
	    $posts = get_posts(array('post_type' => 'bng_type_location','posts_per_page' => -1));
	    $locations = array();
	    foreach ($posts as $post) {
	    	if($code = get_post_meta($post->ID, 'location_code', true)) {
	    		$locations[$post->ID] = $code;
	    	}
		}
	    add_filter( 'parse_query', array( $this, 'modify_filter_location' ) );
	    return $locations;
	}

	public function restrict_by_location() {
	    global $typenow;
	    global $wp_query;
	    if ($typenow=='page' || $typenow=='post') {	       
	        $search_location = (isset($_GET['search_location'])) ? $_GET['search_location'] : "";
	        $locations = $this->get_locations_code();
			?>
	        <select type='text' id='search_location' name='search_location' value='<?php echo $search_location; ?>'>
	        	<?php foreach ($locations as $id => $code) : ?>
	        		<option value="<?php echo $id; ?>" <?php echo ($search_location == $id)?'selected':''; ?>><?php echo $code; ?></option>
	        	<?php endforeach; ?>
	        </select>
	        <?php
	    }
	}

	public function modify_filter_location( $query ) {
	    global $typenow;
	    global $pagenow;

	    $search_location = (isset($_GET['search_location'])) ? $_GET['search_location'] : "";

	    if( $pagenow == 'edit.php' && ($typenow == 'page' || $typenow == 'post' ) && $search_location ) {
	        $query->query_vars[ 'meta_key' ] = 'location_post';
	        $query->query_vars[ 'meta_value' ] = $search_location;
	        $query->query_vars[ 'meta_compare' ] = 'LIKE';
	    }
	}


}

?>