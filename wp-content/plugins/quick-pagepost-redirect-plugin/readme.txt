=== Quick Page/Post Redirect Plugin ===
Contributors: anadnet
Tags: redirect, 301, 302, meta, plugin, forward, nofollow, posts, pages, 404, custom post types, nav menu
Donate Link: 
Requires at least: 4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tested up to: 4.3.1
Stable tag: 5.1.8

Easily redirect pages/posts or custom post types to another page/post or external URL by specifying the redirect URL and type (301, 302, 307, meta).

== Description ==
**Current Version 5.1.8**

This plugin has two redirect functionalities - **"Quick Redirects"** and **"Individual Redirects"**:

= QUICK REDIRECTS (301 Redirects) =
Quick Redirects are designed to be quick and simple to add. You do not need to have an existing page or post set up to add one. You just put the Request URL and the Destination URL and the plugin will redirect it. This type of redirect is great for fixing typos when a page was created, redirecting old URLs to a new URL so there is no 404, and to redirect links from an old site that has been converted to WordPress.

= INDIVIDUAL REDIRECTS (for existing pages/posts) =
For pages/posts that already exist, the plugin adds an option meta box to the edit screen where you can specify the redirect location and type (301, 302 or meta). This type of redirect is useful for many things, including menu items, duplicate posts, or just redirecting a page to a different URL or location on your existing site. 

For best results use some form of WordPress Permalink structure. If you have other Redirect plugins installed, it is recommended that you use only one redirect plugin or they may conflict with each other or one may take over before the other can do its job.

= What You CAN Do (aka, Features): = 
* Works with WordPress Nav Menus
* Works with WordPress Custom Post Types (select setting on options page)
* You can set a redirected page or menu item to open in a new window (Quick Redirects require **Use jQuery?** option to be set)
* You can add a *rel="nofollow"* attribute to the page or menu item link for the redirect (Quick Redirects require **Use jQuery?** option to be set)
* You can completely re-write the URL for the redirect so it takes the place of the original URL (rewrite the href link)
* You can redirect without needing to create a Page or Post using Quick Redirects. This is useful for sites that were converted to WordPress and have old links that create 404 errors (see FAQs for more information).
* Destination URL can be to another WordPress page/post or any other website with an external URL. 
* Request URL can be a full URL path, the post or page ID, permalink or page slug.
* Option Screen to set global overrides like turning off all redirects at once, setting a global destination link, make all redirects open in a new window, etc.
* View a summary of all redirected pages/posts, custom post types and Quick Redirects that are currently set up.
* Plugin Clean up functions for those who decide they may want to remove all plugin data on uninstall.
* Import/Export of redirects for backup, or to add bulk Quick Redirects.
* Built-in FAQs/Help feed that can be updated daily with relevant questions.
* Optional column for list pages to easily show if a page/post has a redirect set up and where it will redirect to.
* Helper functions for adding or deleting redirects programmatically (see 'filters-hooks-helper_functions.txt' file in plugin folder for help and usage).

= What You CANNOT Do: =
* This plugin does not have wild-card redirect features.
* This plugin DOES NOT modify the .htaccess file. It works using the WordPress function wp_redirect(), which is a form of PHP header location redirect.
* You cannot redirect the Home (Posts) page - unless you set a page as the home page and redirect that.
* If your theme uses some form of custom layout or functionality, some features may not work like open on a new window or no follow functionality UNLESS you have the **Use jQuery?** option to set.

This plugin is not compatible with WordPress versions less than 4.0. Requires PHP 5.2+.

**PLEASE NOTE:** A new page or post needs to be Published in order for Page/Post redirect to happen for Individual Redirects (existing page is not necessary for Quick Redirects). It WILL work on a DRAFT Status Post/Page ONLY, and I mean ONLY, if the Post/Page has FIRST been Published and the re-saved as a Draft. This does not apply to Quick Redirects.

