<?php
/**
 * @package neom Starter Sites
 * @since 1.0
 */


/**
 * Set import files
 */
if ( ! function_exists( 'neom_starter_sites_import_files' ) ) {

	function neom_starter_sites_import_files() {

		// Demos url.
		$demo_url = awp_companion_plugin_url;

		return array(
			array(
				'import_file_name'           => esc_html__( 'Neom', 'neom' ),
				// 'categories'                 => array( 'Pro Demos' ),
				'import_file_url'            => $demo_url . 'inc/neom/demo-content/neom/neom.xml',
				'import_widget_file_url'     => $demo_url . 'inc/neom/demo-content/neom/neom.wie',
				'import_customizer_file_url' => $demo_url . 'inc/neom/demo-content/neom/neom.dat',
				'preview_url'                => 'https://awplife.com/demo/neom-premium/',
				'import_preview_image_url'   => $demo_url . '/inc/neom/img/demo-screenshots/neom.png',
			),

			array(
				'import_file_name'           => esc_html__( 'Neom City', 'neom' ),
				// 'categories'                 => array( 'Pro Demos' ),
				'import_file_url'            => $demo_url . 'inc/neom/demo-content/neom-city/neom-city.xml',
				'import_widget_file_url'     => $demo_url . 'inc/neom/demo-content/neom-city/neom-city.wie',
				'import_customizer_file_url' => $demo_url . 'inc/neom/demo-content/neom-city/neom-city.dat',
				'preview_url'                => 'https://awplife.com/demo/neom-city/',
				'import_preview_image_url'   => $demo_url . '/inc/neom/img/demo-screenshots/neom-city.png',
			),

			array(
				'import_file_name'         => esc_html__( 'Neom Design', 'neom' ),
				'categories'               => array( 'Pro Demos' ),
				'preview_url'              => 'https://awplife.com/demo/neom-design/',
				'import_preview_image_url' => $demo_url . '/inc/neom/img/demo-screenshots/neom-design.png',
			),

			array(
				'import_file_name'         => esc_html__( 'The Line', 'neom' ),
				'categories'               => array( 'Pro Demos' ),
				'preview_url'              => 'https://awplife.com/demo/the-line/',
				'import_preview_image_url' => $demo_url . '/inc/neom/img/demo-screenshots/the-line.png',
			),

		);
	}
}
add_filter( 'pt-ocdi/import_files', 'neom_starter_sites_import_files' );

/**
 * Define actions that happen after import
 */
if ( ! function_exists( 'neom_starter_sites_after_import_mods' ) ) {

	function neom_starter_sites_after_import_mods() {

		// Assign the menu (Make Menu Name Same As Demo).
		$main_menu = get_term_by( 'name', 'Neom Menu', 'nav_menu' );
		set_theme_mod(
			'nav_menu_locations',
			array(
				'primary_menu' => $main_menu->term_id,
			)
		);

		// Asign the static front page and the blog page.
		/*
		$front_page = get_page_by_title( 'Home' );
		$blog_page  = get_page_by_title( 'Blog' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page -> ID );
		update_option( 'page_for_posts', $blog_page -> ID ); */

	}
}
add_action( 'pt-ocdi/after_import', 'neom_starter_sites_after_import_mods' );


// Custom CSS for OCDI plugin.
function neom_starter_sites_ocdi_css() { ?>
	<style>
		.ocdi__gl-item:nth-child(n+3) .ocdi__gl-item-buttons .button-primary, .ocdi .ocdi__theme-about, .ocdi__intro-text {
			display: none;
		}
		.ocdi__gl-item-image-container::after {
			padding-top: 75% !important;
		}

	</style>
	<?php
}
add_action( 'admin_enqueue_scripts', 'neom_starter_sites_ocdi_css' );

// Change the "One Click Demo Import" name from "Starter Sites" in Appearance menu.
function neom_starter_sites_ocdi_plugin_page_setup( $default_settings ) {

	$default_settings['parent_slug'] = 'themes.php';
	$default_settings['page_title']  = esc_html__( 'Neom One Click Demo Import', 'neom' );
	$default_settings['menu_title']  = esc_html__( 'Neom Starter Sites', 'neom' );
	$default_settings['capability']  = 'import';
	$default_settings['menu_slug']   = 'one-click-demo-import';

	return $default_settings;

}
add_filter( 'ocdi/plugin_page_setup', 'neom_starter_sites_ocdi_plugin_page_setup' );

// Register required plugins for the demos.
function neom_starter_sites_register_plugins( $plugins ) {

	// List of plugins used by all theme demos.
	$theme_plugins = array(
		array(
			'name'     => 'Cooming Soon',
			'slug'     => 'coming-soon-maintenance-mode',
			'required' => true,
		),
		array(
			'name'     => 'Portfolio Gallery',
			'slug'     => 'portfolio-filter-gallery',
			'required' => true,
		),
		array(
			'name'     => 'WooCommerce',
			'slug'     => 'woocommerce',
			'required' => true,
		),
		array(
			'name'     => 'Customizer login Page',
			'slug'     => 'customizer-login-page',
			'required' => true,
		),
	);

	return array_merge( $plugins, $theme_plugins );

}
add_filter( 'ocdi/register_plugins', 'neom_starter_sites_register_plugins' );


/**
* Remove branding
*/
// add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );
