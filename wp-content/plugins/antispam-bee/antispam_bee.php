<?php
/*
Plugin Name: Antispam Bee
Description: Easy and extremely productive spam-fighting plugin with many sophisticated solutions. Includes protection again trackback spam.
Author:      pluginkollektiv
Author URI:  http://pluginkollektiv.org
Plugin URI:  https://wordpress.org/plugins/antispam-bee/
Text Domain: antispam-bee
Domain Path: /lang
License:     GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Version:     2.6.8
*/

/*
Copyright (C)  2009-2015 Sergej Müller

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


/* Sicherheitsabfrage */
if ( ! class_exists('WP') ) {
	die();
}


/**
* Antispam_Bee
*
* @since   0.1
* @change  2.4
*/

class Antispam_Bee {


	/* Init */
	public static $defaults;
	private static $_base;
	private static $_secret;
	private static $_reason;


	/**
	* "Konstruktor" der Klasse
	*
	* @since   0.1
	* @change  2.6.4
	*/

  	public static function init()
  	{
  		/* Delete spam reason */
  		add_action(
  			'unspam_comment',
  			array(
  				__CLASS__,
  				'delete_spam_reason_by_comment'
  			)
  		);

		/* AJAX & Co. */
		if ( (defined('DOING_AJAX') && DOING_AJAX) or (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ) {
			return;
		}

		/* Initialisierung */
		self::_init_internal_vars();

		/* Cronjob */
		if ( defined('DOING_CRON') ) {
			add_action(
				'antispam_bee_daily_cronjob',
				array(
					__CLASS__,
					'start_daily_cronjob'
				)
			);

		/* Admin */
		} elseif ( is_admin() ) {
			/* Menü */
			add_action(
				'admin_menu',
				array(
					__CLASS__,
					'add_sidebar_menu'
				)
			);

			/* Dashboard */
			if ( self::_current_page('dashboard') ) {
				add_action(
					'init',
					array(
						__CLASS__,
						'load_plugin_lang'
					)
				);
				add_filter(
					'dashboard_glance_items',
					array(
						__CLASS__,
						'add_dashboard_count'
					)
				);
				add_action(
					'wp_dashboard_setup',
					array(
						__CLASS__,
						'add_dashboard_chart'
					)
				);

			/* Plugins */
			} else if ( self::_current_page('plugins') ) {
				add_action(
					'init',
					array(
						__CLASS__,
						'load_plugin_lang'
					)
				);
				add_filter(
					'plugin_row_meta',
					array(
						__CLASS__,
						'init_row_meta'
					),
					10,
					2
				);
				add_filter(
					'plugin_action_links_' .self::$_base,
					array(
						__CLASS__,
						'init_action_links'
					)
				);

			/* Optionen */
			} else if ( self::_current_page('options') ) {
				add_action(
					'admin_init',
					array(
						__CLASS__,
						'load_plugin_lang'
					)
				);
				add_action(
					'admin_init',
					array(
						__CLASS__,
						'init_plugin_sources'
					)
				);

			} else if ( self::_current_page('admin-post') ) {
				require_once( dirname(__FILE__). '/inc/gui.class.php' );

				add_action(
					'admin_post_ab_save_changes',
					array(
						'Antispam_Bee_GUI',
						'save_changes'
					)
				);

			} else if ( self::_current_page('edit-comments') ) {
				if ( ! empty($_GET['comment_status']) && $_GET['comment_status'] === 'spam' && ! self::get_option('no_notice') ) {
					/* Include file */
					require_once( dirname(__FILE__). '/inc/columns.class.php' );

					/* Load textdomain */
					self::load_plugin_lang();

					/* Add plugin columns */
					add_filter(
						'manage_edit-comments_columns',
						array(
							'Antispam_Bee_Columns',
							'register_plugin_columns'
						)
					);
					add_filter(
						'manage_comments_custom_column',
						array(
							'Antispam_Bee_Columns',
							'print_plugin_column'
						),
						10,
						2
					);
					add_filter(
					    'admin_print_styles-edit-comments.php',
					    array(
					    	'Antispam_Bee_Columns',
					    	'print_column_styles'
					    )
					);

					add_filter(
						'manage_edit-comments_sortable_columns',
						array(
							'Antispam_Bee_Columns',
							'register_sortable_columns'
						)
					);
					add_action(
						'pre_get_posts',
						array(
							'Antispam_Bee_Columns',
							'set_orderby_query'
						)
					);
				}
			}

		/* Frontend */
		} else {
			add_action(
				'template_redirect',
				array(
					__CLASS__,
					'prepare_comment_field'
				)
			);
			add_action(
				'init',
				array(
					__CLASS__,
					'precheck_incoming_request'
				)
			);
			add_action(
				'preprocess_comment',
				array(
					__CLASS__,
					'handle_incoming_request'
				),
				1
			);
			add_action(
				'antispam_bee_count',
				array(
					__CLASS__,
					'the_spam_count'
				)
			);
		}
	}




	############################
	########  INSTALL  #########
	############################


	/**
	* Aktion bei der Aktivierung des Plugins
	*
	* @since   0.1
	* @change  2.4
	*/

	public static function activate()
	{
		/* Option anlegen */
		add_option(
			'antispam_bee',
			array(),
			'',
			'no'
		);

		/* Cron aktivieren */
		if ( self::get_option('cronjob_enable') ) {
			self::init_scheduled_hook();
		}
	}


	/**
	* Aktion bei der Deaktivierung des Plugins
	*
	* @since   0.1
	* @change  2.4
	*/

	public static function deactivate()
	{
		self::clear_scheduled_hook();
	}


	/**
	* Aktion beim Löschen des Plugins
	*
	* @since   2.4
	* @change  2.4
	*/

	public static function uninstall()
	{
		/* Global */
		global $wpdb;

		/* Remove settings */
		delete_option('antispam_bee');

		/* Clean DB */
		$wpdb->query("OPTIMIZE TABLE `" .$wpdb->options. "`");
	}




	############################
	#########  INTERN  #########
	############################


	/**
	* Initialisierung der internen Variablen
	*
	* @since   2.4
	* @change  2.6.5
	*/

	private static function _init_internal_vars()
	{
		self::$_base   = plugin_basename(__FILE__);
		self::$_secret = substr(md5(get_bloginfo('url')), 0, 5). '-comment';

		self::$defaults = array(
			'options' => array(
				/* Allgemein */
				'advanced_check' 	=> 1,
				'regexp_check'		=> 1,
				'spam_ip' 			=> 1,
				'already_commented'	=> 1,
				'gravatar_check'	=> 0,
				'time_check'		=> 0,
				'ignore_pings' 		=> 0,
				'always_allowed' 	=> 0,

				'dashboard_chart' 	=> 0,
				'dashboard_count' 	=> 0,

				/* Filter */
				'dnsbl_check'		=> 0,
				'bbcode_check'		=> 1,

				/* Erweitert */
				'flag_spam' 		=> 1,
				'email_notify' 		=> 1,
				'no_notice' 		=> 0,
				'cronjob_enable' 	=> 0,
				'cronjob_interval'	=> 0,

				'ignore_filter' 	=> 0,
				'ignore_type' 		=> 0,

				'reasons_enable'	=> 0,
				'ignore_reasons'	=> array()
			),
			'reasons' => array(
				'css'		=> 'CSS Hack',
				'time'		=> 'Comment time',
				'empty'		=> 'Empty Data',
				'server'	=> 'Fake IP',
				'localdb'	=> 'Local DB Spam',
				'dnsbl'		=> 'DNSBL Spam',
				'bbcode'	=> 'BBCode',
				'regexp'	=> 'RegExp'
			)
		);
	}


	/**
	* Prüfung und Rückgabe eines Array-Keys
	*
	* @since   2.4.2
	* @change  2.4.2
	*
	* @param   array   $array  Array mit Werten
	* @param   string  $key    Name des Keys
	* @return  mixed           Wert des angeforderten Keys
	*/

	public static function get_key($array, $key)
	{
		if ( empty($array) or empty($key) or empty($array[$key]) ) {
			return null;
		}

		return $array[$key];
	}


	/**
	* Lokalisierung der Admin-Seiten
	*
	* @since   0.1
	* @change  2.4
	*
	* @param   string   $page  Kennzeichnung der Seite
	* @return  boolean         TRUE Bei Erfolg
	*/

	private static function _current_page($page)
	{
		switch ($page) {
			case 'dashboard':
				return ( empty($GLOBALS['pagenow']) or ( !empty($GLOBALS['pagenow']) && $GLOBALS['pagenow'] == 'index.php' ) );

			case 'options':
				return ( !empty($_GET['page']) && $_GET['page'] == 'antispam_bee' );

			case 'plugins':
				return ( !empty($GLOBALS['pagenow']) && $GLOBALS['pagenow'] == 'plugins.php' );

			case 'admin-post':
				return ( !empty($GLOBALS['pagenow']) && $GLOBALS['pagenow'] == 'admin-post.php' );

			case 'edit-comments':
				return ( !empty($GLOBALS['pagenow']) && $GLOBALS['pagenow'] == 'edit-comments.php' );

			default:
				return false;
		}
	}


	/**
	* Einbindung der Sprachdatei
	*
	* @since   0.1
	* @change  2.4
	*/

	public static function load_plugin_lang()
	{
		load_plugin_textdomain(
			'antispam-bee',
			false,
			'antispam-bee/lang'
		);
	}


	/**
	* Hinzufügen des Links zu den Einstellungen
	*
	* @since   1.1
	* @change  1.1
	*/

	public static function init_action_links($data)
	{
		/* Rechte? */
		if ( ! current_user_can('manage_options') ) {
			return $data;
		}

		return array_merge(
			$data,
			array(
				sprintf(
					'<a href="%s">%s</a>',
					add_query_arg(
						array(
							'page' => 'antispam_bee'
						),
						admin_url('options-general.php')
					),
					__('Settings', 'antispam-bee')
				)
			)
		);
	}


	/**
	* Meta-Links des Plugins
	*
	* @since   0.1
	* @change  2.6.2
	*
	* @param   array   $input  Bereits vorhandene Links
	* @param   string  $file   Aktuelle Seite
	* @return  array   $data   Modifizierte Links
	*/

	public static function init_row_meta($input, $file)
	{
		/* Rechte */
		if ( $file != self::$_base ) {
			return $input;
		}

		return array_merge(
			$input,
			array(
				'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LG5VC9KXMAYXJ" target="_blank">PayPal</a>',
			)
		);
	}




	############################
	#######  RESSOURCEN  #######
	############################


	/**
	* Registrierung von Ressourcen (CSS & JS)
	*
	* @since   1.6
	* @change  2.4.5
	*/

	public static function init_plugin_sources()
	{
		/* Infos auslesen */
		$plugin = get_plugin_data(__FILE__);

		/* JS einbinden */
		wp_register_script(
			'ab_script',
			plugins_url('js/scripts.min.js', __FILE__),
			array('jquery'),
			$plugin['Version']
		);

		/* CSS einbinden */
		wp_register_style(
			'ab_style',
			plugins_url('css/styles.min.css', __FILE__),
			array(),
			$plugin['Version']
		);
	}


	/**
	* Initialisierung der Optionsseite
	*
	* @since   0.1
	* @change  2.4.3
	*/

	public static function add_sidebar_menu()
	{
		/* Menü anlegen */
		$page = add_options_page(
			'Antispam Bee',
			'Antispam Bee',
			'manage_options',
			'antispam_bee',
			array(
				'Antispam_Bee_GUI',
				'options_page'
			)
		);

		/* JS einbinden */
		add_action(
			'admin_print_scripts-' . $page,
			array(
				__CLASS__,
				'add_options_script'
			)
		);

		/* CSS einbinden */
		add_action(
			'admin_print_styles-' . $page,
			array(
				__CLASS__,
				'add_options_style'
			)
		);

		/* PHP laden */
		add_action(
			'load-' .$page,
			array(
				__CLASS__,
				'init_options_page'
			)
		);
	}


	/**
	* Initialisierung von JavaScript
	*
	* @since   1.6
	* @change  2.4
	*/

	public static function add_options_script()
	{
		wp_enqueue_script('ab_script');
	}


	/**
	* Initialisierung von Stylesheets
	*
	* @since   1.6
	* @change  2.4
	*/

	public static function add_options_style()
	{
		wp_enqueue_style('ab_style');
	}


	/**
	* Einbindung der GUI
	*
	* @since   2.4
	* @change  2.4
	*/

	public static function init_options_page()
	{
		require_once( dirname(__FILE__). '/inc/gui.class.php' );
	}




	############################
	#######  DASHBOARD  ########
	############################


	/**
	* Anzeige des Spam-Counters auf dem Dashboard
	*
	* @since   0.1
	* @change  2.6.5
	*
	* @param   array  $items  Initial array with dashboard items
    * @return  array  $items  Merged array with dashboard items
	*/

	public static function add_dashboard_count( $items = array() )
	{
		/* Skip */
        if ( ! current_user_can('manage_options') OR ! self::get_option('dashboard_count') ) {
            return $items;
        }

        /* Icon styling */
        echo '<style>#dashboard_right_now .ab-count:before {content: "\f117"}</style>';

        /* Right now item */
        $items[] = sprintf(
        	'<a href="%s" class="ab-count">%s %s</a>',
        	add_query_arg(
        	    array(
        	        'page' => 'antispam_bee'
        	    ),
        	    admin_url('options-general.php')
        	),
        	esc_html( self::_get_spam_count() ),
        	esc_html__('Blocked', 'antispam-bee')
        );

        return $items;
	}


	/**
	* Initialisierung des Dashboard-Chart
	*
	* @since   1.9
	* @change  2.5.6
	*/

	public static function add_dashboard_chart()
	{
		/* Filter */
		if ( ! current_user_can('publish_posts') OR ! self::get_option('dashboard_chart') ) {
			return;
		}

		/* Widget hinzufügen */
		wp_add_dashboard_widget(
			'ab_widget',
			'Antispam Bee',
			array(
				__CLASS__,
				'show_spam_chart'
			)
		);

		/* JS laden */
		add_action(
			'wp_print_scripts',
			array(
				__CLASS__,
				'add_dashboard_script'
			)
		);

		/* CSS laden */
		add_action(
			'admin_head',
			array(
				__CLASS__,
				'add_dashboard_style'
			)
		);
	}


	/**
	* Print dashboard styles
	*
	* @since   1.9.0
	* @change  2.5.8
	*/

	public static function add_dashboard_style()
	{
		/* Get plugin data */
		$plugin = get_plugin_data(__FILE__);

		/* Register styles */
		wp_register_style(
			'ab_chart',
			plugins_url('css/dashboard.min.css', __FILE__),
			array(),
			$plugin['Version']
		);

		/* Embed styles */
  		wp_print_styles('ab_chart');
	}


	/**
	* Print dashboard scripts
	*
	* @since   1.9.0
	* @change  2.5.8
	*/

	public static function add_dashboard_script()
	{
		/* Get stats */
		if ( ! self::get_option('daily_stats') ) {
			return;
		}

		/* Get plugin data */
		$plugin = get_plugin_data(__FILE__);

		/* Register scripts */
		wp_register_script(
			'sm_raphael_js',
			plugins_url('js/raphael.min.js', __FILE__),
			array(),
			$plugin['Version'],
			true
		);
		wp_register_script(
			'sm_raphael_helper',
			plugins_url('js/raphael.helper.min.js', __FILE__),
			array(),
			$plugin['Version'],
			true
		);
		wp_register_script(
			'ab_chart_js',
			plugins_url('js/dashboard.min.js', __FILE__),
			array('jquery'),
			$plugin['Version'],
			true
		);

		/* Embed scripts */
		wp_enqueue_script('sm_raphael_js');
		wp_enqueue_script('sm_raphael_helper');
		wp_enqueue_script('ab_chart_js');
	}


	/**
	* Print dashboard html
	*
	* @since   1.9.0
	* @change  2.5.8
	*/

	public static function show_spam_chart()
	{
		/* Get stats */
		$items = (array)self::get_option('daily_stats');

		/* Emty array? */
		if ( empty($items) ) {
			echo sprintf(
				'<div id="ab_chart"><p>%s</p></div>',
				esc_html__('No data available.', 'antispam-bee')
			);

			return;
		}

		/* Sort stats */
		ksort($items, SORT_NUMERIC);

		/* HTML start */
		$html = "<table id=ab_chart_data>\n";


		/* Timestamp table */
		$html .= "<tfoot><tr>\n";
		foreach($items as $date => $count) {
			$html .= "<th>" .$date. "</th>\n";
		}
		$html .= "</tr></tfoot>\n";

		/* Counter table */
		$html .= "<tbody><tr>\n";
		foreach($items as $date => $count) {
			$html .= "<td>" .(int) $count. "</td>\n";
		}
		$html .= "</tr></tbody>\n";


		/* HTML end */
		$html .= "</table>\n";

		/* Print html */
		echo '<div id="ab_chart">' .$html. '</div>';
	}




	############################
	########  OPTIONS  #########
	############################


	/**
	* Get all plugin options
	*
	* @since   2.4
	* @change  2.6.1
	*
	* @return  array  $options  Array with option fields
	*/

	public static function get_options()
	{
		if ( ! $options = wp_cache_get('antispam_bee') ) {
			wp_cache_set(
				'antispam_bee',
				$options = get_option('antispam_bee')
			);
		}

		return wp_parse_args(
			$options,
			self::$defaults['options']
		);
	}


	/**
	* Get single option field
	*
	* @since   0.1
	* @change  2.4.2
	*
	* @param   string  $field  Field name
	* @return  mixed           Field value
	*/

	public static function get_option($field)
	{
		/* Get all options */
		$options = self::get_options();

		return self::get_key($options, $field);
	}


	/**
	* Update single option field
	*
	* @since   0.1
	* @change  2.4
	*
	* @param   string  $field  Field name
	* @param   mixed           Field value
	*/

	private static function _update_option($field, $value)
	{
		self::update_options(
			array(
				$field => $value
			)
		);
	}


	/**
	* Update multiple option fields
	*
	* @since   0.1
	* @change  2.6.1
	*
	* @param   array  $data  Array with plugin option fields
	*/

	public static function update_options($data)
	{
		/* Get options */
		$options = get_option('antispam_bee');

		/* Merge new data */
		if ( is_array($options) ) {
			$options = array_merge(
				$options,
				$data
			);
		} else {
			$options = $data;
		}

		/* Update options */
		update_option(
			'antispam_bee',
			$options
		);

		/* Refresh cache */
		wp_cache_set(
			'antispam_bee',
			$options
		);
	}




	############################
	########  CRONJOBS  ########
	############################


	/**
	* Ausführung des täglichen Cronjobs
	*
	* @since   0.1
	* @change  2.4
	*/

	public static function start_daily_cronjob()
	{
		/* Kein Cronjob? */
		if ( !self::get_option('cronjob_enable') ) {
			return;
		}

		/* Timestamp updaten */
		self::_update_option(
			'cronjob_timestamp',
			time()
		);

		/* Spam löschen */
		self::_delete_old_spam();
	}


	/**
	* Löschung alter Spamkommentare
	*
	* @since   0.1
	* @change  2.4
	*/

	private static function _delete_old_spam()
	{
		/* Anzahl der Tage */
		$days = (int)self::get_option('cronjob_interval');

		/* Kein Wert? */
		if ( empty($days) ) {
			return false;
		}

		/* Global */
		global $wpdb;

		/* Kommentare löschen */
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM `$wpdb->comments` WHERE `comment_approved` = 'spam' AND SUBDATE(NOW(), %d) > comment_date_gmt",
				$days
			)
		);

