<?php

class WPML_TP_Polling_Box {

	/**
	 * Renders the html for the TP polling pickup box
	 *
	 * @return string
	 */
	public function render() {
		$logger_settings = new WPML_Jobs_Fetch_Log_Settings();

		ob_start();
		?>
		<div id="icl_tm_pickup_wrap" class="clear">
			<div class="wpml-tm-dashboard-pickup">
				<div id="icl_tm_pickup_wrap_errors" class="icl_tm_pickup_wrap"
				     style="display:none"><p></p></div>
				<div id="icl_tm_pickup_wrap_completed"
				     class="icl_tm_pickup_wrap" style="display:none"><p></p>
				</div>
				<div id="icl_tm_pickup_wrap_cancelled"
				     class="icl_tm_pickup_wrap" style="display:none"><p></p>
				</div>
				<div id="icl_tm_pickup_wrap_error_submitting"
				     class="icl_tm_pickup_wrap" style="display:none"><p></p>
				</div>

                <p><span id="icl_pickup_nof_jobs"></span> <input type="button" class="button-secondary"
                          data-reloading-text="<?php esc_attr_e( 'Reloading:',
                              'wpml-translation-management' ) ?>" value=""
                          id="icl_tm_get_translations"/>
                    <span id="icl_pickup_last_pickup" class="wpml-tm-dashboard-last-pickup"></span>
                    <br />
                    <a href="<?php echo esc_attr( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=' . $logger_settings->get_ui_key() );?>">
	                    <?php esc_html_e( 'Open the content updates log', 'wpml-translation-management' ); ?>
                    </a>
	                <?php
	                if ( WP_DEBUG ) {
		                ?>&nbsp;			<a target="_blank"
				                                 class="button-primary"
				                                 href="<?php echo esc_attr( 'admin.php?page='
		                                                            . WPML_TM_FOLDER
		                                                            . '/menu/main.php&sm=com-log' ); ?>">
			                <?php esc_html_e( 'Open the communication log', 'wpml-translation-management' ); ?>
			                </a>
		                <?php
	                }
	                ?>
                </p>
			</div>
			<div id="tp_polling_job" style="display:none"></div>
		</div>
		<?php
		wp_nonce_field( 'icl_pickup_translations_nonce',
			'_icl_nonce_pickup_t' );
		wp_nonce_field( 'icl_populate_translations_pickup_box_nonce',
			'_icl_nonce_populate_t' );
		wp_enqueue_script( 'wpml-tp-polling-setup' );

		return ob_get_clean();
	}
}