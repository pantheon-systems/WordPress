=== Duplicate Post ===
Contributors: 		lopo
Donate link: 		https://duplicate-post.lopo.it/
Tags: 				duplicate post, copy, clone
Requires at least: 	3.6
Tested up to: 		5.1
Stable tag: 		3.2.2
Requires PHP:		5.2.4
License: 			GPLv2 or later
License URI: 		http://www.gnu.org/licenses/gpl-2.0.html

Copy posts of any type with a click!

== Description ==

This plugin allows users to clone posts of any type, or copy them to new drafts for further editing.
If you find this useful, [**please consider donating**](https://duplicate-post.lopo.it/) whatever sum you choose, **even just 10 cents**. Just a few cents from every user would help me develop the plugin and improve support.

How it works:

1. In 'Edit Posts'/'Edit Pages', you can click on 'Clone' link below the post/page title: this will immediately create a copy and return to the list.

2. In 'Edit Posts'/'Edit Pages', you can select one or more items, then choose 'Clone' in the 'Bulk Actions' dropdown to copy them all at once.

3. In 'Edit Posts'/'Edit Pages', you can click on 'New Draft' link below the post/page title.

4. On the post edit screen, you can click on 'Copy to a new draft' above "Cancel"/"Move to trash" or in the admin bar. 

5. While viewing a post as a logged in user, you can click on 'Copy to a new draft' in the admin bar.

3, 4 and 5 will lead to the edit page for the new draft: change what you want, click on 'Publish' and you're done.

There is also a **template tag**, so you can put it in your templates and clone your posts/pages from the front-end. Clicking on the link will lead you to the edit page for the new draft, just like the admin bar link.

Duplicate Post has many useful settings to customize its behavior and restrict its use to certain roles or post types. Check out the extensive documentation on [the plugin's site](https://duplicate-post.lopo.it).

**If you're a plugin developer**, I suggest you to read the [Developer's Guide](https://duplicate-post.lopo.it/docs/developers-guide/) to ensure compatibility between your plugin(s) and mine. Feel free to [contact me](https://duplicate-post.lopo.it/contact) so we can keep in touch and collaborate.

Duplicate Post does not collect any information outside your WordPress installation, therefore it's 100% GDPR compliant.

Thanks for all the suggestions, bug reports, translations and donations, they're frankly too many to be listed here!

== Installation ==

Use WordPress' Add New Plugin feature, searching "Duplicate Post", or download the archive and:

1. Unzip the archive on your computer  
2. Upload `duplicate-post` directory to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to Settings -> Duplicate Post and customize behaviour as needed

== Frequently Asked Questions ==

= The plugin doesn't work, why? =

First, check your version of WordPress: the plugin is not supposed to work on old versions anymore. Make sure also to upgrade to the last version of the plugin!

Then try to deactivate and re-activate it, some user have reported that this fixes some problems.

Pay also attention to the "Permissions" tab in the Settings: make sure the plugin is enabled for the desired roles and post types.

If it still doesn't work, maybe there is some kind of conflict with other plugins: feel free [to write in the forum](https://wordpress.org/support/plugin/duplicate-post) and we'll try to discover a solution (it will be *really* helpful if you try to deactivate all your other plugins one by one to see which one conflicts with mine... But do it only if you know what you're doing, I will not be responsible of any problem you may experience).

= The plugin is not translated in my language! =

From version 3.0 the plugin's translations are managed by the WordPress.org platform and the plugin is shipped without language files, so first of all update translations under Dashboard->Updates.

If Duplicate Post is still in English, or if there are some untraslated strings, you can help traslating to your language [here](https://translate.wordpress.org/projects/wp-plugins/duplicate-post): you only need a WordPress.org account.
[Contact me](https://duplicate-post.lopo.it/contact) if you wish to become an editor for your language.

== Screenshots ==

1. Here you can copy the post you're editing to a new draft.
2. By clicking on "Clone" the post is cloned immediately. "New draft" leads to the edit screen.
3. The options page.
4. The template tag manually added to Twenty Ten theme. Click on the "Copy to a new draft" link and you're redirected to the edit screen for a new draft copy of your post.
5. The admin bar link. 
6. Bulk clone action.

== Upgrade Notice ==

= 3.2.2 =
Adds compatibility with Gutenberg UI and fixes a problem with slugs on new installs

= 3.2.1 =
Fixes some problems with Multisite, WPML, revisions

= 3.2 =
new website + WPML compatibility + various fixes

= 3.1.2 =
Fixes the problem with custom fields

= 3.1.1 =
Bulk clone + custom field wildcards + other features + bugfixes + fix for nasty nag

= 3.1 =
Bulk clone + custom field wildcards + other features + bugfixes

= 3.0.3 =
Notices + small fixes and improvements

= 3.0.2 =
Small bugfixes: check the changelog for more info

= 3.0.1 =
Recommended if you have 3.0: fixes the upgrade bug

= 3.0 =
Major redesign of the settings page + fine-tune options (what to copy, custom post types, etc.) + bugfixes and XSS prevention

= 2.6 =
PHP 5.4 (Strict Standards) compatible + Fixed possible XSS and SQL injections + other bugs 

= 2.4.1 =
Fixes a couple of bug. Recommended if you have problems with v2.4

= 2.4 =
Copy child pages + a couple of bugfixes + licence switch to GPLv2

= 2.3 =
Fixes a bunch of bugs + copy attachments + choose where to show the links.

= 2.2 =
VERY IMPORTANT UPGRADE to get rid of problems with complex custom fields, afflicting both 2.1.* releases.

= 2.1.1 =
Fix for upgrade problem 

= 2.1 =
Copy from admin bar + user levels out, roles and capabilities in. 

= 2.0.2 =
Fixed permalink bug + double choice on posts list

= 2.0.1 =
Bug fix + new option

= 2.0 =
Several improvements and new features, see changelog. Requires WP 3.0+.

= 1.1.1 =
Some users have experienced a fatal error when upgrading to v1.1: this may fix it, if it's caused by a plugin conflict.

= 1.1 =
New features and customization, WP 3.0 compatibility: you should upgrade if you want to copy Custom Posts with Custom Taxonomies.

== Changelog ==

= 3.2.2 (2018-04-13) =
* The "Admin bar" option shows the link in the post edit screen too, so you can use the plugin with Gutenberg enabled
* Option for "Slug" not set by default on new installations 
* Better display of icon in the Admin bar on small screens

= 3.2.1 (2017-11-25) =
* Fixing some issues of the 3.* trunk before major redesign
* Fixes issue when upgrading on multisite
* Improved compatibility with WPML + page builders, thanks to WPML team
* Prevents creating a revision immediately after the cloning

= 3.2 (2017-04-04) =
* new website with extensive documentation
* WPML compatibility, thanks to WPML team
* improved Jetpack compatibility (Subscriptions, Markdown)
* small changes to hooks
* improved security with nonces
* various small fixes

= 3.1.2 (2016-12-13) =
* Fix for custom fields not copied

= 3.1.1 (2016-12-13) =
* Fix for nasty update nag (plus a failsafe checkbox)

= 3.1 (2016-12-13) =
* Bulk clone action added (WP 4.7+)
* Wildcards enabled for custom fields to skip
* Options to copy post author, post format (moved from taxonomies), menu order, post template
* Check publish_posts/publish_pages capability to prevent Contributors from publishing by cloning
* Using wp_slash (WP 3.6+) or a better workaround (WP 4.4+) on the post and its meta, should also fix some compatibility issues
* Check if admin bar is showing before enqueueing CSS
* Probable fix for repeated clone bug
* Other minor bugs fixed 

= 3.0.3 (2016-10-29) =
* Notices in admin after copying
* Fixes warning in custom post type archives
* Uses site options for version and notice
* Minor fixes 

= 3.0.2 (2016-10-18) =
* Can now be enabled for every custom post type with visible UI (not just public ones)
* Admin bar CSS only enqueued when needed
* New "Donate" button
* Fixes for minor bugs and typos

= 3.0.1 (2016-10-09) =
* Fixes the issues for people upgrading from an older version

= 3.0 (2016-10-09) =
* Settings page redesigned
* More options to enable/disable copy of every part of a post
* Enable/disable cloning for every custom post type
* Jetpack Publicize compatibility
* Fixed a possible XSS
* Fixed other little bugs
* Translations removed to use WP.org's official translation project
* Checked PHP 7 compatibility

= 2.6 (2014-04-27) =
* PHP 5.4 (Strict Standards) compatible
* Fixed possible XSS and SQL injections
* other bugs 
* Updated and added translations
* Tested up to WP 3.8.1

= 2.4.1 (2014-02-22) =
* Fixed regression about draft permalinks
* Fixed bug with guid
* Don't clone to_ping and pinged (maybe there will be an option about those later)

= 2.4 (2012-04-29) =
* New option to clone the children of the original page
* Licence changed to GPLv2 or later
* Fixed publishing dates for drafts 
* Fixed bug with prefix/suffix
* Translation project moved to GlotPress

= 2.3 (2012-04-06) =
* Added options to choose where to show the "Clone" links
* Clone attachments (i.e. references in the DB, not physical files) 
* Fix for untranslated user roles
* Some other fixes (missing checks, PHP warnings and errors, etc.)

= 2.2 (2012-02-01) =
* Fix for problems when copying serialized meta fields
* Fix for multiple _dp_original field
* Removed deprecated parameter when adding options

= 2.1.1 (2012-01-04) =
* Can't rely on activation hook for upgrade, this caused problems with new options

= 2.1 (2012-01-03) =
* Even more code cleaning (no more custom queries, using WP API)
* Term order preserved when copying
* Stopped using deprecated User levels, now it uses Roles and Capabilities
* 'Copy to a new draft' link in admin bar
* duplicate_post_get_original template tag
* Settings link in plugin list, 'Donate' and 'Translate' link in option page

= 2.0.2 (2011-12-12) =
* Fixed bug for permalinks
* Two links on posts list: clone immediately or copy to a new draft to edit.
* Tested on multisite mode.

= 2.0.1 (2011-12-08) =
* Fixed bug for action filters
* New option so you can choose if cloning from the posts list must copy the post status (draft, published, pending) too.

= 2.0 (2011-12-08) =
* WP 3.3 compatibility (still not tested against multiblog feature, so beware)
* Minimum WP version: 3.0
* Code cleanup
* Immediate cloning from post list
* Added options for taxonomies and post excerpt
* Added suffix option
* Added template tag

= 1.1.2 (2011-04-08) =
* WP 3.1.1 compatibility (still not tested against multiblog feature, so beware)
* Added complete Polish language files

= 1.1.1 (2010-06-30) =
* Plugin split in two files for faster opening in Plugins list page
* fix conflicts with a few other plugins
* Added Dutch language files

= 1.1 (2010-06-24) =
* WP 3.0 compatibility (not tested against multiblog feature, so beware)
* Option page: minimum user level, title prefix, fields not to be copied, copy post/page date also
* Added German, Swedish, Romanian, Hebrew, Catalan (incomplete) and Polish (incomplete) language files

= 1.0 (2010-06-15) =
* Better integration with WP 2.7+ interface
* Added actions for plugins which store post metadata in self-managed tables
* Added French and Spanish language files
* Dropped WP 2.6.5 compatibility

= 0.6.1 (2009-12-03) =
* Tested WP 2.9 compatibility

= 0.6 (2007-07-21) =
* Fix for WP 2.8.1
* WPMU compatibility
* Internationalization (Italian and Japanese language files shipped)

= 0.5 (2009-01-09) =
* Fix for post-meta
* WP2.7 compatibility 

= 0.4 (2008-11-23) =
* Support for new WP post revision feature

= 0.3 (2008-03-01) =
* Initial version on WP repository

== Contribute ==

If you find this useful and if you want to contribute, there are three ways:

   1. You can [write me](https://duplicate-post.lopo.it/contact) and submit your bug reports, suggestions and requests for features;
   2. If you want to translate it to your language (there are just a few lines of text), you can use the [translation project](https://translate.wordpress.org/projects/wp-plugins/duplicate-post);
   3. Using the plugin is free, but if you want you can support my efforts by donating with PayPal [here](https://duplicate-post.lopo.it/donate)
