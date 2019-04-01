<?php

class WPML_TM_Email_Notification_View extends WPML_TM_Email_View {

	const PROMOTE_TRANSLATION_SERVICES_TEMPLATE = 'notification/promote-translation-services.twig';

	/**
	 * @param array  $model
	 * @param string $template
	 *
	 * @return string
	 */
	public function render_model( array $model, $template ) {
		if ( isset( $model['casual_name'] ) && $model['casual_name'] ) {
			$content = $this->render_casual_header( $model['casual_name'] );
		} else {
			$content = $this->render_header( $model['username'] );
		}
		$content .= $this->template_service->show( $model, $template );
		$content .= $this->render_promote_translation_services( $model );
		$content .= $this->render_footer();

		return $content;
	}

	/**
	 * @param array $model
	 *
	 * @return string
	 */
	private function render_promote_translation_services( array $model ) {
		$content = '';

		if ( isset( $model['promote_translation_services'] ) && $model['promote_translation_services'] ) {
			$translation_services_url = esc_url( admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=translation-services' ) );

			/* translators: Promote translation services: %s replaced by "professional translation services integrated with WPML" */
			$promoteA = esc_html_x( 'Need faster translation work? Try one of the %s.', 'Promote translation services: %s replaced by "professional translation services integrated with WPML"', 'wpml-translation-management' );
			/* translators: Promote translation services: Promote translation services: used to build a link to the translation services page */
			$promoteB = esc_html_x( 'professional translation services integrated with WPML', 'Promote translation services: used to build a link to the translation services page', 'wpml-translation-management' );

			$promote_model['message'] = sprintf( $promoteA, '<a href="' . $translation_services_url . '">' . $promoteB . '</a>' );

			$content = $this->template_service->show( $promote_model, self::PROMOTE_TRANSLATION_SERVICES_TEMPLATE );
		}

		return $content;
	}

	/** @return string */
	private function render_footer() {
		$notifications_url  = esc_url( admin_url( 'admin.php?page=' . WPML_TM_FOLDER . WPML_Translation_Management::PAGE_SLUG_SETTINGS . '&sm=notifications' ) );
		$notifications_text = esc_html__( 'WPML Notification Settings', 'wpml-translation-management' );
		$notifications_link = '<a href="' . $notifications_url . '" style="color: #ffffff;">' . $notifications_text . '</a>';

		$bottom_text = sprintf(
			esc_html__(
				'To stop receiving notifications, log-in to %s and change your preferences.',
				'wpml-translation-management'
			),
			$notifications_link
		);

		return $this->render_email_footer( $bottom_text );
	}
}
