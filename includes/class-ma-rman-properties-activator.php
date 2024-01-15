<?php

/**
 * Fired during plugin activation
 *
 * @link       https://jawadarshad.io
 * @since      1.0.0
 *
 * @package    Ma_Rman_Properties
 * @subpackage Ma_Rman_Properties/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ma_Rman_Properties
 * @subpackage Ma_Rman_Properties/includes
 * @author     Jawad Arshad <jaaviarshad@gmail.com>
 */
class Ma_Rman_Properties_Activator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * 
	 * @since    1.0.0
	 */
	public static function activate() {
		  // On activation, schedule the cron job
		if (!wp_next_scheduled('mj_rman_api_schedule')) {
				wp_schedule_event(time(), 'hourly', 'mj_rman_api_schedule');
		}
	}

}
