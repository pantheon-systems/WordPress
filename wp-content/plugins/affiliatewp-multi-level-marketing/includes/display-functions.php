<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display an affiliate's name
 *
 * @since  1.1
 */
function show_affiliate_name( $affiliate_id = 0 ) {
		
	if ( empty( $affiliate_id ) ) $affiliate_id = affwp_get_affiliate_id();
	
	if ( empty( $affiliate_id ) ) return;

	$aff_name = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );
	
	if ( empty( $aff_name ) ) $aff_name = 'None';
	
	?>
	
	<span class="affwp-mlm-aff-name name-only"><?php echo $aff_name; ?></span>
	
	<?php

}

/**
 * Display an affiliate's avatar
 *
 * @since  1.1
 */
function show_affiliate_avatar( $affiliate_id = 0 ) {
		
	if ( empty( $affiliate_id ) ) $affiliate_id = affwp_get_affiliate_id();
	
	if ( empty( $affiliate_id ) ) return;
	
	$aff_user_id = affwp_get_affiliate_user_id( $affiliate_id );
	$aff_name = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );
	$aff_avatar = get_avatar( $aff_user_id );
	
	if ( class_exists( 'BuddyPress' ) ) {
		$aff_avatar = bp_core_fetch_avatar( array(
						'item_id' => $aff_user_id,
						'type' => 'full',
						'width' => 150,
						'height' => 150,
						'html' => true,  
						'alt' => sprintf( __( 'Profile picture of %s', 'buddypress' ), $aff_name ),
						'class' => 'avatar',
						)
					  );
	}

	if ( empty( $aff_name ) ) $aff_name = 'None';
	
	?>
	<div class="affwp-mlm-aff">
    	<?php if ( !empty( $aff_avatar ) ) {  ?>
			<div class="affwp-mlm-aff-avatar">
            	<?php if ( class_exists( 'BuddyPress' ) && class_exists( 'AffiliateWP_BuddyPress_Pro' ) ) {  ?>
            		<a href="<?php echo bp_core_get_user_domain( $aff_user_id ); ?>">
				<?php }  ?> 
					<?php echo $aff_avatar; ?>
                <?php if ( class_exists( 'BuddyPress' ) && class_exists( 'AffiliateWP_BuddyPress_Pro' ) ) {  ?>
                	</a>
                <?php }  ?> 
            </div>
        <?php }  ?>    
		<span class="affwp-mlm-aff-name"><?php echo $aff_name; ?></span>
	</div>
	<?php
	
}


/**
 * Display an affiliate's parent (Parent Affiliate)
 *
 * @since  1.1
 */
function show_parent_affiliate( $affiliate_id = 0, $show = '' ) {

	if ( empty( $affiliate_id ) ) $affiliate_id = affwp_get_affiliate_id();
	
	if ( empty( $affiliate_id ) ) return;
	
	$parent_id = affwp_mlm_get_parent_affiliate( $affiliate_id );
	
	// Show the parent affiliate's name
	if ( $show == 'name' ) {
		show_affiliate_name( $parent_id );
	}
	
	// Show the parent affiliate's avatar, name, etc.
	if ( $show == 'avatar' ) {
		show_affiliate_avatar( $parent_id );
	}
	
}

/**
 * Display an affiliate's referrer (Direct Affiliate)
 *
 * @since  1.1
 */
function show_direct_affiliate( $affiliate_id = 0, $show = '' ) {

	if ( empty( $affiliate_id ) ) $affiliate_id = affwp_get_affiliate_id();
	
	if ( empty( $affiliate_id ) ) return;
	
	$direct_id = affwp_mlm_get_direct_affiliate( $affiliate_id );
	
	// Show the direct affiliate's name
	if ( $show == 'name' ) {
		show_affiliate_name( $direct_id );
	}
	
	// Show the direct affiliate's avatar, name, etc.
	if ( $show == 'avatar' ) {
		show_affiliate_avatar( $direct_id );
	}
	
}