		/* DB optimieren */
		$wpdb->query("OPTIMIZE TABLE `$wpdb->comments`");
	}


	/**
	* Initialisierung des Cronjobs
	*
	* @since   0.1
	* @change  2.4
	*/

	public static function init_scheduled_hook()
	{
		if ( ! wp_next_scheduled('antispam_bee_daily_cronjob') ) {
			wp_schedule_event(
				time(),
				'daily',
				'antispam_bee_daily_cronjob'
			);
		}
	}


	/**
	* Löschung des Cronjobs
	*
	* @since   0.1
	* @change  2.4
	*/

	public static function clear_scheduled_hook()
	{
		if ( wp_next_scheduled('antispam_bee_daily_cronjob') ) {
			wp_clear_scheduled_hook('antispam_bee_daily_cronjob');
		}
	}




	############################
	######  SPAMPRÜFUNG  #######
	############################


	/**
	* Überprüfung der POST-Werte
	*
	* @since   0.1
	* @change  2.6.3
	*/

	public static function precheck_incoming_request()
	{
		/* Skip if not a comment request */
		if ( is_feed() OR is_trackback() OR empty($_POST) OR self::_is_mobile() ) {
			return;
		}

		/* Request params */
		$request_uri = self::get_key($_SERVER, 'REQUEST_URI');
		$request_path = parse_url($request_uri, PHP_URL_PATH);

		/* Request check */
		if ( strpos($request_path, 'wp-comments-post.php') === false ) {
			return;
		}

		/* Form fields */
		$hidden_field = self::get_key($_POST, 'comment');
		$plugin_field = self::get_key($_POST, self::$_secret);

		/* Hidden field check */
		if ( empty($hidden_field) && ! empty($plugin_field) ) {
			$_POST['comment'] = $plugin_field;
			unset( $_POST[self::$_secret] );
		} else {
			$_POST['ab_spam__hidden_field'] = 1;
		}
	}


	/**
	* Prüfung der eingehenden Anfragen auf Spam
	*
	* @since   0.1
	* @change  2.6.3
	*
	* @param   array  $comment  Unbehandelter Kommentar
	* @return  array  $comment  Behandelter Kommentar
	*/

	public static function handle_incoming_request($comment)
	{
		/* Add client IP */
		$comment['comment_author_IP'] = self::get_client_ip();

		/* Hook client IP */
		add_filter(
			'pre_comment_user_ip',
			array(
				__CLASS__,
				'get_client_ip'
			),
			1
		);

		/* Request params */
		$request_uri = self::get_key($_SERVER, 'REQUEST_URI');
		$request_path = parse_url($request_uri, PHP_URL_PATH);

		/* Empty path? */
		if ( empty($request_path) ) {
			return self::_handle_spam_request(
				$comment,
				'empty'
			);
		}

		/* Defaults */
		$ping = array(
			'types'   => array('pingback', 'trackback', 'pings'),
			'allowed' => !self::get_option('ignore_pings')
		);

		/* Is a comment */
		if ( strpos($request_path, 'wp-comments-post.php') !== false && ! empty($_POST) ) {
			/* Verify request */
			$status = self::_verify_comment_request($comment);

			/* Treat the request as spam */
			if ( ! empty($status['reason']) ) {
				return self::_handle_spam_request(
					$comment,
					$status['reason']
				);
			}

		/* Is a trackback */
		} else if ( in_array(self::get_key($comment, 'comment_type'), $ping['types']) && $ping['allowed'] ) {
			/* Verify request */
			$status = self::_verify_trackback_request($comment);

			/* Treat the request as spam */
			if ( ! empty($status['reason']) ) {
				return self::_handle_spam_request(
					$comment,
					$status['reason'],
					true
				);
			}
		}

		return $comment;
	}


	/**
	* Bereitet die Ersetzung des KOmmentarfeldes vor
	*
	* @since   0.1
	* @change  2.4
	*/

	public static function prepare_comment_field()
	{
		/* Nur Frontend */
		if ( is_feed() or is_trackback() or is_robots() or self::_is_mobile() ) {
			return;
		}

		/* Nur Beiträge */
		if ( !is_singular() && !self::get_option('always_allowed') ) {
			return;
		}

		/* Fire! */
		ob_start(
			array(
				'Antispam_Bee',
				'replace_comment_field'
			)
		);
	}


	/**
	* ersetzt das Kommentarfeld
	*
	* @since   2.4
	* @change  2.6.4
	*
	* @param   string  $data  HTML-Code der Webseite
	* @return  string         Behandelter HTML-Code
	*/

	public static function replace_comment_field($data)
	{
		/* Leer? */
		if ( empty($data) ) {
			return;
		}

		/* Find the comment textarea */
		if ( ! preg_match('#<textarea.+?name=["\']comment["\']#s', $data) ) {
			return $data;
		}

		/* Build init time field */
		if ( self::get_option('time_check') ) {
			$init_time_field = sprintf(
				'<input type="hidden" name="ab_init_time" value="%d" />',
				time()
			);
		} else {
			$init_time_field = '';
		}

		/* Inject HTML */
		return preg_replace(
			'#<textarea(.+?)name=["\']comment["\'](.+?)</textarea>#s',
            sprintf(
                '<textarea$1name="%s"$2</textarea><textarea name="comment" style="display:none" rows="1" cols="1"></textarea>%s',
                self::$_secret,
                $init_time_field
            ),
			$data,
			1
		);
	}


	/**
	* Prüfung der Trackbacks
	*
	* @since   2.4
	* @change  2.6.6
	*
	* @param   array  $comment  Daten des Trackbacks
	* @return  array            Array mit dem Verdachtsgrund [optional]
	*/

	private static function _verify_trackback_request($comment)
	{
		/* Kommentarwerte */
		$ip = self::get_key($comment, 'comment_author_IP');
		$url = self::get_key($comment, 'comment_author_url');
		$body = self::get_key($comment, 'comment_content');

		/* Leere Werte ? */
		if ( empty($url) OR empty($body) ) {
			return array(
				'reason' => 'empty'
			);
		}

		/* IP? */
		if ( empty($ip) ) {
			return array(
				'reason' => 'empty'
			);
		}

		/* Optionen */
		$options = self::get_options();

		/* BBCode Spam */
		if ( $options['bbcode_check'] && self::_is_bbcode_spam($body) ) {
			return array(
				'reason' => 'bbcode'
			);
		}

		/* IP != Server */
		if ( $options['advanced_check'] && self::_is_fake_ip($ip, parse_url($url, PHP_URL_HOST)) ) {
			return array(
				'reason' => 'server'
			);
		}

		/* IP im lokalen Spam */
		if ( $options['spam_ip'] && self::_is_db_spam($ip, $url) ) {
			return array(
				'reason' => 'localdb'
			);
		}

		/* DNSBL Spam */
		if ( $options['dnsbl_check'] && self::_is_dnsbl_spam($ip) ) {
			return array(
				'reason' => 'dnsbl'
			);
		}
	}


	/**
	* Prüfung den Kommentar
	*
	* @since   2.4
	* @change  2.6.5
	*
	* @param   array  $comment  Daten des Kommentars
	* @return  array            Array mit dem Verdachtsgrund [optional]
	*/

	private static function _verify_comment_request($comment)
	{
		/* Kommentarwerte */
		$ip = self::get_key($comment, 'comment_author_IP');
		$url = self::get_key($comment, 'comment_author_url');
		$body = self::get_key($comment, 'comment_content');
		$email = self::get_key($comment, 'comment_author_email');
		$author = self::get_key($comment, 'comment_author');

		/* Leere Werte ? */
		if ( empty($body) ) {
			return array(
				'reason' => 'empty'
			);
		}

		/* IP? */
		if ( empty($ip) ) {
			return array(
				'reason' => 'empty'
			);
		}

		/* Leere Werte ? */
		if ( get_option('require_name_email') && ( empty($email) OR empty($author) ) ) {
			return array(
				'reason' => 'empty'
			);
		}

		/* Optionen */
		$options = self::get_options();

		/* Bereits kommentiert? */
		if ( $options['already_commented'] && ! empty($email) && self::_is_approved_email($email) ) {
			return;
		}

		/* Check for a Gravatar */
		if ( $options['gravatar_check'] && ! empty($email) && self::_has_valid_gravatar($email) ) {
		    return;
		}

		/* Bot erkannt */
		if ( ! empty($_POST['ab_spam__hidden_field']) ) {
			return array(
				'reason' => 'css'
			);
		}

		/* Action time */
		if ( $options['time_check'] && self::_is_shortest_time() ) {
			return array(
				'reason' => 'time'
			);
		}

		/* BBCode Spam */
		if ( $options['bbcode_check'] && self::_is_bbcode_spam($body) ) {
			return array(
				'reason' => 'bbcode'
			);
		}

		/* Erweiterter Schutz */
		if ( $options['advanced_check'] && self::_is_fake_ip($ip) ) {
			return array(
				'reason' => 'server'
			);
		}

		/* Regexp für Spam */
		if ( $options['regexp_check'] && self::_is_regexp_spam(
			array(
				'ip'	 => $ip,
				'host'	 => parse_url($url, PHP_URL_HOST),
				'body'	 => $body,
				'email'	 => $email,
				'author' => $author
			)
		) ) {
			return array(
				'reason' => 'regexp'
			);
		}

		/* IP im lokalen Spam */
		if ( $options['spam_ip'] && self::_is_db_spam($ip, $url, $email) ) {
			return array(
				'reason' => 'localdb'
			);
		}

		/* DNSBL Spam */
		if ( $options['dnsbl_check'] && self::_is_dnsbl_spam($ip) ) {
			return array(
				'reason' => 'dnsbl'
			);
		}
	}


	/**
	* Check for a Gravatar image
	*
	* @since   2.6.5
	* @change  2.6.5
	*
	* @param   string	$email  Input email
	* @return  boolean       	Check status (true = Gravatar available)
	*/

    private static function _has_valid_gravatar($email) {
        $response = wp_safe_remote_get(
            sprintf(
                'https://www.gravatar.com/avatar/%s?d=404',
                md5( strtolower( trim($email) ) )
            )
        );

        if ( is_wp_error($response) ) {
            return null;
        }

        if ( wp_remote_retrieve_response_code($response) === 200 ) {
            return true;
        }

        return false;
    }


	/**
	* Check for comment action time
	*
	* @since   2.6.4
	* @change  2.6.4
	*
	* @return  boolean    TRUE if the action time is less than 5 seconds
	*/

	private static function _is_shortest_time()
	{
		/* Comment init time */
		if ( ! $init_time = (int)self::get_key($_POST, 'ab_init_time') ) {
			return false;
		}

		/* Compare time values */
		if ( time() - $init_time < apply_filters('ab_action_time_limit', 5) ) {
			return true;
		}

		return false;
	}


	/**
	* Anwendung von Regexp, auch benutzerdefiniert
	*
	* @since   2.5.2
	* @change  2.5.6
	*
	* @param   array	$comment  Array mit Kommentardaten
	* @return  boolean       	  TRUE bei verdächtigem Kommentar
	*/

	private static function _is_regexp_spam($comment)
	{
		/* Felder */
		$fields = array(
			'ip',
			'host',
			'body',
			'email',
			'author'
		);

		/* Regexp */
		$patterns = array(
			0 => array(
				'host'	=> '^(www\.)?\d+\w+\.com$',
				'body'	=> '^\w+\s\d+$',
				'email'	=> '@gmail.com$'
			),
			1 => array(
				'body'	=> '\<\!.+?mfunc.+?\>'
			)
		);

		/* Spammy author */
		if ( $quoted_author = preg_quote($comment['author'], '/') ) {
			$patterns[] = array(
				'body' => sprintf(
					'<a.+?>%s<\/a>$',
					$quoted_author
				)
			);
			$patterns[] = array(
				'body' => sprintf(
					'%s https?:.+?$',
					$quoted_author
				)
			);
			$patterns[] = array(
				'email'	 => '@gmail.com$',
				'author' => '^[a-z0-9-\.]+\.[a-z]{2,6}$',
				'host'	 => sprintf(
					'^%s$',
					$quoted_author
				)
			);
		}

		/* Hook */
		$patterns = apply_filters(
			'antispam_bee_patterns',
			$patterns
		);

		/* Leer? */
		if ( ! $patterns ) {
			return false;
		}

		/* Ausdrücke loopen */
		foreach ($patterns as $pattern) {
			$hits = array();

			/* Felder loopen */
			foreach ($pattern as $field => $regexp) {
				/* Empty value? */
				if ( empty($field) OR !in_array($field, $fields) OR empty($regexp) ) {
					continue;
				}

				/* Ignore non utf-8 chars */
				$comment[$field] = ( function_exists('iconv') ? iconv('utf-8', 'utf-8//TRANSLIT', $comment[$field]) : $comment[$field] );

				/* Empty value? */
				if ( empty($comment[$field]) ) {
					continue;
				}

				/* Perform regex */
				if ( @preg_match('/' .$regexp. '/isu', $comment[$field]) ) {
					$hits[$field] = true;
				}
			}

			if ( count($hits) === count($pattern) ) {
				return true;
			}
		}

		return false;
	}


	/**
	* Prüfung eines Kommentars auf seine Existenz im lokalen Spam
	*
	* @since   2.0.0
	* @change  2.5.4
	*
	* @param   string	$ip     Kommentar-IP
	* @param   string	$url    Kommentar-URL [optional]
	* @param   string	$email  Kommentar-Email [optional]
	* @return  boolean          TRUE bei verdächtigem Kommentar
	*/

	private static function _is_db_spam($ip, $url = '', $email = '')
	{
		/* Global */
		global $wpdb;

		/* Default */
		$filter = array('`comment_author_IP` = %s');
		$params = array( wp_unslash($ip) );

		/* URL abgleichen */
		if ( ! empty($url) ) {
			$filter[] = '`comment_author_url` = %s';
			$params[] = wp_unslash($url);
		}

		/* E-Mail abgleichen */
		if ( ! empty($email) ) {
			$filter[] = '`comment_author_email` = %s';
			$params[] = wp_unslash($email);
		}

		/* Query ausführen */
		$result = $wpdb->get_var(
			$wpdb->prepare(
				sprintf(
					"SELECT `comment_ID` FROM `$wpdb->comments` WHERE `comment_approved` = 'spam' AND (%s) LIMIT 1",
					implode(' OR ', $filter)
				),
				$params
			)
		);

		return !empty($result);
	}


	/**
	* Prüfung auf DNSBL Spam
	*
	* @since   2.4.5
	* @change  2.4.5
	*
	* @param   string   $ip  IP-Adresse
	* @return  boolean       TRUE bei gemeldeter IP
	*/

	private static function _is_dnsbl_spam($ip)
	{
		/* Start request */
		$response = wp_safe_remote_request(
			esc_url_raw(
				sprintf(
					'http://www.stopforumspam.com/api?ip=%s&f=json',
					$ip
				),
				'http'
			)
		);

		/* Response error? */
		if ( is_wp_error($response) ) {
			return false;
		}

		/* Get JSON */
		$json = wp_remote_retrieve_body($response);

		/* Decode JSON */
		$result = json_decode($json);

		/* Empty data */
		if ( empty($result->success) ) {
			return false;
		}

		/* Return status */
		return (bool) $result->ip->appears;
	}


	/**
	* Prüfung auf BBCode Spam
	*
	* @since   2.5.1
	* @change  2.5.1
	*
	* @param   string   $body  Inhalt eines Kommentars
	* @return  boolean         TRUE bei BBCode im Inhalt
	*/

	private static function _is_bbcode_spam($body)
	{
		return (bool) preg_match('/\[url[=\]].*\[\/url\]/is', $body);
	}


	/**
	* Prüfung auf eine bereits freigegebene E-Mail-Adresse
	*
	* @since   2.0
	* @change  2.5.1
	*
	* @param   string   $email  E-Mail-Adresse
	* @return  boolean          TRUE bei einem gefundenen Eintrag
	*/

	private static function _is_approved_email($email)
	{
		/* Global */
		global $wpdb;

		/* Suchen */
		$result = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `comment_ID` FROM `$wpdb->comments` WHERE `comment_approved` = '1' AND `comment_author_email` = %s LIMIT 1",
				wp_unslash($email)
			)
		);

		/* Gefunden? */
		if ( $result ) {
			return true;
		}

		return false;
	}


	/**
	* Check for a fake IP
	*
	* @since   2.0
	* @change  2.6.2
	*
	* @param   string   $ip    Client IP
	* @param   string   $host  Client Host [optional]
	* @return  boolean         TRUE if fake IP
	*/

	private static function _is_fake_ip($client_ip, $client_host = false)
	{
		/* Remote Host */
		$host_by_ip = gethostbyaddr($client_ip);

		/* IPv6 */
		if ( self::_is_ipv6($client_ip) ) {
			return $client_ip != $host_by_ip;
		}

		/* IPv4 / Comment */
		if ( empty($client_host) ) {
			$ip_by_host = gethostbyname($host_by_ip);

			if ( $ip_by_host === $host_by_ip ) {
				return false;
			}

		/* IPv4 / Trackback */
		} else {
			if ( $host_by_ip === $client_ip ) {
				return true;
			}

			$ip_by_host = gethostbyname($client_host);
		}

		if ( strpos( $client_ip, self::_cut_ip($ip_by_host) ) === false ) {
			return true;
		}

		return false;
	}


	/**
	* Kürzung der IP-Adressen
	*
	* @since   0.1
	* @change  2.5.1
	*
	* @param   string   $ip       Original IP
	* @param   boolean  $cut_end  Kürzen vom Ende?
	* @return  string             Gekürzte IP
	*/

	private static function _cut_ip($ip, $cut_end = true)
	{
		/* Trenner */
		$separator = ( self::_is_ipv4($ip) ? '.' : ':' );

		return str_replace(
			( $cut_end ? strrchr( $ip, $separator) : strstr( $ip, $separator) ),
			'',
			$ip
		);
	}


	/**
	* Anonymisierung der IP-Adressen
	*
	* @since   2.5.1
	* @change  2.5.1
	*
	* @param   string  $ip  Original IP
	* @return  string       Anonyme IP
	*/

	private static function _anonymize_ip($ip)
	{
		if ( self::_is_ipv4($ip) ) {
			return self::_cut_ip($ip). '.0';
		}

		return self::_cut_ip($ip, false). ':0:0:0:0:0:0:0';
	}


	/**
	* Dreht die IP-Adresse
	*
	* @since   2.4.5
	* @change  2.4.5
	*
	* @param   string   $ip  IP-Adresse
	* @return  string        Gedrehte IP-Adresse
	*/

	private static function _reverse_ip($ip)
	{
		return implode(
			'.',
			array_reverse(
				explode(
					'.',
					$ip
				)
			)
		);
	}


	/**
	* Check for an IPv4 address
	*
	* @since   2.4
	* @change  2.6.4
	*
	* @param   string   $ip  IP to validate
	* @return  integer       TRUE if IPv4
	*/

	private static function _is_ipv4($ip)
	{
		if ( function_exists('filter_var') ) {
			return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) !== false;
		} else {
			return preg_match('/^\d{1,3}(\.\d{1,3}){3,3}$/', $ip);
		}
	}


	/**
	* Check for an IPv6 address
	*
	* @since   2.6.2
	* @change  2.6.4
	*
	* @param   string   $ip  IP to validate
	* @return  boolean       TRUE if IPv6
	*/

	private static function _is_ipv6($ip)
	{
		if ( function_exists('filter_var') ) {
			return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) !== false;
		} else {
			return ! self::_is_ipv4($ip);
		}
	}


	/**
	* Prüfung auf Mobile
	*
	* @since   0.1
	* @change  2.4
	*
	* @return  boolean  TRUE, wenn "wptouch" aktiv ist
	*/

	private static function _is_mobile()
	{
		return strpos(TEMPLATEPATH, 'wptouch');
	}




	############################
	#####  SPAM-BEHANDLUNG  ####
	############################


	/**
	* Ausführung des Lösch-/Markier-Vorgangs
	*
	* @since   0.1
	* @change  2.6.0
	*
	* @param   array    $comment  Unbehandelte Kommentardaten
	* @param   string   $reason   Verdachtsgrund
	* @param   boolean  $is_ping  Ping (ja oder nein) [optional]
	* @return  array    $comment  Behandelte Kommentardaten
	*/

	private static function _handle_spam_request($comment, $reason, $is_ping = false)
	{
		/* Optionen */
		$options = self::get_options();

		/* Einstellungen */
		$spam_remove = !$options['flag_spam'];
		$spam_notice = !$options['no_notice'];

		/* Filter-Einstellungen */
		$ignore_filter = $options['ignore_filter'];
		$ignore_type = $options['ignore_type'];
		$ignore_reason = in_array($reason, (array)$options['ignore_reasons']);

		/* Spam merken */
		self::_update_spam_log($comment);
		self::_update_spam_count();
		self::_update_daily_stats();

		/* Spam löschen */
		if ( $spam_remove ) {
			self::_go_in_peace();
		}

		/* Typen behandeln */
		if ( $ignore_filter && (( $ignore_type == 1 && $is_ping ) or ( $ignore_type == 2 && !$is_ping )) ) {
			self::_go_in_peace();
		}

		/* Spamgrund */
		if ( $ignore_reason ) {
			self::_go_in_peace();
		}

		/* Spam-Grund */
		self::$_reason = $reason;

		/* Spam markieren */
		add_filter(
			'pre_comment_approved',
			create_function(
				'',
				'return "spam";'
			)
		);

		/* E-Mail senden */
		add_filter(
			'trackback_post',
			array(
				__CLASS__,
				'send_mail_notification'
			)
		);
		add_filter(
			'comment_post',
			array(
				__CLASS__,
				'send_mail_notification'
			)
		);

		/* Spam reason as comment meta */
		if ( $spam_notice ) {
			add_filter(
				'comment_post',
				array(
					__CLASS__,
					'add_spam_reason_to_comment'
				)
			);
		}

		return $comment;
	}


	/**
	* Logfile mit erkanntem Spam
	*
	* @since   2.5.7
	* @change  2.6.1
	*
	* @param   array   $comment  Array mit Kommentardaten
	* @return  mixed   			 FALSE im Fehlerfall
	*/

	private static function _update_spam_log($comment)
	{
		/* Skip logfile? */
		if ( ! defined('ANTISPAM_BEE_LOG_FILE') OR ! ANTISPAM_BEE_LOG_FILE OR ! is_writable(ANTISPAM_BEE_LOG_FILE) OR validate_file(ANTISPAM_BEE_LOG_FILE) === 1 ) {
			return false;
		}

		/* Compose entry */
		$entry = sprintf(
			'%s comment for post=%d from host=%s marked as spam%s',
			current_time('mysql'),
			$comment['comment_post_ID'],
			$comment['comment_author_IP'],
			PHP_EOL
		);

		/* Write */
		file_put_contents(
			ANTISPAM_BEE_LOG_FILE,
			$entry,
			FILE_APPEND | LOCK_EX
		);
	}


	/**
	* Sendet den 403-Header und beendet die Verbindung
	*
	* @since   2.5.6
	* @change  2.5.6
	*/

	private static function _go_in_peace()
	{
		status_header(403);
		die('Spam deleted.');
	}


	/**
	* Return real client IP
	*
	* @since   2.6.1
	* @change  2.6.1
	*
	* @return  mixed  $ip  Client IP
	*/

	public static function get_client_ip()
	{
		if ( isset($_SERVER['HTTP_CLIENT_IP']) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if ( isset($_SERVER['HTTP_X_FORWARDED']) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED'];
		} else if ( isset($_SERVER['HTTP_FORWARDED_FOR']) ) {
			$ip = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if ( isset($_SERVER['HTTP_FORWARDED']) ) {
			$ip = $_SERVER['HTTP_FORWARDED'];
		} else if ( isset($_SERVER['REMOTE_ADDR']) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			return '';
		}

		if ( strpos($ip, ',') !== false ) {
			$ips = explode(',', $ip);
			$ip = trim(@$ips[0]);
		}

		if ( function_exists('filter_var') ) {
			return filter_var(
				$ip,
				FILTER_VALIDATE_IP
			);
		}

		return preg_replace(
			'/[^0-9a-f:\., ]/si',
			'',
			$ip
		);
	}


	/**
	* Add spam reason as comment data
	*
	* @since   2.6.0
	* @change  2.6.0
	*
	* @param   integer  $comment_id  Comment ID
	*/

	public static function add_spam_reason_to_comment( $comment_id )
	{
		add_comment_meta(
			$comment_id,
			'antispam_bee_reason',
			self::$_reason
		);
	}


	/**
	* Delete spam reason as comment data
	*
	* @since   2.6.0
	* @change  2.6.0
	*
	* @param   integer  $comment_id  Comment ID
	*/

	public static function delete_spam_reason_by_comment( $comment_id )
	{
		delete_comment_meta(
			$comment_id,
			'antispam_bee_reason'
		);
	}


	/**
	* Versand einer Benachrichtigung via E-Mail
	*
	* @since   0.1
	* @change  2.5.7
	*
	* @hook    string  antispam_bee_notification_subject  Custom subject for notification mails
	*
	* @param   intval  $id  ID des Kommentars
	* @return  intval  $id  ID des Kommentars
	*/

	public static function send_mail_notification($id)
	{
		/* Optionen */
		$options = self::get_options();

		/* Keine Benachrichtigung? */
		if ( !$options['email_notify'] ) {
			return $id;
		}

		/* Kommentar */
		$comment = get_comment($id, ARRAY_A);

		/* Keine Werte? */
		if ( empty($comment) ) {
			return $id;
		}

		/* Parent-Post */
		if ( ! $post = get_post($comment['comment_post_ID']) ) {
			return $id;
		}

		/* Sprache laden */
		self::load_plugin_lang();

		/* Betreff */
		$subject = sprintf(
			'[%s] %s',
			stripslashes_deep(
				html_entity_decode(
					get_bloginfo('name'),
					ENT_QUOTES
				)
			),
			__('Comment marked as spam', 'antispam-bee')
		);

		/* Content */
		if ( !$content = strip_tags(stripslashes($comment['comment_content'])) ) {
			$content = sprintf(
				'-- %s --',
				__('Content removed by Antispam Bee', 'antispam-bee')
			);
		}

		/* Body */
		$body = sprintf(
			"%s \"%s\"\r\n\r\n",
			__('New spam comment on your post', 'antispam-bee'),
			strip_tags($post->post_title)
		).sprintf(
			"%s: %s\r\n",
			__('Author', 'antispam-bee'),
			( empty($comment['comment_author']) ? '' : strip_tags($comment['comment_author']) )
		).sprintf(
			"URL: %s\r\n",
			esc_url($comment['comment_author_url']) /* empty check exists */
		).sprintf(
			"%s: %s\r\n",
			__('Type', 'antispam-bee'),
			__( ( empty($comment['comment_type']) ? 'Comment' : 'Trackback' ), 'antispam-bee' )
		).sprintf(
			"Whois: http://whois.arin.net/rest/ip/%s\r\n",
			$comment['comment_author_IP']
		).sprintf(
			"%s: %s\r\n\r\n",
			__('Spam Reason', 'antispam-bee'),
			__(self::$defaults['reasons'][self::$_reason], 'antispam-bee')
		).sprintf(
			"%s\r\n\r\n\r\n",
			$content
		).(
			EMPTY_TRASH_DAYS ? (
				sprintf(
					"%s: %s\r\n",
					__('Trash it', 'antispam-bee'),
					admin_url('comment.php?action=trash&c=' .$id)
				)
			) : (
				sprintf(
					"%s: %s\r\n",
					__('Delete it', 'antispam-bee'),
					admin_url('comment.php?action=delete&c=' .$id)
				)
			)
		).sprintf(
				"%s: %s\r\n",
			__('Approve it', 'antispam-bee'),
			admin_url('comment.php?action=approve&c=' .$id)
		).sprintf(
			"%s: %s\r\n\r\n",
			__('Spam list', 'antispam-bee'),
			admin_url('edit-comments.php?comment_status=spam')
		).sprintf(
			"%s\r\n%s\r\n",
			__('Notify message by Antispam Bee', 'antispam-bee'),
			__('http://antispambee.com', 'antispam-bee')
		);

		/* Send */
		wp_mail(
			get_bloginfo('admin_email'),
			apply_filters(
				'antispam_bee_notification_subject',
				$subject
			),
			$body
		);

		return $id;
	}




	############################
	#######  STATISTIK  ########
	############################


	/**
	* Rückgabe der Anzahl von Spam-Kommentaren
	*
	* @since   0.1
	* @change  2.4
	*
	* @param   intval  $count  Anzahl der Spam-Kommentare
	*/

	private static function _get_spam_count()
	{
		/* Init */
		$count = self::get_option('spam_count');

		/* Fire */
		return ( get_locale() == 'de_DE' ? number_format($count, 0, '', '.') : number_format_i18n($count) );
	}


	/**
	* Ausgabe der Anzahl von Spam-Kommentaren
	*
	* @since   0.1
	* @change  2.4
	*/

	public static function the_spam_count()
	{
		echo esc_html( self::_get_spam_count() );
	}


	/**
	* Aktualisierung der Anzahl von Spam-Kommentaren
	*
	* @since   0.1
	* @change  2.6.1
	*/

	private static function _update_spam_count()
	{
		/* Skip if not enabled */
		if ( ! self::get_option('dashboard_count') ) {
			return;
		}

		self::_update_option(
			'spam_count',
			intval( self::get_option('spam_count') + 1 )
		);
	}


	/**
	* Aktualisierung der Statistik
	*
	* @since   1.9
	* @change  2.6.1
	*/

	private static function _update_daily_stats()
	{
		/* Skip if not enabled */
		if ( ! self::get_option('dashboard_chart') ) {
			return;
		}

		/* Init */
		$stats = (array)self::get_option('daily_stats');
		$today = (int)strtotime('today');

		/* Hochzählen */
		if ( array_key_exists($today, $stats) ) {
			$stats[$today] ++;
		} else {
			$stats[$today] = 1;
		}

		/* Sortieren */
		krsort($stats, SORT_NUMERIC);

		/* Speichern */
		self::_update_option(
			'daily_stats',
			array_slice($stats, 0, 31, true)
		);
	}
}


/* Fire */
add_action(
	'plugins_loaded',
	array(
		'Antispam_Bee',
		'init'
	)
);

/* Activation */
register_activation_hook(
	__FILE__,
	array(
		'Antispam_Bee',
		'activate'
	)
);

/* Deactivation */
register_deactivation_hook(
	__FILE__,
	array(
		'Antispam_Bee',
		'deactivate'
	)
);

/* Uninstall */
register_uninstall_hook(
	__FILE__,
	array(
		'Antispam_Bee',
		'uninstall'
	)
);
