<?php

$integration = new Affiliate_WP_Caldera_Forms;

/**
 * This file is the processor panel in Caldera Forms settings.
 */
echo Caldera_Forms_Processor_UI::config_fields( $integration->fields() );
