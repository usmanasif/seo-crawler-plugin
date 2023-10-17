<?php
/**
 * Plugin main class
 *
 * @package     SEO Crawler
 * @since       October, 2023
 * @author      Usman Asif
 * @license     GPL-2.0-or-later
 */

namespace ROCKET_WP_CRAWLER;

/**
 * Main plugin class. It manages initialization, install, and activations.
 */
class Rocket_Wpc_Plugin_Class {
	/**
	 * Manages plugin initialization
	 *
	 * @return void
	 */
	public function __construct() {

		// Register plugin lifecycle hooks.
		register_deactivation_hook( ROCKET_CRWL_PLUGIN_FILENAME, array( $this, 'wpc_deactivate' ) );
	}

	/**
	 * Handles plugin activation:
	 *
	 * @return void
	 */
	public static function wpc_activate() {
		// Security checks.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
		check_admin_referer( "activate-plugin_{$plugin}" );
	}

	/**
	 * Handles plugin deactivation
	 *
	 * @return void
	 */
	public function wpc_deactivate() {
		// Security checks.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );
		$this->unschedule_crawl();
	}

	/**
	 * Handles plugin uninstall
	 *
	 * @return void
	 */
	public static function wpc_uninstall() {

		// Security checks.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
	}

	/**
	 * Initialize the plugin by setting up hooks.
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Add admin menu for the plugin.
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		add_menu_page( 'SEO Crawler', 'SEO Crawler', 'manage_options', 'seo-crawler', array( $this, 'display_admin_page' ) );
	}

	/**
	 * Schedule the crawl event.
	 *
	 * @return void
	 */
	public static function schedule_crawl() {
		if ( ! wp_next_scheduled( 'seo_link_checker_cron' ) ) {
			wp_schedule_event( time(), 'hourly', 'seo_link_checker_cron' );
		}
	}

	/**
	 * Unschedule the crawl event.
	 *
	 * @return void
	 */
	public function unschedule_crawl() {
		$timestamp = wp_next_scheduled( 'seo_link_checker_cron' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'seo_link_checker_cron' );
		}
	}

	/**
	 * Display the admin page.
	 *
	 * @return void
	 */
	public function display_admin_page() {
		require_once plugin_dir_path( __DIR__ ) . 'src/admin/seo-link-checker-admin.php';
		$admin_page = SEO_Link_Checker_Admin::get_instance();
		$admin_page->display();
	}

	/**
	 * Enqueue styles for the admin page.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'results-table-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), '1.0.0' );
	}
}
