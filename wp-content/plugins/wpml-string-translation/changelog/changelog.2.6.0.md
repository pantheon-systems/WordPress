# Features
* [wpmlst-1380] Performance improvements for String Packages
* [wpmlst-1373] New way of importing strings from mo files, imported files are not loaded anymore
* [wpmlst-1249] Allow registering page builder strings when the post has the "draft" status
* [wpmlst-1228] Merged WPML Page Builder plugin into WPML String Translation
* [wpmlst-1222] Set optimal ST settings to improve performance
* [wpmlst-1221] Performance improvements in translating functions
* [wpmlst-1220] Disable ST `gettext` hooks in English language
* [wpmlst-1214] Handle settings coming from the custom XML

# Fixes
* [wpmlst-1408] Fixed issue with Elementor wrongly escaping double quotes used in content
* [wpmlst-1398] Improved performances of the `[wpml-string]` shortcode
* [wpmlst-1367] Fixed a compatibility issue between Gravity Form and Beaver Builder while sending a translation job
* [wpmlst-1318] Added the ability to allow HTML tags in shortcode attributes
* [wpmlst-1311] Prevent parsing shortcodes mixed with regular content
* [wpmlst-1305] Fixed issue in Theme Plugin Localization screen to show amount of scanned plugin strings
* [wpmlst-1290] Fixed issue where translation was not updated when the same string value was used in two different element types
* [wpmlst-1285] Added filters for Elementor and Beaver Builder so developers can add translation support for custom widgets and modules
* [wpmlst-1204] Fixed issue when rendering theme or plugin in Theme Plugin Localization screen that contains an apostrophe on its name
* [wpmlst-1173] Title of Widget Language Switcher has been registered two times
* [wpmlst-1108] Fixed a fatal error when translating strings as a translator with PHP 7.1