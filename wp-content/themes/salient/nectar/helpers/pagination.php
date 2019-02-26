<?php
/**
 * Pagination related helpers
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/*Previous and Next Post in Same Taxonomy*/
/*Author: Bill Erickson*/
if( !function_exists('be_get_previous_post') ) {
	function be_get_previous_post($in_same_cat = false, $excluded_categories = '', $taxonomy = 'category') {
		return be_get_adjacent_post($in_same_cat, $excluded_categories, true, $taxonomy);
	}
}

if( !function_exists('be_get_next_post') ) {
	function be_get_next_post($in_same_cat = false, $excluded_categories = '', $taxonomy = 'category') {
		return be_get_adjacent_post($in_same_cat, $excluded_categories, false, $taxonomy);
	}
}


if( !function_exists('be_get_adjacent_post') ) {
	
	function be_get_adjacent_post( $in_same_cat = false, $excluded_categories = '', $previous = true, $taxonomy = 'category' ) {
		global $post, $wpdb;

		if ( empty( $post ) )
			return null;

		$current_post_date = $post->post_date;

		$join = '';
		$posts_in_ex_cats_sql = '';
		if ( $in_same_cat || ! empty( $excluded_categories ) ) {
			$join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";

			if ( $in_same_cat ) {
				$cat_array = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'ids'));
				$join .= " AND tt.taxonomy = '$taxonomy' AND tt.term_id IN (" . implode(',', $cat_array) . ")";
			}

			$posts_in_ex_cats_sql = "AND tt.taxonomy = '$taxonomy'";
			if ( ! empty( $excluded_categories ) ) {
				if ( ! is_array( $excluded_categories ) ) {
					// back-compat, $excluded_categories used to be IDs separated by " and "
					if ( strpos( $excluded_categories, ' and ' ) !== false ) {
						_deprecated_argument( __FUNCTION__, '3.3', sprintf( __( 'Use commas instead of %s to separate excluded categories.','salient' ), "'and'" ) );
						$excluded_categories = explode( ' and ', $excluded_categories );
					} else {
						$excluded_categories = explode( ',', $excluded_categories );
					}
				}

				$excluded_categories = array_map( 'intval', $excluded_categories );
					
				if ( ! empty( $cat_array ) ) {
					$excluded_categories = array_diff($excluded_categories, $cat_array);
					$posts_in_ex_cats_sql = '';
				}

				if ( !empty($excluded_categories) ) {
					$posts_in_ex_cats_sql = " AND tt.taxonomy = '$taxonomy' AND tt.term_id NOT IN (" . implode($excluded_categories, ',') . ")";
				}
			}
		}

		$adjacent = $previous ? 'previous' : 'next';
		$op = $previous ? '<' : '>';
		$order = $previous ? 'DESC' : 'ASC';

		$join = apply_filters( "get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories );
		$where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish' $posts_in_ex_cats_sql", $current_post_date, $post->post_type), $in_same_cat, $excluded_categories );
		$sort = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1" );

		$query = "SELECT p.* FROM $wpdb->posts AS p $join $where $sort";
		$query_key = 'adjacent_post_' . md5($query);
		$result = wp_cache_get($query_key, 'counts');
		if ( false !== $result )
			return $result;

		$result = $wpdb->get_row("SELECT p.* FROM $wpdb->posts AS p $join $where $sort");
		if ( null === $result )
			$result = '';

		wp_cache_set($query_key, $result, 'counts');
		return $result;
	}
	
}

if( !function_exists('be_previous_post_link') ) {
	function be_previous_post_link($format='&laquo; %link', $link='%title', $in_same_cat = false, $excluded_categories = '', $taxonomy = 'category') {
		be_adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, true, $taxonomy);
	}
}


if( !function_exists('be_next_post_link') ) {
	function be_next_post_link($format='%link &raquo;', $link='%title', $in_same_cat = false, $excluded_categories = '', $taxonomy = 'category') {
		be_adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, false, $taxonomy);
	}
}

if( !function_exists('be_adjacent_post_link') ) {
	
	function be_adjacent_post_link($format, $link, $in_same_cat = false, $excluded_categories = '', $previous = true, $taxonomy = 'category') {
		if ( $previous && is_attachment() )
			$post = & get_post($GLOBALS['post']->post_parent);
		else
			$post = be_get_adjacent_post($in_same_cat, $excluded_categories, $previous, $taxonomy);

		if ( !$post )
			return;

		$title = $post->post_title;

		if ( empty($post->post_title) )
			$title = $previous ? esc_html__('Previous Post','salient') : esc_html__('Next Post','salient');

		$title = apply_filters('the_title', $title, $post->ID);
		$date = mysql2date(get_option('date_format'), $post->post_date);
		$rel = $previous ? 'prev' : 'next';

		$string = '<a href="'. esc_url(get_permalink($post)) .'" rel="'.$rel.'">';
		$link = str_replace('%title', $title, $link);
		$link = str_replace('%date', $date, $link);
		$link = $string . $link . '</a>';

		$format = str_replace('%link', $link, $format);

		$adjacent = $previous ? 'previous' : 'next';
		echo apply_filters( "{$adjacent}_post_link", $format, $link );
	}
	
}





if ( !function_exists( 'nectar_pagination' ) ) {
	
	function nectar_pagination() {
		
		global $nectar_options;
		//extra pagination
		if( !empty($nectar_options['extra_pagination']) && $nectar_options['extra_pagination'] == '1' ){
			
			    global $wp_query, $wp_rewrite; 
	      
			    $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1; 
			    $total_pages = $wp_query->max_num_pages; 
			      
			    if ($total_pages > 1){  
			      
			      $permalink_structure = get_option('permalink_structure');
				  $query_type = (count($_GET)) ? '&' : '?';	
			      $format = empty( $permalink_structure ) ? $query_type.'paged=%#%' : 'page/%#%/';  
				
				  echo '<div id="pagination" data-is-text="'.esc_attr__("All items loaded", 'salient').'">';
				   
			      echo paginate_links(array(  
			          'base' => get_pagenum_link(1) . '%_%',  
			          'format' => $format,  
			          'current' => $current,  
			          'total' => $total_pages,  
			          'prev_text'    => esc_html__('Previous','salient'),
    				  	'next_text'    => esc_html__('Next','salient')
			        )); 
					
				  echo  '</div>'; 
					
			    }  
	}
		//regular pagination
		else{
			
			if( get_next_posts_link() || get_previous_posts_link() ) { 
				echo '<div id="pagination" data-is-text="'.esc_attr__("All items loaded", 'salient').'">
				      <div class="prev">'.get_previous_posts_link('&laquo; Previous').'</div>
				      <div class="next">'.get_next_posts_link('NextPrevious &raquo;','').'</div>
			          </div>';
			
	        }
		}
		
	}
	
}