/**
 * Display the number of Sub Affiliates a Parent Affiliate has
 *
 * @since  1.1.2
 */
function show_downline_count( $affiliate_id = 0 ) {

	if ( empty( $affiliate_id ) ) $affiliate_id = affwp_get_affiliate_id();
	
	if ( empty( $affiliate_id ) ) return;
	
	if ( affwp_mlm_is_parent_affiliate( $affiliate_id ) ) {
		$downline_count = count( affwp_mlm_get_downline_array( $affiliate_id ) ) - 1;
	} else {
		$downline_count = 0;
	}
	
	?>
	
	<span class="affwp-mlm-aff-downline-count"><?php echo $downline_count; ?></span>
	
	<?php
}

/**
 * Get an affiliate's data (Stats)
 *
 * @since  1.1
 */
function get_affiliate_data( $affiliate_id ) {

	if ( empty( $affiliate_id ) ) $affiliate_id = affwp_get_affiliate_id();
	
	if ( empty( $affiliate_id ) ) return;

	// Affiliate info
	$affiliate = affwp_get_affiliate( $affiliate_id );
	$join_date = esc_attr( date_i18n( 'm-d-Y', strtotime( $affiliate->date_registered ) ) );
	$status    = affwp_get_affiliate_status( $affiliate_id );
	$user_id   = affwp_get_affiliate_user_id( $affiliate_id );
	$aff_user  = get_userdata( $user_id );
	$contact   = $aff_user->user_email;
	
	// Referral data
	$paid_referrals   = affwp_get_affiliate_referral_count( $affiliate_id );
	$unpaid_referrals = affwp_count_referrals( $affiliate, 'unpaid' );
	$total_referrals  = $paid_referrals + $unpaid_referrals;
	
	// Earnings data
	$paid_earnings   = affwp_get_affiliate_earnings( $affiliate_id, true );
	$unpaid_earnings = affwp_get_affiliate_unpaid_earnings( $affiliate_id, true );
	$total_earnings  = affwp_get_affiliate_earnings( $affiliate_id ) + affwp_get_affiliate_unpaid_earnings( $affiliate_id );
	$total_earnings  = affwp_currency_filter( affwp_format_amount( $total_earnings ) );
	
	// Network data
	$direct_id        = affwp_mlm_get_direct_affiliate( $affiliate_id );
	$parent_id        = affwp_mlm_get_parent_affiliate( $affiliate_id );
	$referrer         = affiliate_wp()->affiliates->get_affiliate_name( $direct_id );
	$referrer 	  	  = str_replace( "'", "", $referrer );
	$parent 		  = affiliate_wp()->affiliates->get_affiliate_name( $parent_id );
	$parent		 	  = str_replace( "'", "", $parent );
	$sub_affiliates   = count( affwp_mlm_get_sub_affiliates( $affiliate_id ) );
	$downline 		  = count( affwp_mlm_get_downline_array( $affiliate_id ) ) - 1;
	
	if ( $downline < 0 ) $downline = 0;
	
	$aff_data =	array(
					'info' => array(
						'title'    => __( 'Info', 'affiliatewp-multi-level-marketing' ),
						'icon'     => 'fa-info',
						'content'  => array(						
							'joined'  => $join_date,
							'status'  => $status,
							'contact' => $contact,
						)
					),
					'referrals' => array(
						'title'    => __( 'Referrals', 'affiliatewp-multi-level-marketing' ),
						'icon'     => 'fa-link',
						'content'  => array(						
							'paid'   => $paid_referrals,
							'unpaid' => $unpaid_referrals,
							'total'  => $total_referrals,
						)
					),
					'earnings' => array(
						'title'    => __( 'Earnings', 'affiliatewp-multi-level-marketing' ),
						'icon'     => 'fa-usd',
						'content'  => array(						
							'paid'   => $paid_earnings,
							'unpaid' => $unpaid_earnings,
							'total'  => $total_earnings,
						)
					),
					'sub_affiliates' => array(
						'title'    => __( 'Network', 'affiliatewp-multi-level-marketing' ),
						'icon'     => 'fa-sitemap',
						'content'  => array(
							'referrer' => $referrer,
							'parent'   => $parent,
							'direct'   => $sub_affiliates,
							'downline' => $downline,
						)
					)

				);
	
	$aff_data = apply_filters( 'affwp_mlm_aff_data', $aff_data, $affiliate_id );
	
	// Remove Direct Affiliate if same as Parent
	if ( empty( $parent ) || $referrer == $parent ) 
		unset( $aff_data['sub_affiliates']['content']['referrer'] );
	
	// Disable specific sections based on settings
	$disabled_aff_data = affiliate_wp()->settings->get( 'affwp_mlm_aff_data_disabled' );
	
	if ( $disabled_aff_data ) {
		foreach ( $disabled_aff_data as $disabled_key => $disabled_data ) {
			if ( $disabled_key ) {
				unset( $aff_data[$disabled_key] );
			}
		}
	}
	
	return $aff_data;

}

