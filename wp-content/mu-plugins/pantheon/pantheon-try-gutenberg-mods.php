<?php
/**
 * Modify the learn more Gutenberg link
 * to go to the Pantheon Gutenberg page
 * rather than WordPress.org
 *
 * @param string $learn_more
 * @return string
 */
function _pantheon_try_gutenberg_host_link( $learn_more ){

    $style_mods = '<style type="text/css">.try-gutenberg-panel .try-gutenberg-panel-column:not(.try-gutenberg-panel-image-column) {grid-template-rows: auto;}</style>';

    return $style_mods . sprintf(
        /* translators: Link to https://pantheon.io/gutenberg */
        __( '<a href="%s">Learn more about Gutenberg on Pantheon</a>.' ),
        __( 'https://pantheon.io/gutenberg' )
    );

}

/**
 * Only add the filter if we should
 * be adding Pantheon modifications
 * to the Try Gutenberg panel
 */
if( ( ! defined('DISABLE_PANTHEON_TRY_GUTENBERG_MODS') || ! DISABLE_PANTHEON_TRY_GUTENBERG_MODS ) ){
    add_filter( 'try_gutenberg_learn_more_link', '_pantheon_try_gutenberg_host_link');
}

/**
 * Add a Pantheon specific section to
 * the end of the Try Gutenberg panel
 *
 * @return void
 */
function _pantheon_try_gutenberg_append_warning(){
?>
<style type="text/css">
    .try-gutenberg-panel .pantheon-notice {
        padding: 0 1em 0.5em;
        margin: 0.5em 0 2em;
        border: 1px solid #ccc;
        border-left: solid 4px #5bc0de;
        border-radius: 4px;
    }
</style>
<div class="try-gutenberg-panel-content"><div class="pantheon-notice">
<h3>
<?php __('Gutenberg on Pantheon'); ?>
</h3>
<p>
<?php
printf(
 	/* translators: Link to https://pantheon.io/gutenberg */
 	__( 'Pantheon offers resources, such as webinars, on getting the most out of Gutenberg. <a href="%s">Learn about Gutenberg on Pantheon</a>.' ),
 	__( 'https://pantheon.io/gutenberg' )
);
?>
</p>
<p>
<?php
printf(
 	/* translators: Link to https://pantheon.io/docs/pantheon-workflow/ */
 	__( 'Just like any code change, Gutenberg should be added, tested and deployed using <a href="%s">the Pantheon workflow</a>.' ),
 	__( 'https://pantheon.io/docs/pantheon-workflow/' )
);
?>
</p>
</div></div>
<?php    
}

/**
 * Only add the action if we should
 * be adding Pantheon modifications
 * to the Try Gutenberg panel
 */
if( ( ! defined('DISABLE_PANTHEON_TRY_GUTENBERG_MODS') || ! DISABLE_PANTHEON_TRY_GUTENBERG_MODS ) ){
    add_action( 'try_gutenberg_panel', '_pantheon_try_gutenberg_append_warning', 99);
}