<?php
/**
 * Class Strong_Testimonials_Form
 */
class Strong_Testimonials_Form {

	public $form_options;

	public $plugins;

	public $form_values;

	public $form_errors;

	public $captcha;

	/**
	 * Strong_Testimonials_Form constructor.
	 */
	public function __construct() {
		$this->form_options = get_option( 'wpmtst_form_options' );
		$this->plugins = apply_filters( 'wpmtst_captcha_plugins', get_option( 'wpmtst_captcha_plugins' ) );
		$this->add_actions();
		$this->load_captcha();
		$this->load_honeypots();
	}

	/**
	 * Add our actions.
	 */
	public function add_actions() {
		add_action( 'init', array( $this, 'process_form' ), 20 );

		add_action( 'wp_ajax_wpmtst_form2', array( $this, 'process_form_ajax' ) );
		add_action( 'wp_ajax_nopriv_wpmtst_form2', array( $this, 'process_form_ajax' ) );
	}

	/**
	 * Load Captcha class.
	 */
	public function load_captcha() {
		if ( ! isset( $this->form_options['captcha'] ) || ! $this->form_options['captcha'] ) {
			return;
		}

		$slug = $this->form_options['captcha'];
		if ( ! $slug || ! isset( $this->plugins[ $slug ]['class'] ) ) {
			return;
		}

		require_once WPMTST_INC . 'integrations/class-integration-captcha.php';

		$file_name  = "class-integration-$slug.php";
		$file_path  = WPMTST_INC . 'integrations/' . $file_name;
		$class_name = 'Strong_Testimonials_Integration_' . $this->plugins[ $slug ]['class'];

		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		}

