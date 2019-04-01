=== Fast Velocity Minify ===
Contributors: Alignak
Tags: PHP Minify, Lighthouse, GTmetrix, Pingdom, Pagespeed, CSS Merging, JS Merging, CSS Minification, JS Minification, Speed Optimization, HTML Minification, Performance, Optimization, Speed, Fast
Requires at least: 4.5
Stable tag: 2.6.0
Tested up to: 5.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Improve your speed score on GTmetrix, Pingdom Tools and Google PageSpeed Insights by merging and minifying CSS, JavaScript and HTML, setting up HTTP preload and preconnect headers, loading CSS async and a few more options. 
 

== Description ==

This plugin reduces HTTP requests by merging CSS & Javascript files into groups of files, while attempting to use the least amount of files as possible. It minifies CSS and JS files with PHP Minify (no extra requirements).

There are also options to apply critical CSS and load CSS async, as well as to define HTTP preload and preconnect headers (server push).

Minification is done on the frontend during the first uncached request. Once the first request is processed, any other pages that require the same set of CSS and JavaScript files, will be served that same (static) cache file.

This plugin includes options for developers and advanced users, however the default settings should work just fine for most sites.

= Aditional Optimization =

I can offer you aditional `custom made` optimization on top of this plugin. If you would like to hire me, please visit my profile links for further information.


= Features =

