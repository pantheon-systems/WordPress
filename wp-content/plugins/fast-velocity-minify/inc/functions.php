<?php

# handle better utf-8 and unicode encoding
if(function_exists('mb_internal_encoding')) { mb_internal_encoding('UTF-8'); }

# must have
@ini_set('pcre.backtrack_limit',5000000); 
@ini_set('pcre.recursion_limit',5000000);

# PHP Minify [1.3.60] (must be defined on the outer scope)
# https://github.com/matthiasmullie/minify
$path = $plugindir . 'libs/matthiasmullie';
require_once $path . '/minify/src/Minify.php';
require_once $path . '/minify/src/CSS.php';
require_once $path . '/minify/src/JS.php';
require_once $path . '/minify/src/Exception.php';
require_once $path . '/minify/src/Exceptions/BasicException.php';
require_once $path . '/minify/src/Exceptions/FileImportException.php';
require_once $path . '/minify/src/Exceptions/IOException.php';
require_once $path . '/path-converter/src/ConverterInterface.php';
require_once $path . '/path-converter/src/Converter.php';
use MatthiasMullie\Minify;

# use HTML minification
require_once ($plugindir . 'libs/mrclay/HTML.php');

# get list of allowed google fonts from the API (847 fonts on 2018-12-13)
# https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha
# https://www.xcartmods.co.uk/google-fonts-list.php
$gfontswhitelist = array('ABeeZee','Abel','Abhaya Libre','Abril Fatface','Aclonica','Acme','Actor','Adamina','Advent Pro','Aguafina Script','Akronim','Aladin','Aldrich','Alef','Alegreya','Alegreya SC','Alegreya Sans','Alegreya Sans SC','Alex Brush','Alfa Slab One','Alice','Alike','Alike Angular','Allan','Allerta','Allerta Stencil','Allura','Almendra','Almendra Display','Almendra SC','Amarante','Amaranth','Amatic SC','Amatica SC','Amethysta','Amiko','Amiri','Amita','Anaheim','Andada','Andika','Angkor','Annie Use Your Telescope','Anonymous Pro','Antic','Antic Didone','Antic Slab','Anton','Arapey','Arbutus','Arbutus Slab','Architects Daughter','Archivo','Archivo Black','Archivo Narrow','Aref Ruqaa','Arima Madurai','Arimo','Arizonia','Armata','Arsenal','Artifika','Arvo','Arya','Asap','Asap Condensed','Asar','Asset','Assistant','Astloch','Asul','Athiti','Atma','Atomic Age','Aubrey','Audiowide','Autour One','Average','Average Sans','Averia Gruesa Libre','Averia Libre','Averia Sans Libre','Averia Serif Libre','Bad Script','Bahiana','Baloo','Baloo Bhai','Baloo Bhaijaan','Baloo Bhaina','Baloo Chettan','Baloo Da','Baloo Paaji','Baloo Tamma','Baloo Tammudu','Baloo Thambi','Balthazar','Bangers','Barrio','Basic','Battambang','Baumans','Bayon','Belgrano','Bellefair','Belleza','BenchNine','Bentham','Berkshire Swash','Bevan','Bigelow Rules','Bigshot One','Bilbo','Bilbo Swash Caps','BioRhyme','BioRhyme Expanded','Biryani','Bitter','Black Ops One','Bokor','Bonbon','Boogaloo','Bowlby One','Bowlby One SC','Brawler','Bree Serif','Bubblegum Sans','Bubbler One','Buda','Buenard','Bungee','Bungee Hairline','Bungee Inline','Bungee Outline','Bungee Shade','Butcherman','Butterfly Kids','Cabin','Cabin Condensed','Cabin Sketch','Caesar Dressing','Cagliostro','Cairo','Calligraffitti','Cambay','Cambo','Candal','Cantarell','Cantata One','Cantora One','Capriola','Cardo','Carme','Carrois Gothic','Carrois Gothic SC','Carter One','Catamaran','Caudex','Caveat','Caveat Brush','Cedarville Cursive','Ceviche One','Changa','Changa One','Chango','Chathura','Chau Philomene One','Chela One','Chelsea Market','Chenla','Cherry Cream Soda','Cherry Swash','Chewy','Chicle','Chivo','Chonburi','Cinzel','Cinzel Decorative','Clicker Script','Coda','Coda Caption','Codystar','Coiny','Combo','Comfortaa','Coming Soon','Concert One','Condiment','Content','Contrail One','Convergence','Cookie','Copse','Corben','Cormorant','Cormorant Garamond','Cormorant Infant','Cormorant SC','Cormorant Unicase','Cormorant Upright','Courgette','Cousine','Coustard','Covered By Your Grace','Crafty Girls','Creepster','Crete Round','Crimson Text','Croissant One','Crushed','Cuprum','Cutive','Cutive Mono','Damion','Dancing Script','Dangrek','David Libre','Dawning of a New Day','Days One','Dekko','Delius','Delius Swash Caps','Delius Unicase','Della Respira','Denk One','Devonshire','Dhurjati','Didact Gothic','Diplomata','Diplomata SC','Domine','Donegal One','Doppio One','Dorsa','Dosis','Dr Sugiyama','Droid Sans','Droid Sans Mono','Droid Serif','Duru Sans','Dynalight','EB Garamond','Eagle Lake','Eater','Economica','Eczar','El Messiri','Electrolize','Elsie','Elsie Swash Caps','Emblema One','Emilys Candy','Encode Sans','Encode Sans Condensed','Encode Sans Expanded','Encode Sans Semi Condensed','Encode Sans Semi Expanded','Engagement','Englebert','Enriqueta','Erica One','Esteban','Euphoria Script','Ewert','Exo','Exo 2','Expletus Sans','Fanwood Text','Farsan','Fascinate','Fascinate Inline','Faster One','Fasthand','Fauna One','Faustina','Federant','Federo','Felipa','Fenix','Finger Paint','Fira Mono','Fira Sans','Fira Sans Condensed','Fira Sans Extra Condensed','Fjalla One','Fjord One','Flamenco','Flavors','Fondamento','Fontdiner Swanky','Forum','Francois One','Frank Ruhl Libre','Freckle Face','Fredericka the Great','Fredoka One','Freehand','Fresca','Frijole','Fruktur','Fugaz One','GFS Didot','GFS Neohellenic','Gabriela','Gafata','Galada','Galdeano','Galindo','Gentium Basic','Gentium Book Basic','Geo','Geostar','Geostar Fill','Germania One','Gidugu','Gilda Display','Give You Glory','Glass Antiqua','Glegoo','Gloria Hallelujah','Goblin One','Gochi Hand','Gorditas','Goudy Bookletter 1911','Graduate','Grand Hotel','Gravitas One','Great Vibes','Griffy','Gruppo','Gudea','Gurajada','Habibi','Halant','Hammersmith One','Hanalei','Hanalei Fill','Handlee','Hanuman','Happy Monkey','Harmattan','Headland One','Heebo','Henny Penny','Herr Von Muellerhoff','Hind','Hind Guntur','Hind Madurai','Hind Siliguri','Hind Vadodara','Holtwood One SC','Homemade Apple','Homenaje','IM Fell DW Pica','IM Fell DW Pica SC','IM Fell Double Pica','IM Fell Double Pica SC','IM Fell English','IM Fell English SC','IM Fell French Canon','IM Fell French Canon SC','IM Fell Great Primer','IM Fell Great Primer SC','Iceberg','Iceland','Imprima','Inconsolata','Inder','Indie Flower','Inika','Inknut Antiqua','Irish Grover','Istok Web','Italiana','Italianno','Itim','Jacques Francois','Jacques Francois Shadow','Jaldi','Jim Nightshade','Jockey One','Jolly Lodger','Jomhuria','Josefin Sans','Josefin Slab','Joti One','Judson','Julee','Julius Sans One','Junge','Jura','Just Another Hand','Just Me Again Down Here','Kadwa','Kalam','Kameron','Kanit','Kantumruy','Karla','Karma','Katibeh','Kaushan Script','Kavivanar','Kavoon','Kdam Thmor','Keania One','Kelly Slab','Kenia','Khand','Khmer','Khula','Kite One','Knewave','Kotta One','Koulen','Kranky','Kreon','Kristi','Krona One','Kumar One','Kumar One Outline','Kurale','La Belle Aurore','Laila','Lakki Reddy','Lalezar','Lancelot','Lateef','Lato','League Script','Leckerli One','Ledger','Lekton','Lemon','Lemonada','Libre Barcode 128','Libre Barcode 128 Text','Libre Barcode 39','Libre Barcode 39 Extended','Libre Barcode 39 Extended Text','Libre Barcode 39 Text','Libre Baskerville','Libre Franklin','Life Savers','Lilita One','Lily Script One','Limelight','Linden Hill','Lobster','Lobster Two','Londrina Outline','Londrina Shadow','Londrina Sketch','Londrina Solid','Lora','Love Ya Like A Sister','Loved by the King','Lovers Quarrel','Luckiest Guy','Lusitana','Lustria','Macondo','Macondo Swash Caps','Mada','Magra','Maiden Orange','Maitree','Mako','Mallanna','Mandali','Manuale','Marcellus','Marcellus SC','Marck Script','Margarine','Marko One','Marmelad','Martel','Martel Sans','Marvel','Mate','Mate SC','Maven Pro','McLaren','Meddon','MedievalSharp','Medula One','Meera Inimai','Megrim','Meie Script','Merienda','Merienda One','Merriweather','Merriweather Sans','Metal','Metal Mania','Metamorphous','Metrophobic','Michroma','Milonga','Miltonian','Miltonian Tattoo','Miniver','Miriam Libre','Mirza','Miss Fajardose','Mitr','Modak','Modern Antiqua','Mogra','Molengo','Molle','Monda','Monofett','Monoton','Monsieur La Doulaise','Montaga','Montez','Montserrat','Montserrat Alternates','Montserrat Subrayada','Moul','Moulpali','Mountains of Christmas','Mouse Memoirs','Mr Bedfort','Mr Dafoe','Mr De Haviland','Mrs Saint Delafield','Mrs Sheppards','Mukta','Mukta Mahee','Mukta Malar','Mukta Vaani','Muli','Mystery Quest','NTR','Neucha','Neuton','New Rocker','News Cycle','Niconne','Nixie One','Nobile','Nokora','Norican','Nosifer','Nothing You Could Do','Noticia Text','Noto Sans','Noto Serif','Nova Cut','Nova Flat','Nova Mono','Nova Oval','Nova Round','Nova Script','Nova Slim','Nova Square','Numans','Nunito','Nunito Sans','Odor Mean Chey','Offside','Old Standard TT','Oldenburg','Oleo Script','Oleo Script Swash Caps','Open Sans','Open Sans Condensed','Oranienbaum','Orbitron','Oregano','Orienta','Original Surfer','Oswald','Over the Rainbow','Overlock','Overlock SC','Overpass','Overpass Mono','Ovo','Oxygen','Oxygen Mono','PT Mono','PT Sans','PT Sans Caption','PT Sans Narrow','PT Serif','PT Serif Caption','Pacifico','Padauk','Palanquin','Palanquin Dark','Pangolin','Paprika','Parisienne','Passero One','Passion One','Pathway Gothic One','Patrick Hand','Patrick Hand SC','Pattaya','Patua One','Pavanam','Paytone One','Peddana','Peralta','Permanent Marker','Petit Formal Script','Petrona','Philosopher','Piedra','Pinyon Script','Pirata One','Plaster','Play','Playball','Playfair Display','Playfair Display SC','Podkova','Poiret One','Poller One','Poly','Pompiere','Pontano Sans','Poppins','Port Lligat Sans','Port Lligat Slab','Pragati Narrow','Prata','Preahvihear','Press Start 2P','Pridi','Princess Sofia','Prociono','Prompt','Prosto One','Proza Libre','Puritan','Purple Purse','Quando','Quantico','Quattrocento','Quattrocento Sans','Questrial','Quicksand','Quintessential','Qwigley','Racing Sans One','Radley','Rajdhani','Rakkas','Raleway','Raleway Dots','Ramabhadra','Ramaraja','Rambla','Rammetto One','Ranchers','Rancho','Ranga','Rasa','Rationale','Ravi Prakash','Redressed','Reem Kufi','Reenie Beanie','Revalia','Rhodium Libre','Ribeye','Ribeye Marrow','Righteous','Risque','Roboto','Roboto Condensed','Roboto Mono','Roboto Slab','Rochester','Rock Salt','Rokkitt','Romanesco','Ropa Sans','Rosario','Rosarivo','Rouge Script','Rozha One','Rubik','Rubik Mono One','Ruda','Rufina','Ruge Boogie','Ruluko','Rum Raisin','Ruslan Display','Russo One','Ruthie','Rye','Sacramento','Sahitya','Sail','Saira','Saira Condensed','Saira Extra Condensed','Saira Semi Condensed','Salsa','Sanchez','Sancreek','Sansita','Sarala','Sarina','Sarpanch','Satisfy','Scada','Scheherazade','Schoolbell','Scope One','Seaweed Script','Secular One','Sedgwick Ave','Sedgwick Ave Display','Sevillana','Seymour One','Shadows Into Light','Shadows Into Light Two','Shanti','Share','Share Tech','Share Tech Mono','Shojumaru','Short Stack','Shrikhand','Siemreap','Sigmar One','Signika','Signika Negative','Simonetta','Sintony','Sirin Stencil','Six Caps','Skranji','Slabo 13px','Slabo 27px','Slackey','Smokum','Smythe','Sniglet','Snippet','Snowburst One','Sofadi One','Sofia','Sonsie One','Sorts Mill Goudy','Source Code Pro','Source Sans Pro','Source Serif Pro','Space Mono','Special Elite','Spectral','Spicy Rice','Spinnaker','Spirax','Squada One','Sree Krushnadevaraya','Sriracha','Stalemate','Stalinist One','Stardos Stencil','Stint Ultra Condensed','Stint Ultra Expanded','Stoke','Strait','Sue Ellen Francisco','Suez One','Sumana','Sunshiney','Supermercado One','Sura','Suranna','Suravaram','Suwannaphum','Swanky and Moo Moo','Syncopate','Tangerine','Taprom','Tauri','Taviraj','Teko','Telex','Tenali Ramakrishna','Tenor Sans','Text Me One','The Girl Next Door','Tienne','Tillana','Timmana','Tinos','Titan One','Titillium Web','Trade Winds','Trirong','Trocchi','Trochut','Trykker','Tulpen One','Ubuntu','Ubuntu Condensed','Ubuntu Mono','Ultra','Uncial Antiqua','Underdog','Unica One','UnifrakturCook','UnifrakturMaguntia','Unkempt','Unlock','Unna','VT323','Vampiro One','Varela','Varela Round','Vast Shadow','Vesper Libre','Vibur','Vidaloka','Viga','Voces','Volkhov','Vollkorn','Voltaire','Waiting for the Sunrise','Wallpoet','Walter Turncoat','Warnes','Wellfleet','Wendy One','Wire One','Work Sans','Yanone Kaffeesatz','Yantramanav','Yatra One','Yellowtail','Yeseva One','Yesteryear','Yrsa','Zeyada','Zilla Slab','Zilla Slab Highlight');


