<?php
function _pantheon_try_gutenberg_host_link(){
?>
<style type="text/css">
    .try-gutenberg-panel .try-gutenberg-panel-column:not(.try-gutenberg-panel-image-column) {
        grid-template-rows: auto;
    }
    .try-gutenberg-panel .host-link {
        font-weight: bold;
    }
    .try-gutenberg-action > p:first-of-type {
        display: none;
    }
</style>
<p>
<?php
printf(
 	/* translators: Link to https://pantheon.io/gutenberg */
 	__( '<a href="%s" class="host-link">Learn about Gutenberg on Pantheon</a>' ),
 	__( 'https://pantheon.io/gutenberg' )
);
?>
</p>
<?php    
}

add_action( 'try_gutenberg_after_install_button', '_pantheon_try_gutenberg_host_link');

function _pantheon_try_gutenberg_append_warning(){
?>
<style type="text/css">
    .try-gutenberg-panel .pantheon-notice {
        font-size: 16px;
        color: #2C3539;
        border-left: solid 5px #2C3539;
        padding: 1em;
        margin: 0.5em 0;
    }
</style>
<div class="try-gutenberg-panel-content"><div class="pantheon-notice">
<?php
printf(
 	/* translators: Link to https://pantheon.io/docs/pantheon-workflow/ */
 	__( 'We encourage you to test Gutenberg on Pantheon in a safe way. Please remember, like any code change, installing Gutenberg needs to go through <a href="%s">the Pantheon workflow</a>.' ),
 	__( 'https://pantheon.io/docs/pantheon-workflow/' )
);
?>
</div></div>
<?php    
}

// if( in_array($_ENV['PANTHEON_ENVIRONMENT'], array('test', 'live') ) ){
    add_action( 'try_gutenberg_panel', '_pantheon_try_gutenberg_append_warning', 99);
// }