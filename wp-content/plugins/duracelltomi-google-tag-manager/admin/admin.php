<?php
define( 'GTM4WP_ADMINSLUG',               'gtm4wp-settings' );
define( 'GTM4WP_ADMIN_GROUP',             'gtm4wp-admin-group' );

define( 'GTM4WP_ADMIN_GROUP_GENERAL',     'gtm4wp-admin-group-general' );
define( 'GTM4WP_ADMIN_GROUP_GTMID',       'gtm4wp-admin-group-gtm-id' );
define( 'GTM4WP_ADMIN_GROUP_PLACEMENT',   'gtm4wp-admin-code-placement' );
define( 'GTM4WP_ADMIN_GROUP_INFO',        'gtm4wp-admin-group-datalayer-info' );

define( 'GTM4WP_ADMIN_GROUP_INCLUDES',    'gtm4wp-admin-group-includes' );
define( 'GTM4WP_ADMIN_GROUP_EVENTS',      'gtm4wp-admin-group-events' );
define( 'GTM4WP_ADMIN_GROUP_SCROLLER',    'gtm4wp-admin-group-scroller' );
define( 'GTM4WP_ADMIN_GROUP_BLACKLIST',   'gtm4wp-admin-group-blacklist-tags' );
define( 'GTM4WP_ADMIN_GROUP_INTEGRATION', 'gtm4wp-admin-group-integration' );
define( 'GTM4WP_ADMIN_GROUP_ADVANCED',    'gtm4wp-admin-group-advanced' );
define( 'GTM4WP_ADMIN_GROUP_CREDITS',     'gtm4wp-admin-group-credits' );

define( 'GTM4WP_USER_NOTICES_KEY', 'gtm4wp_user_notices_dismisses' );

define( 'GTM4WP_PHASE_STABLE',       'gtm4wp-phase-stable' );
define( 'GTM4WP_PHASE_BETA',         'gtm4wp-phase-beta' );
define( 'GTM4WP_PHASE_EXPERIMENTAL', 'gtm4wp-phase-experimental' );
define( 'GTM4WP_PHASE_DEPRECATED',   'gtm4wp-phase-deprecated' );

$GLOBALS["gtm4wp_def_user_notices_dismisses"] = array(
	"enter-gtm-code" => false,
	"wc-ga-plugin-warning" => false,
	"wc-gayoast-plugin-warning" => false,
	"php56-warning" => false,
	"woo2x-warning" => false,
	"deprecated-warning" => false
);

