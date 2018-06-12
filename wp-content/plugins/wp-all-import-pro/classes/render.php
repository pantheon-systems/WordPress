<?php
if ( ! class_exists('PMXI_Render')){

	class PMXI_Render{

		public function __construct(){

		}

		public static function xml_find_repeating(DOMElement $el, $path = '/')
		{
			$path .= $el->nodeName;
			if ( ! $el->parentNode instanceof DOMDocument) {
				$path .= '[1]';
			}
			$children = array();
			foreach ($el->childNodes as $child) {
				if ($child instanceof DOMElement) {
					if ( ! empty($children[$child->nodeName])) {
						return $path . '/' . $child->nodeName;
					} else {
						$children[$child->nodeName] = true;
					}
				}
			}
			// reaching this point means we didn't find anything among current element children, so recursively ask children to find something in them
			foreach ($el->childNodes as $child) {
				if ($child instanceof DOMElement) {
					$result = slef::xml_find_repeating($child, $path . '/');
					if ($result) {
						return $result;
					}
				}
			}
			// reaching this point means we didn't find anything, so return element itself if the function was called for it
			if ('/' . $el->nodeName == $path) {
				return $path;
			}
			
			return NULL;		
		}	

		public static function render_csv_element(DOMElement $el, $shorten = false, $path = '/', $ind = 1, $lvl = 0)
		{
			$path .= $el->nodeName;		
			if ( ! $el->parentNode instanceof DOMDocument and $ind > 0) {
				$path .= "[$ind]";
			}		
			
			echo '<div class="xml-element csv_element lvl-' . $lvl . ' lvl-mod4-' . ($lvl % 4) . '" title="' . $path . '">';
			if ($el->hasChildNodes()) {
				$is_render_collapsed = $ind > 1;			
				if ($lvl) echo '<div class="csv-tag opening"><span class="csv-tag-name">' . $el->nodeName . '</span>'; echo '</div>';
				if (1 == $el->childNodes->length and $el->childNodes->item(0) instanceof DOMText) {
					self::render_csv_text(trim($el->childNodes->item(0)->wholeText), $shorten, $is_render_collapsed);
				} else {
					echo '<div class="csv-content' . ($is_render_collapsed ? ' collapsed' : '') . '">';
					$indexes = array();										
					foreach ($el->childNodes as $child) {
						if ($child instanceof DOMElement) {
							empty($indexes[$child->nodeName]) and $indexes[$child->nodeName] = 0; $indexes[$child->nodeName]++;
							self::render_csv_element($child, $shorten, $path . '/', $indexes[$child->nodeName], $lvl + 1); 
						} elseif ($child instanceof DOMText) {												
							self::render_csv_text(trim($child->wholeText), $shorten); 
						} elseif ($child instanceof DOMComment) {
							if (preg_match('%\[pmxi_more:(\d+)\]%', $child->nodeValue, $mtch)) {
								$no = intval($mtch[1]);
								echo '<div class="xml-more">[ &dArr; ' . sprintf(__('<strong>%s</strong> %s more', 'wp_all_import_plugin'), $no, _n('element', 'elements', $no, 'wp_all_import_plugin')) . ' &dArr; ]</div>';
							}
						}
					}
					echo '</div>';
				}
				//echo '<div class="xml-tag closing"><span class="xml-tag-name">' . $el->nodeName . '</span></div>';
			} else {
				echo '<div class="xml-tag opening empty"><span class="xml-tag-name">' . $el->nodeName . '</span>'; self::render_xml_attributes($el); echo '</div>';
			}
			echo '</div>';
		}

		protected static function render_csv_text($text, $shorten = false, $is_render_collapsed = false)
		{
			if (empty($text) and 0 !== (int)$text) {
				return; // do not display empty text nodes
			}
			if (preg_match('%\[more:(\d+)\]%', $text, $mtch)) {
				$no = intval($mtch[1]);
				echo '<div class="xml-more">[ &dArr; ' . sprintf(__('<strong>%s</strong> %s more', 'wp_all_import_plugin'), $no, _n('element', 'elements', $no, 'wp_all_import_plugin')) . ' &dArr; ]</div>';
				return;
			}
			$more = '';
			if ($shorten and preg_match('%^(.*?\s+){20}(?=\S)%', $text, $mtch)) {
				$text = $mtch[0];
				$more = '<span class="xml-more">[' . __('more', 'wp_all_import_plugin') . ']</span>';
			}
			$is_short = strlen($text) <= 40;
			$newtext = htmlspecialchars($text); 
			//$newtext = preg_replace('%(?<!\s)\b(?!\s|\W[\w\s])|\w{20}%', '$0&#8203;', $newtext); // put explicit breaks for xml content to wrap
			echo '<div class="xml-content textonly' . ($is_short ? ' short' : '') . ($is_render_collapsed ? ' collapsed' : '') . ' '. (is_numeric($text) ? 'is_numeric' : '') .'">' . $newtext . $more . '</div>';
		}
		public static $option_paths = array();
		public static function render_xml_elements_for_filtring(DOMElement $el, $path ='', $lvl = 0){			
			if ("" != $path){ 
				if ($lvl > 1) $path .= "->" . $el->nodeName; else $path = $el->nodeName; 
				if (empty(self::$option_paths[$path])) 
					self::$option_paths[$path] = 1;
				else
					self::$option_paths[$path]++;
				echo '<option value="'.$path.'['. self::$option_paths[$path] .']">' .$path . '['. self::$option_paths[$path] .']</option>';
			}
			else $path = $el->nodeName;		
					
			foreach ($el->attributes as $attr) {
				echo '<option value="'.$path .'['. self::$option_paths[$path] .']'. '/@' . $attr->nodeName.'">'. $path .'['. self::$option_paths[$path] .']'. '@' . $attr->nodeName . '</option>';
			}
			if ($el->hasChildNodes()) {
				foreach ($el->childNodes as $child) {
					if ($child instanceof DOMElement) 
						self::render_xml_elements_for_filtring($child, $path, $lvl + 1);
				}
			}		
		}

		public static function render_xml_element(DOMElement $el, $shorten = false, $path = '/', $ind = 1, $lvl = 0)
		{
			$path .= $el->nodeName;	
			$alternativePath = $path;	
			if ( ! $el->parentNode instanceof DOMDocument and $ind > 0) {
				$path .= "[$ind]";
			}		
			
			echo '<div class="xml-element lvl-' . $lvl . ' lvl-mod4-' . ($lvl % 4) . '" title="' . $path . '">';
			//if ($el->hasAttributes()){
				echo '<div class="xml-element-xpaths">'; self::render_element_xpaths($el, $alternativePath, $ind, $lvl); echo '</div>';
			//}
			if ($el->hasChildNodes()) {
				$is_render_collapsed = $ind > 1;
				if ($el->childNodes->length > 1 or ! $el->childNodes->item(0) instanceof DOMText or strlen(trim($el->childNodes->item(0)->wholeText)) > 40) {
					echo '<div class="xml-expander">' . ($is_render_collapsed ? '+' : '-') . '</div>';
				}
				echo '<div class="xml-tag opening">&lt;<span class="xml-tag-name">' . $el->nodeName . '</span>'; self::render_xml_attributes($el, $path . '/'); echo '&gt;</div>';
				if (1 == $el->childNodes->length and $el->childNodes->item(0) instanceof DOMText) {
					self::render_xml_text(trim($el->childNodes->item(0)->wholeText), $shorten, $is_render_collapsed);
				} else {
					echo '<div class="xml-content' . ($is_render_collapsed ? ' collapsed' : '') . '">';
					$indexes = array();										
					foreach ($el->childNodes as $eli => $child) {
						if ($child instanceof DOMElement) {
							empty($indexes[$child->nodeName]) and $indexes[$child->nodeName] = 0; $indexes[$child->nodeName]++;
							self::render_xml_element($child, $shorten, $path . '/', $indexes[$child->nodeName], $lvl + 1); 
						} elseif ($child instanceof DOMCdataSection) {
							self::render_xml_text(trim($child->wholeText), $shorten, false, true); 
						} elseif ($child instanceof DOMText) {							
							if ( $el->childNodes->item($eli - 1) and ($el->childNodes->item($eli - 1) instanceof DOMCdataSection) ){

							}
							elseif( $el->childNodes->item($eli + 1) and ($el->childNodes->item($eli + 1) instanceof DOMCdataSection) ){

							}
							else{								
								self::render_xml_text(trim($child->wholeText), $shorten); 
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
				echo '<div class="xml-tag opening empty">&lt;<span class="xml-tag-name">' . $el->nodeName . '</span>'; self::render_xml_attributes($el); echo '/&gt;</div>';
			}
			echo '</div>';
		}

		protected static function render_xml_text($text, $shorten = false, $is_render_collapsed = false, $is_cdata = false)
		{
			if (empty($text) and 0 !== (int)$text) {
				return; // do not display empty text nodes
			}
			if (preg_match('%\[more:(\d+)\]%', $text, $mtch)) {
				$no = intval($mtch[1]);
				echo '<div class="xml-more">[ &dArr; ' . sprintf(__('<strong>%s</strong> %s more', 'wp_all_import_plugin'), $no, _n('element', 'elements', $no, 'wp_all_import_plugin')) . ' &dArr; ]</div>';
				return;
			}
			$more = '';
			if ($shorten and preg_match('%^(.*?\s+){20}(?=\S)%', $text, $mtch)) {
				$text = $mtch[0];
				$more = '<span class="xml-more">[' . __('more', 'wp_all_import_plugin') . ']</span>';
			}			
			$is_short = strlen($text) <= 40;			
			$text = htmlspecialchars($text);
			if ($is_cdata){
				$text = "<span class='wpallimport-cdata'>" . htmlspecialchars("<![CDATA[") . "</span> " . $text . " <span class='wpallimport-cdata'>" . htmlspecialchars("]]>") . "</span>";
			}			
			//$text = preg_replace('%(?<!\s)\b(?!\s|\W[\w\s])|\w{20}%', '$0&#8203;', $text); // put explicit breaks for xml content to wrap
			echo '<div class="xml-content textonly' . ($is_short ? ' short' : '') . ($is_render_collapsed ? ' collapsed' : '') . '">' . $text . $more . '</div>';
		}

		public static function get_xml_path(DOMElement $el, DOMXPath $xpath)
		{
			for($p = '', $doc = $el; $doc and ! $doc instanceof DOMDocument; $doc = $doc->parentNode) {
				if (($ind = $xpath->query('preceding-sibling::' . $doc->nodeName, $doc)->length)) {
					$p = '[' . ++$ind . ']' . $p;
				} elseif ( ! $doc->parentNode instanceof DOMDocument) {
					$p = '[' . ($ind = 1) . ']' . $p;
				}
				$p = '/' . $doc->nodeName . $p;
			}
			return $p;
		}

		protected static function render_xml_attributes(DOMElement $el, $path = '/')
		{
			foreach ($el->attributes as $attr) {
				echo ' <span class="xml-attr" title="' . $path . '@' . $attr->nodeName . '"><span class="xml-attr-name">' . $attr->nodeName . '</span>=<span class="xml-attr-value">"' . esc_attr($attr->value) . '"</span></span>';
			}
		}	

		protected static function render_element_xpaths(DOMElement $el, $path = '/', $ind = 1, $lvl = 0){						
			?>
			<ul id="menu-<?php echo str_replace('/', '-', esc_attr($path)); ?>" class="ui-helper-hidden">
				<?php foreach ($el->attributes as $attr) : if ( empty($attr->value) ) continue; ?>
			    <li data-command="action1" title="<?php echo esc_attr($path . '[@'. $attr->nodeName .' = "' . esc_attr($attr->value) . '"]'); ?>">
			    	<a href="#"><?php echo $path . '[@'. $attr->nodeName .' = "' . esc_attr($attr->value) . '"]'; ?></a>
			    </li>
			    <li data-command="action2" title="<?php echo esc_attr($path . '[@'. $attr->nodeName .'[contains(.,"' . esc_attr($attr->value) . '")]]'); ?>">
			    	<a href="#"><?php echo $path . '[@'. $attr->nodeName .'[contains(.,"' . esc_attr($attr->value) . '")]]'; ?></a>
			    </li>
				<?php endforeach; ?>
				<?php 
				$altNode = null;
				$altNodeText = null;
				$parentNode = $el->parentNode;
				$grandNode = $parentNode->parentNode;
				
				if ( ! $grandNode instanceof DOMDocument and $grandNode instanceof DOMElement ){		
					
					$equalsElements = 0;
					foreach ($grandNode->childNodes as $child) {
						if ($child instanceof DOMElement) {
							if ($child->nodeName == $parentNode->nodeName){
								$equalsElements++;
								if ($equalsElements > 1)
									break;
							}
						}
					}													

					if ($equalsElements > 1){
						if ($parentNode->hasChildNodes()) {
							foreach ($parentNode->childNodes as $i => $child) {
								if ($child instanceof DOMElement) {
									if ($child->nodeName != $el->nodeName){
										$altNode = $child;
										if ($child->hasChildNodes()){
											foreach ($child->childNodes as $i => $txtChild) {
												if ($txtChild instanceof DOMText) {
													$altNodeText = $txtChild;										
													break;
												}
											}
										}										
										break;
									}
								}
							}
						}
						
						if ( ! empty($altNode) and !empty($altNodeText) ){

							$pathArgs = explode('/', $path);
							array_pop($pathArgs);						
							array_pop($pathArgs);		
							$vpath = esc_attr(implode('/', $pathArgs) . '/' . $parentNode->nodeName . '[contains('. $altNode->nodeName .',"' . esc_attr($altNodeText->wholeText) . '")]/' . $el->nodeName);				
							?>
							<li data-command="action3" title="<?php echo $vpath; ?>">
						    	<a href="#"><?php echo $vpath; ?></a>
						    </li>						
						    <?php

						}
					}					
				}				
				?>								
			</ul>
			<?php			
		}
	}
}