# Features
* [wpmlcore-2200] Added `blog_translators` to programmatically change the list of translators
* [wpmlcore-2186] WPML now allows to load a taxonomy template by language, also for the default language
* [wpmlcore-2157] Added `wpml_admin_language_switcher_items` filter to change the languages shown in the admin language switcher
* [wpmlcore-2149] Added button to to clear all WPML caches
* [wpmlcore-2138] fixed `wpml_icon_to_translation` to pass the post ID
* [wpmlcore-2043] Data transfer between domains (when using languages in domains): needed with WooCommerce, and in preparation for other upcoming features

# Fixes
* [wpmlcore-2325] Resolves database error when adding new sites in network installations
* [wpmlcore-2320] Fixed issue with bbPress and WPML configuration in network installations
* [wpmlcore-2317] Don't show the taxonomy language switcher on the taxonomy edit page
* [wpmlcore-2286] Resolved `PHP Notice: bbp_setup_current_user was called <strong>incorrectly</strong>.`
* [wpmlcore-2278] Resolved dependency on of trid on browser referrer
* [wpmlcore-2277] Fixed issue with comments not being shown on back-end in multisites using WooCommerce
* [wpmlcore-2269] Adjust the current language if it's different from the language of the menu in the get param
* [wpmlcore-2259] Fix translation of taxonomy labels when original is not in English
* [wpmlcore-2243] Fixed compatibility issues with Views
* [wpmlcore-2233] Terms count are properly updated when setting a post a private
* [wpmlcore-2232] `\SitePress::pre_option_page` properly caches data
* [wpmlcore-2230] Fixed database errors when showing pages list and there are no taxonomies associated with the pages post type
* [wpmlcore-2225] Fix synchronizing post private state
* [wpmlcore-2224] Fixed random DB Errors & maximum execution time errors when upgrading to WPML 3.3
* [wpmlcore-2212] Password-protected posts and private status are properly copied to translations, when this setting is enabled
* [wpmlcore-2201] Fixes issue with password-protected posts in different domains per language configurations
* [wpmlcore-2196] Fixed performance issues when lists posts (in particular, but not only, WooCommerce products)
* [wpmlcore-2185] Fixed issue with duplicated posts reverting to scheduled state with missed schedule status
* [wpmlcore-2176] Removed Translation Management dependency when duplicated posts are updated
* [wpmlcore-2171] Resolved notices when selecting "All languages" in admin
* [wpmlcore-2168] Fix so that taxonomies can have a custom language template for the default language
* [wpmlcore-2167] Resolved broken settings issue with WooCommerce during WPML activation
* [wpmlcore-2158] Fixed issue with menu theme location being lost after updating another menu item
* [wpmlcore-2144] Fixed incosisten behavior with hierarchical post types using the same slug
* [wpmlcore-2134] Fixes the issue of errors in the communication with ICL leading to a white-screen
* [wpmlcore-2130] Categories to navigation menu can be now added when WPML is active
* [wpmlcore-2125] get_term_children results will be consistent when there is an element with the same id in `icl_translations`
* [wpmlcore-2113] Removed asynchronous AJAX requests
* [wpmlcore-2102] Incorrect .htaccess when using a directory for the default language
* [wpmlcore-2093] Administrator language switcher will be only added after installation is completed
* [wpmlcore-2087] Resolved http(s) protocol and different domains per language issues
* [wpmlcore-2076] Fixes compatibility issues with languages switcher and 2015 theme
* [wpmlcore-2058] Resolved notice "Undefined index: strings_language" after WPML activation﻿
* [wpmlcore-2055] Improved browser redirect performances
* [wpmlcore-2047] Filter url for scripts and styles when language is per domain
* [wpmlcore-2038] Fixes the issue of `WPML_Root_Page` class not being included in some cases
* [wpmlcore-2024] The "Translate Independently" button now work when autosave is off
* [wpmlcore-1887] Fixed an issue causing a notice and incorrect results for certain taxonomy queries that involved custom post types
* [wpmlcore-1810] Removed obsolete setting "Add links to the original content with rel="canonical" attributes."
* [wpmlcore-1797] Fixed the "Display hidden languages" options for users
* [wpmlcore-1499] Fixed issue with javascript browser redirection happening even in default language
* [wpmlcore-1468] Setting a menu language switcher as "horizontal" now allows to set it back to "dropdown"
* [wpmlcore-1347] Improved multiple posts duplication performances