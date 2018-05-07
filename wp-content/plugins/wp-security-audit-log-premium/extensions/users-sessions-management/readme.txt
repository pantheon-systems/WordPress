=== Users Sessions Management for WP Security Audit Log ===
Contributors: WPWhiteSecurity, robert681
Plugin URI: http://www.wpsecurityauditlog.com/extensions/user-sessions-management-wp-security-audit-log/ 
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Requires at least: 3.6
Tested up to: 4.8.2
Stable tag: 1.0.6

See who is logged in to your WordPress and manage users' session.

== Description ==
The Users Sessions Management for WP Security Audit Log is an extension to [WP Security Audit Log WordPress plugin](https://wordpress.org/plugins/wp-security-audit-log/) allows you to see who is logged in to your WordPress and WordPress multisite network and should you wish terminate their session. The plugin also allows you to limit a WordPress user from being used in multiple sessions and be alerted via email on the blocked and allowed users sessions.

== Installation ==

1. Upload the `users-sessions-management-wsal` folder to the `/wp-content/plugins/` directory.
2. Activate the Users Sessions Management for WP Security Audit Log plugin from the 'Plugins' menu in the WordPress dashboard.
3. Navigate to the new Users Sessions Management node in the WP Security Audit Log plugin menu.

== Changelog ==

=1.0.6 (2017-09-28]
	* Fixed a problem in which the IP address was not reported correctly when WordPress was running behind a WAF or reverse proxy.
	
= 1.0.5 (2017-07-18]
	* Introduced Alert 1007 that is reported when the administrator terminates another users' session via this add-on.

= 1.0.4 (2017-04-22) =
	* Update add-on to support new functionality in main plugin WP Security Audit Log. 

= 1.0.3 (2016-11-09) =
	* Update timestamp and Date & time format so they are always the same as the ones configured in WordPress. 

= 1.0.2 (2016-08-04) =
	* Supporting environments where users' sessions are double serialized in the database.
	
= 1.0.1 (2016-07-13) =
	* Updated email module to support different FROM email address - configurable from main plugin.

= 1.0.0 (2016-06-01) =
	* Initial release of Users Sessions Management for WP Security Audit Log
