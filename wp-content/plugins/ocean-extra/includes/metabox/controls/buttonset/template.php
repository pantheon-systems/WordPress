<div class="oceanwp-mb-desc">
	<# if ( data.label ) { #>
		<span class="butterbean-label">{{ data.label }}</span>
	<# } #>

	<# if ( data.description ) { #>
		<span class="butterbean-description">{{{ data.description }}}</span>
	<# } #>
</div>

<div class="oceanwp-mb-field">
	<ul class="butterbean-buttonset">

		<# for ( key in data.choices ) { #>

			<li>
				<input type="radio" class="buttonset-input" value="{{ key }}" name="{{ data.field_name }}" id="{{ data.field_name }}_{{ key }}" <# if ( data.value === key ) { #> checked="checked" <# } #> />
				<label class="buttonset-label" for="{{ data.field_name }}_{{ key }}">{{ data.choices[ key ] }}</label>
			</li>

		<# } #>

	</ul>
</div>