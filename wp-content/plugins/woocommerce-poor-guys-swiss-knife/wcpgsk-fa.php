<?php

function woocommerce_quantity_input( $args = array(), $product = null, $echo = true ) {
	if ( is_null( $product ) )
		$product = $GLOBALS['product'];
	$options = get_option( 'wcpgsk_settings' );
	$product_id = $product->post->ID;		
	$selectqty = get_post_meta($product_id, '_wcpgsk_selectqty', true);		
	//var_dump( $args );
	$ival = apply_filters( 'woocommerce_quantity_input_min', '', $product );
	if ( !$ival ) :
		$ival = apply_filters( 'woocommerce_quantity_input_step', '1', $product );
	endif;
	$stepval = apply_filters( 'woocommerce_quantity_input_step', '1', $product );
	$maxval = apply_filters( 'woocommerce_quantity_input_max', '', $product );
	$mval = apply_filters( 'woocommerce_quantity_input_min', '', $product );
	if ( !empty($args) && isset($args['input_name']) && strpos($args['input_name'], 'quantity[') !== false ) :
		
		if ( !$ival || (is_numeric( $ival ) && $ival < 1 ) ) :
			if ( $stepval <= $maxval || !$maxval ) :
				$ival = $stepval;
			else :
				$ival = 1;
			endif;
		endif;
		//$mval = 0;
	elseif ( !$ival || (is_numeric( $ival ) && $ival < 1 ) ) :
		if ( $stepval <= $maxval || !$maxval ) :
			$ival = $stepval;
		else :
			$ival = 1;
		endif;
		//$mval = 0;
	endif;
	$defaults = array(
		'input_name'  	=> 'quantity',
		'input_value'  	=> $ival,
		'max_value'  	=> $maxval,
		'min_value'  	=> $mval,
		'step' 		=> $stepval,
		'style'		=> apply_filters( 'woocommerce_quantity_style', 'float:left; margin-right:10px;', $product )
	);
	if ( !empty($args) && isset($args['input_name']) && !empty($args['input_name']) ) :
		$defaults['input_name'] = $args['input_name'];
	endif;
	
	if ( $maxval ) :
		unset( $args['max_value'] );
	endif;
	$args = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults ), $product );
	
	
	if ( isset($options['cart']['minmaxstepproduct']) && $options['cart']['minmaxstepproduct'] == 1 && isset($selectqty) && $selectqty == 'yes' ) :
	
		$minqty = get_post_meta($product_id, '_wcpgsk_minqty', true);
		$maxqty = get_post_meta($product_id, '_wcpgsk_maxqty', true);
		$stepqty = get_post_meta($product_id, '_wcpgsk_stepqty', true);		
		
		if ( ! empty( $args['min_value'] ) )
			$min = $args['min_value'];
		else $min = $minqty > 0 ? $minqty : 0;

		if ( ! empty( $args['max_value'] ) )
			$max = $args['max_value'];
		else $max = $maxqty > 0 ? $maxqty : '';

		if ( ! empty( $args['step'] ) )
			$step = $args['step'];
		else $step = $stepqty > 0 ? $stepqty : 1;
		if ( ( !empty( $min ) && is_numeric( $min) ) && ( !empty( $max ) && is_numeric( $max ) ) ) :
			$options = '';
			for ( $count = $min; $count <= $max; $count = $count+$step ) {
				$options .= '<option value="' . $count . '" ' . selected($count, $args['input_value'], false) . '>' . $count . '</option>';
			}
			$retstr = '<div class="quantity_select" style="' . $args['style'] . '"><select name="' . esc_attr( $args['input_name'] ) . '" title="' . _x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) . '" class="qty">' . $options . '</select></div>';
			if ( $echo ) :
				echo $retstr;
			else :
				return $retstr;
			endif;
			
		
		else :
			extract( $args, EXTR_OVERWRITE );
			if ( function_exists( 'wc_get_template' ) ) :				
				ob_start();
				wc_get_template( 'global/quantity-input.php', $args );
				if ( $echo ) {
					echo ob_get_clean();
				} else {
					return ob_get_clean();
				}
			else :
				?>
					<div class="quantity"><input type="number" step="<?php echo esc_attr( $step ); ?>" <?php if ( is_numeric( $min_value ) ) : ?>min="<?php echo esc_attr( $min_value ); ?>"<?php endif; ?> <?php if ( is_numeric( $max_value ) ) : ?>max="<?php echo esc_attr( $max_value ); ?>"<?php endif; ?> name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" class="input-text qty text" size="4" /></div>
				<?php
			endif;
		endif;
	else :
		extract( $args, EXTR_OVERWRITE );
		if ( function_exists( 'wc_get_template' ) ) :				
			ob_start();
			wc_get_template( 'global/quantity-input.php', $args );
			if ( $echo ) {
				echo ob_get_clean();
			} else {
				return ob_get_clean();
			}
		else :
			?>
				<div class="quantity"><input type="number" step="<?php echo esc_attr( $step ); ?>" <?php if ( is_numeric( $min_value ) ) : ?>min="<?php echo esc_attr( $min_value ); ?>"<?php endif; ?> <?php if ( is_numeric( $max_value ) ) : ?>max="<?php echo esc_attr( $max_value ); ?>"<?php endif; ?> name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" class="input-text qty text" size="4" /></div>
			<?php
		endif;
	endif;
}