*	Merge JS and CSS files into groups to reduce the number of HTTP requests
*	Google Fonts merging, inlining and optimization
*	Handles scripts loaded both in the header & footer separately
*	Keeps the order of the scripts even if you exclude some files from minification
*	Supports localized scripts (https://codex.wordpress.org/Function_Reference/wp_localize_script)
*	Minifies CSS and JS with PHP Minify only, no third party software or libraries needed.
*	Option to defer JavaScript and CSS files, either globally or pagespeed insights only.
*	Creates static cache files in the uploads directory.
*	Preserves your original files, by duplicating and copying those files to the uploads directory 
*	View the status and detailed logs on the WordPress admin page.
*	Option to Minify HTML, remove extra info from the header and other optimizations.
*	Ability to turn off minification for JS, CSS or HTML (purge the cache to see it)
*	Ability to turn off CSS or JS merging completely (so you can debug which section causes conflicts and exclude the offending files)
*	Ability to manually ignore JavaScript or CSS files that conflict when merged together (please report if you find some)
*	Support for conditional scripts and styles, as well as inlined code that depends on the handles
*	Support for multisite installations (each site has its own settings)
*	Support for gzip_static on Nginx
*	Support for preconnect and preload headers
*	CDN option, to rewrite all static assets inside the JS or CSS files
*	WP CLI support to check stats and purge the cache
*	Auto purging of cache files for W3 Total Cache, WP Supercache, WP Rocket, Wp Fastest Cache, Cachify, Comet Cache, Zen Cache, LiteSpeed Cache, Nginx Cache (by Till Krüss ), SG Optimizer, HyperCache, Cache Enabler, Breeze (Cloudways), Godaddy Managed WordPress Hosting and WP Engine (read the FAQs)
*	and some more...


= WP-CLI Commands =
*	Purge all caches: `wp fvm purge`
*	Purge all caches on a network site: `wp --url=blog.example.com fvm purge`
*	Purge all caches on the entire network (linux): `wp site list --field=url | xargs -n1 -I % wp --url=% fvm purge`
*	Get cache size: `wp fvm stats`
*	Get cache size on a network site: `wp --url=blog.example.com fvm stats`
*	Get cache size on each site (linux): `wp site list --field=url | xargs -n1 -I % wp --url=% fvm stats`


= Notes =
*	The JavaScript minification is by [PHP Minify](https://github.com/matthiasmullie/minify)
*	Compatible with Nginx, HHVM and PHP 7
*	Minimum requirements are PHP 5.5 and WP 4.4, from version 1.4.0 onwards


== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory or upload the zip within WordPress
2. Activate the plugin through the `Plugins` menu in WordPress
3. Configure the options under: `Settings > Fast Velocity Minify` and that's it.


== Screenshots ==

1. The Status and Logs page.
2. The Settings page.
3. The Pro settings.
4. The Developers settings.

== Frequently Asked Questions ==

= Can I update plugins and themes after installing FVM? =
FVM doesn't touch your original files. It copies those files to the cache directory, minifies that copy and merges them together under a different name. If you install new plugins, change themes or do plugin updates, FVM will purge its cache as well as some of the most popular cache plugins.

= After installing, why did my site feels slow to load? =
Please see the question below.

= Why are there lots of JS and CSS files listed on the status page and why is the cache directory taking so much space? =
Some themes combine and enqueue their CSS using a PHP script with a query string that changes on every pageload... (this is to bust cache, but it's bad practice since it prevents caching at all). When FVM sees a different url being enqueued, it will consider that as a new file and try to create a new set of files on every pageview as well. You must then exclude that dynamic url via the Ignore List on the settings for your cache to be efficient and stop growing. Also note, if your pages enqueue different styles or  javascript in different pages (fairly common), that is "one set" of files to be merged. Pay attention to the logs header and look for the page url where those files have ben generated. If you have multiple files generated for the same url, you have some css/js that keeps changing on every pageview (and thus needs exclusion).


= How can I exclude certain assets? =
Each line on the ignore list will try to match a substring against all CSS or JS files, for example `//yoursite.com/wp-content/plugins/some-plugin/js/` will ignore all files inside that directory. You can also shorten the URL like `/some-plugin/js/` and then it will match any css or js URL that has `/some-plugin/js/` on the path. Obviously, doing `/js/` would match any files inside any "/js/" directory and in any location, so to avoid unexpected situations please always use the longest, most specific path you can use. There is no need to use asterisks or regex code (it won't work).


= Why is the ignore list not working? =
The ignore list "is" working, just try to use partial paths (see previous faq) and use relative urls only without any query vars. 


= Is it compatible with other caching plugins? =
You must disable any features on your theme or cache plugins which perform minification of css, html and js. Double minification not only slows the whole process, but also has the high potential of causing conflicts in javascript. The plugin will try to automatically purge several popular cache plugins, however if you have a cache on the server side (some hosting services have this) you may need to purge it manually, after you purge FVM to see the results you expect. The automatic purge is active for the following plugins and hosting: W3 Total Cache, WP Supercache, WP Rocket, Wp Fastest Cache, Cachify, Comet Cache, Zen Cache, LiteSpeed Cache, Cache Enabler, SG Optimizer, Breeze (Cloudways), Godaddy Managed WordPress Hosting and WP Engine


= Do you recommend a specific Cache Plugin? =
Currently we recommend the "Cache Enabler" plugin, for it's simplicity, compatibility with most systems and performance. Alternatively, W3 Total Cache is a great choice as well.


= Is it resource intensive, or will it use too much CPU on my shared hosting plan? =
Unless you are not excluding dynamic CSS files that change the url in every pageload, its not heavy at all. On the first run, each single file is minified into an intermediate cache. When a new group of CSS/JS files is found on a new page, it reuses those files and merges them into a new static cache file. All pages that request the same group of CSS or JS files will also make use of that file, thus regeneration only happens once. In addition, gz and br files will be pre-compressed (if supported).


= How do I use the pre-compressed files with gzip_static or brotli_static on Nginx? =
When we merge and minify the css and js files, we also create a `.gz` file to be used with `gzip_static` on Nginx. You need to enable this feature on your Nginx configuration file if you want to make use of it. Likewise, if you have Nginx compiled with brotli and have enabled the php-ext-brotli extension for PHP, you can enable the brotli_static option and FVM will also generate .br files for you :)


= Is it compatible with multisites? =
Yes, it generates a new cache file for every different set of JS and CSS requirements it finds, but you must enable and configure FVM settings for each site in your network separatly (no global settings for all sites).


= Is it compatible with AdSense and other ad networks? =
If you are just inserting ads on your pages, yes. If you are using a custom script to inject those ads, please double check if it works. 


= After installing, why are some images and sliders not working? =

a) You cannot do double minification, so make sure you have disabled any features on your theme or other plugins that perform minification of css, html and js files.

b) If you enabled the option to defer JS or CSS, please note that some themes and plugins need jQuery and other libraries to be render blocking, so they are not "undefined" during page load.

c) The plugin relies on PHP Minify to minify JavaScript and css files, however it is not a perfect library and there are plugins that are already minified and do not output a "min.js" or "min.css" filename (and end up being minified again). Try to disable minification on JS and CSS files and purge the cache, then either dequeue it and enqueue an alternative file or add it to the ignore list.

