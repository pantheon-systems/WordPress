<div class="oceanwp-mb-desc">
	<# if ( data.label ) { #>
		<span class="butterbean-label">{{ data.label }}</span>
	<# } #>

	<# if ( data.description ) { #>
		<span class="butterbean-description">{{{ data.description }}}</span>
	<# } #>
</div>

<div class="oceanwp-mb-field">
	<select class="widefat butterbean-multiple-select" multiple="multiple" name="{{ data.field_name }}[]" {{{ data.attr }}}>

		<# _.each( data.choices, function( label, choice ) { #>

			<option value="{{ choice }}" <# if ( ( -1 !== _.indexOf( data.value, choice ) ) ) { #> selected="selected" <# } #>>{{ label }}</option>

		<# } ) #>

	</select>
</div>