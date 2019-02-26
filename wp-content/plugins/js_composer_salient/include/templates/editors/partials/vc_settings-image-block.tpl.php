<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<script type="text/html" id="vc_settings-image-block">
	<li class="added">
		<div class="inner" style="width: 80px; height: 80px; overflow: hidden;text-align: center;">
			<img rel="{{ id }}" src="<# if(obj.sizes && obj.sizes.thumbnail) { #>{{ sizes.thumbnail.url }}<# } else {#>{{ url }}<# } #>"/>
		</div>
		<a href="#" class="vc_icon-remove"><i class="vc-composer-icon vc-c-icon-close"></i></a>
	</li>
</script>
