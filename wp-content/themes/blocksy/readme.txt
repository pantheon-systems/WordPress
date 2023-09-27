=== Blocksy ===
Contributors: creativethemeshq
Website: https://creativethemes.com
Email: info@creativethemes.com
Tags: blog, e-commerce, wide-blocks, block-styles, grid-layout, one-column, two-columns, three-columns, four-columns, right-sidebar, left-sidebar, translation-ready, custom-colors, custom-logo, custom-menu, featured-images, footer-widgets, full-width-template, theme-options, threaded-comments
Requires at least: 5.2
Requires PHP: 5.7
Tested up to: 5.2
Stable tag: trunk
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Blocksy is a blazing fast and lightweight WordPress theme built with the latest web technologies. It was built with the Gutenberg editor in mind and has a lot of options that makes it extendable and customizable. You can easily create any type of website, such as business agency, shop, corporate, education, restaurant, blog, portfolio, landing page and so on. It works like a charm with popular WordPress page builders, including Elementor, Beaver Builder, Visual Composer and Brizy. Since it is responsive and adaptive, translation ready, SEO optimized and has WooCommerce built-in, you will experience an easy build and even an increase in conversions.

== Installation ==

1. In your admin panel, go to Appearance > Themes and click the Add New button.
2. Type in Blocksy in the search form and press the 'Enter' key on your keyboard.
3. Click Activate to use your new theme right away.

== Build Instructions ==

This theme contains some JavaScript files that need a compilation step in
order to be consumable by the browsers. The compilation is done by the
[`build-process`](https://github.com/creative-Themes/build-process) package
(which is just a preset config over WebPack). We do plan to eventually migrate
over to [`wp-scripts`](https://github.com/WordPress/gutenberg/tree/master/packages/scripts)
as the build pipeline.

So, in order to build the theme files, you need to execute these commands:

1. `npm install` or `yarn install`, both work just fine.
2. `npm run build` -- for a production build, or `npm run dev` for developments builds, which include a proper file watcher
3. The final files will be included in `admin/dashboard/static/bundle` and `static/bundle` directories.

The repeated `BlocksyReact`, `BlocksyReactDOM` and `wp.element` got enqueued
that way for two reasons:

1. We started to use React Hooks in WordPress 5.1, and we needed an actual
version of `wp.element` for that. A version of `wp.element` with hooks got
shipped only with WordPress 5.2. We planned on getting rid of our global version as
soon as 5.2 got released, but now I see a problem with backwards
compatibility.
2. We need to use a global version of React and ReactDOM because we have some
components using hooks both in the theme an din the `blocksy-companion` plugin
(https://creativethemes.com/downloads/blocksy-companion.zip). That way we
avoid breaking the rules of hooks.

== Screenshot Licenses ==

Screenshot images are all licensed under CC0 Public Domain
http://streetwill.co/posts/749-az4
http://streetwill.co/posts/788-black-day
http://streetwill.co/posts/205-peaceful
http://streetwill.co/posts/497-coloration
http://streetwill.co/posts/811-grass-flower-on-sunset-background
http://streetwill.co/posts/454-camber-sands-beach-house
http://streetwill.co/posts/350-golden
http://streetwill.co/posts/610-food5
http://streetwill.co/posts/853-aucstp

== Copyright ==
Blocksy WordPress Theme, Copyright 2019 creativethemes.com
Blocksy is distributed under the terms of the GNU GPL

Blocksy bundles the following third-party resources:

@reach/dialog, Copyright (c) 2018-present, MOGHOUSE LLC
Licenses: The MIT License (MIT)
Source: https://github.com/reach/reach-ui/tree/master/packages/dialog

@reach/router, Copyright (c) 2018-present, Ryan Florence
Licenses: The MIT License (MIT)
Source: https://github.com/reach/router

@wordpress/element, WordPress - Web publishing software, Copyright 2011-2019 by the contributors
Licenses: GNU GENERAL PUBLIC LICENSE
Source: https://github.com/WordPress/gutenberg/tree/master/packages/element

@wordpress/date, WordPress - Web publishing software, Copyright 2011-2019 by the contributors
Licenses: GNU GENERAL PUBLIC LICENSE
Source: https://github.com/WordPress/gutenberg/tree/master/packages/date

@wordpress/i18n, WordPress - Web publishing software, Copyright 2011-2019 by the contributors
Licenses: GNU GENERAL PUBLIC LICENSE
Source: https://github.com/WordPress/gutenberg/tree/master/packages/i18n

bezier-easing, Copyright (c) 2014 Gaëtan Renaudeau
Licenses: MIT License
Source: https://github.com/gre/bezier-easing

classnames, Copyright (c) 2018 Jed Watson
Licenses: The MIT License (MIT)
Source: https://github.com/JedWatson/classnames

ct-log, Copyright (c) 2016 Creative Themes
Licenses: The MIT License (MIT)
Source: https://github.com/Creative-Themes/wp-js-log

deep-equal
Licenses: MIT. Derived largely from node's assert module.
Source: https://github.com/substack/node-deep-equal

dom-chef, Copyright (c) Vadim Demedes <vdemedes@gmail.com> (github.com/vadimdemedes)
Licenses: The MIT License (MIT)
Source: https://github.com/vadimdemedes/dom-chef

downshift, Copyright (c) 2017 PayPal
Licenses: The MIT License (MIT)
Source: https://github.com/downshift-js/downshift

file-saver, Copyright © 2016 Eli Grey
Licenses: The MIT License
Source: https://github.com/eligrey/FileSaver.js

infinite-scroll
Licenses: GNU GPL license v3
Source: https://github.com/metafizzy/infinite-scroll#commercial-license

intersection-observer, http://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
Licenses: W3C Software and Document License
Source: https://github.com/w3c/IntersectionObserver

nanoid, Copyright 2017 Andrey Sitnik <andrey@sitnik.ru>
Licenses: The MIT License (MIT)
Source: https://github.com/ai/nanoid

objectFitPolyfill, Made by Constance Chen
Licenses: Released under the MIT license
Source: https://github.com/constancecchen/object-fit-polyfill

react-outside-click-handler, Copyright (c) 2018 Airbnb
Licenses: MIT License
Source: https://github.com/airbnb/react-outside-click-handler

react-sortable-hoc, Copyright (c) 2016, Claudéric Demers
Licenses: The MIT License (MIT)
Source: https://github.com/clauderic/react-sortable-hoc

react-spring, Copyright (c) 2018 Paul Henschel
Licenses: MIT License
Source: https://github.com/drcmda/react-spring

react-transition-group, Copyright (c) 2018, React Community
Licenses: BSD 3-Clause License
Source: https://github.com/reactjs/react-transition-group

scriptjs, Copyright (c) 2011 - 2015 Dustin Diaz <dustin@dustindiaz.com>
Licenses: The MIT License
Source: https://github.com/ded/script.js

simple-linear-scale
Licenses: The MIT License (MIT)
Source: https://github.com/tmcw-up-for-adoption/simple-linear-scale

use-force-update, Copyright (c) 2018 Charles Stover
Licenses: MIT License
Source: https://github.com/CharlesStover/use-force-update

mobius1-selectr, Copyright 2016 Karl Saunders
Licenses: MIT License
Source: https://github.com/Mobius1/Selectr

rellax.js, Copyright 2016 Moe Amaya
Licenses: MIT License
Source: https://github.com/dixonandmoe/rellax/