# check if php has disabled some functions by default
function fvm_function_available($func) {
	if (ini_get('safe_mode')) return false;
	$disabled = ini_get('disable_functions');
	if ($disabled) {
		$disabled = explode(',', $disabled);
		$disabled = array_map('trim', $disabled);
		return !in_array($func, $disabled);
	}
	return true;
}


# run during activation
function fastvelocity_plugin_activate() {
	
	# increment cache time
	fvm_cache_increment();
	
	# old cache purge event cron
	if (!wp_next_scheduled ('fastvelocity_purge_old_cron')) {
		wp_schedule_event(time(), 'daily', 'fastvelocity_purge_old_cron_event');
	}
	
	
	# setup defaults if no option to preserve exists
	if(get_option('fastvelocity_preserve_settings_on_uninstall') == false) {
		
		# default options to enable (1)
		$options_enable_default = array('fastvelocity_min_remove_print_mediatypes',  'fastvelocity_fvm_clean_header_one', 'fastvelocity_min_skip_google_fonts', 'fastvelocity_min_force_inline_css_footer', 'fastvelocity_min_skip_cssorder', 'fastvelocity_gfonts_method', 'fastvelocity_fontawesome_method', 'fastvelocity_min_disable_css_inline_merge');
		foreach($options_enable_default as $option) {
			update_option($option, 1, 'yes');
		}
		
		# default blacklist
		$exc = array('/html5shiv.js', '/html5shiv-printshiv.min.js', '/excanvas.js', '/avada-ie9.js', '/respond.js', '/respond.min.js', '/selectivizr.js', '/Avada/assets/css/ie.css', '/html5.js', '/IE9.js', '/fusion-ie9.js', '/vc_lte_ie9.min.css', '/old-ie.css', '/ie.css', '/vc-ie8.min.css', '/mailchimp-for-wp/assets/js/third-party/placeholders.min.js', '/assets/js/plugins/wp-enqueue/min/webfontloader.js', '/a.optnmstr.com/app/js/api.min.js', '/pixelyoursite/js/public.js', '/assets/js/wcdrip-drip.js');
		update_option('fastvelocity_min_blacklist', implode(PHP_EOL, $exc)); 
		
		# default ignore list
		$exc = array('/Avada/assets/js/main.min.js', '/woocommerce-product-search/js/product-search.js', '/includes/builder/scripts/frontend-builder-scripts.js', '/assets/js/jquery.themepunch.tools.min.js', '/js/TweenMax.min.js', '/jupiter/assets/js/min/full-scripts', '/wp-content/themes/Divi/core/admin/js/react-dom.production.min.js', '/LayerSlider/static/layerslider/js/greensock.js', '/themes/kalium/assets/js/main.min.js');
		update_option('fastvelocity_min_ignorelist', implode(PHP_EOL, $exc));
		
	}
}

