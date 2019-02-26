<?php
/**
 * Emails
 *
 * This class handles all emails sent through AffiliateWP
 *
 * @package     AffiliateWP
 * @subpackage  Classes/Emails
 * @copyright   Copyright (c) 2015, Pippin Williamson
 * @license     http://opensource.org/license/gpl-2.1.php GNU Public License
 * @since       1.6
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Affiliate_WP_Emails class
 *
 * @since 1.6
 */
class Affiliate_WP_Emails {

	/**
	 * Holds the from address
	 *
	 * @since 1.6
	 */
	private $from_address;

	/**
	 * Holds the from name
	 *
	 * @since 1.6
	 */
	private $from_name;

	/**
	 * Holds the email content type
	 *
	 * @since 1.6
	 */
	private $content_type;

	/**
	 * Holds the email headers
	 *
	 * @since 1.6
	 */
	private $headers;

	/**
	 * Whether to send email in HTML
	 *
	 * @since 1.6
	 */
	private $html = true;

	/**
	 * The email template to use
	 *
	 * @since 1.6
	 */
	private $template;

	/**
	 * The header text for the email
	 *
	 * @since 1.6
	 */
	private $heading = '';

	/**
	 * Container for storing all tags
	 *
	 * @since 1.6
	 */
	private $tags;

	/**
	 * Affiliate ID
	 *
	 * @since 1.6
	 */
	private $affiliate_id;

	/**
	 * Referral object
	 *
	 * @since 1.6
	 */
	private $referral;

	/**
	 * Get things going
	 *
	 * @since 1.6
	 * @return void
	 */
	public function __construct() {

		if ( 'none' === $this->get_template() ) {
			$this->html = false;
		}

		add_action( 'affwp_email_send_before', array( $this, 'send_before' ) );
		add_action( 'affwp_email_send_after', array( $this, 'send_after' ) );
	}


	/**
	 * Set a property
	 *
	 * @since 1.6
	 * @return void
	 */
	public function __set( $key, $value ) {
		$this->$key = $value;
	}


	/**
	 * Get the email from name
	 *
	 * @since 1.6
	 * @return string The email from name
	 */
	public function get_from_name() {
		global $affwp_options;

		if ( ! $this->from_name ) {
			$this->from_name = affiliate_wp()->settings->get( 'from_name', get_bloginfo( 'name' ) );
		}

		return apply_filters( 'affwp_email_from_name', wp_specialchars_decode( $this->from_name ), $this );
	}


	/**
	 * Get the email from address
	 *
	 * @since 1.6
	 * @return string The email from address
	 */
	public function get_from_address() {
		if ( ! $this->from_address ) {
			$this->from_address = affiliate_wp()->settings->get( 'from_email', get_option( 'admin_email' ) );
		}

		return apply_filters( 'affwp_email_from_address', $this->from_address, $this );
	}


	/**
	 * Get the email content type
	 *
	 * @since 1.6
	 * @return string The email content type
	 */
	public function get_content_type() {
		if ( ! $this->content_type && $this->html ) {
			$this->content_type = apply_filters( 'affwp_email_default_content_type', 'text/html', $this );
		} elseif ( ! $this->html ) {
			$this->content_type = 'text/plain';
		}

		return apply_filters( 'affwp_email_content_type', $this->content_type, $this );
	}


	/**
	 * Get the email headers
	 *
	 * @since 1.6
	 * @return string The email headers
	 */
	public function get_headers() {
		if ( ! $this->headers ) {
			$this->headers  = "From: {$this->get_from_name()} <{$this->get_from_address()}>\r\n";
			$this->headers .= "Reply-To: {$this->get_from_address()}\r\n";
			$this->headers .= "Content-Type: {$this->get_content_type()}; charset=utf-8\r\n";
		}

		return apply_filters( 'affwp_email_headers', $this->headers, $this );
	}


	/**
	 * Retrieve email templates
	 *
	 * @since 1.6
	 * @return array The email templates
	 */
	public function get_templates() {
		$templates    = array(
			'default' => __( 'Default Template', 'affiliate-wp' ),
			'none'	  => __( 'No template, plain text only', 'affiliate-wp' )
		);

		return apply_filters( 'affwp_email_templates', $templates );
	}


