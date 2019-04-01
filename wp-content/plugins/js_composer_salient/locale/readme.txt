Official info about translation: http://codex.wordpress.org/Translating_WordPress

In two words: open default.po file and save new copy as js_composer-es_ES.po for Spanish.
And then use translating program to translate it to Spanish language.

// Code to be placed in functions.php of your theme or a custom plugin file.
add_filter( 'load_textdomain_mofile', 'load_custom_plugin_translation_file', 10, 2 );

/*
 * Replace 'textdomain' with your plugin's textdomain. e.g. 'js_composer'.
 * File to be named, for example, yourtranslationfile-en_GB.mo
 * File to be placed, for example, wp-content/lanaguages/js_composer/js_composer-en_GB.mo
 */
function load_custom_plugin_translation_file( $mofile, $domain ) {
  if ( 'js_composer' === $domain ) {
    $mofile = WP_LANG_DIR . '/js_composer/js_composer-' . get_locale() . '.mo';
  }
  return $mofile;
}
