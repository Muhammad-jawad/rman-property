<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://jawadarshad.io
 * @since             1.0.0
 * @package           Ma_Rman_Properties
 *
 * @wordpress-plugin
 * Plugin Name:       Rentman Properties API
 * Plugin URI:        https://jawadarshad.io/plugins/rentman-properties
 * Description:       This plugin has been meticulously crafted to seamlessly integrate with the API of https://www.rman.co.uk/. Its primary function is to efficiently retrieve data from the Rentman website and present it on the current website, offering a streamlined and enhanced user experience.
 * Version:           1.0.0
 * Author:            Jawad Arshad
 * Author URI:        https://jawadarshad.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ma-rman-properties
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MA_RMAN_PROPERTIES_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ma-rman-properties-activator.php
 */
function activate_ma_rman_properties() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ma-rman-properties-activator.php';
	Ma_Rman_Properties_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ma-rman-properties-deactivator.php
 */
function deactivate_ma_rman_properties() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ma-rman-properties-deactivator.php';
	Ma_Rman_Properties_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ma_rman_properties' );
register_deactivation_hook( __FILE__, 'deactivate_ma_rman_properties' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ma-rman-properties.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ma_rman_properties() {

	$plugin = new Ma_Rman_Properties();
	$plugin->run();

}
run_ma_rman_properties();
