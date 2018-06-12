=== Better Search Replace ===
Contributors: deliciousbrains, mattshaw
Tags: search replace, search and replace, update urls, database, search replace database, update database urls, update live url, better search replace, search&replace
Requires at least: 3.0.1
Tested up to: 4.9.1
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A simple plugin to update URLs or other text in a database.

== Description ==

When moving your WordPress site to a new domain or server, you will likely run into a need to run a search/replace on the database for everything to work correctly. Fortunately, there are several plugins available for this task, however, all have a different approach to a few key features. This plugin consolidates the best features from these plugins, incorporating the following features in one simple plugin:

* Serialization support for all tables
* The ability to select specific tables
* The ability to run a "dry run" to see how many fields will be updated
* No server requirements aside from a running installation of WordPress
* WordPress Multisite support

> **Time-saving features available in the Pro version:**
>
> * View exactly what changed during a search/replace
> * Backup and import the database while running a search/replace
> * Priority email support from the developer of the plugin
> * Save or load custom profiles for quickly repeating a search/replace in the future
> * Support and updates for 1 year
>
> **[Learn more about Better Search Replace Pro](https://bettersearchreplace.com/)**

The search and replace functionality is heavily based on interconnect/it's great and open-source Search Replace DB script, modified to use WordPress native database functions to ensure compatibility.

**Supported Languages**

* English
* French
* German
* Spanish

**Want to contribute?**

Feel free to open an issue or submit a pull request on [GitHub](https://github.com/deliciousbrains/better-search-replace/).

== Installation ==

Install Better Search Replace like you would install any other WordPress plugin.

Dashboard Method:

1. Login to your WordPress admin and go to Plugins -> Add New
2. Type "Better Search Replace" in the search bar and select this plugin
3. Click "Install", and then "Activate Plugin"


Upload Method:

1. Unzip the plugin and upload the "better-search-replace" folder to your 'wp-content/plugins' directory
2. Activate the plugin through the Plugins menu in WordPress

== Frequently Asked Questions ==

= Using Better Search Replace =

Once activated, Better Search Replace will add a page under the "Tools" menu page in your WordPress admin.

= Is my host supported? =

Yes! This plugin should be compatible with any host.

= Can I damage my site with this plugin? =

Yes! Entering a wrong search or replace string could damage your database. Because of this, it is always adviseable to have a backup of your database before using this plugin.

= How does this work on WordPress Multisite? =

When running this plugin on a WordPress Multisite installation, it will only be loaded and visible for Network admins. Network admins can go to the dashboard of any subsite to run a search/replace on just the tables for that subsite, or go to the dashboard of the main/base site to run a search/replace on all tables.

= How can I use this plugin when changing URLs? =

If you're moving your site from one server to another and changing the URL of your WordPress installation, the approach below allows you to do so easily without affecting the old site:

1. Backup the database on your current site
2. Install the database on your new host
3. On the new host, define the new site URL in the `wp-config.php` file, as shown [here](http://codex.wordpress.org/Changing_The_Site_URL#Edit_wp-config.php)
4. Log in at your new admin URL and run Better Search Replace on the old site URL for the new site URL
5. Delete the site_url constant you added to `wp-config.php`. You may also need to regenerate your .htaccess by going to Settings -> Permalinks and saving the settings.

More information on moving WordPress can be found [here](http://codex.wordpress.org/Moving_WordPress).

== Screenshots ==

1. The Better Search Replace page added to the "Tools" menu
2. After running a search/replace dry-run.

== Changelog ==

= 1.3.2 - January 3, 2018 =
* Fix: Only one table searched on some environments (props @Ov3rfly)
* Tweak: Update text in sidebar

= 1.3.1 - September 14, 2017 =
* Security: Check if data is serialized before unserializing it
* Improvement: Increased size of table select

= 1.3 - November 10, 2016 =
* Improvement: Updated sidebar and added pro version discount
* Fix: Outdated links to old website
* Fix: Prevent requests to invalid tabs

= 1.2.10 - June 2, 2016 =
* Fix: CSS not loaded on details page

= 1.2.9 - December 8, 2015 =
* Fix: Bug with case-insensitive searches in serialized objects
* Fix: Bug with early skip due to lack of primary key

= 1.2.8 - November 25, 2015 =
* Fix: Bug with report details

= 1.2.7 - November 24, 2015 =
* Fix: Untranslateable string
* Tweak: Check BSR_PATH instead of ABSPATH to be consistent
* Tested with 4.4

= 1.2.6 =
* Removed unused code/small cleanup

= 1.2.5 =
* Improved progress bar info and styles
* Small cleanup

= 1.2.4 =
* Added "Settings saved" notice when saving settings
* Fixed bug with wp_magic_quotes interfering with some search strings

= 1.2.3 =
* Fixed bug with searching for backslashes
* Fixed potential bug with getting tables in large multisites
* Fixed potential notice in append_report
* Improved handling of missing primary keys

= 1.2.2 =
* Fixed AJAX conflict with WooCommerce
* Fixed a few issues with translations
* Tweaked "System Info" to use get_locale() instead of WP_LANG constant
* Updated German translation (props @Linus Ziegenhagen)

= 1.2.1 =
* Fixed minor issue with display of progress bar
* Updated translation file

= 1.2 =
* Switched to AJAX bulk processing for search/replaces
* Decreased minimum "Max Page Size" to 1000
* Added "Help" tab with system info for easier troubleshooting

= 1.1.1 =
* Added ability to change max page size
* Decreased default page size to prevent white screen issue on some environments

= 1.1 =
* Added ability to change capability required to use plugin
* Small bugfixes and translation fixes

= 1.0.6 =
* Added table sizes to the database table listing
* Added French translation (props @Jean Philippe)

= 1.0.5 =
* Added support for case-insensitive searches
* Added German translation (props @Linus Ziegenhagen)

= 1.0.4 =
* Potential security fixes

= 1.0.3 =
* Fixed issue with searching for special characters like '\'
* Fixed bug with replacing some objects

= 1.0.2 =
* Fixed untranslateable strings on submit button and submenu page.

= 1.0.1 =
* Fixed issue with loading translations and added Spanish translation (props Eduardo Larequi)
* Fixed bug with reporting timing
* Updated to use "Dry Run" as default
* Added support for WordPress Multisite (see FAQs for more info)

= 1.0 =
* Initial release
