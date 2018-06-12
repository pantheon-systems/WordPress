<?php
wp_enqueue_script('pmxe-angular-app', PMXE_ROOT_URL . '/frontend/dist/app.js', array('jquery'), PMXE_VERSION);
wp_enqueue_style('pmxe-angular-scss', PMXE_ROOT_URL . '/frontend/dist/styles.css', array(), PMXE_VERSION);

if(getenv('WPAE_DEV')) {
    // Livereload in dev mode
    echo '<script src="//localhost:35729/livereload.js"></script>';
}
?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#testLink').click(function(){
            mediator.publish('something', { data: 'Some data' });
        });
    });
</script>
<div ng-app="GoogleMerchants" ng-controller="mainController" ng-init="init()">

</div>
