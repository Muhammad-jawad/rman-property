<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://jawadarshad.io
 * @since      1.0.0
 *
 * @package    Ma_Rman_Properties
 * @subpackage Ma_Rman_Properties/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ma_Rman_Properties
 * @subpackage Ma_Rman_Properties/includes
 * @author     Jawad Arshad <jaaviarshad@gmail.com>
 */
class Ma_Rman_Properties {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ma_Rman_Properties_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MA_RMAN_PROPERTIES_VERSION' ) ) {
			$this->version = MA_RMAN_PROPERTIES_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ma-rman-properties';



		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ma_Rman_Properties_Loader. Orchestrates the hooks of the plugin.
	 * - Ma_Rman_Properties_i18n. Defines internationalization functionality.
	 * - Ma_Rman_Properties_Admin. Defines all hooks for the admin area.
	 * - Ma_Rman_Properties_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ma-rman-properties-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ma-rman-properties-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ma-rman-properties-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ma-rman-properties-public.php';

		$this->loader = new Ma_Rman_Properties_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ma_Rman_Properties_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ma_Rman_Properties_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ma_Rman_Properties_Admin( $this->get_plugin_name(), $this->get_version() );

		
        if (class_exists('ACF')) {
			// The ACF class does exist, so you can define your functions here
		
			// Enable ACF for the 'properties' post type
			$this->loader->add_filter('acf/settings/show_in_post_types', 'enable_acf_for_properties', 10, 1);
		
			// Optionally, you can remove the WordPress custom fields metabox for the 'properties' post type
			add_filter('acf/settings/remove_wp_meta_box', '__return_false');
		}


		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_admin, 'ma_rman_register_cpt_properties' );
		
		// Setting Page
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'mj_rman_add_settings_page' );
		
		$this->loader->add_action( 'admin_init', $plugin_admin, 'mj_rman_register_settings' );

		$this->loader->add_action( 'mj_rman_api_schedule', $plugin_admin, 'call_rman_api_function' );

		$this->loader->add_action( 'wp_ajax_rman_ajax_action', $plugin_admin, 'ma_rman_ajax_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_rman_ajax_action', $plugin_admin, 'ma_rman_ajax_callback' );


	}

	public function enable_acf_for_properties($post_types)
	{
		// Add 'properties' to the list of post types where ACF should be enabled
		$post_types[] = 'properties';
		return $post_types;
	}

	
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ma_Rman_Properties_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );


		$this->loader->add_shortcode( 'properties_list', $plugin_public , 'ma_archive_shortcode' );

        $this->loader->add_action( 'wp_ajax_rman_load_more', $plugin_public, 'ma_archive_load_more' );
		$this->loader->add_action( 'wp_ajax_nopriv_rman_load_more', $plugin_public, 'ma_archive_load_more' );


        /* Filter the single_template with our custom function*/
        $this->loader->add_filter( 'single_template', $plugin_public, 'setup_single_page_template' );
        // Add wishlist in footer
        $this->loader->add_filter( 'wp_footer', $plugin_public, 'add_wishlist_in_footer' );




	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ma_Rman_Properties_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