d) Sometimes a plugin conflicts with another when merged (look at google chrome console log for hints). Try to disable CSS processing first and see if it works. Disable JS processing second and see if it works. Try to disable HTML minification last and see if it works. If one of those work, you know there is a conflict when merging/minifying.

e) If you have a conflict, try to add each CSS and each JS file to the ignore list one by one, until you find the one that causes the conflict. If you have no idea of which files to add, check the log file on the "status page" for a list of files being merged into each generated file.

f) If you coded some inline JS code that depends on some JS file being loaded before it's execution, try to save that code into an external file and enqueue it as a dependency. It will be merged together, thus no longer being "undefined".


= Why are some of the CSS and JS files not being merged? =
The plugin only processes JS and CSS files enqueued using the official WordPress api method - https://developer.wordpress.org/themes/basics/including-css-javascript/ -as well as files from the same domain (unless specified on the settings). 


= Can I merge files from other domains? =
Yes and no. You can for example, merge js files such as jQuery if they are loading from a CDN and it will work, because it doesn't matter where those files are being served from. However, stuff like Facebook and other social media widgets, as well as tracking codes, widgets and so on, cannot usually be merged and cached locally as they may load something different on every pageload, or anytime they change something. Ads and widgets make your site slow, so make sure you only use the minimum necessary plugins and widgets.


= How to undo all changes done by the plugin? =
The plugin itself does not do any "changes" to your site and all original files are untouched. It intercepts the enqueued CSS and JS files just before printing your HTML, copies them and enqueues the newly optimized cached version of those files to the frontend. As with any plugin... simply disable or uninstall the plugin, purge all caches you may have in use (plugins, server, cloudflare, etc.) and the site will go back to what it was before installing it. The plugin does not delete anything from the database or modify any of your files. 


= I have disabled or deleted the plugin but my design is still broken! =
Some "cheap" (or sometimes expensive) "optimized" hosting providers, implement a (misconfigured) aggressive cache on their servers that caches PHP code execution and PHP files. I've seen people completely deleting all WordPress files from their host via SFTP/FTP and the website kept working fine for hours. Furthermore, very often they rate limit your cache purge requests... so if you delete FVM and are still seeing references to FVM files on the "view-source:https://example.com" please be patient and contact your web hosting to purge all caches. Providers known to have this issue are some plans on hostgator and iPage (please report others if you find them).


= Why is my Visual Composer or Page Editor not working? =
Some plugins and themes need to edit the layout and styles on the frontend. If you have trouble with page editors, please enable the "Fix Page Editors" option on FVM and purge your caches. Note: You will only see the FVM minification working when you're logged out or using another browser after this setting. 


= What are the recommended cloudflare settings for this plugin? =
On the "Speed" tab, deselect the Auto Minify for JavaScript, CSS and HTML as well as the Rocket Loader option as there is no benefit of using them with our plugin (we already minify things). Those options can also break the design due to double minification or the fact that the Rocket Loader is still experimental (you can read about that on the "Help" link under each selected option on cloudflare).


