<?php
/**
 * Welcome Page Class
 *
 * @package     woocommerce-chained-products/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * CP_Admin_Welcome class
 */
class CP_Admin_Welcome {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'cp_welcome' ) );
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {

		if ( empty( $_GET['page'] ) ) { // WPCS: CSRF ok.
			return;
		}

		$welcome_page_name  = __( 'About Chained Products', 'woocommerce-chained-products' );
		$welcome_page_title = __( 'Welcome to Chained Products', 'woocommerce-chained-products' );

		switch ( $_GET['page'] ) { // WPCS: CSRF ok.
			case 'cp-about':
				add_submenu_page( 'edit.php?post_type=product', $welcome_page_title, $welcome_page_name, 'manage_options', 'cp-about', array( $this, 'about_screen' ) );
				break;
			case 'cp-shortcode':
				add_submenu_page( 'edit.php?post_type=product', $welcome_page_title, $welcome_page_name, 'manage_options', 'cp-shortcode', array( $this, 'shortcode_screen' ) );
				break;
			case 'cp-faqs':
				add_submenu_page( 'edit.php?post_type=product', $welcome_page_title, $welcome_page_name, 'manage_options', 'cp-faqs', array( $this, 'faqs_screen' ) );
				break;
		}
	}

	/**
	 * Add styles just for this page, and remove dashboard page links.
	 */
	public function admin_head() {
		remove_submenu_page( 'edit.php?post_type=product', 'cp-about' );
		remove_submenu_page( 'edit.php?post_type=product', 'cp-shortcode' );
		remove_submenu_page( 'edit.php?post_type=product', 'cp-faqs' );

		?>
		<style type="text/css">
			/*<![CDATA[*/
			.about-wrap h3 {
				margin-top: 1em;
				margin-right: 0em;
				margin-bottom: 0.1em;
				font-size: 1.25em;
				line-height: 1.3em;
			}
			.about-wrap .button-primary {
				margin-top: 18px;
			}
			.about-wrap .button-hero {
				color: #FFF!important;
				border-color: #03a025!important;
				background: #03a025 !important;
				box-shadow: 0 1px 0 #03a025;
				font-size: 1em;
				font-weight: bold;
			}
			.about-wrap .button-hero:hover {
				color: #FFF!important;
				background: #0AAB2E!important;
				border-color: #0AAB2E!important;
			}
			.about-wrap p {
				margin-top: 0.6em;
				margin-bottom: 0.8em;
				line-height: 1.6em;
				font-size: 14px;
			}
			.about-wrap .feature-section {
				padding-bottom: 5px;
			}
			/*]]>*/
		</style>
		<?php
	}

	/**
	 * Intro text/links shown on all about pages.
	 */
	private function intro() {

		if ( is_callable( 'WC_Admin_Chained_Products::get_chained_products_plugin_data' ) ) {
			$plugin_data = WC_Admin_Chained_Products::get_chained_products_plugin_data();
			$version     = $plugin_data['Version'];
		} else {
			$version = '';
		}

		?>
		<h1>
			<?php
				/* translators: Plugin version */
				printf( esc_html__( 'Welcome to Chained Products %s', 'woocommerce-chained-products' ), esc_html( $version ) );
			?>
		</h1>

		<h3>
			<?php
				echo esc_html__( 'Thanks for installing! We hope you enjoy using Chained Products.', 'woocommerce-chained-products' );
			?>
		</h3>

		<div class="feature-section col two-col" style="margin-bottom:30px!important;">
			<div class="col">
				<p style="margin-left:unset">
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>" class="button button-hero"><?php echo esc_html__( 'Create combo!', 'woocommerce-chained-products' ); ?></a>
				</p>
			</div>

			<div class="col last-feature">
				<p align="right" style="margin-left:unset">
					<a href="<?php echo esc_url( apply_filters( 'chained_products_docs_url', 'https://docs.woocommerce.com/document/chained-products/', 'woocommerce-chained-products' ) ); ?>" class="docs button button-primary" target="_blank"><?php echo esc_html__( 'Docs', 'woocommerce-chained-products' ); ?></a>
				</p>
			</div>
		</div>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab
			<?php
			if ( ! empty( $_GET['page'] ) && 'cp-about' === $_GET['page'] ) { // WPCS: CSRF ok.
				echo 'nav-tab-active';}
?>
" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'cp-about' ), 'admin.php' ) ) ); ?>">
				<?php echo esc_html__( 'Know Chained Products', 'woocommerce-chained-products' ); ?>
			</a>
			<a class="nav-tab
			<?php
			if ( ! empty( $_GET['page'] ) && 'cp-shortcode' === $_GET['page'] ) { // WPCS: CSRF ok.
				echo 'nav-tab-active';}
?>
" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'cp-shortcode' ), 'admin.php' ) ) ); ?>">
				<?php echo esc_html__( 'Shortcode', 'woocommerce-chained-products' ); ?>
			</a>
			<a class="nav-tab
			<?php
			if ( ! empty( $_GET['page'] ) && 'cp-faqs' === $_GET['page'] ) { // WPCS: CSRF ok.
				echo 'nav-tab-active';}
?>
" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'cp-faqs' ), 'admin.php' ) ) ); ?>">
				<?php echo esc_html__( "FAQ's", 'woocommerce-chained-products' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		?>

		<script type="text/javascript">
			jQuery(document).on('ready', function(){
				jQuery('#menu-posts-product').find('a[href="edit.php?post_type=product"]').addClass('current');
				jQuery('#menu-posts-product').find('a[href="edit.php?post_type=product"]').parent().addClass('current');
			});
		</script>

		<div class="wrap about-wrap" style="max-width:unset;">

		<?php $this->intro(); ?>

			<div>
				<center><h3><?php echo esc_html__( 'Terminologies', 'woocommerce-chained-products' ); ?></h3></center>
				<div class="feature-section col two-col" >
					<div class="col">
						<h4><?php echo esc_html__( 'Main Product / Chained Parent', 'woocommerce-chained-products' ); ?></h4>
						<p style="margin-left:unset">
							<?php echo esc_html__( 'This is the product to which other products will be attached. On adding this product to cart, all the attached products will be automatically added to cart or order with price zero.', 'woocommerce-chained-products' ); ?>
							<?php
							if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
								echo esc_html__( 'You can also enable pricing for any individual chained item while configuring the product.', 'woocommerce-chained-products' );
							}
							?>
						</p>
					</div>
					<div class="col last-feature">
						<h4><?php echo esc_html__( 'Chained Item / Chained Child', 'woocommerce-chained-products' ); ?></h4>
						<p style="margin-left:unset">
							<?php echo esc_html__( 'Products which are attached to any other product are termed as Chained item. Chained item will always be added automatically to cart when its parent is added to cart or order.', 'woocommerce-chained-products' ); ?>
						</p>
					</div>
				</div>
				<center><h3><?php echo esc_html__( 'Chained Products', 'woocommerce-chained-products' ); ?></h3></center>
				<div class="feature-section col three-col">
					<div class="col">
						<h4><?php echo esc_html__( 'What is Chained Products?', 'woocommerce-chained-products' ); ?></h4>
						<p>
							<?php echo esc_html__( 'It\'s a WooCommerce add-on, which allows you to add any other WooCommerce product to an existing product in such a way that it creates a chain. In any order, when the main product is availble, all its chained item will also be present.', 'woocommerce-chained-products' ); ?>
						</p>
					</div>
					<div class="col">
						<h4><?php echo esc_html__( 'What\'s the final result?', 'woocommerce-chained-products' ); ?></h4>
						<p>
							<?php echo esc_html__( 'Whenever a product, to which other products are chained, and it will be added to cart, all the product which are attached to main product will be added to cart.', 'woocommerce-chained-products' ); ?>
						</p>
					</div>
					<div class="col last-feature">
						<h4><?php echo esc_html__( 'What\'s new?', 'woocommerce-chained-products' ); ?></h4>
						<p>
						<?php
						if ( Chained_Products_WC_Compatibility::is_wc_gte_30() ) {
							echo esc_html__( 'Now you can enable pricing for any individual chained item while configuring the chained product.', 'woocommerce-chained-products' );
						} else {
							echo esc_html__( 'All those products which are attached to main product, when added to cart, their price will be removed, i.e. it will be added as price zero', 'woocommerce-chained-products' );
						}
						?>
						</p>
					</div>
				</div>
				<center><h3><?php echo esc_html__( 'What is possible', 'woocommerce-chained-products' ); ?></h3></center>
				<div class="feature-section col three-col" >
					<div class="col">
						<h4><?php echo esc_html__( 'Create combos & packs', 'woocommerce-chained-products' ); ?></h4>
						<p>
							<?php echo esc_html__( 'A combo product is a collection of multiple product. Generally combos are created to encourage customer to buy many products.', 'woocommerce-chained-products' ); ?>
						</p>
						<p>
							<?php echo esc_html__( 'You can create a separate product & include all those product which you want in that combos, set a price for this combo. Now, if enabled, the plugin will also handle inventory for all products of combo.', 'woocommerce-chained-products' ); ?>
						</p>
					</div>
					<div class="col">
						<h4><?php echo esc_html__( 'Giveaway a product to all your existing customer', 'woocommerce-chained-products' ); ?></h4>
						<p>
							<?php echo esc_html__( 'There can be cases that you want to giveaway a product for free to your existing customer & also new customer. Chained Products provides you a setting to add newly added chained items to all existing order which includes chained parent.', 'woocommerce-chained-products' ); ?>
						</p>
					</div>
					<div class="col last-feature">
						<h4><?php echo esc_html__( 'Buy 1 Get X', 'woocommerce-chained-products' ); ?></h4>
						<p>
							<?php echo esc_html__( 'Since Chained Products allows you to set quantity for chained items, you can create combo such as: Buy 1 Get 1 Free, Buy 1 Get 2 Free & so on...', 'woocommerce-chained-products' ); ?>
						</p>
					</div>
				</div>
				<div class="feature-section col three-col" >
					<div class="col">
						<h4><?php echo esc_html__( 'Display Chained items info on Product page', 'woocommerce-chained-products' ); ?></h4>
						<p>
							<?php echo __( 'Chained Products provides you a shortcode <b>[chained_products]</b> using which you can easily display chained items information on product page.', 'woocommerce-chained-products' ); // WPCS: XSS ok. ?>
						</p>
					</div>
					<div class="col">
						<h4><?php echo esc_html__( 'Works well with other Product Types', 'woocommerce-chained-products' ); ?></h4>
						<p>
							<?php echo __( 'Chained Products also works with <b>WooCommerce Product Bundles, WooCommerce Give Products, WooCommerce Composite Products, WooCommerce Mix \'n Match Products</b>.', 'woocommerce-chained-products' ); // WPCS: XSS ok. ?>
						</p>
						<p>
							<?php echo esc_html__( 'You can set a Chained Parent as an item in any of the above product type. Now, whenever the product will be added to cart or order, chained parent along with its chained item will also be added to cart or order.', 'woocommerce-chained-products' ); ?>
						</p>
					</div>
					<div class="col last-feature">
						<h4><?php echo esc_html__( 'Stock Dependency', 'woocommerce-chained-products' ); ?></h4>
						<p>
							<?php echo esc_html__( 'Stock Management feature of Chained Products can be very powerful if your store is selling an assembled item. Though an assembled item is a single unit but it has many parts & in multiple quantity. You may have inventory for those individual item also. Chained Products can play very important role here.', 'woocommerce-chained-products' ); ?>
						</p>
						<p>
							<?php echo esc_html__( 'Let\'s take an example: You are selling Desktop PC. You\'ve created many Desktop PCs with different configuration. Each configuration individually is a separate product which is chained to Desktop PC. So, whenever any customer will order 1 Desktop PC, inventory of its parts will be reduced automatically', 'woocommerce-chained-products' ); ?>
						</p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Output the Shortcode screen.
	 */
	public function shortcode_screen() {
		?>
		<script type="text/javascript">
			jQuery(document).on('ready', function(){
				jQuery('#menu-posts-product').find('a[href="edit.php?post_type=product"]').addClass('current');
				jQuery('#menu-posts-product').find('a[href="edit.php?post_type=product"]').parent().addClass('current');
			});
		</script>

		<div class="wrap about-wrap" style="max-width:unset;">

			<?php $this->intro(); ?>

			<h2 align="center"><em><code>[chained_products]</code></em></h2>

			<div>
				<div class="feature-section col two-col">
					<div class="col">
						<p><?php echo esc_html__( 'Chained Products shortcode is created to show all chained products on a product page. Previously when shortcode feature was not available, all chained products were showing in an additional tab on product\'s page.', 'woocommerce-chained-products' ); ?></p>
					</div>
					<div class="col last-feature">
						<p><?php echo esc_html__( 'Now the shortcode gives you more flexibility & control on "How & where to display chained products". You can also set whether to show chained products in list format or grid format, show/hide price & quantity.', 'woocommerce-chained-products' ); ?></p>
					</div>
				</div>
			</div>
			<div>
				<h3 align="center"><?php echo esc_html__( 'Possible Usage', 'woocommerce-chained-products' ); ?></h3><br>
				<div>
					<div class="feature-section col three-col">
						<div class="col">
							<p><code>[chained_products]</code></p>
							<img src="https://docs.woocommerce.com/wp-content/uploads/2012/05/default-shortcode.png" />
						</div>
						<div class="col">
							<p><code>[chained_products price="no"]</code></p>
							<img src="https://docs.woocommerce.com/wp-content/uploads/2012/05/cp-shortcode-price-no.png" />
						</div>
						<div class="col last-feature">
							<p><code>[chained_products price="yes" quantity="no" style="grid"]</code></p>
							<img src="https://docs.woocommerce.com/wp-content/uploads/2012/05/cp-shortcode-price-yes-qty-no-style-grid.png" />
						</div>
					</div>
					<div class="feature-section col three-col">
						<div class="col">
							<p><code>[chained_products style="list"]</code></p>
							<img src="https://docs.woocommerce.com/wp-content/uploads/2012/05/cp-shortcode-style-list.png" />
						</div>
						<div class="col">
							<p><code>[chained_products quantity="no" style="list"]</code></p>
							<img src="https://docs.woocommerce.com/wp-content/uploads/2012/05/cp-shortcode-qty-no-style-list.png" />
						</div>
						<div class="col last-feature">
							<p><code>[chained_products price="no" quantity="yes" style="list"]</code></p>
							<img src="https://docs.woocommerce.com/wp-content/uploads/2012/05/cp-shortcode-price-no-qty-yes-style-list.png" />
						</div>
					</div>
				</div>
			</div>
			<div>
				<h3 align="center"><?php echo esc_html__( 'Shortcode Attributes', 'woocommerce-chained-products' ); ?></h3><br>
				<div>
					<table class="wp-list-table widefat striped">
						<thead>
							<tr>
								<th><?php echo esc_html__( 'Attributes', 'woocommerce-chained-products' ); ?></th>
								<th><?php echo esc_html__( 'Values', 'woocommerce-chained-products' ); ?></th>
								<th><?php echo esc_html__( 'Default', 'woocommerce-chained-products' ); ?></th>
								<th><?php echo esc_html__( 'Description', 'woocommerce-chained-products' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><code>price</code></td>
								<td><code>yes</code> / <code>no</code></td>
								<td><code>yes</code></td>
								<td><?php echo esc_html__( 'show / hide prices of chained products', 'woocommerce-chained-products' ); ?></td>
							</tr>
							<tr>
								<td><code>quantity</code></td>
								<td><code>yes</code> / <code>no</code></td>
								<td><code>yes</code></td>
								<td><?php echo esc_html__( 'show / hide quantities of chained products', 'woocommerce-chained-products' ); ?></td>
							</tr>
							<tr>
								<td><code>style</code></td>
								<td><code>grid</code> / <code>list</code></td>
								<td><code>grid</code></td>
								<td><?php echo esc_html__( 'Display chained products in Grid view / List view', 'woocommerce-chained-products' ); ?></td>
							</tr>
							<tr>
								<td><code>css_class</code></td>
								<td><?php echo esc_html__( 'any custom value', 'woocommerce-chained-products' ); ?></td>
								<td></td>
								<td><?php echo esc_html__( 'You can add your custom CSS classes here. It\'ll be applicable on container which holds chained products. You can add CSS properties to your custom class in your theme', 'woocommerce-chained-products' ); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<?php
	}


	/**
	 * Output the FAQ's screen.
	 */
	public function faqs_screen() {
		?>
		<script type="text/javascript">
			jQuery(document).on('ready', function(){
				jQuery('#menu-posts-product').find('a[href="edit.php?post_type=product"]').addClass('current');
				jQuery('#menu-posts-product').find('a[href="edit.php?post_type=product"]').parent().addClass('current');
			});
		</script>

		<div class="wrap about-wrap" style="max-width:unset;">

			<?php $this->intro(); ?>

			<h3><?php echo esc_html__( 'FAQ / Common Problems', 'woocommerce-chained-products' ); ?></h3>

			<?php
				$faqs = array(
					array(
						'que' => __( 'Chained Products\' fields are broken', 'woocommerce-chained-products' ),
						'ans' => __( 'Make sure you are using latest version of Chained Products. If the issue still persist, deactivate all plugins except WooCommerce & Chained Products. Recheck the issue, if the issue still persists, contact us. If the issue goes away, re-activate other plugins one-by-one & re-checking the fields, to find out which plugin is conflicting. Inform us about this issue.', 'woocommerce-chained-products' ),
					),
					array(
						'que' => __( 'Chained Products\' not visible on product page', 'woocommerce-chained-products' ),
						/* translators: Chained product shortcode */
						'ans' => sprintf( __( 'Re-check product\'s setting & try to find shortcode %s in description or short description field. If the shortcode is not available add it manually. You may also get a notification asking to insert shortcode for chained products. If so, click the link on notification & save the product. If shorcode is already available, then remove it, & type it again.', 'woocommerce-chained-products' ), '<code>[chained_products]</code>' ),
					),
					array(
						'que' => __( 'Chained Products\' are not loading on product page OR it is showing incorrect data related to chained items', 'woocommerce-chained-products' ),
						'ans' => __( 'First you need to verify that it is not conflicting with any other plugin. You can follow same steps as mentioned in earlier FAQ which asks to deactivate all plugins except Chained Products. It can also be related to themes. To verify this, you can switch to other themes.', 'woocommerce-chained-products' ),
					),
					array(
						'que' => __( 'Unable to increase quantity or add product to cart', 'woocommerce-chained-products' ),
						'ans' => __( 'A Chained parent can be attached with many other products. Manage stocks might be enabled in those products. If child item is available in limited quantity, it\'ll allow you to add main product only upto that limit, even if main product has sufficient stock.', 'woocommerce-chained-products' ),
					),

				);

				$faqs = array_chunk( $faqs, 2 );

				echo '<div>';
			foreach ( $faqs as $fqs ) {
				echo '<div class="two-col">';
				foreach ( $fqs as $index => $faq ) {
					echo '<div' . ( ( 1 === $index ) ? ' class="col last-feature"' : ' class="col"' ) . '>';
					echo '<h4>' . $faq['que'] . '</h4>'; // WPCS: XSS ok.
					echo '<p>' . $faq['ans'] . '</p>'; // WPCS: XSS ok.
					echo '</div>';
				}
				echo '</div>';
			}
				echo '</div>';
			?>

		</div>
		<div class="clear"></div>
		<div align="center">
			<p>
				<?php
					/* translators: WooCommerce support link */
					echo sprintf( __( 'If you are facing any other issues, please %s from your WooCommerce account.', 'woocommerce-chained-products' ), '<a href="https://woocommerce.com/my-account/create-a-ticket/">' . esc_html__( 'submit a ticket', 'woocommerce-chained-products' ) . '</a>' ); // WPCS: XSS ok.
				?>
			</p>
		</div>

		<?php
	}


	/**
	 * Sends user to the welcome page on first activation.
	 */
	public function cp_welcome() {

		if ( ! get_transient( '_chained_products_activation_redirect' ) ) {
			return;
		}

		// Delete the redirect transient.
		delete_transient( '_chained_products_activation_redirect' );

		wp_safe_redirect( admin_url( 'admin.php?page=cp-about' ) );
		exit;

	}
}

new CP_Admin_Welcome();
