<?php
namespace AffWP\CLI;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AffiliateWP WP-CLI Commands.
 *
 * @since 1.9
 *
 * @see \WP_CLI_Command
 */
class Command extends \WP_CLI_Command {

	/**
	 * Prints information about AffiliateWP.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function details( $_, $assoc_args ) {
		if ( ! class_exists( 'Affiliate_WP' ) ) {
			\WP_CLI::error( __( 'AffiliateWP is not installed', 'affiliate-wp' ) );
		}

		// Version.
		if ( defined( 'AFFILIATEWP_VERSION' ) ) {
			self::format_line( __( 'You are running AffiliateWP version: %s', 'affiliate-wp' ), AFFILIATEWP_VERSION, '%_' );
		}

		// License Status (+ line break).
		\WP_CLI::line();

			if ( 'valid' == affiliate_wp()->settings->get( 'license_status' ) ) {
				$license_status = '%G' . _x( 'Valid', 'license status', 'affiliate-wp' ) . '%N';
			} else {
				$license_status = '%R' . _x( 'Invalid', 'license status', 'affiliate-wp' ) . '%N';
			}

			self::format_line( __( 'License status is: %s', 'affiliate-wp' ), $license_status );

			// Debug Mode.
			if ( affiliate_wp()->settings->get( 'debug_mode' ) ) {
				$debug_mode = '%G' . _x( 'Enabled', 'debug_mode', 'affiliate-wp' ) . '%N';
			} else {
				$debug_mode = '%R' . _x( 'Disabled', 'debug mode', 'affiliate-wp' ) . '%N';
			}

			self::format_line( __( 'Debug mode is: %s', 'affiliate-wp' ), $debug_mode );

			// Affiliates Require Approval.
			if ( affiliate_wp()->settings->get( 'require_approval' ) ) {
				$require_approval = '%G' . _x( 'Enabled', 'affiliates require approval', 'affiliate-wp' ) . '%N';
			} else {
				$require_approval = '%R' . _x( 'Disabled', 'affiliates require approval', 'affiliate-wp' ) . '%N';
			}

			self::format_line( __( 'Affiliates require approval: %s', 'affiliate-wp' ), $require_approval );

			/*
			 * Notifications enabled/disabled.
			 *
			 * Remember, it's backward logic.
			 */
			if ( ! affiliate_wp()->settings->get( 'disable_all_emails' ) ) {
				$notifications_disabled = '%G' . _x( 'Enabled', 'emails disabled', 'affiliate-wp' ) . '%N';
			} else {
				$notifications_disabled = '%R' . _x( 'Disabled', 'emails disabled', 'affiliate-wp' ) . '%N';
			}

			self::format_line( __( 'Email notifications are: %s', 'affiliate-wp' ), $notifications_disabled );

		// Integrations, global referral rate and rate type, and currency (+ line break).
		\WP_CLI::line();

			$integrations = affiliate_wp()->settings->get( 'integrations' );

			if ( ! $integrations ) {
				$integrations = _x( 'None', 'integrations', 'affiliate-wp' );
			} else {
				$integrations = implode( ', ', array_values( $integrations ) );
			}

			self::format_line( __( 'Integrations: %s', 'affiliate-wp' ), $integrations, '%_' );

			// Referral rate and type.
			$global_rate = affiliate_wp()->settings->get( 'referral_rate', 20 );
			$global_rate_type = affiliate_wp()->settings->get( 'referral_rate_type', 'percentage' );

			// Global referral rate.
			self::format_line( __( 'Global referral rate: %s', 'affiliate-wp' ), $global_rate, '%_' );

			// Global referral rate type.
			self::format_line( __( 'Global referral rate type: %s', 'affiliate-wp' ), $global_rate_type, '%_' );

			// Currency.
			self::format_line( __( 'Currency: %s', 'affiliate-wp' ), affwp_get_currency(), '%_' );

		// Referral Variable (+ line break).
		\WP_CLI::line();

			$referral_var = affiliate_wp()->settings->get( 'referral_var', 'ref' );

			self::format_line( __( 'Referral variable: %s', 'affiliate-wp' ), $referral_var, '%_' );

			// Show pretty referrals.
			if ( affwp_is_pretty_referral_urls() ) {
				$show_pretty_referrals = '%G' . _x( 'Enabled', 'show pretty referral URLs', 'affiliate-wp' ) . '%N';
			} else {
				$show_pretty_referrals = '%R' . _x( 'Disabled', 'show pretty referral URLs', 'affiliate-wp' ) . '%N';
			}

			self::format_line( __( 'Show pretty referral URLs: %s', 'affiliate-wp' ), $show_pretty_referrals );

			// Referral URLs.
			if ( affwp_is_pretty_referral_urls() ) {
				$referral_url_id = "https://example.org/{$referral_var}/123";
				$referral_url_username = "https://example.org/{$referral_var}/username";
			} else {
				$referral_url_id = "https://example.org/?{$referral_var}=123";
				$referral_url_username = "https://example.org/?{$referral_var}=username";
			}

