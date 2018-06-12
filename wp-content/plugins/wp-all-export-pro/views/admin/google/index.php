<?php

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