$GLOBALS["gtm4wp_includefieldtexts"] = array(
	GTM4WP_OPTION_INCLUDE_POSTTYPE    => array(
		"label"       => __( "Posttype of current post/archive", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the type of the current post or archive page (post, page or any custom post type).", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_CATEGORIES  => array(
		"label"       => __( "Category list of current post/archive", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the category names of the current post or archive page", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_TAGS        => array(
		"label"       => __( "Tags of current post", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the tags of the current post.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_AUTHORID    => array(
		"label"       => __( "Post author ID", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the ID of the author on the current post or author page.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_AUTHOR      => array(
		"label"       => __( "Post author name", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the name of the author on the current post or author page.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_POSTDATE    => array(
		"label"       => __( "Post date", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the date of the current post. This will include 4 dataLayer variables: full date, post year, post month, post date.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_POSTTITLE   => array(
		"label"       => __( "Post title", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the title of the current post.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_POSTCOUNT   => array(
		"label"       => __( "Post count", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the count of the posts currently shown on the page and the total number of posts in the category/tag/any taxonomy.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_POSTID   => array(
		"label"       => __( "Post ID", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the post id.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_POSTFORMAT => array(
		"label"       => __( "Post Format", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the post format.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_SEARCHDATA  => array(
		"label"       => __( "Search data", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the search term, referring page URL and number of results on the search page.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_LOGGEDIN    => array(
		"label"       => __( "Logged in status", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include whether there is a logged in user on your website.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_USERROLE    => array(
		"label"       => __( "Logged in user role", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the role of the logged in user.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_USERID      => array(
		"label"       => __( "Logged in user ID", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the ID of the logged in user.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_USERNAME   => array(
		"label"       => __( "Logged in user name", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the username of the logged in user.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_USEREMAIL   => array(
		"label"       => __( "Logged in user email", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the email address of the logged in user.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_USERREGDATE => array(
		"label"       => __( "Logged in user creation date", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the date of creation (registration) of the logged in user.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_VISITOR_IP => array(
		"label"       => __( "Visitor IP", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the IP address of the visitor. You might use this to filter internal traffic inside your GTM container. Please be aware that per GDPR its not allowed to transmit this full IP address to Google Analytics or to any other measurement system without explicit consent from the visitor.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_REMARKETING => array(
		"label"       => __( "Remarketing variable", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include a dataLayer variable where all dataLayer values are stored to be included in your AdWords remarketing tag as a custom variable field", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_BROWSERDATA => array(
		"label"       => __( "Browser data *", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the name, version and engine data of the browser the visitor uses.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_BETA
	),
	GTM4WP_OPTION_INCLUDE_OSDATA => array(
		"label"       => __( "OS data *", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the name and version of the operating system the visitor uses.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_BETA
	),
	GTM4WP_OPTION_INCLUDE_DEVICEDATA  => array(
		"label"       => __( "Device data *", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include the type of device the user is currently using (desktop, tablet or mobile) including manufacturer and model data.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_BETA
	),
	GTM4WP_OPTION_INCLUDE_MISCGEO => array(
		"label"       => __( "Geo data", 'duracelltomi-google-tag-manager' ),
		"description" => __( 'Add geo data (latitude, longitude, country, city, etc) of the current visitor (provided by ipstack.com)', 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_EXPERIMENTAL
	),
	GTM4WP_OPTION_INCLUDE_MISCGEOAPI => array(
		"label"       => __( "IPStack.com API key", 'duracelltomi-google-tag-manager' ),
		"description" => sprintf( __( 'Enter your IPStack.com API key here. <a href="%s" target="_blank">Get a free API key here</a>.', 'duracelltomi-google-tag-manager' ), "https://ipstack.com/product?utm_source=gtm4wp&utm_medium=link&utm_campaign=gtm4wp-google-tag-manager-for-wordpress" ),
		"phase"       => GTM4WP_PHASE_EXPERIMENTAL
	),
	GTM4WP_OPTION_INCLUDE_WEATHER => array(
		"label"       => __( "Weather data", 'duracelltomi-google-tag-manager' ),
		"description" => sprintf( __( "Check this option to include the current weather conditions around the current visitor.<br /><br />".
		                              "<strong>Attention!</strong> This feature uses <a href=\"%s\" target=\"_blank\">ipstack.com</a> and ".
		                              "<a href=\"%s\" target=\"_blank\">openweathermap.org</a> to collect data.<br />".
		                              "Depending on your website's traffic, additional fees may apply!<br />".
		                              "This plugin caches weather data for 1 hour to lower the need to access those services.<br /><br />".
		                              "If you activate weather data, <strong>you will need</strong> to add an IPStack.com API key regardless of whether you ".
		                              "activate the 'Geo data' option!", 'duracelltomi-google-tag-manager' ),
		                              "https://ipstack.com/product?utm_source=gtm4wp&utm_medium=link&utm_campaign=gtm4wp-google-tag-manager-for-wordpress",
		                              "http://openweathermap.org/price?utm_source=gtm4wp&utm_medium=link&utm_campaign=gtm4wp-google-tag-manager-for-wordpress"
		),
		"phase"       => GTM4WP_PHASE_EXPERIMENTAL
	),
	GTM4WP_OPTION_INCLUDE_WEATHERUNITS => array(
		"label"       => __( "Weather data units", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Select which temperature units you would like to use.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_EXPERIMENTAL
	),
	GTM4WP_OPTION_INCLUDE_WEATHEROWMAPI => array(
		"label"       => __( "OpenWeatherMap API key", 'duracelltomi-google-tag-manager' ),
		"description" => sprintf( __( 'Enter your OpenWeatherMap API key here. <a href="%s" target="_blank">Get a free API key here</a>.', 'duracelltomi-google-tag-manager' ), "http://openweathermap.org/price?utm_source=gtm4wp&utm_medium=link&utm_campaign=gtm4wp-google-tag-manager-for-wordpress" ),
		"phase"       => GTM4WP_PHASE_EXPERIMENTAL
	),
	GTM4WP_OPTION_INCLUDE_SITEID => array(
		"label"       => __( "Site ID", 'duracelltomi-google-tag-manager' ),
		"description" => __( 'ID of the current site in a WordPress Multisite environment', 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_INCLUDE_SITENAME => array(
		"label"       => __( "Site name", 'duracelltomi-google-tag-manager' ),
		"description" => __( 'Name of the current site in a WordPress Multisite environment', 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	)
);

$GLOBALS["gtm4wp_eventfieldtexts"] = array(
	GTM4WP_OPTION_EVENTS_FORMMOVE => array(
		"label"       => __( "Form fill events (gtm4wp.formElementEnter & gtm4wp.formElementLeave)", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include a Tag Manager event when a visitor moves between elements of a form (comment, contact, etc).", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_EVENTS_NEWUSERREG => array(
		"label"       => __( "New user registration", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include a Tag Manager event when a new user registration has been completed on the frontend of your site (admin events not included)", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_EVENTS_USERLOGIN => array(
		"label"       => __( "User logged in", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include a Tag Manager event when an existing user has been logged in on the frontend of your site (admin events not included)", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_EVENTS_YOUTUBE => array(
		"label"       => __( "YouTube video events", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include a Tag Manager event when a visitor interacts with a YouTube video embeded on your site.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_EXPERIMENTAL
	),
	GTM4WP_OPTION_EVENTS_VIMEO => array(
		"label"       => __( "Vimeo video events", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include a Tag Manager event when a visitor interacts with a Vimeo video embeded on your site.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_EXPERIMENTAL
	),
	GTM4WP_OPTION_EVENTS_SOUNDCLOUD => array(
		"label"       => __( "Soundcloud events", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include a Tag Manager event when a visitor interacts with a Soundcloud media embeded on your site.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_EXPERIMENTAL
	),
	GTM4WP_OPTION_EVENTS_SOCIAL => array(
		"label"       => __( "Social actions (gtm4wp.socialAction)", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include a Tag Manager event when a visitor uses a social button to share/like content on a social network.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_DEPRECATED
	),
	GTM4WP_OPTION_EVENTS_OUTBOUND    => array(
		"label"       => __( "Outbound link click events (gtm4wp.outboundClick)", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include a Tag Manager event when a visitor clicks on a link directing the visitor out of your website.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_DEPRECATED
	),
	GTM4WP_OPTION_EVENTS_DOWNLOADS   => array(
		"label"       => __( "Download click events (gtm4wp.downloadClick)", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include a Tag Manager event when a visitors clicks on a link that leads to a downloadable file on your website.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_DEPRECATED
	),
	GTM4WP_OPTION_EVENTS_DWLEXT      => array(
		"label"       => __( "Extensions to track", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Enter a comma separated list of extensions to track when 'Include download click events' option is set.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_DEPRECATED
	),
	GTM4WP_OPTION_EVENTS_EMAILCLICKS => array(
		"label"       => __( "Email click events (gtm4wp.emailClick)", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Check this option to include a Tag Manager event when a visitor clicks on an email link.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_DEPRECATED
	)
);

$GLOBALS["gtm4wp_scrollerfieldtexts"] = array(
	GTM4WP_OPTION_SCROLLER_ENABLED      => array(
		"label"       => __( "Enabled", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Enable scroll tracker script on your website.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_SCROLLER_DEBUGMODE    => array(
		"label"       => __( "Debug mode", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Fire console.log() commands instead of dataLayer events.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_SCROLLER_CALLBACKTIME => array(
		"label"       => __( "Time delay before location check", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Enter the number of milliseconds after the script checks the current location. It prevents too many events being fired while scrolling.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_SCROLLER_DISTANCE     => array(
		"label"       => __( "Minimum distance", 'duracelltomi-google-tag-manager' ),
		"description" => __( "The minimum amount of pixels that a visitor has to scroll before we treat the move as scrolling.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_SCROLLER_CONTENTID    => array(
		"label"       => __( "Content ID", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Enter the DOM ID of the content element in your template. Leave it empty for default(content). Do not include the # sign.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_SCROLLER_READERTIME   => array(
		"label"       => __( "Scroller time", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Enter the number of seconds after the the scroller user is being treated as a reader, someone who really reads the content, not just scrolls through it.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	)
);

$GLOBALS["gtm4wp_blacklistfieldtexts"] = array(
	GTM4WP_OPTION_BLACKLIST_ENABLE       => array(
		"label"         => __( "Enable blacklist/whitelist", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "Disable feature or select whether you want to whitelist or blacklist tags", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_AWCONV       => array(
		"label"         => __( "AdWords Conversion Tracking Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_AWREMARKET       => array(
		"label"         => __( "AdWords Remarketing Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_GACLASSIC       => array(
		"label"         => __( "Google Analytics Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_UA       => array(
		"label"         => __( "Universal Analytics Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_ADADVISOR       => array(
		"label"         => __( "AdAdvisor Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_ADROLL       => array(
		"label"         => __( "AdRoll Smart Pixel Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_BIZO       => array(
		"label"         => __( "Bizo Insight Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_CLICKTALE       => array(
		"label"         => __( "ClickTale Standard Tracking Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_COMSCORE       => array(
		"label"         => __( "comScore Unified Digital Measurement Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_CUSTOMHTML       => array(
		"label"         => __( "Custom HTML Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_CUSTOMIMG       => array(
		"label"         => __( "Custom Image Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_DBLCLKCOUNT       => array(
		"label"         => __( "DoubleClick Floodlight Counter Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_DBLCLKSALES       => array(
		"label"         => __( "DoubleClick Floodlight Sales Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MARIN       => array(
		"label"         => __( "Marin Software Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MPLEXIFRAME       => array(
		"label"         => __( "Mediaplex - IFRAME MCT Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MPLEXROI       => array(
		"label"         => __( "Mediaplex - Standard IMG ROI Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MEDIA6DEG       => array(
		"label"         => __( "Media6Degrees Universal Pixel Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_TURNCONV       => array(
		"label"         => __( "Turn Conversion Tracking Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_TURNDATA       => array(
		"label"         => __( "Turn Data Collection Tag", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	)
);

$GLOBALS["gtm4wp_blacklistmfieldtexts"] = array(
	GTM4WP_OPTION_BLACKLIST_MACRO_DOMELEMENT => array(
		"label"         => __( "DOM element Macro", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MACRO_CUSTOMJS   => array(
		"label"         => __( "Custom JavaScript Macro", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MACRO_CONSTANT   => array(
		"label"         => __( "Constant Macro", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MACRO_1STCOOKIE  => array(
		"label"         => __( "First Party Cookie Macro", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MACRO_EVENTNAME  => array(
		"label"         => __( "Event Name Macro", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MACRO_JSVAR      => array(
		"label"         => __( "JavaScript Variable Macro", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MACRO_DLAYERVAR  => array(
		"label"         => __( "DataLayer Variable Macro", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "It is not recommended to blacklist this type of macro since most features of this plugin depend on dataLayer macros.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MACRO_RANDOMNUM  => array(
		"label"         => __( "Random Number Macro", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MACRO_REFERRER   => array(
		"label"         => __( "Referrer Macro", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MACRO_URL        => array(
		"label"         => __( "URL Macro", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_BLACKLIST_MACRO_AUTOEVENT  => array(
		"label"         => __( "Auto-Event Variable Macro", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	)
);

$GLOBALS["gtm4wp_integratefieldtexts"] = array(
	GTM4WP_OPTION_INTEGRATE_WPCF7       => array(
		"label"         => __( "Contact Form 7", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "Check this to include a dataLayer event after a successfull form submission.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE,
		"plugintocheck" => "contact-form-7/wp-contact-form-7.php"
	),
	GTM4WP_OPTION_INTEGRATE_WCTRACKCLASSICEC => array(
		"label"         => __( "Track classic e-commerce", 'duracelltomi-google-tag-manager' ),
		"description"   => sprintf( __( "Choose this option if you would like to track e-commerce data using <a href=\"%s\" target=\"_blank\">classic transaction data</a>.", 'duracelltomi-google-tag-manager'  ) , 'https://developers.google.com/analytics/devguides/collection/analyticsjs/ecommerce' ),
		"phase"       => GTM4WP_PHASE_STABLE,
		"plugintocheck" => "woocommerce/woocommerce.php"
	),
	GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC => array(
		"label"         => __( "Track enhanced e-commerce", 'duracelltomi-google-tag-manager' ),
		"description"   => sprintf( __( "Choose this option if you would like to track e-commerce data using <a href=\"%s\" target=\"_blank\">enhanced ecommerce tracking</a>.", 'duracelltomi-google-tag-manager'  ) , 'https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce' ),
		"phase"       => GTM4WP_PHASE_BETA,
		"plugintocheck" => "woocommerce/woocommerce.php"
	),
	GTM4WP_OPTION_INTEGRATE_WCEECCARTASFIRSTSTEP => array(
		"label"         => __( "Cart as 1st checkout step", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "Enable this to track the cart page as the first checkout step in enhanced ecommerce instead of the checkout page itself", 'duracelltomi-google-tag-manager' ),
		"phase"         => GTM4WP_PHASE_BETA,
		"plugintocheck" => "woocommerce/woocommerce.php"
	),
	GTM4WP_OPTION_INTEGRATE_WCCUSTOMERDATA => array(
		"label"         => __( "Customer data in data layer", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "Enable this to add all customer data (billing and shipping data, total number of orders and order value) into the data layer (WooCommerce 3.x required)", 'duracelltomi-google-tag-manager' ),
		"phase"         => GTM4WP_PHASE_BETA,
		"plugintocheck" => "woocommerce/woocommerce.php"
	),
	GTM4WP_OPTION_INTEGRATE_WCEXCLUDETAX => array(
		"label"         => __( "Exclude tax from revenue", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "Enable this to exclude tax from the revenue variable while generating the purchase data", 'duracelltomi-google-tag-manager' ),
		"phase"         => GTM4WP_PHASE_BETA,
		"plugintocheck" => "woocommerce/woocommerce.php"
	),
	GTM4WP_OPTION_INTEGRATE_WCREMARKETING  => array(
		"label"         => __( "AdWords Remarketing", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "Enable this to add Google AdWords dynamic remarketing variables to the dataLayer", 'duracelltomi-google-tag-manager' ),
		"phase"         => GTM4WP_PHASE_BETA,
		"plugintocheck" => "woocommerce/woocommerce.php"
	),
	GTM4WP_OPTION_INTEGRATE_WCREMPRODIDPREFIX => array(
		"label"         => __( "Product ID prefix", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "Some product feed generator plugins prefix product IDs with a fixed text like 'woocommerce_gpf'. You can enter this prefix here so that tags in your website include this prefix as well.", 'duracelltomi-google-tag-manager' ),
		"phase"         => GTM4WP_PHASE_BETA,
		"plugintocheck" => "woocommerce/woocommerce.php"
	),
	GTM4WP_OPTION_INTEGRATE_WCUSESKU => array(
		"label"         => __( "Use SKU instead of ID", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "Check this to use product SKU instead of the ID of the products for remarketing and ecommerce tracking. Will fallback to ID if no SKU is set.", 'duracelltomi-google-tag-manager' ),
		"phase"         => GTM4WP_PHASE_BETA,
		"plugintocheck" => "woocommerce/woocommerce.php"
	),
	GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH => array(
		"label"         => __( "Include full category path.", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "Check this to inclulde the full category path of each product in enhanced ecommerce tracking. WARNING! This can lead to performance issues on large sites with lots of traffic!", 'duracelltomi-google-tag-manager' ),
		"phase"         => GTM4WP_PHASE_BETA,
		"plugintocheck" => "woocommerce/woocommerce.php"
	),
	GTM4WP_OPTION_INTEGRATE_WCPRODPERIMPRESSION => array(
		"label"         => __( "Products per impression", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "If you have many products shown on product category pages and/or on your site home, you could miss pageviews in Google Analytics due to the ".
		                       "amount of data that is needed to be sent. To prevent this, you can split product impression data into multiple Google Analytics events by ".
		                       "entering a number here (minimum 10-15 recommended) and adding gtm4wp.productImpressionEEC into your Google Analytics ecommerce event helper ".
		                       "tag's trigger.<br /><br />Leave this value 0 to include product impression data in your pageview hit.", 'duracelltomi-google-tag-manager' ),
		"phase"         => GTM4WP_PHASE_BETA,
		"plugintocheck" => "woocommerce/woocommerce.php"
	),
	GTM4WP_OPTION_INTEGRATE_WCNOORDERTRACKEDFLAG => array(
		"label"         => __( "Do not flag orders as being tracked", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "Check this to prevent the plugin to flag orders as being already tracked.<br /><br />This ensures that no order data will be tracked ".
		                       "multiple times in any ad or measurement system so please only enable this feature if you really need it (with iDeal you might need this)!",
		                       'duracelltomi-google-tag-manager' ),
		"phase"         => GTM4WP_PHASE_BETA,
		"plugintocheck" => "woocommerce/woocommerce.php"
	),

	GTM4WP_OPTION_INTEGRATE_GOOGLEOPTIMIZEIDS => array(
		"label"         => __( "Google Optimize container ID list", 'duracelltomi-google-tag-manager' ),
		"description"   => sprintf( __( "Enter a comma separated list of Google Optimize container IDs that you would like to use on your site.<br />".
		                   "This plugin will add the <a href=\"%s\">page-hiding snippet</a> to your pages.<br /><br />".
		                   "The Google Optimize container will be only loaded if you provide your Google Analytics property ID bellow as well.", 'duracelltomi-google-tag-manager' ), 'https://developers.google.com/optimize/#the_page-hiding_snippet_code' ) .
			'<br /><span class="goid_validation_error">' . __( "This does not seems to be a valid Google Optimize ID! Valid format: GTM-XXXXXX where X can be numbers and capital letters. Use comma without any space (,) to enter multpile IDs.", 'duracelltomi-google-tag-manager' ) . '</span>',
		"phase"         => GTM4WP_PHASE_EXPERIMENTAL
	),
	GTM4WP_OPTION_INTEGRATE_GOOGLEOPTIMIZETIMEOUT => array(
		"label"         => __( "Google Optimize page-hiding timeout", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "Enter here the amount of time in milliseconds that the page-hiding snippet should wait before page content gets visible even if Google Optimize has not been completely loaded yet.", 'duracelltomi-google-tag-manager' ),
		"phase"         => GTM4WP_PHASE_EXPERIMENTAL
	),
	GTM4WP_OPTION_INTEGRATE_GOOGLEOPTIMIZEGAID => array(
		"label"         => __( "Google Optimize - Google Analytics property ID", 'duracelltomi-google-tag-manager' ),
		"description"   => __( "If you enter your Google Analytics property ID here as well (UA-NNNNNN-N), this plugin will also load your Google Optimize container itself. Leave this blank to only load the page hiding snippet.", 'duracelltomi-google-tag-manager' ) .
			'<br /><span class="goid_ga_validation_error">' . __( "This does not seems to be a valid Google Analytics property ID! Valid format: UA-NNNNNN-N where N can be numbers. Enter a single property ID here.", 'duracelltomi-google-tag-manager' ) . '</span>',
		"phase"         => GTM4WP_PHASE_EXPERIMENTAL
	),

	GTM4WP_OPTION_INTEGRATE_AMPID => array(
		"label"         => __( "Google Tag Manager 'AMP' Container ID", 'duracelltomi-google-tag-manager' ),
		"description"   => sprintf( __( "Enter a comma separated list of Google Tag Manager container IDs that you would like to use on your site. This plugin will add the <a href=\"%s\">AMP GTM snippet</a> to your AMP pages.", 'duracelltomi-google-tag-manager' ), 'https://support.google.com/tagmanager/answer/6103696?hl=en' ) .
			'<br /><span class="ampid_validation_error">' . __( "This does not seems to be a valid Google Tag Manager Container ID! Valid format: GTM-XXXXXX where X can be numbers and capital letters. Use comma without any space (,) to enter multpile IDs.", 'duracelltomi-google-tag-manager' ) . '</span>',
		"phase"         => GTM4WP_PHASE_EXPERIMENTAL,
		"plugintocheck" => "amp/amp.php"
	)
);

$GLOBALS["gtm4wp_advancedfieldtexts"] = array(
	GTM4WP_OPTION_DATALAYER_NAME  => array(
		"label"       => __( "dataLayer variable name", 'duracelltomi-google-tag-manager' ),
		"description" => __( "In some cases you need to rename the dataLayer variable. You can enter your name here. Leave black for default name: dataLayer", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_ENV_GTM_AUTH    => array(
		"label"       => __( "Environment gtm_auth parameter", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Enter the gtm_auth parameter of the Google Tag Manager environment that has to be activated on this site. Both gtm_auth and gtm_preview parameters are required to activate the desired environment.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_ENV_GTM_PREVIEW => array(
		"label"       => __( "Environment gtm_preview parameter", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Enter the gtm_auth parameter of the Google Tag Manager environment that has to be activated on this site. Both gtm_auth and gtm_preview parameters are required to activate the desired environment.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_DONOTTRACK => array(
		"label"       => __( "Include browser 'Do not track' setting", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Add into the data layer whether the user has asked not to track any website interaction. You may want to respect this and disable all tags if this variable is set in the data layer.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	),
	GTM4WP_OPTION_LOADEARLY => array(
		"label"       => __( "Load GTM container as early as possible", 'duracelltomi-google-tag-manager' ),
		"description" => __( "Turning on this option will load your Google Tag Manager container as early as possible during page load. This can cause issues if you are using jQuery in your custom HTML tags that fire on 'Page View' events.", 'duracelltomi-google-tag-manager' ),
		"phase"       => GTM4WP_PHASE_STABLE
	)
);

function gtm4wp_admin_output_section( $args ) {
	echo '<span class="tabinfo">';

	switch( $args["id"] ) {
		case GTM4WP_ADMIN_GROUP_GENERAL: {
			_e( 'This plugin is intended to be used by IT girls&guys and marketing staff. Please be sure you read the <a href="https://developers.google.com/tag-manager/" target="_blank">Google Tag Manager Help Center</a> before you start using this plugin.<br /><br />', 'duracelltomi-google-tag-manager' );

			break;
		}

		case GTM4WP_ADMIN_GROUP_INCLUDES: {
			_e( "Here you can check what data is needed to be included in the dataLayer to be able to access them in Google Tag Manager", 'duracelltomi-google-tag-manager' );
			echo "<br />";
			printf( __( '* Browser, OS and Device data is provided using <a href="%s">WhichBrowser</a> library.', 'duracelltomi-google-tag-manager' ), "http://whichbrowser.net/" );
			if ( version_compare( PHP_VERSION, '5.4.0' ) < 0 ) {
				echo "<br />";
				_e( 'You are using an <strong>outdated</strong> version of PHP (v' . PHP_VERSION . '). You <strong>should not turn on</strong> browser/device/os tracking as this can hurt your site. Please talk to your hosting and upgrade to PHP 5.4 or newer.', 'duracelltomi-google-tag-manager' );
			}

			break;
		}

		case GTM4WP_ADMIN_GROUP_EVENTS: {
			_e( "Fire tags in Google Tag Manager on special events on your website", 'duracelltomi-google-tag-manager' );
			echo '<p style="font-weight: bold;">';
			_e( 'In October 2013 Google released a new feature called <a href="https://support.google.com/tagmanager/answer/3415369?hl=en" target="_blank">auto event tracking</a>. It is up to you how you use click events either using Google\'s solution or the settings below.', 'duracelltomi-google-tag-manager' );
			echo '</p>';

			break;
		}

		case GTM4WP_ADMIN_GROUP_SCROLLER: {
			_e( "Fire tags based on how the visitor scrolls through your page.", 'duracelltomi-google-tag-manager' );
			echo '<br />';
			printf( __( 'Based on the script originaly posted to <a href="%s">Analytics Talk</a>', 'duracelltomi-google-tag-manager' ) , "http://cutroni.com/blog/2012/02/21/advanced-content-tracking-with-google-analytics-part-1/");

			break;
		}

		case GTM4WP_ADMIN_GROUP_BLACKLIST: {
			_e( "Here you can control which types of tags and macros can be executed on your site regardless of what tags are included in your container on the Google Tag Manager site. Use this to increase security!", 'duracelltomi-google-tag-manager' );
			echo '<br />';
			_e( "Do not modify if you do not know what to do since it can cause issues in your tag deployment!", 'duracelltomi-google-tag-manager' );
			echo '<br />';
			_e( "Blacklisting of macros only work if you enable blacklist or whilelist of tags on the previous tab.", 'duracelltomi-google-tag-manager' );
			echo '<br />';
			_e( "Uncheck a row to blacklist a macro type.", 'duracelltomi-google-tag-manager' );

			break;
		}

		case GTM4WP_ADMIN_GROUP_INTEGRATION: {
			_e( "Google Tag Manager for WordPress can integrate with several popular plugins. Please check the plugins you would like to integrate with:", 'duracelltomi-google-tag-manager' );

			break;
		}

		case GTM4WP_ADMIN_GROUP_ADVANCED: {
			_e( "You usually do not need to modify thoose settings. Please be carefull while hacking here.", 'duracelltomi-google-tag-manager' );

			break;
		}

		case GTM4WP_ADMIN_GROUP_CREDITS: {
			_e( "Some info about the author of this plugin", 'duracelltomi-google-tag-manager' );

			break;
		}
	} // end switch

	echo '</span>';
}

function gtm4wp_admin_output_field( $args ) {
	global $gtm4wp_options;

	switch( $args["label_for"] ) {
		case GTM4WP_ADMIN_GROUP_GTMID: {
			echo '<input type="text" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_GTM_CODE . ']" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_GTM_CODE . ']" value="' . $gtm4wp_options[GTM4WP_OPTION_GTM_CODE] . '" /><br />' . $args["description"];
			echo '<br /><span class="gtmid_validation_error">' . __( "This does not seems to be a valid Google Tag Manager ID! Valid format: GTM-XXXXX where X can be numbers and capital letters. Use comma without any space (,) to enter multpile container IDs.", 'duracelltomi-google-tag-manager' ) . '</span>';

			break;
		}

		case GTM4WP_ADMIN_GROUP_PLACEMENT: {
			echo '<input type="radio" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_GTM_PLACEMENT . ']_' . GTM4WP_PLACEMENT_FOOTER . '" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_GTM_PLACEMENT . ']" value="' . GTM4WP_PLACEMENT_FOOTER . '" ' . ( $gtm4wp_options[ GTM4WP_OPTION_GTM_PLACEMENT ] == GTM4WP_PLACEMENT_FOOTER ? 'checked="checked"' : '' ) . '/> ' . __( "Footer of the page (not recommended by Google, no tweak in your template required)", 'duracelltomi-google-tag-manager' ) . '<br />';
			echo '<input type="radio" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_GTM_PLACEMENT . ']_' . GTM4WP_PLACEMENT_BODYOPEN . '" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_GTM_PLACEMENT . ']" value="' . GTM4WP_PLACEMENT_BODYOPEN . '" ' . ( $gtm4wp_options[ GTM4WP_OPTION_GTM_PLACEMENT ] == GTM4WP_PLACEMENT_BODYOPEN ? 'checked="checked"' : '' ) . '/> ' . __( "Custom (needs tweak in your template)", 'duracelltomi-google-tag-manager' ) . '<br />';
			echo '<input type="radio" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_GTM_PLACEMENT . ']_' . GTM4WP_PLACEMENT_BODYOPEN_AUTO . '" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_GTM_PLACEMENT . ']" value="' . GTM4WP_PLACEMENT_BODYOPEN_AUTO . '" ' . ( $gtm4wp_options[ GTM4WP_OPTION_GTM_PLACEMENT ] == GTM4WP_PLACEMENT_BODYOPEN_AUTO ? 'checked="checked"' : '' ) . '/> ' . __( "Codeless injection (no tweak, right placement but experimental, could break your frontend)", 'duracelltomi-google-tag-manager' ) . '<br />';
			echo '<input type="radio" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_GTM_PLACEMENT . ']_' . GTM4WP_PLACEMENT_OFF . '" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_GTM_PLACEMENT . ']" value="' . GTM4WP_PLACEMENT_OFF . '" ' . ( $gtm4wp_options[ GTM4WP_OPTION_GTM_PLACEMENT ] == GTM4WP_PLACEMENT_OFF ? 'checked="checked"' : '' ) . '/> ' . __( "Off (only add data layer to the page source)", 'duracelltomi-google-tag-manager' ) . '<br /><br />' . $args["description"];

			break;
		}

		case GTM4WP_OPTIONS . "[" . GTM4WP_OPTION_DATALAYER_NAME . "]": {
			echo '<input type="text" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_DATALAYER_NAME . ']" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_DATALAYER_NAME . ']" value="' . $gtm4wp_options[GTM4WP_OPTION_DATALAYER_NAME] . '" /><br />' . $args["description"];
			echo '<br /><span class="datalayername_validation_error">' . __( "This does not seems to be a valid JavaScript variable name! Please check and try again", 'duracelltomi-google-tag-manager' ) . '</span>';

			break;
		}

		case GTM4WP_OPTIONS . "[" . GTM4WP_OPTION_ENV_GTM_AUTH . "]": {
			echo '<input type="text" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_ENV_GTM_AUTH . ']" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_ENV_GTM_AUTH . ']" value="' . $gtm4wp_options[GTM4WP_OPTION_ENV_GTM_AUTH] . '" /><br />' . $args["description"];
			echo '<br /><span class="gtmauth_validation_error">' . __( "This does not seems to be a valid gtm_auth parameter! It should only contain letters, number and the &quot;-&quot; character. Please check and try again", 'duracelltomi-google-tag-manager' ) . '</span>';

			break;
		}

		case GTM4WP_OPTIONS . "[" . GTM4WP_OPTION_ENV_GTM_PREVIEW . "]": {
			echo '<input type="text" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_ENV_GTM_PREVIEW . ']" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_ENV_GTM_PREVIEW . ']" value="' . $gtm4wp_options[GTM4WP_OPTION_ENV_GTM_PREVIEW] . '" /><br />' . $args["description"];
			echo '<br /><span class="gtmpreview_validation_error">' . __( "This does not seems to be a valid gtm_preview parameter! It should have the format &quot;env-NN&quot; where NN is an integer number. Please check and try again", 'duracelltomi-google-tag-manager' ) . '</span>';

			break;
		}

		case GTM4WP_OPTIONS . "[" . GTM4WP_OPTION_BLACKLIST_ENABLE . "]": {
			echo '<input type="radio" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_BLACKLIST_ENABLE . ']_0" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_BLACKLIST_ENABLE . ']" value="0" ' . ( $gtm4wp_options[GTM4WP_OPTION_BLACKLIST_ENABLE] == 0 ? 'checked="checked"' : '' ) . '/> ' . __( "Disable feature: control everything on Google Tag Manager interface", 'duracelltomi-google-tag-manager' ) . '<br />';
			echo '<input type="radio" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_BLACKLIST_ENABLE . ']_1" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_BLACKLIST_ENABLE . ']" value="1" ' . ( $gtm4wp_options[GTM4WP_OPTION_BLACKLIST_ENABLE] == 1 ? 'checked="checked"' : '' ) . '/> ' . __( "Allow all, except the checked items below (blacklist)", 'duracelltomi-google-tag-manager' ) . '<br />';
			echo '<input type="radio" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_BLACKLIST_ENABLE . ']_2" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_BLACKLIST_ENABLE . ']" value="2" ' . ( $gtm4wp_options[GTM4WP_OPTION_BLACKLIST_ENABLE] == 2 ? 'checked="checked"' : '' ) . '/> ' . __( "Block all, except the checked items below (whitelist)", 'duracelltomi-google-tag-manager' ) . '<br />';
			echo $args["description"];

			break;
		}

		case GTM4WP_OPTIONS . "[" . GTM4WP_OPTION_INCLUDE_WEATHERUNITS . "]": {
			echo '<input type="radio" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_INCLUDE_WEATHERUNITS . ']_0" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_INCLUDE_WEATHERUNITS . ']" value="0" ' . ( $gtm4wp_options[GTM4WP_OPTION_INCLUDE_WEATHERUNITS] == 0 ? 'checked="checked"' : '' ) . '/> ' . __( "Celsius", 'duracelltomi-google-tag-manager' ) . '<br />';
			echo '<input type="radio" id="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_INCLUDE_WEATHERUNITS . ']_1" name="' . GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_INCLUDE_WEATHERUNITS . ']" value="1" ' . ( $gtm4wp_options[GTM4WP_OPTION_INCLUDE_WEATHERUNITS] == 1 ? 'checked="checked"' : '' ) . '/> ' . __( "Fahrenheit", 'duracelltomi-google-tag-manager' ) . '<br />';
			echo $args["description"];

			break;
		}

		case GTM4WP_ADMIN_GROUP_INFO: {
			echo $args["description"];

			break;
		}

		default: {
			$optval = $gtm4wp_options[$args["optionfieldid"]];

			// fix wrong data type saved in v0.4
			if ( GTM4WP_OPTION_EVENTS_SOCIAL == $args["optionfieldid"] ) {
				$optval = ( boolean )( $optval );
			}

			switch( gettype( $optval ) ) {
				case "boolean": {
					echo '<input type="checkbox" id="' . GTM4WP_OPTIONS . '[' . $args["optionfieldid"] . ']" name="' . GTM4WP_OPTIONS . '[' . $args["optionfieldid"] . ']" value="1" ' . checked( 1, $optval, false ) . ' /><br />' . $args["description"];

					if ( isset( $args["plugintocheck"] ) && ( $args["plugintocheck"] != "" ) ) {
						if ( is_plugin_active( $args["plugintocheck"] ) ) {
							echo "<br />" . __( 'This plugin is <strong class="gtm4wp-plugin-active">active</strong>, it is strongly recommended to enable this integration!', 'duracelltomi-google-tag-manager' );
						} else {
							echo "<br />" . __( 'This plugin ('.$args["plugintocheck"].') is <strong class="gtm4wp-plugin-not-active">not active</strong>, enabling this integration could cause issues on frontend!', 'duracelltomi-google-tag-manager' );
						}
					}

					break;
				}

				case "integer": {
					echo '<input type="number" step="1" min="0" class="small-text" id="' . GTM4WP_OPTIONS . '[' . $args["optionfieldid"] . ']" name="' . GTM4WP_OPTIONS . '[' . $args["optionfieldid"] . ']" value="' . esc_attr( $optval ) . '" /><br />' . $args["description"];

					if ( isset( $args["plugintocheck"] ) && ( $args["plugintocheck"] != "" ) ) {
						if ( is_plugin_active( $args["plugintocheck"] ) ) {
							echo "<br />" . __( 'This plugin is <strong class="gtm4wp-plugin-active">active</strong>, it is strongly recommended to enable this integration!', 'duracelltomi-google-tag-manager' );
						} else {
							echo "<br />" . __( 'This plugin is <strong class="gtm4wp-plugin-not-active">not active</strong>, enabling this integration could cause issues on frontend!', 'duracelltomi-google-tag-manager' );
						}
					}

					break;
				}

				default : {
					echo '<input type="text" id="' . GTM4WP_OPTIONS . '[' . $args["optionfieldid"] . ']" name="' . GTM4WP_OPTIONS . '[' . $args["optionfieldid"] . ']" value="' . esc_attr( $optval ) . '" size="80" /><br />' . $args["description"];

					if ( isset( $args["plugintocheck"] ) && ( $args["plugintocheck"] != "" ) ) {
						if ( is_plugin_active( $args["plugintocheck"] ) ) {
							echo "<br />" . __( 'This plugin is <strong class="gtm4wp-plugin-active">active</strong>, it is strongly recommended to enable this integration!', 'duracelltomi-google-tag-manager' );
						} else {
							echo "<br />" . __( 'This plugin is <strong class="gtm4wp-plugin-not-active">not active</strong>, enabling this integration could cause issues on frontend!', 'duracelltomi-google-tag-manager' );
						}
					}
				}
			} // end switch gettype optval
		}
	} // end switch
}

function gtm4wp_sanitize_options($options) {
	global $wpdb;

	$output = gtm4wp_reload_options();

	foreach($output as $optionname => $optionvalue) {
		if ( isset( $options[$optionname] ) ) {
			$newoptionvalue = $options[$optionname];
		} else {
			$newoptionvalue = "";
		}

		// "include" settings
		if ( substr($optionname, 0, 8) == "include-" ) {
			$output[$optionname] = (boolean) $newoptionvalue;

		// tracked download extensions
		} else if ( $optionname == GTM4WP_OPTION_EVENTS_DWLEXT ) {
			$output[$optionname] = str_replace( " ", "", trim( $newoptionvalue ) );

		// dataLayer events
		} else if ( substr($optionname, 0, 6) == "event-" ) {
			$output[$optionname] = (boolean) $newoptionvalue;

			// clear oembed transients when feature is enabled because we need to hook into the oembed process to enable some 3rd party APIs
			if ( $output[$optionname] && !$optionvalue ) {
				if ( GTM4WP_OPTION_EVENTS_YOUTUBE == $optionname ) {
					$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_value LIKE '%youtube.com%' AND meta_key LIKE '_oembed_%'" );
				}

				if ( GTM4WP_OPTION_EVENTS_VIMEO == $optionname ) {
					$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_value LIKE '%vimeo.com%' AND meta_key LIKE '_oembed_%'" );
				}
			}

		// Google Optimize settings
		} else if ( $optionname == GTM4WP_OPTION_INTEGRATE_GOOGLEOPTIMIZEIDS ) {
			$_goid_val  = trim($newoptionvalue);
			if ( "" == $_goid_val ) {
				$_goid_list = array();
			} else {
				$_goid_list = explode( ",", $_goid_val );
			}
			$_goid_haserror = false;

			foreach( $_goid_list as $one_go_id ) {
				$_goid_haserror = $_goid_haserror || !preg_match( "/^GTM-[A-Z0-9]+$/", $one_go_id );
			}

			if ( $_goid_haserror && (count($_goid_list) > 0) ) {
				add_settings_error( GTM4WP_ADMIN_GROUP, GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_INTEGRATE_GOOGLEOPTIMIZEIDS . ']', __( "Invalid Google Optimize ID. Valid ID format: GTM-XXXXX. Use comma without additional space (,) to enter more than one ID.", 'duracelltomi-google-tag-manager' ) );
			} else {
				$output[$optionname] = $newoptionvalue;
			}

		} else if ( $optionname == GTM4WP_OPTION_INTEGRATE_GOOGLEOPTIMIZEGAID ) {
			$_gaid_val  = trim($newoptionvalue);
			if ( "" == $_gaid_val ) {
				$_gaid_list = array();
			} else {
				$_gaid_list = explode( ",", $_gaid_val );
			}
			$_gaid_haserror = false;

			foreach( $_gaid_list as $one_ga_id ) {
				$_gaid_haserror = $_gaid_haserror || !preg_match( "/^UA-[0-9]+-[0-9]+$/", $one_ga_id );
			}

			if ( $_gaid_haserror && (count($_gaid_list) > 0) ) {
				add_settings_error( GTM4WP_ADMIN_GROUP, GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_INTEGRATE_GOOGLEOPTIMIZEIDS . ']', __( "Invalid Google Analytics property ID. Valid ID format: UA-NNNNNN-N.", 'duracelltomi-google-tag-manager' ) );
			} else {
				$output[$optionname] = $newoptionvalue;
			}

		} else if ( $optionname == GTM4WP_OPTION_INTEGRATE_GOOGLEOPTIMIZETIMEOUT ) {
			$output[$optionname] = (int) $newoptionvalue;

		} else if ( $optionname == GTM4WP_OPTION_INTEGRATE_WCPRODPERIMPRESSION ) {
			$output[$optionname] = (int) $newoptionvalue;

		} else if ( $optionname == GTM4WP_OPTION_INTEGRATE_WCREMPRODIDPREFIX ) {
			$output[$optionname] = trim( (string) $newoptionvalue );

		// Accelerated Mobile Pages settings
		} else if ( $optionname == GTM4WP_OPTION_INTEGRATE_AMPID ) {
			$_ampid_val  = trim($newoptionvalue);
			if ( "" == $_ampid_val ) {
				$_ampid_list = array();
			} else {
				$_ampid_list = explode( ",", $_ampid_val );
			}
			$_ampid_haserror = false;

			foreach( $_ampid_list as $one_amp_id ) {
				$_ampid_haserror = $_ampid_haserror || !preg_match( "/^GTM-[A-Z0-9]+$/", $one_amp_id );
			}

			if ( $_ampid_haserror && (count($_ampid_list) > 0) ) {
				add_settings_error( GTM4WP_ADMIN_GROUP, GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_INTEGRATE_AMPID . ']', __( "Invalid AMP Google Tag Manager Container ID. Valid ID format: GTM-XXXXX. Use comma without additional space (,) to enter more than one ID.", 'duracelltomi-google-tag-manager' ) );
			} else {
				$output[$optionname] = $newoptionvalue;
			}


		// integrations
		} else if ( substr($optionname, 0, 10) == "integrate-" ) {
			$output[$optionname] = (boolean) $newoptionvalue;

		// GTM code or dataLayer variable name
		} else if ( ( $optionname == GTM4WP_OPTION_GTM_CODE ) || ( $optionname == GTM4WP_OPTION_DATALAYER_NAME ) || ( $optionname == GTM4WP_OPTION_ENV_GTM_AUTH ) || ( $optionname == GTM4WP_OPTION_ENV_GTM_PREVIEW ) ) {
			$newoptionvalue = trim($newoptionvalue);

			if ( $optionname == GTM4WP_OPTION_GTM_CODE ) {
				$_gtmid_list = explode( ",", $newoptionvalue );
				$_gtmid_haserror = false;

				foreach( $_gtmid_list as $one_gtm_id ) {
				  $_gtmid_haserror = $_gtmid_haserror || !preg_match( "/^GTM-[A-Z0-9]+$/", $one_gtm_id );
				}

				if ( $_gtmid_haserror ) {
					add_settings_error( GTM4WP_ADMIN_GROUP, GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_GTM_CODE . ']', __( "Invalid Google Tag Manager ID. Valid ID format: GTM-XXXXX. Use comma without additional space (,) to enter more than one container ID.", 'duracelltomi-google-tag-manager' ) );
				} else {
					$output[$optionname] = $newoptionvalue;
				}
			} else if ( ( $optionname == GTM4WP_OPTION_DATALAYER_NAME ) && ( $newoptionvalue != "" ) && ( ! preg_match( "/^[a-zA-Z][a-zA-Z0-9_-]*$/", $newoptionvalue ) ) ) {
				add_settings_error( GTM4WP_ADMIN_GROUP, GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_DATALAYER_NAME . ']', __( "Invalid dataLayer variable name. Please start with a character from a-z or A-Z followed by characters from a-z, A-Z, 0-9 or '_' or '-'!", 'duracelltomi-google-tag-manager' ) );

			} else if ( ( $optionname == GTM4WP_OPTION_ENV_GTM_AUTH ) && ( $newoptionvalue != "" ) && ( ! preg_match( "/^[a-zA-Z0-9-_]+$/", $newoptionvalue ) ) ) {
				add_settings_error( GTM4WP_ADMIN_GROUP, GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_ENV_GTM_AUTH . ']', __( "Invalid gtm_auth environment parameter value. It should only contain letters, numbers or the '-' and '_' characters.", 'duracelltomi-google-tag-manager' ) );

			} else if ( ( $optionname == GTM4WP_OPTION_ENV_GTM_PREVIEW ) && ( $newoptionvalue != "" ) && ( ! preg_match( "/^env-[0-9]+$/", $newoptionvalue ) ) ) {
				add_settings_error( GTM4WP_ADMIN_GROUP, GTM4WP_OPTIONS . '[' . GTM4WP_OPTION_ENV_GTM_PREVIEW . ']', __( "Invalid gtm_preview environment parameter value. It should have the format 'env-NN' where NN is an integer number.", 'duracelltomi-google-tag-manager' ) );

			} else {
				$output[$optionname] = $newoptionvalue;
			}

		// GTM container code placement
		} else if ( $optionname == GTM4WP_OPTION_GTM_PLACEMENT ) {
			$output[$optionname] = (int) $newoptionvalue;
			if ( ( $output[$optionname] < 0) || ( $output[$optionname] > 3 ) ) {
				$output[$optionname] = 0;
			}

		// scroll tracking content ID
		} else if ( $optionname == GTM4WP_OPTION_SCROLLER_CONTENTID ) {
			$output[$optionname] = trim( str_replace( "#", "", $newoptionvalue ) );

		// anything else
		} else {
			switch( gettype($optionvalue)) {
				case "boolean": {
					$output[$optionname] = (boolean) $newoptionvalue;

					break;
				}

				case "integer": {
					$output[$optionname] = (int) $newoptionvalue;

					break;
				}

				default: {
					$output[$optionname] = $newoptionvalue;
				}
			} // end switch
		}
	}

	return $output;
}

function gtm4wp_admin_init() {
	global $gtm4wp_includefieldtexts, $gtm4wp_eventfieldtexts, $gtm4wp_integratefieldtexts, $gtm4wp_scrollerfieldtexts,
		$gtm4wp_blacklistfieldtexts, $gtm4wp_blacklistmfieldtexts, $gtm4wp_advancedfieldtexts;

	register_setting( GTM4WP_ADMIN_GROUP, GTM4WP_OPTIONS, "gtm4wp_sanitize_options" );

	add_settings_section(
		GTM4WP_ADMIN_GROUP_GENERAL,
		__( 'General', 'duracelltomi-google-tag-manager' ),
		'gtm4wp_admin_output_section',
		GTM4WP_ADMINSLUG
	);

	add_settings_field(
		GTM4WP_ADMIN_GROUP_GTMID,
		__( 'Google Tag Manager ID', 'duracelltomi-google-tag-manager' ),
		'gtm4wp_admin_output_field',
		GTM4WP_ADMINSLUG,
		GTM4WP_ADMIN_GROUP_GENERAL,
		array(
			"label_for" => GTM4WP_ADMIN_GROUP_GTMID,
			"description" => __( "Enter your Google Tag Manager ID here. Use comma without space (,) to enter multiple IDs.", 'duracelltomi-google-tag-manager' )
		)
	);

	add_settings_field(
		GTM4WP_ADMIN_GROUP_PLACEMENT,
		__( 'Container code placement', 'duracelltomi-google-tag-manager' ),
		'gtm4wp_admin_output_field',
		GTM4WP_ADMINSLUG,
		GTM4WP_ADMIN_GROUP_GENERAL,
		array(
			"label_for" => GTM4WP_ADMIN_GROUP_PLACEMENT,
//			"description" => __( "Select how your container code should be included in your website.<br />If you select 'Custom' you need to edit your template file and add the following line just after the opening &lt;body&gt; tag:<br /><code>&lt;?php if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) { gtm4wp_the_gtm_tag(); } ?&gt;</code>", 'duracelltomi-google-tag-manager' )
			"description" => __( "Code placement decides where to put the second, so called noscript part of the GTM container code.<br />This code is usually only executed if your visitor has for some reason disabled JavaScript.<br/>The main GTM container code will be placed into the <code>&lt;head&gt;</code> section of your webpages anyway (where it belongs to).<br />If you select 'Custom' you need to edit your template file and add the following line just after the opening <code>&lt;body&gt;</code> tag:<br /><code>&lt;?php if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) { gtm4wp_the_gtm_tag(); } ?&gt;</code>", 'duracelltomi-google-tag-manager' )
		)
	);

	add_settings_section(
		GTM4WP_ADMIN_GROUP_INCLUDES,
		__( 'Basic data', 'duracelltomi-google-tag-manager' ),
		'gtm4wp_admin_output_section',
		GTM4WP_ADMINSLUG
	);

	foreach($gtm4wp_includefieldtexts as $fieldid => $fielddata) {
		add_settings_field(
			"gtm4wp-admin-" . $fieldid . "-id",
			$fielddata["label"].'<span class="'.$fielddata["phase"].'"></span>',
			'gtm4wp_admin_output_field',
			GTM4WP_ADMINSLUG,
			GTM4WP_ADMIN_GROUP_INCLUDES,
			array(
				"label_for" => "gtm4wp-options[" . $fieldid . "]",
				"description" => $fielddata["description"],
				"optionfieldid" => $fieldid
			)
		);
	}

	add_settings_section(
		GTM4WP_ADMIN_GROUP_EVENTS,
		__( 'Events', 'duracelltomi-google-tag-manager' ),
		'gtm4wp_admin_output_section',
		GTM4WP_ADMINSLUG
	);

	foreach($gtm4wp_eventfieldtexts as $fieldid => $fielddata) {
		add_settings_field(
			"gtm4wp-admin-" . $fieldid . "-id",
			$fielddata["label"].'<span class="'.$fielddata["phase"].'"></span>',
			'gtm4wp_admin_output_field',
			GTM4WP_ADMINSLUG,
			GTM4WP_ADMIN_GROUP_EVENTS,
			array(
				"label_for" => "gtm4wp-options[" . $fieldid . "]",
				"description" => $fielddata["description"],
				"optionfieldid" => $fieldid
			)
		);
	}

	add_settings_section(
		GTM4WP_ADMIN_GROUP_SCROLLER,
		__( 'Scroll tracking', 'duracelltomi-google-tag-manager' ),
		'gtm4wp_admin_output_section',
		GTM4WP_ADMINSLUG
	);

	foreach($gtm4wp_scrollerfieldtexts as $fieldid => $fielddata) {
		add_settings_field(
			"gtm4wp-admin-" . $fieldid . "-id",
			$fielddata["label"].'<span class="'.$fielddata["phase"].'"></span>',
			'gtm4wp_admin_output_field',
			GTM4WP_ADMINSLUG,
			GTM4WP_ADMIN_GROUP_SCROLLER,
			array(
				"label_for" => "gtm4wp-options[" . $fieldid . "]",
				"description" => $fielddata["description"],
				"optionfieldid" => $fieldid
			)
		);
	}

	add_settings_section(
		GTM4WP_ADMIN_GROUP_BLACKLIST,
		__( 'Blacklist tags', 'duracelltomi-google-tag-manager' ),
		'gtm4wp_admin_output_section',
		GTM4WP_ADMINSLUG
	);

	foreach($gtm4wp_blacklistfieldtexts as $fieldid => $fielddata) {
		add_settings_field(
			"gtm4wp-admin-" . $fieldid . "-id",
			$fielddata["label"].'<span class="'.$fielddata["phase"].'"></span>',
			'gtm4wp_admin_output_field',
			GTM4WP_ADMINSLUG,
			GTM4WP_ADMIN_GROUP_BLACKLIST,
			array(
				"label_for" => "gtm4wp-options[" . $fieldid . "]",
				"description" => $fielddata["description"],
				"optionfieldid" => $fieldid
			)
		);
	}

	foreach($gtm4wp_blacklistmfieldtexts as $fieldid => $fielddata) {
		add_settings_field(
			"gtm4wp-admin-" . $fieldid . "-id",
			$fielddata["label"].'<span class="'.$fielddata["phase"].'"></span>',
			'gtm4wp_admin_output_field',
			GTM4WP_ADMINSLUG,
			GTM4WP_ADMIN_GROUP_BLACKLIST,
			array(
				"label_for" => "gtm4wp-options[" . $fieldid . "]",
				"description" => $fielddata["description"],
				"optionfieldid" => $fieldid
			)
		);
	}

	add_settings_section(
		GTM4WP_ADMIN_GROUP_INTEGRATION,
		__( 'Integration', 'duracelltomi-google-tag-manager' ),
		'gtm4wp_admin_output_section',
		GTM4WP_ADMINSLUG
	);

	foreach($gtm4wp_integratefieldtexts as $fieldid => $fielddata) {
		add_settings_field(
			"gtm4wp-admin-" . $fieldid . "-id",
			$fielddata["label"].'<span class="'.$fielddata["phase"].'"></span>',
			'gtm4wp_admin_output_field',
			GTM4WP_ADMINSLUG,
			GTM4WP_ADMIN_GROUP_INTEGRATION,
			array(
				"label_for" => "gtm4wp-options[" . $fieldid . "]",
				"description" => $fielddata["description"],
				"optionfieldid" => $fieldid,
				"plugintocheck" => isset( $fielddata["plugintocheck"] ) ? $fielddata["plugintocheck"] : ""
			)
		);
	}

	add_settings_section(
		GTM4WP_ADMIN_GROUP_ADVANCED,
		__( 'Advanced', 'duracelltomi-google-tag-manager' ),
		'gtm4wp_admin_output_section',
		GTM4WP_ADMINSLUG
	);

	foreach($gtm4wp_advancedfieldtexts as $fieldid => $fielddata) {
		add_settings_field(
			"gtm4wp-admin-" . $fieldid . "-id",
			$fielddata["label"].'<span class="'.$fielddata["phase"].'"></span>',
			'gtm4wp_admin_output_field',
			GTM4WP_ADMINSLUG,
			GTM4WP_ADMIN_GROUP_ADVANCED,
			array(
				"label_for" => "gtm4wp-options[" . $fieldid . "]",
				"description" => $fielddata["description"],
				"optionfieldid" => $fieldid,
				"plugintocheck" => isset( $fielddata["plugintocheck"] ) ? $fielddata["plugintocheck"] : ""
			)
		);
	}

	add_settings_section(
		GTM4WP_ADMIN_GROUP_CREDITS,
		__( 'Credits', 'duracelltomi-google-tag-manager' ),
		'gtm4wp_admin_output_section',
		GTM4WP_ADMINSLUG
	);

	add_settings_field(
		GTM4WP_ADMIN_GROUP_INFO,
		__( 'Author', 'duracelltomi-google-tag-manager' ),
		'gtm4wp_admin_output_field',
		GTM4WP_ADMINSLUG,
		GTM4WP_ADMIN_GROUP_CREDITS,
		array(
			"label_for" => GTM4WP_ADMIN_GROUP_INFO,
			"description" => '<strong>Thomas Geiger</strong><br />
			                  Website: <a href="https://gtm4wp.com/" target="_blank">gtm4wp.com</a><br />
			                  <a href="https://www.linkedin.com/in/duracelltomi" target="_blank">Me on LinkedIn</a><br />
			                  <a href="http://www.linkedin.com/company/jabjab-online-marketing-ltd-" target="_blank">JabJab Online Marketing on LinkedIn</a>'
		)
	);

	// apply oembed code changes on the admin as well since the oembed call on the admin is cached by WordPress into a transient
	// that is applied on the frontend later
	require_once( dirname( __FILE__ ) . "/../integration/youtube.php" );
	require_once( dirname( __FILE__ ) . "/../integration/vimeo.php" );
	require_once( dirname( __FILE__ ) . "/../integration/soundcloud.php" );
}

function gtm4wp_show_admin_page() {
	global $gtp4wp_plugin_url;
?>
<div class="wrap">
	<div id="gtm4wp-icon" class="icon32" style="background-image: url(<?php echo $gtp4wp_plugin_url; ?>admin/images/tag_manager-32.png);"><br /></div>
	<h2><?php _e( 'Google Tag Manager for WordPress options', 'duracelltomi-google-tag-manager' ); ?></h2>
	<form action="options.php" method="post">
<?php settings_fields( GTM4WP_ADMIN_GROUP ); ?>
<?php do_settings_sections( GTM4WP_ADMINSLUG ); ?>
<?php submit_button(); ?>

	</form>
</div>
<?php
}

function gtm4wp_add_admin_page() {
	add_options_page(
		__( 'Google Tag Manager for WordPress settings', 'duracelltomi-google-tag-manager' ),
		__( 'Google Tag Manager', 'duracelltomi-google-tag-manager' ),
		'manage_options',
		GTM4WP_ADMINSLUG,
		'gtm4wp_show_admin_page'
	);
}

function gtm4wp_add_admin_js($hook) {
	global $gtp4wp_plugin_url;

	if ( $hook == "settings_page_" . GTM4WP_ADMINSLUG ) {
		wp_register_script( "admin-subtabs", $gtp4wp_plugin_url . "js/admin-subtabs.js", array(), GTM4WP_VERSION );

		$subtabtexts = array(
			"posttabtitle" => __( "Posts" , 'duracelltomi-google-tag-manager' ),
			"searchtabtitle" => __( "Search" , 'duracelltomi-google-tag-manager' ),
			"visitortabtitle" => __( "Visitors" , 'duracelltomi-google-tag-manager' ),
			"adwordstabtitle" => __( "AdWords" , 'duracelltomi-google-tag-manager' ),
			"browsertabtitle" => __( "Browser/OS/Device" , 'duracelltomi-google-tag-manager' ),
			"blocktagstabtitle" => __( "Blacklist tags" , 'duracelltomi-google-tag-manager' ),
			"blockmacrostabtitle" => __( "Blacklist macros" , 'duracelltomi-google-tag-manager' ),
			"wpcf7tabtitle" => __( "Contact Form 7" , 'duracelltomi-google-tag-manager' ),
			"wctabtitle" => __( "WooCommerce" , 'duracelltomi-google-tag-manager' ),
			"gotabtitle" => __( "Google Optimize" , 'duracelltomi-google-tag-manager' ),
			"amptabtitle" => __( "Accelerated Mobile Pages" , 'duracelltomi-google-tag-manager' ),
			"weathertabtitle" => __( "Weather data" , 'duracelltomi-google-tag-manager' ),
			"generaleventstabtitle" => __( "General events" , 'duracelltomi-google-tag-manager' ),
			"mediaeventstabtitle" => __( "Media events" , 'duracelltomi-google-tag-manager' ),
			"depecratedeventstabtitle" => __( "Deprecated" , 'duracelltomi-google-tag-manager' ),
			"sitetabtitle" => __( "Site" , 'duracelltomi-google-tag-manager' ),
			"misctabtitle" => __( "Misc" , 'duracelltomi-google-tag-manager' )
		);
		wp_localize_script( "admin-subtabs", 'gtm4wp', $subtabtexts );

		wp_enqueue_script( "admin-subtabs", array(), GTM4WP_VERSION );
		wp_enqueue_script( "admin-tabcreator", $gtp4wp_plugin_url . "js/admin-tabcreator.js", array( "jquery-core" ), GTM4WP_VERSION );

		wp_enqueue_style( "gtm4wp-validate", $gtp4wp_plugin_url . "css/admin-gtm4wp.css", array(), GTM4WP_VERSION );
	}
}

function gtm4wp_admin_head() {
	echo '
<style type="text/css">
	.gtmid_validation_error,
	.goid_validation_error,
	.goid_ga_validation_error,
	.ampid_validation_error,
	.datalayername_validation_error,
	.gtmauth_validation_error,
	.gtmpreview_validation_error {
		display: none;
		color: #c00;
		font-weight: bold;
	}
</style>
<script type="text/javascript">
	jQuery(function() {
		jQuery( "#gtm4wp-options\\\\[gtm-code\\\\]" )
			.bind( "blur", function() {
				var gtmid_regex = /^GTM-[A-Z0-9]+$/;
				var gtmid_list = jQuery( this ).val().trim().split( "," );

				var gtmid_haserror = false;
				for( var i=0; i<gtmid_list.length; i++ ) {
					gtmid_haserror = gtmid_haserror || !gtmid_regex.test( gtmid_list[ i ] );
				}

				if ( gtmid_haserror ) {
					jQuery( ".gtmid_validation_error" )
						.show();
				} else {
					jQuery( ".gtmid_validation_error" )
						.hide();
				}
			});

		jQuery( "#gtm4wp-options\\\\[integrate-google-optimize-idlist\\\\]" )
			.bind( "blur", function() {
				var goid_regex = /^GTM-[A-Z0-9]+$/;
				var goid_val  = jQuery( this ).val().trim();
				if ( "" == goid_val ) {
					goid_list = [];
				} else {
					var goid_list = goid_val.split( "," );
				}

				var goid_haserror = false;
				for( var i=0; i<goid_list.length; i++ ) {
					goid_haserror = goid_haserror || !goid_regex.test( goid_list[ i ] );
				}

				if ( goid_haserror && (goid_list.length > 0) ) {
					jQuery( ".goid_validation_error" )
						.show();
				} else {
					jQuery( ".goid_validation_error" )
						.hide();
				}
			});

		jQuery( "#gtm4wp-options\\\\[integrate-google-optimize-gaid\\\\]" )
			.bind( "blur", function() {
				var gogaid_regex = /^UA-[0-9]+-[0-9]+$/;
				var gogaid_val  = jQuery( this ).val().trim();
				if ( "" == gogaid_val ) {
					gogaid_list = [];
				} else {
					var gogaid_list = gogaid_val.split( "," );
				}

				var gogaid_haserror = false;
				for( var i=0; i<gogaid_list.length; i++ ) {
					gogaid_haserror = gogaid_haserror || !gogaid_regex.test( gogaid_list[ i ] );
				}

				if ( gogaid_haserror && (gogaid_list.length > 0) ) {
					jQuery( ".goid_ga_validation_error" )
						.show();
				} else {
					jQuery( ".goid_ga_validation_error" )
						.hide();
				}
			});

		jQuery( "#gtm4wp-options\\\\[integrate-amp-gtm\\\\]" )
			.bind( "blur", function() {
				var ampid_regex = /^GTM-[A-Z0-9]+$/;
				var ampid_val  = jQuery( this ).val().trim();
				if ( "" == ampid_val ) {
					ampid_list = [];
				} else {
					var ampid_list = ampid_val.split( "," );
				}

				var ampid_haserror = false;
				for( var i=0; i<ampid_list.length; i++ ) {
					ampid_haserror = ampid_haserror || !ampid_regex.test( gogaid_list[ i ] );
				}

				if ( ampid_haserror && (ampid_list.length > 0) ) {
					jQuery( ".ampid_validation_error" )
						.show();
				} else {
					jQuery( ".ampid_validation_error" )
						.hide();
				}
			});

		jQuery( "#gtm4wp-options\\\\[gtm-datalayer-variable-name\\\\]" )
			.bind( "blur", function() {
				var currentval = jQuery( this ).val();

				jQuery( ".datalayername_validation_error" )
					.hide();

				if ( currentval != "" ) {
					// I know this is not the exact definition for a variable name but I think other kind of variable names should not be used.
					var gtmvarname_regex = /^[a-zA-Z][a-zA-Z0-9_-]*$/;
					if ( ! gtmvarname_regex.test( currentval ) ) {
						jQuery( ".datalayername_validation_error" )
							.show();
					}
				}
			});

		jQuery( "#gtm4wp-options\\\\[gtm-env-gtm-auth\\\\]" )
			.bind( "blur", function() {
				var currentval = jQuery( this ).val();

				jQuery( ".gtmauth_validation_error" )
					.hide();

				if ( currentval != "" ) {
					var gtmauth_regex = /^[a-zA-Z0-9-_]+$/;
					if ( ! gtmauth_regex.test( currentval ) ) {
						jQuery( ".gtmauth_validation_error" )
							.show();
					}
				}
			});

		jQuery( "#gtm4wp-options\\\\[gtm-env-gtm-preview\\\\]" )
			.bind( "blur", function() {
				var currentval = jQuery( this ).val();

				jQuery( ".gtmpreview_validation_error" )
					.hide();

				if ( currentval != "" ) {
					var gtmpreview_regex = /^env-[0-9]+$/;
					if ( ! gtmpreview_regex.test( currentval ) ) {
						jQuery( ".gtmpreview_validation_error" )
							.show();
					}
				}
			});

		jQuery( document )
			.on( "click", ".gtm4wp-notice .notice-dismiss", function( e ) {
				jQuery.post(ajaxurl, {
					action: "gtm4wp_dismiss_notice",
					noticeid: jQuery( this ).closest(".gtm4wp-notice")
						.attr( "data-href" )
						.substring( 1 )
				});
			});
	});
</script>';
}

function gtm4wp_show_warning() {
	global $gtm4wp_options, $gtp4wp_plugin_url, $gtm4wp_integratefieldtexts, $current_user,
		$gtm4wp_def_user_notices_dismisses;

	$woo_plugin_active = is_plugin_active( $gtm4wp_integratefieldtexts[ GTM4WP_OPTION_INTEGRATE_WCTRACKCLASSICEC ][ "plugintocheck" ] );
	if ( $woo_plugin_active ) {
		$woo = WC();
	} else {
		$woo = NULL;
	}

	$gtm4wp_user_notices_dismisses = get_user_meta( $current_user->ID, GTM4WP_USER_NOTICES_KEY, true );
	if ( $gtm4wp_user_notices_dismisses === "" ) {
		$gtm4wp_user_notices_dismisses = $gtm4wp_def_user_notices_dismisses;
	} else {
		$gtm4wp_user_notices_dismisses = @unserialize( $gtm4wp_user_notices_dismisses );
		if ( false === $gtm4wp_user_notices_dismisses ) {
			$gtm4wp_user_notices_dismisses = array();
		}
	}
	$gtm4wp_user_notices_dismisses = array_merge( $gtm4wp_def_user_notices_dismisses, $gtm4wp_user_notices_dismisses );

	if ( ( trim( $gtm4wp_options[GTM4WP_OPTION_GTM_CODE] ) == "" ) && ( false === $gtm4wp_user_notices_dismisses["enter-gtm-code"] ) ) {
		echo '<div class="gtm4wp-notice notice notice-error is-dismissible" data-href="?enter-gtm-code"><p><strong>' . sprintf( __( 'To start using Google Tag Manager for WordPress, please <a href="%s">enter your GTM ID</a>', 'duracelltomi-google-tag-manager' ), "options-general.php?page=" . GTM4WP_ADMINSLUG ) . '</strong></p></div>';
	}

	if ((
		("" != $gtm4wp_options[GTM4WP_OPTION_ENV_GTM_AUTH]) && ("" == $gtm4wp_options[GTM4WP_OPTION_ENV_GTM_PREVIEW])
	) || (
		("" == $gtm4wp_options[GTM4WP_OPTION_ENV_GTM_AUTH]) && ("" != $gtm4wp_options[GTM4WP_OPTION_ENV_GTM_PREVIEW])
	)) {
		echo '<div class="gtm4wp-notice notice notice-error" data-href="?incomplete-gtm-env-config"><p><strong>' . sprintf( __( 'Incomplete Google Tag Manager environment configuration: either gtm_preview or gtm_auth parameter value is missing!', 'duracelltomi-google-tag-manager' ), "options-general.php?page=" . GTM4WP_ADMINSLUG ) . '</strong></p></div>';
	}

	if ( ( false === $gtm4wp_user_notices_dismisses["wc-ga-plugin-warning"] ) || ( false === $gtm4wp_user_notices_dismisses["wc-gayoast-plugin-warning"] ) ) {
		$is_wc_active = $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKCLASSICEC ] ||
				$gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ||
				$gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ];

		if ( ( false === $gtm4wp_user_notices_dismisses["wc-ga-plugin-warning"] ) && $is_wc_active && $woo && ( version_compare( $woo->version, "2.1" ) < 0 ) ) {
			$woo_ga_options = get_option( "woocommerce_google_analytics_settings" );
			if ( $woo_ga_options ) {
				if ( "" != $woo_ga_options["ga_id"] ) {
					echo '<div class="gtm4wp-notice notice notice-warning is-dismissible" data-href="?wc-ga-plugin-warning"><p><strong>' . __( 'Notice: you should disable Google Analytics tracking <a href="admin.php?page=wc-settings&tab=integration&section=google_analytics">in WooCommerce settings</a> by leaving Google Analytics ID field empty if you are using Google Analytics tags inside Google Tag Manager!', 'duracelltomi-google-tag-manager' ) . '</strong></p></div>';
				}
			}
		}

		if ( ( false === $gtm4wp_user_notices_dismisses["wc-ga-plugin-warning"] ) && $is_wc_active && is_plugin_active( "woocommerce-google-analytics-integration/woocommerce-google-analytics-integration.php" ) ) {
			echo '<div class="gtm4wp-notice notice notice-warning is-dismissible" data-href="?wc-ga-plugin-warning"><p><strong>' . __( 'Notice: you should deactivate the plugin "WooCommerce Google Analytics Integration" if you are using Google Analytics tags inside Google Tag Manager!', 'duracelltomi-google-tag-manager' ) . '</strong></p></div>';
		}

		if ( ( false === $gtm4wp_user_notices_dismisses["wc-gayoast-plugin-warning"] ) && $is_wc_active && is_plugin_active( "google-analytics-for-wordpress/googleanalytics.php" ) ) {
			echo '<div class="gtm4wp-notice notice notice-warning is-dismissible" data-href="?wc-gayoast-plugin-warning"><p><strong>' . __( 'Notice: you should deactivate the plugin "Google Analytics for WordPress by MonsterInsights" if you are using Google Analytics tags inside Google Tag Manager!', 'duracelltomi-google-tag-manager' ) . '</strong></p></div>';
		}
	}

	if ( ( false === $gtm4wp_user_notices_dismisses["php56-warning"] ) && ( version_compare( PHP_VERSION, '5.6.0' ) < 0 ) ) {
		echo '<div class="gtm4wp-notice notice notice-warning is-dismissible" data-href="?php56-warning"><p><strong>' . __( 'Warning: You are using an outdated version of PHP (v' . PHP_VERSION . ') that can cause issues with the plugin Google Tag Manager for WordPress. Please consider to upgrade your PHP.', 'duracelltomi-google-tag-manager' ) . '</strong></p></div>';
	}

	if ( ( false === $gtm4wp_user_notices_dismisses["woo2x-warning"] ) && ( $woo ) && ( version_compare( $woo->version, "3.0", "<" ) ) ) {
		echo '<div class="gtm4wp-notice notice notice-warning is-dismissible" data-href="?woo2x-warning"><p><strong>' . __( 'Warning: You are using an outdated version of WooCommerce (v' . $woo->version . '). Google Tag Manager for WordPress will drop support for this version in the near future. Please consider to upgrade.', 'duracelltomi-google-tag-manager' ) . '</strong></p></div>';
	}

	if ( ( false === $gtm4wp_user_notices_dismisses["deprecated-warning"] ) && (
		( $gtm4wp_options[ GTM4WP_OPTION_EVENTS_SOCIAL ] ) ||
		( $gtm4wp_options[ GTM4WP_OPTION_EVENTS_OUTBOUND ] ) ||
		( $gtm4wp_options[ GTM4WP_OPTION_EVENTS_OUTBOUND ] ) ||
		( $gtm4wp_options[ GTM4WP_OPTION_EVENTS_OUTBOUND ] )
	)) {
		$deprecated_list = array(
			"Social actions",
			"Outbound link click events",
			"Download click events",
			"Email click events"
		);
		echo '<div class="gtm4wp-notice notice notice-warning is-dismissible" data-href="?deprecated-warning"><p><strong>' . __( 'Warning: Some features of Google Tag Manager for WordPress are deprecated and will be removed in the next version: <ul><li>' . implode( '</li><li>', $deprecated_list ) . '</li></ul>', 'duracelltomi-google-tag-manager' ) . '</strong></p></div>';
	}
}

function gtm4wp_dismiss_notice() {
	global $gtm4wp_def_user_notices_dismisses, $current_user;

	$gtm4wp_user_notices_dismisses = get_user_meta( $current_user->ID, GTM4WP_USER_NOTICES_KEY, true );
	if ( $gtm4wp_user_notices_dismisses === "" ) {
		$gtm4wp_user_notices_dismisses = $gtm4wp_def_user_notices_dismisses;
	} else {
		$gtm4wp_user_notices_dismisses = @unserialize( $gtm4wp_user_notices_dismisses );
		if ( false === $gtm4wp_user_notices_dismisses ) {
			$gtm4wp_user_notices_dismisses = array();
		}
	}
	$gtm4wp_user_notices_dismisses = array_merge( $gtm4wp_def_user_notices_dismisses, $gtm4wp_user_notices_dismisses );

	$noticeid = trim( basename( $_POST["noticeid"] ) );
	if ( array_key_exists( $noticeid, $gtm4wp_user_notices_dismisses ) ) {
		$gtm4wp_user_notices_dismisses[ $noticeid ] = true;
		update_user_meta( $current_user->ID, GTM4WP_USER_NOTICES_KEY, serialize( $gtm4wp_user_notices_dismisses ) );
	}
}

function gtm4wp_add_plugin_action_links( $links, $file ) {
	global $gtp4wp_plugin_basename;

	if ( $file != $gtp4wp_plugin_basename )
		return $links;

	$settings_link = '<a href="' . menu_page_url( GTM4WP_ADMINSLUG, false ) . '">' . esc_html( __( 'Settings' ) ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}

function gtm4wp_show_upgrade_notification( $current_plugin_metadata, $new_plugin_metadata ) {
	if ( isset( $new_plugin_metadata->upgrade_notice ) && strlen( trim( $new_plugin_metadata->upgrade_notice ) ) > 0 ) {
		echo '<p style="background-color: #d54e21; padding: 10px; color: #f9f9f9; margin-top: 10px"><strong>Important Upgrade Notice:</strong> ';
		echo esc_html($new_plugin_metadata->upgrade_notice), '</p>';
	}
}

add_action( 'admin_init', 'gtm4wp_admin_init' );
add_action( 'admin_menu', 'gtm4wp_add_admin_page' );
add_action( 'admin_enqueue_scripts', 'gtm4wp_add_admin_js' );
add_action( 'admin_notices', 'gtm4wp_show_warning' );
add_action( 'admin_head', 'gtm4wp_admin_head' );
add_filter( 'plugin_action_links', 'gtm4wp_add_plugin_action_links', 10, 2 );
add_action( 'wp_ajax_gtm4wp_dismiss_notice', 'gtm4wp_dismiss_notice' );
add_action( 'in_plugin_update_message-duracelltomi-google-tag-manager-for-wordpress/duracelltomi-google-tag-manager-for-wordpress.php', 'gtm4wp_show_upgrade_notification', 10, 2 );