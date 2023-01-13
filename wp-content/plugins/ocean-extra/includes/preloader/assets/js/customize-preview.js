/**
 * Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Preloader Customizer preview reload changes asynchronously.
 */

 ( function( $ ) {

	// Declare vars
	var api = wp.customize;

	// Image size
	api("ocean_preloader_image_size", function($swipe) {
        $swipe.bind(function(to) {
            var $child = $(".customizer-ocean_preloader_image_size");
            if (to) {
                /** @type {string} */
                var img = '<style class="customizer-ocean_preloader_image_size">.ocean-preloader--active .preloader-image, .ocean-preloader--active .preloader-logo { max-width: ' + to + 'px; } .ocean-preloader--active .preloader-svg svg { width: ' + to + "px; }</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    // Content
    api('ocean_preloader_content', function( value ) {
        value.bind( function( newval ) {
          $( '#preloader-content .preloader-after-content' ).html( newval );
        });
    });

	// Container Width
	api("ocean_preloader_container_width", function($swipe) {
        $swipe.bind(function(to) {
            var $child = $(".customizer-ocean_preloader_container_width");
            if (to) {
                /** @type {string} */
                var img = '<style class="customizer-ocean_preloader_container_width">.ocean-preloader--active .preloader-inner { width: ' + to + "px; }</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    // Container Width
	api("ocean_preloader_container_width_tablet", function($swipe) {
        $swipe.bind(function(to) {
            var $child = $(".customizer-ocean_preloader_container_width_tablet");
            if (to) {
                /** @type {string} */
                var img = '<style class="customizer-ocean_preloader_container_width_tablet">@media (max-width: 768px){.ocean-preloader--active .preloader-inner { width: ' + to + "px; }}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    // Container Width
	api("ocean_preloader_container_width_mobile", function($swipe) {
        $swipe.bind(function(to) {
            var $child = $(".customizer-ocean_preloader_container_width_mobile");
            if (to) {
                /** @type {string} */
                var img = '<style class="customizer-ocean_preloader_container_width_mobile">@media (max-width: 768px){.ocean-preloader--active .preloader-inner { width: ' + to + "px; }}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

	// Overlay color.
	api('ocean_preloader_overlay_color', function( value ) {
		value.bind( function( newval ) {
	        if ( newval ) {
				$( '.ocean-preloader--active #ocean-preloader' ).css( 'background-color', newval );
	        }
		});
    });

	// Icon color.
	api('ocean_preloader_icon_color', function( value ) {
		value.bind( function( newval ) {
	        if ( newval ) {
				$( '.ocean-preloader--active .preloader-roller div:after' ).css( 'background', newval );
                $( '.ocean-preloader--active .preloader-circle > div' ).css( 'background', newval );
                $( '.ocean-preloader--active .preloader-ripple-plain div' ).css( 'background', newval );
                $( '.ocean-preloader--active .preloader-ripple-circle div' ).css( 'border-color', newval );
                $( '.ocean-preloader--active .preloader-ring div' ).css( 'border-top-color', newval );
                $( '.ocean-preloader--active .preloader-dual-ring:after' ).css( {'border-top-color': newval, 'border-bottom-color': newval} );
                $( '.ocean-preloader--active .preloader-heart div, .ocean-preloader--active .preloader-heart div::after, .ocean-preloader--active .preloader-heart div::before' ).css( 'background', newval );
                $( '.ocean-preloader--active .preloader-ellipsis div' ).css( 'background', newval );
                $( '.ocean-preloader--active .preloader-spinner-dot div' ).css( 'background', newval );
                $( '.ocean-preloader--active .preloader-spinner-line div:after' ).css( 'background', newval );
	        }
		});
    });

    // Typography - After content
    api("preloader_after_content_typography[font-family]", function ($swipe) {
        $swipe.bind(function (pair) {
            if (pair) {
                /** @type {string} */
                var idfirst = (pair.trim().toLowerCase().replace(" ", "-"), "customizer-typography-preloader_after_content-font-family");
                var fontSize = pair.replace(" ", "%20");
                fontSize = fontSize.replace(",", "%2C");
                /** @type {string} */
                fontSize = oceanwpTG.googleFontsUrl + "/css?family=" + pair + ":" + oceanwpTG.googleFontsWeight;
                if ($("#" + idfirst).length) {
                    $("#" + idfirst).attr("href", fontSize);
                } else {
                    $("head").append('<link id="' + idfirst + '" rel="stylesheet" type="text/css" href="' + fontSize + '">');
                }
            }
            var $child = $(".customizer-typography-preloader_after_content-font-family");
            if (pair) {
                /** @type {string} */
                var img = '<style class="customizer-typography-preloader_after_content-font-family">.ocean-preloader--active .preloader-after-content{font-family: ' + pair + ";}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_typography[font-weight]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-font-weight");
            if (dataAndEvents) {
                /** @type {string} */
                var img =
                    '<style class="customizer-typography-preloader_after_content-font-weight">.ocean-preloader--active .preloader-after-content{font-weight: ' +
                    dataAndEvents +
                    ";}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_typography[font-style]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-font-style");
            if (dataAndEvents) {
                /** @type {string} */
                var img =
                    '<style class="customizer-typography-preloader_after_content-font-style">.ocean-preloader--active .preloader-after-content{font-style: ' +
                    dataAndEvents +
                    ";}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_typography[font-size]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-font-size");
            if (dataAndEvents) {
                /** @type {string} */
                var img =
                    '<style class="customizer-typography-preloader_after_content-font-size">.ocean-preloader--active .preloader-after-content{font-size: ' +
                    dataAndEvents +
                    ";}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_tablet_typography[font-size]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-tablet-font-size");
            if (dataAndEvents) {
                /** @type {string} */
                var img =
                    '<style class="customizer-typography-preloader_after_content-tablet-font-size">@media (max-width: 768px){.ocean-preloader--active .preloader-after-content{font-size: ' +
                    dataAndEvents +
                    ";}}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_mobile_typography[font-size]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-mobile-font-size");
            if (dataAndEvents) {
                /** @type {string} */
                var img =
                    '<style class="customizer-typography-preloader_after_content-mobile-font-size">@media (max-width: 480px){.ocean-preloader--active .preloader-after-content{font-size: ' +
                    dataAndEvents +
                    ";}}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_typography[color]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-color");
            if (dataAndEvents) {
                /** @type {string} */
                var img = '<style class="customizer-typography-preloader_after_content-color">.ocean-preloader--active .preloader-after-content{color: ' + dataAndEvents + ";}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_typography[line-height]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-line-height");
            if (dataAndEvents) {
                /** @type {string} */
                var img =
                    '<style class="customizer-typography-preloader_after_content-line-height">.ocean-preloader--active .preloader-after-content{line-height: ' +
                    dataAndEvents +
                    ";}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_tablet_typography[line-height]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-tablet-line-height");
            if (dataAndEvents) {
                /** @type {string} */
                var img =
                    '<style class="customizer-typography-preloader_after_content-tablet-line-height">@media (max-width: 768px){.ocean-preloader--active .preloader-after-content{line-height: ' +
                    dataAndEvents +
                    ";}}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_mobile_typography[line-height]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-mobile-line-height");
            if (dataAndEvents) {
                /** @type {string} */
                var img =
                    '<style class="customizer-typography-preloader_after_content-mobile-line-height">@media (max-width: 480px){.ocean-preloader--active .preloader-after-content{line-height: ' +
                    dataAndEvents +
                    ";}}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_typography[letter-spacing]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-letter-spacing");
            if (dataAndEvents) {
                /** @type {string} */
                var img =
                    '<style class="customizer-typography-preloader_after_content-letter-spacing">.ocean-preloader--active .preloader-after-content{letter-spacing: ' +
                    dataAndEvents +
                    "px;}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_tablet_typography[letter-spacing]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-tablet-letter-spacing");
            if (dataAndEvents) {
                /** @type {string} */
                var img =
                    '<style class="customizer-typography-preloader_after_content-tablet-letter-spacing">@media (max-width: 768px){.ocean-preloader--active .preloader-after-content{letter-spacing: ' +
                    dataAndEvents +
                    "px;}}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_mobile_typography[letter-spacing]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-mobile-letter-spacing");
            if (dataAndEvents) {
                /** @type {string} */
                var img =
                    '<style class="customizer-typography-preloader_after_content-mobile-letter-spacing">@media (max-width: 480px){.ocean-preloader--active .preloader-after-content{letter-spacing: ' +
                    dataAndEvents +
                    "px;}}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

    api("preloader_after_content_typography[text-transform]", function ($swipe) {
        $swipe.bind(function (dataAndEvents) {
            var $child = $(".customizer-typography-preloader_after_content-text-transform");
            if (dataAndEvents) {
                /** @type {string} */
                var img =
                    '<style class="customizer-typography-preloader_after_content-text-transform">.ocean-preloader--active .preloader-after-content{text-transform: ' +
                    dataAndEvents +
                    ";}</style>";
                if ($child.length) {
                    $child.replaceWith(img);
                } else {
                    $("head").append(img);
                }
            } else {
                $child.remove();
            }
        });
    });

} )( jQuery );