<?php
/**
 * Renders the info tab
 *
 * @package Solr_Power
 */

?>
<div id="solr_info" class="solrtab active">
	<?php
	$server_info = SolrPower_Api::get_instance()->get_server_info();
	?>
	<div class="solr-display">
		<div>
			<h3>Solr Configuration</h3>

			<ul>
				<li><strong>Ping Status:</strong>
					<?php echo ( $server_info['ping_status'] ) ? '<span class="solr-green">Successful</span>' : '<span class="solr-red">Failed</span>'; ?>
				</li>

				<li><strong>Solr Server IP address:</strong>
					<?php echo esc_html( $server_info['ip_address'] ); ?>
				</li>
				<li>
					<strong>Solr Server Port:</strong>
					<?php echo esc_html( $server_info['port'] ); ?>
				</li>
				<li>
					<strong>Solr Server Path:</strong>
					<?php echo esc_html( $server_info['path'] ); ?>
				</li>
			</ul>

		</div>
	</div>
	<?php if ( $server_info['ping_status'] ) { ?>
		<div class="solr-display">
			<div>
				<h3>Indexing Stats by Post Type</h3>
				<ul>
					<?php
					foreach ( SolrPower_Api::get_instance()->index_stats() as $type => $stat ) {
						/**
						 * Allows additional information to be included.
						 *
						 * @param integer $stat Stat to be displayed.
						 * @param string  $type Type of stat.
						 */
						$stat = apply_filters( 'solr_index_stat', $stat, $type );
						?>
						<li>
							<strong><?php echo esc_html( $type ); ?>:</strong>
							<?php echo esc_html( $stat ); ?>
						</li>
					<?php } ?>
				</ul>

			</div>
		</div>
	<?php } ?>
	<br class="clear">
</div>
