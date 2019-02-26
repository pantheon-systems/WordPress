# Fixes
* [wpmlcore-4819] Fixed some third-party plugins loading order issues, by attempting to load WPML before any other plugin
* [wpmlcore-4816] Fixed the logical expression which detects if the post private flag needs to be synchronized
* [wpmlcore-4814] Added hooks to extend the translation options in the term edit page
* [wpmlcore-4812] Fixed the language code of the Norwegian language to "no" (with defult locale set to "nb_NO")
* [wpmlcore-4806] Fixed a fatal error when running WPML 3.8.0 and WPML String Translation 2.6.1
* [wpmlcore-4804] Updated the default locale for several languages which were missing this information or had the wrong one
* [wpmlcore-4662] Fixed a compatibility issue with get_term_by in WP 4.8
* [wpmlcore-4518] Fixed issue with "Adjust IDs for multilingual functionality" on AJAX requests