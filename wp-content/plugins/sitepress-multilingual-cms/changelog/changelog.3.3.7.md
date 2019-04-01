# Features
* [wpmlcore-2708] Added an option in "WPML -> Languages -> SEO Options" to chose when to render the `hreflang` links
* [wpmlcore-2676] WordPress SEO user meta fields are now translatable with WPML String Translation
* [wpmlcore-2667] The `Language tag` in *WPML* -> *Languages* -> *Edit Languages* is now pre-filled, for known languages, with a two letters language code (same as the language code). Users can still use language codes with variations.
* [wpmlcore-2633] Auto-disconnect duplicates only if there are changes.
* [wpmlcore-2593] Added `wpml_element_trid` to get the `trid` of a translated element: required for `wpml_get_element_translations` filter and others which expect this argument.
* [wpmlcore-2574] Auto-disconnect duplicates when they are edited
* [wpmlcore-2541] Removal of "icon-32" usage
* [wpmlcore-2495] Increased the accepted lenght of "Default Locale" and "Tag" in the languages table, so to allow variations such as "de_DE_formal"

# Fixes
* [wpmlcore-873] Fixed rtl alignment for language Urdu
* [wpmlcore-2761] Enforced security
* [wpmlcore-2745] Fix redirect for hidden language when using languages in domains
* [wpmlcore-2697] Fixed glitch in screen "Menus" for select box: "Translation of" for WP 4.5
* [wpmlcore-2680] Added an option in "WPML -> Languages -> SEO Options" to chose when to render the `hreflang` links
* [wpmlcore-2675] Fixed potential bug with old PHP version (<5.2.9) when filtering posts
* [wpmlcore-2664] Fixed notices when updating to latest version with browser redirection activated.
* [wpmlcore-2646] Improved `sitepress.js` to use `var` when declaring variables, so to allow minification and combination of scripts in strict mode
* [wpmlcore-2638] `wp_get_archives` now works for any custom post type
* [wpmlcore-2629] Fixed Post/Pages accessible without language directory when root page is enabled
* [wpmlcore-2625] Fix language selectors on taxonomy edit screens for WP 4.5
* [wpmlcore-2624] Improved post date syncronization.
* [wpmlcore-2623] Fixed issue with missing trailing slashes when Yoast SEO is enabled.
* [wpmlcore-2612] Fixed unexpected browser redirection on root page
* [wpmlcore-2608] Fixed Yoast SEO sitemap generated, in directory per language excludes other languages.
* [wpmlcore-2606] Added a warning for SEO issues with automatic redirection
* [wpmlcore-2604] Fixed compatibility issue with Yoast SEO when domain per language is used.
* [wpmlcore-2602] Fixed post status synchronization if "Copy publishing date" option is used.
* [wpmlcore-2596] Fixed issued with different domain per language and domains beginning with number
* [wpmlcore-2586] Avoid redeclaring the `Twig_Autoloader` class, if another plugin or theme already declared it.
* [wpmlcore-2577] Fixed issue with WP SEO disabling Adjust IDs for multilingual functionality
* [wpmlcore-2573] Fixed bug when `wpml_active_languages` filter hook is called before or during `wp` action hook
* [wpmlcore-2572] Limit query modification in class Page_Name_Query_Filter
* [wpmlcore-2553] Fixed duplciated URI when it contains a query string and languages in domains is used.
* [wpmlcore-2537] Fixed hook "wpml_make_post_duplicates" to produce duplicate post instead of independent translation.
* [wpmlcore-2529] Fixed default category mismatch when adding a new language.
* [wpmlcore-2524] Fixed post with same name was creating the same sample permalink
* [wpmlcore-2523] Fixed issue in Yoast SEO sitemap which was not showing taxonomies in other languages than default
* [wpmlcore-2206] Fixed edit language issue when option 'Use flag from WPML' for flag file is chosen
* [wpmlcore-2179] Fixed issue inconsistency with Wordpress core - pages can be accessed trough category.
* [wpmlcore-2143] Remove non-translators cache from options.
* [wpmlcore-2067] When the blog page is deleted, and then re-translated, is now possible to set the blog page in Settings > Reading
* [wpmlcore-1428] Removed Moldavian language, since it's a non existing language (use Romanian instead).