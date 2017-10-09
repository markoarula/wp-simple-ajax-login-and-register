<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/markoarula
 * @since      1.0.0
 *
 * @package    WP_Simple_Ajax_Login_And_Register
 * @subpackage WP_Simple_Ajax_Login_And_Register/includes
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Simple_Ajax_Login_And_Register
 * @subpackage WP_Simple_Ajax_Login_And_Register/includes
 * @author     Marko Arula <markoarula21@gmail.com>
 */
class WP_Simple_Ajax_Login_And_Register_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wp_simple_ajax_login_and_register    The ID of this plugin.
	 */
	private $wp_simple_ajax_login_and_register;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param  string $wp_simple_ajax_login_and_register       The name of this plugin.
	 * @param  string $version    The version of this plugin.
	 */
	public function __construct( $wp_simple_ajax_login_and_register, $version ) {

		$this->wp_simple_ajax_login_and_register = $wp_simple_ajax_login_and_register;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->wp_simple_ajax_login_and_register, plugin_dir_url( __FILE__ ) . 'css/wp-simple-ajax-login-and-register.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->wp_simple_ajax_login_and_register, plugin_dir_url( __FILE__ ) . 'js/wp-simple-ajax-login-and-register.js', array( 'jquery' ), $this->version, false );
		$ajaxurl = '';
		if ( in_array( 'sitepress-multilingual-cms/sitepress.php', get_option( 'active_plugins' ), true ) ) {
			$ajaxurl .= admin_url( 'admin-ajax.php?lang=' . ICL_LANGUAGE_CODE );
		} else {
			$ajaxurl .= admin_url( 'admin-ajax.php' );
		}
		wp_localize_script( $this->wp_simple_ajax_login_and_register, 'ajax_login_object', array(
			'ajaxurl'          => $ajaxurl,
			'logouturl'        => wp_logout_url( home_url() ),
			'redirecturl'      => esc_url( home_url() ),
			'success_register' => esc_html__( 'User created. Activate via email', 'wp-simple-ajax-login-and-register' ),
			'mail_sent'  	   => esc_html__( 'Reset password mail sent', 'wp-simple-ajax-login-and-register' ),
			'logouttext'       => esc_html__( 'Logout', 'wp-simple-ajax-login-and-register' ),
			'loadingmessage'   => esc_html__( 'Sending user info, please wait...', 'wp-simple-ajax-login-and-register' ),
			'pass_no_match'    => esc_html__( 'Passwords do not match.', 'wp-simple-ajax-login-and-register' ),
			'term_of_use'      => esc_html__( 'Please accept the terms of use.', 'wp-simple-ajax-login-and-register' ),
			'used_email'       => esc_html__( 'Email is already registered.', 'wp-simple-ajax-login-and-register' ),
			'faild_register'   => esc_html__( 'Registration has not passed, please make sure all fields are properly filled in.', 'wp-simple-ajax-login-and-register' ),
		));
	}

	/**
	 * Add login hook
	 *
	 * This function will hook to wp_footer hook and add the login layout.
	 *
	 * @since 1.0.0
	 */
	public function mytheme_add_login_form() {
	?>
		<div class="wp_simple_ajax_login_and_register">
			<form id="mytheme_login" action="login" method="post">
				<h3><?php esc_attr_e( 'User login', 'wp-simple-ajax-login-and-register' ); ?></h3>
				<p class="status"></p>
				<input id="username" type="text" name="username" placeholder="<?php esc_attr_e( 'Username', 'wp-simple-ajax-login-and-register' ); ?>">
				<input id="password" type="password" name="password" placeholder="<?php esc_attr_e( 'Password', 'wp-simple-ajax-login-and-register' ); ?>">
				<input class="submit_button" type="submit" value="<?php esc_attr_e( 'Login', 'wp-simple-ajax-login-and-register' ); ?>" name="submit">
				<div class="forgotten_box">
					<a class="mytheme_register" href="#"><?php esc_attr_e( 'Not registered? Click here.', 'wp-simple-ajax-login-and-register' ); ?></a>
					<a class="mytheme_lost_password" href="#"><?php esc_attr_e( 'Lost your password?', 'wp-simple-ajax-login-and-register' ); ?></a>
				</div>
				<?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
			</form>
			<form id="mytheme_register" action="register" method="post">
				<h3><?php esc_attr_e( 'Register', 'wp-simple-ajax-login-and-register' ); ?></h3>
				<p class="status"></p>
				<!-- <input id="user_login" type="text" name="user_login" placeholder="<?php esc_attr_e( 'Email', 'wp-simple-ajax-login-and-register' ); ?>"> -->
				<input id="user_email" type="text" name="user_email" placeholder="<?php esc_attr_e( 'Email', 'wp-simple-ajax-login-and-register' ); ?>">
				<input id="account_password" type="password" name="account_password" placeholder="<?php esc_attr_e( 'Password', 'wp-simple-ajax-login-and-register' ); ?>">
				<input id="account_password_repeat" type="password" name="account_password_repeat" placeholder="<?php esc_attr_e( 'Repeat Password', 'wp-simple-ajax-login-and-register' ); ?>">
				<input id="terms" type="checkbox" name="terms">
				<label for="terms" class="checkbox"><?php esc_html_e( 'I\'ve read and accept the ', 'wp-simple-ajax-login-and-register' ); ?><a href="<?php echo esc_url( get_the_permalink( get_theme_mod( 'ajax_login_tos', '' ) ) ); ?>" target="_blank"><?php esc_html_e( 'terms &amp; conditions', 'wp-simple-ajax-login-and-register' ); ?></a></label>
				<input class="submit_button" type="submit" value="<?php esc_attr_e( 'Register', 'wp-simple-ajax-login-and-register' ); ?>" name="submit">
				<?php if ( ! is_user_logged_in() ) : ?>
				<div class="forgotten_box">
					<a class="go_back_to_login" href="#"><?php esc_attr_e( 'Already registered? Log in.', 'wp-simple-ajax-login-and-register' ); ?></a>
				</div>
				<?php endif;
				wp_nonce_field( 'user_register_nonce', 'user_register' ); ?>
			</form>
			<form id="mytheme_forgotten_pass" action="lostpassword" method="post">
				<h3><?php esc_attr_e( 'Lost password', 'wp-simple-ajax-login-and-register' ); ?></h3>
				<p><?php esc_html_e( 'Please enter your email address. You will receive a link to create a new password via email.', 'wp-simple-ajax-login-and-register' ); ?></p>
				<p class="status"></p>
				<input id="user_forgotten_email" type="text" name="user_forgotten_email" placeholder="<?php esc_attr_e( 'Email', 'wp-simple-ajax-login-and-register' ); ?>">
				<input class="submit_button" type="submit" value="<?php esc_attr_e( 'Get new password', 'wp-simple-ajax-login-and-register' ); ?>" name="submit">
				<div class="forgotten_box">
					<a class="go_back_to_login" href="#"><?php esc_attr_e( 'Already registered? Log in.', 'wp-simple-ajax-login-and-register' ); ?></a>
					<a class="go_back_to_register" href="#"><?php esc_attr_e( 'Not registered? Click here.', 'wp-simple-ajax-login-and-register' ); ?></a>
				</div>
				<?php wp_nonce_field( 'user_get_password_nonce', 'user_get_password' ); ?>
			</form>
			<div class="wp_simple_ajax_login_and_register_overlay"></div>
		</div>
	<?php
	}

	/**
	 * Register customizer settings for login
	 *
	 * @see add_action('customize_register',$func)
	 * @param  WP_Customize_Manager $wp_customize WP Customize object.
	 * @since 1.0.0
	 * @access public
	 */
	public function wp_simple_ajax_login_and_register_customize_register( WP_Customize_Manager $wp_customize ) {
		/**
		------------------------------------------------------------
		Panel: AJAX Login
		------------------------------------------------------------
		*/
		$wp_customize->add_section( 'wp_simple_ajax_login_and_register', array(
			'title'	   => esc_html__( 'AJAX Login Settings', 'wp-simple-ajax-login-and-register' ),
			'priority' => 20,
		) );

		/**
		EDD Title
		*/
		$wp_customize->add_setting( 'ajax_login_tos', array(
			'default'           => '',
			'sanitize_callback' => 'esc_html',
		) );
		$wp_customize->add_control( 'ajax_login_tos', array(
			'label'    	  => esc_html__( 'Terms of Use page', 'wp-simple-ajax-login-and-register' ),
			'description' => esc_html__( 'Choose the page for Terms of Use.', 'wp-simple-ajax-login-and-register' ),
			'type'  	  => 'dropdown-pages',
			'section'  	  => 'wp_simple_ajax_login_and_register',
		) );
	}

	/**
	 * Exclude login nav menu item
	 *
	 * @param  array  $items Array of menu item objects.
	 * @param  object $menu  Menu object.
	 * @param  array  $args  Arguments to pass to get_posts().
	 * @return array		 Modified menu with added menu item.
	 * @since 1.0.0
	 */
	public function mytheme_exclude_menu_items( $items, $menu, $args ) {
		foreach ( $items as $menu_item_key => $menu_item_value ) {
			if ( 'mytheme_show_login' === $menu_item_value->classes[0] ) {
				$post_id = $menu_item_value->object_id;
				if ( is_user_logged_in() ) {
					$menu_item_value->post_title   = esc_html__( 'Logout', 'wp-simple-ajax-login-and-register' );
					$menu_item_value->post_excerpt = esc_html__( 'Logout', 'wp-simple-ajax-login-and-register' );
					$menu_item_value->title        = esc_html__( 'Logout', 'wp-simple-ajax-login-and-register' );
					$menu_item_value->attr_title   = esc_html__( 'Logout', 'wp-simple-ajax-login-and-register' );
					$menu_item_value->classes[0]   = '';
					$menu_item_value->classes[0]   = 'mytheme_logout';
				}
			}
			if ( 'mytheme_logout' === $menu_item_value->classes[0] ) {
				$post_id = $menu_item_value->object_id;
				if ( ! is_user_logged_in() ) {
					$menu_item_value->post_title   = esc_html__( 'Login', 'wp-simple-ajax-login-and-register' );
					$menu_item_value->post_excerpt = esc_html__( 'Login', 'wp-simple-ajax-login-and-register' );
					$menu_item_value->title        = esc_html__( 'Login', 'wp-simple-ajax-login-and-register' );
					$menu_item_value->attr_title   = esc_html__( 'Login', 'wp-simple-ajax-login-and-register' );
					$menu_item_value->classes[0]   = '';
					$menu_item_value->classes[0]   = 'mytheme_show_login';
				}
			}
		}
		return $items;
	}

	/**
	 * Redirect if user is not activated
	 *
	 * @param string  $user_login Username.
	 * @param WP_User $user       WP_User object of the logged-in user.
	 * @since 1.0.0
	 */
	public function mytheme_redirect_inactive_user( $user_login, $user ) {
		if ( '0' === get_user_meta( $user->ID, 'user_activated', true ) ) {
			wp_logout();
			wp_safe_redirect( home_url() );
			wp_die( esc_html( 'User not activated. Check your confirmation email.', 'wp-simple-ajax-login-and-register' ) );
		}
	}

	/**
	 * Activate user when user clicks on the confirmation e mail
	 *
	 * @since 1.0.0
	 */
	public function mytheme_activate_user() {
		if ( isset( $_GET['user_nonce'], $_GET['user_id'] ) && '' !== $_GET['user_nonce'] && '' !== $_GET['user_id'] ) { // Input var okay.

			$user = get_user_by( 'ID', sanitize_text_field( wp_unslash( $_GET['user_id'] ) ) ); // Input var okay.
			$user_id = $user->ID;

			if ( '1' === get_user_meta( $user_id, 'user_activated', true ) ) {
				wp_die( sprintf( wp_kses_post( 'User %1$s already activated. Go to <a href="%2$s">homepage</a> or <a href="%3$s">log in</a>', 'wp-simple-ajax-login-and-register' ), esc_html( $user->data->user_login ), esc_url( get_home_url() ), esc_url( wp_login_url( get_permalink() ) ) ) );
			} else {
				if ( username_exists( $user->data->user_login ) ) {
					update_user_meta( $user_id, 'user_activated', '1', '0' );
					$user_query = new WP_User( $user_id );
					$user_query->add_role( 'subscriber' );
				}
				wp_die( sprintf( wp_kses_post( 'User %1$s successfully activated. Go to <a href="%2$s">homepage</a> or <a href="%3$s">log in</a>', 'wp-simple-ajax-login-and-register' ), esc_html( $user->data->user_login ), esc_url( get_home_url() ), esc_url( wp_login_url( get_permalink() ) ) ) );
			}
		}
	}

}