= TROUBLESHOOTING: =
* To include custom post types, check the setting on the plugin option page - and you also can hide it from post types you don't want it on.
* If you experience jQuery conflicts with the plugin, try turning off the **Use jQuery?** setting in the options page. BUT, please note that if this option if off, the new window and no follow functionality may be inconsistent (this mainly depends on how your theme is set up)
* If you check the box for "Show Redirect URL below" on the edit page, please note that you MUST use the full URL in the Redirect URL box. If you do not, you may experience some odd links and 404 pages, as this option changes the link for the page/post to the EXACT URL you enter in that field. (i.e., if you enter '2' in the field, it will redirect to 'http://2' which is not the same as 'http://yoursite.com/?p=2').
* If your browser tells you that your are in an infinite loop, check to make sure you do not have pages redirecting to another page that redirects back to the initial page. That WILL cause an infinite loop.
* If you are using the Quick Redirects method to do your redirects, try to use Request URLs that start with a '/' and are relative to the root (i.e., 'http://mysite.com/test/' should be set to '/test/' for the request field).
* If your site uses mixes SSL, use relative links whenever possible (i.e., '/my-page/'). The plugin is designed to detect the incoming protocol and try to apply the appropriate protocol to the destination URL.
* Links in page/post content and links that are created using get_permalink() or the_permalink() will not open in a new window or add the rel=nofollow UNLESS you have the **Use jQuery?** option set.
* If your page or post is not redirecting, this is most likely because something else like the theme functions file or another plugin is outputting the header BEFORE the plugin can perform the redirect. This can be tested by turning off all plugins except the Quick Page/Post Redirect Plugin and testing if the redirect works. Many times a plugin or bad code is the culprit.
* We try to test the plugin in many popular themes and alongside popular plugins. In our experience, (with exception to a few bugs from time to time) many times another plugin is the cause of the issues - or a customized theme. If you do notice a problem, please let us know at info@anadnet.com - along with the WP version, theme you are using and plugins you have installed - and we will try to troubleshoot the problem. 
* Check the FAQs/Help located in the Plugin menu for more up to date issues and fixes.

== Installation ==

= If you downloaded this plugin: =
1. Upload `quick_page_post_redirect` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Once Activated, you can add a redirect by entering the correct information in the `Quick Page/Post Redirect` box in the edit section of a page or post
1. You can create a redirect with the 'Quick Redirects' option located in the Quick Redirects admin menu.

= If you install this plugin through WordPress 2.8+ plugin search interface: =
1. Click Install `Quick Page/Post Redirect Plugin`
1. Activate the plugin through the 'Plugins' menu.
1. Once Activated, you can add a redirect by entering the correct information in the `Quick Page/Post Redirect` box in the edit section of a page or post
1. You can create a redirect with the 'Quick Redirects' option located in the Quick Redirects admin menu.

== Frequently Asked Questions ==
** SEE A LIST OF MORE UP TO DATE FAQS IN THE PLUGIN MENU ITSELF ** 

= Why is my Page/Post not redirecting? =
FIRST - make sure it is active if using Individual Redirects (set up on the edit page for a post or page). Then, check to make sure the global option to turn off all redirects is not checked (in the plugin options).

SECOND - if you are using Quick Redirects, try using links relative to the root (so 'http://mysite.com/contact/' would be '/contact/' if using the root path). If your site is in a sub-folder (set in Settings/General), do not use the sub-folder in the root path as it is already taken into consideration by WordPress. 

NEXT - clear your site's cache files if you are using a caching plugin/theme. You may also need to clear your browser cache and internet files if you use caching - the browser WILL hold cached versions of a page and not redirect if there was no redirect in the cached version.

FINALLY - if you are not using a permalink structure of some sort, it is recommended that you set up at least a basic one. Redirects without a permalink structure can be inconsistant.

If your page or post is still not redirecting, then it is most likely because something else like the theme functions file or another plugin is outputting the header BEFORE the plugin can perform the redirect. This can be tested by turning off all plugins except the Quick Page/Post Redirect Plugin and testing if the redirect works. many time a plugin or bad code is the culprit - or the redirect is just simply turned off. 

We have tested the plugin in dozens of themes and a whole lot more plugins. In our experience, (with exception to a few bugs) many times another plugin or the theme scripting is the problem. If you do notice a problem, please let us know at info@anadnet.com - along with the WP version, theme you are using and plugins you have installed - and we will try to troubleshoot the problem. 

= Should I use a full URL with http:// or https:// ? =
Yes, you can, but you do not always need to. If you are redirecting to an external URL, then yes. If you are just redirecting to another page or post on your site, then no, it is not needed. When in doubt, use the entire URL. For Quick Redirects, it is recommended that you use relative URLs whenever possible.

