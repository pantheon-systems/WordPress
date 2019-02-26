<?php global $affwp_creative_atts;?>
<div class="affwp-creative<?php echo esc_attr( $affwp_creative_atts['id_class'] ); ?>">

	<?php if ( ! empty( $affwp_creative_atts['desc'] ) ) : ?>
		<p class="affwp-creative-desc"><?php echo $affwp_creative_atts['desc']; ?></p>
	<?php endif; ?>

	<?php if ( $affwp_creative_atts['preview'] != 'no' ) : ?>

		<?php
		// Image preview - using ID of image from media library
		if ( $affwp_creative_atts['image_attributes'] ) : ?>
		<p>
			<a href="<?php echo esc_url( affwp_get_affiliate_referral_url( array( 'base_url' => $affwp_creative_atts['url'] ) ) ); ?>" title="<?php echo esc_attr( $affwp_creative_atts['text'] ); ?>">
				<img src="<?php echo esc_attr( $affwp_creative_atts['image_attributes'][0] ); ?>" width="<?php echo esc_attr( $affwp_creative_atts['image_attributes'][1] ); ?>" height="<?php echo esc_attr( $image_attributes[2] ); ?>" alt="<?php echo esc_attr( $text ); ?>">
			</a>
		</p>

		<?php
		// Image preview - External image URL or picked from media library
		elseif ( $affwp_creative_atts['image_link'] ) :
			$image = $affwp_creative_atts['image_link'];
		?>
			<p>
				<a href="<?php echo esc_url( affwp_get_affiliate_referral_url( array( 'base_url' => $affwp_creative_atts['url'] ) ) ); ?>" title="<?php echo esc_attr( $affwp_creative_atts['text'] ); ?>">
					<img src="<?php echo esc_attr( $affwp_creative_atts['image_link'] ); ?>" alt="<?php echo esc_attr( $affwp_creative_atts['text'] ); ?>">
				</a>
			</p>

		<?php else : // text link preview ?>
			<p>
				<a href="<?php echo esc_url( affwp_get_affiliate_referral_url( array( 'base_url' => $affwp_creative_atts['url'] ) ) ); ?>" title="<?php echo esc_attr( $affwp_creative_atts['text'] ); ?>"><?php echo esc_attr( $affwp_creative_atts['text'] ); ?></a>
			</p>
		<?php endif; ?>

	<?php endif; ?>

	<?php
		echo apply_filters( 'affwp_affiliate_creative_text', '<p>' . __( 'Copy and paste the following:', 'affiliate-wp' ) . '</p>' );

		// Image - media library
		if ( $affwp_creative_atts['image_attributes'] ) {
			$image_or_text = '<img src="' . esc_attr( $affwp_creative_atts['image_attributes'][0] ) . '" alt="' . esc_attr( $affwp_creative_atts['text'] ) .'" />';
		}
		// Image - External URL
		elseif ( $affwp_creative_atts['image_link'] ) {
			$image_or_text = '<img src="' . esc_attr( $affwp_creative_atts['image_link'] ) . '" alt="' . esc_attr( $affwp_creative_atts['text'] ) .'" />';
		}
		// Show site name when no image
		else {
			$image_or_text = esc_attr( $affwp_creative_atts['text'] );
		}
	?>

	<?php
		$creative = '<a href="' . esc_url( affwp_get_affiliate_referral_url( array( 'base_url' => $affwp_creative_atts['url'] ) ) ) .'" title="' . esc_attr( $affwp_creative_atts['text'] ) . '">' . $image_or_text . '</a>';
		echo '<pre><code>' . esc_html( $creative ) . '</code></pre>';
	?>

</div>
