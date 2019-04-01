# Features
* [wpmlcore-5714] Introduced the filter hook `wpml_ls_template_paths`.
* [wpmlcore-5706] Add an option to allow translation of raw HTML in page builders.
* [wpmlcore-5701] Integrated Elementor WP text widget module
* [wpmlcore-5700] Added hooks for customising the behaviour of the browser logic
* [wpmlcore-5692] Add WP Super Cache support for registering cookies which can be configured to be cached
* [wpmlcore-5375] Add support for Avada global elements.

# Fixes
* [wpmlcore-5750] Fixed issue where the translation of a Gutenberg post also added with Gutenberg isn't linked with the original post
* [wpmlcore-5719] Resolved exception resulting to 500 error when changing status of WooCommerce shop page
* [wpmlcore-5709] Fixed the translation not displaying on frontend when Enfold page builder's page is translated with ATE.
* [wpmlcore-5707] Resolved improper canonical link translation with Yoast SEO
* [wpmlcore-5704] Add support for custom grid translation in Visual Composer.
* [wpmlcore-5695] Resolved fatal error "Call to undefined function get_user_by()" with Core network install
* [wpmlcore-5688] New "wpml_custom_fields_sync_option_updated" action for updating custom fields sync preferences
* [wpmlcore-5680] The script controlling the translation services list shouldn't be loaded when that list is not rendered
* [wpmlcore-5677] Do not run the posts' translation update logic when updating the post from the front-end (e.g., when purchasing a product which updates the stock)
* [wpmlcore-5664] Included WPML version when enqueueing language cookie scripts
* [wpmlcore-5662] Strings with former `wpml-media` text domain updated with `sitepress`
* [wpmlcore-5661] Resolved exception with Apache and the .htaccess file when changing language on the permalinks page
* [wpmlcore-5660] Fixed issue when submitting a form from Contact Forms 7 having WPML language set as directory
* [wpmlcore-5659] Resolved exception when using languages per domains and creating a page in the secondary language/domain using Elementor
* [wpmlcore-5656] Do not display the translation editor prompt when activating a page builder and an editor is already selected
* [wpmlcore-5646] Fixed fatal error in WPML > Languages page
* [wpmlcore-5633] Fixed extra slash in flags upload folder when domain per languages activated
* [wpmlcore-5630] Resolved exception preventing user from creating a sticky post when posts are set as non-translatable
* [wpmlcore-5606] Fix problem with loading Elementor global widgets
* [wpmlcore-5591] Resolved bug in Media duplication wizard resulting in an endless AJAX process
* [wpmlcore-5581] Prevent database errors when concurrent requests try to initialize post's languages
* [wpmlcore-5570] Fixed a broken GUI on the upload page with a lang parameter.
* [wpmlcore-5560] Fixed error when registering a string and sending a translation job notification
* [wpmlcore-5470] Fixed an error which prevented from saving page builder with a long text.
* [wpmlcore-5464] Added hreflang in empty categories archives pages
* [wpmlcore-5445] Show media from source language post in translated post for featured image
* [wpmlcore-5443] Fixed issue not letting WooCommerce users edit any customer's detail of an order
* [wpmlcore-5191] Resolved exception with Widget not being displayed in the frontend when using register_widget
* [wpmlcore-5180] Do not display the languages column in non-translatable post types
* [wpmlcore-5091] Limit the terms shown in the "Taxonomy translations" screen to avoid running out of memory
* [wpmlcore-4785] Fixed an issue that prevented us from adding a widget wia WP-CLI.