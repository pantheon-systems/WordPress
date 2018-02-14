<?php
function pmxe_render_xml_element($el, $shorten = false, $path = '/', $ind = 1, $lvl = 0)
{
	$path .= $el->nodeName;	
	$alternativePath = $path;	
	if ( ! $el->parentNode instanceof DOMDocument and $ind > 0) {
		$path .= "[$ind]";
	}		
	
	echo '<div class="xml-element lvl-' . $lvl . ' lvl-mod4-' . ($lvl % 4) . '" title="' . $path . '">';
	//if ($el->hasAttributes()){
		//echo '<div class="xml-element-xpaths">'; self::render_element_xpaths($el, $alternativePath, $ind, $lvl); echo '</div>';
	//}
	if ($el->hasChildNodes()) {
		$is_render_collapsed = $ind > 1;
		if ($el->childNodes->length > 1 or ! $el->childNodes->item(0) instanceof DOMText or strlen(trim($el->childNodes->item(0)->wholeText)) > 40) {
			echo '<div class="xml-expander">' . ($is_render_collapsed ? '+' : '-') . '</div>';
		}
		echo '<div class="xml-tag opening">&lt;<span class="xml-tag-name">' . $el->nodeName . '</span>'; pmxe_render_xml_attributes($el, $path . '/'); echo '&gt;</div>';
		if (1 == $el->childNodes->length and $el->childNodes->item(0) instanceof DOMText) {
			pmxe_render_xml_text(trim($el->childNodes->item(0)->wholeText), $shorten, $is_render_collapsed);
		} else {
			echo '<div class="xml-content' . ($is_render_collapsed ? ' collapsed' : '') . '">';
			$indexes = array();													
			foreach ($el->childNodes as $eli => $child) {						
				if ($child instanceof DOMElement) {
					empty($indexes[$child->nodeName]) and $indexes[$child->nodeName] = 0; $indexes[$child->nodeName]++;
					pmxe_render_xml_element($child, $shorten, $path . '/', $indexes[$child->nodeName], $lvl + 1); 
				} elseif ($child instanceof DOMCdataSection) {					
					pmxe_render_xml_text(trim($child->wholeText), $shorten, false, true);
				} elseif ($child instanceof DOMText) {												
					if ( $el->childNodes->item($eli - 1) and ($el->childNodes->item($eli - 1) instanceof DOMCdataSection) ){

					}
					elseif( $el->childNodes->item($eli + 1) and ($el->childNodes->item($eli + 1) instanceof DOMCdataSection) ){

					}
					else{								
						pmxe_render_xml_text(trim($child->wholeText), $shorten); 
					}
				} elseif ($child instanceof DOMComment) {
					if (preg_match('%\[pmxi_more:(\d+)\]%', $child->nodeValue, $mtch)) {
						$no = intval($mtch[1]);
						echo '<div class="xml-more">[ &dArr; ' . sprintf(__('<strong>%s</strong> %s more', 'wp_all_import_plugin'), $no, _n('element', 'elements', $no, 'wp_all_import_plugin')) . ' &dArr; ]</div>';
					}
				}
			}
			echo '</div>';
		}
		echo '<div class="xml-tag closing">&lt;/<span class="xml-tag-name">' . $el->nodeName . '</span>&gt;</div>';
	} else {
		echo '<div class="xml-tag opening empty">&lt;<span class="xml-tag-name">' . $el->nodeName . '</span>'; pmxe_render_xml_attributes($el); echo '/&gt;</div>';
	}
	echo '</div>';
}