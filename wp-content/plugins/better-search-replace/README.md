# Better Search Replace #
**Contributors:** Delicious Brains, mattshaw

**Tags:** search replace, update urls, database, search replace database, update database urls, update live url

**Requires at least:** 3.0.1

**Tested up to:** 4.9.1

**Stable tag:** trunk

**License:** GPLv3 or later

**License URI:** http://www.gnu.org/licenses/gpl-3.0.html


A simple plugin for updating URLs or other text in a database.

## Description ##

When moving your WordPress site to a new domain or server, you will likely run into a need to run a search/replace on the database for everything to work correctly. Fortunately, there are several plugins available for this task, however, all have a different approach to a few key features. This plugin is an attempt to consolidate the best features from these plugins, incorporating the following features in one simple plugin:

* Serialization support for all tables
* The ability to select specific tables
* The ability to run a "dry run" to see how many fields will be updated
* No server requirements aside from a running installation of WordPress
* WordPress Multisite support

**Premium features available in the Pro version:**

* View exactly what changed during a search/replace
* Backup and import the database while running a search/replace
* Priority email support from the developer of the plugin
* Save or load custom profiles for quickly repeating a search/replace in the future
* Support and updates for 1 year

**[Learn more about Better Search Replace Pro](https://bettersearchreplace.com/)**


The search/replace functionality is heavily based on interconnect/it's great and open-source Search Replace DB script, modified to use WordPress native database functions to ensure compatibility.

## Installation ##

Install Better Search Replace like you would install any other WordPress plugin.

Dashboard Method:

1. Login to your WordPress admin and go to Plugins -> Add New
2. Type "Better Search Replace" in the search bar and select this plugin
3. Click "Install", and then "Activate Plugin"


Upload Method:

1. Upload 'better-search-replace.php' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Changelog ##

### 1.3.2 - January 3, 2018 ###
* Fix: Only one table searched on some environments (props @Ov3rfly)
* Tweak: Update text in sidebar

### 1.3 - November 10, 2016 ###
* Improvement: Updated sidebar and added pro version discount
* Fix: Outdated links to old website
* Fix: Prevent requests to invalid tabs

### 1.2.10 - June 2, 2016 ###
* Fix: CSS not loaded on details page

### 1.2.9 - December 8, 2015 ###
* Fix: Bug with case-insensitive searches in serialized objects
* Fix: Bug with early skip due to lack of primary key

### 1.2.8 - November 25, 2015 ###
* Fix: Bug with report details

### 1.2.7 - November 24, 2015 ###
* Fix: Untranslateable string
* Tweak: Check BSR_PATH instead of ABSPATH to be consistent
* Tested with 4.4

### 1.2.6 ###
* Removed unused code/small cleanup

### 1.2.5 ###
* Improved progress bar info and styles
* Small cleanup

### 1.2.4 ###
* Added "Settings saved" notice when saving settings
* Fixed bug with wp_magic_quotes interfering with some search strings

### 1.2.3 ###
* Fixed bug with searching for backslashes
* Fixed potential bug with getting tables in large multisites
* Fixed potential notice in append_report
* Improved handling of missing primary keys

### 1.2.2 ###
* Fixed AJAX conflict with WooCommerce
* Fixed a few issues with translations
* Tweaked "System Info" to use get_locale() instead of WP_LANG constant
* Updated German translation (props @Linus Ziegenhagen)

### 1.2.1 ###
* Fixed minor issue with display of progress bar
* Updated translation file

### 1.2 ###
* Switched to AJAX bulk processing for search/replaces
* Decreased minimum "Max Page Size" to 1000
* Added "Help" tab with system info for easier troubleshooting

### 1.1.1 ###
* Added ability to change max page size
* Decreased default page size to prevent white screen issue on some environments

### 1.1 ###
* Added ability to change capability required to use plugin
* Small bugfixes and translation fixes

### 1.0.6 ###
* Added table sizes to the database table listing
* Added French translation (props @Jean Philippe)

### 1.0.5 ###
* Added support for case-insensitive searches
* Added German translation (props @Linus Ziegenhagen)

### 1.0.4 ###
* Potential security fixes

### 1.0.3 ###
* Fixed issue with searching for special characters like '\'
* Fixed bug with replacing some objects

### 1.0.2 ###
* Fixed untranslateable strings on submit button and submenu page.

### 1.0.1 ###
* Fixed issue with loading translations and added Spanish translation (props Eduardo Larequi)
* Fixed bug with reporting timing
* Updated to use "Dry Run" as default
* Added support for WordPress Multisite (see FAQs for more info)

### 1.0 ###
* Initial release
