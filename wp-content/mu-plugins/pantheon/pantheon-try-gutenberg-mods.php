<?php
/**
  * Should we proceed with adding the
  * return to Pantheon button?
  *
  * Only if:
  * We are on a Pantheon subdomain and
  * "RETURN_TO_PANTHEON_BUTTON" is not false
  */
$mod_try_gutenberg = apply_filters( 'show_pantheon_try_gutenberg_mods', true);

function _pantheon_try_gutenberg_host_link(){
?>
<style type="text/css">
    .try-gutenberg-panel .try-gutenberg-panel-column:not(.try-gutenberg-panel-image-column) {
        grid-template-rows: auto;
    }
    .try-gutenberg-panel .host-link {
        font-weight: bold;
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

if( true === $mod_try_gutenberg ){
    add_action( 'try_gutenberg_after_install_button', '_pantheon_try_gutenberg_host_link');
}

function _pantheon_try_gutenberg_append_warning(){
?>
<style type="text/css">
    .try-gutenberg-panel .pantheon-notice {
        color: #2C3539;
        border-left: solid 5px #2C3539;
        padding: 0 1em;
        margin: 0.5em 0;
    }
</style>
<div class="try-gutenberg-panel-content"><div class="pantheon-notice">
<?php
printf(
 	/* translators: Link to https://pantheon.io/docs/pantheon-workflow/ */
 	__( '<h3>New to Pantheon?</h3><p>Remember to install Gutenberg in the Dev environment and use <a href="%s">the Pantheon workflow</a> when ready to deploy to Test or Live.</p>' ),
 	__( 'https://pantheon.io/docs/pantheon-workflow/' )
);
?>
</div></div>
<?php    
}

if( true === $mod_try_gutenberg ){
    add_action( 'try_gutenberg_panel', '_pantheon_try_gutenberg_append_warning', 99);
}