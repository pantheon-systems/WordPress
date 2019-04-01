<div id="affwp-affiliate-dashboard-visits" class="affwp-tab-content">

	<h4><?php _e( 'Referral URL Visits', 'affiliate-wp' ); ?></h4>

	<span id="affwp-table-summary" class="screen-reader-text">
		<?php _e( 'Column one lists the visit URL in relative format, column two lists the referrer, and column three indicates whether the visit converted into a referral.', 'affiliate-wp' ); ?>
	</span>

	<?php
	affwp_enqueue_style( 'dashicons', 'visits' );

	$per_page = 30;
	$page     = affwp_get_current_page_number();
	$pages    = absint( ceil( affwp_get_affiliate_visit_count( affwp_get_affiliate_id() ) / $per_page ) );
	$visits   = affiliate_wp()->visits->get_visits(
		array(
			'number'       => $per_page,
			'offset'       => $per_page * ( $page - 1 ),
			'affiliate_id' => affwp_get_affiliate_id(),
		)
	);
	?>

	<table id="affwp-affiliate-dashboard-visits" class="affwp-table affwp-table-responsive" aria-describedby="affwp-table-summary">
		<thead>
			<tr>
				<th class="visit-url"><?php _e( 'URL', 'affiliate-wp' ); ?></th>
				<th class="referring-url"><?php _e( 'Referring URL', 'affiliate-wp' ); ?></th>
				<th class="referral-status"><?php _e( 'Converted', 'affiliate-wp' ); ?></th>
				<th class="visit-date"><?php _e( 'Date', 'affiliate-wp' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php if ( $visits ) : ?>

				<?php foreach ( $visits as $visit ) : ?>
					<tr>
						<td data-th="<?php _e( 'URL', 'affiliate-wp' ); ?>">
							<a href="<?php echo esc_url( $visit->url ); ?>" title="<?php echo esc_attr( $visit->url ); ?>">
								<?php echo affwp_make_url_human_readable( $visit->url ); ?>
							</a>
						</td>
						<td data-th="<?php _e( 'Referring URL', 'affiliate-wp' ); ?>"><?php echo ! empty( $visit->referrer ) ? $visit->referrer : __( 'Direct traffic', 'affiliate-wp' ); ?></td>
						<td data-th="<?php _e( 'Converted', 'affiliate-wp' ); ?>">
							<?php $converted = ! empty( $visit->referral_id ) ? 'yes' : 'no'; ?>
							<span class="visit-converted <?php echo esc_attr( $converted ); ?>" aria-label="<?php printf( esc_attr__( 'Visit converted: %s', 'affiliate-wp' ), $converted ); ?>">
								<i></i>
							</span>
						</td>
						<td data-th="<?php _e( 'Date', 'affiliate-wp' ); ?>">
							<?php echo esc_html( $visit->date_i18n( 'datetime' ) ); ?>
						</td>
					</tr>
				<?php endforeach; ?>

			<?php else : ?>

				<tr>
					<td class="affwp-table-no-data" colspan="4"><?php _e( 'You have not received any visits yet.', 'affiliate-wp' ); ?></td>
				</tr>

			<?php endif; ?>
		</tbody>
	</table>

	<?php if ( $pages > 1 ) : ?>

		<p class="affwp-pagination">
			<?php
			echo paginate_links(
				array(
					'current'      => $page,
					'total'        => $pages,
					'add_fragment' => '#affwp-affiliate-dashboard-visits',
					'add_args'     => array(
						'tab' => 'visits',
					),
				)
			);
			?>
		</p>

	<?php endif; ?>

</div>
