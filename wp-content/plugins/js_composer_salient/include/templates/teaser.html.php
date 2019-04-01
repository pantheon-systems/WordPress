<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<script type="text/html" id="vc_teaser-button">
	<div class="vc_teaser-checkbox">
		<label class="vc_teaser-button-label vc_teaser-label-{{ value }}"><input
				class="vc_teaser-button vc_teaser-btn-{{ value }}" type="checkbox" value="{{value}}">
			<span>{{label}}</span></label>
	</div>
</script>
<script type="text/html" id="vc_teaser-title">
	<div class="vc_teaser-control vc_teaser-ctr-{{ name }}" data-control="{{ name }}" id="vc_teaser-title-control">
		<div class="vc_move"></div>
		<span></span>

		<div class="vc_link-controls">
			<?php _e( 'Link to', 'js_composer' ) ?>: <a href="#"
			                                            class="vc_link-control{{ 'none' === link ? ' vc_active-link' : ''}}"
			                                            data-link="none"><?php _e( 'No link', 'js_composer' ) ?></a>
			| <a href="#" class="vc_link-control{{ 'post' === link ? ' vc_active-link' : ''}}"
			     data-link="post"><?php _e( 'On post', 'js_composer' ) ?></a>
			| <a href="#" class="vc_link-control{{ 'big_image' === link ? ' vc_active-link' : ''}}"
			     data-link="big_image"><?php _e( 'Big image', 'js_composer' ) ?></a>
		</div>
	</div>
</script>
<script type="text/html" id="vc_teaser-image">
	<div class="vc_teaser-control vc_teaser-ctr-{{ name }}" data-control="{{ name }}" id="vc_teaser-image-control">
		<div class="vc_move"></div>
		<div class="vc_buttons">
			<a href="#" class="vc_teaser-image-featured"
			   data-mode="featured"><?php _e( 'Featured', 'js_composer' ) ?></a> |
			<a href="#" class="vc_teaser-image-custom" data-mode="custom"><?php _e( 'Custom', 'js_composer' ) ?></a>
		</div>
		<div class="vc_image">
		</div>
		<div class="vc_link-controls">
			<?php _e( 'Link to', 'js_composer' ) ?>: <a href="#"
			                                            class="vc_link-control{{ 'none' === link ? ' vc_active-link' : ''}}"
			                                            data-link="none"><?php _e( 'No link', 'js_composer' ) ?></a>
			| <a href="#" class="vc_link-control{{ 'post' === link ? ' vc_active-link' : ''}}"
			     data-link="post"><?php _e( 'On post', 'js_composer' ) ?></a>
			| <a href="#" class="vc_link-control{{ 'big_image' === link ? ' vc_active-link' : ''}}"
			     data-link="big_image"><?php _e( 'Big image', 'js_composer' ) ?></a>
		</div>
	</div>
</script>
<script type="text/html" id="vc_teaser-text">
	<div class="vc_teaser-control vc_teaser-ctr-{{ name }}" data-control="{{ name }}" id="vc_teaser-text-control">
		<div class="vc_move"></div>
		<div class="vc_buttons">
			<a href="#" class="vc_teaser-text-excerpt vc_teaser-text-control"
			   data-mode="excerpt"><?php _e( 'Excerpt', 'js_composer' ) ?></a> |
			<a href="#" class="vc_teaser-text-text vc_teaser-text-control"
			   data-mode="text"><?php _e( 'Text', 'js_composer' ) ?></a> |
			<a href="#" class="vc_teaser-text-custom vc_teaser-text-control"
			   data-mode="custom"><?php _e( 'Custom', 'js_composer' ) ?></a>
		</div>
		<div class="vc_text">
		</div>
	</div>
</script>
<script type="text/html" id="vc_teaser-link">
	<div class="vc_teaser-control vc_teaser-ctr-{{ name }}" data-control="{{ name }}" id="vc_teaser-link-control">
		<div class="vc_move"></div>
		<a href="#"><?php _e( 'Read more', 'js_composer' ) ?></a>
	</div>
</script>
<script type="text/html" id="vc_teaser-custom-image-block">
	<div class="vc_custom">
		<div class="vc_teaser-custom-image-view">

		</div>
		<a class="vc_teaser_add_custom_image" href="#"
		   title="<?php _e( 'Add custom image', 'js_composer' ) ?>"><?php _e( 'Add custom image', 'js_composer' ) ?></a>

	</div>
</script>
<script type="text/html" id="vc_teaser-custom-image">
	<a href="#" class="vc_teaser_add_custom_image" style="width: 266px; text-align: center;">
		<img rel="<%= id %>" src="<%= url %>"/>
	</a>
</script>
