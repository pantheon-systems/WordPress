=== All In One Schema Rich Snippets ===
Contributors: brainstormforce, yawalkarm
Donate link: https://www.brainstormforce.com
Tags: schema markup, structured data, rich snippets, schema.org, Microdata, schema
Requires at least: 3.7
Tested up to: 4.9.8
Stable tag: 1.5.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Boost CTR. Improve SEO & Rankings. Supports most of the content type. Works perfectly with Google, Bing, Yahoo & Facebook.

== Description ==

Get eye catching results in search engines with the most popular schema markup plugin. Easy implementation of schema types like Review, Events, Recipes, Article, Products, Services etc

= What is a Rich Snippet? =
It is basically a short summary of your page in the search results of Google, Yahoo, Bing and sometimes in the News feed of Facebook in nice format with star ratings, author photo, image, etc.
[See Examples of Rich Snippets Here.](https://wpschema.com/free-rich-snippets-schema-plugin-for-wordpress/?utm_source=wp-org-readme&utm_medium=rich-snippet-example "Rich Snippets Examples")

= How does a Rich Snippet help? =
- It gives search engines only the important & precise information to display in search result snippets.
- Rich Snippets are very interactive (photos, star ratings, price, author, etc.) to let you stand out from competition
- [See what difference it makes](https://wpschema.com/free-rich-snippets-schema-plugin-for-wordpress/ "See the difference") in CTR (Click Through Rate)
- Helps you rank higher in search results
- Helps Facebook display proper information when users share your links on Facebook
> **Curious, how does this plugin work?**


= Supported Content Types - =
This plugin supports the following types of Schemas:
* Review
* Event
* People
* Product
* Recipe
* Software Application
* Video
* Articles 

= Future release would include - =
* Breadcrumbs
* Local Business
* Books
= Want to contribute to the plugin? =
You may now contribute to the plugin on Github: [All in one Schema.org Rich Snippets on Github](https://github.com/brainstormforce/All-In-One-Schema.org-Rich-Snippets "Contribute on Github")

== Installation ==

= Through Dashboard =
1. Go to Plugins -> Add New -> Search for "All in One Schema.org Rich Snippets" Or Upload the plugins zip file
= Through FTP =
1. Upload the plugin into `wp-content/plugins` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. That's It. 
Now go and create a new post. There you will find a meta box, select the type of post from dropdown and fill out the details, publish the post.
Google will start showing rich snippets in the search results, as soon as your post is crawled.

You can test the rich snippet on Google Webmasters Rich Snippets Testing Tool

== Frequently Asked Questions ==

= What is a Rich Snippet? =
It is basically a short summary of your page in the search results of Google, Yahoo, Bing and sometimes in the News feed of Facebook in nice format with star ratings, author photo, image, etc.
= How does a Rich Snippet help? =
- It gives search engines only the important & precise information to display in search result snippets.
- Rich Snippets are very interactive (photos, star ratings, price, author, etc.) to let you stand out from competition
- [See what difference it makes](https://www.brainstormforce.com/schema-rich-snippets/ "See the difference") in CTR (Click Through Rate)
- Helps you rank higher in search results
- Helps Facebook display proper information when users share your links on Facebook

= Which Content Types are Supported? =
This plugin currently supports almost all of the content types that are released by Schema.org at one place.
Review, Event, People, Product, Recipe, Software Application, Video, Articles etc.

== Screenshots ==
1. Meta box in post-new under the editor screen.
2. Select content type from dropdown
3. Fill the details as much as you can
4. Test the post or page URL in Google Rich Snippets Testing

== Changelog ==

= 1.5.4 =
* Improvement: Dashboard UI Updated.
* Fix: Removed publisher logo width-height meta tags.
* Fix: Removed default border CSS for images in frontend.

= 1.5.3 =
* Improvement: Updated schema exiting action and enqueue files function.

= 1.5.2 =
* Fix: Frontend Summary box structure validation issue.
* Fix: Editor object undefined issue lead js issue in the page.

= 1.5.1 =
* Fix: Plugin outputting extra output causing Ajax calls to break after last update.

= 1.5.0 =
* Improvement: Improved overall the security of the plugin by using sanitization and escaping the attributes wherever possible, checking nounce and user capabilities before any actions are performed.
* Fix: XSS Vulnerability in the settings page, Thanks for the report Neven Biruski (DefenseCode).
* Fix: Missing closing div tag in the generated schema markup breaking style for some themes.
* Fix: Load the external scripts without protocol to prevent it from breaking on https sites.

= 1.4.4 =
* Fix: PHP fatal error to older version of PHP

= 1.4.3 =
* Fix: WooCommerce Support Added

= 1.4.2 =
* Improvement: Added company/organization and address in people schema. 
* Improvement: Added nutrition & ingredients in recipe schema. 
* Improvement: Added software image & operating system in software application schema. 
* Improvement: Added video description in software application schema. 
* Improvement: Added author, publisher - organization and publisher logo in article schema. 
* Improvement: Added provider location, provider location image, and telephone in service schema. 
* Improvement: Changes admin bar test rich snippet redirect link to the structured data testing tool.
* Fix: removed all error in schema according to structured data testing tool.

= 1.4.1 =
* Fix: Compatibility fix WordPress 4.7.

= 1.4.0 =
* Added new service schema
* Minor CSS fixes

= 1.3.0 =
* Improvement: Updated markup data to meet Google Structured data guidelines
* Fixed: WordPress 4.4 compatibility
* Fixed: Admin UI on small screens

= 1.2.0 =
* Improvement: WordPress 4.0 compatibility
* Fixed: Colorpicker breaking other plugins colorpicker settings.

= 1.1.9 =
* Fixed: Image uploading in meta issue resolved.
* Fixed: Compatibility with WordPress 3.9

= 1.1.8 =
* Fixed: CSS and JS now loads on the page / post where rich snippets are configured.

= 1.1.7 =
* Improvement: Added "Test Rich Snippets" menu in admin bar for testing rich snippets in Google Webmasters Tools
* Fixed: retina.js issue resolved
* Removed unnecessary code

= 1.1.6 =
* Improvement: Compatibility with WordPres 3.8
* Fixed: Admin CSS breaking tabs in WP 3.8
* Added - reference post url field in "contact developers" form on settings page

= 1.1.5 =
* Improvement: Replaced rating 'count' with 'votes' on products - as directed by Google
* Fixed: Article snippet not displaying accurate when snippet title is blank
* Fixed: Recipe string 'Published on' can be changed.

= 1.1.4 =
* Fixed:  Illegal string offset `user_rating` Warning

= 1.1.3 =
* Improvement : Network Activation

= 1.1.2 =
* Fixed: Edit media functionality.

= 1.1.1 =
* Added: Article type
* Added: Compatibility with WooThemes Plugins and themes
* Added: New Media Manager for uploading images in metabox

= 1.1.0 =
* Added: Admin options
* Fixed: Ratings on recipe, products and software application
* Improvement: Admin options for customizing everything
* Improvement: New snippet box design with responsive layout

= 1.0.4 =
* Fixed: Rating on Comments
* Fixed: On deleting any deactivated plugin
* Fixed: Error message comming on commenting
* Fixed: On post save draft

= 1.0.3 =
* Clean up the code
* Fixed: Plugin activation error
* Fixed: Error on editing theme and plugin files.
* Removed : Breadcrumbs

= 1.0.2 =
* Added: RDFa Breadcrumbs Plugin is now a part of All in One Schema.org Rich Snippets !
* Added: Star rating and review for recipe
* Fized: Recipe type
* Fixed: Post update error

= 1.0.1 =
* Minor Bugs Fixes

= 1.0 =
* Initial Release.