		if ( class_exists( $class_name ) ) {
			$this->captcha = new $class_name();
			add_filter( 'wpmtst_add_captcha', array( $this->captcha, 'add_captcha' ), 20 );
			add_filter( 'wpmtst_check_captcha', array( $this->captcha, 'check_captcha' ) );
		}

	}

	/**
	 * Load honeypots.
	 */
	public function load_honeypots() {
		if ( isset( $this->form_options['honeypot_before'] ) && $this->form_options['honeypot_before'] ) {
			add_action( 'honeypot_before_spam_testimonial', array( $this, 'honeypot_error' ) );
		}

		if ( isset( $this->form_options['honeypot_after'] ) && $this->form_options['honeypot_after'] ) {
			add_action( 'honeypot_after_spam_testimonial', array( $this, 'honeypot_error' ) );
		}
	}

	/**
	 * Process a form.
	 * Moved to `init` hook for strong_testimonials_view() template function.
	 *
	 * @since 2.3.0
	 */
	public function process_form() {
		if ( wp_doing_ajax() ) {
			return;
		}

		if ( isset( $_POST['wpmtst_form_nonce'] ) ) {
			$form_options = get_option( 'wpmtst_form_options' );
			$success      = $this->form_processor();
			if ( $success ) {
				switch ( $form_options['success_action'] ) {
					case 'id':
						$goback = get_permalink( $form_options['success_redirect_id'] );
						break;
					case 'url':
						$goback = $form_options['success_redirect_url'];
						break;
					default:
						$goback = add_query_arg( 'success', '', wp_get_referer() );
				}
				wp_redirect( apply_filters( 'wpmtst_form_redirect_url', $goback ) );
				exit;
			}
		}
	}

	/**
	 * Ajax form submission handler
	 */
	public function process_form_ajax() {
		if ( isset( $_POST['wpmtst_form_nonce'] ) ) {
			$success = $this->form_processor();
			if ( $success ) {
				$return = array(
					'success' => true,
					'message' => wpmtst_get_success_message(),
				);
			} else {
				$return = array(
					'success' => false,
					'errors'  => $this->get_form_errors()
				);
			}
			echo json_encode( $return );
		}

		wp_die();
	}

	/**
	 * Store form values.
	 *
	 * @param $form_values
	 */
	public function set_form_values( $form_values ) {
		$this->form_values = $form_values;
	}

	/**
	 * Return form values.
	 *
	 * @return mixed
	 */
	public function get_form_values() {
		return $this->form_values;
	}

	/**
	 * Store from errors.
	 *
	 * @param $form_errors
	 */
	public function set_form_errors( $form_errors ) {
		$this->form_errors = $form_errors;
	}

	/**
	 * Return form errors.
	 *
	 * @return mixed
	 */
	public function get_form_errors() {
		return $this->form_errors;
	}

	/**
	 * Testimonial form processor.
	 *
	 * @since 1.21.0
	 */
	public function form_processor() {

		if ( empty( $_POST ) || ! wp_verify_nonce( $_POST['wpmtst_form_nonce'], 'wpmtst_form_action' ) ) {
			return false;
		}

		do_action( 'wpmtst_form_submission' );

		$new_post = stripslashes_deep( $_POST );
		/**
		 * Trim spaces
		 * @since 2.26.6
		 */
		$new_post = wpmtst_trim_array( $new_post );

		add_filter( 'upload_mimes', array( $this, 'restrict_mime' ) );

		$form_options = get_option( 'wpmtst_form_options' );

		// Init four arrays: post, post_meta, categories, attachment(s).
		$testimonial_post = array(
			'post_status' => $form_options['post_status'],
			'post_type'   => 'wpm-testimonial'
		);
		$testimonial_meta = array();
		$testimonial_cats = array();
		$testimonial_att  = array();

		$form_errors = array();

		$form_name = isset( $new_post['form_id'] ) ? $new_post['form_id'] : 'custom';
		$fields    = wpmtst_get_form_fields( $form_name );

		if ( $form_options['captcha'] ) {
			$form_errors = apply_filters( 'wpmtst_check_captcha', $form_errors );
		}

		if ( $form_options['honeypot_before'] ) {
			$this->honeypot_before();
		}

		if ( $form_options['honeypot_after'] ) {
			$this->honeypot_after();
		}

		/**
		 * sanitize & validate
		 */
		foreach ( $fields as $key => $field ) {

			if ( isset( $field['required'] ) && $field['required'] ) {
				if ( ( 'file' == $field['input_type'] ) ) {
					if ( ! isset( $_FILES[ $field['name'] ] ) || ! $_FILES[ $field['name'] ]['size'] ) {
						$form_errors[ $field['name'] ] = $field['error'];
						continue;
					}
				} elseif ( empty( $new_post[ $field['name'] ] ) ) {
					$form_errors[ $field['name'] ] = $field['error'];
					continue;
				}
			}

			// Check for callback first.
			if ( isset( $field['action_input'] ) && $field['action_input'] ) {
				// Assuming value can be stored as text field
				$testimonial_meta[ $field['name'] ] = sanitize_text_field( $new_post[ $field['name'] ] );
				// TODO Register a validator callback
			}
			else {
				switch ( $field['record_type'] ) {
					case 'post':
						if ( 'file' == $field['input_type'] ) {
							$testimonial_att[ $field['name'] ] = array( 'field' => isset( $field['map'] ) ? $field['map'] : 'post' );
						} elseif ( 'textarea' == $field['input_type'] ) {
							$testimonial_post[ $field['name'] ] = wpmtst_sanitize_textarea( $new_post[ $field['name'] ] );
						} else {
							$testimonial_post[ $field['name'] ] = sanitize_text_field( $new_post[ $field['name'] ] );
						}
						break;

					case 'custom':
						if ( 'email' == $field['input_type'] ) {
							if ( is_email( $new_post[ $field['name'] ] ) ) {
								$testimonial_meta[ $field['name'] ] = sanitize_email( $new_post[ $field['name'] ] );
							}
							else {
								$form_errors[ $field['name'] ] = $field['error'];
							}
						} elseif ( 'url' == $field['input_type'] ) {
							// wpmtst_get_website() will prefix with "http://" so don't add that to an empty input
							if ( $new_post[ $field['name'] ] ) {
								$testimonial_meta[ $field['name'] ] = esc_url_raw( wpmtst_get_website( $new_post[ $field['name'] ] ) );
							}
						} elseif ( 'textarea' == $field['input_type'] ) {
							$testimonial_post[ $field['name'] ] = wpmtst_sanitize_textarea( $new_post[ $field['name'] ] );
						} elseif ( 'checkbox' == $field['input_type'] ) {
							$testimonial_meta[ $field['name'] ] = wpmtst_sanitize_checkbox( $new_post, $field['name'] );
						} else {
							$testimonial_meta[ $field['name'] ] = sanitize_text_field( $new_post[ $field['name'] ] );
						}
						break;

					case 'optional':
						if ( 'category' == strtok( $field['input_type'], '-' ) ) {
							$testimonial_meta[ $field['name'] ] = $new_post[ $field['name'] ];
						}
						elseif ( 'rating' == $field['input_type'] ) {
							$testimonial_meta[ $field['name'] ] = $new_post[ $field['name'] ];
						}
						else {
							$testimonial_meta[ $field['name'] ] = sanitize_text_field( $new_post[ $field['name'] ] );
						}
						break;

					default:
				}
			}

		}

		/**
		 * No missing required fields, carry on.
		 */
		if ( ! count( $form_errors ) ) {

			// Special handling: if post_title is not required, create one from post_content
			if ( ! isset( $testimonial_post['post_title'] ) || ! $testimonial_post['post_title'] ) {
				$words_array                    = explode( ' ', $testimonial_post['post_content'] );
				$five_words                     = array_slice( $words_array, 0, 5 );
				$testimonial_post['post_title'] = implode( ' ', $five_words );
			}

			/**
			 * Validate image attachments and store WP error messages.
			 */
			/*
			 * $_FILES = Array
			 * (
			 *   [featured_image] => Array
			 *     (
			 *       [name] => Screenshot.png
			 *       [type] => image/png
			 *       [tmp_name] => C:\wamp64\tmp\php7EA4.tmp
			 *       [error] => 0
			 *       [size] => 615273
			 *     )
			 * )
			 */
			foreach ( $testimonial_att as $name => $atts ) {
				if ( isset( $_FILES[ $name ] ) && $_FILES[ $name ]['size'] > 1 ) {
					$file = $_FILES[ $name ];

					// Upload file
					$overrides     = array( 'test_form' => false );
					$uploaded_file = $this->handle_upload( $file, $overrides );
					/*
					 * $uploaded_file = Array
					 * (
					 *   [file] => string 'M:\wp\strong\site/wp-content/uploads/Lotus8.jpg'
					 *   [url]  => string 'http://strong.dev/wp-content/uploads/Lotus8.jpg'
					 *   [type] => string 'image/jpeg'
					 * )
					 */
					if ( isset( $uploaded_file['error'] ) ) {
						$form_errors[ $name ] = $uploaded_file['error'];
						break;
					} else {
						// Create an attachment
						$attachment = array(
							'post_title'     => $file['name'],
							'post_content'   => '',
							'post_type'      => 'attachment',
							'post_parent'    => null, // populated after inserting post
							'post_mime_type' => $file['type'],
							'guid'           => $uploaded_file['url']
						);

						$testimonial_att[ $name ]['attachment']    = $attachment;
						$testimonial_att[ $name ]['uploaded_file'] = $uploaded_file;
					}

				}
			}
		}

		/**
		 * No faulty uploads, carry on.
		 */
		if ( ! count( $form_errors ) ) {

			// create new testimonial post
			$testimonial_id = wp_insert_post( apply_filters( 'wpmtst_new_testimonial_post', $testimonial_post ) );

			if ( is_wp_error( $testimonial_id ) ) {

				WPMST()->debug->log( $testimonial_id, __FUNCTION__ );
				// TODO report errors in admin
				$form_errors['post'] = $form_options['messages']['submission-error']['text'];

			} else {

				$testimonial_post['id'] = $testimonial_id;

				/**
				 * Add categories.
				 *
				 * @since 2.17.0 Handle arrays (if using checklist) or strings (if using <select>).
				 * @since 2.19.1 Storing default category (as set in view) in separate hidden field.
				 */

				if ( $new_post['default_category'] ) {
					$testimonial_cats = explode( ',', $new_post['default_category'] );
				}

				if ( $new_post['category'] ) {
					if ( is_string( $new_post['category'] ) ) {
						$new_post['category'] = explode( ',', $new_post['category'] );
					}
					$testimonial_cats = array_merge( $testimonial_cats, $new_post['category'] );
				}

				$testimonial_cats = array_map( 'intval', array_unique( $testimonial_cats ) );

				if ( array_filter( $testimonial_cats ) ) {
					$category_success = wp_set_object_terms( $testimonial_id, $testimonial_cats, 'wpm-testimonial-category' );

					if ( ! $category_success ) {
						// TODO improve error handling
					}
				}

				// save submit date
				$testimonial_meta['submit_date'] = current_time( 'mysql' );

				/**
				 * Save custom fields.
				 *
				 * @since 2.17.0 Exclude categories.
				 */
				$new_meta = array_diff_key( $testimonial_meta, array( 'category' => '' ) );
				$new_meta = apply_filters( 'wpmtst_new_testimonial_meta', $new_meta );
				foreach ( $new_meta as $key => $field ) {
					add_post_meta( $testimonial_id, $key, $field );
				}

				// save attachments
				$testimonial_att = apply_filters( 'wpmtst_new_testimonial_attachments', $testimonial_att );
				foreach ( $testimonial_att as $name => $atts ) {
					if ( isset( $atts['attachment'] ) ) {
						$atts['attachment']['post_parent'] = $testimonial_id;
						$attach_id = wp_insert_attachment( $atts['attachment'], $atts['uploaded_file']['file'], $testimonial_id );
						$attach_data = wp_generate_attachment_metadata( $attach_id, $atts['uploaded_file']['file'] );
						$result = wp_update_attachment_metadata( $attach_id, $attach_data );
						add_post_meta( $testimonial_id, $name, $atts['uploaded_file']['url'] );
						if ( 'featured_image' == $atts['field'] ) {
							set_post_thumbnail( $testimonial_id, $attach_id );
						}
					}
				}

			}

		}

		remove_filter( 'upload_mimes', array( $this, 'restrict_mime' ) );

		/**
		 * Post inserted successfully, carry on.
		 */
		$form_values = array_merge( $testimonial_post, $testimonial_meta );

		ksort( $testimonial_post );
		ksort( $testimonial_meta );
		do_action( 'wpmtst_new_testimonial_added', $testimonial_post, $testimonial_meta, $testimonial_cats, $testimonial_att );

		if ( ! count( $form_errors ) ) {
			// Clear saved form data and errors.
			$this->set_form_values( null );
			$this->set_form_errors( null );
			$this->notify_admin( $form_values, $form_name );

			return true;
		}

		// Redisplay form with submitted values and error messages.
		$this->set_form_values( stripslashes_deep( $form_values ) );
		$this->set_form_errors( $form_errors );

		return false;
	}

	/**
	 * Honeypot preprocessor
	 */
	public function honeypot_before() {
		if ( isset( $_POST['wpmtst_if_visitor'] ) && ! empty( $_POST['wpmtst_if_visitor'] ) ) {
			do_action( 'honeypot_before_spam_testimonial', $_POST );
		}
	}

	/**
	 * Honeypot preprocessor
	 */
	public function honeypot_after() {
		if ( ! isset ( $_POST['wpmtst_after'] ) ) {
			do_action( 'honeypot_after_spam_testimonial', $_POST );
		}
	}

	/**
	 * Honeypot error
	 */
	public function honeypot_error() {
		$form_options = get_option( 'wpmtst_form_options' );
		$messages     = $form_options['messages'];
		$part         = 'submission-error';
		if ( isset( $messages[ $part ]['text'] ) ) {
			$message = apply_filters( 'wpmtst_form_message', $messages['submission-error']['text'], $messages[ $part ] );
		} else {
			$message = __( 'Unknown error.', 'strong-testimonials' );
		}
		wp_die( $message );
	}

	/**
	 * Restrict MIME types for security reasons.
	 *
	 * @param $mimes
	 *
	 * @return array
	 */
	public function restrict_mime( $mimes ) {
		$mimes = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
		);

		return $mimes;
	}

	/**
	 * File upload handler
	 *
	 * @param $file_handler
	 * @param $overrides
	 *
	 * @return array
	 */
	public function handle_upload( $file_handler, $overrides ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$upload = wp_handle_upload( $file_handler, $overrides );

		return $upload;
	}

	/**
	 * Send notification email upon testimonial submission.
	 *
	 * @param array $post
	 * @param string $form_name
	 *
	 * @since 1.7.0
	 */
	public function notify_admin( $post, $form_name = 'custom' ) {
		$form_options = get_option( 'wpmtst_form_options' );
		if ( ! $form_options['admin_notify'] ) {
			return;
		}

		$fields = wpmtst_get_form_fields( $form_name );

		if ( $form_options['sender_site_email'] ) {
			$sender_email = get_bloginfo( 'admin_email' );
		}
		else {
			$sender_email = $form_options['sender_email'];
		}

		// Subject line
		$subject = $form_options['email_subject'];
		$subject = str_replace( '%BLOGNAME%', get_bloginfo( 'name' ), $subject );
		$subject = str_replace( '%TITLE%', $post['post_title'], $subject );
		$subject = str_replace( '%STATUS%', $post['post_status'], $subject );
		$subject = $this->replace_custom_fields( $subject, $fields, $post );

		// Message text
		$message = $form_options['email_message'];
		$message = str_replace( '%BLOGNAME%', get_bloginfo( 'name' ), $message );
		$message = str_replace( '%TITLE%', $post['post_title'], $message );
		$message = str_replace( '%CONTENT%', $post['post_content'], $message );
		$message = str_replace( '%STATUS%', $post['post_status'], $message );
		$message = $this->replace_custom_fields( $message, $fields, $post );

		foreach ( $form_options['recipients'] as $recipient ) {

			if ( isset( $recipient['admin_site_email'] ) && $recipient['admin_site_email'] ) {
				$admin_email = get_bloginfo( 'admin_email' );
			}
			else {
				$admin_email = $recipient['admin_email'];
			}

			// Mandrill rejects the 'name <email>' format
			if ( $recipient['admin_name'] && ! $form_options['mail_queue'] ) {
				$to = sprintf( '%s <%s>', $recipient['admin_name'], $admin_email );
			}
			else {
				$to = sprintf( '%s', $admin_email );
			}

			// Headers
			$headers = 'MIME-Version: 1.0' . "\n";
			$headers .= 'Content-Type: text/plain; charset="' . get_option( 'blog_charset' ) . '"' . "\n";
			if ( $form_options['sender_name'] ) {
				$headers .= sprintf( 'From: %s <%s>', $form_options['sender_name'], $sender_email ) . "\n";
			}
			else {
				$headers .= sprintf( 'From: %s', $sender_email ) . "\n";
			}

			$email = array( 'to' => $to, 'subject' => $subject, 'message' => $message, 'headers' => $headers );

			if ( $form_options['mail_queue'] ) {
				WPMST()->mail->enqueue_mail( $email );
			}
			else {
				WPMST()->mail->send_mail( $email );
			}

		} // for each recipient
	}

	/**
	 * Replace tags for custom fields.
	 *
	 * @param $text
	 * @param $fields
	 * @param $post
	 *
	 * @return string
	 */
	private function replace_custom_fields( $text, $fields, $post ) {
		foreach ( $fields as $field ) {

			$replace    = '(blank)';
			$post_field = isset( $post[ $field['name'] ] ) ? $post[ $field['name'] ] : false;

			if ( $post_field ) {
				if ( 'category' == $field['name'] ) {
					$term = get_term( $post_field, 'wpm-testimonial-category' );
					if ( $term && ! is_wp_error( $term ) ) {
						$replace = $term->name;
					}
				}
				elseif ( 'rating' == $field['input_type'] ) {
					$replace = $post_field . ' ' . _n( 'star', 'stars', $post_field, 'strong-testimonials' );
				}
				elseif ( 'checkbox' == $field['input_type'] ) {
					$replace = $post_field ? 'yes' : 'no';
				}
				else {
					$replace = $post_field;
				}
			}

			$replace   = apply_filters( 'wpmtst_notification_custom_field_value', $replace, $field, $post );
			$field_tag = '%' . strtoupper( $field['name'] ) . '%';
			$text      = str_replace( $field_tag, $replace, $text );
		}

		return $text;
	}

}