/**
 * Display an affiliate's data in a list format (Stats)
 *
 * @since  1.1
 */
function show_affiliate_data( $affiliate_id ) {

	if ( empty( $affiliate_id ) ) $affiliate_id = affwp_get_affiliate_id();
	
	if ( empty( $affiliate_id ) ) return;

	$aff_data = get_affiliate_data( $affiliate_id );

	if ( empty( $aff_data ) ) return $formatted_data;

	$formatted_data = '<ul class="affwp-mlm-aff-data-wrap">';
		$formatted_data .= '<i class="fa fa-chevron-down"></i>';
		$formatted_data .= '<ul class="affwp-mlm-aff-data">';
		
			foreach( $aff_data as $key => $data ) {
			
				$formatted_data .= '<li class="'. $key .'">';
					$formatted_data .= '<i class="fa '. $data['icon'] .'"></i>';
					$formatted_data .= '<span>'. $data['title'] .'</span>';
	
						$formatted_data .= '<ul>';
							foreach( $data['content'] as $content_key => $content_data ) {
								$content_key = str_replace( "_"," ", $content_key );
								$content_key = ucwords( $content_key );
								
								$formatted_data .= '<li>';
									$formatted_data .= '<strong>'. $content_key .'</strong>';
									$formatted_data .= '<span>'. $content_data .'</span>';
								$formatted_data .= '</li>';
							}
						$formatted_data .= '</ul>';
				$formatted_data .= '</li>';

			}	
		$formatted_data .= '</ul>';
	$formatted_data .= '</ul>';
	
	return $formatted_data;

}

/**
 * Display an affiliate's Sub Affiliates (Tree View)
 *
 * @since  1.1
 */
