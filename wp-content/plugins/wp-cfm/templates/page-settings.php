<?php

$configuration = $this->registry->get_configuration_items();
$configuration = $this->helper->group_items( $configuration );

?>

<div class="wrap">
    <h2>WP-CFM</h2>

    <?php if ( defined( 'WPCFM_CURRENT_ENV' ) && !empty( WPCFM_CURRENT_ENV ) ): ?>
    <div class="wpcfm-info">
        <?php _e( 'Current environment', 'wpcfm' ); ?>: <code><?php echo ucfirst( WPCFM_CURRENT_ENV ); ?></code>
    </div>

    <!-- Environment Switcher -->
    <div class="wpcfm-env-switch">
        <?php _e( 'Switch config to', 'wpcfm' ); ?>: <select name="wpcfm_env_switch" id="wpcfm_env_switch">
            <?php foreach ( WPCFM_REGISTER_MULTI_ENV as $env ): ?>
                <option value="<?php echo $env; ?>"
                    <?php if ( ( WPCFM_CURRENT_ENV == $env && !defined( 'WPCFM_COMPARE_ENV' ) ) || ( defined( 'WPCFM_COMPARE_ENV' ) && WPCFM_COMPARE_ENV == $env ) ): ?>
                    <?php echo 'selected="selected"'; ?>
                <?php endif ?>
                ><?php echo ucfirst( $env ); ?></option>
            <?php endforeach ?>
        </select>
    </div>
<?php endif; ?>

<?php if (defined('WPCFM_CONFIG_FORMAT_REQUESTED') && in_array(WPCFM_CONFIG_FORMAT_REQUESTED, array('yml', 'yaml'))): ?>
<div class="wpcfm-error">Your PHP version is not compatible with Yaml export format. Upgrade to at least PHP 5.6.4.</div>
<?php endif; ?>

<?php if ( !empty ( $this->readwrite->error ) ) : ?>
    <div class="wpcfm-error"><?php echo $this->readwrite->error; ?></div>
<?php endif; ?>

<div class="wpcfm-warnings">
    <?php foreach ( $this->registry->get_duplicates() as $option => $bundles ): ?>
        <div class="wpcfm-warning">
            <?php _e( 'Warning: ', 'wpcfm' ); ?>
            <?php echo( $option ); ?>
            <?php _e( 'is tracked by multiple bundles: ', 'wpcfm' ); ?>
            <?php echo( implode( ', ', $bundles ) ); ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="wpcfm-bundles">
    <div class="wpcfm-action-buttons">
        <div style="float:right">
            <span class="wpcfm-response"></span>
            <a class="button-primary wpcfm-save"><?php _e( 'Save Changes', 'wpcfm' ); ?></a>
        </div>
        <a class="button add-bundle"><?php _e( 'Add Bundle', 'wpcfm' ); ?></a>
        <div class="clear"></div>
    </div>

    <div class="bundle-row row-all" data-bundle="all">
        <div class="bundle-header">
            <div class="bundle-actions">
                <a class="button diff-bundle" title="Compare differences"><?php _e( 'Diff', 'wpcfm' ); ?></a> &nbsp;
                <a class="button push-bundle" title="Write database changes to the filesystem"><?php _e( 'Push', 'wpcfm' ); ?></a> &nbsp;
                <a class="button pull-bundle" title="Import file changes into the database"><?php _e( 'Pull', 'wpcfm' ); ?></a>
            </div>
            <div class="bundle-toggle">All Bundles</div>
            <div class="clear"></div>
        </div>
    </div>
</div>

<div class="attribution">
    Created by <a href="http://forumone.com/" target="_blank">Forum One</a>
</div>

<!-- clone settings -->

<div class="bundles-hidden">
    <div class="bundle-row" data-bundle="new_bundle">
        <div class="bundle-header">
            <div class="bundle-actions">
                <span class="no-actions">Save to see actions</span>
                <a class="button diff-bundle" title="Compare differences"><?php _e( 'Diff', 'wpcfm' ); ?></a> &nbsp;
                <a class="button push-bundle disabled" title="Write database changes to the filesystem"><?php _e( 'Push', 'wpcfm' ); ?></a> &nbsp;
                <a class="button pull-bundle disabled" title="Import file changes into the database"><?php _e( 'Pull', 'wpcfm' ); ?></a>
            </div>
            <div class="bundle-toggle">New bundle</div>
            <div class="clear"></div>
        </div>
        <div class="bundle-row-inner">
            <input type="text" class="bundle-label" value="New bundle" />
            <input type="text" class="bundle-name" value="new_bundle" />
            <a href="#" class="hide-registered">Hide registered</a>
            <a href="#" class="show-all hidden">Show all</a>
            <div class="bundle-select-wrapper">
                <select class="bundle-select" multiple="multiple">
                    <?php foreach ( $configuration as $group => $config ) : ?>
                        <optgroup label="<?php echo $group; ?>">
                            <?php foreach ( $config as $key => $data ) : ?>
                                <?php $label = isset( $data['label'] ) ? $data['label'] : $key ; ?>
                                <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <a class="remove-bundle"><?php _e( 'Delete Bundle', 'wpcfm' ); ?></a>
            <a class="download-bundle hidden"><?php _e( 'Download', 'wpcfm' ); ?></a>
            <div class="clear"></div>
        </div>
    </div>
</div>
</div>

<!-- diff modal -->

<div class="media-modal">
    <a class="media-modal-close"><span class="media-modal-icon"></span></a>
    <div class="media-modal-content">
        <div class="media-frame">
            <div class="media-frame-title">
                <h1>Diff Viewer</h1>
            </div>
            <div class="media-frame-router">
                <div class="media-router">
                    Compare file and database versions. Changes marked in <span style="background:#c6ffc6">GREEN</span> exist in the database but not the filesystem.
                </div>
            </div>
            <div class="media-frame-content">
                <div class="wpcfm-diff">
                    <pre class="original"></pre>
                    <pre class="changed"></pre>
                    <pre class="diff"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="media-modal-backdrop"></div>
