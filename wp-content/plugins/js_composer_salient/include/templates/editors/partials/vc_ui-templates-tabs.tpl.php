<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/** @var $box Vc_Templates_Panel_Editor */
$with_tabs = count( $categories ) > 0;
if ( count( $categories ) > 0 ) :
	$first = true;
	
	/*nectar addition*/
	if($categories) {
		$categories = array_reverse($categories);
	}
	/*nectar addition end*/
	?>
	<ul class="vc_general vc_ui-tabs-line" data-vc-ui-element="panel-tabs-controls">
						<?php foreach ( $categories as $key => $value ) :
							echo '<li'
							. ' class="vc_panel-tabs-control' . ( $first ? ' vc_active' : '' ) . '"><button data-vc-ui-element-target="[data-tab='
							. trim( esc_attr( $key ) )
							. ']" class="vc_ui-tabs-line-trigger" data-vc-ui-element="panel-tab-control">' . esc_html( $value ) . '</button>';
							echo '</li>';
							$first = false;
endforeach;
						echo '<li class="vc_ui-tabs-line-dropdown-toggle" data-vc-action="dropdown" data-vc-content=".vc_ui-tabs-line-dropdown" data-vc-ui-element="panel-tabs-line-toggle">
                            <span class="vc_ui-tabs-line-trigger" data-vc-accordion="" data-vc-container=".vc_ui-tabs-line-dropdown-toggle" data-vc-target=".vc_ui-tabs-line-dropdown"> </span>
							<ul class="vc_ui-tabs-line-dropdown" data-vc-ui-element="panel-tabs-line-dropdown">
							</ul>
					</li>';
						echo '</ul>';
endif;
?>
