<div>
    <input type="radio" id="template-<?php esc_attr_e( $key ); ?>"
           name="view[data][<?php esc_attr_e( $current_mode ); ?>]"
           value="<?php esc_attr_e( $key ); ?>" <?php checked( $key, $view['template'] ); ?>>
    <label for="template-<?php esc_attr_e( $key ); ?>">
		<?php echo $template['config']['name']; ?>
    </label>
</div>