function show_sub_affiliates_tree( $affiliate_id = 0, $levels = 0 ) {

	if ( empty( $affiliate_id ) ) $affiliate_id = affwp_get_affiliate_id();
	
	if ( empty( $affiliate_id ) ) return;

	$sub_affiliates = affwp_mlm_get_downline_array( $affiliate_id, $levels );
	$total_downline = count( $sub_affiliates ) - 1; // Entire Downline
	if ( $total_downline < 0 ) $total_downline = 0; 
	$level_count = 0;
	
	if ( ! affiliate_wp()->settings->get( 'affwp_mlm_view_parent' ) ) $tree_class = 'hide-parent';
	?>
    
	<div id="affwp-mlm-sub-affiliates-tree" class="<?php echo $tree_class ?>">
	<?php if ( affwp_mlm_is_parent_affiliate( $affiliate_id ) ) : ?>
    	<h4><?php echo __( 'Sub Affiliates', 'affiliatewp-multi-level-marketing' ); ?><span class="affwp-mlm-downline-count"><?php echo $total_downline; ?></span></h4>
        	<?php
				if ( $sub_affiliates ) { ?>

			 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
              <script type="text/javascript">
                  google.charts.load('current', {packages:["orgchart"]});
                  google.charts.setOnLoadCallback(drawChart);
            
                  function drawChart() {
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Affiliate Name');
                    data.addColumn('string', 'Parent Affiliate');
                    data.addColumn('string', 'ToolTip');
            
                    // For each orgchart node, provide the name, parent, and tooltip to show.
                    data.addRows([
                    
                    <?php foreach ( $sub_affiliates as $sub_id ) :
                    
                    $user_id = affwp_get_affiliate_user_id( $sub_id );
                    $sub_user = get_user_by( 'id', $user_id );
                    
                    $parent_aff_id = affwp_mlm_get_parent_affiliate( $sub_id );
                    $parent_user_id = affwp_get_affiliate_user_id( $parent_aff_id );
                    $parent_user = get_user_by( 'id', $parent_user_id );
                    
                    // Both names must match
                    $sub_name = $sub_user->display_name;
					$sub_name = str_replace( "'", "", $sub_name );
                    $parent_name = $parent_user->display_name;
					$parent_name = str_replace( "'", "", $parent_name );
                    $affiliate_status = affwp_get_affiliate_status( $sub_id );
                    
                    //$sub_node = show_affiliate_avatar( $sub_id );
                    $sub_data = show_affiliate_data( $sub_id );
                    $sub_avatar = get_avatar( $user_id );

					if ( class_exists( 'BuddyPress' ) ) {
						$sub_avatar = bp_core_fetch_avatar( array(
										'item_id' => $user_id,
										'type' => 'full',
										'width' => 150,
										'height' => 150,
										'html' => true,  
										'alt' => sprintf( __( 'Profile picture of %s', 'buddypress' ), $sub_name ),
										'class' => 'avatar',
										)
									  );
					}					
										
					$sub_avatar = str_replace( "'", "\"", $sub_avatar ); // Ensure double quotes are used when BP isn't active
                    $sub_node  = '<div class="sub_node '. $affiliate_status .' affwp-mlm-aff">';
					
						if ( class_exists( 'BuddyPress' ) && class_exists( 'AffiliateWP_BuddyPress_Pro' ) ) {
							$sub_node .= '<div class="affwp-mlm-aff-avatar">';
								$sub_node .= '<a href="'. bp_core_get_user_domain( $user_id ) .'">';
									$sub_node .= $sub_avatar;
								$sub_node .= '</a>';
							$sub_node .= '</div>';
						} else{
							$sub_node .= '<div class="affwp-mlm-aff-avatar">'. $sub_avatar .'</div>';							
						}
						
                        $sub_node .= '<span class="affwp-mlm-aff-name">'. $sub_name .'</span>';
						
						$sub_node .= apply_filters( 'affwp_mlm_aff_extra', $aff_extra, $sub_id );
						
						if ( ! empty( $sub_data ) ) $sub_node .= $sub_data;
						
                    $sub_node .= '</div>';
            
                    $tooltip = 'Affiliate ID: '. $sub_id;
					
					// Show Parent in Tree
					if ( $sub_id == $affiliate_id && !empty( $parent_aff_id ) && affiliate_wp()->settings->get( 'affwp_mlm_view_parent' ) ) {
						$parent_aff_status = affwp_get_affiliate_status( $parent_aff_id );
						
						$parent_data = show_affiliate_data( $parent_aff_id );
						$parent_avatar = get_avatar( $parent_user_id );
						
						if ( class_exists( 'BuddyPress' ) ) {
							$parent_avatar = bp_core_fetch_avatar( array(
											'item_id' => $parent_user_id,
											'type' => 'full',
											'width' => 150,
											'height' => 150,
											'html' => true,  
											'alt' => sprintf( __( 'Profile picture of %s', 'buddypress' ), $parent_name ),
											'class' => 'avatar',
											)
										  );
						}
						
						$parent_avatar = str_replace( "'", "\"", $parent_avatar ); // Ensure double quotes are used when BP isn't active
						$parent_node  = '<div class="sub_node '. $parent_aff_status .' affwp-mlm-aff parent-aff">';
						
							if ( class_exists( 'BuddyPress' ) && class_exists( 'AffiliateWP_BuddyPress_Pro' ) ) {
								$parent_node .= '<div class="affwp-mlm-aff-avatar">';
									$parent_node .= '<a href="'. bp_core_get_user_domain( $parent_user_id ) .'">';
										$parent_node .= $parent_avatar;
									$parent_node .= '</a>';
								$parent_node .= '</div>';
							} else{
								$parent_node .= '<div class="affwp-mlm-aff-avatar">'. $parent_avatar .'</div>';							
							}
							
							$parent_node .= '<span class="affwp-mlm-aff-name">'. $parent_name .'</span>';
							
							if ( ! empty( $parent_data ) ) $parent_node .= $parent_data;
							
						$parent_node .= '</div>';
						$parent_name = $parent_node;
						
					}
                    ?>
                    
                    [{v:'<?php echo $sub_name; ?>', f:'<?php echo $sub_node; ?>'}, '<?php echo $parent_name; ?>', '<?php echo $tooltip; ?>'],
                    
                    <?php endforeach; ?>
                    
                    ]);
                    
                    var options = {
                      allowHtml: true,
                      allowColapse: true,
                      size: 'medium',
                      nodeClass: 'sub_affiliate_node',
                      selectedNodeClass: 'sub_affiliate_node_selected'
                    };
            
                    // Create the chart.
                    var chart = new google.visualization.OrgChart( document.getElementById( 'tree_wrap' ) );
                    // Draw the chart, setting the allowHtml option to true for the tooltips.
                    chart.draw( data, options );
                  }
               </script>
   
				<div id="tree_wrap"></div>

		  <?php } ?>


    <?php else : ?>
            
        <h4><?php _e( 'No Sub Affiliates yet.', 'affiliatewp-multi-level-marketing' ); ?></h4>
        
        <?php if ( $affiliate_id == affwp_get_affiliate_id() && affiliate_wp()->settings->get( 'allow_affiliate_registration' ) ) { ?>
            <p><?php _e( 'When a user registers as an affiliate using your referral URL they will become your sub-affiliate.', 'affiliatewp-multi-level-marketing' ); ?></p>
        <?php } ?>
            
	<?php endif; ?>
    
    </div>	
<?php
}

