<th>
		<?php _e( 'Background', 'strong-testimonials' ); ?>
</th>
<td>
    <div class="section-radios background-section">

        <!-- Inputs -->
        <div class="radio-buttons">
            <ul class="radio-list background-list">
					<li>
                    <input type="radio"
                           id="bg-none"
                           name="view[data][background][type]"
                           value="" <?php checked( $view['background']['type'], '' ); ?>>
                    <label for="bg-none">
						<?php _e( 'inherit from theme', 'strong-testimonials' ); ?>
                    </label>
					</li>
					<li>
                    <input type="radio"
                           id="bg-single"
                           name="view[data][background][type]"
                           value="single" <?php checked( $view['background']['type'], 'single' ); ?>>
                    <label for="bg-single">
						<?php _e( 'single color', 'strong-testimonials' ); ?>
                    </label>
					</li>
					<li>
                    <input type="radio"
                           id="bg-gradient"
                           name="view[data][background][type]"
                           value="gradient" <?php checked( $view['background']['type'], 'gradient' ); ?>>
                    <label for="bg-gradient">
						<?php _e( 'gradient', 'strong-testimonials' ); ?>
                    </label>
					</li>
					<li>
                    <input type="radio"
                           id="bg-preset"
                           name="view[data][background][type]"
                           value="preset" <?php checked( $view['background']['type'], 'preset' ); ?>>
                    <label for="bg-preset">
						<?php _e( 'preset', 'strong-testimonials' ); ?>
                    </label>
					</li>
				</ul>
			</div>

        <!-- Background info -->
        <div class="radio-description" id="view-background-info">

            <div class="background-description bg-none">
                <div class="description-inner options">
                    <div>
						<?php _e( 'No options', 'strong-testimonials' ); ?>
                    </div>
                </div>
            </div>

						<div class="background-description bg-single">
                <div class="description-inner options">
                    <div>
								<label>
                            <input type="text"
                                   id="bg-color"
                                   name="view[data][background][color]"
                                   value="<?php echo $view['background']['color']; ?>"
                                   class="wp-color-picker-field">
								</label>
							</div>
						</div>
            </div>

						<div class="background-description bg-gradient">
                <div class="description-inner options">
                    <div>
                        <div class="color-picker-wrap">
                            <div>
											<label for="bg-gradient1"><?php _e( 'From top', 'strong-testimonials' ); ?></label>
										</div>
                            <div>
                                <input type="text"
                                       id="bg-gradient1"
                                       name="view[data][background][gradient1]"
                                       value="<?php echo $view['background']['gradient1']; ?>"
                                       class="wp-color-picker-field gradient">
                            </div>
										</div>
									</div>
                </div>
                <div class="description-inner options">
                    <div>
                        <div class="color-picker-wrap">
                            <div>
											<label for ="bg-gradient2"><?php _e( 'To bottom', 'strong-testimonials' ); ?></label>
										</div>
                            <div>
                                <input type="text"
                                       id="bg-gradient2"
                                       name="view[data][background][gradient2]"
                                       value="<?php echo $view['background']['gradient2']; ?>"
                                       class="wp-color-picker-field gradient">
										</div>
									</div>
								</div>

							</div>
						</div>

						<div class="background-description bg-preset">
                <div class="description-inner options">
                    <div>
								<label for="view-background-preset">
									<select id="view-background-preset" name="view[data][background][preset]">
										<?php
										$presets = wpmtst_get_background_presets();
										$current_preset = ( isset( $view['background']['preset'] ) && $view['background']['preset'] ) ? $view['background']['preset'] : '';
										echo '<option value="" ' . selected( $current_preset, '', false ) . '>&mdash;</option>';
										foreach ( $presets as $key => $preset ) {
											echo '<option value="' . $key . '" ' . selected( $current_preset, $key, false ) . '>' . $preset['label'] . '</option>';
										}
										?>
									</select>
								</label>
							</div>
						</div>

					</div>


			</div>

	</div>

</td>

<td rowspan="2" class="rowspan">
    <div id="view-color-preview" class="table-cell">

        <div class="background-preview-wrap">

            <div id="background-preview">
                Lorem ipsum dolor sit amet, accusam complectitur an eos. No vix perpetua adolescens, vix vidisse maiorum
                in. No erat falli scripta qui, vis ubique scripta electram ad. Vix prompta adipisci no, ad vidisse
                expetendis.
            </div>

        </div>

    </div>
</td>