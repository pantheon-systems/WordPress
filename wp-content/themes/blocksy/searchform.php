<?php

/**
 * Search form
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

$placeholder = esc_attr_x('Search', 'placeholder', 'blocksy');

if (isset($args['search_placeholder'])) {
	$placeholder = $args['search_placeholder'];
}

if (isset($args['ct_product_price'])) {
	$show_product_price = $args['ct_product_price'];
} else {
	$show_product_price = get_theme_mod('searchProductPrice', 'no') === 'yes';
}

if (isset($args['search_live_results'])) {
	$has_live_results = $args['search_live_results'];
} else {
	$has_live_results = get_theme_mod('search_enable_live_results', 'yes');
}

$search_live_results_output = '';

if ($has_live_results === 'yes') {
	if (! isset($args['live_results_attr'])) {
		$args['live_results_attr'] = 'thumbs';
	}

	$live_results_attr = !empty($args['live_results_attr']) ? [$args['live_results_attr']] : [];

	if ( $show_product_price ) {
		array_push($live_results_attr, 'product_price');
	}

	$product_price_attr = $show_product_price ? 'visible' : 'hidden';

	$search_live_results_output = 'data-live-results="' . implode(':', $live_results_attr) . '"';
}

$class_output = '';

$any = [];

if (get_query_var('post_type') && ! is_array(get_query_var('post_type'))) {
	$any = explode(':', get_query_var('post_type'));
}

if (
	isset($_GET['ct_post_type'])
	&&
	$_GET['ct_post_type']
) {
	$any = explode(':', $_GET['ct_post_type']);
}

if (
	isset($args['ct_post_type'])
	&&
	$args['ct_post_type']
) {
	$any = $args['ct_post_type'];
}

if (
	isset($args['enable_search_field_class'])
	&&
	$args['enable_search_field_class']
) {
	$class_output = 'class="modal-field"';
}

$home_url = home_url('/');

if (function_exists('pll_home_url')) {
	$home_url = pll_home_url();
}

$icon = apply_filters(
	'blocksy:search-form:icon',
	'<svg class="ct-icon" aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M14.8,13.7L12,11c0.9-1.2,1.5-2.6,1.5-4.2c0-3.7-3-6.8-6.8-6.8S0,3,0,6.8s3,6.8,6.8,6.8c1.6,0,3.1-0.6,4.2-1.5l2.8,2.8c0.1,0.1,0.3,0.2,0.5,0.2s0.4-0.1,0.5-0.2C15.1,14.5,15.1,14,14.8,13.7z M1.5,6.8c0-2.9,2.4-5.2,5.2-5.2S12,3.9,12,6.8S9.6,12,6.8,12S1.5,9.6,1.5,6.8z"/></svg>'
);

if (isset($args['icon'])) {
	$icon = $args['icon'];
}

?>


<form role="search" method="get" class="search-form" action="<?php echo esc_url($home_url); ?>" aria-haspopup="listbox" <?php echo wp_kses_post($search_live_results_output) ?>>

	<input type="search" <?php echo $class_output ?> placeholder="<?php echo $placeholder; ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" title="<?php echo __('Search for...', 'blocksy') ?>" aria-label="<?php echo __('Search for...', 'blocksy') ?>">

	<button type="submit" class="search-submit" aria-label="<?php echo __('Search button', 'blocksy')?>">
		<?php
			/**
			 * Note to code reviewers: This line doesn't need to be escaped.
			 * The value used here escapes the value properly.
			 * It contains an inline SVG, which is safe.
			 */
			echo $icon
		?>

		<span data-loader="circles"><span></span><span></span><span></span></span>
	</button>

	<?php if (count($any) === 1) { ?>
		<input type="hidden" name="post_type" value="<?php echo $any[0] ?>">
	<?php } ?>

	<?php if (count($any) > 1) { ?>
		<input type="hidden" name="ct_post_type" value="<?php echo implode(':', $any) ?>">
	<?php } ?>

	<?php if ($show_product_price || is_customize_preview()) { ?>
		<input type="hidden" name="ct_product_price" value="<?php echo $show_product_price || is_customize_preview(); ?>">
	<?php } ?>

	<?php if ($has_live_results === 'yes') { ?>
		<div class="screen-reader-text" aria-live="polite" role="status">
			<?php echo __('No results', 'blocksy') ?>
		</div>
	<?php } ?>

</form>


