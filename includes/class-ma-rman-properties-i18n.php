<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://jawadarshad.io
 * @since      1.0.0
 *
 * @package    Ma_Rman_Properties
 * @subpackage Ma_Rman_Properties/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ma_Rman_Properties
 * @subpackage Ma_Rman_Properties/includes
 * @author     Jawad Arshad <jaaviarshad@gmail.com>
 */
class Ma_Rman_Properties_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ma-rman-properties',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
