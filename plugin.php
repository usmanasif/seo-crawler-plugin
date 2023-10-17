<?php
/**
 * Plugin Template
 *
 * @package     SEO Crawler
 * @author      Usman Asif
 * @copyright   UA rights reserved
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: SEO Crawler
 * Version:     1.0
 * Description: A plugin to fetch hyperlinks from the homepage to list them and improve SEO.
 * Author:      Usman Asif
 */

namespace ROCKET_WP_CRAWLER;

if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'ROCKET_CRWL_PLUGIN_FILENAME', __FILE__ ); // Filename of the plugin, including the file.

if ( ! defined( 'ABSPATH' ) ) { // If WordPress is not loaded.
	exit( 'WordPress not loaded. Can not load the plugin' );
}

// Load the dependencies installed through composer.
require_once __DIR__ . '/src/plugin.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/support/exceptions.php';
require_once plugin_dir_path( __FILE__ ) . 'src/admin/seo-link-checker-admin.php';

// Plugin initialization.
/**
 * Creates the plugin object on plugins_loaded hook
 *
 * @return void
 */
function wpc_crawler_plugin_init() {
	SEO_Link_Checker_Admin::get_instance();
	$wpc_crawler_plugin = new Rocket_Wpc_Plugin_Class();
	$wpc_crawler_plugin->run();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\wpc_crawler_plugin_init' );

register_activation_hook( __FILE__, __NAMESPACE__ . '\Rocket_Wpc_Plugin_Class::wpc_activate' );
register_uninstall_hook( __FILE__, __NAMESPACE__ . '\Rocket_Wpc_Plugin_Class::wpc_uninstall' );
