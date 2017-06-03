=== TinyMCE Advanced ===
Contributors: azaozz
Tags: wysiwyg, formatting, tinymce, write, editor
Requires at least: 4.5
Tested up to: 4.5
Stable tag: 4.3.10
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enables the advanced features of TinyMCE, the WordPress WYSIWYG editor.

== Description ==

This plugin will let you add, remove and arrange the buttons that are shown on the Visual Editor toolbar.
It includes 15 plugins for [TinyMCE](http://tinymce.com/) that are automatically enabled or disabled depending on what buttons are chosen.

= Some of the features added by this plugin =

* Support for creating and editing tables.
* More options when inserting lists.
* Search and Replace in the editor.
* Ability to set Font Family and Font Size.
* And many others.

With this plugin you will also be able to enable the TinyMCE menu. It is a convenient way to access a lot of features that are not used frequently.

== Installation ==

Best is to install directly from WordPress. If manual installation is required, please make sure that the plugin files are in a folder named "tinymce-advanced" (not two nested folders) in the WordPress plugins folder, usually "wp-content/plugins".

== Changelog ==

= 4.3.10.1 =
* Fixed adding paragraph tags when loading posts that were saved before turning autop off.
* Disabled the (new) inline toolbar for tables as it was overlapping the table in some cases.

= 4.3.10 =
* Updated for WordPress 4.5.1 and TinyMCE 4.3.10.
* Fixed support for adding editor-style.css to themes that don't have it.

= 4.3.8 =
* Updated for WordPress 4.5 and TinyMCE 4.3.8.
* Separated standard options and admin options.
* Added settings that can disable the plugin for the main editor, other editors in wp-admin or editors on the front-end.
* Korean translation by Josh Kim and Greek translation by Stathis Mellios.

= 4.2.8 =
* Updated for WordPress 4.4 and TinyMCE 4.2.8.
* Japanese translation by Manabu Miwa.

= 4.2.5 =
* Updated for WordPress 4.3.1 and TinyMCE 4.2.5.
* Fixed text domain and plugin headers.

= 4.2.3.1 =
* Fix error with removing the 'textpattern' plugin.

= 4.2.3 =
* Updated for WordPress 4.3 and TinyMCE 4.2.3.
* Removed the 'textpattern' plugin as WordPress 4.3 includes similar functionality by default.
* French translation by Nicolas Schneider.

= 4.1.9 =
* Updated for WordPress 4.2 and TinyMCE 4.1.9.
* Fixed bugs with showing oEmbed previews when pasting an URL.
* Fixed bugs with getting the content from TinyMCE with line breaks.

= 4.1.7 =
* Updated for WordPress 4.1 and TinyMCE 4.1.7.
* Fixed bug where consecutive caption shortcodes may be split with an empty paragraph tag.

= 4.1.1 =
* Fix bug with image captions when wpautop is disabled.
* Add translation support to the settings page. Button names/descriptions are translated from JS using the existing WordPress translation, so this part of the settings page will be translated by default. The other text still needs separate translation.

= 4.1 =
* Updated for WordPress 4.0 and TinyMCE 4.1.
* Add the 'textpattern' plugin that supports some of the markdown syntax while typing, [(more info)](http://www.tinymce.com/wiki.php/Configuration:textpattern_patterns).
* Add the updated 'table' plugin that supports background and border color.

= 4.0.2 =
* Fix showing of the second, third and forth button rows when the Toolbar Toggle button is not used.
* Fix adding the ''directionality'' plugin when RTL or LTR button is selected.
* Show the ''Advanced Options'' to super admins on multisite installs.
* Add the ''link'' plugin including link rel setting. Replaces the Insert/Edit Link dialog when enabled.
* Include updated ''table'' plugin that has support for vertical align for cells.

= 4.0.1 =
Fix warnings on pages other than Edit Post. Update the description.

= 4.0 =
Updated for WordPress 3.9 and TinyMCE 4.0. Refreshed the settings screen. Added support for exporting and importing of the settings.

= 3.5.9.1 =
Updated for WordPress 3.8, fixed auto-embedding of single line URLs when not removing paragraph tags.

= 3.5.9 =
Updated for WordPress 3.7 and TinyMCE 3.5.9.

= 3.5.8 =
Updated for WordPress 3.5 and TinyMCE 3.5.8.

= 3.4.9 =
Updated for WordPress 3.4 and TinyMCE 3.4.9.

= 3.4.5.1 =
Fixed a bug preventing TinyMCE from importing CSS classes from editor-style.css.

= 3.4.5 =
Updated for WordPress 3.3 or later and TinyMCE 3.4.5.

= 3.4.2.1 =
Fix the removal of the *media* plugin so it does not require re-saving the settings.

= 3.4.2 =
Compatibility with WordPress 3.2 and TinyMCE 3.4.2, removed the options for support for iframe and HTML 5.0 elements as they are supported by default in WordPress 3.2, removed the *media* plugin as it is included by default.

= 3.3.9.1 =
Added advanced options: stop removing iframes, stop removing HTML 5.0 elements, moved the support for custom editor styles to editor-style.css in the current theme.

Attention: if you have a customized tadv-mce.css file and your theme doesn't have editor-style.css, please download tadv-mce.css, rename it to editor-style.css and upload it to your current theme directory. Alternatively you can add there the editor-style.css from the Twenty Ten theme. If your theme has editor-style.css you can add any custom styles there.

= 3.3.9 =
Compatibility with WordPress 3.1 and TinyMCE 3.3.9, improved P and BR tags option.

= 3.2.7 =
Compatibility with WordPress 2.9 and TinyMCE 3.2.7, several minor bug fixes.

= 3.2.4 =
Compatibility with WordPress 2.8 and TinyMCE 3.2.4, minor bug fixes.

= 3.2 =
Compatibility with WordPress 2.7 and TinyMCE 3.2, minor bug fixes.

= 3.1 =
Compatibility with WordPress 2.6 and TinyMCE 3.1, keeps empty paragraphs when disabling the removal of P and BR tags, the buttons for MCImageManager and MCFileManager can be arranged (if installed).

= 3.0.1 =
Compatibility with WordPress 2.5.1 and TinyMCE 3.0.7, added option to disable the removal of P and BR tags when saving and in the HTML editor (autop), added two more buttons to the HTML editor: autop and undo, fixed the removal of non-default TinyMCE buttons.

= 3.0 =
Support for WordPress 2.5 and TinyMCE 3.0.

= 2.2 =
Deactivate/Uninstall option page, font size drop-down menu and other small changes.

= 2.1 =
Improved language selection, improved compatibility with WordPress 2.3 and TinyMCE 2.1.1.1, option to override some of the imported css classes and other small improvements and bugfixes.

= 2.0 =
Includes an admin page for arranging the TinyMCE toolbar buttons, easy installation, a lot of bugfixes, customized "Smilies" plugin that uses the built-in WordPress smilies, etc. The admin page uses jQuery and jQuery UI that lets you "drag and drop" the TinyMCE buttons to arrange your own toolbars and enables/disables the corresponding plugins depending on the used buttons.

== Upgrade Notice ==

= 4.2.3 =
Updated for WordPress 4.3 and TinyMCE 4.2.3.

= 4.1.9 =
Updated for WordPress 4.2 and TinyMCE 4.1.9.

= 4.1 =
Includes the 'textpattern' plugin that supports some of the markdown syntax while typing, and the updated 'table' plugin that supports background and border color for tables.

== Frequently Asked Questions ==

= No styles are imported in the Formats sub-menu. =

These styles are imported from your current theme editor-style.css file. However some themes do not have this functionality. For these themes TinyMCE Advanced has the option to let you add a customized editor-style.css and import it into the editor.

= I have just installed this plugin, but it does not do anything. =

Log out of WordPress, clear your browser cache, quit and restart the browser and try again. If that does not work, there may be a caching proxy or network cache somewhere between you and your host. You may need to wait for a few hours until this cache expires.

= When I add "Smilies", they do not show in the editor. =

The "Emoticons" button in TinyMCE adds the codes for the smilies. The actual images are added by WordPress when viewing the Post. Make sure the checkbox "Convert emoticons to graphics on display" in "Options - Writing" is checked.

= The plugin does not add any buttons. =

Make sure the "Disable the visual editor when writing" checkbox under "Users - Your Profile" is **not** checked.

= I still see the "old" buttons in the editor =

Click the "Restore Default Settings" button on the plugin settings page and then set the buttons again and save.

= Other questions? More screenshots? =

Please post on the support forum or visit the homepage for [TinyMCE Advanced](http://www.laptoptips.ca/projects/tinymce-advanced/).


== Screenshots ==

1. The WordPress editor after installing this plugin (default plugin settings).
2. The TinyMCE Advanced settings page.
