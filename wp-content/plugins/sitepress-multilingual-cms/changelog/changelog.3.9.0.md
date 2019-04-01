# Features
* [wpmlcore-5004] Fix a compatibility issue with "AJAX load more" plugin
* [wpmlcore-4943] Improved UI for Custom Post Types, Taxonomies and Custom fields in Translation Options or Multilingual Content Setup pages
* [wpmlcore-4933] Fixed wrong locale for Thai language
* [wpmlcore-4912] Included menu classes to the autoloader in order to resolve compatibility issues with iThemes Sync
* [wpmlcore-4876] Removed the "Blog posts to display" feature and used "Display as translated" instead.
* [wpmlcore-4840] Added the possibility to translate images alt text when via the translation editor
* [wpmlcore-4831] Added feedback and logging of failed attempts when downloading the remote configuration files for themes and plugins
* [wpmlcore-4793] Implemented new feature allowing users to set a Post Type or a Taxonomy to display translations or fallback to original language if translations do not exist
* [wpmlcore-4706] Language menu items are now added on `wp_get_nav_menu_items` instead of `wp_nav_menu_objects`

# Fixes
* [wpmlcore-5084] Fixed issue showing translations with Pending Review status to non-logged in users
* [wpmlcore-5061] Fixed a JS error on categories/tags admin page when ACF plugin is active.
* [wpmlcore-5056] Fixed error when trying to get the post type of a non-existent post
* [wpmlcore-5055] Fixed issue with Previous and Next links not working properly in secondary languages when MemCached is configured
* [wpmlcore-5052] Removed unneeded file left behind from a git conflict
* [wpmlcore-5038] Improved WPML Reset functionality
* [wpmlcore-5037] Fixed error when language switcher is added in a menu containing no item
* [wpmlcore-5018] Fixed link field in "Flip Box" module of Elementor Pro in order to make it translatable
* [wpmlcore-5012] Fixed an issue with post tags when the same name is used in different languages
* [wpmlcore-5002] Default locale failed to be set for some language
* [wpmlcore-4981] Fixed a possible issue in WP 4.9 with "tax_query" in WP_Query
* [wpmlcore-4974] Fixed an http protocol issue with language switcher assets
* [wpmlcore-4932] Fixed issue with color picker in the Footer Language Switcher's modal
* [wpmlcore-4922] Fixed wrong redirection in post types containing posts that share same name
* [wpmlcore-4917] Resolved JS conflict with 3rd party plugins in Appearance/Menus page
* [wpmlcore-4878] Handle the hreflang (former Tag) as two letters code by default
* [wpmlcore-4871] Stopped autoloading the heavy "wpml_config_index" and "wpml_config_index" wp_options
* [wpmlcore-4847] Fixed action "wpml_make_post_duplicates" not duplicating custom fields of draft posts
* [wpmlcore-4825] Updated browser redirection hook which enqueues scripts to wp_enqueue_scripts
* [wpmlcore-4796] Resolved exception with "wpml_permalink" feature not returning the translated slug
* [wpmlcore-4792] Fixed issue with requested language from HTTP_REFERRER
* [wpmlcore-4790] Removed the deprecated function `is_ajax()`
* [wpmlcore-4781] Fixed an error in the customizer when no menu exists.
* [wpmlcore-4779] Fixed an issue when synchronizing custom menu links and the default language is not English.
* [wpmlcore-4778] Fixed the language for the notice to promote the translation feedback
* [wpmlcore-4733] Fixed an issue with `get_terms` and mixing translatable and non-translatable taxonomies.
* [wpmlcore-4723] Fixed issue with .mo files for default language not being automatically downloaded
* [wpmlcore-4711] Fixed issue with terms-meta not being copied over to translations.
* [wpmlcore-4703] Wrong redirect to page when post with the same name in /%category%/%postname% permalinks
* [wpmlcore-4664] Resolved exception with Custom Languages added in WPML returning the language code instead of the language tag
* [wpmlcore-4610] Resolved exception with posts in secondary languages wrongly redirecting to the original ones when they have the same slug but they are not linked as translations
* [wpmlcore-4590] Resolved exception with Page id when Home and Blog page have the same slug which was breaking compatibility with "infinite scrolling" of Jetpack
* [wpmlcore-2903] Fixed an issue with post draft not listed when auto-saved only with heartbeat