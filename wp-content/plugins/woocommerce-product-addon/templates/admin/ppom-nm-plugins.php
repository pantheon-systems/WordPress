<?php
/*
** N-Media More Plugins Here...
*/

/* 
**========== Direct access not allowed =========== 
*/ 
if( ! defined('ABSPATH') ) die('Not Allowed');

$ppom_site_url = 'https://najeebmedia.com/wordpress-plugin/woocommerce-personalized-product-option/';
?>

<div class="ppom-nm-plugins-wrapper">

	<div id="ppom-nm-plugins-modal" class="ppom-modal-box" style="display: none;">
		    
	    <header> 
	        <img alt="N-Media" src="<?php echo esc_url(PPOM_URL.'/images/nmedia-logo.png')?>" class="ppom-nm-logo">
	        <h3><?php _e('PPOM Addons', "ppom");?></h3>
	    </header>

	    <div class="ppom-modal-body">
	    	<div class="row">

	    		<!-- Addon - Texter -->
	    		<div class="col-md-12">
	    			<div class="ppom-nm-card-block">
	    				<div class="ppom-card-header">
		    				<h3><?php _e('Addon - Texter', "ppom");?></h3>
	    				</div>
	    				<div class="ppom-card-body">
		    				<p><?php _e('PPOM Texter Addon is the best and simple solution for web2print business using WooCommerce. Now define a fixed position and area for Text in your Templates like on Mug, T-shirt or Visiting Cards with preset font style, family, size. The client will fill the text with his all of its attributes and send to cart. It’s like a smart Product Designer. Multiple templates can also be attached to one product.', "ppom");?></p>
	    				</div>
		    			<div class="ppom-card-footer">
		    				<a class="btn btn-info" href="<?php echo esc_url($ppom_site_url) ?>#ppom-texter" target="_blank"><?php _e('More Info...', "ppom");?></a>
		    			</div>
	    			</div>
	    		</div>

	    		<!-- Addon - Fields PopUp -->
	    		<div class="col-md-12">
	    			<div class="ppom-nm-card-block">
	    				<div class="ppom-card-header">
		    				<h3><?php _e('Addon - Fields PopUp', "ppom");?></h3>
	    				</div>
	    				<div class="ppom-card-body">
		    				<p><?php _e('PPOM Fields PopUp wrap all PPOM fields inside a popup. A product with large number of fields can now has simple button with customized label. To enable this PopUp just one click required in product edit page. For more details please visit Demo or watch video.', "ppom");?></p>
	    				</div>
		    			<div class="ppom-card-footer">
		    				<a href="<?php echo esc_url($ppom_site_url) ?>#fieldspopup" class="btn btn-info" target="_blank"><?php _e('More Info...', "ppom");?></a>
		    			</div>
	    			</div>
	    		</div>

	    		<!-- Addon - Image DropDown -->
	    		<div class="col-md-12">
	    			<div class="ppom-nm-card-block">
	    				<div class="ppom-card-header">
		    				<h3><?php _e('Addon - Image DropDown', "ppom");?></h3>
	    				</div>
	    				<div class="ppom-card-body">
		    				<p><?php _e('PPOM Image DropDown Addon show images inside a select box. The title, description, and prices can be added along with all images. It’s best when you have a long list of images and don’t want to use Image Type input.', "ppom");?></p>
	    				</div>
		    			<div class="ppom-card-footer">
		    				<a href="<?php echo esc_url($ppom_site_url) ?>#imagedropdown" class="btn btn-info" target="_blank"><?php _e('More Info...', "ppom");?></a>
		    			</div>
	    			</div>
	    		</div>

	    		<!-- Addon - Bulk Quantity for Options -->
	    		<div class="col-md-12">
	    			<div class="ppom-nm-card-block">
	    				<div class="ppom-card-header">
		    				<h3><?php _e('Addon - Bulk Quantity for Options', "ppom");?></h3>
	    				</div>
	    				<div class="ppom-card-body">
		    				<p><?php _e('Bulk Quantity for Options Addon allow store admin to set discount prices for each options. This Addon is best tool for companies like Printin, designing and who looking to sale products with options with different prices.', "ppom");?></p>
	    				</div>
		    			<div class="ppom-card-footer">
		    				<a href="<?php echo esc_url($ppom_site_url) ?>#bulkquantity" class="btn btn-info" target="_blank"><?php _e('More Info...', "ppom");?></a>
		    			</div>
	    			</div>
	    		</div>

	    		<!-- Addon - Google Font and Map Picker -->
	    		<div class="col-md-12">
	    			<div class="ppom-nm-card-block">
	    				<div class="ppom-card-header">
		    				<h3><?php _e('Addon - Google Font and Map Picker', "ppom");?></h3>
	    				</div>
	    				<div class="ppom-card-body">
		    				<p><?php _e('Google Font and Map Picker has two input fields. Font selector loads fonts from Google and client can pick font and can see live preview of font effect. Admin can also filter font families and set Custom Fonts. Google Map fetch coordinate based on address and show map.', "ppom");?></p>
	    				</div>
		    			<div class="ppom-card-footer">
		    				<a href="<?php echo esc_url($ppom_site_url) ?>#googlefontandmappicker" class="btn btn-info" target="_blank"><?php _e('More Info...', "ppom");?></a>
		    			</div>
	    			</div>
	    		</div>

	    		<!-- Addon - WooCommerce Package Price -->
	    		<div class="col-md-12">
	    			<div class="ppom-nm-card-block">
	    				<div class="ppom-card-header">
		    				<h3><?php _e('Addon - WooCommerce Package Price', "ppom");?></h3>
	    				</div>
	    				<div class="ppom-card-body">
		    				<p><?php _e('Sometimes prices are very complex like for a printing company, they are selling their visiting cards in Packages.So Package Price Add-on allows admin to set prices against package. It’s usage is very simple, just add quantity (package) and it’s price. There is also option to set unit like you are selling visiting cards then unit may called as “cards”.', "ppom");?></p>
	    				</div>
		    			<div class="ppom-card-footer">
		    				<a href="<?php echo esc_url($ppom_site_url) ?>#woocommercepackageprice" class="btn btn-info" target="_blank"><?php _e('More Info...', "ppom");?></a>
		    			</div>
	    			</div>
	    		</div>

	    		<!-- Addon - Domain Checker -->
	    		<div class="col-md-12">
	    			<div class="ppom-nm-card-block">
	    				<div class="ppom-card-header">
		    				<h3><?php _e('Addon - Domain Checker', "ppom");?></h3>
	    				</div>
	    				<div class="ppom-card-body">
		    				<p><?php _e('Domain Checker Addon will check any domain’s availability. Adds domain to cart if it’s not already registered. A simple solution to sell domains with WooCommerce PPOM. Customized messages for domain availability/not-availability. Ajax base script to check domain and show result.', "ppom");?></p>
	    				</div>
		    			<div class="ppom-card-footer">
		    				<a href="<?php echo esc_url($ppom_site_url) ?>#domainchecker" class="btn btn-info" target="_blank"><?php _e('More Info...', "ppom");?></a>
		    			</div>
	    			</div>
	    		</div>

	    		<!-- Addon - Cart Edit -->
	    		<div class="col-md-12">
	    			<div class="ppom-nm-card-block">
	    				<div class="ppom-card-header">
		    				<h3><?php _e('Addon - Cart Edit', "ppom");?></h3>
	    				</div>
	    				<div class="ppom-card-body">
		    				<p><?php _e('PPOM Cart Edit Addon allow clients to edit fields once these are added to cart on cart page. It has also option to show all meta fields in different column on cart page. Extra column can be disable from Settings -> PPOM Cart tab.', "ppom");?></p>
	    				</div>
		    			<div class="ppom-card-footer">
		    				<a href="<?php echo esc_url($ppom_site_url) ?>#cartedit" class="btn btn-info" target="_blank"><?php _e('More Info...', "ppom");?></a>
		    			</div>
	    			</div>
	    		</div>
	    	</div>
	    </div>
	    
	    <footer>
	    	<button type="button" class="btn btn-default close-model ppom-js-modal-close"><?php _e('Close' , 'ppom-addon-pdf'); ?></button>
	    </footer>
	</div>
</div>