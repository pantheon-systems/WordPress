<?php
/**
 * Adds the metabox to each page
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AffiliateWP Affiliate Landing Pages Metabox Class
 *
 * @since 1.0
 */
class AffiliateWP_Affiliate_Landing_Page_Metabox {

    public function __construct() {

        // Add metabox.
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

        // Save metabox.
        add_action( 'save_post', array( $this, 'save_meta_box' ), 10, 2 );

    }

    /**
     * Register the meta box.
     *
     * @since  1.0
     * @return void
     */
	public function add_meta_box() {

		if ( true === affwp_alp_is_enabled() ) {
			add_meta_box( 'affwp_alp_settings', __( 'Affiliate Landing Pages', 'affiliatewp-affiliate-landing-pages' ), array( $this, 'meta_box' ), array( 'page', 'post' ), 'side', 'default' );
		}

	}

	/**
	 * Add the meta box
	 *
	 * @since  1.0
	 */
	public function meta_box( $post ) {

		$user_name = get_post_meta( $post->ID, 'affwp_landing_page_user_name', true );

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'affwp_alp_nonce', 'affwp_alp_nonce' );

	?>
		<p>
			<label for="user_name"><strong><?php _e( 'Assign landing page to affiliate', 'affiliatewp-affiliate-landing-pages' ); ?></strong></label>
		</p>

		<p class="affwp-ajax-search-wrap">
			<input type="text" name="user_name" id="user_name" value="<?php echo esc_attr( $user_name ); ?>" class="affwp-user-search large-text" data-affwp-status="active" autocomplete="off" />
		</p>
		<p class="howto"><?php _e( 'To search for an affiliate, begin typing their name, email address or username.', 'affiliatewp-affiliate-landing-pages' ); ?></p>

	<?php
	}


    /**
     * Save post meta when the save_post action is called
     *
     * @since 1.0
     * @param int $post_id
     * @global array $post All the data of the the current post
     * @return void
     */
    public function save_meta_box( $post_id, $post ) {

		/**
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['affwp_alp_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['affwp_alp_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'affwp_alp_nonce' ) ) {
			return $post_id;
		}

		// If this is an autosave, our form has not been submitted,
		// so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

		}

		// OK, its safe for us to save the data now.
		$user_name = isset( $_POST['user_name'] ) ? sanitize_text_field( $_POST['user_name'] ) : '';

		if ( $user_name ) {
			// Update post meta.
			update_post_meta( $post_id, 'affwp_landing_page_user_name', $user_name );
		} else {
			// Delete post meta.
			delete_post_meta( $post_id, 'affwp_landing_page_user_name' );
		}

    }

}

$metabox = new AffiliateWP_Affiliate_Landing_Page_Metabox;
