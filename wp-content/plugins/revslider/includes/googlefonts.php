<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 * @since 	  5.1.0
 * @lastfetch 16.03.2018
 */
 
if( !defined( 'ABSPATH') ) exit();

/**
*** CREATED WITH SCRIPT SNIPPET AND DATA TAKEN FROM https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&fields=items(family%2Csubsets%2Cvariants%2Ccategory)&key={YOUR_API_KEY}

$list = json_decode($list_raw, true);
$list = $list['items'];

echo '<pre>';
foreach($list as $l){
	echo "'".$l['family'] ."' => array("."\n";
	echo "'variants' => array(";
	foreach($l['variants'] as $k => $v){
		if($k > 0) echo ", ";
		if($v == 'regular') $v = '400';
		echo "'".$v."'";
	}
	echo "),\n";
	echo "'subsets' => array(";
	foreach($l['subsets'] as $k => $v){
		if($k > 0) echo ", ";
		echo "'".$v."'";
	}
	echo "),\n";
	echo "'category' => '". $l['category'] ."'";
	echo "\n),\n";
}
echo '</pre>';
**/

$googlefonts = array(
'Roboto' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Open Sans' => array(
'variants' => array('300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Lato' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Montserrat' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Roboto Condensed' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Oswald' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext'),
'category' => 'sans-serif'
),
'Source Sans Pro' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Slabo 27px' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Raleway' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'PT Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Roboto Slab' => array(
'variants' => array('100', '300', '400', '700'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'serif'
),
'Merriweather' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'Open Sans Condensed' => array(
'variants' => array('300', '300italic', '700'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Ubuntu' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin', 'greek-ext', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Noto Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'devanagari', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Playfair Display' => array(
'variants' => array('400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext'),
'category' => 'serif'
),
'Lora' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'Poppins' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'PT Serif' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'Roboto Mono' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'monospace'
),
'Arimo' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'hebrew', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Titillium Web' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Nunito' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Muli' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'PT Sans Narrow' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Noto Serif' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'serif'
),
'Indie Flower' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Inconsolata' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'monospace'
),
'Fira Sans' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Dosis' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Anton' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Bitter' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Crimson Text' => array(
'variants' => array('400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Oxygen' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cabin' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Nanum Gothic' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('korean', 'latin'),
'category' => 'sans-serif'
),
'Lobster' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'display'
),
'Arvo' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Josefin Sans' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Libre Baskerville' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Fjalla One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Work Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Exo 2' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'sans-serif'
),
'Quicksand' => array(
'variants' => array('300', '400', '500', '700'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Hind' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Yanone Kaffeesatz' => array(
'variants' => array('200', '300', '400', '700'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext'),
'category' => 'sans-serif'
),
'Abel' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Rubik' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'hebrew', 'cyrillic', 'latin-ext'),
'category' => 'sans-serif'
),
'Abril Fatface' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Karla' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Pacifico' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext'),
'category' => 'handwriting'
),
'Libre Franklin' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Shadows Into Light' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Merriweather Sans' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Varela Round' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'hebrew', 'latin-ext'),
'category' => 'sans-serif'
),
'Nunito Sans' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Ubuntu Condensed' => array(
'variants' => array('400'),
'subsets' => array('latin', 'greek-ext', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Cormorant Garamond' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'Asap' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Bree Serif' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Acme' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Gloria Hallelujah' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Signika' => array(
'variants' => array('300', '400', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Archivo Narrow' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Dancing Script' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'handwriting'
),
'Questrial' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Source Code Pro' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Kanit' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'thai', 'latin-ext'),
'category' => 'sans-serif'
),
'Exo' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Patua One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Amatic SC' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'vietnamese', 'hebrew', 'cyrillic', 'latin-ext'),
'category' => 'handwriting'
),
'Play' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Source Serif Pro' => array(
'variants' => array('400', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Francois One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Maven Pro' => array(
'variants' => array('400', '500', '700', '900'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'EB Garamond' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'serif'
),
'Crete Round' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Hind Siliguri' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'bengali', 'latin-ext'),
'category' => 'sans-serif'
),
'PT Sans Caption' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Berkshire Swash' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Comfortaa' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'display'
),
'Rokkitt' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'serif'
),
'Rajdhani' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Cairo' => array(
'variants' => array('200', '300', '400', '600', '700', '900'),
'subsets' => array('latin', 'latin-ext', 'arabic'),
'category' => 'sans-serif'
),
'Cuprum' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Shrikhand' => array(
'variants' => array('400'),
'subsets' => array('latin', 'gujarati', 'latin-ext'),
'category' => 'display'
),
'Cinzel' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Vollkorn' => array(
'variants' => array('400', 'italic', '600', '600italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'serif'
),
'Catamaran' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('tamil', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Heebo' => array(
'variants' => array('100', '300', '400', '500', '700', '800', '900'),
'subsets' => array('latin', 'hebrew'),
'category' => 'sans-serif'
),
'Passion One' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Poiret One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'display'
),
'Pathway Gothic One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Alegreya' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'serif'
),
'Courgette' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Permanent Marker' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Ropa Sans' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Great Vibes' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Russo One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'sans-serif'
),
'Noticia Text' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'serif'
),
'Old Standard TT' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'Quattrocento Sans' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Kaushan Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Righteous' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Yantramanav' => array(
'variants' => array('100', '300', '400', '500', '700', '900'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Teko' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Cookie' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Satisfy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Philosopher' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'ABeeZee' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Josefin Slab' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Alegreya Sans' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Archivo Black' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Orbitron' => array(
'variants' => array('400', '500', '700', '900'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Quattrocento' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Istok Web' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'News Cycle' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Domine' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Lobster Two' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Amaranth' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Monda' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cardo' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'greek-ext', 'latin-ext', 'greek'),
'category' => 'serif'
),
'Gudea' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Tinos' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'hebrew', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'serif'
),
'Economica' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Didact Gothic' => array(
'variants' => array('400'),
'subsets' => array('latin', 'greek-ext', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Playfair Display SC' => array(
'variants' => array('400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext'),
'category' => 'serif'
),
'Prompt' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'thai', 'latin-ext'),
'category' => 'sans-serif'
),
'Alfa Slab One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Assistant' => array(
'variants' => array('200', '300', '400', '600', '700', '800'),
'subsets' => array('latin', 'hebrew'),
'category' => 'sans-serif'
),
'Jura' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Amiri' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'arabic'),
'category' => 'serif'
),
'Sacramento' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Tangerine' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Fira Sans Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Sanchez' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Armata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Pontano Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Concert One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Handlee' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Kalam' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'handwriting'
),
'Cabin Condensed' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Volkhov' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Bangers' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Fredoka One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Arapey' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Hammersmith One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'BenchNine' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Fira Sans Extra Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Neuton' => array(
'variants' => array('200', '300', '400', 'italic', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Caveat' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'handwriting'
),
'Cantarell' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Ruda' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Boogaloo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Audiowide' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Nanum Myeongjo' => array(
'variants' => array('400', '700', '800'),
'subsets' => array('korean', 'latin'),
'category' => 'serif'
),
'Hind Madurai' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('tamil', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Khand' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Architects Daughter' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Shadows Into Light Two' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Marck Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'handwriting'
),
'Glegoo' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'Monoton' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'VT323' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'monospace'
),
'Limelight' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Arima Madurai' => array(
'variants' => array('100', '200', '300', '400', '500', '700', '800', '900'),
'subsets' => array('tamil', 'latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Yellowtail' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Forum' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'display'
),
'Neucha' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic'),
'category' => 'handwriting'
),
'Antic Slab' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Pragati Narrow' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Actor' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Unica One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Prosto One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'display'
),
'Changa' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'arabic'),
'category' => 'sans-serif'
),
'Special Elite' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Ultra' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Kreon' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Hind Vadodara' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'gujarati', 'latin-ext'),
'category' => 'sans-serif'
),
'Gentium Basic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Vidaloka' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Enriqueta' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'PT Mono' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'monospace'
),
'Arsenal' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Patrick Hand' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'handwriting'
),
'Sorts Mill Goudy' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Magra' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Yrsa' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Signika Negative' => array(
'variants' => array('300', '400', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Homemade Apple' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Julius Sans One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Prata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'cyrillic-ext'),
'category' => 'serif'
),
'Sintony' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Scada' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Covered By Your Grace' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Bevan' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Alice' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'cyrillic-ext'),
'category' => 'serif'
),
'Rancho' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Playball' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Cabin Sketch' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Aldrich' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Martel' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'Rock Salt' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Bad Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic'),
'category' => 'handwriting'
),
'Gentium Book Basic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Adamina' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Frank Ruhl Libre' => array(
'variants' => array('300', '400', '500', '700', '900'),
'subsets' => array('latin', 'hebrew', 'latin-ext'),
'category' => 'serif'
),
'Luckiest Guy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Damion' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Barlow Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Basic' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Chewy' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Caveat Brush' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Rufina' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Contrail One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Varela' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Alex Brush' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Sigmar One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Paytone One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Arbutus Slab' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Advent Pro' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'greek'),
'category' => 'sans-serif'
),
'Nothing You Could Do' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Alegreya Sans SC' => array(
'variants' => array('100', '100italic', '300', '300italic', '400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Lusitana' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Rambla' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Coustard' => array(
'variants' => array('400', '900'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Chivo' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Share' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Press Start 2P' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'display'
),
'Gochi Hand' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Coda' => array(
'variants' => array('400', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Montserrat Alternates' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Ubuntu Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'greek-ext', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'monospace'
),
'Saira Extra Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Just Another Hand' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'PT Serif Caption' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'Ovo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Barlow' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Fugaz One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Allura' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Oleo Script' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lustria' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Merienda' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Kameron' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Overlock' => array(
'variants' => array('400', 'italic', '700', '700italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Marmelad' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'sans-serif'
),
'Saira Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Cantata One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Syncopate' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Slabo 13px' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Rammetto One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Marcellus' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Electrolize' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Itim' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'thai', 'latin-ext'),
'category' => 'handwriting'
),
'Overpass' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Sarala' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Viga' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Spinnaker' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Nobile' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Marcellus SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Carter One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Cousine' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'hebrew', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'monospace'
),
'Molengo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Michroma' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Niconne' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Coming Soon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Pinyon Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Barlow Semi Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Carme' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Parisienne' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Kurale' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'devanagari', 'cyrillic-ext'),
'category' => 'serif'
),
'Ceviche One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Quantico' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Khula' => array(
'variants' => array('300', '400', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Oranienbaum' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'Leckerli One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Copse' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Black Ops One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Squada One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Reenie Beanie' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Rasa' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'gujarati', 'latin-ext'),
'category' => 'serif'
),
'Marvel' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Norican' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Radley' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Karma' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'Average' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Hind Guntur' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'telugu'),
'category' => 'sans-serif'
),
'Telex' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mr Dafoe' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Carrois Gothic' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Fauna One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Lalezar' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext', 'arabic'),
'category' => 'display'
),
'Days One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Freckle Face' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Voltaire' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Grand Hotel' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Bowlby One SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Archivo' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Lateef' => array(
'variants' => array('400'),
'subsets' => array('latin', 'arabic'),
'category' => 'handwriting'
),
'Baloo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext', 'devanagari'),
'category' => 'display'
),
'Pridi' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'vietnamese', 'thai', 'latin-ext'),
'category' => 'serif'
),
'Alef' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'hebrew'),
'category' => 'sans-serif'
),
'Fredericka the Great' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Italianno' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Taviraj' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'thai', 'latin-ext'),
'category' => 'serif'
),
'Jockey One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cormorant' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'Candal' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Allerta' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Anonymous Pro' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'cyrillic', 'latin-ext', 'greek'),
'category' => 'monospace'
),
'Annie Use Your Telescope' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Rozha One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'Rochester' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Antic' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Changa One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Bubblegum Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Nixie One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Zilla Slab' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Reem Kufi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'arabic'),
'category' => 'sans-serif'
),
'Halant' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'El Messiri' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'cyrillic', 'arabic'),
'category' => 'sans-serif'
),
'Martel Sans' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Yesteryear' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Mitr' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'vietnamese', 'thai', 'latin-ext'),
'category' => 'sans-serif'
),
'Alegreya SC' => array(
'variants' => array('400', 'italic', '500', '500italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'greek-ext', 'vietnamese', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'serif'
),
'Aclonica' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Share Tech Mono' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'monospace'
),
'Saira' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Bungee Inline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Nanum Gothic Coding' => array(
'variants' => array('400', '700'),
'subsets' => array('korean', 'latin'),
'category' => 'monospace'
),
'Goudy Bookletter 1911' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Allerta Stencil' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Palanquin' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Love Ya Like A Sister' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Homenaje' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Tauri' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Fanwood Text' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Space Mono' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'monospace'
),
'Tenor Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'sans-serif'
),
'Graduate' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Convergence' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Rosario' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Nanum Pen Script' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Londrina Solid' => array(
'variants' => array('100', '300', '400', '900'),
'subsets' => array('latin'),
'category' => 'display'
),
'Metrophobic' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Cutive' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Nanum Brush Script' => array(
'variants' => array('400'),
'subsets' => array('korean', 'latin'),
'category' => 'handwriting'
),
'Cutive Mono' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Judson' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'serif'
),
'Aladin' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Cinzel Decorative' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin'),
'category' => 'display'
),
'Doppio One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Inder' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mukta Vaani' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'gujarati', 'latin-ext'),
'category' => 'sans-serif'
),
'Andada' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Biryani' => array(
'variants' => array('200', '300', '400', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Knewave' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Calligraffitti' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Kelly Slab' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'display'
),
'Abhaya Libre' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'sinhala', 'latin-ext'),
'category' => 'serif'
),
'Caudex' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'greek-ext', 'latin-ext', 'greek'),
'category' => 'serif'
),
'Average Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Schoolbell' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Lilita One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bungee' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Six Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'GFS Didot' => array(
'variants' => array('400'),
'subsets' => array('greek'),
'category' => 'serif'
),
'Racing Sans One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Petit Formal Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Delius' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Scheherazade' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'arabic'),
'category' => 'serif'
),
'Jaldi' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Sue Ellen Francisco' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Quando' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Gilda Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Andika' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Montez' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Miriam Libre' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'hebrew', 'latin-ext'),
'category' => 'sans-serif'
),
'Trocchi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Mukta' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Krona One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Belleza' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Merienda One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Unna' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Pompiere' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Happy Monkey' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Herr Von Muellerhoff' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Puritan' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Saira Semi Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Trirong' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'vietnamese', 'thai', 'latin-ext'),
'category' => 'serif'
),
'Chelsea Market' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Give You Glory' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Yeseva One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'display'
),
'Gravitas One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Lekton' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Alike' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mada' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '900'),
'subsets' => array('latin', 'arabic'),
'category' => 'sans-serif'
),
'Titan One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Cambo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'The Girl Next Door' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'IM Fell Double Pica' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Balthazar' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Zeyada' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Arizonia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Encode Sans Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Risque' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Baloo Paaji' => array(
'variants' => array('400'),
'subsets' => array('latin', 'gurmukhi', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Mr De Haviland' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Capriola' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Oregano' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Spectral' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext'),
'category' => 'serif'
),
'Cambay' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Fontdiner Swanky' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Short Stack' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Gruppo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lemon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Amiko' => array(
'variants' => array('400', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Allan' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Wire One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Mako' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Duru Sans' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Poly' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Maitree' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'vietnamese', 'thai', 'latin-ext'),
'category' => 'serif'
),
'Mate' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Gafata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Mouse Memoirs' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Bowlby One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Vast Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Megrim' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Eczar' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'Kristi' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Secular One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'hebrew', 'latin-ext'),
'category' => 'sans-serif'
),
'Seaweed Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Spectral SC' => array(
'variants' => array('200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext'),
'category' => 'serif'
),
'Gabriela' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'cyrillic-ext'),
'category' => 'serif'
),
'Faster One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Coda Caption' => array(
'variants' => array('800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Averia Serif Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Belgrano' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Expletus Sans' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Hanuman' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'serif'
),
'Geo' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Baumans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Athiti' => array(
'variants' => array('200', '300', '400', '500', '600', '700'),
'subsets' => array('latin', 'vietnamese', 'thai', 'latin-ext'),
'category' => 'sans-serif'
),
'Brawler' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Fira Mono' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('latin', 'greek-ext', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext'),
'category' => 'monospace'
),
'Qwigley' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Fondamento' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Carrois Gothic SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'La Belle Aurore' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Oxygen Mono' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Walter Turncoat' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'IM Fell English' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Dawning of a New Day' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Lily Script One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Federo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Amita' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'handwriting'
),
'Buenard' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Strait' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'NTR' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Cherry Swash' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Anaheim' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Emilys Candy' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Just Me Again Down Here' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Bilbo Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Waiting for the Sunrise' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Port Lligat Slab' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Clicker Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Shojumaru' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Palanquin Dark' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Text Me One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Voces' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Orienta' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Rouge Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Rye' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Imprima' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Crafty Girls' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Iceland' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Podkova' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'Cedarville Cursive' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Delius Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'UnifrakturMaguntia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Pattaya' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'thai', 'cyrillic', 'latin-ext'),
'category' => 'sans-serif'
),
'Frijole' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Pangolin' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'handwriting'
),
'Unkempt' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Oleo Script Swash Caps' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Harmattan' => array(
'variants' => array('400'),
'subsets' => array('latin', 'arabic'),
'category' => 'sans-serif'
),
'Londrina Outline' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Patrick Hand SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'handwriting'
),
'Stardos Stencil' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Lemonada' => array(
'variants' => array('300', '400', '600', '700'),
'subsets' => array('latin', 'vietnamese', 'latin-ext', 'arabic'),
'category' => 'display'
),
'Loved by the King' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Stalemate' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Baloo Bhaina' => array(
'variants' => array('400'),
'subsets' => array('latin', 'oriya', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Gurajada' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Creepster' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Meddon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Rubik Mono One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'sans-serif'
),
'Chau Philomene One' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Artifika' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Euphoria Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'IM Fell English SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Italiana' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Finger Paint' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Amethysta' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mirza' => array(
'variants' => array('400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'arabic'),
'category' => 'display'
),
'Sofia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Denk One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Poller One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Tienne' => array(
'variants' => array('400', '700', '900'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Ranchers' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Condiment' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Proza Libre' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Raleway Dots' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell DW Pica' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Suez One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'hebrew', 'latin-ext'),
'category' => 'serif'
),
'Crushed' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sniglet' => array(
'variants' => array('400', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Asap Condensed' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Nova Mono' => array(
'variants' => array('400'),
'subsets' => array('latin', 'greek'),
'category' => 'monospace'
),
'Holtwood One SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Salsa' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Over the Rainbow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Bentham' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Sriracha' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'thai', 'latin-ext'),
'category' => 'handwriting'
),
'Vampiro One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ledger' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'serif'
),
'Katibeh' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'arabic'),
'category' => 'display'
),
'Prociono' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Baloo Tamma' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'kannada', 'latin-ext'),
'category' => 'display'
),
'Corben' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Kotta One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Wallpoet' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Aguafina Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Suranna' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Cherry Cream Soda' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Life Savers' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Medula One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Vesper Libre' => array(
'variants' => array('400', '500', '700', '900'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'Skranji' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Headland One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Englebert' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Shanti' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Share Tech' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Mallanna' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'McLaren' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Fjord One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Laila' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'Engagement' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Bungee Shade' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Encode Sans' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Codystar' => array(
'variants' => array('300', '400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Averia Sans Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Nova Square' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Slackey' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Aref Ruqaa' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'arabic'),
'category' => 'serif'
),
'Dorsa' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'David Libre' => array(
'variants' => array('400', '500', '700'),
'subsets' => array('latin', 'vietnamese', 'hebrew', 'latin-ext'),
'category' => 'serif'
),
'Mystery Quest' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ramabhadra' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Rakkas' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'arabic'),
'category' => 'display'
),
'Simonetta' => array(
'variants' => array('400', 'italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Sarpanch' => array(
'variants' => array('400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Cormorant Infant' => array(
'variants' => array('300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'Fenix' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Chonburi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'thai', 'latin-ext'),
'category' => 'display'
),
'Baloo Chettan' => array(
'variants' => array('400'),
'subsets' => array('malayalam', 'latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Milonga' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Amarante' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Nova Slim' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Metamorphous' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Habibi' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Sumana' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'Alike Angular' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Mate SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Tulpen One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Flamenco' => array(
'variants' => array('300', '400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Asul' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Stint Ultra Expanded' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Numans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'League Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Cantora One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Kite One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Dynalight' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Vibur' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Averia Libre' => array(
'variants' => array('300', '300italic', '400', 'italic', '700', '700italic'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kranky' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Mogra' => array(
'variants' => array('400'),
'subsets' => array('latin', 'gujarati', 'latin-ext'),
'category' => 'display'
),
'Rosarivo' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Wendy One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Cormorant SC' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'Quintessential' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Maiden Orange' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Princess Sofia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Sail' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Khmer' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Nosifer' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Miniver' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sancreek' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Donegal One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Mountains of Christmas' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Port Lligat Sans' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Mukta Mahee' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('latin', 'gurmukhi', 'latin-ext'),
'category' => 'sans-serif'
),
'Elsie' => array(
'variants' => array('400', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ruslan Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'display'
),
'Antic Didone' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Bilbo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Bellefair' => array(
'variants' => array('400'),
'subsets' => array('latin', 'hebrew', 'latin-ext'),
'category' => 'serif'
),
'Sansita' => array(
'variants' => array('400', 'italic', '700', '700italic', '800', '800italic', '900', '900italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Rhodium Libre' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'Inika' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Mrs Saint Delafield' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Scope One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Stoke' => array(
'variants' => array('300', '400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Glass Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Germania One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Delius Unicase' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Montserrat Subrayada' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Cagliostro' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Offside' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Buda' => array(
'variants' => array('300'),
'subsets' => array('latin'),
'category' => 'display'
),
'Esteban' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Kadwa' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'devanagari'),
'category' => 'serif'
),
'Rationale' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Junge' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Arya' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'sans-serif'
),
'Battambang' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Swanky and Moo Moo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Sarina' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Fresca' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Encode Sans Semi Condensed' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Kavoon' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mandali' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Ribeye' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Nova Flat' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Dekko' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'handwriting'
),
'IM Fell French Canon' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Peralta' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ruluko' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'IM Fell DW Pica SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Pirata One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Henny Penny' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Croissant One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bubbler One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'UnifrakturCook' => array(
'variants' => array('700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Baloo Thambi' => array(
'variants' => array('400'),
'subsets' => array('tamil', 'latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Paprika' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Linden Hill' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Trade Winds' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Spirax' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'IM Fell Great Primer' => array(
'variants' => array('400', 'italic'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Nova Round' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sonsie One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Yatra One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'display'
),
'Stint Ultra Condensed' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Plaster' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Overlock SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Baloo Bhai' => array(
'variants' => array('400'),
'subsets' => array('latin', 'gujarati', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Piedra' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Joti One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Almendra' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Meie Script' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Farsan' => array(
'variants' => array('400'),
'subsets' => array('latin', 'gujarati', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Asset' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'BioRhyme' => array(
'variants' => array('200', '300', '400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Julee' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Redressed' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Monsieur La Doulaise' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Pavanam' => array(
'variants' => array('400'),
'subsets' => array('tamil', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Griffy' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Timmana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Autour One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mukta Malar' => array(
'variants' => array('200', '300', '400', '500', '600', '700', '800'),
'subsets' => array('tamil', 'latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Eagle Lake' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Angkor' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Fascinate Inline' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sedgwick Ave' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'handwriting'
),
'Akronim' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'MedievalSharp' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Lovers Quarrel' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Revalia' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Galindo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Encode Sans Expanded' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Wellfleet' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Monofett' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Petrona' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Butcherman' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Uncial Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sunshiney' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Ruthie' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Snippet' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Content' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Baloo Bhaijaan' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext', 'arabic'),
'category' => 'display'
),
'Jim Nightshade' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Original Surfer' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Snowburst One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Della Respira' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Montaga' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Averia Gruesa Libre' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'IM Fell Double Pica SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Koulen' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Iceberg' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Ramaraja' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Cormorant Upright' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'serif'
),
'Underdog' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'display'
),
'Purple Purse' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Molle' => array(
'variants' => array('italic'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Caesar Dressing' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Macondo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sahitya' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'devanagari'),
'category' => 'serif'
),
'Bigshot One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Inknut Antiqua' => array(
'variants' => array('300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'Sirin Stencil' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Goblin One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Ewert' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Irish Grover' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Spicy Rice' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Oldenburg' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Chango' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Ravi Prakash' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'display'
),
'Lancelot' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Arbutus' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Trykker' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'Ranga' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'display'
),
'Flavors' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Elsie Swash Caps' => array(
'variants' => array('400', '900'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Atomic Age' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Smythe' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Dr Sugiyama' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Almendra SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Metal Mania' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Jolly Lodger' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mrs Sheppards' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Lakki Reddy' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'handwriting'
),
'Rum Raisin' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'sans-serif'
),
'Manuale' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'serif'
),
'Galada' => array(
'variants' => array('400'),
'subsets' => array('latin', 'bengali'),
'category' => 'display'
),
'Padauk' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'myanmar'),
'category' => 'sans-serif'
),
'Atma' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'bengali', 'latin-ext'),
'category' => 'display'
),
'Marko One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'New Rocker' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Coiny' => array(
'variants' => array('400'),
'subsets' => array('tamil', 'latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Fascinate' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Seymour One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'sans-serif'
),
'Modak' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'display'
),
'Tillana' => array(
'variants' => array('400', '500', '600', '700', '800'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'handwriting'
),
'Overpass Mono' => array(
'variants' => array('300', '400', '600', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'monospace'
),
'Modern Antiqua' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Jomhuria' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'arabic'),
'category' => 'display'
),
'Londrina Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Romanesco' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Nokora' => array(
'variants' => array('400', '700'),
'subsets' => array('khmer'),
'category' => 'serif'
),
'Miltonian Tattoo' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Jacques Francois' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Devonshire' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Margarine' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Faustina' => array(
'variants' => array('400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'serif'
),
'Ruge Boogie' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'IM Fell French Canon SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Jacques Francois Shadow' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Diplomata' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Diplomata SC' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Sree Krushnadevaraya' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Sedgwick Ave Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'handwriting'
),
'Galdeano' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'sans-serif'
),
'Keania One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Astloch' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sura' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'IM Fell Great Primer SC' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'serif'
),
'Ribeye Marrow' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Smokum' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Eater' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bonbon' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'handwriting'
),
'Geostar Fill' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Londrina Sketch' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Nova Oval' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Odor Mean Chey' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Miltonian' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Asar' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext', 'devanagari'),
'category' => 'serif'
),
'Freehand' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Felipa' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Kenia' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'GFS Neohellenic' => array(
'variants' => array('400', 'italic', '700', '700italic'),
'subsets' => array('greek'),
'category' => 'sans-serif'
),
'Warnes' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Passero One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Chicle' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bayon' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Barrio' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Meera Inimai' => array(
'variants' => array('400'),
'subsets' => array('tamil', 'latin'),
'category' => 'sans-serif'
),
'Stalinist One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'cyrillic', 'latin-ext'),
'category' => 'display'
),
'Bokor' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Zilla Slab Highlight' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mina' => array(
'variants' => array('400', '700'),
'subsets' => array('latin', 'bengali', 'latin-ext'),
'category' => 'sans-serif'
),
'Trochut' => array(
'variants' => array('400', 'italic', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kantumruy' => array(
'variants' => array('300', '400', '700'),
'subsets' => array('khmer'),
'category' => 'sans-serif'
),
'Suwannaphum' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Miss Fajardose' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Fruktur' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Nova Cut' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Combo' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Almendra Display' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Moul' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Supermercado One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Metal' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Nova Script' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kumar One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'gujarati', 'latin-ext'),
'category' => 'display'
),
'Emblema One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bigelow Rules' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bahiana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Dangrek' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Cormorant Unicase' => array(
'variants' => array('300', '400', '500', '600', '700'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'Siemreap' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Kavivanar' => array(
'variants' => array('400'),
'subsets' => array('tamil', 'latin', 'latin-ext'),
'category' => 'handwriting'
),
'Macondo Swash Caps' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Chathura' => array(
'variants' => array('100', '300', '400', '700', '800'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Butterfly Kids' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Gorditas' => array(
'variants' => array('400', '700'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sofadi One' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Geostar' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Sevillana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Aubrey' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Baloo Da' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'bengali', 'latin-ext'),
'category' => 'display'
),
'Federant' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Hanalei' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Bungee Outline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Erica One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Vollkorn SC' => array(
'variants' => array('400', '600', '700', '900'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'IBM Plex Sans' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'sans-serif'
),
'Tenali Ramakrishna' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Hanalei Fill' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Taprom' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Baloo Tammudu' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext', 'telugu'),
'category' => 'display'
),
'Bungee Hairline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'display'
),
'Chela One' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'display'
),
'Mr Bedfort' => array(
'variants' => array('400'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'handwriting'
),
'Peddana' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Libre Barcode 39 Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Kumar One Outline' => array(
'variants' => array('400'),
'subsets' => array('latin', 'gujarati', 'latin-ext'),
'category' => 'display'
),
'Kdam Thmor' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Preahvihear' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Chenla' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Gidugu' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Encode Sans Semi Expanded' => array(
'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Unlock' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Dhurjati' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'sans-serif'
),
'Fasthand' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'serif'
),
'Libre Barcode 39 Extended' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'IBM Plex Mono' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'monospace'
),
'Suravaram' => array(
'variants' => array('400'),
'subsets' => array('latin', 'telugu'),
'category' => 'serif'
),
'Moulpali' => array(
'variants' => array('400'),
'subsets' => array('khmer'),
'category' => 'display'
),
'Libre Barcode 39 Extended Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'IBM Plex Serif' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700italic'),
'subsets' => array('latin', 'vietnamese', 'cyrillic', 'latin-ext', 'cyrillic-ext'),
'category' => 'serif'
),
'BioRhyme Expanded' => array(
'variants' => array('200', '300', '400', '700', '800'),
'subsets' => array('latin', 'latin-ext'),
'category' => 'serif'
),
'IBM Plex Sans Condensed' => array(
'variants' => array('100', '100italic', '200', '200italic', '300', '300italic', '400', 'italic', '500', '500italic', '600', '600italic', '700', '700italic'),
'subsets' => array('latin', 'vietnamese', 'latin-ext'),
'category' => 'sans-serif'
),
'Libre Barcode 39' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Libre Barcode 128 Text' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
),
'Libre Barcode 128' => array(
'variants' => array('400'),
'subsets' => array('latin'),
'category' => 'display'
)
);

?>