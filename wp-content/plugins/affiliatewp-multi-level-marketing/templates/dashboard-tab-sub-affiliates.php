<?php 

	$affiliate_id = affwp_get_affiliate_id();

	// Get affiliate's existing view settings
	$subs_view = affwp_get_affiliate_meta( $affiliate_id, 'view_subs_aff' );
	$subs_view = isset( $subs_view[0] ) ? $subs_view[0] : '';
	$show = $subs_view;


?>

<div id="affwp-affiliate-dashboard-sub-affiliates" class="affwp-tab-content">

	<?php do_action( 'affwp_dashboard_sub_affiliates_top', $affiliate_id ); ?>

	<?php show_sub_affiliates( $affiliate_id, $show ); ?>
    
    <?php do_action( 'affwp_dashboard_sub_affiliates_bottom', $affiliate_id ); ?>

</div>	