= How can I load CSS async? =
You are probably a developer if you are trying this. The answer is: make sure FVM is only generating 1 CSS file, because "async" means multiple files will load out of order (however CSS needs order most of the times). If FVM is generating more than 1 CSS file per mediatype, try to manually dequeue some of the CSS files that are breaking the series on FVM (such as external enqueued files), or add their domain to the settings to be merged together. Please note... this is an advanced option for skilled developers. Do not try to fiddle with these settings if you are not one, as it will almost certainly break your site layout and functionality.


= I have a complaint or I need support right now. =
Before getting angry because you have no answer within a few hours (even with paid plugins, sometimes it takes weeks...), please be informed about how wordpress.org and the plugins directory work. The plugins directory is an open source, free service where developers and programmers contribute (on their free time) with plugins that can be downloaded and installed by anyone "at their own risk" and are all released under the GPL license. While all plugins have to be approved and reviewed by the WordPress team before being published (for dangerous code, spam, etc.) this does not change the license or add any warranty. All plugins are provided as they are, free of charge and should be used at your own risk (so you should make backups before installing any plugin or performing updates) and it is your sole responsibility if you break your site after installing a plugin from the plugins directory. For a full version of the license, please read: https://wordpress.org/about/gpl/

= Why haven't you replied to my topic on the support forum yet? =
Support is provided by plugin authors on their free time and without warranty of a reply, so you can experience different levels of support level from plugin to plugin. As the author of this plugin I strive to provide support on a daily basis and I can take a look and help you with some issues related with my plugin, but please note that this is done out of my goodwill and in no way I have any legal or moral obligation for doing this. Sometimes I am extremely busy and may take a few days to reply, but I will always reply. 

= But I really need fast support right now, is there any other way? =
I am also available for hiring if you need custom-made speed optimizations. After you have installed the plugin, check the "Help" tab for contact information, or check my profile links here on WordPress. 


= Where can I report bugs? =
You can get support on the official WordPress plugin page at https://wordpress.org/support/plugin/fast-velocity-minify 
Alternatively, you can reach me via info (at) fastvelocity.com for security or other vulnerabilities.

= How can I donate to the plugin author? =
If you would like to donate any amount to the plugin author (thank you in advance), you can do it via PayPal at https://goo.gl/vpLrSV


== Upgrade Notice ==

= 2.5.9 =
Minor bug fixes

= 3.0 =
Please backup your site before updating. Version 3.0 will have a major code rewrite to improve JS and CSS merging. 


== Changelog ==

= 2.6.0 [2019.03.02] =
* fixed cache purging with the hypercache plugin
* fixed a bug with inline scripts and styles not showing up if there is no url for the enqueued handle
* changed the cache directory from the wp-content/uploads to wp-content/cache
* improved compatibility with page cache plugins and servers (purging FVM without purging the page cache should be fine now)
* added a daily cronjob, to delete public invalid cache files that are older than 3 months (your page cache should expire before this)

= 2.5.9 [2019.02.19] =
* fixed some PHP notices, when wordpress fails to download a missing js/css file

= 2.5.8 [2019.02.06] =
* minor bug fix with the defer for pagespeed option

= 2.5.7 [2019.02.04] =
* reverted back the css merging method to version 2.5.2 due to some compatibility issues

= 2.5.6 [2019.01.18] =
* fixed some php notices
* disabled FVM on amp pages
* expected to be the last update on the 2.x branch, before 3.0 major release

= 2.5.5 [2019.01.12] =
* fixed the dynamic urls being forced as http://
* fixed the inlined styles being stripped when the inline all CSS option is enabled
* added option to disable merging of inlined css code (for when you have dynamic inline css code)
* other minor bug fixes

= 2.5.4 [2019.01.11] =
* css merging bug fixes

= 2.5.3 [2019.01.11] =
* fixed inlined css code being minified, even when minification is off
* compatibility and performance improvements for the CSS merging and inlining functionality

