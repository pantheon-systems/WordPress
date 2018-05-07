=== Reports for WP Security Audit Log ===
Contributors: WPWhiteSecurity, robert681
Plugin URI: https://www.wpsecurityauditlog.com/extensions/compliance-reports-add-on-for-wordpress/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Requires at least: 3.6
Tested up to: 4.8.2
Stable tag: 2.1.2

Generate reports to meet legal and regulatory compliance requirements, and keep track of users' productivity.

== Description ==
Reports for WP Security Audit Log is an extension for [WP Security Audit Log WordPress plugin](https://wordpress.org/plugins/wp-security-audit-log/), WordPress' most comprehensive and popular security monitoring and auditing plugin. WSAL Reporting Extension allows you to generate reports from the audit log of WP Security Audit Log plugin. Unlike other WordPress reporting tools and plugins, WSAL Reporting extension allows you to generate any type of report about any type of activity rather than having inbuilt report templates that restrict you to specific reports. You can generate reports per user or group of users, user roles, site, type of activity etc. 

== Installation ==

1. Upload the `reports-wsal` folder to the `/wp-content/plugins/` directory
2. Activate the Reports for WP Security Audit Log plugin from the 'Plugins' menu in the WordPress Administration Screens
3. Access the Reporting entry in the WP Security Audit Log plugin menu to generate any type of report you need.

== Changelog ==

= 2.1.2 (2017-09-20) =
* **Bug Fix**
	* Fixed several issues with the scheduler / automatic sending of reports

= 2.1.1 (2017-07-29) =
* **Improvements**
	* Improved the code to load wp-config.php 
	* Removed the code for the WP_Session cookie from the generate report function
	* Removed error log function
	* Moved the \uploads\reports\ directory to \uploads\wp-security-audit-log\reports\

= 2.1.0 (2017-06-01) =
* **New Features**
	* [New & fully configurable Periodic reports](https://www.wpsecurityauditlog.com/wordpress-user-monitoring-plugin-releases/statistics-scheduled-wordpress-reports/).
	* Periodic reports can be configured to be sent daily, weekly, monthly and quarterly.
	* New Statistics Reports.
	* New reports criteria.
	
* **Improvements**
	* Added a criteria for WooCommerce report so you can generate a report for all WooCommerce changes.

= 2.0.10 (2017-04-22) =
	* Updated plugin to support new WordPress audit trail alerts categorisation.

= 2.0.9 (2017-03-09) =
	* Removed non logged in user cookie handling to support latest update of WP Security Audit Log
 
= 2.0.8 (2017-02-25) =
	* Fixed an issue with username autocomplete. Now the exact match needs to be typed in.

= 2.0.7 (2017-01-16) =
	* Set a limit on auto-suggestion so the plugin works better on websites with thousands of users, websites etc.

= 2.0.6 (2017-01-03) =
	* Support for the [archiving database](https://www.wpsecurityauditlog.com/wordpress-user-monitoring-plugin-documentation/faq-archiving-wordpress-audit-trail/) therefore reports can be generated for alerts in the archiving database as well.
  
= 2.0.5 (2016-11-09) =
	* Added dynamic help text in Dates inputs to instruct user which date format to use.
	* Timestamps, date and time formats in reports now are the same as those configured in WordPress.

= 2.0.4 (2016-09-30) =
	* Added date validation and help text
	
= 2.0.3 (2016-07-12) =
	* Updated email module to support different FROM email address - configurable from main plugin.
	
= 2.0.2 (2016-06-01) =
	* Fixed an issue during which the uploads/reports/ directory was being created with the wrong permissions.

= 2.0.1 (2016-01-16) =
	* Updated the SQL queries to address some issues.
	
= 2.0 (2016-01-12) =
	* Introduced the new [Automated Weekly or Monthly Email Summary Reports ](http://www.wpsecurityauditlog.com/wordpress-user-monitoring-plugin-releases/reports-add-on-2-0-automatically-receive-wordpress-reports-email/)
	* Included the revision link in reports for when a content is changes in a post, page or custom post type
	* Improved the performance of the reporting engine. Can parse more alerts in a fewer time
	* Handled timeout issue: when reports take longer to complete the plugin will automatically send keep alives

= 1.2.2 (2015-11-10) =
	* Add the content change revision links in reports that allows you to see what content changes the user made. [More Information](http://www.wpsecurityauditlog.com/wordpress-user-monitoring-plugin-releases/record-all-wordpress-content-changes-wp-security-audit-log-plugin/)
	
= 1.2.1 (2015-10-08) =
	* Fixed user listing issue (previously plugin was not listing all users available in WordPress)
	
= 1.2.0 (2015-09-09) =
	* Updated plugin database connector
	* Support for new add-on External DB Connector
	
= 1.1.1 (2015-08-05) =
	* Renamed Add-On
	* Updated links to new website

= 1.1.0 (2015-07-16) =
	* Updated plugin to use new database connector in WP Security Audit Log

= 1.0.0 (2014-10-30) =
	* Initial release of WSAL Reporting extension
