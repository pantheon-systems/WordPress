=== Search for WP Security Audit Log ===
Contributors: WPWhiteSecurity, robert681
Plugin URI: http://www.wpsecurityauditlog.com/extensions/search-add-on-for-wordpress-security-audit-log/ 
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Requires at least: 3.6
Tested up to: 4.7
Stable tag: 1.1.5

Automatically search for WordPress Security Alerts in the WordPress Audit Log using free-text based searches and filters.

== Description ==
Search for WP Security Audit Log is an extension to [WP Security Audit Log WordPress plugin](https://wordpress.org/plugins/wp-security-audit-log/) which allows you automatically search for WordPress Security Alerts in the WordPress Audit Log by using both free text based searches and filtering rules.

== Installation ==

1. Upload the `search-wsal` folder to the `/wp-content/plugins/` directory
2. Activate the Search for WP Security Audit Log plugin from the 'Plugins' menu in the WordPress Administration Screens
3. A search box is added on the top right corner of the Audit Log in WP Security Audit Log.

== Changelog ==

= 1.1.5 (2017-01-03)
  * Support for the [archiving database](https://www.wpsecurityauditlog.com/wordpress-user-monitoring-plugin-documentation/faq-archiving-wordpress-audit-trail/) therefore the add-on can search for data in alerts stored in the archiving database.

= 1.1.4 (2016-11-09) =
	* Added dynamic help text in dates filters instruct user of configured date format in WordPress.
	* Plugin now uses the date format configures in WordPress for filters, search and also results.

= 1.1.3 (2016-08-20) =
	* Improved the handling of the error when the main plugin is not enabled.

= 1.1.2 (2015-10-08) =
	* Added new functionality to search by Alert ID without using filters.
	* Updated license notifiation (when license is activated the notification is automatically dismissed).
	* Fixed an issue where some specific IP addresses could not be added in filters.

= 1.1.1 (2015-08-05) =
	* Renamed Add-On.
	* Updated links to new website.

= 1.1.0 (2015-07-16) =
	* Updated plugin to use new database connector in WP Security Audit Log.

= 1.0.0 (2014-08-12) =
	* Initial release of WSAL Search extension.
