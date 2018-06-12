=== MetaSlider ===
Contributors: matchalabs, DavidAnderson, dnutbourne, kbat82
Tags: wordpress slideshow,seo,slideshow,slider,widget,wordpress slider,image slider,flexslider,flex slider,nivoslider,nivo slider,responsive,responsive slides,coinslider,coin slider,slideshow,carousel,responsive slider,vertical slides
Donate link: https://david.dw-perspective.org.uk/donate
Requires at least: 3.5
Stable tag: 3.7.2
Tested up to: 4.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easy to use WordPress slider plugin. Create SEO-optimized responsive slideshows with Nivo Slider, Flex Slider, Coin Slider and Responsive Slides.

== Description ==

With MetaSlider, you can create your own unique, SEO-optimized slideshow in a matter of seconds!

With WordPress’ most popular slider plugin, enhancing your blog or website couldn’t be easier: simply select images from your WordPress Media Library, drag and drop them into place, and then set the slide captions, links and SEO fields all from one page.

Choose one of 4 different slideshow types, and use our provided short-code or template to embed the slideshows.

**Included slideshow types:**

* **Flex Slider 2** - responsive, 2 transition effects, carousel mode
* **Nivo Slider** - responsive, 16 transition effects, 4 themes
* **Responsive Slides** - responsive & incredibly light weight
* **Coin Slider** - 4 transition effects

**Features**