= Can I do a permanent 301 Redirect? =
Yes. You can perform a 301 Permanent Redirect. Additionally, you can select a 302 Temporary or a 307 Temporary redirect or a Meta redirect. Quick Redirects are always 301 unless you override them with a filter.

= Is the plugin SEO friendly? =
Yes it is.
The plugin uses standard redirect status methods to redirect the URLs. SEO crawlers use the status code to determine if a page request is available, moved or if there is some other error. 

If you do not want a search engine to follow a Redirect URL, use the No Follow option to add 'rel="nofollow"' to the link.

= If I redirect a page that has a good Ranking in Google, will I lose that Ranking? =
The answer is dependent on two things:

1. What type of redirect it is
2. What content is on the redirected page.

If you use a redirect of 301 AND the content on the destination URL is the same as the original page (just a different URL), then allof the ranking and 'link juice', as they say, will stay with the page.

If you use a redirect of 301 and the content is different, then it will be indexed and ranked accordingly, as any other page would.

If you use a 302 redirect, the search engines will not change anything, but also index the destination page as it would any other page.

= Do I need to have a Page or Post Created to redirect? =
No. There is a Quick Redirects feature that allows you to create a redirect for any URL on your site. This is VERY helpful when you move an old site to WordPress and have old links that need to go some place new. For example, 
If you had a link on a site that went to http://yoursite.com/aboutme.html you can now redirect that to http://yoursite.com/about/ without needing to edit the htaccess file. You simply add the old URL (/aboutme.html) and tell it you want to go to the new one (/about/). Simple as that.

The functionality is located in the QUICK REDIRECTS menu. The old URL goes in the Request field and the to new URL goes in the Destination field. Simple and Quick!

= Does the Page/Post need to be Published to redirect? =
YES... and NO... The redirect will always work on a Published Post/Page. For it to work correctly on a Post/Page in DRAFT status, you need to fist publish the page, then re-save it as a draft. If you don't follow that step, you will get a 404 error. 

= Can I add 'rel="nofollow" attribute to the redirect link? =
YES, you can add a ' rel="nofollow" ' attribute for the redirect link. Simply check the "add rel=nofollow" box when setting up the redirect on the page/post edit page. Note - this option is only available for the Quick Redirects method when the 'Use jQuery?' functionality is enabled in the settings and you select the 'NF' box for the corresponding redirect.

= Can I make the redirect open in a new window? =
YES, you can make the redirect link open in a new window. Simply check the "Open in a new window" box when setting up the individual redirect on the page/post edit page. Note - this option is only available for the Quick Redirects method when the 'Use jQuery?' functionality is enabled in the settings and you select the 'NW' box for the corresponding redirect.

= I still can't get the OPEN IN NEW WINDOW option to work... why? =
First, make sure you have the 'Use jQuery?' option set in the options page. This funcitonality drastically increases the plugin's ability to add the correct properties and attributes to the links to make them work as desired.

If you cannot us this option (because of a conflict with another script), then you may only have limited success with this feature. 
The reason - some themes put custom links in the menu, like RSS and other similar items. Many times (an this is usually the main reason why), they do not use the WP hook to add the menu item to the list - they literally just put it there. Unless the theme uses the internal WordPress hooks to call the menu, redirects, open in a new window and rel=nofollow features just will not work.
ADDITIONALLY - Links in page/post content and Permalinks will not open in a new window or add the rel=nofollow. That is because the theme template actually sets up the links by calling "the_permalink()" function so add these elements is not consistently possible so it has been excluded from the functionality. The links will still redirect just fine but without that feature.

= I want to just have the link for the redirecting page/post show the new redirect link in the link, not the old one, can I do that? =
YES, you can hide the original page link and have it replaced with the redirect link. Any place the theme calls either "wp_page_links", "post_links" or "page_links" functions, the plugin can replace the original link with the new one. Simply check the "Show Redirect URL" box when setting up the redirect on the page/post edit page. 

Note - This option is available for the Quick Redirects only with the 'Use jQuery?' option enabled. 

= I have Business Cards/Postcards/Ads that say my website is http://something.com/my-name/ or http://something.com/my-product/, but it should be a different page, can I set that up with this? =
YES! Just set up a Quick Redirect (see above) and set the Request URL to `/my-name/` or `/my-product/` and the Destination URL to the place you want it to go. The destination doesn't even need to be on the same site - it can go anywhere you want it to go!

