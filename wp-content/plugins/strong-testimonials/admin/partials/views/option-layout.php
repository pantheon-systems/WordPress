<th>
	<?php _e( 'Layout', 'strong-testimonials' ); ?>
</th>
<td colspan="2">
    <div class="section-radios layout-section">

        <!-- Inputs -->
        <div class="radio-buttons">
            <ul class="radio-list layout-list">
					<li>
                    <input type="radio"
                           id="view-layout-normal"
                           name="view[data][layout]"
                           value="" <?php checked( false, $view['layout'] ); ?>>
						<label for="view-layout-normal">
							<?php _e( 'normal', 'strong-testimonials' ); ?>
						</label>
					</li>
					<li>
                    <input type="radio"
                           id="view-layout-masonry"
                           name="view[data][layout]"
                           value="masonry" <?php checked( 'masonry', $view['layout'] ); ?>>
						<label for="view-layout-masonry">
							<?php _e( 'Masonry', 'strong-testimonials' ); ?>
						</label>
					</li>
					<li>
                    <input type="radio"
                           id="view-layout-columns"
                           name="view[data][layout]"
                           value="columns" <?php checked( 'columns', $view['layout'] ); ?>>
						<label for="view-layout-columns">
							<?php _e( 'columns', 'strong-testimonials' ); ?>
						</label>
					</li>
					<li>
                    <input type="radio"
                           id="view-layout-grid"
                           name="view[data][layout]"
                           value="grid" <?php checked( 'grid', $view['layout'] ); ?>>
						<label for="view-layout-grid">
							<?php _e( 'grid', 'strong-testimonials' ); ?>
						</label>
					</li>
				</ul>
			</div>

			<!-- Layout Info -->
        <div>
            <div class="radio-description" id="view-layout-info">

				<div class="layout-description view-layout-normal">
                    <p><?php _e( 'A single column.', 'strong-testimonials' ); ?></p>
                </div>

                <div class="layout-description view-layout-masonry">
                    <p><?php printf( __( 'A cascading, responsive grid using the jQuery plugin <a href="%s" target="_blank">Masonry</a>.', 'strong-testimonials' ), esc_url( 'http://masonry.desandro.com/' ) ); ?></p>
                    <p><?php _e( 'The universal solution that works well regardless of testimonial lengths.', 'strong-testimonials' ); ?></p>
                    <p><?php _e( 'Not compatible with pagination.', 'strong-testimonials' ); ?></p>
                </div>

                <div class="layout-description view-layout-columns">
                    <p><?php printf( __( 'Using <a href="%s" target="_blank">CSS multi-column</a>. Fill from top to bottom, then over to next column.', 'strong-testimonials' ), esc_url( 'https://css-tricks.com/guide-responsive-friendly-css-columns/' ) ); ?></p>
                    <p><?php _e( 'Works well with both long and short testimonials.', 'strong-testimonials' ); ?></p>
                    <p><?php _e( 'Compatible with pagination.', 'strong-testimonials' ); ?></p>
                </div>

                <div class="layout-description view-layout-grid">
                    <p><?php
						$url = 'https://scotch.io/tutorials/a-visual-guide-to-css3-flexbox-properties';
						printf( __( 'Using <a href="%s" target="_blank">CSS flexbox</a>.', 'strong-testimonials' ), esc_url( $url ) );
						?></p>
                    <p><?php _e( 'Testimonials will be equal height so this works best when they are about the same length either naturally or using excerpts.', 'strong-testimonials' ); ?></p>
                    <p><?php _e( 'Compatible with pagination.', 'strong-testimonials' ); ?></p>
                </div>

            </div>

            <!-- Column selector -->
            <div class="radio-description options" id="column-count-wrapper">
                <div>
                    <label for="view-column-count"><?php _e( 'number of columns', 'strong-testimonials' ); ?></label>
                    <select id="view-column-count" name="view[data][column_count]">
                        <option value="2" <?php selected( $view['column_count'], 2 ); ?>>2</option>
                        <option value="3" <?php selected( $view['column_count'], 3 ); ?>>3</option>
                        <option value="4" <?php selected( $view['column_count'], 4 ); ?>>4</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Examples -->
        <div>
            <div class="layout-example view-layout-normal">
					<div class="example-container">
						<div class="box"><span>1</span></div>
						<div class="box size2"><span>2</span></div>
						<div class="box"><span>3</span></div>
						<div class="box size2"><span>4</span></div>
						<div class="box"><span>5</span></div>
					</div>
				</div>
            <div class="layout-example view-layout-masonry">
					<div class="example-container col-2">
						<div class="grid-sizer"></div>
						<div class="box"><span>1</span></div>
						<div class="box size2"><span>2</span></div>
						<div class="box"><span>3</span></div>
						<div class="box size3"><span>4</span></div>
						<div class="box"><span>5</span></div>
						<div class="box size2"><span>6</span></div>
						<div class="box"><span>7</span></div>
						<div class="box size3"><span>8</span></div>
						<div class="box"><span>9</span></div>
					</div>
				</div>
            <div class="layout-example view-layout-columns">
					<div class="example-container col-2">
						<div class="box"><span>1</span></div>
						<div class="box size2"><span>2</span></div>
						<div class="box"><span>3</span></div>
						<div class="box size3"><span>4</span></div>
						<div class="box"><span>5</span></div>
						<div class="box size2"><span>6</span></div>
						<div class="box"><span>7</span></div>
						<div class="box size3"><span>8</span></div>
						<div class="box"><span>9</span></div>
					</div>
				</div>
            <div class="layout-example view-layout-grid">
					<div class="example-container col-2">
						<div class="box"><span>1</span></div>
						<div class="box"><span>2</span></div>
						<div class="box"><span>3</span></div>
						<div class="box"><span>4</span></div>
						<div class="box"><span>5</span></div>
						<div class="box"><span>6</span></div>
						<div class="box"><span>7</span></div>
						<div class="box"><span>8</span></div>
						<div class="box"><span>9</span></div>
					</div>
				</div>
		</div>

	</div>
</td>
