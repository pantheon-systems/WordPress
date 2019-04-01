# Features
* [wpmlcore-2536] Improve Yoast SEO compatibility by filtering out hidden language posts from sitemap
* [wpmlcore-1954] Added constant that disables automatic update of wpml-config.xml configurations: ICL_REMOTE_WPML_CONFIG_DISABLED

# Fixes
* [wpmlcore-2571] Fix browser redirect so redirect only happens if the current page language is not one of the preferred browser languages
* [wpmlcore-2562] Resolved issue when re-activating WPML in sites having corrupted databases
* [wpmlcore-2546] Resolved issue that lead to database corruption and fatal error `InvalidArgumentException: element_id and type do not match`
* [wpmlcore-2528] Cached calls to `glob()` function when auto loading classes
* [wpmlcore-2518] Show "Display hidden languages" option to admins and users users with "translate" capability
* [wpmlcore-2511] Inject "hreflang" meta tags higher in the head.
* [wpmlcore-2505] Fixed URL handling when using Domains per languages in an installation under a sub-folder
* [wpmlcore-2418] Added under the hood logic, to handle taxonomy terms meta translation
* [wpmlcore-2407] Fixed issue with post status when post date sync is enabled
* [wpmlcore-2351] Fixed different results produced in non default languages by queries with mixed post type and taxonomies in filter
* [wpmlcore-1616] Removed references to Font Awesome library and replaced usage with Dashicons