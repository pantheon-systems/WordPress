=== External Database for WP Security Audit Log ===
Contributors: WPWhiteSecurity, robert681
Plugin URI: http://www.wpsecurityauditlog.com/extensions/audit-log-external-database/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Requires at least: 3.6
Tested up to: 4.7
Stable tag: 1.2.0

Mirroring, Archiving and External database solution for the WordPress audit trail.

== Description ==
The External Database Add-On [WP Security Audit Log WordPress plugin](https://wordpress.org/plugins/wp-security-audit-log/) enables you to

* Store the WordPress Audit Trail in an external database rather than in the WordPress database itself.
* Mirror the Audit Trail to third party logging solutions such as Syslog and Papertrail.
* Archive alerts to a secondary database so the main audit trail is kept small and fast.

By storing the Audit Log in an external database you ensure that the WordPress performance is not affected and comply with most common regulatory compliance requirements such as PCI and HIPAA.

== Installation ==

1. Upload the `external-db-wsal` folder to the `/wp-content/plugins/` directory
2. Activate the External Database for WP Security Audit Log plugin from the 'Plugins' menu in the WordPress Administration Screens
3. Configure the external database connection details.

== Changelog ==

= 1.2 (2017-01-03) =
	* **New Features:**
	* [Mirroring of audit trail to Syslog](https://www.wpsecurityauditlog.com/wordpress-user-monitoring-plugin-documentation/faq-mirroring-wordpress-audit-trail-syslog/)
	* [Mirroring  of audit trail to Papertrail](https://www.wpsecurityauditlog.com/wordpress-user-monitoring-plugin-documentation/faq-mirroring-wordpress-audit-trail-papertrail/)
	* [Archiving of alerts from the audit trail to an external database](https://www.wpsecurityauditlog.com/wordpress-user-monitoring-plugin-documentation/faq-archiving-wordpress-audit-trail/).	
	
= 1.1 (2016-04-26) =
	* Added AJAX calls to DB migration - supporting bigger migrations.

= 1.0 (2015-08-15) =
	* Initial release of External Database for WP Security Audit Log