/**
 * Display an affiliate's Sub Affiliates (List View)
 *
 * @since  1.1
 */
function show_sub_affiliates_list( $affiliate_id = 0, $levels = 0 ) {

	if ( empty( $affiliate_id ) ) $affiliate_id = affwp_get_affiliate_id();
	
	if ( empty( $affiliate_id ) ) return;

	$sub_affiliates = affwp_mlm_get_downline( $affiliate_id, $levels );
	$name = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );
	$level_count = 0;

	if ( affwp_mlm_is_parent_affiliate( $affiliate_id ) ) : ?>
    	<h4><?php echo __( 'Sub Affiliates', 'affiliatewp-multi-level-marketing' ); ?></h4>    
        	<?php
				if ( $sub_affiliates ) { ?>
                
                	<?php if ( is_admin() ) { ?>
						<style type="text/css">#sub_affiliates th { padding-left: 10px; }</style>
                    <?php } ?>    
                    <table id="sub_affiliates" class="affwp-table table form-table wp-list-table widefat">
                        <thead>
                            <tr>
                                <th class="sub-level"><?php _e( 'Level', 'affiliatewp-multi-level-marketing' ); ?></th>
                                <th class="sub-name"><?php _e( 'Name', 'affiliatewp-multi-level-marketing' ); ?></th>
                                <th class="sub-status"><?php _e( 'Status', 'affiliatewp-multi-level-marketing' ); ?></th>
                                <th class="sub-parent"><?php _e( 'Parent', 'affiliatewp-multi-level-marketing' ); ?></th>
                                <th class="sub-referrals"><?php _e( 'Referrals', 'affiliatewp-multi-level-marketing' ); ?></th>
                                <th class="sub-earnings"><?php _e( 'Earnings', 'affiliatewp-multi-level-marketing' ); ?></th>
                                <th class="sub-sub-affiliates"><?php _e( 'Sub Affiliates', 'affiliatewp-multi-level-marketing' ); ?></th>
								<?php do_action( 'affwp_mlm_sub_affiliates_dashboard_th' ) ?>
                            </tr>
                        </thead>
                        <tbody>

				 <?php foreach ( $sub_affiliates as $lvl ) {
						
						$level_count++;
						
						if ( in_array( $affiliate_id, $lvl ) ) $level_count = 0;
						
						foreach ( $lvl as $sub_id ) {
							
							if ( $sub_id == $affiliate_id )
								continue;
							
							$lvl_label 		  = 'Level ' . $level_count;
							$name      		  = affiliate_wp()->affiliates->get_affiliate_name( $sub_id );
							$status           = affwp_get_affiliate_status( $sub_id );
							
							$parent_id        = affwp_mlm_get_parent_affiliate( $sub_id );
							$parent 		  = affiliate_wp()->affiliates->get_affiliate_name( $parent_id );
							
							$paid_referrals   = affwp_get_affiliate_referral_count( $sub_id );
							$unpaid_referrals = affwp_count_referrals( $sub_id, 'unpaid' );
							$referrals  	  = $paid_referrals + $unpaid_referrals;

							$paid_earnings   = affwp_get_affiliate_earnings( $sub_id, true );
							$unpaid_earnings = affwp_get_affiliate_unpaid_earnings( $sub_id, true );
							$earnings  		 = affwp_get_affiliate_earnings( $sub_id ) + affwp_get_affiliate_unpaid_earnings( $sub_id );
							$earnings  		 = affwp_currency_filter( affwp_format_amount( $earnings ) );
							
							$downline  		 = count( affwp_mlm_get_downline_array( $sub_id ) ) - 1; // Entire Downline
							if ( $downline < 0 ) $downline = 0; 
						
			?>

                        <tr>
                            <td class="sub-level"><?php echo $lvl_label; ?></td>
                            <td class="sub-name"><?php echo $name; ?></td>
                            <td class="sub-status"><?php echo $status; ?></td>
							<td class="sub-parent"><?php echo $parent; ?></td>
                            <td class="sub-referrals"><?php echo $referrals; ?></td>
                            <td class="sub-earnings"><?php echo $earnings; ?></td>
                            <td class="sub-downline"><?php echo $downline; ?></td>
                            <?php do_action( 'affwp_mlm_sub_affiliates_dashboard_td', $sub_id ); ?>
                        </tr> 

            	 <?php } ?>  
    
			 <?php } ?>
             
                </tbody>                            
            </table>
            
		  <?php } ?>

    <?php else : ?>
            
        <h4><?php _e( 'No Sub Affiliates yet.', 'affiliatewp-multi-level-marketing' ); ?></h4>
        
        <?php if ( $affiliate_id == affwp_get_affiliate_id() && affiliate_wp()->settings->get( 'allow_affiliate_registration' ) ) { ?>
            <p><?php _e( 'When a user registers as an affiliate using your referral URL they will become your sub-affiliate.', 'affiliatewp-multi-level-marketing' ); ?></p>
        <?php } ?>
            
	<?php endif;
	
}

