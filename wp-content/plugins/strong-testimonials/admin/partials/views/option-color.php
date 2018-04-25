<th>
		<?php _e( 'Font Color', 'strong-testimonials' ); ?>
</th>
<td>
    <div class="section-radios font-color-section">

        <div class="radio-buttons">
            <ul class="radio-list font-folor-list">
					<li>
                    <input type="radio"
                           id="fc-none"
                           name="view[data][font-color][type]"
                           value="" <?php checked( $view['font-color']['type'], '' ); ?>>
                    <label for="fc-none">
						<?php _e( 'inherit from theme', 'strong-testimonials' ); ?>
                    </label>
					</li>
					<li>
                    <input type="radio"
                           id="fc-custom"
                           name="view[data][font-color][type]"
                           value="custom" <?php checked( $view['font-color']['type'], 'custom' ); ?>>
                    <label for="fc-custom">
						<?php _e( 'custom', 'strong-testimonials' ); ?>
                    </label>
					</li>
				</ul>
			</div>

        <div class="radio-description" id="view-font-color-info">
            <div class="font-color-description fc-none">
                <div class="description-inner options">
                    <div>
						<?php _e( 'No options', 'strong-testimonials' ); ?>
                    </div>
                </div>
            </div>

						<div class="font-color-description fc-custom">
                <div class="description-inner options">
                    <div>
								<label>
                            <input type="text"
                                   id="fc-color"
                                   name="view[data][font-color][color]"
                                   value="<?php echo $view['font-color']['color']; ?>"
                                   class="wp-color-picker-field">
								</label>
							</div>
						</div>

					</div>

			</div>

		</div>
</td>
