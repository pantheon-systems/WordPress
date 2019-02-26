<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 */
if ( 'yes' === $atts['show_filter'] && ! empty( $filter_terms ) ) :
	$unique_terms = array_unique( $filter_terms );
	$terms_ids = ! empty( $atts['exclude_filter'] ) ? array_diff( $unique_terms, // Posts filter terms
		array_map( 'abs', preg_split( '/\s*\,\s*/', $atts['exclude_filter'] ) ) ) : $unique_terms;
	$terms = count( $terms_ids ) > 0 ? get_terms( $atts['filter_source'], array(
		'include' => implode( ',', $terms_ids ),
	) ) : array();

	$filter_default = $atts['filter_default_title'];
	if ( empty( $filter_default ) ) {
		$filter_default = __( 'All', 'js_composer' );
	}
	if ( 'dropdown' !== $atts['filter_style'] ) {
		echo '<ul class="vc_grid-filter vc_clearfix vc_grid-filter-' . esc_attr( $atts['filter_style'] ) . ' vc_grid-filter-size-' . esc_attr( $atts['filter_size'] ) . ' vc_grid-filter-' . esc_attr( $atts['filter_align'] ) . ' vc_grid-filter-color-' . esc_attr( $atts['filter_color'] ) . '" data-vc-grid-filter="' . esc_attr( $atts['filter_source'] ) . '"><li class="vc_active vc_grid-filter-item"><span data-vc-grid-filter-value="*">';
		echo esc_attr( $filter_default );;
		echo '</span></li>';
		foreach ( $terms as $term ) {
			echo '<li class="vc_grid-filter-item"><span' . ' data-vc-grid-filter-value=".vc_grid-term-' . $term->term_id . '">';
			echo esc_attr( $term->name );
			echo '</span><!-- fix whitespace
				--></li>';
		}
		echo '</ul>';
	}

	?>
	<!-- for responsive vc_responsive !-->
	<div class="<?php echo 'dropdown' === $atts['filter_style'] ? 'vc_grid-filter-dropdown' : 'vc_grid-filter-select'; ?> vc_grid-filter-<?php echo esc_attr( $atts['filter_align'] ); ?> vc_grid-filter-color-<?php echo esc_attr( $atts['filter_color'] ); ?>" data-vc-grid-filter-select="<?php echo esc_attr( $atts['filter_source'] ) ?>">
		<div class="vc_grid-styled-select"><select data-filter="<?php echo esc_attr( $atts['filter_source'] ) ?>">
				<option class="vc_active" value="*"><?php echo esc_attr( $filter_default ); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
				<?php foreach ( $terms as $term ) :
					echo '<option value=".vc_grid-term-' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>';
				endforeach; ?>
			</select><i class="vc_arrow-icon-navicon"></i>
		</div>
	</div>
<?php endif;

