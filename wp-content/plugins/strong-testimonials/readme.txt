=== Strong Testimonials ===
Contributors: cdillon27
Tags: testimonials, testimonial slider, testimonial form, reviews, star ratings
Requires at least: 3.7
Tested up to: 4.9.5
Stable tag: 2.30.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple yet powerful. Very customizable. Developer-friendly. Strong support.

== Description ==

In just a few steps, you will be collecting and publishing your testimonials or reviews. Beginners and pros alike will appreciate the wealth of flexible features refined over 4 years from user feedback and requests. Keep moving forward with quick and thorough support to help you with configuration and customization.

**[See the demos](https://strongdemos.com/strong-testimonials/)** | **[Read the documentation](https://strongplugins.com/documents/)** | **[Shop for add-ons](https://strongplugins.com/plugins/category/strong-testimonials/)**

### Primary Features

* No complicated shortcodes
* A front-end form
* Custom fields
* Star ratings
* Slider with several navigation options
* Grids and Masonry
* Ready for translation with [WPML](https://wpml.org/), [Polylang](https://wordpress.org/plugins/polylang/), and [WPGlobus](https://wordpress.org/plugins/wpglobus/)

### More Features

* Sort by oldest, newest, random, or menu order (drag-and-drop)
* Categories
* Excerpts and "Read more" links
* Featured Images (thumbnails) and Gravatars
* Pagination
* Embeds (YouTube, Twitter, Instagram, Facebook)
* Custom capabilities
* Developer-friendly (actions, filters, templates)

### Style

> This plugin provides a few designs with only basic style options for background color and font color. Everything else will be inherited from your theme.
>
> Some templates have light & dark versions and other options. If you want to customize things like fonts, margins and borders, you will need custom CSS.
>
> I will help with theme conflicts and a few tweaks. Otherwise, consider learning enough CSS to be dangerous or hiring a developer for a couple hours.

### Testimonial Submission Form

This plugin provides one form with custom fields. Customize the form by adding or removing fields and changing properties like the order, label, and placeholder.

Anti-spam measures include honeypots and Captcha via these plugins:

* [Google Captcha (reCAPTCHA) by BestWebSoft](https://wordpress.org/plugins/google-captcha/) *recommended*
* [Captcha Pro](https://bestwebsoft.com/products/wordpress/plugins/captcha/)
* [Really Simple Captcha](https://wordpress.org/plugins/really-simple-captcha/)

Send custom notification emails to multiple admins.

Submit the form via Ajax for use with plugins like [Popup Maker](https://wordpress.org/plugins/popup-maker/).

#### Free Add-on

Use the [Country Selector](https://wordpress.org/plugins/strong-testimonials-country-selector/) plugin to add a country selector to your form. [See the demo](https://strongdemos.com/strong-testimonials/form-examples/with-country-selector/).

### Displaying Testimonials

**Everything happens in a View**. Instead of learning multiple shortcodes with dozens of options, a View contains all the options in a simple, intuitive editor that no other testimonial plugin has.

Create unlimited views. For example, one view for a form, another for a static grid, another for a slideshow, and so on.

Display a view using a shortcode or the widget.

A variety of templates are included that work well in most themes.

For ultimate control and seamless integration, copy any template to your theme and customize it.

Use the template function to display a view in your template file:

`<?php if ( function_exists( 'strong_testimonials_view' ) ) {
    strong_testimonials_view( $id );
} ?>`

### Pro Add-ons

#### Assignment

Assign testimonials to any object (posts, pages, media or custom content types) with features designed to simplify your workflow. Works well with portfolio, directory and service business themes. [Learn more](https://strongplugins.com/plugins/strong-testimonials-assignment/?utm_source=wordpressorg&utm_medium=readme)

#### Review Markup

Testimonials are essentially five-star reviews. Adding review markup will improve search results and encourage search engines to display rich snippets (the stars). [Learn more](https://strongplugins.com/plugins/strong-testimonials-review-markup/?utm_source=wordpressorg&utm_medium=readme)

#### Multiple Forms

Create unlimited forms, each with their own custom fields, to tailor testimonials for different products, services and markets. [Learn more](https://strongplugins.com/plugins/strong-testimonials-multiple-forms/?utm_source=wordpressorg&utm_medium=readme)

#### Properties

Want to rebrand "testimonials" as "reviews", "customer stories" or something else? Want to change the permalink structure? Control every aspect front and back. [Learn more](https://strongplugins.com/plugins/strong-testimonials-properties/?utm_source=wordpressorg&utm_medium=readme)

### Documentation

* [Getting started](https://strongplugins.com/document/strong-testimonials/getting-started/?utm_source=wordpressorg&utm_medium=readme)
* [Star ratings](https://strongplugins.com/document/strong-testimonials/star-ratings/?utm_source=wordpressorg&utm_medium=readme)
* [Customizing the form](https://strongplugins.com/document/strong-testimonials/complete-example-customizing-form/?utm_source=wordpressorg&utm_medium=readme)
* and [more&hellip;](https://strongplugins.com/documents/?utm_source=wordpressorg&utm_medium=readme)

### Try these plugins too

* [FooBox Image Lightbox](https://wordpress.org/plugins/foobox-image-lightbox/) to view thumbnails as full-size images.
* [Simple CSS](https://wordpress.org/plugins/simple-css/) works great for quick CSS tweaks.
* [Wider Admin Menu](https://wordpress.org/plugins/wider-admin-menu/) lets your admin menu b r e a t h e.

== Installation ==

1. Go to Plugins > Add New.
1. Search for "strong testimonials".
1. Click "Install Now".

OR

1. Download the zip file.
1. Upload the zip file via Plugins > Add New > Upload.

Activate the plugin. Look for "Testimonials" in the admin menu.

== Frequently Asked Questions ==

= What are the shortcodes? =

[testimonial_view] - To display your testimonials as a list or a slideshow, or to display the form. The first step is to create a **view** which manages all the options in an easy-to-use (some call it fun!) editor.

`[testimonial_view id=1]`

[testimonial_count] - To display the number of testimonials you have. For example:

`Read some of our [testimonial_count] testimonials!`

= Can I add testimonials from YouTube, Twitter, Instagram and Facebook? =

Yes. The plugin supports the [WordPress embed](https://codex.wordpress.org/Embeds) feature for inserting testimonials from [these sources](https://codex.wordpress.org/Embeds#Does_This_Work_With_Any_URL.3F).

= Can I change the fields on the form? =

Yes. There is a custom fields editor to add or remove fields, change field details, and drag-and-drop to reorder them.

= After the form has been submitted, can I redirect them to another page or display a custom message? =

Yes and yes.

= Can I set the status of the newly submitted testimonial? =

Yes, either pending or published.

= Can I reorder my testimonials by drag and drop? =

Yes.

= Can I change the fields that appear below the testimonial? =

Yes. In views, change these custom fields in a few clicks.

= Can I display a large version of the featured image in a popup? =

Yes. This requires a lightbox so if your theme does not include one, you will need a lightbox plugin.

= Will it automatically use my existing testimonials? =

No. If you already have testimonials in another plugin or theme, you will have to re-enter them. Why? Because every theme and plugin stores data differently.

= Can I import my existing testimonials? =

It depends. The plugin does not provide an import tool because every situation is different. With some technical skills, you may be able to successfully export your existing testimonials to a CSV file and import them into Strong Testimonials. Contact me if you want help with that. Otherwise, it may be simpler and easier to migrate them manually.

= Is it true that including a link to my site in my support requests really helps you troubleshoot problems? =

Undeniably, yes.

This [screenshot](http://www.screencast.com/t/TPMRWM0yug) shows where I immediately start looking for clues before asking for more information and potentially waiting hours or days for a response (it happens).

I can determine what theme you're using, what plugins are active, whether you're using any caching/minification/optimization (do you need to clear your cache?), if there are any JavaScript errors in your theme or another plugin (more common than you may think), and somewhat how the testimonial view is configured.

If you prefer not to post your URL publicly, start a private support ticket at [support.strongplugins.com](https://support.strongplugins.com).

== Screenshots ==

1. Slideshow
2. Default template
3. Default form
4. Admin list table
5. General settings
6. Form settings
7. Fields editor
8. View editor

== Changelog ==

= 2.30.8 - April 26, 2018 =
* Fix incorrect textdomains.
* Fix bug in form validation translation files.
* Remove obsolete German translation.
* Update translation files.
* Refactor the submit buttons on settings pages.

= 2.30.7 - April 23, 2018 =
* Fix bug in front-end controller script.

= 2.30.6 - April 17, 2018 =
* Improve embeds in Masonry layout.
* Fix `[testimonial_count category]` shortcode in Properties add-on.
* Update the EDD license updater class.
* Improve notification email admin UI.
* Improve notification email message when custom fields are blank.

= 2.30.5 - April 9, 2018 =
* Fix bug when email field is not required.
* Fix display of templates in view editor when theme/add-on templates are present.
* Fix inconsistent use of filter on default view settings.
* Improve compatibility with themes and thumbnail column in admin list.
* Improve compatibility with installation scripts (table creation).
* Improve check for missing add-on license.
* Add front-end "Nothing found" message for administrators.
* Add data attribute 'count' for found_posts to view container for troubleshooting.
* Add filter on `[testimonial_count]` shortcode defaults.
* Add ability to capture notification email on localhost.
* Revive a logger class.
* Minor admin UI tweaks.

= 2.30.4 - Mar 20, 2018 =
* Fix bug in slider in Firefox 59.

= 2.30.3 - Mar 16, 2018 =
* Improve slider script compatibility (event propagation).

= 2.30.2 - Mar 11, 2018 =
* Fix backwards-compatibility for WordPress versions 4.5 and older.

= 2.30.1 - Mar 6, 2018 =
* Fix minor bug in PHP7 compatibility.
* Improve real-time validation in fields editor.
* Minor CSS fixes.

= 2.30.0 - Feb 10, 2018 =
* Add option for font color in view editor.
* Add option for a custom CSS class on the image link for lightboxes.
* Add shortcode attributes for post_ids, category, order, and count.
* Use `is_email()` to validate email addresses.
* Use `number_format_i18n()` in testimonial_count shortcode.
* Refactor template groups into base templates with options.
* Add option to disable touch swipe navigation in slideshows.
* Simplify slideshow CSS.
* Fix bug in slider script in Chrome.

See changelog.txt for previous versions.

== Upgrade Notice ==

= 2.30.8 =
Better template options. Improved compatibility. Minor bug fixes.