= 2.5.2 [2019.01.11] =
* removed CURL as a fallback method (CURL is already a fallback on the WP HTTP API) as per WP recommendation
* fixed a query monitor notice about mkdir
* removed some legacy code + some minor performance improvements on the code
* improvement for the defer for pagespeed option and ignore list
* improvement for the loadCSS functionality
* improvements for merging the google fonts option

= 2.5.1 [2018.12.17] =
* minor bug fix related to the font awesome option
* added cache purging support to Breeze (Cloudways)

= 2.5.0 [2018.12.13] =
* bug fixes with the google fonts merging option
* better default settings

= 2.4.9 [2018.12.13] =
* improved the google fonts merging to only allow existing google fonts (no more google fonts 404 errors due to the merging of the fonts)
* downgraded PHP Minify to version 1.3.60 due to someone reporting a server error with the new version

= 2.4.8 [2018.12.07] =
* changed the file permissions for the generated cache directory and files, to match the uploads directory
* added some extra checks for when PHP is running in safe mode

= 2.4.7 [2018.12.06] =
* added better default options after new install
* added option to preserve FVM settings on deactivation
* added an HTML comment to the frontend with the path and notification, when FVM cannot generate the cache files (ie: wrong file permissions)
* added a notification on wp-admin when FVM cannot generate the cache files due to wrong permissions
* added an option to force "chmod 0777" on FVM cache files, to avoid errors on servers that need more than "chmod 0755"
* improved the google fonts merging option when the enqueue is faulty (ie: incomplete google font urls )
* fixed the cache purge notifications not showing on wp-admin

= 2.4.6 [2018.12.05] =
* fixed a bug that could cause an error 500 if an enqueued CSS or JS file was not found
* added brotli_static support, if you have the php-ext-brotli extension installed - https://github.com/kjdev/php-ext-brotli

= 2.4.5 [2018.12.04] =
* fixed a bug, where it may show a warning during cache purge on wp-admin
* exclude footer FVM generated files from the HTTP header preload option (footer files are not in the critical path)

= 2.4.4 [2018.12.03] =
* added option to inline CSS in the footer, while still preserving the merged file in the header
* improvements for the google fonts merging option
* fixed double notification, when purging caches without a cache plugin

= 2.4.3 [2018.12.03] =
* added font-display, to ensure text remains visible during webfont load for inlined google fonts and font-awesome
* added automatic removal of "source mappings" from JS files during merging or minification
* added font awesome async and exclusion from PSI options, as well as merging and inlining when the url matches "font-awesome" (ie: cdnjs)
* added automatically inline of small CSS code (up to 20KB) for any FVM CSS files in the footer (requests reduction)
* added automatically inline of small CSS code (up to 20KB) for any FVM CSS files in the header, which are not of mediatype "all"
* improved the cache purge button (no more redirect from frontend to backend)
* updated PHP Minify and Path Converter from master
* bug fixes related to "Exclude JS files from PSI" option

