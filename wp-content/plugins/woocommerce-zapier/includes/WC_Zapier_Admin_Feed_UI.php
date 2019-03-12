<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Add/Edit Zapier Feed dashboard screen.
 *
 * Class WC_Zapier_Admin_Feed_UI
 */
class WC_Zapier_Admin_Feed_UI {

	private $prefix = 'wc_zapier_';

	private $meta_fields = array();

	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		add_filter( 'manage_wc_zapier_feed_posts_columns', array( $this, 'columns' ) );
		add_filter( 'manage_wc_zapier_feed_posts_custom_column', array( $this, 'custom_column' ), 10, 2 );

		add_action( 'admin_head-post.php', array( $this, 'hide_publishing_actions' ) );
		add_action( 'admin_head-post-new.php', array( $this, 'hide_publishing_actions' ) );

		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		// Zapier Feeds listing screen
		add_filter( 'bulk_actions-' . 'edit-wc_zapier_feed', array( $this, 'bulk_actions' ) );
		add_filter( 'post_row_actions', array( $this, 'post_row_actions'), 10,2 );

		foreach ( array( 'edit.php', 'post-new.php', 'post.php' ) as $page )
			add_action( "admin_print_styles-$page", array( $this, 'css' ) );

		$this->meta_fields = array(
			array(
				'type' => 'title',
				'desc' => '<p>' . __( 'To configure your Zapier Feed, complete the information below.' , 'wc_zapier' ) . '</p>' .
                          '<p>' . sprintf( __( 'Note: The setup process is quite involved, so we recommend <a href="%s" target="_blank" title="(Opens in a new window)">reading the documentation</a>.', 'wc_zapier' ), WC_Zapier::documentation_url . '#setup' ) . '</p>' .
                          '<p>' . sprintf( __( '<a href="%s" target="_blank" title="(Opens in a new window)">Please click here for a description of each Trigger</a>.', 'wc_zapier' ), WC_Zapier::documentation_url . '#triggers' ) . ' </p>' ,
				'id'   => "{$this->prefix}feed_details"
			),
			array(
				'id'                      => "{$this->prefix}trigger"
			, 'title'                   => __( 'Trigger', 'wc_zapier' )
			, 'type'                    => 'radio'
			, 'options'                 => WC_Zapier_Trigger_Factory::get_triggers_for_display()
			, 'maps_to'                 => 'trigger'
			, 'display_on_posts_screen' => true
			),
			array(
				'id'                      => "{$this->prefix}webhook_url"
			, 'title'                   => __( 'Webhook URL', 'wc_zapier' )
			, 'desc'                    => '<br />' . sprintf( __( 'The URL to your Zapier webhook. This information is provided by Zapier when you create a new Zap on the Zapier website.<br />Example: <code>%s</code>', 'wc_zapier' ), WC_Zapier_Feed::webhook_url_example )
			, 'type'                    => 'text'
			, 'css'                     => 'min-width:400px;'
			, 'maps_to'                 => 'webhook_url'
			, 'display_on_posts_screen' => true
			),
			array(
				'id'      => "{$this->prefix}title"
			, 'title'   => __( 'Title', 'wc_zapier' )
			, 'desc'    => '<br />' . __( 'Descriptive title/name of this Zapier Feed.<br />Should typically match the name of your Zap on the Zapier website.', 'wc_zapier' )
			, 'type'    => 'text'
			, 'css'     => 'min-width:400px;'
			, 'maps_to' => 'title'
			),
			array( 'type' => 'sectionend', 'id' => "{$this->prefix}feed_details" )
		);

	}

	/**
	 * Admin CSS, which adds the Zapier icon to various dashboard screens.
	 */
	public function css() {
		global $typenow;

		if ( 'wc_zapier_feed' == $typenow ) {
?>
<style type="text/css">
.icon32-posts-wc_zapier_feed { background-image: url(<?php echo esc_url( WC_Zapier::$plugin_url ); ?>assets/images/zapier.png?v=2);
</style>
<?php
		}
	}


	/**
	 * Adds the Zapier Feed Details metabox to the Add/Edit Zapier Feed Dashboard Screen.
	 */
	public function add_meta_boxes() {

		add_meta_box(
			'zapierfeedinfo',
			__( 'Zapier Feed Details', 'wc_zapier' ),
			array( $this, 'metabox_output' ),
			'wc_zapier_feed',
			'normal',
			'high'
		);

	}

	/**
	 * Obtains the current/default values for the zapier feed fields.
	 *
	 * This is necessary because the woocommerce_admin_fields() uses get_option()
	 *
	 * Executed by the pre_option_* filters
	 *
	 * @param $method
	 * @param $args
	 *
	 * @return bool|string
	 */
	public function __call( $method, $args ) {
		global $post;
		$feed = new WC_Zapier_Feed( $post );
		$field = str_replace( "pre_option_{$this->prefix}", '', $method );
		if ( $field !== $method ) {
			switch ( $field ) {
				case 'webhook_url':
					return $feed->webhook_url();
					break;
				case 'trigger':
					return is_null( $feed->trigger() ) ? 'wc.new_order' : $feed->trigger()->get_trigger_key();
					break;
				case 'title':
					return $feed->title();
					break;
			}
		}
		return false;

	}

	/**
	 * The output for the metabox that is shown on the Add/Edit Zapier Feed screen.
	 */
	public function metabox_output() {
		global $post;

		// We're going to use WooCommerce's settings/admin fields API (including the woocommerce_admin_fields() function)
		require_once( WC()->plugin_path() . '/includes/admin/wc-admin-functions.php' );

		wp_nonce_field( "{$this->prefix}feed_details", "{$this->prefix}feed_details_nonce", true, true );

		foreach ( $this->meta_fields as $field ) {
			$name = "pre_option_{$field['id']}";
			add_filter( $name, array( $this, $name ) );
		}

		if ( isset( $_GET['post'] ) ) {
			// We're editing an existing feed

			// Check for validation errors and display them if necessary
			$messages = get_option( 'wc_zapier_feed_messages', array() );

			if ( isset($messages[$post->ID]) && is_array( $messages[$post->ID] ) ) {
				// Critical errors
				foreach ( $messages[$post->ID]['errors'] as $error ) {
					// Error messages contain HTML code, so we can't use esc_html().
					echo '<div class="error"><p>' . wp_kses_post( $error ) . '</p></div>';
				}
				// Friendly warnings
				foreach ( $messages[$post->ID]['warnings'] as $warning ) {
					// Warnings contain HTML code, so we can't use esc_html().
					echo '<div class="updated"><p>' . wp_kses_post( $warning ) . '</p></div>';
				}
				unset($messages[$post->ID]);
				update_option( 'wc_zapier_feed_messages', $messages );
			} else if ( 'publish' == get_post_status( $post ) )  {
				// No warnings/errors with this feed, and it is published (active)
				echo '<div class="updated"><p>' . esc_html( __( 'This Zapier Feed is active and ready to receive real data.', 'wc_zapier' ) ) . '</p></div>';
			} else {
				// No warnings/errors with this feed
				echo '<div class="updated"><p>' . esc_html( __( 'This Zapier Feed is inactive. No real data will be sent to this feed until it is made active (published).', 'wc_zapier' ) ) . '</p></div>';
			}
		} else {
			// Add new feed screen
		}

		woocommerce_admin_fields( $this->meta_fields );

		foreach ( $this->meta_fields as $field ) {
			$name = "pre_option_{$field['id']}";
			remove_filter( $name, array( $this, $name ) );
		}

	}

	/**
	 * Saves the zapier feed data into the correct fields.
	 *
	 * Executed by the 'save_post' hook
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return mixed
	 */
	public function save_post( $post_id, $post ) {

		if ( 'wc_zapier_feed' != $post->post_type ) // Ignore other post types
			return;

		if ( wp_is_post_revision( $post_id ) ) // Ignore post revisions
		 	return;

		if ( wp_is_post_autosave( $post_id ) ) // Ignore autosaves
			return;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) // Ignore autosaves
			return;

		if ( 'auto-draft' == $post->post_status ) // Ignore auto drafts
			return;

		if ( 'trash' == $post->post_status ) // Ignore feeds that are being trashed
			return;

		if ( ! current_user_can( 'edit_post', $post_id ) ) // Ignore unauthenticated requests
			return;

		// verify nonce
		if ( ! isset($_POST["{$this->prefix}feed_details_nonce"]) || ! wp_verify_nonce( $_POST["{$this->prefix}feed_details_nonce"], "{$this->prefix}feed_details" ) )
			return;


		$feed = new WC_Zapier_Feed( $post );

		$feed->set_title( isset( $_POST["{$this->prefix}title"] ) ? $_POST["{$this->prefix}title"] : '' );
		$feed->set_webhook_url( isset( $_POST["{$this->prefix}webhook_url"] ) ? $_POST["{$this->prefix}webhook_url"] : '' );
		$feed->set_trigger_with_key( isset( $_POST["{$this->prefix}trigger"] ) ? $_POST["{$this->prefix}trigger"] : '' );

		$validation_results = $feed->validate();

		if ( is_array( $validation_results ) ) {
			// we have warnings and/or errors
			$messages = get_option( 'wc_zapier_feed_messages', array() );

			$messages[$post->ID] = $validation_results;

			if ( !empty( $validation_results['errors'] ) ) {
				// Validation errors exist
				$messages[$post->ID]['errors'][] = _n( 'This Zapier Feed cannot be activated until this issue is resolved.', 'This Zapier Feed cannot be made active until these issues are resolved.', count( $messages[$post->ID]['errors'] ), 'wc_zapier' );
			}
			update_option( 'wc_zapier_feed_messages', $messages );
		}

		add_filter( 'redirect_post_location', array( $this, 'redirect_post_location' ), 10, 2 );

		// Temporarily disable this save_post hook while we update the post record
		remove_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		$feed->save();
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		return $post_id;
	}

	/**
	 * If we encounter a validation error, override the redirect location so WordPress doesn't display a message.
	 *
	 * Instead, our validation error messages are displayed.
	 *
	 * @param $location
	 * @param $post_id
	 *
	 * @return string
	 */
	public function redirect_post_location( $location, $post_id ) {
		$location = add_query_arg( 'message', '0', get_edit_post_link( $post_id, 'url' ) );
		return $location;
	}

	/**
	 * If we're on the Add/Edit Zapier feed screen, hide the Visiblity and Published Date from the Publish Metabox.
	 */
	public function hide_publishing_actions() {
		global $post;
		if ( $post->post_type == 'wc_zapier_feed' ) {
			echo '
					<style type="text/css">
							#misc-publishing-actions #visibility,
							#misc-publishing-actions .curtime {
									display:none;
							}
					</style>
			';
		}

	}

	/**
	 * Disable the Bulk Edit feature on the Zapier Feeds listing screen.
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	public function bulk_actions( $actions ) {
		if ( isset($actions['edit']) )
			unset($actions['edit']);
		return $actions;
	}


	/**
	 * Disable Quick Edit on the Zapier Feeds listing screen.
	 *
	 * As per http://core.trac.wordpress.org/ticket/19343.
	 *
	 * @param array $actions array of actions
	 * @param WP_Post $post Post object
	 *
	 * @return mixed
	 */
	public function post_row_actions( $actions, $post ) {

		if( $post->post_type == 'wc_zapier_feed' ) {
			unset($actions['inline hide-if-no-js']);
		}

		return $actions;
	}

	/**
	 * Customise the columns that are displayed on the Zapier Feeds dashboard listing screen.
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function columns( $columns ) {

		// Remove date column
		unset($columns['date']);

		// Add our custom fields
		foreach ( $this->meta_fields as $field ) {
			if ( isset($field['display_on_posts_screen']) && $field['display_on_posts_screen'] ) {
				$columns[$field['id']] = $field['title'];
			}
		}

		return $columns;
	}

	/**
	 * Output the custom columns on the Zaper Feeds listing screen.
	 *
	 * @param string $column
	 * @param int    $post_id
	 */
	public function custom_column( $column, $post_id ) {

		$feed = new WC_Zapier_Feed( $post_id );

		foreach ( $this->meta_fields as $field ) {
			if ( $column == $field['id'] ) {
				if ( isset($field['maps_to']) ) {
					switch ( $field['id'] ) {
						case "{$this->prefix}trigger":
							// Convert the trigger id into a user-friendly name
							if ( !is_null( $feed->{$field['maps_to']}() ) ) // Just in case the trigger key in the database no longer exists
								echo esc_html( $feed->{$field['maps_to']}()->get_trigger_title() );
							break;
						default:
							echo esc_html( $feed->{$field['maps_to']}() );
					}
				}
			}
		}
	}

}
