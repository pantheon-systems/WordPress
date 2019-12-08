=== Fastly ===
Contributors: Fastly, Inchoo, CondeNast
Tags: fastly, cdn, performance, speed, spike, spike-protection, caching, dynamic, comments, ddos
Requires at least: 4.6.2
Tested up to: 5.2.4
Stable tag: trunk
License: GPLv2

Integrates Fastly with WordPress publishing tools.

This is the official Fastly plugin for WordPress.

The official code repository for this plugin is available here:

https://github.com/fastly/WordPress-Plugin/

== Description ==

Installation:

You can either install from source (you\'re looking at it), or from the WordPress [plugin directory](http://wordpress.org/plugins/fastly/).

1. To proceed with configuration you will need to [sign up for Fastly](https://www.fastly.com/signup) and create and activate a new service (unless you already have one). Details of how to create and activate a new service can be found [here](https://docs.fastly.com/guides/basic-setup/sign-up-and-create-your-first-service). You will also need to find your Service ID and make a note of the string.
2. You will need to create an API token with the Global API access option selected. [Click here for token management screen](https://manage.fastly.com/account/personal/tokens).
3. Set up the Fastly plugin inside your WordPress admin panel
4. In your Wordpress blog admin panel, Under Fastly->General, enter & save your Fastly API token and Service ID
5. Verify connection by pressing `TEST CONNECTION` button.
6. In order to get the most value out of Fastly we recommend you upload VCL snippets from https://github.com/fastly/WordPress-Plugin/tree/master/vcl_snippets. These snippets will add code for following
- Force certain paths to be passed (not cached) e.g. wp-admin, wp-login.php
- Makes sure that logged in user sessions are never cached
- Handling for serving [stale on error](https://docs.fastly.com/guides/performance-tuning/serving-stale-content.html)

You can upload them by hand or press `Update VCL` button in the UI.

For more information, or if you have any problems, please email us.

_Note: you may have to disable other caching plugins like W3TotalCache to avoid getting odd cache behaviour._

- Pulls in the [Fastly API](http://docs.fastly.com/api)
- Integrates purging in post/page/taxonomies publishing
- Includes an admin panel in `wp-admin`
- Integrates some of the advanced purging options from Fastly API
- Allows to monitor purging using webhooks for slack

Using this plugin means you won't have to purge content in Fastly when you make changes to your WordPress content. Purges will automatically happen with no need for manual intervention.

Customization:

Image optimization:
 To activate, contact support@fastly.com to request image optimization activation for your Fastly service.
 Once activated on service level, you will be able to enable it in your blog under Fastly->Advanced.

 Breakdown of IO options:
    Enable Image Optimization in Fastly configuration - Activating this uploads VCL with needed headers to specific service and activates new version

    Enable Image Optimization in Wordpress - Main switch to activate IO which is needed for all other options to work.

    Enable adaptive pixel ratios - Switch for adaptive pixel ratios implementation. This replaces adaptive pixels srcset to format which Fastly IO can parse and replace. Initially works only on inserted attachments like featured images, but can be applied on content images if enabled.

    Adaptive pixel ratio sizes - Select pixel ratios that will be generated when creating image srcset html.

    Enable image optimization for content images - Safe switch for Image optimization of content images (due to difference from featured images, those are processed differently). To fully utilize, insert full size images in content.

Available wordpress hooks (add_action) on:

Editing purging keys output
 purgely_pre_send_keys
 purgely_post_send_keys
    functions: add_keys

Editing surrogate control headers output(max-age, stale-while-revalidate, stale-if-error)
 purgely_pre_send_surrogate_control
 purgely_post_send_surrogate_control
    functions: edit_headers, unset_headers

Edit cache control headers output (max-age)
 purgely_pre_send_cache_control
 purgely_post_send_cache_control
    functions: edit_headers, unset_headers

Example:
add_action(\'purgely_pre_send_surrogate_control\', \'custom_headers_edit\');
function custom_headers_edit($header_object)
{
  $header_object->edit_headers(array(\'custom-header\' => \'555\', \'max-age\' => \'99\'));
}

add_action(\'purgely_pre_send_keys\', \'custom_surrogate_keys\');
function custom_surrogate_keys($keys_object) {
    $keys_object->add_key(\'custom-key\');
}

Note: you may have to disable other caching plugins like W3TotalCache to avoid getting odd cache behaviour.

== Screenshots ==
1. Fastly General Tab
2. Fastly Advanced Tab
3. Fastly Webhooks Tab

== Changelog ==

= 1.2.11 =

* API token sanitization was too aggressive stripping off underscores which are now legitimate characters in a token

= 1.2.10 =

* Remove a call to a now deprecated function https://github.com/fastly/WordPress-Plugin/issues/72

= 1.2.9 =

* Added fix for scheduled posts transition to published

= 1.2.8 =

* Minor fixes

= 1.2.7 =

* Fixed duplicated API calls on admin page loads

= 1.2.6 =

* Add Image Optimization configuration

= 1.2.5 =
* Added fix for including only always purged keys if existing
* Added fix for header surrogate key number larger than limit

= 1.2.4 =
* Added fix for not yet existing pages not being purged (404 pages key issue)
* Added admin entry for always purged keys
* Make surrogate keys comply with multi-site configurations

= 1.2.3 =
* wp_cli added configuration listing and updating functionality
* Enabled setting of HTML for Maintenance/Error page (503)
* Minor fixes

= 1.2.2 =
* Action Hooks fix

= 1.2.1 =
* Minor VCL clean up

= 1.2.0 =
* Added purge by url
* Changes regarding logging logic
* VCL update User Interface changes
* Fixed and enabled support for wp_cli

= 1.1.1 =
* Some Purgely plugin functionalities integrated into Fastly (along with some advanced options)
* Purging by Surrogate-Keys is used instead of purging by url
* Added webhooks support (Slack focused) to log purges and other critical events
* Added debugging logs option, purge all button for emergency
* Advanced options: Surrogate Cache TTL, Cache TTL, Default Purge Type, Allow Full Cache Purges, Log purges in error log,
Debug mode, Enable Stale while Revalidate, Stale while Revalidate TTL, Enable Stale if Error, Stale if Error TTL.
* Fastly VCL update
* Curl no longer needed

= 1.1 =
* Include fixes for header sending
* Enable \"soft\" purging

= 1.0 =
* Mark as deprecated
* Recommend Purgely from Condé Nast
* Add in link to GitHub repo

= 0.99 =
* Add a guard function for cURL prequisite
* Bring up to date with WP Plugin repo standards

= 0.98 =
* Security fixes for XSS/CSRF
* Only load CSS/JS on admin page
* Properly enqueue scripts and styles
* Use WP HTTP API methods
* Properly register scripts

= 0.94 =
* Change to using PURGE not POST for purges
* Correct URL building for comments purger

= 0.92 =
* Fix bug in port addition

= 0.91 =
* Make work in PHP 5.3

= 0.9 =
* Fix comment purging

= 0.8 =
* Fix url purging

= 0.7 =
* Fix category purging

= 0.6 =
* Remove bogus error_log call

= 0.5 =
* Switch to using curl
* Change PURGE methodology
* Performance enhancements

== About Fastly ==

Fastly is the only real-time content delivery network designed to seamlessly integrate with your development stack.

Fastly provides real-time updating of content and the ability to cache dynamic as well as static content. For any content that is truly uncacheable, we'll accelerate it.

In addition we allow you to update your configuration in seconds, provide real time log and stats streaming, powerful edge scripting capabilities, and TLS termination (amongst many other features).

== License ==

Fastly.com WordPress Plugin
Copyright (C) 2011,2012,2013,2014,2015,2016,2017 Fastly.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

== Upgrade Notice ==
Additional features with improvements in purging precision and Fastly API options
