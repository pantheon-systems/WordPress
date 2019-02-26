<?php

class WPML_TM_ATE_Translator_Message_Classic_Editor implements IWPML_Action {

	const ACTION = 'wpml_ate_translator_classic_editor';
	const USER_OPTION = 'wpml_ate_translator_classic_editor_minimized';

	/** @var WPML_Translation_Manager_Records */
	private $translation_manager_records;

	/** @var WPML_WP_User_Factory */
	private $user_factory;

	/** @var WPML_TM_ATE_Request_Activation_Email */
	private $activation_email;

	public function __construct(
		WPML_Translation_Manager_Records $translation_manager_records,
		WPML_WP_User_Factory $user_factory,
		WPML_TM_ATE_Request_Activation_Email $activation_email
	) {
		$this->translation_manager_records = $translation_manager_records;
		$this->user_factory                = $user_factory;
		$this->activation_email            = $activation_email;
	}

	public function add_hooks() {
		add_action( 'wpml_tm_editor_messages', array( $this, 'classic_editor_message' ) );
		add_action( 'wp_ajax_' . self::ACTION, array( $this, 'handle_ajax' ) );
	}

	public function classic_editor_message() {
		$main_message       = esc_html__( "This site can use WPML's Advanced Translation Editor, but you did not receive permission to use it. You are still translating with WPML's classic translation editor. Please ask your site's Translation Manager to enable the Advanced Translation Editor for you.", 'wpml-translation-management' );
		$learn_more         = esc_html__( "Learn more about WPML's Advanced Translation Editor", 'wpml-translation-management' );
		$short_message      = esc_html__( "Advanced Translation Editor is disabled.", 'wpml-translation-management' );
		$more               = esc_html__( "More", 'wpml-translation-management' );
		$request_activation = esc_html__( 'Request activation from', 'wpml-translation-management' );

		$show_minimized = (bool) $this->user_factory->create_current()->get_option( self::USER_OPTION );

		?>
		<div
			class="notice notice-info otgs-notice js-classic-editor-notice"
			data-nonce="<?php echo wp_create_nonce( self::ACTION ); ?>"
			data-action="<?php echo self::ACTION; ?>"
			<?php if ( $show_minimized ) { ?> style="display: none" <?php } ?>
		>
			<p><?php echo $main_message; ?></p>
			<p><a href="#" class="wpml-external-link" target="_blank"><?php echo $learn_more; ?></a></p>
			<p>
				<a class="button js-request-activation"><?php echo $request_activation; ?></a> <?php $this->output_translation_manager_list(); ?>
			</p>
			<p class="js-email-sent" style="display: none"></p>

			<a class="js-minimize otgs-notice-toggle">
				<?php esc_html_e( 'Minimize', 'wpml-translation-management' ); ?>
			</a>
		</div>

		<div
			class="notice notice-info otgs-notice js-classic-editor-notice-minimized"
			<?php if ( ! $show_minimized ) { ?> style="display: none" <?php } ?>
		>
			<p><?php echo $short_message; ?> <a class="js-maximize"><?php echo $more; ?></a></p>
		</div>
		<?php
	}

	private function output_translation_manager_list() {
		$translation_managers = $this->translation_manager_records->get_users_with_capability();
		?>

		<select class="js-translation-managers">
			<?php foreach ( $translation_managers as $translation_manager ) {
				$display_name = $translation_manager->user_login . ' (' . $translation_manager->user_email . ')';
				?>
				<option
					value="<?php echo $translation_manager->ID; ?> "><?php echo $display_name; ?></option>
			<?php } ?>
		</select>
		<?php

	}

	public function handle_ajax() {
		if ( wp_verify_nonce( $_POST['nonce'], self::ACTION ) ) {
			$current_user = $this->user_factory->create_current();

			switch ( $_POST['command'] ) {
				case 'minimize':
					$current_user->update_option( self::USER_OPTION, true );
					wp_send_json_success( array( 'message' => '' ) );

				case 'maximize':
					$current_user->update_option( self::USER_OPTION, false );
					wp_send_json_success( array( 'message' => '' ) );

				case 'requestActivation':
					$manager = $this->user_factory->create( (int) $_POST['manager'] );
					if ( $this->activation_email->send_email( $manager, $current_user ) ) {
						$message = sprintf(
							esc_html__( 'An email has been sent to %s', 'wpml-translation-management' ),
							$manager->user_login
						);
					} else {
						$message = sprintf(
							esc_html__( 'Sorry, the email could not be sent to %s for an unknown reason.', 'wpml-translation-management' ),
							$manager->user_login
						);
					}
					wp_send_json_success( array( 'message' => $message ) );
			}
		}
	}

}