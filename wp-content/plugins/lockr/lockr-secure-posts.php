<?php
/**
 * Lockr integration with Gutenberg to encrtpt raw content.
 *
 * @package Lockr
 */

// Don't call the file directly and give up info!
if ( ! function_exists( 'add_action' ) ) {
	echo 'Lock it up!';
	exit;
}

/**
 * Encrypt data coming out of the post editor when password protected, private or unpublished.
 *
 * @param array $data The post data to be saved.
 * @param array $postarr The post array to be saved.
 */
function lockr_secure_post_encrypt( $data, $postarr ) {
	$encrypt_posts = get_option( 'lockr_encrypt_posts' );
	$hash_pass     = get_option( 'lockr_hash_pass' );

	if ( ! empty( $postarr['post_password'] && ! empty( $encrypt_posts ) ) ) {
		$data['post_content'] = lockr_encrypt( $data['post_content'] );
	}
	if ( ! empty( $hash_pass ) ) {
		$post_password = $data['post_password'];

		// Check for New Password.
		if ( '' !== $post_password && '****' !== $post_password && 0 !== strpos( $post_password, '$P$B' ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$hasher = new PasswordHash( 8, true );

			$hashed_password = $hasher->HashPassword( wp_unslash( $post_password ) );

			$data['post_password'] = $hashed_password;
		} else {
			$data['post_password'] = $post_password;
		}
	}

	return $data;
}

add_filter( 'wp_insert_post_data', 'lockr_secure_post_encrypt', '10000', 2 );

/**
 * Decrypt data coming out of the post editor when password protected, private or unpublished.
 *
 * @param string $content The content of the post.
 */
function lockr_secure_post_decrypt( $content ) {
	global $post;

	if ( ! empty( $post->post_password ) ) {
		$content_parts = json_decode( $content, true );
		$post_content  = $post->post_content;
		if ( null !== $content_parts && isset( $content_parts['key_name'] ) ) {
			// Content is encrypted.
			if ( null === json_decode( $post_content ) ) {
				$content = $post_content;
			} else {
				$content_decrypted = lockr_decrypt( $content );
				if ( null !== $content_decrypted ) {
					$content = $content_decrypted;
				}
			}
		}
	}

	return $content;
}

add_filter( 'the_content', 'lockr_secure_post_decrypt', '-10000' );

/**
 * Decrypt data coming out of the post query when password protected, private or unpublished.
 */
function lockr_secure_post_result_decrypt() {
	global $post;

	if ( ! empty( $post->post_password ) ) {

		$post_content           = $post->post_content;
		$post_content_decrypted = lockr_decrypt( $post_content );
		if ( null !== $post_content_decrypted ) {
			$post->post_content = wp_unslash( $post_content_decrypted );
		}
	}
}

add_action( 'the_post', 'lockr_secure_post_result_decrypt', '-10000' );

/**
 * Set a session with the postpass instead of the current cookie hashing method.
 */
function lockr_secure_post_postpass_check() {
	$hash_pass = get_option( 'lockr_hash_pass' );

	if ( class_exists( Pantheon_sessions ) && ! empty( $hash_pass ) ) {

		session_start();
		require_once ABSPATH . WPINC . '/class-phpass.php';
		$hasher = new PasswordHash( 8, true );
		// Check the fingerprint to make sure the session isn't being hijacked.
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && $hasher->CheckPassword( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), $_SESSION['fingerprint'] ) ) {
			// Check to see if we have a current post set.
			if ( isset( $_SESSION['current_post'], $_POST['post_password'] ) ) { // WPCS: CSRF ok.
				$checkpost  = get_post( $_SESSION['current_post'] );
				$post_hash  = $checkpost->post_password;
				$pass_match = $hasher->CheckPassword( wp_unslash( $_POST['post_password'] ), $post_hash ); // WPCS: sanitization ok, CSRF ok.

				$_SESSION[ 'post_' . $_SESSION['current_post'] ] = $post_hash;
			}
		}
	}
}

add_action( 'login_form_postpass', 'lockr_secure_post_postpass_check' );

/**
 * Set a session with the postpass instead of the current cookie hashing method.
 */
function lockr_secure_post_postpass_session() {
	global $wp;
	global $post;
	$hash_pass = get_option( 'lockr_hash_pass' );
	if ( class_exists( 'Pantheon_sessions' ) && ! empty( $post->post_password ) && ! empty( $hash_pass ) ) {

		if ( ! session_id() ) {
			session_start();
		}
		if ( ! isset( $_SESSION['fingerprint'] ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$hasher = new PasswordHash( 8, true );
			if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
				$_SESSION['fingerprint'] = $hasher->HashPassword( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) );
			}
		}
	}
}

add_action( 'wp', 'lockr_secure_post_postpass_session' );

/**
 * Check for hashed passwords used for protecting posts.
 *
 * @param bool  $required If a password form is required.
 * @param array $post The post in question.
 */
function lockr_password_form_check( $required, $post ) {
	if ( '' === $post->post_password ) {
		return false;
	} else {
		$hash_pass = get_option( 'lockr_hash_pass' );
		if ( class_exists( Pantheon_sessions ) && ! empty( $hash_pass ) ) {
			$_SESSION['current_post'] = $post->ID;
			if ( isset( $_SESSION[ 'post_' . $post->ID ] ) ) {
				return $post->post_password !== $_SESSION[ 'post_' . $post->ID ];
			} else {
				$_SESSION[ 'post_' . $post->ID ] = false;
				return true;
			}
		}
	}
}

add_filter( 'post_password_required', 'lockr_password_form_check', 10000, 2 );
