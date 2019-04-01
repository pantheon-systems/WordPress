<?php
/**
 * Creatives
 *
 * This class handles the asset management of affiliate banners/HTML/links etc
 *
 * @package     AffiliateWP
 * @copyright   Copyright (c) 2012, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2
 */

class Affiliate_WP_Creatives {

	/**
	 * The [affiliate_creative] shortcode
	 *
	 * @since  1.2
	 * @return string
	 */
	public function affiliate_creative( $args = array() ) {

		// Creative's ID
		$id = isset( $args['id'] ) ? (int) $args['id'] : 0;

		if ( ! $creative = affwp_get_creative( $id ) ) {
			return;
		}

		// creative's link/URL
		if ( ! empty( $args['link'] ) ) {
			// set link to shortcode parameter
			$link = $args['link'];
		} elseif ( $creative->url ) {
			// set link to creative's link from creatives section
			$link = $creative->url;
		} else {
			// set link to the site URL
			$link = get_site_url();
		}

		// creative's image link
		$image_link = ! empty( $args['image_link'] ) ? $args['image_link'] : $creative->image;

		// creative's text (shown in alt/title tags)
		if ( ! empty( $args['text'] ) ) {
			// set text to shortcode parameter if used
			$text = $args['text'];
		} elseif ( $creative->text ) {
			// set text to creative's text from the creatives section
			$text = $creative->text;
		} else {
			// set text to name of blog
			$text = get_bloginfo( 'name' );
		}

		// creative's description
		$description = ! empty( $args['description'] ) ? $args['description'] : $creative->description;

		// creative's preview parameter
		$preview = ! empty( $args['preview'] ) ? $args['preview'] : 'yes';

		// get the image attributes from image_id
		$attributes = ! empty( $args['image_id'] ) ? wp_get_attachment_image_src( $args['image_id'], 'full' ) : '';

		// load the HTML required for the creative
		return $this->html( $id, $link, $image_link, $attributes, $preview, $text, $description );

	}

	/**
	 * The [affiliate_creatives] shortcode
	 *
	 * @since  1.2
	 * @return string
	 */
	public function affiliate_creatives( $args = array() ) {

		$defaults = array(
			'preview' => 'yes',
			'status'  => 'active'
		);

		$args = wp_parse_args( $args, $defaults );

		ob_start();

		$creatives = affiliate_wp()->creatives->get_creatives( $args );

		if ( $creatives ) {
			foreach ( $creatives as $creative ) {

				$url   = $creative->url;
				$image = $creative->image;
				$text  = $creative->text;
				$desc  = ! empty( $creative->description ) ? $creative->description : '';

				echo $this->html( $creative->creative_id, $url, $image, '', $args['preview'], $text, $desc );
			}
		}

		return ob_get_clean();
	}

	/**
	 * Returns the referral link to append to the end of a URL
	 *
	 * @since  1.2
	 * @return string Affiliate's referral link
	 */
	public function ref_link( $url = '' ) {
		return affwp_get_affiliate_referral_url( array( 'base_url' => $url ) );
	}

	/**
	 * Shortcode HTML
	 *
	 * @since  1.2
	 * @param  $image the image URL. Either the URL from the image column in DB or external URL of image.
	 * @return string
	 */
	public function html( $id = '', $url, $image_link, $image_attributes, $preview, $text, $desc = '' ) {

		global $affwp_creative_atts;

		$id_class = $id ? ' creative-' . $id : '';

		$affwp_creative_atts = array(
			'id'               => $id,
			'url'              => $url,
			'id_class'         => $id_class,
			'desc'             => $desc,
			'preview'          => $preview,
			'image_attributes' => $image_attributes,
			'image_link'       => $image_link,
			'text'             => $text
		);

		ob_start();

		affiliate_wp()->templates->get_template_part( 'creative' );

		$html = ob_get_clean();
		return apply_filters( 'affwp_affiliate_creative_html', $html, $url, $image_link, $image_attributes, $preview, $text );
	}

}