# run during deactivation
function fastvelocity_plugin_deactivate() {
	
	# remove all on deactivation
	fvm_purge_all();
	fvm_purge_others();
	
	# old cache purge event cron
	if (wp_next_scheduled ('fastvelocity_purge_old_cron')) {
		$timestamp = wp_next_scheduled ('fastvelocity_purge_old_cron');
		wp_unschedule_event ($timestamp, 'fastvelocity_purge_old_cron_event');
	}
	
}

# run during uninstall
function fastvelocity_plugin_uninstall() {
	global $wpdb;
	
	# remove defaults if no option to preserve exists
	if(get_option('fastvelocity_preserve_settings_on_uninstall') == false) {
	
		# delete all fvm options
		$plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'fastvelocity_%' OR option_name LIKE 'fvm-%'" );
		if(is_array($plugin_options) && count($plugin_options) > 0) {
			foreach( $plugin_options as $option ) { delete_option( $option->option_name ); }
		}
		
		# purge all caches
		if(function_exists('fvm_purge_all_uninstall') && function_exists('fvm_purge_others')) {
			fvm_purge_all_uninstall();
			fvm_purge_others();
		}
	
	}
}



# detect external or internal scripts
function fvm_is_local_domain($src) {
$locations = array(home_url(), site_url(), network_home_url(), network_site_url());

	# cdn support
	$fvm_cdn_url = get_option('fastvelocity_min_fvm_cdn_url');
	$defer_for_pagespeed = get_option('fastvelocity_min_defer_for_pagespeed');
	$fvm_cdn_force = get_option('fastvelocity_min_fvm_cdn_force');
	
	# excluded from cdn because of https://www.chromestatus.com/feature/5718547946799104 (we use document.write to preserve render blocking)
	if(!empty($fvm_cdn_url) && ($defer_for_pagespeed != true || $fvm_cdn_force != false) ) {
		array_push($locations, $fvm_cdn_url);
	}
	
	# cleanup locations
	$locations = array_filter(array_unique($locations));

	# debug
	$debug = array('src'=>$src, 'fvm_cdn_url'=>$fvm_cdn_url, 'defer_for_pagespeed'=>$defer_for_pagespeed, 'fvm_cdn_force'=>$fvm_cdn_force, 'locations'=>$locations);
	
	
	# external or not?
	$ret = false;
	foreach ($locations as $l) { 
		$l = trim(trim(str_ireplace(array('http://', 'https://', 'www.'), '', trim($l)), '/')); 
		if (stripos($src, $l) !== false && $ret === false) { $ret = true; }
	}

# response
return $ret;
}