/**
 * Display an affiliate's Sub Affiliates
 *
 * @since  1.1
 */
function show_sub_affiliates( $affiliate_id = 0, $show = '', $levels = 0 ) {

	if ( empty( $affiliate_id ) ) $affiliate_id = affwp_get_affiliate_id();
	
	if ( empty( $affiliate_id ) ) return;
	
	if ( empty( $show ) ) $show = affiliate_wp()->settings->get( 'affwp_mlm_view_subs' );

	if ( empty( $show ) ) $show = 'tree';
	
	// Show the tree view
	if ( $show == 'tree' ) {
		show_sub_affiliates_tree( $affiliate_id, $levels );
	}

	// Show the list view
	if ( $show == 'list' ) {
		show_sub_affiliates_list( $affiliate_id, $levels );
	}

}

/**
 * Display a table of an affiliate's indirect referrals
 *
 * @since  1.1
 */
function show_indirect_referrals( $affiliate_id = 0, $add_fragment = '', $tab = '' ) {
		
	if ( empty( $affiliate_id ) ) $affiliate_id = affwp_get_affiliate_id();
	
	if ( empty( $affiliate_id ) ) return;
	
	if ( empty( $add_fragment ) ) $add_fragment = '#affwp-affiliate-dashboard-sub-affiliates';
	
	if ( empty( $tab ) ) $tab = 'sub-affiliates'; // 'referrals'
	
	?>

	<h4><?php _e( 'Indirect Referrals', 'affiliatewp-multi-level-marketing' ); ?></h4>
	
	<?php
	// get referrals for sub affiliates				
	$per_page  = 30;
	$page      = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

	// get the affiliate's referrals
	$referrals = affiliate_wp_mlm()->get_sub_affiliate_referrals(
		array(
			'number' => $per_page,
			'offset' => $per_page * ( $page - 1 ),
			'affiliate_id' => $affiliate_id,
		)
	);
	?>

	<?php if ( $referrals ) : ?>
	<p><?php _e( 'These referrals were awarded to you due to the efforts of your Sub Affiliates. These are also shown on the referrals tab.', 'affiliatewp-multi-level-marketing' ); ?></p>
	<?php endif; ?>
	
	<table id="affwp-affiliate-dashboard-referrals" class="affwp-table">
		<thead>
			<tr>
				<th class="referral-amount"><?php _e( 'Amount', 'affiliate-wp' ); ?></th>
				<th class="referral-description"><?php _e( 'Description', 'affiliate-wp' ); ?></th>
				<th class="referral-status"><?php _e( 'Status', 'affiliate-wp' ); ?></th>
				<th class="referral-date"><?php _e( 'Date', 'affiliate-wp' ); ?></th>
				<?php do_action( 'affwp_mlm_indirect_referrals_dashboard_th' ) ?>
			</tr>
		</thead>

		<tbody>
			<?php if ( $referrals ) : ?>

				<?php foreach ( $referrals as $referral ) : ?>
					<tr>
						<td class="referral-amount"><?php echo affwp_currency_filter( affwp_format_amount( $referral->amount ) ); ?></td>
						<td class="referral-description"><?php echo $referral->description; ?></td>
						<td class="referral-status <?php echo $referral->status; ?>"><?php echo affwp_get_referral_status_label( $referral ); ?></td>
						<td class="referral-date"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $referral->date ) ); ?></td>
						<?php do_action( 'affwp_mlm_indirect_referrals_dashboard_td', $referral ); ?>
					</tr>
				<?php endforeach; ?>

			<?php else : ?>

				<tr>
					<?php if ( $affiliate_id == affwp_get_affiliate_id() ) { ?>
						<td colspan="4"><?php _e( 'You have not made any referrals from your sub affiliates yet.', 'affiliatewp-multi-level-marketing' ); ?></td>
					<?php } else { ?>
						<td colspan="4"><?php _e( 'This affiliate has not made any referrals from their sub affiliates yet.', 'affiliatewp-multi-level-marketing' ); ?></td>
					<?php } ?>
				</tr>

			<?php endif; ?>
		</tbody>
	</table>

	<div class="affwp-pagination">
		<?php
		echo paginate_links( array(
			'current'      => $page,
			'total'        => ceil( affiliate_wp_mlm()->count_sub_affiliate_referrals() / $per_page ),
			'add_fragment' => $add_fragment,
			'add_args'     => array(
			'tab'          => $tab
			)
		) );
		?>
	</div>

	<?php if ( $tab == 'referrals' ) { ?>
        <br><h4><?php echo __( 'Direct Referrals', 'affiliatewp-multi-level-marketing' ); ?></h4>
	<?php
	}
}
