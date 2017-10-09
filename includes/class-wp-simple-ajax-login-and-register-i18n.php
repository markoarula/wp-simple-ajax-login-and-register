<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/markoarula
 * @since      1.0.0
 *
 * @package    WP_Simple_Ajax_Login_And_Register
 * @subpackage WP_Simple_Ajax_Login_And_Register/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    WP_Simple_Ajax_Login_And_Register
 * @subpackage WP_Simple_Ajax_Login_And_Register/includes
 * @author     Marko Arula <markoarula21@gmail.com>
 */
class WP_Simple_Ajax_Login_And_Register_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-simple-ajax-login-and-register',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
