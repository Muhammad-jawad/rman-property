<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://jawadarshad.io
 * @since      1.0.0
 *
 * @package    Ma_Rman_Properties
 * @subpackage Ma_Rman_Properties/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ma_Rman_Properties
 * @subpackage Ma_Rman_Properties/public
 * @author     Jawad Arshad <jaaviarshad@gmail.com>
 */
class Ma_Rman_Properties_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // Hook to enqueue styles and scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        if (is_singular('properties')) {

            // Load Swiper CSS
           if (!$this->is_css_containing_name_loaded('swiper')) {
               // A CSS containing "swiper" in its handle is loaded, do something
               wp_enqueue_style($this->plugin_name . '-swiper', plugin_dir_url(__FILE__) . 'css/swiper.min.css' , array(), $this->version, 'all');
           }
            // Load Magnific CSS
           if (!$this->is_script_containing_name_loaded('magnific')) {
               // A CSS containing "magnific" in its handle is loaded, do something
               wp_enqueue_style($this->plugin_name . '-magnificpopup', plugin_dir_url(__FILE__) . 'css/magnific-popup.css' , array(), $this->version, 'all');
           }
          
       }

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ma-rman-properties-public.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-font-awesome', 'https://kit.fontawesome.com/a93df7bd97.css', array(), '5.15.3', 'all' );

    }


   
    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ma-rman-properties-public.js', array('jquery'), $this->version, false);

        wp_localize_script($this->plugin_name , "url", array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce('like-nonce')
        ));

        if (is_singular('properties')) {

             // Load Swiper JS
            if (!$this->is_script_containing_name_loaded('swiper')) {
                // A script containing "swiper" in its handle is loaded, do something
                wp_enqueue_script($this->plugin_name . "-swiper", plugin_dir_url(__FILE__) . '/js/swiper.min.js', array(), $this->version, false);
            }
             // Load Magnific JS
            if (!$this->is_script_containing_name_loaded('magnific')) {
                // A script containing "magnific" in its handle is loaded, do something
                wp_enqueue_script($this->plugin_name . '-magnificpopup', plugin_dir_url(__FILE__) . 'js/jquery.magnific-popup.min.js' , array(), $this->version, false);
            }
	       
        }

        
        
        

        // add script for swiper carousel using wp_enqueue_script
    }

     /**
     * Check if jS already exists
     *
     * @since    1.0.0
     */

     function is_script_containing_name_loaded($name) {
        $scripts = $this->get_all_scripts_used_on_current_page();
        foreach ( $scripts as $handle => $script) {
            if (strpos($handle, $name) !== false) {
                return true;
            }
        }
    
        return false;
    }

    /**
     * Check if CSS already exists
     *
     * @since    1.0.0
     */

    function is_css_containing_name_loaded($name) {
        $styles = $this->get_all_styles_used_on_current_page();
    
        foreach ($styles as $handle => $style) {
            if (strpos($handle, $name) !== false) {
                return true;
            }
        }
    
        return false;
    }

    function get_all_scripts_used_on_current_page() {
        global $wp_scripts;
        $scripts_used = array();
    
        foreach ($wp_scripts->queue as $handle) {
            $script = $wp_scripts->registered[$handle];
            $scripts_used[$handle] = $script->src;
        }
    
        return $scripts_used;
    }

    function get_all_styles_used_on_current_page() {
        global $wp_styles;
        $styles_used = array();
    
        foreach ($wp_styles->queue as $handle) {
            $style = $wp_styles->registered[$handle];
            $styles_used[$handle] = $style->src;
        }
    
        return $styles_used;
    }


    /**
     * Shortcode callback to display property archive.
     *
     * @since    1.0.0
     * @param    array    $atts    Shortcode attributes.
     * @return   string             HTML content of the property archive.
     */
    public function ma_archive_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'number_of_posts' => 4,
                // 'mb' => '30'
            ),
            $atts,
            'ma-rman'
        );

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        $response = "";

        $args = array(
            'post_type' => 'properties',
            'post_status' => 'publish',
            'posts_per_page' => $atts['number_of_posts'],
            'orderby' => 'title',
            'order' => 'ASC',
            'paged' => $paged,
        );

        $loop = new WP_Query($args);

        $response .= '<div class="property__grid">';

        while ($loop->have_posts()) : $loop->the_post();

            // Add data to the array for the current post
            $post_data = array(
                'title' => get_the_title(),
                'content' => get_the_content(),
                'display_price' => get_post_meta(get_the_ID(), 'displayprice', true),
                // Add any other data you want to pass to the template
            );

            // Use buffer to capture output
            ob_start();

            // Include the template and pass $post_data
            include plugin_dir_path(dirname(__FILE__)) . "public/partials/ma-rman-properties-archive.php";

            // Get the buffered output and append to the response
            $response .= ob_get_clean();

        endwhile;

        wp_reset_postdata();

        $response .= '</div>';

        if ($loop->max_num_pages > 1) {
            $response .= "<div class='property__button load-more'>
                <a href='javascript:void(0)' class='btn btn__primary orange-button' id='rman_load-more' data-no-posts='{$atts['number_of_posts']}'>Load more</a>
                </div>";
        }

        // Return the response HTML
        return $response;
    }

    /**
     * AJAX callback to load more properties.
     *
     * @since    1.0.0
     */
    public function ma_archive_load_more()
    {
        
        $response = "";

        $ajax_args = array(
            'post_type' => 'properties',
            'post_status' => 'publish',
            'posts_per_page' => $_POST['no_of_posts'],
            'orderby' => 'title',
            'order' => 'ASC',
            'paged' => $_POST['paged'],
        );

        $ajaxposts = new WP_Query($ajax_args);

        $max_pages = $ajaxposts->max_num_pages;

        while ($ajaxposts->have_posts()) : $ajaxposts->the_post();

            // Add data to the array for the current post
            $post_data = array(
                'title' => get_the_title(),
                'content' => get_the_content(),
                'display_price' => get_post_meta(get_the_ID(), 'displayprice', true),
                // Add any other data you want to pass to the template
            );

            // Use buffer to capture output
            ob_start();

            // Include the template and pass $post_data
            include plugin_dir_path(dirname(__FILE__)) . "public/partials/ma-rman-properties-archive.php";

            // Get the buffered output and append to the response
            $response .= ob_get_clean();

        endwhile;

        wp_reset_postdata();

        $result = [
            'max' => $max_pages,
            'html' => $response,
        ];

        echo json_encode($result);

        exit;
    }

    public function setup_single_page_template($single)
    {
        global $post;

        /* Checks for single template by post type */
        if ( $post->post_type == 'properties' ) {
            if ( file_exists( plugin_dir_path(dirname(__FILE__)) . 'public/partials/ma-rman-properties-single.php' ) ) {
                return plugin_dir_path(dirname(__FILE__)) . 'public/partials/ma-rman-properties-single.php';
            }
        }

        return $single;
    }
    public function add_wishlist_in_footer() 
    { 
        ?>
        <div class="ma-wishlist-container" style="display:none">
            <div class="wishlist-lists"></div>
            <div class="wishlist-icon"><i class="fa-regular fa-heart"></i><div class="wishlist-count"></div></div>
        </div>
        <?php
    }
   
}
