# Features
* [wpmlcore-3197] Removed the WPML Dashboard Widget to avoid unnecessary calls.
* [wpmlcore-3081] Duplicated content will use the same canonical for all all languages
* [wpmlcore-2952] Canonical for non translated post types must always be the "default language" URL

# Fixes
* [wpmlcore-3172] Reduce SQL queries when checking if site uses ICanLocalize translation services previous to WPML 3.2
* [wpmlcore-3104] ACF Repeater subfields are now visible on edit post screen after downloading translated content with Translation Management
* [wpmlcore-2937] Fixed multisite so that links to posts on sub-sites are correct
* [wpmlcore-2637] Add `wpml_translate_link_targets` filter to fix links to point to translated content
* [wpmlcore-2631] Fixed XML-RPC API calls to set the language of a newly created post (e.g. `metaWeblog.newPost`, `wp.newPost`, and `wp.newPage`).
* [wpmlcore-2590] Fix conversion to absolute link when link is to a post in another language
* [wpmlcore-1761] Fix notice when no field is specified in tax_query when getting posts.