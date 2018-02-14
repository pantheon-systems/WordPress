=== String locator ===
Contributors: Clorith
Author URI: http://www.clorith.net
Plugin URI: http://wordpress.org/plugins/string-locator/
Donate link: https://www.paypal.me/clorith
Tags: theme, plugin, text, search, find, editor, syntax, highlight
Requires at least: 3.6
Tested up to: 4.8
Stable tag: 2.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Find and edit code or texts in your themes and plugins

== Description ==

When working on themes and plugins you often notice a piece of text that appears hardcoded into the files, you need to modify it, but you don't know what theme or plugin it's in, and certainly not which individual file to look in.

Easily search through your themes, plugins or even WordPress core and be presented with a list of files, the matched text and what line of the file matched your search.
You can then quickly make edits directly in your browser by clicking the link from the search results.

By default a consistency check is performed when making edits to files, this will look for inconsistencies with braces, brackets and parenthesis that are often accidentally left in.
This drastically reduces the risk of breaking your site when making edits, but is in no way an absolute guarantee.


== Frequently asked questions ==

= Will Smart-Scan guarantee my site is safe when making edits? =
Although it will do it's best at detecting incorrect usage of the commonly used symbols (parenthesis, brackets and braces), there is no guarantee every possible error is detected. The best safe guard is to keep consistent backups of your site (even when not making edits).

As of version 1.6, the plugin will check your site health after performing an edit. If the site is returning a site breaking error code, we'll revert to the previous version of the file.

= My search is failing and I am told that my search is an invalid pattern =
This error is only related to regex searches, and is based off how PHP reads your regex string.

When writing your search string, make sure to wrap your search in forward slashes (`/`), directly followed by any modifiers like case insensitive (`i`) that you may want to use.


== Screenshots ==

1. Searching through the Twenty Fourteen theme for the string 'not found'
2. Having clicked the link for one of the results and being taken to the editor in the browser
3. Smart-Scan has detected an inconsistency in the use of braces

== Changelog ==

= 2.2.0 =
* Fixed some lingering potential HTTPS issues.
* Fixed result previews not cutting the excerpt making them excessively long.
* Fixed archive file skipping not accounting for letter casing in extensions.
* Introduced common media types to the file skipping procedure.
* Added default timeout periods, the plugin will no longer allow indefinite execution to work around http proxies.
* Added more translatable strings that were missed.
* Added Must-Use Plugins to individual plugin search.
* Made changes to the uninstall routine to ensure we remove any related database entries on removal.

= Older entries =
See changelog.txt for the version history