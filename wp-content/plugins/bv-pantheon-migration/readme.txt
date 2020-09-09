=== Pantheon Migrations ===
Contributors: akshatc, blogvault, getpantheon
Tags: pantheon, migration
Requires at least: 4.0
Tested up to: 5.5
Requires PHP: 5.4.0
Stable tag: 4.35
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The easiest way to migrate your site to Pantheon

== Description ==

Pantheon is a website management platform that is the best place to run your WordPress site — hands down. And migrating your WordPress site doesn’t get any easier than with Pantheon Migrations.

With this plugin forget the headaches of manually migrating your site. All you need to activate the plugin is administrative access to your WordPress site. Just copy and paste your SFTP credentials from your new Pantheon site to the Pantheon Migrations tab in your WordPress Dashboard and click “migrate”. You can get back to work on other projects, and our migrations team will email you when everything is complete.

For full instructions please see our [docs page](https://pantheon.io/docs/migrate-wordpress/) on Pantheon Migrations.

Shout out to [BlogVault.net](https://blogvault.net/) for the development and hand in creating this plugin. By using this plugin you are agreeing to their [Terms of Service](https://blogvault.net/tos)


== Installation ==

= There are two methods = 

1. Upload `bv-pantheon-migration` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Once the plugin is activated, click on Pantheon Migration in the left side navigation

Enter the required information:

`Destination URL:` (this will be your pantheon address you are migrating to, example: http://dev-sitename.pantheon.io)
`Machine Token:` (Machine tokens are used to uniquely identify your machine and securely authenticate via Terminus)

Click the `Migrate` button and you will be redirected to the migration landing page. The plugin will automatically verify your Machine Token and let you know if there are any issues.

After the migration is complete there will be a button you can click to see the results of your migration and automatically redirected to your Pantheon site URL.

== Frequently Asked Questions ==

= 1) I do not have a Pantheon account, can I still use this plugin? = 

No, but signing up for a Pantheon account is free and so is migrating your site with Pantheon Migrations. [Sign up here!](https://pantheon.io/register)

= 2) What information will the plugin ask for? =

You will have to provide the plugin your destination url and Machine Token from your Pantheon account. Please read the [Installation section](https://wordpress.org/plugins/bv-pantheon-migration/installation/) for more information on where to find this.

= 3) Is Multisite supported with this plugin = 

Not yet, Pantheon is currently working on testing the support of Multisite on our platform but it's still too soon. We will update this section when it's available.

= 4) How long does it take to migrate a website? = 

This can range anywhere from 30 minutes to several hours depending on the size of the website. On average, migrations to Pantheon take about 1 hour. 

= 5) Can I migrate a site from WordPress.com? = 

Currently you can only migrate a self hosted WordPress installation, the plugin does not support migrating from WordPress.com.

= 6) What happens if I run into an error after the migration is complete? = 

We are always wanting to assist and help out in any way that we can. If you encounter any type of issue please use the support section of our plugin. [Click here](https://wordpress.org/support/plugin/bv-pantheon-migration/) to file an issue. `This section is monitored daily.`

= 7) Do I need to leave the window open while the migration is processing? = 

No, that's the beauty of this plugin. It runs on a SAAS based technology and a secure web address that runs everything in the background. Once you start the migration you can close the window at any time and come back to it later while it's still running, no need to wait for hours. You will also receive an email once the migration has completed.

== Screenshots ==

1. Accessing your SFTP credentials within Pantheon
2. Adding information to the Pantheon Migrations plugin

== Changelog ==
= 4.35 =
* Improved scanfiles and filelist api

= 4.31 =
* Fetching Mysql Version
* Robust data fetch APIs
* Core plugin changes
* Sanitizing incoming params

= 3.4 =
* Plugin branding fixes

= 3.2 =
* Updating account authentication struture

= 3.1 =
* Adding params validation
* Adding support for custom user tables

= 2.1 =
* Restructuring classes

= 1.88 =
* Callback improvements

= 1.86 =
* Updating tested upto 5.1

= 1.84 =
* Disable form on submit

= 1.82 =
* Updating tested upto 5.0

= 1.77 =
* Adding function_exists for getmyuid and get_current_user functions 

= 1.76 =
* Removing create_funtion for PHP 7.2 compatibility

= 1.72 =
* Adding Misc Callback

= 1.71 =
* Adding logout functionality in the plugin

= 1.69 =
* Adding support for chunked base64 encoding

= 1.68 =
* Updating upload rows

= 1.66 =
* Updating TOS and privacy policies

= 1.64 =
* Bug fixes for lp and fw

= 1.62 =
* SSL support in plugin for API calls
* Adding support for plugin branding

= 1.44 =
* Removed bv_manage_site
* Updated asym_key

= 1.41 =
* Better integrity checking
* Woo Commerce Dynamic sync support

= 1.40 =
* Manage sites straight from BlogVault dashboard

= 1.31 =
* Changing dynamic backups to be pull-based

= 1.30 =
* Using dbsig based authenticatation

= 1.22 =
* Adding support for GLOB based directory listings
* Adding support for Machine Tokens instead of SFTP details

= 1.21 =
* Adding support for PHP 5 style constructors

= 1.20 =
* Adding DB Signature and Server Signature to uniquely identify a site
* Adding the stats api to the WordPress Backup plugin.
* Sending tablename/rcount as part of the callback

= 1.17 =
* Add support for repair table so that the backup plugin itself can be used to repair tables without needing PHPMyAdmin access
* Making the plugin to be available network wide.
* Adding support for 401 Auth checks on the source or destination

= 1.16 =
* Improving the Base64 Decode functionality so that it is extensible for any parameter in the future and backups can be completed for any site
* Separating out callbacks gettablecreate and getrowscount to make the backups more modular
* The plugin will now automatically ping the server once a day. This will ensure that we know if we are not doing the backup of a site where the plugin is activated.
* Use SHA1 for authentication instead of MD5

= 1.15 =
* First release of Pantheon Plugin
