<?php
/**
 * The plugin bootstrap file
 *
 * The plugin is used to toggle ajax form for the user login.
 * All that is needed is to add the class mytheme_show_login on a button
 * that will be used as a login button, and a modal will show and log in
 * the user.
 *
 * @link              https://github.com/markoarula
 * @since             1.0.0
 * @package           WP_Simple_Ajax_Login_And_Register
 *
 * @wordpress-plugin
 * Plugin Name:       WP Simple Ajax Login and Register
 * Plugin URI:        https://github.com/markoaruladownloads/ajax-login/
 * Description:       AJAX based login form
 * Version:           1.0.0
 * Author:            Marko Arula
 * Author URI:        https://github.com/markoarula
 * License:       	  https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * Text Domain:       wp-simple-ajax-login-and-register
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-simple-ajax-login-and-register.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_simple_ajax_login_and_register() {

	$plugin = new WP_Simple_Ajax_Login_And_Register();
	$plugin->run();

}

if ( ! function_exists( 'wp_simple_ajax_login_and_register_errors' ) ) {
	/**
	 * Errors function
	 *
	 * @since 1.0.0
	 */
	function wp_simple_ajax_login_and_register_errors() {
		static $wp_error; // Will hold global variable safely.
		return isset( $wp_error ) ? $wp_error : ( $wp_error = new WP_Error( null, null, null ) );
	}
}

add_action( 'wp_ajax_mytheme_ajax_login', 'mytheme_ajax_login' );
add_action( 'wp_ajax_nopriv_mytheme_ajax_login', 'mytheme_ajax_login' );

/**
 * Ajax login function
 *
 * @since 1.0.0
 */
