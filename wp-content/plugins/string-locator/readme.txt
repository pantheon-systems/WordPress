=== String locator ===
Contributors: Clorith
Author URI: http://www.clorith.net
Plugin URI: http://wordpress.org/plugins/string-locator/
Donate link: https://www.paypal.me/clorith
Tags: theme, plugin, text, search, find, editor, syntax, highlight
Requires at least: 4.9
Tested up to: 4.9
Stable tag: 2.3.0
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

= 2.3.0 =
* Upped version requirement to 4.9 as we now use the bundled CodeMirror in WordPress core.
* Converted translation functions to the escaping versions to avoid accidental output from translations.
* Removed bundled languages, these should be served by WordPress.org now.
* Improved behavior when a search failure happens, we were accidentally looping error messages for every file (whoops).
* Added more translatable strings.
* Added various filters:
** `string_locator_bad_http_codes`
** `string_locator_bad_file_types`
**

= Older entries =
See changelog.txt for the version history