* Simple, easy to use interface - perfect for individual users, developers & clients!
* Create Responsive, SEO-optimized slideshows in seconds
* Unrestricted support for Image slides (supports caption, link, title text, alt text)
* Full width slideshow support
* Drag and drop slide reordering
* Admin preview
* Intelligent image cropping
* Set image crop position
* Built in Widget and Shortcode
* Loads of slideshow configuration options - transition effect, speed etc (per slideshow)
* Fully localized
* WordPress Multi Site compatible
* Compatible with translation plugins (WPML, PolyLang & qTranslate)
* Extensive Developer API (hooks & filters)
* Fast - only the minimum JavaScript/CSS is included on your page
* Free basic support (covering installation issues and theme/plugin conflicts)
* Lightbox support with the [MetaSlider Lightbox](https://wordpress.org/plugins/ml-slider-lightbox/) addon

**General:**
* Simple and intuitive interface – perfect for individual users, developers & clients!
* Fast – requires only the minimum JavaScript/CSS on your page
* Creatively responsive
	
**Creative:**
* Unrestricted full-width support for image slides, including captions, links, title texts and alt. texts.
* Includes drag and drop slide reordering, intelligent image cropping, set image crop position. 
* Multiple slideshow configuration options (e.g. for speed, 
* Has Admin Preview

**Other:**
* Includes Admin preview, plus built-in Widget and Short-code
* Fully localized
* WordPress Multi-site compatible, and 
* compatible with translation plugins (WPML, PolyLang & qTranslate)
* Developer Friendly, with extensive hooks & filters

**Support:**
* Includes free basic support (covering installation issues and theme/plugin conflicts).

The <a href="https://www.metaslider.com/upgrade">MetaSlider Pro</a> includes added support for:

* YouTube & Vimeo slides
* HTML slides
* Layer slides with CSS3 animations & HTML5 Video backgrounds
* Dynamic Post Feed/Featured Image Slides (content slider)
* Custom Themes
* Thumbnail Navigation
* Premium Support

Read more and thanks to:

* [Flex Slider](http://flexslider.woothemes.com/)
* [Responsive Slides](http://responsive-slides.viljamis.com/)
* [Coin Slider](http://workshop.rs/projects/coin-slider/)
* [Nivo Slider](http://dev7studios.com/nivo-slider/)

Find out more at https://www.metaslider.com

Follow us on Twitter: [@wpmetaslider](https://twitter.com/wpmetaslider)

== Installation ==

The easy way:

1. Go to the Plugins Menu in WordPress
1. Search for "MetaSlider"
1. Click "Install"

The not so easy way:

1. Upload the `ml-slider` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Manage your slideshows using the 'MetaSlider' menu option

== Frequently Asked Questions ==

https://www.metaslider.com/documentation/

= How do I include a slideshow in the header of my site? =

Video Guide:

https://www.youtube.com/watch?v=gSsWgd66Jjk

Text Guide:

You will need to paste the "Template Include" code into your theme (you can find this in the 'Usage' section underneath the slideshow settings)

* Go to Appearance > Editor in WordPress
* Edit a file called 'header.php' (or similar)
* Find the correct place to add it (for example above or below the logo)
* Paste in the code and save.

= I only want to show the slideshow on my homepage, how can I do that? =

Add the 'restrict_to' parameter to the shortcode, eg:

`[metaslider id=XXX restrict_to=home]");`

Theme specific instructions:

https://www.metaslider.com/documentation/theme-integration/

= It's not working - what can I do? =

Check out the troubleshooting page here:

https://www.metaslider.com/documentation/troubleshooting/

= MetaSlider is cropping my images in the wrong place - what can I do? =

See https://www.metaslider.com/documentation/image-cropping/

== Screenshots ==

1. MetaSlider - for live demos see https://www.metaslider.com/examples/
2. Nivo Slider example
3. Coin Slider example
4. Flex Slider example
5. Carousel Example
6. Administration panel - selecting slides

== Changelog ==

= 3.7.2 - 2018/Mar/20 =

* TWEAK: Remove an obsolete admin notice
* SECURITY: Prevent a non-persistent logged-in XSS attack. The attacker must persuade a logged-in admin-level WP user to click on a malicious link specifically targeted to your site; this can result in his chosen JavaScript being run inside your browser on the MetaSlider page. Hence, the risk is low, but you should certainly update.

= 3.7.1 - 2018/Mar/13 =

* FIX: Updates FlexSlider to remove flash on page load. 

= 3.7.0 - 2018/Feb/26 =

* FEATURE: Allows users to inherit default captions and other data from the image. 
* FIX: Adds capability filter to pages.
* TWEAK: Updates to the correct support link.

= 3.6.8 - 2018/Jan/26 =

* FIX: Updates support links to their correct locations
* FIX: Updates compatibility for various themes (including Genesis)

= 3.6.7 - 2017/Dec/15 =

* FEATURE: Adds back in some instructions on how to display the slideshow
* TWEAK: Removes unnecessary type attribute that was causing valiation errors
* TWEAK: Adds DocBlock checking with CI for PHP and JS files
* FIX: Adds compatibility fixes for older WP versions.

= 3.6.6 - 2017/Nov/23 =

* FIX: Fixes FlexSlider bug when resizing slider.
* FIX: Updates layout on post feed slides.

= 3.6.5 - 2017/Nov/20 =

* FIX: Fixes issues with bottom margin of slideshow, among other minor tweaks.
* TWEAK: Prevents a PHP debug log item that appeared in the short-lived 3.6.4

= 3.6.3 - 2017/Nov/16 =

* FIX: Updates various styles to retain compatibility with previous releases based upon user feedback
* FIX: Removes translation of slider brand names.
* UPGRADE NOTE: If upgrading to MetaSlider 3.6+, users of the MetaSlider Add-On pack should also upgrade that plugin to a current release (2.7.1 or later).

= 3.6.2 - 2017/Nov/15 =

* FIX: Update various styles to retain compatibility with previous releases based upon user feedback
* FIX: Restore compatibility with old WP versions that lack the wp_add_inline_script() function

= 3.6.1 - 2017/Nov/14 =

* FIX: Removes default FlexSlider styling

= 3.6.0 - 2017/Nov/14 =

* FEATURE: Allow a slide to be restored after deletion
* FEATURE: Allow the background image to be changed
* FEATURE: Adds a guided tour
* FIX: Loads inline JS properly
* FIX: FlexSlider touch events respect pause on hover setting
* FIX: Allow for https image URLs
* TRANSLATIONS: Addresses spelling/localization issues
* TWEAK: Accessibility enhancements
* TWEAK: Adds links to activation page
* TWEAK: Updates FlexSlider to latest
* TWEAK: Change the label "Meta Slider" to "MetaSlider"
* TWEAK: Add dashboard notices

= 3.5.1 [01/05/17] =

* Fix: Pre-populate caption and alt text fields for new image slides (based on original media file data)
* Fix: When a media file is deleted from the media library, also remove it from the slideshow
* Fix: Update _wp_attachment_metadata when creating new image sizes

= 3.5 [13/03/17] =

* New slides will now be added as a new post type (ml-slide) (existing slideshows and slides will be unaffected)
* Workaround/Fix: Don't use WP_Image_Editor to load slide images that are missing metadata (invalid images)
* Fix: Load admin JavaScript in footer
* New: Add "metaslider_after_resize_image" action

= 3.4.2 [16/01/17] =

* Workaround/Fix: Don't use WP_Image_Editor to load admin slide thumbnails, use wp_get_attachment_image_src instead. Attempts to fix white screen issues affecting some users. Related: https://core.trac.wordpress.org/ticket/36534
* Fix: Load admin JavaScript in footer

= 3.4 [04/01/17] =

This is the first in a series of small updates which will eventually allow us to remove restrictions in the plugin which prevent us from implementing certain functionality, including:

- Changing a slide image
- Using unique captions when the same slide has been added to more than one slideshow
- Adding the same slide to a slideshow multiple times
- Duplicating slides and sliders
- Drafting slides
- Scheduling slides

We are releasing this update in a number of small stages due to the number of users MetaSlider has. We are being overly cautious to ensure it's a smooth transition. This update does not make any major changes to the current plugin functionality, but it does put in place the "scaffolding" code which we will rely on to implement further updates.

= 3.3.7 [06/05/16] =

* Fix: "Maximum level reached" error when inserting the shortcode for a slideshow into it's own caption. Thanks to Zhouyuan @ Fortinet for reporting this.

= 3.3.6 [14/12/15] =

* Fix: Save Spinner

= 3.3.5 [22/09/15] =

* Prepare plugin for WordPress.org translation project (rename textdomain from 'metaslider' to 'ml-slider')
* Small styling fix

= 3.3.4.1 [29/07/15] =

* Fix Roots theme CSS conflict

= 3.3.4 [16/07/15] =

* Add HTML5 validation by applying a property="stylesheet" attribute to MetaSlider <link> CSS tags
* Remove unused "Resource Manager" code
* Chinese language pack updated (thanks to mamsds!)

= 3.3.3 [11/06/15] =

* Ukrainian language pack added (thanks to mister_r!)
* Fix: MetaSlider hoplink incorrectly adding parameters to filtered url
* Add "metaslider_attachment_url" filter

= 3.3.2 [16/04/15] =

* Fix: FPD Security issue. Thanks to Ole Aass (@oleaass) for finding and disclosing this issue.

More information:

The fix will prevent some servers (configured with 'display_errors' set to 'on') from disclosing the full path to certain files within MetaSlider.

http://codex.wordpress.org/Security_FAQ#Why_are_there_path_disclosures_when_directly_loading_certain_files.3F

= 3.3.1 [23/03/15] =

* Fix: Remove 'create video playlist' option from Media Library (on MetaSlider page only)
* Fix: Support for Enhanced Media Library plugin
* Fix: Return public slide when DOING_AJAX
* Improvement: Use admin actions to save slideshow settings

= 3.3 [17/02/15] =

* New feature: Smart pad option (for Image Slides & Flex Slider only)
* Portuguese language files added (thanks to mauro.mascarenhas)
* Russian language files updated (thanks to asidoryak)

= 3.2.1 [16/12/14] =

* Change: Change slide image functionality backed out
* Fix: Apply FireFox mobile fix to Flex Slider (github #1110)

= 3.2 [26/11/14] =

* New feature: Change slide image (click top right of slide thumbnail)
* Update: German language files (thanks to Ov3rfly!)

= 3.1.1 [21/10/14] =

* Fix: restrict_to shortcode parameter
* Change: Add metaslider_flex_slider_list_item_attributes filter

= 3.1 [14/10/14] =

* New feature: Ajax delete slide (to stop users from losing changes when deleting a slide)
* New feature: restrict_to shortcode parameter now accepts page IDs
* Update: Change icon
* Fix: Minor admin styling fix
* Fix: Hide share buttons for pro users
* Change: Remove upgrade nags from media library, add Go Pro page (with an option to hide the page)

= 3.0.1 [19/08/14] =

* Fix: Escape admin setting text fields
* Fix: Escape admin tab names (thanks to Dylan Irzi for spotting and reporting this!)
* Change: Allow shortcode parameters to be filtered

= 3.0 [30/07/14] =

**This is not a major update. We're just following the WordPress versioning conventions (3.0 comes after 2.9)**

* New feature: Set crop position for slides (requires WP 3.9+)
* New feature: Disable cropping setting
* Fix: Use get_posts instead of WP_Query to extract slideshows (fix conflicts with plugins using get_post_type in admin_footer hooks)
* Change: Add filter for capability required to use MetaSlider

= 2.9.1 [15/07/14] =

* New feature: Hungarian Language Pack added
* Fix: Escape attributes and JS in slideshow output (credit to jwenerd!)
* Fix: Escape attributes and text fields in admin
* New feature: Admin slide tabs can be modified with filters

= 2.9 [25/06/14] =

* New feature: Japanese Language Pack added
* New feature: Persian Language Pack added
* New feature: Switch between tab and list view
* New feature: Added ms-left and ms-right css classes to align slideshow to left or right
* Improvement: Flex Slider updated to 2.3.0-bleeding (fix initial image fade)
* Fix: reference to window.parent in media library
* Fix: Thumbnail outline in firefox

= 2.8.1 [28/04/14] =

* Fix: All in One Events Calendar conflict fix (Advanced Settings not toggling)
* Fix: CSS resets to avoid theme conflicts
* Fix: Autoload visibility conflict (http://wordpress.org/support/topic/autoload-visibillity-conflict)
* Fix: Layer Editor in IE11 - text fields not accessible in modal windows
* Fix: FlexSlider IE11 Fade transition

= 2.8 [28/04/14] =

* New feature: Russian Language Pack added
* Fix: Carousel image scaling in FireFox
* Fix: wpautop issue with double ampersand
* New feature: Shortcode parameter added to restrict slideshow to displaying on homepage only (see FAQ)
* Improvement: Save slideshow after reordering slides
* Fix: PHP Warning when no slideshows have been created

= 2.8-beta [16/04/14] =

* Improvement: Preview now uses admin-post action
* Improvement: Classes are now auto loaded to reduce memory footprint
(Thanks to Viktor Szépe for the above suggestions!)
* Improvement: Slideshow initilization time reduced
* Improvement: HTML5 Compatibility: Alt tags always present on image tag - even if empty.
* Improvement: Flex Slider slideshows should now 'reserve' a space for themselves while they fully load
* Update: Flex Slider updated to v2.2.2
* New feature: Romanian Language Pack added (Thanks to Octav Madalin Stanoaia)
* New feature: Dutch Language Pack added
* New feature: WP Super Cache compatibility - cache is cleared when saving slideshow
* New feature: HTML5 Compatibility (Experimental). Set `define('METASLIDER_ENABLE_RESOURCE_MANAGER', true);` in wp-config.php to move MetaSlider link tags head of the page.

= 2.7.2 [25/03/14] =

* Fix: Only apply carousel margin to slides
* Fix: Enqueue Easing library when carousel mode is enabled, regardless of effect selection
* Fix: Thumbnail margin when theme has #content div

= 2.7.1 [19/03/14] =

* Fix: Remove easing parameter when effect is set to fade
* Fix: Navigation options greyed out in IE
* Fix: qTranslate captions not being processed (typo)

= 2.7 [18/03/14] =

* New feature: Croatian Lang pack added
* New feature: Carousel margin option added
* New feature: Process shortcodes in captions
* Improvement: Tab rename UX
* Improvement: Admin save spinner functionality improved
* Improvement: CSS Resets updated
* Improvement: Use plugins_loaded action to initialize plugin
* Fix: PHP Warnings when one slideshow exists
* Fix: Smart Cropping sometimes not returning smart cropped image
* Fix: Add z-index to MetaSlider, attempted conflict fix for themes with drop down menus.
* Fix: Only include the easing library when transition effect is set to slide
* Fix: White Label Branding plugin compatibility.
* Change: "Responsive" option renamed to "R. Slides". The (old) "Responsive" option refers to the "Responsive Slides" jQuery library, but users were getting confused as the naming suggested it was the only responsive option. Flex Slider & Nivo Slider are also responsive.

= 2.6.3 [23/01/14] =

* Improvement: Various admin screen styling improvements
* Fix: Add 'ms-' prefix to Advanced settings toggle boxes and Preview button (avoid theme conflicts)
* Fix: RTL fixes
* Improvement: Filters added for complete slideshow output
* Improvement: Filter added for slide image label
* Improvement: 'No Conflict' mode refactored
* Improvement: 'slider' parameter added to flexslider before/start/after etc callbacks
* Change: Renamed in admin menu from "MetaSlider Lite" to "MetaSlider"

= 2.6.2 [02/01/14] =

* Fix: Vantage background image tiling

= 2.6.1 [31/12/13] =

* Fix: Advanced settings arrow toggle
* Fix: All in one SEO / Page builder / MetaSlider conflict
* Fix: NextGen "Insert Gallery" conflict
* New feature: Norwegian language pack added

= 2.6 [19/12/13] =

* Fix: Typo in metaslider_responsive_slide_image_attributes filter
* Fix: Caption not working in Nivo Slider
* Fix: Tab styling improved
* Fix: New window styling improved in WP3.7 and below

More info/Comments: http://www.metaslider.com/coming-soon-meta-slider-2-6-free/

= 2.6-beta [15/12/13] =

* New feature: Interface update for WordPress 3.8 admin redesign
* New feature: 'Stretch' setting for full width slideshows
* New feature: No conflict mode
* New feature: 'Add slider' button for posts and pages
* New feature: SEO options (add title & alt text to slides)
* Change: CSS is now enqueued using wp_enqueue_style (Use a minification plugin or caching plugin to move styles to the <head> if HTML5 validity is required - eg W3 Total Cache)

More info/Comments: http://www.metaslider.com/coming-soon-meta-slider-2-6-free/

= 2.5 [25/11/13] =
* Fix: JetPack Photon conflict
* Improvement: German Language pack added (thanks to gordon34)
* Improvement: Chinese language pack updated (thanks to 断青丝)
* Improvement: MP6 styling fixes

= 2.5-beta2 [14/11/13] =
* Fix: Vantage theme backwards compatibility
* Fix: Flexslider anchor attributes filter

= 2.5-beta1 [12/11/13] =
* Fix: Center align slideshow

= 2.5-beta [12/11/13] =
* New Feature: 'percentwidth' parameter added to shortcode to allow for 100% wide slideshows
* Improvement: Generate resized images through multiple Ajax requests on save (blank screen fix)
* Improvement: IE9 admin styling tidied up
* Improvement: Filters added to add/change attributes in <img> and <a> tags
* Improvement: Security - nonce checking added
* Change: Remove bottom margin from flex slider when navigation is hidden (add a CSS Class of 'add-margin' if you need the margin)
* Fix: Add slides to slideshow in the same order they're selected in the Media Library
* Fix: Symlink path resolution
* Fix: Do not try to resize/open images that are corrupt (missing metadata) (blank screen fix)

= 2.4.2 [17/10/13] =
* Fix: qTranslate caption & URL parsing for image slides

= 2.4.1 [17/10/13] =
* Fix: PHP Warning (reported by & thanks to: fgirardey)

= 2.4 [16/10/13] =
* Fix: FlexSlider styling in twenty twelve theme
* Fix: IE10 - "Caption" placeholder text being saved as actual caption
* Improvement: Settings table tidied up
* Improvement: New slides are resized during addition to the slideshow
* Improvement: Default slideshow size increased to 700x300
* Improvement: Image filename now displayed for each slide (instead of image dimensions)
* Improvement: Replace deprecated 'live()' jQuery call with 'on()'
* Improvement: Polish Language pack added (thanks to gordon34)
* Improvement: Chinese language pack added (thanks to 断青丝)
* Improvement: 'metaslider_resized_image_url' filter added (could be used to disable cropping)
* Change: qTranslate support for slide URLs (see: http://screencast.com/t/FrsrptyhoT)
* Change: PolyLang fix to ensure slides are extracted for all languages (set up a new slideshow for each language)
* Change: WPML fix to ensure slides are extracted for all languages (set up a new slideshow for each language)


= 2.3 [18/09/13] =
* Improvement: Flex Slider upgraded to v2.2
* Improvement: Responsive Slides upgraded to v1.54
* Improvement: 'Create first slideshow' prompt added for new users
* Change: 'scoped' attribute removed from inline CSS tag until browsers catch up with supporting it properly. A new filter has been added: "metaslider_style_attributes" if you wish to add the scoped attribute back in.
* Change: wp_footer check removed due to confusion
* New Feature: 'metaslider_max_tabs' filter added to convert tab list to ordered drop down menu
* Fix: Remove 'Insert Media' tab from 'Add Slide' modal (WP 3.6 only)
* New Feature: Filters added to allow modification of image slide HTML
* Improvement: Settings area tidied up
* Improvement: Image URL Field less restrictive
* Improvement: HTML Output tidied up

= 2.2.2 [21/08/13] =
* Improvement: System check added with option to dismiss messages. Checks made for: role scoper plugin, wp_footer, wordpress version & GD/ImageMagick.

= 2.2.1 [08/08/13] =
* Fix: Responsive slides styling in FireFox (reported by and thanks to: dznr418)
* Fix: Flex Slider carousel causing browser to crash in some circumstances

= 2.2 [01/08/13] =
* Fix: Paragraph tags being added to output using Nivo Slider

= 2.1.6 [22/07/2013] =
* Fix: Use the original image file if the slideshow size is the same size as the image file
* Fix: Conflict with Advanced Post Types Order plugin
* Fix: Colorbox conflict when using resizable elements in lightbox
* Improvement: Refresh slides after clicking 'save'
* Improvement: Ensure taxonomy category exists before tagging slide to slideshow
* Fix: Only submit form when submit button is clicked (not all buttons)
* Fix: Coin slider caption width in FireFox
* Improvement: Added hook to adjust carousel image margin

= 2.1.5 [24/05/13] =
* Fix: HTML 5 Validation

= 2.1.4 [21/05/13] =
* Fix: Widget markup invalid (reported by and thanks to: CarlosCanvas)

= 2.1.3 [21/05/13] =
* Fix: User Access Manager Plugin incompatibility issues (reported by and thanks to: eltipografico)

= 2.1.2 [21/05/13] =
* Fix: Nivo Slider theme select dropdown (reported by and thanks to: macks)
* Fix: HTML5 Validation fix for inline styles
* Improvement: Title field added to widget (suggested by and thanks to: pa_esp)
* New feature: Spanish language pack (thanks to eltipografico)

= 2.1.1 [13/05/13] =
* Fix: PHP version compatibility

= 2.1 [12/05/13] =
* New feature: Widget added
* New feature: System check added (checks for required image libraries and WordPress version)
* Fix: Multiple CSS fixes added for popular themes
* Fix: Flex slider shows first slide when JS is disabled
* Improvement: Display warning message when unchecking Print JS and Print CSS options
* Improvement: Coinslider navigation centered

= 2.0.2 [02/05/13] =
* Fix: PHP Error when using slides the same size as the slideshow

= 2.0.1 [28/04/13] =
* New feature: French language pack (thanks to: fb-graphiklab)
* Fix: Use transparent background on default flexslider theme
* Fix: Set direction to LTR for flexslider viewport (fix for RTL languages)
* Fix: Nivoslider HTML Captions
* Fix: Responsive slides navigation positioning

= 2.0 [21/04/13] =
* Fix: Responsive slides navigation styling
* Fix: Update slide order on save
* Fix: Smart crop edge cases
* Fix: Flexslider navigation overflow

= 2.0-betaX [17/04/13] =
* Improvement: Error messages exposed in admin is MetaSlider cannot load the slides
* Improvement: Load default settings if original settings are corrupt/incomplete
* Fix: Smart Crop ratio
* Fix: UTF-8 characters in captions (reported by and thanks to: javitopo)
* Fix: JetPack Photo not loading images (reported by and thanks to: Jason)
* Fix: Double slash on jQuery easing path
* Fix: Paragraph tags outputted in JavaScript (reported by and thanks to: CrimsonRaddish)

= 2.0-beta =
* New feature: Preview slideshows in admin control panel
* New feature: 'Easing' options added to flex slider
* New feature: 'Carousel mode' option added for flex slider
* New feature: 'Auto play' option added
* New feature: 'Smart Crop' setting ensures your slideshow size remains consitent regardless of image dimensions
* New feature: 'Center align slideshow' option added for all sliders
* New feature: Coin Slider upgraded to latest version, new options now exposed in MetaSlider
* New feature: Captions now supported by responsive slides
* Improvement: Responsive AJAX powered administration screen
* Improvement: Code refactored
* Improvement: Flex Slider captions now sit over the slide
* Fix: Nivo slider invalid markup (reported by and thanks to: nellyshark)
* Fix: JS && encoding error (reported by and thanks to: neefje)

= 1.3 [28/02/13] =
* Renamed to MetaSlider (previously ML Slider)
* Improvement: Admin styling cleaned up
* Improvement: Code refactored
* Improvement: Plugin localized
* Improvement: Template include PHP code now displayed on slider edit page
* Improvement: jQuery tablednd replaced with jQuery sortable for reordering slides
* New feature: Open URL in new window option added
* Improvement: max-width css rule added to slider wrapper
* Fix: UTF-8 support in captions (reported by and thanks to: petergluk)
* Fix: JS && encoding error (reported by and thanks to: neefje)
* Fix: Editors now have permission to use MetaSlider (reported by and thanks to: rritsud)

= 1.2.1 [20/02/13] =
* Fix: Number of slides per slideshow limited to WordPress 'blog pages show at most' setting (reported by and thanks to: Kenny)
* Fix: Add warning when BMP file is added to slider (reported by and thanks to: MadBong)
* Fix: Allow images smaller than default thumbnail size to be added to slider (reported by and thanks to: MadBong)

= 1.2 [19/02/13] =
* Improvement: Code refactored
* Fix: Unable to assign the same image to more than one slider
* Fix: JavaScript error when jQuery is loaded in page footer
* Improvement: Warning notice when the slider has unsaved changes
* Fix: Captions not being escaped (reported by and thanks to: papabeers)
* Improvement: Add multiple files to slider from Media Browser

= 1.1 [18/02/13] =
* Improvement: Code refactored
* Fix: hitting [enter] brings up Media Library
* Improvement: Settings for new sliders now based on the last edited slider
* Improvement: More screenshots added

= 1.0.1 [17/02/13] =
* Fix: min version incorrect (should be 3.5)

= 1.0 [15/02/13] =
* Initial version

== Upgrade Notice ==
* 3.7.2 : Fix non-persistent admin XSS attack (requiring clicking on a targeted, crafted link specific to your site leading to one-time execution of his chosen JavaScript in your browser - so, low risk but you should certainly update)
