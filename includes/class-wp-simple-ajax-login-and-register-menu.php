<?php
/**
 * Login menu item in nav menu meta box
 *
 * @link       https://github.com/markoarula
 * @since      1.0.0
 *
 * @package    WP_Simple_Ajax_Login_And_Register
 * @subpackage WP_Simple_Ajax_Login_And_Register/includes
 */

/**
 * Login menu metabox class
 *
 * Will add login meta box in nav menu page.
 *
 * @package    WP_Simple_Ajax_Login_And_Register
 * @subpackage WP_Simple_Ajax_Login_And_Register/includes
 * @author     Marko Arula <markoarula21@gmail.com>
 */
class Mytheme_Login_Menu_Metabox {
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
	 * Add nav menu meta box with login menu item.
	 *
	 * @since 1.0.0
	 */
	public function mytheme_add_nav_menu_meta_box() {
		add_meta_box(
			'mytheme_login_menu_add',
			esc_html__( 'Add Login Menu Item', 'wp-simple-ajax-login-and-register' ),
			array( $this, 'mytheme_login_nav_menu_link' ),
			'nav-menus',
			'side',
			'low'
		);
	}

	/**
	 * Adds content to the sidebar login
	 *
	 * @since 1.0.0
	 */
	public function mytheme_login_nav_menu_link() {
		?>
		<div id="login" class="posttypediv">
			<div id="tabs-panel-login" class="tabs-panel tabs-panel-active">
				<ul id ="login-checklist" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="menu_login"> <?php esc_html_e( 'Login', 'wp-simple-ajax-login-and-register' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-attr-title" name="menu-item[-1][menu-item-attr-title]" value="<?php esc_html_e( 'Login', 'wp-simple-ajax-login-and-register' ); ?>">
						<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php esc_html_e( 'Login', 'wp-simple-ajax-login-and-register' ); ?>" />
						<input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="#">
						<input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="mytheme_show_login">
						<input type="hidden" class="menu-item-description" name="menu-item[-1][menu-item-description]" value="">
					</li>
				</ul>
			</div>
			<p class="button-controls">
				<span class="list-controls">
					<a href="<?php echo esc_url( admin_url( 'nav-menus.php?page-tab=all&amp;selectall=1#login' ) ); ?>" class="select-all"><?php esc_html_e( 'Select All', 'wp-simple-ajax-login-and-register' ); ?></a>
				</span>
				<span class="add-to-menu">
					<input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_html_e( 'Add to Menu', 'wp-simple-ajax-login-and-register' ); ?>" name="add-login-menu-item" id="submit-login">
					<span class="spinner"></span>
				</span>
			</p>
		</div>
	<?php
	}

}
