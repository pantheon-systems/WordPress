<label>
	<div class="oceanwp-mb-desc">
		<# if ( data.label ) { #>
			<span class="butterbean-label">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="butterbean-description">{{{ data.description }}}</span>
		<# } #>
	</div>

	<div class="oceanwp-mb-field">
		<ul>

			<# if ( data.family && data.family.choices ) { #>
				<li class="typography-font-family">

					<# if ( data.family.label ) { #>
						<span class="label">{{ data.family.label }}</span>
					<# } #>

					<select class="widefat butterbean-select" name="{{ data.family.name }}">

						<# _.each( data.family.choices, function( label, choice ) { #>
							<option value="{{ choice }}" <# if ( choice === data.family.value ) { #> selected="selected" <# } #>>{{ label }}</option>
						<# } ) #>

					</select>
				</li>
			<# } #>

			<# if ( data.size ) { #>
				<li class="typography-font-size">

					<# if ( data.size.label ) { #>
						<span class="label">{{ data.size.label }}</span>
					<# } #>

					<input type="text" name="{{ data.size.name }}" value="{{ data.size.value }}" placeholder="<?php esc_html_e( 'px - em - rem', 'ocean-portfolio' ); ?>" />

				</li>
			<# } #>

			<# if ( data.weight && data.weight.choices ) { #>
				<li class="typography-font-weight">

					<# if ( data.weight.label ) { #>
						<span class="label">{{ data.weight.label }}</span>
					<# } #>

					<select class="widefat butterbean-select" name="{{ data.weight.name }}">

						<# _.each( data.weight.choices, function( label, choice ) { #>

							<option value="{{ choice }}" <# if ( choice === data.weight.value ) { #> selected="selected" <# } #>>{{ label }}</option>

						<# } ) #>

					</select>
				</li>
			<# } #>

			<# if ( data.style && data.style.choices ) { #>
				<li class="typography-font-style">

					<# if ( data.style.label ) { #>
						<span class="label">{{ data.style.label }}</span>
					<# } #>

					<select class="widefat butterbean-select" name="{{ data.style.name }}">

						<# _.each( data.style.choices, function( label, choice ) { #>

							<option value="{{ choice }}" <# if ( choice === data.style.value ) { #> selected="selected" <# } #>>{{ label }}</option>

						<# } ) #>

					</select>
				</li>
			<# } #>

			<# if ( data.transform && data.transform.choices ) { #>
				<li class="typography-text-transform">

					<# if ( data.transform.label ) { #>
						<span class="label">{{ data.transform.label }}</span>
					<# } #>

					<select class="widefat butterbean-select" name="{{ data.transform.name }}">

						<# _.each( data.transform.choices, function( label, choice ) { #>

							<option value="{{ choice }}" <# if ( choice === data.transform.value ) { #> selected="selected" <# } #>>{{ label }}</option>

						<# } ) #>

					</select>
				</li>
			<# } #>

			<# if ( data.line_height ) { #>
				<li class="typography-line-height">

					<# if ( data.line_height.label ) { #>
						<span class="label">{{ data.line_height.label }}</span>
					<# } #>

					<input type="text" name="{{ data.line_height.name }}" value="{{ data.line_height.value }}" placeholder="<?php esc_html_e( 'px - em - rem', 'ocean-portfolio' ); ?>" />

				</li>
			<# } #>

			<# if ( data.spacing ) { #>
				<li class="typography-letter-spacing">

					<# if ( data.spacing.label ) { #>
						<span class="label">{{ data.spacing.label }}</span>
					<# } #>

					<input type="text" name="{{ data.spacing.name }}" value="{{ data.spacing.value }}" placeholder="<?php esc_html_e( 'px - em - rem', 'ocean-portfolio' ); ?>" />

				</li>
			<# } #>

		</ul>
	</div>
</label>