			// ID referral URL.
			self::format_line( __( 'Referral URL (ID): %s', 'affiliate-wp' ), $referral_url_id, '%_' );

			// Username referral URL.
			self::format_line( __( 'Referral URL (Username): %s', 'affiliate-wp' ), $referral_url_username, '%_' );

		// Affiliate Area URL (+ line break).
		\WP_CLI::line();

			self::format_line( __( 'Affiliate Area URL: %s', 'affiliate-wp' ), affwp_get_affiliate_area_page_url(), '%_' );

			// Terms of Use page URL.
			$terms_of_use_page_id = affiliate_wp()->settings->get( 'terms_of_use' );
			if ( $terms_of_use_url = get_permalink( $terms_of_use_page_id ) ) {
				self::format_line( __( 'Terms of Use URL: %s', 'affiliate-wp' ), $terms_of_use_url, '%_' );
			}

		\WP_CLI::line();
	}

	/**
	 * Displays count totals for all affiliates, creatives, payouts, referrals, and visits.
	 *
	 * ## OPTIONS
	 *
	 * [--global]
	 * : Whether to retrieve global stats from a multisite network.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function stats( $args, $assoc_args ) {
		$affiliate_count = $referral_count = $visit_count = 0;

		$global = \WP_CLI\Utils\get_flag_value( $assoc_args, 'global' );

		if ( is_multisite() && $global ) {
			// wp_is_large_network() is handled in wp_get_sites().
			$sites = wp_list_pluck( wp_get_sites( array( 'limit' => 9999 ) ), 'blog_id' );
		} else {
			$sites = array( get_current_blog_id() );
		}

		$count = count( $sites );

		if ( $count > 1 ) {
			self::format_line( __( 'Found %s sites. Retrieving stats ...', 'affiliate-wp' ), $count, '%_' );
		}

		foreach ( $sites as $site_id ) {
			if ( $count > 1 && $global ) {
				switch_to_blog( $site_id );
			}
			$affiliate_count = $affiliate_count + affiliate_wp()->affiliates->count();
			$creative_count  = $creative_count  + affiliate_wp()->creatives->count();
			$payout_count    = $payout_count    + affiliate_wp()->affiliates->payouts->count();
			$referral_count  = $referral_count  + affiliate_wp()->referrals->count();
			$visit_count     = $visit_count     + affiliate_wp()->visits->count();

			if ( $count > 1 && $global ) {
				restore_current_blog();
			}
		}

		// Affiliates.
		self::format_line( __( 'Total Affiliates: %s', 'affiliate-wp' ), $affiliate_count, '%_' );

		// Creatives.
		self::format_line( __( 'Total Creatives: %s', 'affiliate-wp' ), $creative_count, '%_' );

		// Payouts.
		self::format_line( __( 'Total Payouts: %s', 'affiliate-wp' ), $payout_count, '%_' );

		// Referrals.
		self::format_line( __( 'Total Referrals: %s', 'affiliate-wp' ), $referral_count, '%_' );

		// Visits.
		self::format_line( __( 'Total Visits: %s', 'affiliate-wp' ), $visit_count, '%_' );
	}

	/**
	 * Displays the contents of the AffWP debug log.
	 *
	 * ## OPTIONS
	 *
	 * [--clear]
	 * : Whether to clear the debug log. Requires confirmation.
	 *
	 * @subcommand debug
	 */
	public function debug_log( $_, $assoc_args ) {
		// Retain the logger instance for back-compat.
		$logger = affiliate_wp()->utils->logs;

		$clear  = \WP_CLI\Utils\get_flag_value( $assoc_args, 'clear', false );

		if ( $clear ) {
			\WP_CLI::confirm( __( 'Are you sure you want to clear the debug log?', 'affiliate-wp' ), $assoc_args );

			// Clear the log.
			affiliate_wp()->utils->logs->clear_log();

			\WP_CLI::success( __( 'The debug log has been cleared.', 'affiliate-wp' ) );
		}
		echo "\n" . $logger->get_log();
	}

	/**
	 * Dumps out the System Info log for debugging purposes.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @subcommand info
	 */
	public function system_info( $_, $assoc_args ) {
		\WP_CLI::log( affwp_tools_system_info_report() );
	}

	/**
	 * Serves as a shorthand wrapper for \WP_CLI::line() combined with \WP_CLI::colorize().
	 *
	 * @since 1.9
	 * @access protected
	 * @static
	 *
	 * @param string $text        Base text with specifier.
	 * @param mixed  $replacement Replacement text used for sprintf().
	 * @param string $color       Optional. Color code. See \WP_CLI::colorize(). Default empty.
	 */
	protected static function format_line( $text, $replacement, $color = '' ) {
		\WP_CLI::line( sprintf( $text, \WP_CLI::colorize( $color . $replacement . '%N' ) ) );
	}
}

\WP_CLI::add_command( 'affwp', 'AffWP\CLI\Command' );
