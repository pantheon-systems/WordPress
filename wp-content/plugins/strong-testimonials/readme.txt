=== Strong Testimonials ===
Contributors: cdillon27
Tags: testimonials, testimonial slider, testimonial form, reviews, star ratings
Requires at least: 3.7
Tested up to: 4.9.4
Stable tag: 2.30
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple yet powerful. Very customizable. Developer-friendly. Free support.

== Description ==

A flexible testimonials plugin that works right out of the box for beginners with advanced features for pros, backed by strong support.

**[Go Demo](https://strongdemos.com/strong-testimonials/)** | **[Documentation](https://strongplugins.com/documents/)** | **[Add-ons](https://strongplugins.com/plugins/category/strong-testimonials/)**

### Is this the right plugin for you?

If you are a small business with up to several hundred testimonials or reviews, maybe using categories for different products or services, that needs flexible display options and a customizable form for accepting new testimonials, this plugin will work in just a few steps.

> *This plugin only provides basic style options for background color and font color.*
>
> Some templates have light & dark versions and other options. Everything else will be inherited from your theme. If you want to customize things like fonts, margins and borders, you will need custom CSS.
>
> I will help with major conflicts and minor tweaks. Otherwise, consider learning enough CSS to be dangerous ;) or hiring a developer for a couple hours.

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
* Developer-friendly

### Testimonial Submission Form

Customize the form by adding or removing fields and changing properties like the order, label, and placeholder.

Anti-spam measures include honeypots and Captcha via these plugins:

* [Google Captcha (reCAPTCHA) by BestWebSoft](https://wordpress.org/plugins/google-captcha/) *recommended*
* [Captcha Pro](https://bestwebsoft.com/products/wordpress/plugins/captcha/)
* [Really Simple Captcha](https://wordpress.org/plugins/really-simple-captcha/)

Send custom notification emails to multiple admins.

Submit the form via Ajax to use with plugins like [Popup Maker](https://wordpress.org/plugins/popup-maker/).

**Free Add-on:** Add a country selector to your form with the
[Strong Testimonials Country Selector](https://wordpress.org/plugins/strong-testimonials-country-selector/) plugin.

### Displaying Testimonials

Everything happens in a **view**. Instead of learning multiple shortcodes with dozens of options, a view contains all the options in a simple, intuitive editor that no other testimonial plugin has.

Display the view using a single shortcode or the widget.

Create unlimited views. For example, one view for a form, another for a static grid, another for a slideshow, and so on.

Strong Testimonials offers a variety of templates that work well in most themes with maybe a few tweaks.

For ultimate control and seamless integration, copy any template to your theme and customize it.

You can also use the template function to display a view in a theme template file:

`<?php if ( function_exists( 'strong_testimonials_view' ) ) { strong_testimonials_view( $id ); } ?>`

### Pro Add-ons

#### Review Markup

Testimonials are essentially five-star reviews. Adding review markup will improve search results and encourage search engines to display rich snippets (the stars). [Learn more](https://strongplugins.com/plugins/strong-testimonials-review-markup/?utm_source=wordpressorg&utm_medium=readme)

#### Multiple Forms

Create unlimited forms, each with their own custom fields, to tailor testimonials for different products, services and markets. [Learn more](https://strongplugins.com/plugins/strong-testimonials-multiple-forms/?utm_source=wordpressorg&utm_medium=readme)

#### Properties

Want to rebrand "testimonials" as "reviews", "customer stories" or something else? Control every aspect front and back. [Learn more](https://strongplugins.com/plugins/strong-testimonials-properties/?utm_source=wordpressorg&utm_medium=readme)

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


= 2.29.1 - Jan 8, 2018 =
* Only to trigger update because the SVN version was incomplete.

= 2.29 - Jan 8, 2018 =
* Add integration with Google Captcha by BestWebSoft.
* Remove integration with Captcha plugin.
* Remove integration with Advanced noCaptcha reCaptcha plugin.
* Fix CSS conflict with Cherry Slider.
* Fix bug when adding the category field to the notification email.
* Fix read-more filter usage for WPML and Polylang.
* Add filter: `wpmtst_read_more_page_output`.
* Add support for FooBox Image Lightbox.
* Improved the notification email admin UI.
* Handle form submission on custom action.
* Add dismissible persistent admin notice capability.
* Improve add-on configuration check.
* Minor refactoring for improved performance.

See changelog.txt for previous versions.

== Upgrade Notice ==

= 2.30 =
Better template options.
