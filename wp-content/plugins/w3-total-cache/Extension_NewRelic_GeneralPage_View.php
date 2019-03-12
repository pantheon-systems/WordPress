<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<?php
Util_Ui::postbox_header( __( 'Monitoring', 'w3-total-cache' ), '', 'monitoring' );
Util_Ui::config_overloading_button( array(
		'key' => 'newrelic.configuration_overloaded'
	) );
?>

<?php if ( !$new_relic_installed ): ?>
    <p><?php echo sprintf(
		w3tc_er( 'newrelic.general.header', 'New Relic may not be installed or not active on this server. <a href="%s" target="_blank">Sign up for a (free) account</a>. Visit <a href="%s" target="_blank">New Relic</a> for installation instructions.' ),
		esc_url( W3TC_NEWRELIC_SIGNUP_URL ),
		esc_url( 'https://newrelic.com/docs/php/new-relic-for-php' ) )
?>
    </p>
<?php endif; ?>

<table class="form-table">
    <tr>
        <th>
            <label for="newrelic_api_key"><?php
_e( '<acronym title="Application Programming Interface">API</acronym> key:', 'w3-total-cache' )
?></label>
        </th>
        <td class="w3tc-td-with-button">
            <?php echo htmlspecialchars( $config->get_string( array( 'newrelic', 'api_key' ) ) ) ?>
            <input type="button" class="button w3tcnr_configure" value="Configure"
                <?php Util_Ui::sealing_disabled( 'newrelic' ) ?> />
        </td>
    </tr>
    <tr>
        <th>
            <label><?php _e( 'Application name:' , 'w3-total-cache' ) ?></label>
        </th>
        <td class="w3tc-td-with-button"><?php
if ( $config->get_string( array( 'newrelic', 'monitoring_type' ) ) == 'browser' )
	echo '(browser) ';

echo htmlspecialchars( $effective_appname );
?></td>
    </tr>
</table>
<?php Util_Ui::button_config_save( 'general_newrelic' ); ?>
<?php Util_Ui::postbox_footer(); ?>
