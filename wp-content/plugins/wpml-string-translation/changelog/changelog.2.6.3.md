# Features
* [wpmlst-1522] Fix parameters count not matching placeholders count in wpdb::prepare

# Fixes
* [wpmlst-1536] Fixed problem with Page Builders where, of two similar contents, only one was translated in the front-end
* [wpmlst-1530] Fixed issue introduced in WordPress 4.9 with translation of User's description.
* [wpmlst-1529] Fixed minor issue caused during converting a Text Widget to Multilingual one. Introduced in WordPress 4.9 when indicating that a Widget has been saved.
* [wpmlst-1528] Implemented caching when saving the file info of .mo files for improved performance
* [wpmlst-1527] Fixed JS errors happening with WordPress 4.9, when scanning strings in Themes & Plugins localization
* [wpmlst-1524] Fix string translation so that the original value is returned if there are no translations.
* [wpmlst-1519] Fixed issue with Post's link cache not being invalidated when a Post is updated
* [wpmlst-1516] Fixed notice shown when `theme_root` gets overridden (e.g. by a mobile theme)