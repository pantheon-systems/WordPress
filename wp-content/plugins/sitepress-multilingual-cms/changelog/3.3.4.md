# Fixes
* [wpmlcore-2465] Fixed AJAX loading of Media in WP-Admin when domains per languages are used
* [wpmlcore-2453] Fixed fatal error when setting a custom taxonomy as translatable (`Fatal error - Class WPML_Term_Language_Synchronization not found in sitepress.class.php`)
* [wpmlcore-2452] Adding a comment to a translated post won't redirect user to the default language.
* [wpmlcore-2448] Fixed `WordPress database error You have an error in your SQL syntax` message, caused by empty or corrupted languages order.
* [wpmlcore-2445] Use of Fileinfo functions to read file mime type when uploading a custom flag, fall back to the now deprecated `mime_content_type` function, if the first set of cuntions is not available
* [wpmlcore-2433] Fixed compatibility issues with W3 Total Cache when Object caching is used
* [wpmlcore-2420] Fix menu synchronization when menu item has quotes in its title
* [wpmlcore-2136] Corrected "Slawisch" to "Slowakisch" in German language name for "Slovak"
* [wpmlcore-2089] AJAX calls when using languages in domain, now calls the correct AJAX url, rather than the url of the default language.