= What the heck is a 301 or 302 redirect anyway? =
Good question! The number corresponds with the header code that is returned to the browser when the page is first accessed. A good page, meaning something was found, returns a 200 status code and that tells the browser to go ahead and keep loading the content for the page. If nothing is found a 404 error is returned (and we have ALL seen these - usually it is a bad link or a page was moved). There are many other types of codes, but those are the most common. 

The 300+ range of codes in the header tells the browser (and search engine spider) that the original page has moved to a new location - this can be just a new file name a new folder or a completely different site.

A 301 code means that you want to tell the browser (or Google, bing, etc.) that your new page has permanently moved to a new location. This is great for search engines because it lets them know that there was a page there once, but now go to the new place to get it - and they update there old link to is so future visitors will not have to go through the same process.

A 302 or 307 code tell the browser that the file was there but TEMPORARILY it can be found at a new location. This will tell the search engines to KEEP the old link in place because SOME day it will be back at the same old link. There is only a slight difference between a 302 and a 307 status. Truth is, 302 is more widely used, so unless you know why you need a 307, stick with a 302.

= So, which one do I use? =
Easiest way to decide is this: If you want the page to permanently change to a new spot, use 301. If you are editing the page or post and only want it to be down for a few hours, minutes, days or weeks and plan on putting it back with the same link as before, then us 302. If you are having trouble with the redirects, use a `meta` redirect. The meta redirect actually starts to load the page as a 200 good status, then redirects using a meta redirect tag. 

Still not sure? Try 302 for now - at least until you have a little time to read up on the subject.

= If I have a redirect in place, can I view the original page during testing? =
Yes, use the URL as normal, and add `?action=no-redirect` to the query data (or `&action=no-redirect` if there is already query data present).

For example. If you set up a redirect for the page `http://mysite.com/old-page/` and you want to see the page (and not have it redirect on you so you can look at it), type the URL as `http://mysite.com/old-page/?action=no-redirect` and it will load like there is no redirect present.

= That's all the FAQs you have? =
NO it isn't! Check the plugin FAQs/Help page for a more up to date list of Frequently Asked Questions. The plugin now has a live feed of FAQs that can be updated regularly. If you have something you think we should add, please let us know.

== Screenshots ==
1. Quick Redirects setup page - Now with ajax editing.
2. Import and Export features.
3. Options/Setting Page.
4. Summary of redirects plugin page.
5. FAQs/Help Page. This is updated via an RSS feed so it can be updated regularly with fixes and common questions.
6. New Redirect Column (optional) for pages/posts and custom post types. Easily see if a page has a redirect and where it goes. Turn off in settings.
7. Meta Redirect Options Page.

== Changelog ==
= TODO =
* THIS SECTION IS JUST TO KEEP TRACK OF TODO ITEMS FOR FUTURE UPDATES.
* Add New Window and No Follow to links where the URL has been rewritten. Currently if you rewrite the URL neither will work as they are referenced with the original URL, not the rewrite.

= 5.1.8 =
* **Bug Fix:** Used a different minified version for the qppr_frontend_script.min.js file after it received a false positive of being a Trojan. The 5.1.7 version is totally safe to use too. The false positive was caused due to the way the specific file was minified. This is a confirmation that nothing was infected or was acting as a trojan of any sort in any previous version.
= 5.1.7 =
* **Bug Fix:** Fixed Post redirects bug caused by 5.1.6

= 5.1.6 =
* **Security Fix:** Fixed security concern in the ppr_parse_request_new method

= 5.1.5 =
* **Feature Addition:** Add Canonical Redirect detection to fix potential www/non-www redirect match problems. Removed from TODO!
* **Deletion:** Took out testing code that was accidentally left in the previous version.
* **Filter Addition:** Added 'qppr_filter_quickredirect_index' filter to allow changing the the index just before the redirect. See filters-hooks-helper_funcitons.txt in plugin folder for usage.