	/**
	 * Get the enabled email template
	 *
	 * @since 1.6
	 * @return string|null
	 */
	public function get_template() {
		if ( ! $this->template ) {
			$this->template = affiliate_wp()->settings->get( 'email_template', 'default' );
		}

		return apply_filters( 'affwp_email_template', $this->template );
	}


	/**
	 * Get the header text for the email
	 *
	 * @since 1.6
	 * @return string The header text
	 */
	public function get_heading() {
		return apply_filters( 'affwp_email_heading', $this->heading );
	}


	/**
	 * Build the email
	 *
	 * @since 1.6
	 * @param string $message The email message
	 * @return string
	 */
	public function build_email( $message ) {

		if ( false === $this->html ) {
			return apply_filters( 'affwp_email_message', wp_strip_all_tags( $message ), $this );
		}

		$message = $this->text_to_html( $message );

		ob_start();

		affiliate_wp()->templates->get_template_part( 'emails/header', $this->get_template(), true );

		/**
		 * Hooks into the email header
		 *
		 * @since 1.6
		 */
		do_action( 'affwp_email_header', $this );


		affiliate_wp()->templates->get_template_part( 'emails/body', $this->get_template(), true );

		/**
		 * Hooks into the email body
		 *
		 * @since 1.6
		 */
		do_action( 'affwp_email_body', $this );

		affiliate_wp()->templates->get_template_part( 'emails/footer', $this->get_template(), true );

		/**
		 * Hooks into the email footer
		 *
		 * @since 1.6
		 */
		do_action( 'affwp_email_footer', $this );

		$body	 = ob_get_clean();
		$message = str_replace( '{email}', $message, $body );

		return apply_filters( 'affwp_email_message', $message, $this );
	}

	/**
	 * Send the email
	 *
	 * @since 1.6
	 * @param string $to The To address
	 * @param string $subject The subject line of the email
	 * @param string $message The body of the email
	 * @param string|array $attachments Attachments to the email
	 */
	public function send( $to, $subject, $message, $attachments = '' ) {

		if ( ! did_action( 'init' ) && ! did_action( 'admin_init' ) ) {
			_doing_it_wrong( __FUNCTION__, __( 'You cannot send emails with AffWP_Emails until init/admin_init has been reached', 'affiliate-wp' ), null );
			return false;
		}

		// Don't send anything if emails have been disabled
		if ( $this->is_email_disabled() ) {
			return false;
		}

		$this->setup_email_tags();

		/**
		 * Hooks before email is sent
		 *
		 * @since 1.6
		 */
		do_action( 'affwp_email_send_before', $this );

		$message = $this->build_email( $message );

		$message = $this->parse_tags( $message );

		$attachments = apply_filters( 'affwp_email_attachments', $attachments, $this );

		$sent = wp_mail( $to, $subject, $message, $this->get_headers(), $attachments );

		/**
		 * Hooks after the email is sent
		 *
		 * @since 1.6
		 */
		do_action( 'affwp_email_send_after', $this );

		return $sent;
	}


	/**
	 * Add filters/actions before the email is sent
	 *
	 * @since 1.6
	 */
	public function send_before() {
		add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
	}


	/**
	 * Remove filters/actions after the email is sent
	 *
	 * @since 1.6
	 */
	public function send_after() {
		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		// Reset heading to an empty string
		$this->heading = '';
	}


	/**
	 * Converts text formatted HTML. This is primarily for turning line breaks into <p> and <br/> tags.
	 *
	 * @since 1.6
	 */
	public function text_to_html( $message ) {
		if ( 'text/html' === $this->content_type || true === $this->html ) {
			$message = wpautop( $message );
		}

		return $message;
	}