function mytheme_ajax_login() {
	if ( is_user_logged_in() ) {
		return;
	}
	if ( isset( $_POST['username'], $_POST['password'], $_POST['security'] ) && wp_verify_nonce( sanitize_key( $_POST['security'] ), 'ajax-login-nonce' ) ) { // Input var okay.

		$username       = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : ''; // Input var okay.
		$password       = isset( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : ''; // Input var okay.
		$user           = get_user_by( 'login', $username );
		$user_id        = $user->ID;
		$user_activated = get_user_meta( $user_id, 'user_activated', true );

		if ( '' === $username ) {
			// empty username.
			wp_simple_ajax_login_and_register_errors()->add( 'username', esc_html__( 'Please enter a username; #username', 'wp-simple-ajax-login-and-register' ) );
		}
		if ( ! username_exists( $username ) ) {
			// Username already registered.
			wp_simple_ajax_login_and_register_errors()->add( 'username_unavailable', esc_html__( 'Username doesn\'t exist; #username', 'wp-simple-ajax-login-and-register' ) );
		}
		if ( '0' === $user_activated ) {
			// User not activated!
			wp_simple_ajax_login_and_register_errors()->add( 'user_not_activated', esc_html__( 'User isn\'t activated; #username', 'wp-simple-ajax-login-and-register' ) );
		}
		if ( '' === $password ) {
			// password emoty.
			wp_simple_ajax_login_and_register_errors()->add( 'password_empty', esc_html__( 'Please enter a password; #password', 'wp-simple-ajax-login-and-register' ) );
		}
		if ( ! wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
			// Wrong password.
			wp_simple_ajax_login_and_register_errors()->add( 'password_wrong', esc_html__( 'Please enter a correct password; #password', 'wp-simple-ajax-login-and-register' ) );
		}

		$errors = wp_simple_ajax_login_and_register_errors()->get_error_messages();

		if ( ! empty( $errors ) ) {
			wp_die( wp_json_encode( $errors ) );
		} else {
			wp_set_auth_cookie( $user->ID, false );
			wp_set_current_user( $user->ID, $username );
			do_action( 'wp_login', $username );
			wp_die( 'user_logged' );
		}
	}
}

add_action( 'wp_ajax_mytheme_ajax_register', 'mytheme_ajax_register' );
add_action( 'wp_ajax_nopriv_mytheme_ajax_register', 'mytheme_ajax_register' );

/**
 * Ajax login function
 *
 * @since 1.0.0
 */
function mytheme_ajax_register() {
	if ( is_user_logged_in() ) {
		return;
	}
	if ( isset( $_POST['user_register'] ) && wp_verify_nonce( sanitize_key( $_POST['user_register'] ), 'user_register_nonce' ) ) { // Input var okay.

		$user_email       = isset( $_POST['user_email'] ) ? sanitize_text_field( wp_unslash( $_POST['user_email'] ) ) : ''; // Input var okay.
		$account_password = isset( $_POST['account_password'] ) ? sanitize_text_field( wp_unslash( $_POST['account_password'] ) ) : ''; // Input var okay.
		$terms            = isset( $_POST['terms'] ) ? sanitize_text_field( wp_unslash( $_POST['terms'] ) ) : ''; // Input var okay.

		if ( ! is_email( $user_email ) ) {
			// Invalid email.
			wp_simple_ajax_login_and_register_errors()->add( 'email_invalid', esc_html__( 'Invalid email; #user_email', 'wp-simple-ajax-login-and-register' ) );
		}
		if ( email_exists( $user_email ) ) {
			// Email address already registered.
			wp_simple_ajax_login_and_register_errors()->add( 'email_used', esc_html__( 'Email already registered; #user_email', 'wp-simple-ajax-login-and-register' ) );
		}
		if ( 'on' !== $terms ) {
			// passwords do not match.
			wp_simple_ajax_login_and_register_errors()->add( 'terms_not_checked', esc_html__( 'Please accept the terms and conditions; #terms', 'wp-simple-ajax-login-and-register' ) );
		}
		if ( '' === $account_password ) {
			// passwords do not match.
			wp_simple_ajax_login_and_register_errors()->add( 'password_empty', esc_html__( 'Please enter a password; #account_password', 'wp-simple-ajax-login-and-register' ) );
		}

		$errors = wp_simple_ajax_login_and_register_errors()->get_error_messages();

		// only create the user in if there are no errors.
		if ( empty( $errors ) ) {
			$new_user_id = wp_insert_user( array(
					'user_login'      => $user_email,
					'user_pass'       => $account_password,
					'user_email'      => $user_email,
					'user_registered' => date( 'Y-m-d H:i:s' ),
					// 'role'            => '',
				)
			);

			if ( ! function_exists( 'mytheme_random_str' ) ) {
				/**
				 * Random string generator
				 *
				 * @param  int 	  $length   Length of the string.
				 * @param  string $keyspace Available keyspace (optional).
				 * @return string           Random string.
				 */
				function mytheme_random_str( $length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ) {
					$str = '';
					$max = mb_strlen( $keyspace, '8bit' ) - 1;
					for ( $i = 0; $i < $length; ++$i ) {
						$str .= $keyspace[ random_int( 0, $max ) ];
					}
					return $str;
				}
			}

			add_user_meta( $new_user_id, 'user_activated', '0' );
			add_user_meta( $new_user_id, 'user_nonce', mytheme_random_str( 20 ) );

			if ( $new_user_id ) {
				$user_info = get_userdata($new_user_id);
				$user_pass = $user_info->user_pass;
				// send an email to the admin alerting them of the registration.
				$email_subject = sprintf( esc_html__( 'Welcome to %1$s %2$s', 'wp-simple-ajax-login-and-register' ), get_site_url(), $user_login );
				$site_name = get_bloginfo( 'name' );
				$from = get_bloginfo( 'admin_email' );

				// $message_text = sprintf( wp_kses_post( ' Dear %1$s,<br /><br />Thank you for registering to %2$s.<br /><br />In order to activate your account, you\'ll need to <a href="%3$s">confirm your email</a>.<br /><br />Cheers!', 'wp-simple-ajax-login-and-register' ), $user_login,  get_site_url(), get_home_url( '', '/?user_nonce=' . esc_attr( get_user_meta( $new_user_id, 'user_nonce', true ) ) . '&user_id=' . esc_attr( $new_user_id ) ) );

				$message_text = sprintf( wp_kses_post( '
					Dear, <br /><br />

					Thank you for registering to %2$s.<br /><br />

					Below are your login details:<br /><br />

					username: %1$s <br />
					password: xxxxxxxxxxxx <br /><br />

					Sign in via link: <a href="%3$s">confirm your registration</a><br /><br />

					Kind regards,<br /><br />

					' . $site_name . '<br />
					' . $from . '

					', 'wp-simple-ajax-login-and-register' ), $user_login,  get_site_url(), get_home_url( '', '/?user_nonce=' . esc_attr( get_user_meta( $new_user_id, 'user_nonce', true ) ) . '&user_id=' . esc_attr( $new_user_id ) ) );

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
				$headers .= 'From:' . $from . "\r\n" . 'X-Mailer: php';

				wp_mail( $user_email, $email_subject, $message_text, $headers );
			}
			wp_die( 'user_registered' );
		} else {
			wp_die( wp_json_encode( $errors ) );
		}
	}
}

add_action( 'wp_ajax_mytheme_lost_password', 'mytheme_lost_password' );
add_action( 'wp_ajax_nopriv_mytheme_lost_password', 'mytheme_lost_password' );

/**
 * Ajax lost password function
 *
 * @since 1.0.0
 */
function mytheme_lost_password() {
	if ( is_user_logged_in() ) {
		return;
	}
	if ( isset( $_POST['user_forgotten_email'], $_POST['user_get_password'] ) && wp_verify_nonce( sanitize_key( $_POST['user_get_password'] ), 'user_get_password_nonce' ) ) { // Input var okay.

		$user_forgotten_email = isset( $_POST['user_forgotten_email'] ) ? sanitize_text_field( wp_unslash( $_POST['user_forgotten_email'] ) ) : ''; // Input var okay.

		if ( ! is_email( $user_forgotten_email ) ) {
			// Invalid email.
			wp_simple_ajax_login_and_register_errors()->add( 'email_invalid', esc_html__( 'Invalid email; #user_forgotten_email', 'wp-simple-ajax-login-and-register' ) );
		}
		if ( ! email_exists( $user_forgotten_email ) ) {
			// Email address already registered.
			wp_simple_ajax_login_and_register_errors()->add( 'email_nonexistent', esc_html__( 'Email doesn\'t exist; #user_forgotten_email', 'wp-simple-ajax-login-and-register' ) );
		}

		$errors = wp_simple_ajax_login_and_register_errors()->get_error_messages();

		// only create the user in if there are no errors.
		if ( empty( $errors ) ) {

			$user_data = get_user_by( 'email', trim( sanitize_text_field( $user_forgotten_email ) ) );

			$user_login = $user_data->data->user_login;
			$user_email = $user_data->data->user_email;

			do_action( 'retrieve_password', $user_login );

			$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

			if ( ! $allow ) {
				return false;
			} elseif ( is_wp_error( $allow ) ) {
				return false;
			} else {
				global $wpdb, $wp_hasher;

				$key = wp_generate_password( 20, false );
				do_action( 'retrieve_password_key', $user_login, $key );

				if ( empty( $wp_hasher ) ) {
					require_once ABSPATH . 'wp-includes/class-phpass.php';
					$wp_hasher = new PasswordHash( 8, true );
				}
				$hashed = $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
				$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );

				// send an email to the admin alerting them of the registration.
				$email_subject = esc_html__( 'Password reset', 'wp-simple-ajax-login-and-register' );
				$from = get_bloginfo( 'admin_email' );

				$message_text = sprintf( wp_kses_post( 'Someone used your email to reset your password on %1$s. If this was not you, you can contact the administrators on the site or disregard this mail. If this was you, then you can click <a href="%2$s">this link</a> to generate a new password for %1$s', 'wp-simple-ajax-login-and-register' ), get_site_url(), network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) );

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
				$headers .= "From: $from\r\n" . 'X-Mailer: php';

				wp_mail( $user_forgotten_email, $email_subject, $message_text, $headers );
				wp_die( 'mail_sent' );
			}
		} else {
			wp_die( wp_json_encode( $errors ) );
		}
	}
}

run_wp_simple_ajax_login_and_register();