= 5.1.4 =
* **Feature Addition:** Added filter to Meta Box call to allow people to adjust context and priority if they choose. See filters-hooks-helper_funcitons.txt in plugin folder for usage. Thanks [mdmoreau](https://wordpress.org/support/profile/mdmoreau) for the suggestion!
* **Feature Addition:** Added 'action=no-redirect' to be able to view a redirect page without the redirect triggering. Thanks [One Eye Pied](https://wordpress.org/support/profile/one-eye-pied) for the suggestion!
* **Bug Fix:** Adjusted line ending characters for Import/Export to try to allow both Unix and Dos line break characters (LF and CRLF) on Import. Thanks [Jose Luis Cruz](https://wordpress.org/support/profile/joseluiscruz) for pointing this out!
* **Bug Fix:** Fixed database query for jQuery localization funciton. Was a major resource hog on sites with a lot of posts and would crash MySQL on some sites.
* **Update:** Fixed some spelling errors (thanks to those of you who pointed them out).
* **Update:** Updated English Translations.

= 5.1.3 =
* **Update:** Updated English Translations.
* **Bug Fix:** Fixed Meta redirect functions so browsers that no longer allow refresh redirects can still use Meta redirects (i.e., Firefox, Edge, some IE).
* **Bug Fix:** Fixed Function for Individual Redirects for New Window functionality. Was not working unless both No Follow and New Window were selected.
* **TODO:** Add New Window and No Follow to links where the URL has been rewritten. Currently if you rewrite the URL neither will work as they are referenced with the original URL, not the rewrite.
* **TODO (Still):** Add Canonical Redirect filter to fix potential www/non-www redirect match problems.

= 5.1.2 =
* **Update:** Updated English Translations.
* **Update:** Updated license.txt file (had wrong version of license).
* **Update:** Verified plugin for WordPress 4.3 compatibility.
* **Update:** Add check if current user can manage_options when deleting All Redirects. Prevents logged in users without permissions from maliciously deleting redirects.
* **Update:** Minify JavaScript files for front-end and admin (non-minified files still remain in the file structure for reference).
* **Feature Addition:** Added experimental function to delete cache files (for W3 Total Cache, WP Super Cache and WP Fastest Cache) on functionality saves (add/remove redirects and options updates) to try to help some users resolve caching issues after an update with these plugins installed.
* **Feature Addition:** Added "helper" functions to allow for adding or deleting Quick and Individual Redirects programatically (see filters-hooks-helper_functions.txt in plugin directory for more info).
* **Bug Fix:** Fix ajax function for Quick Redirect delete and save (if a redirect was deleted, anything below it would not be correctly referenced and deleting or editing would not work)
* **Bug Fix:** Fix to 'rewrite URL' jQuery function that was replacing text instead of HTML - thanks Leo Kerr <leo@myelectriccar.com.au>
* **TODO (Still):** Add Canonical Redirect filter to fix potential www/non-www redirect match problems.

= 5.1.1 =
* Fix 'array to string' error message on Quick Redirect save - thanks Simon Codrington <simon@webbird.com.au>
* TODO: Add Canonical Redirect filter to fix potential www/non-www redirect match problems.
* Added Meta Redirect Options Page - this splits out Meta Options from the main options page.
* Added more enhanced meta redirect scripting to allow for tracking or other page content (including countdown if desired).
* Fixed Layout issues on Quick Redirect Page making it impossible to edit redirects in some cases.
* Fixed a few spelling errors. 
* Added Help Content to Meta Options page.
* Added metabox setting for meta redirect seconds for individual redirects so you can set different time for each meta redirect.
* Update POT and English Translation file - added limited Spanish translations.

= 5.1.0 =
* Fix security issue for deleting ALL Quick and Individual Redirects.
* Update POT and English Translation file.
* Added 'No Quick Redirects' message when there are no Quick Redirects.

= 5.0.7 =
* Added textdomain for future translations. English Complete.
* Change Quick Redirects page to use ajax to save and edit existing redirects, instead of all redirects in post fields. This is to eliminate the 'max_input_vars' setting in php from stopping large numbers of redirects from saving.
* Fix Metabox loading issues for custom post types.
* Added 'redirect' column for post, page and custom post type listing pages.
* Fixed sanitizing URL on saving of redirects (now will not strip encoded characters and spaces).
* Optimized CSS styles and JavaScript files for admin.
* Added Admin Pointers for new features.
* Added jQuery script to front end pages to better handle New Window/No Follow Functionality.
* Split out Import / Export feature to make it easier to find.
* Update Query String add back function - patch submitted for version 5.0.7 by Romulo De Lazzari <romulodelazzari@gmail.com> (thanks!)

= 5.0.6 =
* Fix to some users getting Warning messages for parse_url function.
* Added nonce field checking for Quick Redirects form to help eliminate the possibility of form takeover on submission of quick redirect saves.

= 5.0.5 =
* Fix to security flaw for logged in admin users.
* Fix to extra spaces that broke some callback functions in the redirect class in 5.0.4.

= 5.0.4 =
* Minor bug cleanup
* Security fixes: fixed possible cross-scripting vulnerability in saving of data to options.
* Changed the hook call level for the redirects hook on normal redirects so it will not interfere with some other plugins.


= 5.0.3 =
* Minor bug cleanup update - (no new features added)
* Bug fixes: JavaScript ghost js file call fixed. Actions hooks not applying issue fixed. Querystring redirect issue addressed. Unset index errors addressed. Some Network/MU problems fixed.
* Modified Import and Export scripts to export a more editable export file. Import can be either old encoded version or new readable PIPE version.
* Typos and minor layout issues addressed.

= 5.0.2 =
* Bug fixes and jQuery now set to off until issues are resolved.
* Set Case Sensitive to on by default - Some people having issues with infinite loops.

= 5.0.1 =
* Fix to jQuery conflict issue.

= 5.0 =
* Added jQuery version check to ensure no problems with themes forcing older versions of jQuery
* Added a few warning /info messages to Quick Redirects page.
* Redirect summary was updated to display Quick Redirects as well as individual redirects. Now it is easier to see at a glance what redirects you have set up.
* Rewrite of Quick Redirects functions to allow selecting Open in New Window (NW) and rel=nofollow (NF) as long as **use jQuery?** is selected. 
* Added "use jQuery" option on settings page - on by default after upgrade
* Added jQuery redirect replace, target="_blank", and rel="nofollow" to increase success for additional options (mainly Quick redirects).
* Changed out WP_PLUGIN_URL for plugins_url() to help resolve errors in redirects for SSL/https
* Changed the way custom post types are handled.These are now on by default for new users - or users who have not specifically set to off.
* The ability to turn off the Plugin Meta Box for any post type was added (admin permissions required).
* Import and Export features were added to allow for backup of existing Quick Redirects, Restoring a backup or adding bulk redirects.
* Plugin clean-up features were added to completely remove either Page/Post meta data (for regular redirects), Quick Redirects, or both.
* Several filter and action hooks were added to help users better integrate the plugin into their theme, should they need additional functionality.
* New FAQs/Help page with items provided by an RSS feed, so we can easily update FAQs when common questions/issues arise.
* Query String data is now preserved for Quick Redirects (thanks to Jon Wilson for the contribution).
* Case insensitivity option was added for Quick Redirects (thanks to Brian DiChiara for the contribution).

= 4.2.2 =
* Fix some embarrassing spelling errors.(07/14/2011)
* Fix Quick Redirects links from inside the redirect edit box and plugin page - they would give a "not authorized" warning because the page location changed in version 4.0 (07/14/11)

= 4.2.1 =
* Fix to trailing slash non-redirect for quick redirects.(06/28/2011)
* Note - this was not a public version fix, but a dev testing version - this fix is publicly included in 4.2.2.

= 4.2 =
* Fix to menus pages always opening in New Window even when not selected.(05/08/2011)
* Fix Categories/Archives automatically redirecting to the first post with redirect set if any post on the page had a redirect set.(05/08/2011)
* Fix Homepage redirecting to first post with redirect set if using posts as home and any post had a redirect.(05/08/2011)
* Fix misrepresentation of new window global setting on options page. Should read that "all redirects WILL open in a new window" not "will NOT open in a new window". (05/08/2011)
* Update description to note that the plugin requires PHP 5+ because some of the class calls will not work in php4 (plugin will not activate). (05/08/2011)

= 4.1 =
* Fix Minor spelling issues and code typos.(05/05/2011)

= 4.0 =
* Rewrite of all functions for better optimization.(05/01/2011)
* Added consolidated DB call at class setup to reduce DB calls to one call per page load.(05/01/2011)
* Moved entire plugin into a class for easier updates.(05/01/2011)
* Added new Options page with Global Overrides.(05/02/2011)
* Integrated Custom Post Types functionality.(05/02/2011)
* Created a Summary Page for a quick glace of set up redirects.(05/04/2011)
* Moved Quick Redirects menu from settings to a new Redirects Menu.(05/03/2011)
* Added additional checks and validations when adding Quick Redirects.(05/03/2011)
* Added a way to delete Quick Redirects easier.(03/01/2011)

= 3.2.3 =
* Fix New Window and No Follow attributes in themes with older menu calls. (12/29/10)
= 3.2.2 =
* Fix meta tag redirect method. Was broken because of new method of checking redirects with less query calls. (12/16/10)
* Fix php code errors - still had some debugging code live that will cause some users to have problems.(12/16/10)
= 3.2.1 =
* limited test release - testing for some of 3.2.2 release fixes. (12/14/10)
= 3.2 =
* remove functions ppr_linktotarget, ppr_linktonorel, ppr_redirectto and ppr_linktometa.(12/10/2010) 
* re-write functions to consolidate queries. (12/10/2010) 
* added new filters for New menu structure to filter wp_nav_menu menus as well as old wp_page_menus functions. (12/10/2010) 
* cleaned up new window and nofollow code to work more consistently. (12/10/2010) 
= 3.1 =
* Re-issue of 2.1 for immediate fix of issue with the 3.0 version.(6/21/2010)
= 3.0 =
* Enhance filter function in main class to reduce Database calls. (06/20/2010)
= 2.1 =
* Fix Bug - Open in New Window would not work unless Show Link URL was also selected. (3/12/2010)
* Fix Bug - Add rel=nofollow would not work if Open in a New Window was not selected. (3/13/2010)
* Fix Bug - Show Link, Add nofollow and Open in New Window would still work when redirect not active. (3/13/2010)
* Added new preg_match_all and preg_replace calls to add target and nofollow links - more efficient and accurate - noticed some cases where old function would add the items if a redirect link had the same URL. (3/13/2010) 
= 2.0 =
* Cosmetic code cleanup. (2/28/2010)
* Remove warning and error messages created in 1.9 (2/28/2010)
= 1.9 =
* Added 'Open in New Window' Feature. (2/20/2010)
* Added 'rel="nofollow"' attribute option for links that will redirect. (2/20/2010)
* Added 'rewrite url/permalink' option to hide the regular link and replace it with the new re-write link anywhere the link is displayed on the site. (2/20/2010)
* Hid the Custom Field Meta Data that the plugin uses - this is just to clean up the custom fields box. (2/20/2010)
= 1.8 =
* Added a new Quick 301 Redirects Page to allow adding of additional redirects that do not have Pages or Posts created for them. Based on Scott Nelle's Simple 301 Redirects plugin.(12/28/2009)
= 1.7 =
* fix to correct meta redirect - moved "exit" command to "addtoheader_theme" function. Also fixed the problem with some pages not redirecting. Made the plugin WordPress MU compatible. (9/8/2009)
= 1.6.1 =
* Small fix to correct the same problem as 1.6 for Category and Archive pages (9/1/2009) 
= 1.6 =
* Fixed wrongful redirect when the first blog post on home page (main blog page) has a redirect set up - this was redirecting the entire page incorrectly. This was only an issue with the first post on a page. (9/1/2009)
= 1.5 =
* Major re-Write of the plugin core function to hook WP at a later time to take advantage of the POST function - no sense re-creating the wheel. 
* Removed the 'no code' redirect, as it turns out, many browsers will not redirect properly without a code - sorry guys.
* Can have page/post as draft and still redirect - but ONLY after the post/page has first been published and then re-saved as draft (this will hopefully be a fix for a later version). (8/31/2009)
= 1.4 =
* Add exit script command after header redirect function - needed on some servers and browsers. (8/19/2009)
= 1.3 = 
* Add Meta Re-fresh option (7/26/2009)
= 1.2 = 
* Add easy Post/Page Edit Box (7/25/2009)
= 1.1 = 
* Fix redirect for off site links (7/7/2009)
= 1.0 = 
* Initial Plugin creation (7/1/2009)

== Upgrade Notice ==
= 5.1.5 =
* Bug Fixes. 