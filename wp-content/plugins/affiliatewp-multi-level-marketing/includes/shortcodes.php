<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* [parent_affiliate] shortcode
*
* @since  1.1
*/
function affwp_parent_affiliate_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'affiliate_id' => affwp_get_affiliate_id(),
		'show' 	       => 'name'
		
	), $atts ) );

	ob_start();
	
	show_parent_affiliate( $affiliate_id, $show );
	
	$content = ob_get_clean();
	return $content;
}
add_shortcode( 'parent_affiliate', 'affwp_parent_affiliate_shortcode' );

/**
* [direct_affiliate] shortcode
*
* @since  1.1
*/
function affwp_direct_affiliate_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'affiliate_id' => affwp_get_affiliate_id(),
		'show' 		   => 'name'
		
	), $atts ) );

	ob_start();
	
	show_direct_affiliate( $affiliate_id, $show );
	
	$content = ob_get_clean();
	return $content;
}
add_shortcode( 'direct_affiliate', 'affwp_direct_affiliate_shortcode' );

/**
* [downline_count] shortcode
*
* @since  1.1.2
*/
function affwp_downline_count_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'affiliate_id' => affwp_get_affiliate_id()
		
	), $atts ) );

	ob_start();
	
	show_downline_count( $affiliate_id );
	
	$content = ob_get_clean();
	return $content;
}
add_shortcode( 'downline_count', 'affwp_downline_count_shortcode' );

/**
* [sub_affiliates] shortcode
*
* @since  1.1
*/
function affwp_sub_affiliates_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'affiliate_id' 	=> affwp_get_affiliate_id(),
		'show' 			=> 'tree',
		'levels' 		=> 0,
		
	), $atts ) );

	ob_start();
	
	show_sub_affiliates( $affiliate_id, $show, $levels );
	
	$content = ob_get_clean();
	return $content;
}
add_shortcode( 'sub_affiliates', 'affwp_sub_affiliates_shortcode' );

/**
* [indirect_referrals] shortcode
*
* @since  1.1
*/
function affwp_indirect_referrals_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'affiliate_id' => affwp_get_affiliate_id()
		
	), $atts ) );

	ob_start();
	
	show_indirect_referrals( $affiliate_id );
	
	$content = ob_get_clean();
	return $content;
}
add_shortcode( 'indirect_referrals', 'affwp_indirect_referrals_shortcode' );