= 2.4.2 [2018.11.29] =
* fixed a bug with the "Exclude JS files in the ignore list from PSI" option (it wasn't excluding properly)
* improved functionality with the "Exclude CSS files from PSI" option (now works with both inline and link stylesheets)
* added an option to automatically preload the CSS and JS files generated by FVM (beware that some server caches like Pantheon may push old css and js files even after purging caches on FVM)
* improved JavaScript minification

= 2.4.1 [2018.11.27] =
* better FVM default settings

= 2.4.0 [2018.11.26] =
* bug fixes related to the inline css option
* changed a few options and added better descriptions to the admin options

= 2.3.9 [2018.11.24] =
* there was an error on my end while pushing 2.3.8... this is a version bump

= 2.3.8 [2018.11.24] =
* removed the dynamic protocol in favour of auto detecting HTTP(s)
* fixed a bug where some CSS files were being removed with the latest CSS inline method
* fixed a bug where the wrong file path was being generated for fonts and some static assets (when the plugin or theme, uses relative paths and the Inline CSS option is enabled)

= 2.3.7 [2018.11.24] =
* bug fixes and performance improvements
* changed a few "options" location to other tabs
* changed the "Inline CSS" method to inline each file right separatly, instead of merging it and then inline it (also improves compatibility)
* added option to exclude JS and CSS files from PSI separatly (will load them Async, so make sure to read the instructions for each)
* added a dedicated Critical Path CSS for "is_front_page" (more conditionals on the roadmap)
* renamed some labels to be more explicit regardless of what they do

= 2.3.6 [2018.11.20] =
* added better header preloader and preconnect headers for css and js files
* added support to automatically purge the cache enabler plugin
* added option to reload the cache, while preserving the old static files
* added better default options after first install
* added and reorganized some options
* added a new developers tab
* removed the YUI compressor option (defaults to PHP Minify)
* readme and screenshots update
* tested up to WP 5.0 tag

= 2.3.5 [2018.08.27] =
* added thinkwithgoogle support for the defer for insights option
* added HyperCache support, thanks to @erich_k4wp
* added suport for wp_add_inline_script, thanks to @yuriipavlov
* fixed a bug where some inlined css was missing if not attached to a parent css file
* the ignore list now also supports CSS handle names (no JS yet)
* updated PHP Minify from master on github
* improved performance for gtmetrix tests

= 2.3.4 [2018.06.30] =
* bug fix

= 2.3.3 [2018.06.30] =
* added a check to prevent creating an empty js or css file
* added an option to force the CDN option when using the defer for insights option
* removed the alternative HTML minification method
* minor performance and bug fixes

= 2.3.2 [2018.06.03] =
* added some compatibility fixes when merging and minifying JS files
* added an option to enable an "FVM Purge" button on the admin bar
* moved all large transients (cached css or js code) to temporary disk files to reduce the database load

= 2.3.1 [2018.06.01] =
* bug fixes and performance tweaks for the "fix page editors" option

= 2.3.0 [2018.05.24] =
* added wp cli support for purge cache (usage: wp fvm purge)
* added wp cli support for getting the cache size (usage: wp fvm stats)

= 2.2.9 [2018.05.23] =
* fixed several bugs related to notices, css minification and file paths
* added more pcre.backtrack_limit and pcre.recursion_limit to avoid blank pages on some servers
* added new option to defer the ignore list for pagespeed

= 2.2.8 [2018.01.21] =
* rollback to 2.2.6 + bugfixes

= 2.2.7 [2018.02.19] =
* fixed a bug with the blacklist functionality
* replaced PHP Minify with JSMin as the default JS minification 
* replaced PHP Minify with CSSTidy as the default CSS minification
* replaced PHP Minify with Minify HTML as the default HTML minification
* moved the intermediary cache from transients to disk files

= 2.2.6 [2018.01.06] =
* fixed a bug with html minification on some files that should not be minified
* fixed a bug with the defer for pagespeed insights
* updated the default blacklist (delete all entries and save again, to restore)

= 2.2.5 [2017.12.18] =
* fixed a fatal error reported on the support forum

= 2.2.4 [2017.12.17] =
* added custom cache directory and url support
* cleaned up some old unused code
* updated to the latest PHP Minify version
* added better descriptions and labels for some options
* added auto exclusion for js and css files when defer for pagespeed is enabled

= 2.2.3 [2017.12.16] =
* added robots.txt and ajax requests to the exclusion list
* added some cdn fixes
* added a new Pro tab
* added a global critical path css section
* added an option to dequeue all css files
* added an option to load CSS Async with LoadCSS (experimental)
* added an option to merge external resources together
* added the possibility to manage the default ignore list (reported files that cause conflicts when merged) 
* added the possibility to manage the blacklist (files that cannot be merged with normal files)
* added better descriptions and labels for some options

= 2.2.2 [2017.11.12] =
* fixed the current cdn option box
* fixed some other minor bugs and notices
* added option to remove all enqueued google fonts (so you can use your own CSS @fontfaces manually)
* added font hinting for the "Inline Google Fonts CSS" option, so it looks better on Windows

= 2.2.1 [2017.08.21] =
* added unicode support to the alternative html minification option
* improved some options description

= 2.2.0 [2017.08.13] =
* fixed some debug notices
* fixed the alternative html minification option

= 2.1.9 [2017.08.11] =
* fixed a development bug

= 2.1.8 [2017.08.11] =
* fixed the html minification not working
* added support for the cdn enabler plugin (force http or https method)

= 2.1.7 [2017.07.17] =
* improved html minification speed and response time to the first byte
* fixed a random bug with the html minification library on large html pages (white pages)
* added support for the "Nginx Cache" plugin purge, by Till Krüss

= 2.1.6 [2017.07.17] =
* fixed a php notice in debug mode
* children styles (added with wp_add_inline_style) are now kept in order and merged together in place
* added faqs for possible "visual composer" issues

= 2.1.5 [2017.07.17] =
* css bug fixes and performance improvements
* added support for auto purging on WP Engine

= 2.1.4 [2017.07.14] =
* added compatibility with WP Engine.com and other providers that use a CNAME with their own subdomain

= 2.1.3 [2017.07.11] =
* updated PHP Minify for better compatibility
* added an alternative mode for html minification (because PHP Minify sometimes breaks things)
* css bug fixes and performance improvements

= 2.1.2 [2017.06.27] =
* fixed another error notice when debug mode is on

= 2.1.1 [2017.06.24] =
* fixed an error notice

= 2.1.0 [2017.06.21] =
* some performance improvements

= 2.0.9 [2017.06.01] =
* several bug and compatibility fixes

= 2.0.8 [2017.05.28] =
* fixed a notice alert on php for undefined function

= 2.0.7 [2017.05.28] =
* added support for auto purging of LiteSpeed Cache 
* added support for auto purging on Godaddy Managed WordPress Hosting
* added the ie only blacklist, wich doesn't split merged files anymore, like the ignore list does
* added auto updates for the default ignore list and blacklist from our api once every 24 hours
* added cdn rewrite support for generated css and js files only
* removed url protocol rewrites and set default to dynamic "//" protocols
* updated the faqs

= 2.0.6 [2017.05.22] =
* added a "Troubleshooting" option to fix frontend editors for admin and editor level users
* updated the faqs

= 2.0.5 [2017.05.15] =
* fixed preserving the SVG namespace definition "http://www.w3.org/2000/svg" used on Bootstrap 4
* added some exclusions for Thrive and Visual Composer frontend preview and editors

= 2.0.4 [2017.05.15] =
* improved compatibility with Windows operating systems

= 2.0.3 [2017.05.15] =
* fixed an "undefined" notice

= 2.0.2 [2017.05.14] =
* improved compatibility on JS merging and minification

= 2.0.1 [2017.05.11] =
* fixed missing file that caused some errors on new installs 

= 2.0.0 [2017.05.11] =
* moved the css and js merging base code back to 1.4.3 because it was better for compatibility
* removed the font awesome optimization tweaks because people have multiple versions and requirements (but duplicate css and js files are always removed)
* added all usable improvements and features up to 1.5.2, except for the "Defer CSS" and "Critical Path" features (will consider for the future)
* added info to the FAQ's about our internal blacklist for known CSS or JS files that are always ignored by the plugin 
* changed the way CSS and JS files are fetched and merged to make use of the new improvements that were supposed to be on 1.4.4+
* changed the advanced settings tab back to the settings page for quicker selection of options by the advanced users
* changed the cache purging option to also delete our plugin transients via the API, rather than just let them expire
* changed the "Inline all CSS" option into header and footer separately

= 1.0 [2016.06.19] =
* Initial Release
