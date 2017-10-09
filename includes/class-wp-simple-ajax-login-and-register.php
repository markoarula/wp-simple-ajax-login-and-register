<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/markoarula
 * @since      1.0.0
 *
 * @package    WP_Simple_Ajax_Login_And_Register
 * @subpackage WP_Simple_Ajax_Login_And_Register/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Simple_Ajax_Login_And_Register
 * @subpackage WP_Simple_Ajax_Login_And_Register/includes
 * @author     Marko Arula <markoarula21@gmail.com>
 */
class WP_Simple_Ajax_Login_And_Register {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WP_Simple_Ajax_Login_And_Register_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $wp_simple_ajax_login_and_register    The string used to uniquely identify this plugin.
	 */
	protected $wp_simple_ajax_login_and_register;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->wp_simple_ajax_login_and_register = 'wp-simple-ajax-login-and-register';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_public_hooks();
		$this->define_login_metabox();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Simple_Ajax_Login_And_Register_Loader. Orchestrates the hooks of the plugin.
	 * - WP_Simple_Ajax_Login_And_Register_i18n. Defines internationalization functionality.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-simple-ajax-login-and-register-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-simple-ajax-login-and-register-i18n.php';

		/**
		 * The class responsible for adding modified topbars to the theme.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-simple-ajax-login-and-register-admin.php';

		/**
		 * The class responsible for adding meta box for login in the nav-menu page
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-simple-ajax-login-and-register-menu.php';

		$this->loader = new WP_Simple_Ajax_Login_And_Register_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Simple_Ajax_Login_And_Register_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WP_Simple_Ajax_Login_And_Register_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new WP_Simple_Ajax_Login_And_Register_Admin( $this->get_wp_simple_ajax_login_and_register(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'mytheme_add_login_form' );
		$this->loader->add_filter( 'wp_get_nav_menu_items', $plugin_public, 'mytheme_exclude_menu_items', 10, 3 );
		$this->loader->add_action( 'customize_register', $plugin_public, 'wp_simple_ajax_login_and_register_customize_register' );
		$this->loader->add_action( 'wp_login', $plugin_public, 'mytheme_redirect_inactive_user', 10, 2 );
		$this->loader->add_action( 'init', $plugin_public, 'mytheme_activate_user', 10 );
	}

	/**
	 * Register hook for adding the metabox in the nav menu page.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_login_metabox() {
		$menu_metabox = new Mytheme_Login_Menu_Metabox( $this->get_wp_simple_ajax_login_and_register(), $this->get_version() );
		$this->loader->add_action( 'admin_init', $menu_metabox, 'mytheme_add_nav_menu_meta_box' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_wp_simple_ajax_login_and_register() {
		return $this->wp_simple_ajax_login_and_register;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WP_Simple_Ajax_Login_And_Register_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
