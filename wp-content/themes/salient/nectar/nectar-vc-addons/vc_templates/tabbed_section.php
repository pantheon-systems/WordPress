<?php
$output = $title = $interval = $el_class = '';
extract(shortcode_atts(array(
    'title' => '',
    'interval' => 0,
    'el_class' => '',
    'style' => 'default',
    'alignment' => 'left',
    'spacing' => '',
    'tab_color' => '',
    'cta_button_text' => '',
    'cta_button_link' => '',
    'cta_button_style' => 'accent-color',
    'full_width_line' => '',
    'icon_size' => '24'
), $atts));


$el_class = $this->getExtraClass($el_class);

$element = 'wpb_tabs';
if ( 'vc_tour' == $this->shortcode) $element = 'wpb_tour';

if($style == 'default' || $style == 'vertical') {
  $icon_size = '';
}

// Extract tab titles
/*nectar addition*/
preg_match_all( '/tab[^]]+title="([^\"]+)"(\sid\=\"([^\"]+)\"){0,1}/i', $content, $matches, PREG_OFFSET_CAPTURE );
/*nectar addition end */
$tab_titles = array();

/**
 * vc_tabs
 *
 */
if ( isset($matches[0]) ) { $tab_titles = $matches[0]; }
$tabs_nav = '';
$tabs_nav .= '<ul class="wpb_tabs_nav ui-tabs-nav clearfix">';
foreach ( $tab_titles as $tab ) {
    preg_match('/title="([^\"]+)"(\sid\=\"([^\"]+)\"){0,1}/i', $tab[0], $tab_matches, PREG_OFFSET_CAPTURE );
    if(isset($tab_matches[1][0])) {
        $tabs_nav .= '<li><a href="#tab-'. (isset($tab_matches[3][0]) ? $tab_matches[3][0] : sanitize_title( $tab_matches[1][0] ) ) .'"><span>' . $tab_matches[1][0] . '</span></a></li>';

    }
}

//cta button
if(strlen($cta_button_text) >= 1) {
     $tabs_nav .= '<li class="cta-button"><a class="nectar-button medium regular-button '.$cta_button_style.'" data-color-override="false" href="'.$cta_button_link.'">' . $cta_button_text . '</a></li>';
}

$tabs_nav .= '</ul>'."\n";

$css_class =  apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, trim('wpb_content_element '.$el_class), $this->settings['base']);

$output .= "\n\t".'<div class="'.$css_class.'" data-interval="'.$interval.'">';
$output .= "\n\t\t".'<div class="wpb_wrapper tabbed clearfix" data-style="'.$style.'" data-spacing="'.$spacing.'" data-icon-size="'.$icon_size.'" data-full-width-line="'.$full_width_line.'" data-color-scheme="'.strtolower($tab_color).'" data-alignment="'.$alignment.'">';
$output .= wpb_widget_title(array('title' => $title, 'extraclass' => $element.'_heading'));
$output .= "\n\t\t\t".$tabs_nav;
$output .= "\n\t\t\t".wpb_js_remove_wpautop($content);
if ( 'vc_tour' == $this->shortcode) {
    $output .= "\n\t\t\t" . '<div class="wpb_tour_next_prev_nav clearfix"> <span class="wpb_prev_slide"><a href="#prev" title="'.__('Previous slide', 'js_composer').'">'.__('Previous slide', 'js_composer').'</a></span> <span class="wpb_next_slide"><a href="#next" title="'.__('Next slide', 'js_composer').'">'.__('Next slide', 'js_composer').'</a></span></div>';
}
$output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
$output .= "\n\t".'</div> '.$this->endBlockComment($element);

echo $output;