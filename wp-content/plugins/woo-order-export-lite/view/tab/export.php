<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<br>
<div class="tabs-content">
	<?php
//settings for form
	$show = array( 'date_filter' => true, 'export_button' => true,'export_button_plain' => true, 'destinations' => false, 'schedule' => false, );
	$WC_Order_Export->render( 'settings-form', array( 'mode' => WC_Order_Export_Manage::EXPORT_NOW, 'id' => 0, 'WC_Order_Export' => $WC_Order_Export, 'ajaxurl' => $ajaxurl, 'show' => $show ) );
	?> 
</div>