<div id="view-mode" class="table-row mode-select">
    <div class="table-cell">
        <?php _e( 'Mode', 'strong-testimonials' ); ?>
    </div>
    <div class="table-cell">
        <div class="mode-list">
            <?php foreach ( $view_options['mode'] as $mode ) : ?>
                <label>
                    <input id="<?php echo $mode['name']; ?>" type="radio" name="view[data][mode]"
                           value="<?php echo $mode['name']; ?>" <?php checked( $view['mode'], $mode['name'] ); ?>>
                    <?php echo $mode['label']; ?>
                    <div class="mode-line"></div>
                </label>
            <?php endforeach; ?>
        </div>
        <div class="mode-description"></div>
    </div>
</div>
