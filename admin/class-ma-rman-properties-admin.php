<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://jawadarshad.io
 * @since      1.0.0
 *
 * @package    Ma_Rman_Properties
 * @subpackage Ma_Rman_Properties/admin
 */

use function PHPSTORM_META\type;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ma_Rman_Properties
 * @subpackage Ma_Rman_Properties/admin
 * @author     Jawad Arshad <jaaviarshad@gmail.com>
 */
class Ma_Rman_Properties_Admin {
    
    //...

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;   
    }

    // Enqueue styles for the admin area
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ma-rman-properties-admin.css', array(), $this->version, 'all');

        // Enqueue color picker styles
        wp_enqueue_style('wp-color-picker');
    }

    // Enqueue scripts for the admin area
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ma-rman-properties-admin.js', array('jquery'), $this->version, false);

        $file_path = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ma-rman-call-ajax-function.php';

        // Pass the URL to JavaScript using wp_localize_script()
        wp_localize_script($this->plugin_name, 'LocalizedData', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));

        // Enqueue color picker scripts
        wp_enqueue_script('wp-color-picker');

    }

	// Register Custom Post Type for Properties
	public function ma_rman_register_cpt_properties() {
		$labels = [
			"name" => esc_html__("Properties", "custom-post-type-ui"),
			"singular_name" => esc_html__("Property", "custom-post-type-ui"),
		];

		$args = [
			"label" => esc_html__("Properties", "custom-post-type-ui"),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => true,
			"rest_base" => "",
			"rest_controller_class" => "WP_REST_Posts_Controller",
			"rest_namespace" => "wp/v2",
			"has_archive" => false,
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"delete_with_user" => false,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"can_export" => false,
			"rewrite" => ["slug" => "properties", "with_front" => false],
			"query_var" => true,
			"menu_icon" => "dashicons-admin-home",
			"supports" => ["title", "thumbnail", "editor", "custom-fields", "excerpt"],
			"show_in_graphql" => false,
		];

		register_post_type("properties", $args);
	}

	// Register Setting Page
	public function mj_rman_add_settings_page() {
		add_options_page('Rentman API setting', 'Rentman Setting', 'manage_options', 'mj-rman-setting', array($this, "mj_rman_render_plugin_settings_page"));
	}

	// Setting page callback
	public function mj_rman_render_plugin_settings_page() {
        $options = get_option('mj_rman_plugin_options');
		?>
		<h2>Rentamn API setting</h2>
		<form action="options.php" method="post">
			<?php
				settings_fields('mj_rman_plugin_options');
				do_settings_sections('mj_rman_plugin');
			?>
			
			<input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save'); ?>" />
            <?php 
				if(!empty($options['api_key'])):
			?>
                <div id="fetchbutton">
				<a id="fetchProperties" class="button button-primary" href="javascript:void(0)">Sync Properties</a>
                </div>
				<div id="progressBarContainer" style="display: none;">
					<div id="progressBar"></div>
				</div>
				<div id="responseMessage" style="display: none;"></div>
			<?php endif; ?>
		</form>
		<?php

        
	}

	// Register settings for plugin
	public function mj_rman_register_settings() {
		register_setting('mj_rman_plugin_options', 'mj_rman_plugin_options', array($this, "mj_rman_plugin_options_validate"));
		add_settings_section(' Ma_Rman_Properties', 'API Settings', array($this, 'mj_rman_plugin_section_text'), 'mj_rman_plugin');
		add_settings_field('mj_rman_plugin_setting_api_key', 'Rentman API setting', array($this, 'mj_rman_plugin_setting_api_key'), 'mj_rman_plugin', ' Ma_Rman_Properties');
        add_settings_field('mj_rman_plugin_setting_goole_mapapi_key', 'Google map API key', array($this, 'mj_rman_plugin_setting_google_api_key'), 'mj_rman_plugin', ' Ma_Rman_Properties');
        add_settings_field('mj_rman_plugin_setting_enquery_form', 'Enquiry Form Shortcode', array($this, 'mj_rman_plugin_setting_enquery_form'), 'mj_rman_plugin', ' Ma_Rman_Properties');

         // Add new fields for colors using the Customizer
        add_settings_field('mj_rman_plugin_setting_rman_title_color', 'Rentman Title Color', array($this, 'mj_rman_plugin_setting_color'), 'mj_rman_plugin', ' Ma_Rman_Properties', ['field' => 'rman_title_color', 'default' => '#ffad00']);
        add_settings_field('mj_rman_plugin_setting_rman_button_bg', 'Rentman Button Background Color', array($this, 'mj_rman_plugin_setting_color'), 'mj_rman_plugin', ' Ma_Rman_Properties', ['field' => 'rman_button_bg', 'default' => '#ffad00']);
        add_settings_field('mj_rman_plugin_setting_rman_button_hover_bg', 'Rentman Button Hover Background Color', array($this, 'mj_rman_plugin_setting_color'), 'mj_rman_plugin', ' Ma_Rman_Properties', ['field' => 'rman_button_hover_bg', 'default' => '#000000']);
        add_settings_field('mj_rman_plugin_setting_rman_button_text_color', 'Rentman Button Text Color', array($this, 'mj_rman_plugin_setting_color'), 'mj_rman_plugin', ' Ma_Rman_Properties', ['field' => 'rman_button_text_color', 'default' => '#ffffff']);
        add_settings_field('mj_rman_plugin_setting_rman_button_hover_text_color', 'Rentman Button Hover Text Color', array($this, 'mj_rman_plugin_setting_color'), 'mj_rman_plugin', ' Ma_Rman_Properties', ['field' => 'rman_button_hover_text_color', 'default' => '#ffffff']);
        add_settings_field('mj_rman_plugin_setting_rman_description_color', 'Rentman Description Color', array($this, 'mj_rman_plugin_setting_color'), 'mj_rman_plugin', ' Ma_Rman_Properties', ['field' => 'rman_description_color', 'default' => '#000000']);
        add_settings_field('mj_rman_plugin_setting_rman_price_color', 'Rentman Price Color', array($this, 'mj_rman_plugin_setting_color'), 'mj_rman_plugin', ' Ma_Rman_Properties', ['field' => 'rman_price_color', 'default' => '#000000']);

	}

    /**
     * Display the color field in the settings page.
     *
     * @param array $args The arguments for the color field.
     */
    public function mj_rman_plugin_setting_color($args) {
        $options = get_option('mj_rman_plugin_options');

        $field = $args['field'];
        $default_color = $args['default']; // Set a default color if needed

        echo "<input type='text' id='mj_rman_plugin_setting_$field' name='mj_rman_plugin_options[$field]' value='" . esc_attr(isset($options[$field]) ? $options[$field] : $default_color) . "' class='color-field' />";
    }

	// Validate and sanitize plugin options
	public function mj_rman_plugin_options_validate($input) {
		$newinput['api_key'] = htmlspecialchars(trim($input['api_key']));
        $newinput['google_map_api'] = htmlspecialchars(trim($input['google_map_api']));
        $newinput['enquiry_form'] = sanitize_text_field(trim($input['enquiry_form']));
        $newinput['rman_title_color'] = sanitize_hex_color($input['rman_title_color']);
        $newinput['rman_button_bg'] = sanitize_hex_color($input['rman_button_bg']);
        $newinput['rman_button_hover_bg'] = sanitize_hex_color($input['rman_button_hover_bg']);
        $newinput['rman_button_text_color'] = sanitize_hex_color($input['rman_button_text_color']);
        $newinput['rman_button_hover_text_color'] = sanitize_hex_color($input['rman_button_hover_text_color']);
        $newinput['rman_description_color'] = sanitize_hex_color($input['rman_description_color']);
        $newinput['rman_price_color'] = sanitize_hex_color($input['rman_price_color']);
		return $newinput;
	}

	// Display section description for API settings
	public function mj_rman_plugin_section_text() {
		echo '<p>Here you can set all the options for using the API</p>';
	}

	// Display API Key input field on settings page
	public function mj_rman_plugin_setting_api_key() {
		$options = get_option('mj_rman_plugin_options');
		echo "<input id='mj_rman_plugin_setting_api_key' name='mj_rman_plugin_options[api_key]' type='text' value='"  . (isset($options['api_key']) ? esc_attr($options['api_key']) : "" ) . "' /><br>";
    }

    // Display API Key input field on settings page
	public function mj_rman_plugin_setting_google_api_key() {
		$options = get_option('mj_rman_plugin_options');
        echo "<input id='mj_rman_plugin_setting_google_api_key' name='mj_rman_plugin_options[google_map_api]' type='text' value='" . (isset($options['google_map_api']) ? esc_attr($options['google_map_api']) : "") . "'/>";
	}
     //Enquiry Form field
	public function mj_rman_plugin_setting_enquery_form() {
		$options = get_option('mj_rman_plugin_options');
        echo "<input id='mj_rman_plugin_setting_enquery_form' name='mj_rman_plugin_options[enquiry_form]' type='text' value='" . (isset($options['enquiry_form']) ? esc_attr($options['enquiry_form']) : "") . "'/>";
	}


	// Send cURL request to Rentman API
	public function send_curl_request($api_key) {
        if(empty($api_key))
        {
            return;
        }
		$token = $api_key;
		$url = 'https://www.rentman.online/propertyadvertising.php?token=' . $token;
		$curl = curl_init($url);

		$headers = array(
			'Accept: application/json',
		);

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if (curl_errno($curl)) {
			$error_message = curl_error($curl);
			echo "cURL Error: " . $error_message;
		}

		curl_close($curl);

        $is_json = json_decode($response , true);
		
        if (is_array($is_json)) {

			$data = json_decode($response, true);
            // var_dump($data);
            // die();
            return $data;	



		} else {

            $response = array(
                'message' => "Incorrect API key found, please insert valid key to sync",
            );
            echo json_encode($response);
            die();
		}  

        
	}


    public function call_rman_api_function () {

        $options = get_option('mj_rman_plugin_options');
        if (!empty($options['api_key'])) {

            $token = $options['api_key'];

			$data = $this->send_curl_request($options['api_key']);

            if(is_array($data))
            {
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ma-rman-submit-post.php';
                $submit_post = new Ma_rman_submit_post($data, $token);
                $submit_post->process_properties();
				
            }

		}
    }


function ma_rman_ajax_callback() {

    if (isset($_POST['action']) && $_POST['action'] === 'rman_ajax_action') {

        // Instantiate your class and call the method  
        $this->call_rman_api_function();

	}
}
}