<?php 

extract(shortcode_atts(array("heading_tag" => "h3", 'text' => '',"btn_style" => "see-through", 'margin_top' => '','margin_right' => '','margin_bottom' => '', 'margin_left' => '', 'btn_type' => '', "link_text" => "", 'text_color' => '', 'url' => '', 'link_type' => 'regular', 'alignment' => 'left', 'class' => '' ), $atts));

$target = ($link_type == 'new_tab') ? 'target="_blank"' : null;
$style = (!empty($text_color)) ? ' style="color: '.$text_color.';"' : null;
$bg_style = (!empty($text_color)) ? ' style="background-color: '.$text_color.';"' : null;
$underline_border_color = $text_color;
$text_color = (!empty($text_color)) ? 'custom' : 'std';

//margins
$margins = '';
if(!empty($margin_top)) {
  $margins .= 'margin-top: '.intval($margin_top).'px; ';
}
if(!empty($margin_right)) {
  $margins .= 'margin-right: '.intval($margin_right).'px; ';
}
if(!empty($margin_bottom)) {
  $margins .= 'margin-bottom: '.intval($margin_bottom).'px; ';
}
if(!empty($margin_left)) {
  $margins .= 'margin-left: '.intval($margin_left).'px;';
}

$style_markup = null;

if(!empty($margins)) {
  $style_markup = 'style="'.$margins.'"';
} 

/*material*/
if($btn_style == 'material') {
  echo '<div class="nectar-cta '.$class.'" data-style="'.$btn_style.'" data-alignment="'.$alignment.'"  data-text-color="'.$text_color.'" '.$style_markup.'>';
  echo '<'.$heading_tag.'> <span class="text">'.$text.' </span>';
  echo  '<span class="link_wrap" '.$style.'><a '.$target .' class="link_text" href="'.$url.'">'.$link_text.'<span class="circle" '.$bg_style.'></span><span class="arrow"></span></a></span>'; 
  echo '</'.$heading_tag.'></div>';
} else if($btn_style == 'see-through') {
  echo '<div class="nectar-cta '.$class.'" data-style="'.$btn_style.'" data-alignment="'.$alignment.'"  data-text-color="'.$text_color.'" '.$style_markup.'>';
  echo '<'.$heading_tag. $style.'> <span class="text">'.$text.' </span>';
  echo  '<span class="link_wrap"><a '.$target .' class="link_text" href="'.$url.'">'.$link_text.'<span class="arrow"></span></a></span>'; 
  echo '</'.$heading_tag.'></div>';
} else if($btn_style == 'next-section') {
  
  if($btn_type == 'down-arrow-bounce') {
    echo '<div class="nectar-next-section-wrap bounce" '.$style_markup.'><a href="#" class="nectar-next-section skip-hash"> <i class="fa fa-angle-down"></i> </a></div>';
  } else if($btn_type == 'down-arrow-bordered') {
    echo '<div class="nectar-next-section-wrap down-arrow-bordered" '.$style_markup.'><a href="#" class="nectar-next-section skip-hash"><i class="fa fa-angle-down top"></i><i class="fa fa-angle-down"></i></a></div>';
  } else if($btn_type == 'mouse-wheel') {
    echo '<div class="nectar-next-section-wrap mouse-wheel" '.$style_markup.'><a href="#" class="nectar-next-section skip-hash"><svg class="nectar-scroll-icon" viewBox="0 0 30 45" enable-background="new 0 0 30 45">
          <path class="nectar-scroll-icon-path" fill="none" stroke="#ffffff" stroke-width="2" stroke-miterlimit="10" d="M15,1.118c12.352,0,13.967,12.88,13.967,12.88v18.76  c0,0-1.514,11.204-13.967,11.204S0.931,32.966,0.931,32.966V14.05C0.931,14.05,2.648,1.118,15,1.118z"></path>
        </svg></a></div>';
  }
  
} else {
  echo '<div class="nectar-cta '.$class.'" data-style="'.$btn_style.'" data-alignment="'.$alignment.'"  data-text-color="'.$text_color.'" '.$style_markup.'>';
  echo '<'.$heading_tag. $style.'> <span class="text">'.$text.' </span>';
  $border_color_attr = (!empty($underline_border_color)) ? 'style="border-color: '.$underline_border_color.';"' : '';
  echo  '<span class="link_wrap"><a '.$target .' class="link_text" '.$border_color_attr.' href="'.$url.'">'.$link_text.'</a></span>'; 
  echo '</'.$heading_tag.'></div>';
}


?>