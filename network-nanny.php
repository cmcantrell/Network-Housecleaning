<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/cmcantrell/Network-Nanny
 * @since             1.0.0
 * @package           Network_Nanny
 *
 * @wordpress-plugin
 * Plugin Name:       Wordpress Network Nanny
 * Plugin URI:        https://github.com/cmcantrell/Network-Nanny
 * Description:       Cleanup your Wordpress installations network tab.
 * Version:           1.0.0
 * Author:            Clinton Cantrell
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       network-nanny
 * Domain Path:       /languages
 */
ini_set('display_errors',1);
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_network_nanny() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-network-nanny-activator.php';
	Network_Nanny_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_network_nanny' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_network_nanny() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-network-nanny-deactivator.php';
	Network_Nanny_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_network_nanny' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-network-nanny.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_network_nanny() {
	$plugin = new Network_Nanny();
	$plugin->run();
}
run_network_nanny();