# functions, get hurl info
function fastvelocity_min_get_hurl($src, $wp_domain, $wp_home) {
	
# preserve empty source handles
$hurl = trim($src); if(empty($hurl)) { return $hurl; }      

# some fixes
$hurl = str_ireplace(array('&#038;', '&amp;'), '&', $hurl);

$default_protocol = get_option('fastvelocity_min_default_protocol', 'dynamic');
if($default_protocol == 'dynamic' || empty($default_protocol)) { 
if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) { $default_protocol = 'https://'; } else { $default_protocol = 'http://'; }
} else { 
$default_protocol = $default_protocol.'://'; 
}

#make sure wp_home doesn't have a forward slash
$wp_home = rtrim($wp_home, '/');

# apply some filters
if (substr($hurl, 0, 2) === "//") { $hurl = $default_protocol.ltrim($hurl, "/"); }  # protocol only
if (substr($hurl, 0, 4) === "http" && stripos($hurl, $wp_domain) === false) { return $hurl; } # return if external domain
if (substr($hurl, 0, 4) !== "http" && stripos($hurl, $wp_domain) !== false) { $hurl = $wp_home.'/'.ltrim($hurl, "/"); } # protocol + home

# prevent double forward slashes in the middle
$hurl = str_ireplace('###', '://', str_ireplace('//', '/', str_ireplace('://', '###', $hurl)));

# consider different wp-content directory
$proceed = 0; if(!empty($wp_home)) { 
	$alt_wp_content = basename($wp_home); 
	if(substr($hurl, 0, strlen($alt_wp_content)) === $alt_wp_content) { $proceed = 1; } 
}

# protocol + home for relative paths
if (substr($hurl, 0, 12) === "/wp-includes" || substr($hurl, 0, 9) === "/wp-admin" || substr($hurl, 0, 11) === "/wp-content" || $proceed == 1) { 
$hurl = $wp_home.'/'.ltrim($hurl, "/"); }

# make sure there is a protocol prefix as required
$hurl = $default_protocol.str_ireplace(array('http://', 'https://'), '', $hurl); # enforce protocol

# no query strings
if (stripos($hurl, '.js?v') !== false) { $hurl = stristr($hurl, '.js?v', true).'.js'; } # no query strings
if (stripos($hurl, '.css?v') !== false) { $hurl = stristr($hurl, '.css?v', true).'.css'; } # no query strings

# make sure there is a protocol prefix as required
$hurl = fvm_compat_urls($hurl); # enforce protocol

return $hurl;	
}