	/**
	 * Search content for email tags and filter email tags through their hooks
	 *
	 * @since 1.6
	 * @param string $content Content to search for email tags
	 * @param int $affiliate_id The affiliate ID
	 * @return string $content Filtered content
	 */
	private function parse_tags( $content ) {

		// Make sure there's at least one tag
		if ( empty( $this->tags ) || ! is_array( $this->tags ) ) {
			return $content;
		}

		$new_content = preg_replace_callback( "/{([A-z0-9\-\_]+)}/s", array( $this, 'do_tag' ), $content );

		return $new_content;
	}

	/**
	 * Setup all registered email tags
	 *
	 * @since 1.6
	 * @return void
	 */
	private function setup_email_tags() {

		$tags = $this->get_tags();

		foreach( $tags as $tag ) {
			if ( isset( $tag['function'] ) && is_callable( $tag['function'] ) ) {
				$this->tags[ $tag['tag'] ] = $tag;
			}
		}

	}

	/**
	 * Retrieve all registered email tags
	 *
	 * @since 1.6
	 * @return array
	 */
	public function get_tags() {

		// Setup default tags array
		$email_tags = array(
			array(
				'tag'         => 'name',
				'description' => __( 'The display name of the affiliate, as set on the affiliate\'s user profile.', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_name'
			),
			array(
				'tag'         => 'user_name',
				'description' => __( 'The user name of the affiliate on the site', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_user_name'
			),
			array(
				'tag'         => 'user_email',
				'description' => __( 'The email address of the affiliate', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_user_email'
			),
			array(
				'tag'         => 'website',
				'description' => __( 'The website of the affiliate', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_website'
			),
			array(
				'tag'         => 'promo_method',
				'description' => __( 'The promo method used by the affiliate', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_promo_method'
			),
			array(
				'tag'         => 'rejection_reason',
				'description' => __( 'The reason an affiliate area was rejected', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_rejection_reason'
			),
			array(
				'tag'         => 'login_url',
				'description' => __( 'The affiliate login URL to your website', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_login_url'
			),
			array(
				'tag'         => 'amount',
				'description' => __( 'The amount of a given referral', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_amount'
			),
			array(
				'tag'         => 'site_name',
				'description' => __( 'Your site name', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_site_name'
			),
			array(
				'tag'         => 'referral_url',
				'description' => __( 'The affiliate&#8217;s referral URL', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_referral_url'
			),
			array(
				'tag'         => 'affiliate_id',
				'description' => __( 'The affiliate&#8217;s ID', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_affiliate_id'
			),
			array(
				'tag'         => 'referral_rate',
				'description' => __( 'The affiliate&#8217;s referral rate', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_referral_rate'
			),
			array(
				'tag'         => 'review_url',
				'description' => __( 'The URL to the review page for a pending affiliate', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_review_url'
			),
			array(
				'tag'         => 'landing_page',
				'description' => __( 'The URL the customer landed on that led to a referral being created', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_get_landing_page'
			),
			array(
				'tag'         => 'campaign_name',
				'description' => __( 'The name of the campaign associated with the referral (if any).', 'affiliate-wp' ),
				'function'    => 'affwp_email_tag_campaign_name'
			),
		);

		return apply_filters( 'affwp_email_tags', $email_tags, $this );

	}

	/**
	 * Parse a specific tag.
	 *
	 * @since 1.6
	 * @param $m Message
	 */
	private function do_tag( $m ) {

		// Get tag
		$tag = $m[1];

		// Return tag if not set
		if ( ! $this->email_tag_exists( $tag ) ) {
			return $m[0];
		}

		return call_user_func( $this->tags[$tag]['function'], $this->affiliate_id, $this->referral, $tag );
	}

	/**
	 * Check if $tag is a registered email tag
	 *
	 * @since 1.6
	 * @param string $tag Email tag that will be searched
	 * @return bool True if exists, false otherwise
	 */
	public function email_tag_exists( $tag ) {
		return array_key_exists( $tag, $this->tags );
	}

	/**
	 * Check if all emails should be disabled
	 *
	 * @since  1.7
	 * @return bool
	 */
	public function is_email_disabled() {
		$disabled = affiliate_wp()->settings->get( 'disable_all_emails', false );
		$disabled = (bool) apply_filters( 'affwp_disable_all_emails', $disabled );

		return $disabled;
	}

}
