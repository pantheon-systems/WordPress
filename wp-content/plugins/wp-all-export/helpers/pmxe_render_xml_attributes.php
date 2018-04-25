<?php
function pmxe_render_xml_attributes($el, $path = '/')
{
	foreach ($el->attributes as $attr) {
		echo ' <span class="xml-attr" title="' . $path . '@' . $attr->nodeName . '"><span class="xml-attr-name">' . $attr->nodeName . '</span>=<span class="xml-attr-value">"' . esc_attr($attr->value) . '"</span></span>';
	}
}