<?php 

	extract(shortcode_atts(array("title"=>'Column title', "highlight" => 'false', "highlight_reason" => 'Most Popular', 'color' => 'Accent-Color', "price" => "99", "currency_symbol" => '$', "interval" => 'Per Month'), $atts));
	
	$highlight_class = null;
	$hightlight_reason_html = null;
	
	if($highlight == 'true') {
		$highlight_class = 'highlight ' . strtolower($color); 
		$hightlight_reason_html = '<span class="highlight-reason">'.$highlight_reason.'</span>';
	} else {
		$highlight_class = 'no-highlight ' . strtolower($color); 
	}
	
    echo'<div class="pricing-column '.$highlight_class.'">
  			<h3>'.$title. $hightlight_reason_html .'</h3>
            <div class="pricing-column-content">
				<h4> <span class="dollar-sign">'.$currency_symbol.'</span> '.$price.' </h4>
				<span class="interval">'.$interval.'</span>' . do_shortcode($content) . '</div></div>';
	

?>