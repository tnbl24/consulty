<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
Plugin Name: Blog Filter
Description: Blog / Post Filter and Blog / Post Gird layout Plugin For WordPress.
Version: 1.5.7
Author: A WP Life
Author URI: http://awplife.com/
Text Domain: blog-filter
Domain Path: /languages
*/

// redirect user to Blog Filter plugin setting page.
register_activation_hook( __FILE__, 'awl_blog_filter_activate' );
function awl_blog_filter_activate() {
	add_option( 'alw_blog_filter_do_activation_redirect', true );
}
add_action( 'admin_init', 'awl_blog_filter_page_redirect' );
function awl_blog_filter_page_redirect() {
	if ( get_option( 'alw_blog_filter_do_activation_redirect', false ) ) {
		delete_option( 'alw_blog_filter_do_activation_redirect' );
		wp_safe_redirect( 'edit.php?page=blog-filter-settings-page' );
		exit;
	}
}

if ( ! class_exists( 'Awl_Blog_Filter' ) ) {
	class Awl_Blog_Filter {
		public function __construct() {
			$this->_constants();
			$this->_hooks();
		}

		protected function _constants() {
			// Plugin Version.
			define( 'BF_PLUGIN_VER', '1.5.7' );

			// Plugin Text Domain.
			define( 'BF_TXTDM', 'blog-filter' );

			// Plugin Name.
			define( 'BF_PLUGIN_NAME', __( 'Blog Filter Premium', 'blog-filter' ) );

			// Plugin Slug.
			define( 'BF_PLUGIN_SLUG', 'awl_blog_filter' );

			// Plugin Directory Path.
			define( 'BF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin Directory URL.
			define( 'BF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		} // end of constructor function.

		protected function _hooks() {

			// Load text domain.
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

			// add menu item, change menu for multisite.
			add_action( 'admin_menu', array( $this, 'blog_filter_menu' ), 101 );

			// Added settings link on plugins page.
			$bf_plugin_name = plugin_basename( __FILE__ );
			add_filter( "plugin_action_links_$bf_plugin_name", 'plugins_page_settings_link' );

			function plugins_page_settings_link( $links ) {
				$bf_settings_link = '<a href="edit.php?page=blog-filter-settings-page">' . __( 'Settings', 'blog-filter' ) . '</a>';
				array_unshift( $links, $bf_settings_link );
				return $links;
			}

			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts_in_header' ) );

			add_filter( 'wp_lazy_loading_enabled', '__return_false' );

		} // end of hook function.

		public function enqueue_scripts_in_header() {
			wp_enqueue_script( 'jquery' );
		}

		public function load_textdomain() {
			load_plugin_textdomain( 'blog-filter', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		public function blog_filter_menu() {
			$filter_settings_menu = add_submenu_page( 'edit.php', __( 'Blog Filters Settings', 'blog-filter' ), __( 'Blog Filters Settings', 'blog-filter' ), 'administrator', 'blog-filter-settings-page', array( $this, 'awl_blog_filter_page' ) );
		}

		public function awl_blog_filter_page() {
			require_once 'blog-filter-settings.php';
		}

	}
	// Plugin Recommend.
		add_action( 'tgmpa_register', 'blog_filter_plugin_recommend' );
	function blog_filter_plugin_recommend() {
		$plugins = array(
			array(
				'name'     => 'Image Gallery – Lightbox Gallery Responsive Gallery',
				'slug'     => 'new-image-gallery',
				'required' => false,
			),
			array(
				'name'     => 'ABC Pricing Table',
				'slug'     => 'abc-pricing-table',
				'required' => false,
			),
			array(
				'name'     => 'Team Builder Member Showcase',
				'slug'     => 'team-builder-member-showcase',
				'required' => false,
			),
		);
		tgmpa( $plugins );
	}

	$pf_post_filter_object = new Awl_Blog_Filter();
	// Shortcode page.
	require_once 'blog-filter-shortcode.php';
	require_once 'class-tgm-plugin-activation.php';

}
