<?php

/**
 * The class responsible for defining all actions that occur in the admin area.
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ma-rman-properties-admin.php';

$plugin_admin = new Ma_Rman_Properties_Admin( 'ma-rman-properties', "1.0.0" );

$response = $plugin_admin->call_rman_api_function();
