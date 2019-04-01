<?php
/**
 * @var array $sections
 */
?>
<div class="wp-suggested-text">
    <?php do_action( 'yith_plugin_fw_privacy_guide_content_before' ); ?>

    <?php
    foreach ( $sections as $key => $section ) {
        $action  = "yith_plugin_fw_privacy_guide_content_{$key}";
        $content = apply_filters( 'yith_plugin_fw_privacy_guide_content', '', $key );

        if ( has_action( $action ) || !empty( $section[ 'tutorial' ] ) || !empty( $section[ 'description' ] ) || $content ) {
            if ( !empty( $section[ 'title' ] ) ) {
                echo "<h2>{$section['title']}</h2>";
            }

            if ( !empty( $section[ 'tutorial' ] ) ) {
                echo "<p class='privacy-policy-tutorial'>{$section['tutorial']}</p>";
            }

            if ( !empty( $section[ 'description' ] ) ) {
                echo "<p>{$section['description']}</p>";
            }

            if ( !empty( $content ) ) {
                echo $content;
            }
        }

        do_action( $action );
    }
    ?>

    <?php do_action( 'yith_plugin_fw_privacy_guide_content_after' ); ?>
</div>