# check if it's an internal url or not
function fvm_internal_url($hurl, $wp_home, $noxtra=NULL) {
if (substr($hurl, 0, strlen($wp_home)) === $wp_home) { return true; }
if (stripos($hurl, $wp_home) !== false) { return true; }
if (isset($_SERVER['HTTP_HOST']) && stripos($hurl, preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'])) !== false) { return true; }
if (isset($_SERVER['SERVER_NAME']) && stripos($hurl, preg_replace('/:\d+$/', '', $_SERVER['SERVER_NAME'])) !== false) { return true; }
if (isset($_SERVER['SERVER_ADDR']) && stripos($hurl, preg_replace('/:\d+$/', '', $_SERVER['SERVER_ADDR'])) !== false) { return true; }

# allow specific external urls to be merged
if($noxtra === NULL) {
$merge_allowed_urls = array_map('trim', explode(PHP_EOL, get_option('fastvelocity_min_merge_allowed_urls', '')));
if(is_array($merge_allowed_urls) && strlen(implode($merge_allowed_urls)) > 0) {
	foreach ($merge_allowed_urls as $e) {
		if (stripos($hurl, $e) !== false && !empty($e)) { return true; }
	}
}
}

return false;
}


# case-insensitive in_array() wrapper
function fastvelocity_min_in_arrayi($hurl, $ignore){
	$hurl = str_ireplace(array('http://', 'https://'), '//', $hurl); # better compatibility
	$hurl = strtok(urldecode(rawurldecode($hurl)), '?'); # no query string, decode entities
	
	if (!empty($hurl) && is_array($ignore)) { 
		foreach ($ignore as $i) {
			$i = str_ireplace(array('http://', 'https://'), '//', $i); # better compatibility
			$i = strtok(urldecode(rawurldecode($i)), '?'); # no query string, decode entities
			$i = trim(trim(trim(rtrim($i, '/')), '*')); # wildcard char removal
			if (stripos($hurl, $i) !== false) { return true; } 
		} 
	}
	return false;
}


# better compatibility urls + fix w3.org NamespaceAndDTDIdentifiers
function fvm_compat_urls($code) {
	$default_protocol = get_option('fastvelocity_min_default_protocol', 'dynamic');
	if($default_protocol == 'dynamic' || empty($default_protocol)) { 
		if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) { $default_protocol = 'https://'; } else { $default_protocol = 'http://'; }
	} else { 
		$default_protocol = $default_protocol.'://'; 
	}
	$code = str_ireplace(array('http://', 'https://'), $default_protocol, $code);
	$code = str_ireplace($default_protocol.'www.w3.org', 'http://www.w3.org', $code);
	return $code;
}


# minify css string with PHP Minify
function fastvelocity_min_minify_css_string($css) {
$minifier = new Minify\CSS($css);
$minifier->setMaxImportSize(15); # [css only] embed assets up to 15 Kb (default 5Kb) - processes gif, png, jpg, jpeg, svg & woff
$min = $minifier->minify();
if($min !== false) { return fvm_compat_urls($min); }
return fvm_compat_urls($css);
}


# find if we are running windows
function fvm_server_is_windows() {
	# PHP 7.2.0+
	if(defined('PHP_OS_FAMILY')) {
		if(strtolower(PHP_OS_FAMILY) == 'windows') { return true; }
	}
	if(function_exists('php_uname')) {
		$os = @php_uname('s');
		if (stripos($os, 'Windows') !== false) { 
			return true; 
		}
	}
	return false;
}



# minify js on demand (one file at one time, for compatibility)
function fastvelocity_min_get_js($url, $js, $disable_js_minification) {
	global $fvm_debug;

# exclude minification on already minified files + jquery (because minification might break those)
$excl = array('jquery.js', '.min.js', '-min.js', '/uploads/fusion-scripts/', '/min/', '.packed.js');
foreach($excl as $e) { if (stripos(basename($url), $e) !== false) { $disable_js_minification = true; break; } }

# remove BOM
$js = fastvelocity_min_remove_utf8_bom($js); 

# minify JS
if(!$disable_js_minification) { 
	$js = fastvelocity_min_minify_js_string($js); 
} else {
	$js = fvm_compat_urls($js); 
}

# try to remove source mapping files
$filename = basename($url);
$remove = array("//# sourceMappingURL=$filename.map", "//# sourceMappingURL = $filename.map");
$js = str_ireplace($remove, '', $js);

# needed when merging js files
$js = trim($js);
if(substr($js, -1) != ';'){ $js = $js.';'; }
if($fvm_debug == true) { $js = '/* info: ' . $url . ' */' . PHP_EOL . $js; }

# return html
return $js . PHP_EOL;
}


# minify JS string with PHP Minify or YUI Compressors
function fastvelocity_min_minify_js_string($js) {
global $tmpdir, $plugindir;
	
	# PHP Minify from https://github.com/matthiasmullie/minify
	$minifier = new Minify\JS($js);
	$min = $minifier->minify();
	if($min !== false && (strlen(trim($js)) == strlen(trim($min)) || strlen(trim($min)) > 0)) { 
		return fvm_compat_urls($min);
	}
	
	# if we are here, something went  wrong and minification didn't work
	$js = "\n/*! FVM: Minification of the following section failed, so it has been merged instead. */\n".$js;
	return fvm_compat_urls($js);
}

# functions, minify html
function fastvelocity_min_minify_html($html) {
return fastvelocity_min_Minify_HTML::minify($html);
}

# functions to minify HTML
function fastvelocity_min_html_compression_finish($html) { return fastvelocity_min_minify_html($html); }
function fastvelocity_min_html_compression_start() {
	if (fastvelocity_exclude_contents() == true) { return; }
	ob_start('fastvelocity_min_html_compression_finish');
}


# remove default HTTP headers
function fastvelocity_remove_redundant_shortlink() {
	remove_action('wp_head', 'wp_shortlink_wp_head', 10);
	remove_action( 'template_redirect', 'wp_shortlink_header', 11);
}

# minify css on demand (one file at one time, for compatibility)
function fastvelocity_min_get_css($url, $css, $disable_css_minification) {
global $wp_domain, $fvm_debug;

# remove BOM
$css = fastvelocity_min_remove_utf8_bom($css); 

# fix url paths
if(!empty($url)) { 
	$css = preg_replace("/url\(\s*['\"]?(?!data:)(?!http)(?![\/'\"])(.+?)['\"]?\s*\)/ui", "url(".dirname($url)."/$1)", $css); 
} 

# remove query strings from fonts (for better seo, but add a small cache buster based on most recent updates)
$ctime = get_option('fvm-last-cache-update', '0'); # last update or zero
$css = preg_replace('/(.eot|.woff2|.woff|.ttf)+[?+](.+?)(\)|\'|\")/ui', "$1"."#".$ctime."$3", $css); # fonts cache buster

# minify CSS
if(!$disable_css_minification) { 
	$css = fastvelocity_min_minify_css_string($css); 
} else {
	$css = fvm_compat_urls($css); 
}

# cdn urls
$fvm_cdn_url = get_option('fastvelocity_min_fvm_cdn_url');
if(!empty($fvm_cdn_url)) {
	$fvm_cdn_url = trim(trim(str_ireplace(array('http://', 'https://'), '', trim($fvm_cdn_url, '/'))), '/');
	$css = str_ireplace($wp_domain, $fvm_cdn_url, $css);
}

# add css comment
$css = trim($css);
if($fvm_debug == true) { $css = '/* info: ' . $url . ' */' . PHP_EOL . trim($css); }

# return html
return $css;
}


# download and cache css and js files
function fvm_download_and_minify($hurl, $inline, $disable_minification, $type, $handle){
global $cachedir, $cachedirurl, $wp_domain, $wp_home, $wp_home_path, $fvm_debug;

# must have
if(is_null($hurl) || empty($hurl)) { return false; }
if(!in_array($type, array('js', 'css'))) { return false; }

# defaults
if($disable_minification != true) { $disable_minification = false; }
if(is_null($inline) || empty($inline)) { $inline = ''; }
$printhandle = ''; if(is_null($handle) || empty($handle)) { $handle = ''; } else { $printhandle = "[$handle]"; }

# debug request
$dreq = array('hurl'=>$hurl, 'inline'=>$inline, 'disable_minification'=>$disable_minification, 'type'=>$type, 'handle'=>$handle);

# filters and defaults
$printurl = str_ireplace(array(site_url(), home_url(), 'http:', 'https:'), '', $hurl);

	# linux servers
	if(fvm_server_is_windows() === false) {
	if (stripos($hurl, $wp_domain) !== false) {
		# default
		$f = str_ireplace(rtrim($wp_home, '/'), rtrim($wp_home_path, '/'), $hurl);
		clearstatcache();
		if (file_exists($f)) { 
			if($type == 'js') { 
				$code = fastvelocity_min_get_js($hurl, file_get_contents($f), $disable_minification); 
			} else { 
				$code = fastvelocity_min_get_css($hurl, file_get_contents($f).$inline, $disable_minification); 
			}
			
			# log, save and return
			$log = $printurl;
			if($fvm_debug == true) { $log.= " --- Debug: $printhandle was opened from $f ---"; }
			$log.= PHP_EOL;
			$return = array('request'=>$dreq, 'log'=>$log, 'code'=>$code, 'status'=>true);
			return json_encode($return);
		}
		
		# failover when home_url != site_url
		$nhurl = str_ireplace(site_url(), home_url(), $hurl);
		$f = str_ireplace(rtrim($wp_home, '/'), rtrim($wp_home_path, '/'), $nhurl);
		clearstatcache();
		if (file_exists($f)) { 
			if($type == 'js') { 
				$code = fastvelocity_min_get_js($hurl, file_get_contents($f), $disable_minification); 
			} else { 
				$code = fastvelocity_min_get_css($hurl, file_get_contents($f).$inline, $disable_minification); 
			}
			
			# log, save and return
			$log = $printurl;
			if($fvm_debug == true) { $log.= " --- Debug: $printhandle was opened from $f ---"; }
			$log.= PHP_EOL;
			$return = array('request'=>$dreq, 'log'=>$log, 'code'=>$code, 'status'=>true);
			return json_encode($return);
		}
	}
	}


	# else, fallback to remote urls (or windows)
	$code = fastvelocity_download($hurl);	
	if($code !== false && !empty($code) && strtolower(substr($code, 0, 9)) != "<!doctype") {
	
		# check if we got HTML instead of js or css code
	
		if($type == 'js') { 
			$code = fastvelocity_min_get_js($hurl, $code, $disable_minification); 
		} else { 
			$code = fastvelocity_min_get_css($hurl, $code.$inline, $disable_minification); 
		}
		
		# log, save and return
		$log = $printurl;
		if($fvm_debug == true) { $log.= " --- Debug: $printhandle was fetched from $hurl ---"; }
		$log.= PHP_EOL;
		$return = array('request'=>$dreq, 'log'=>$log, 'code'=>$code, 'status'=>true);
		return json_encode($return);
	}


	# fallback when home_url != site_url
	if(stripos($hurl, $wp_domain) !== false && home_url() != site_url()) {
		$nhurl = str_ireplace(site_url(), home_url(), $hurl);
		$code = fastvelocity_download($nhurl);
		if($code !== false && !empty($code) && strtolower(substr($code, 0, 9)) != "<!doctype") { 
			if($type == 'js') { 
				$code = fastvelocity_min_get_js($hurl, $code, $disable_minification); 
			} else { 
				$code = fastvelocity_min_get_css($hurl, $code.$inline, $disable_minification); 
			}
			
			# log, save and return
			$log = $printurl;
			if($fvm_debug == true) { $log.= " --- Debug: $printhandle was fetched from $hurl ---"; }
			$log.= PHP_EOL;
			$return = array('request'=>$dreq, 'log'=>$log, 'code'=>$code, 'status'=>true);
			return json_encode($return);
		}
	}


	# if remote urls failed... try to open locally again, regardless of OS in use
	if (stripos($hurl, $wp_domain) !== false) { 
		# default
		$f = str_ireplace(rtrim($wp_home, '/'), rtrim($wp_home_path, '/'), $hurl);
		clearstatcache();
		if (file_exists($f)) { 
			if($type == 'js') {
				$code = fastvelocity_min_get_js($hurl, file_get_contents($f), $disable_minification); 
			} else { 
				$code = fastvelocity_min_get_css($hurl, file_get_contents($f).$inline, $disable_minification); 
			}
			
			# log, save and return
			$log = $printurl;
			if($fvm_debug == true) { $log.= " --- Debug: $printhandle was opened from $f ---"; }
			$log.= PHP_EOL;
			$return = array('request'=>$dreq, 'log'=>$log, 'code'=>$code, 'status'=>true);
			return json_encode($return);
		}
		
		# failover when home_url != site_url
		$nhurl = str_ireplace(site_url(), home_url(), $hurl);
		$f = str_ireplace(rtrim($wp_home, '/'), rtrim($wp_home_path, '/'), $nhurl);
		clearstatcache();
		if (file_exists($f)) { 
			if($type == 'js') { 
				$code = fastvelocity_min_get_js($hurl, file_get_contents($f), $disable_minification); 
			} else { 
				$code = fastvelocity_min_get_css($hurl, file_get_contents($f).$inline, $disable_minification); 
			}
			
			# log, save and return
			$log = $printurl;
			if($fvm_debug == true) { $log.= " --- Debug: $printhandle was opened from $f ---"; }
			$log.= PHP_EOL;
			$return = array('request'=>$dreq, 'log'=>$log, 'code'=>$code, 'status'=>true);
			return json_encode($return);
		}
	}

	
	# else fail
	$log = $printurl;
	if($fvm_debug == true) { $log.= " --- Debug: $printhandle failed. Tried wp_remote_get, curl and local file_get_contents. ---"; }
	$return = array('request'=>$dreq, 'log'=>$log, 'code'=>'', 'status'=>false);
	return json_encode($return);
}


# check if the google font exist or not
function fastvelocity_min_concatenate_google_fonts_allowed($font) {
	global $gfontswhitelist;
	
	#normalize
	$gfontswhitelist = array_map('strtolower', $gfontswhitelist);
	$font = str_ireplace('+', ' ', strtolower($font));
	
	# check
	if(in_array($font, $gfontswhitelist)) {
		return true;
	}
	
	# fallback
	return false;
}


# Concatenate Google Fonts tags (http://fonts.googleapis.com/css?...)
function fastvelocity_min_concatenate_google_fonts($array) {

	# extract unique font families
	$families = array(); 
	foreach ($array as $font) {
		
		# must have
		if (stripos($font, 'family=') !== false) {

			# get fonts name, type and subset, remove wp query strings
			$font = explode('family=', htmlspecialchars_decode(rawurldecode(urldecode($font))));
			$a = explode('&v', end($font)); 
			$font = trim(trim(trim(current($a)), ','));

			# reprocess if fonts are already concatenated in this url
			if(stristr($font, '|') !== false) { 
				$multiple = explode('|', $font); 
				if (count($multiple) > 0) { 
					foreach ($multiple as $f) {
						$families[] = str_ireplace('subsets', 'subset', trim($f));
					} 
				}
			} else { 
				$families[] = str_ireplace('subsets', 'subset', trim($font));
			}
		}
	}
	
	# return if empty
	if(count($families) == 0) {
		return false;
	}
	
	# process names, types, subsets
	$fonts = array(); 
	$subsets = array();
	foreach ($families as $font) {
		
		# extract the subsets
		if (stripos($font, 'subset') !== false) {
			$sub = trim(str_ireplace('&subset=', '', stristr($font, '&')));      # second part of the string, after &
			$font = stristr($font, '&', true);                                   # font name, before &
			
			# subsets to array, unique, trim
			if (stripos($sub, ',') !== false) {
				$ft = explode(',', $sub);
				$ft = array_filter(array_map('trim', array_unique($ft)));
				foreach ($ft as $s) {
					$subsets[$s] = $s;
				}
			} else {
				if (!empty($sub)) {
					$subsets[$sub] = $sub;
				}
			}
			
		}
		
		# check for font name and weights
		$ftypes = array();
		$name = $font;
		if (stripos($font, ':') !== false) {
			$name = stristr($font, ':', true);       # font name, before :
			$fwe = trim(stristr($font, ':'), ':');   # second part of the string, after :

			# ftypes to array, unique, trim
			if (stripos($font, ',') !== false) {
				$ft = explode(',', $fwe);
				$ftypes = array_filter(array_map('trim', array_unique($ft)));
			} else {
				if (!empty($fwe)) {
					$ftypes[] = $fwe;
				}
			}
			
		}
		
		# name filter
		$name = str_ireplace(' ', '+', trim($name));
		
		# save fonts list, merge fontweights
		if(!isset($fonts[$name])) {
			$fonts[$name] = array('name'=>$name, 'type'=>$ftypes); 
		} else {
			$ftypes = array_merge($ftypes, $fonts[$name]['type']);
			$fonts[$name] = array('name'=>$name, 'type'=>$ftypes); 
		}
		
	}

	# build font names with font weights, if allowed
	$build = array();
	foreach($fonts as $farr) {
		if(fastvelocity_min_concatenate_google_fonts_allowed($farr['name']) == true) {
			$f = $farr['name'];
			if(count($farr['type']) > 0) {
				$f.= ':'. implode(',', $farr['type']);
			}
			$build[] = $f;
		}
	}

	# merge, append subsets
	$merge = '';
	if(count($build) > 0) {
		$merge = implode('|', $build);
		if(count($subsets) > 0) {
			$merge.= '&subset='.implode(',', $subsets);
		}
	}

	# return
	if(!empty($merge)) { 
		return 'https://fonts.googleapis.com/css?family='.$merge;
	} else {
		return false;
	}
}

# remove emoji support
function fastvelocity_min_disable_wp_emojicons() {
 remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
 remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
 remove_action( 'wp_print_styles', 'print_emoji_styles' );
 remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
 remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
 remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
 remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}

# remove from tinymce
function fastvelocity_disable_emojis_tinymce( $plugins ) {
 if ( is_array( $plugins ) ) {
 return array_diff( $plugins, array( 'wpemoji' ) );
 } else {
 return array();
 }
}



# escape double quotes
function fastvelocity_escape_double($string) {
	return str_ireplace(array('"', '\\"', '\\\"'), '\"', $string);
}


# remove UTF8 BOM
function fastvelocity_min_remove_utf8_bom($text) {
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/ui", '', $text);
    return $text;
}


# Remove query string from static css files
function fastvelocity_remove_cssjs_ver( $src ) {
 if(stripos($src, '?ver=')) { $src = remove_query_arg('ver', $src); }
 return $src;
}


# rewrite cache files to http, https or dynamic
function fvm_get_protocol($url) {
	global $wp_domain;
	$url = ltrim(str_ireplace(array('http://', 'https://'), '', $url), '/'); # better compatibility

	# cdn support
	$fvm_cdn_url = get_option('fastvelocity_min_fvm_cdn_url');
	$fvm_cdn_url = trim(trim(str_ireplace(array('http://', 'https://'), '', trim($fvm_cdn_url, '/'))), '/');
	
	# process cdn rewrite
	if(!empty($fvm_cdn_url) && fvm_is_local_domain($url) !== false) {
		
		# for js files, we need to consider thew defer for insights option
		if(substr($url, -3) == '.js') {
			
			$defer_for_pagespeed = get_option('fastvelocity_min_defer_for_pagespeed');
			$fvm_cdn_force = get_option('fastvelocity_min_fvm_cdn_force');
			
			if($defer_for_pagespeed != true || $fvm_cdn_force != false) {
				$url = str_ireplace($wp_domain, $fvm_cdn_url, $url);
			}
		
		} else {
			$url = str_ireplace($wp_domain, $fvm_cdn_url, $url);
		}
	}

	# enforce protocol if needed
	$default_protocol = get_option('fastvelocity_min_default_protocol', 'dynamic');
	if($default_protocol == 'dynamic' || empty($default_protocol)) { 
		if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) { $default_protocol = 'https://'; } else { $default_protocol = 'http://'; }
	} else { 
		$default_protocol = $default_protocol.'://'; 
	}
	
	# return
	return $default_protocol.$url;
}





