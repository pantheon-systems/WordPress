<?php
/**
 * Functions
 */

/**
 * Is "Automatic Slug Creation" enabled?
 *
 * @since 1.0.0
 * @return boolean
 */
function affwp_cas_is_automatic_slug_creation_enabled() {
    $enabled = affiliate_wp()->settings->get( 'custom_affiliate_slugs_automatic_slug_creation' );

    if ( $enabled ) {
        return true;
    }

    return false;
}
