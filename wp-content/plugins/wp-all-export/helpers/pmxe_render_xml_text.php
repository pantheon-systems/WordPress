<?php
function pmxe_render_xml_text($text, $shorten = false, $is_render_collapsed = false)
{		
	if (empty($text)) {
		return; // do not display empty text nodes
	}	

	if (preg_match('%\[more:(\d+)\]%', $text, $mtch)) {
		$no = intval($mtch[1]);
		echo '<div class="xml-more">[ &dArr; ' . sprintf(__('<strong>%s</strong> %s more', 'pmxi_plugin'), $no, _n('element', 'elements', $no, 'pmxi_plugin')) . ' &dArr; ]</div>';
		return;
	}
	$more = '';
	if ($shorten and preg_match('%^(.*?\s+){20}(?=\S)%', $text, $mtch)) {
		$text = $mtch[0];
		$more = '<span class="xml-more">[' . __('more', 'pmxi_plugin') . ']</span>';
	}	
	$text = esc_html($text);
	// $text = preg_replace('%(?<!\s)\b(?!\s|\W[\w\s])|\w{20}%', '$0&#8203;', $text); // put explicit breaks for xml content to wrap	
	$is_cdata = ( strpos($text, 'CDATABEGIN') !== false );
	$text = str_replace('CDATABEGIN', '&lt;![CDATA[', $text);
	$text = str_replace('CDATACLOSE', ']]&gt;', $text);
	$is_short = strlen($text) <= 40;
	echo '<div class="xml-content textonly' . ($is_short ? ' short' : '') . ($is_cdata ? ' cdata' : '') . ($is_render_collapsed ? ' collapsed' : '') . '">' . $text . $more . '</div>';
}