# generate ascii slug
function fvm_safename($str, $noname=NULL) {
	$nstr = preg_replace("/[^a-zA-Z0-9]+/", "-", $str);
	$nstr = trim(trim($nstr, '-'));
	if(strlen($nstr) > 1) { 
		return $nstr; 
	}
	
	# return false if no empty name rewrite requested
	if($noname !== NULL) {
		return false;
	}
	
	# fallback
	return 'noname-'.hash('adler32', $str); 
}


# escape html tags for document.write
function fastvelocity_escape_url_js($str) {
return str_ireplace(array('\\\\\"', '\\\\"', '\\\"', '\\"'), '\"', json_encode($str));
}



# exclude processing from some pages / posts / contents
function fastvelocity_exclude_contents() {
	
	# exclude processing here
	if (is_admin() || (defined('DOING_AJAX') && DOING_AJAX) || (function_exists('wp_doing_ajax') && wp_doing_ajax()) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('WP_BLOG_ADMIN') && WP_BLOG_ADMIN) || (defined('WP_NETWORK_ADMIN') && WP_NETWORK_ADMIN) || (defined('WP_INSTALLING') && WP_INSTALLING) || (defined('WP_IMPORTING') && WP_IMPORTING) || (defined('WP_REPAIRING') && WP_REPAIRING) || (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) || (defined('SHORTINIT') && SHORTINIT) || (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') || 
	(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || (isset($_SERVER['REQUEST_URI']) && (substr($_SERVER['REQUEST_URI'], -4) == '.txt' || substr($_SERVER['REQUEST_URI'], -4) == '.xml'))) {
		return true;
	}
	
	# customizer preview, visual composer
	$arr = array('customize_theme', 'preview_id', 'preview');
	foreach ($arr as $a) { if(isset($_GET[$a])) { return true; } }

	# Thrive plugins and other post_types
	$arr = array('tve_form_type', 'tve_lead_shortcode', 'tqb_splash');
	foreach ($arr as $a) { if(isset($_GET['post_type']) && $_GET['post_type'] == $a) { return true; } }
	
	# default
	return false;
}

# Know files that should always be ignored
function fastvelocity_default_ignore($ignore) {
if(is_array($ignore)) {
	
	# from the database
	$exc = array_map('trim', explode(PHP_EOL, get_option('fastvelocity_min_ignorelist', '')));
	
	# should we exclude jquery when defer is enabled?
	$exclude_defer_jquery = get_option('fastvelocity_min_exclude_defer_jquery');
	$enable_defer_js = get_option('fastvelocity_min_enable_defer_js');
	if($enable_defer_js == true && $exclude_defer_jquery == true) {
		$exc[] = '/jquery.js';
		$exc[] = '/jquery.min.js';
	}

	# make sure it's unique and not empty
	$uniq = array();
	foreach ($ignore as $i) { $k = hash('adler32', $i); if(!empty($i)) { $uniq[$k] = $i; } }
	foreach ($exc as $e) { $k = hash('adler32', $e); if(!empty($e)) { $uniq[$k] = $e; } }

	# merge and return
	return $uniq;
} else { return $ignore; }
}


# IE only files that should always be ignored, without incrementing our groups
function fastvelocity_ie_blacklist($url) {

	# from the database
	$exc = array_map('trim', explode(PHP_EOL, get_option('fastvelocity_min_blacklist', '')));
	
	# must have
	$exc[] = '/fvm/cache/';
	
	# is the url on our list and return
	$res = fastvelocity_min_in_arrayi($url, $exc);
	if($res == true) { return true; } else { return false; }
}


# download function with fallback
function fastvelocity_download($url) {
	
	# info (needed for google fonts woff files + hinted fonts) as well as to bypass some security filters
	$uagent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2486.0 Safari/537.36 Edge/13.10586';

	# fetch via wordpress functions
	$response = wp_remote_get($url, array('user-agent'=>$uagent, 'timeout' => 7, 'httpversion' => '1.1', 'sslverify'=>false)); 
	$res_code = wp_remote_retrieve_response_code($response);
	if($res_code == '200') {
		$data = wp_remote_retrieve_body($response);
		if(strlen($data) > 1) {
			return $data;
		}
	}
	
	# verify
	if(!isset($res_code) || empty($res_code) || $res_code == false || is_null($res_code)) {
		return false;
	}
	
	# stop here, error 4xx or 5xx
	if($res_code[0] == '4' || $res_code[0] == '5') {
		return false;
	}
	
	# fallback fail
	return false;
}





