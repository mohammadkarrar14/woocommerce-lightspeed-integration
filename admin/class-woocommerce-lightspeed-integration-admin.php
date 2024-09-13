<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://imagenwebpro.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Lightspeed_Integration
 * @subpackage Woocommerce_Lightspeed_Integration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Lightspeed_Integration
 * @subpackage Woocommerce_Lightspeed_Integration/admin
 * @author     Imagen Web Pro <hello@imagenwebpro.com>
 */
class Woocommerce_Lightspeed_Integration_Admin {

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
     * Hook into WordPress actions to add admin menu and settings.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // Add action hooks for admin menu and settings registration.
        add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'wc_lightspeed_register_settings' ) );

        add_filter( 'manage_edit-product_columns', [ $this, 'add_lightspeed_product_column' ] );
		add_action( 'manage_product_posts_custom_column', [ $this, 'display_lightspeed_product_column' ], 10, 2 );
		add_action( 'woocommerce_single_product_summary', [ $this, 'display_lightspeed_inventory_on_product_page' ], 20 );

    }

    /**
     * Enqueue the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        // Register and enqueue the admin styles for this plugin.
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-lightspeed-integration-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Enqueue the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        // Register and enqueue the admin scripts for this plugin.
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-lightspeed-integration-admin.js', array( 'jquery' ), $this->version, false );

    }

    /**
     * Register the admin menu and submenu pages in the WordPress dashboard.
     * Creates a main menu "Lightspeed" and adds two submenu pages: "Settings" and "Outlets."
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        // Add the main menu page for the plugin.
        add_menu_page(
            __( 'WooCommerce Light Speed Integration', 'wc-lightspeed' ),
            __( 'Lightspeed', 'wc-lightspeed' ),
            'manage_options',
            'wc-lightspeed',
            array( $this, 'display_plugin_settings_page' ),
            'dashicons-superhero',
            56 // Position the menu where you want it to appear.
        );

        // Add the "Settings" submenu page under the Lightspeed menu.
        add_submenu_page(
            'wc-lightspeed',
            __( 'Settings', 'wc-lightspeed' ),
            __( 'Settings', 'wc-lightspeed' ),
            'manage_options',
            'wc-lightspeed-settings',
            array( $this, 'display_plugin_settings_page' )
        );

        // Add the "Outlets" submenu page under the Lightspeed menu.
        add_submenu_page(
            'wc-lightspeed',
            __( 'Outlets', 'wc-lightspeed' ),
            __( 'Outlets', 'wc-lightspeed' ),
            'manage_options',
            'wc-lightspeed-outlets',
            array( $this, 'display_plugin_outlets_page' )
        );
    }

    /**
     * Display the settings page for the plugin.
     * The HTML content is included from an external template file.
     *
     * @since    1.0.0
     */
    public function display_plugin_settings_page() {
        // Define the path to the settings page template.
        $template_path = plugin_dir_path( __FILE__ ) . 'partials/wc-lightspeed-settings-page.php';
        // Check if the template file exists and include it if available.
        if ( file_exists( $template_path ) ) {
            include $template_path;
        }
    }

    /**
     * Display the outlets page for the plugin.
     * The HTML content is included from an external template file.
     *
     * @since    1.0.0
     */
	public function display_plugin_outlets_page() {
	    // Transient name for caching outlets data
	    $transient_name = 'wc_lightspeed_outlets_data';

	    // Check if cached outlets data exists
	    $cached_outlets = get_transient( $transient_name );

	    if ( false === $cached_outlets ) {
	        // No cached data, call the API
	        $api_key = get_option( 'wc_lightspeed_api_key' ); // Fetch the API key from the settings
	        $lightspeed_api = new Woocommerce_Lightspeed_Integration_API( $api_key );

	        // Fetch outlets data from the API
	        $outlets = $lightspeed_api->get_outlets();

	        if ( is_wp_error( $outlets ) ) {
	            // Handle error (e.g., invalid API key or API request failure)
	            echo '<p>' . esc_html__( 'Failed to retrieve outlets from Lightspeed API.', 'wc-lightspeed' ) . '</p>';
	            error_log( $outlets->get_error_message() );
	            return;
	        }

	        // Cache the outlets data for 1 day (24 hours)
	        set_transient( $transient_name, $outlets, DAY_IN_SECONDS );
	    } else {
	        // Use the cached data
	        $outlets = $cached_outlets;
	    }

	    // Unserialize the data if needed
	    if ( ! is_array( $outlets ) ) {
	        $outlets = maybe_unserialize( $outlets );
	    }

	    // Check if unserialization was successful
	    if ( empty( $outlets['data'] ) || ! is_array( $outlets['data'] ) ) {
	        echo '<p>' . esc_html__( 'No outlets data available.', 'wc-lightspeed' ) . '</p>';
	        return;
	    }

	    // Define the path to the outlets page template.
	    $template_path = plugin_dir_path( __FILE__ ) . 'partials/wc-lightspeed-outlets-page.php';

	    if ( file_exists( $template_path ) ) {
	        // Pass the unserialized data to the template
	        $outlets = $outlets['data'];
	        include $template_path;
	    }
	}


	public function display_plugin_inventory_page() {
	    // Transient name for caching products and inventory data
	    $transient_name = 'wc_lightspeed_inventory_data';

	    // Check if cached products and inventory data exist
	    $cached_inventory = get_transient( $transient_name );
	    $current_page = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;
	    $page_size = 10;

	    // Fetch products only when not cached or when caching is not applicable
	    if ( false === $cached_inventory ) {
	        // No cached data, call the API
	        $api_key = get_option( 'wc_lightspeed_api_key' ); // Fetch the API key from the settings
	        $lightspeed_api = new Woocommerce_Lightspeed_Integration_API( $api_key );

	        // Use `page_size` to get products in smaller chunks
	        $products = $lightspeed_api->get( 'products', ['page_size' => $page_size] );

	        if ( is_wp_error( $products ) ) {
	            // Handle error (e.g., invalid API key or API request failure)
	            echo '<p>' . esc_html__( 'Failed to retrieve products from Lightspeed API.', 'wc-lightspeed' ) . '</p>';
	            error_log( $products->get_error_message() );
	            return;
	        }

	        // Cache the products data for 1 day (24 hours)
	        set_transient( $transient_name, $products['data'], DAY_IN_SECONDS );
	    } else {
	        // Use the cached data
	        $products = $cached_inventory;
	    }

	    // Define the path to the inventory page template.
	    $template_path = plugin_dir_path( __FILE__ ) . 'partials/wc-lightspeed-inventory-page.php';

	    if ( file_exists( $template_path ) ) {
	        // Pass the products data to the template
	        include $template_path;
	    }
	}



    /**
	 * Register settings for the plugin.
	 * This includes the Lightspeed API key and other fields if needed.
	 * 
	 * @since    1.0.0
	 */
	public function wc_lightspeed_register_settings() {
	    // Define the settings fields and their sanitization callbacks.
	    $settings = [
	        'wc_lightspeed_api_key' => [
	            'type' => 'string',
	            'sanitize_callback' => 'sanitize_text_field'
	        ],
	        'wc_lightspeed_in_stock_message' => [
	            'type' => 'string',
	            'sanitize_callback' => 'sanitize_text_field'
	        ],
	        'wc_lightspeed_in_stock_color' => [
	            'type' => 'string',
	            'sanitize_callback' => 'sanitize_hex_color'
	        ],
	        'wc_lightspeed_low_stock_message' => [
	            'type' => 'string',
	            'sanitize_callback' => 'sanitize_text_field'
	        ],
	        'wc_lightspeed_low_stock_color' => [
	            'type' => 'string',
	            'sanitize_callback' => 'sanitize_hex_color'
	        ],
	        'wc_lightspeed_zero_stock_message' => [
	            'type' => 'string',
	            'sanitize_callback' => 'sanitize_text_field'
	        ],
	        'wc_lightspeed_zero_stock_color' => [
	            'type' => 'string',
	            'sanitize_callback' => 'sanitize_hex_color'
	        ],
	    ];

	    // Register each setting field.
	    foreach ( $settings as $setting => $args ) {
	        register_setting( 'wc_lightspeed_settings_group', $setting, $args );
	    }
	}


    /**
	 * Save settings for the plugin.
	 * This method processes the form submission and saves the API key and other settings.
	 *
	 * @since    1.0.0
	 */
	public function wc_lightspeed_save_settings() {
	    // Define the fields to be saved.
	    $fields = [ 
	        'wc_lightspeed_api_key', 
	        'wc_lightspeed_in_stock_message', 
	        'wc_lightspeed_in_stock_color',
	        'wc_lightspeed_low_stock_message',
	        'wc_lightspeed_low_stock_color',
	        'wc_lightspeed_zero_stock_message',
	        'wc_lightspeed_zero_stock_color'
	    ];

	    // Loop through each field, sanitize the input, and save it to the database.
	    foreach ( $fields as $field ) {
	        if ( isset( $_POST[ $field ] ) ) {
	            // Sanitize input and update the option in the database.
	            if ( strpos( $field, '_color' ) !== false ) {
	                // Sanitize colors using sanitize_hex_color
	                update_option( $field, sanitize_hex_color( $_POST[ $field ] ) );
	            } else {
	                // Sanitize text fields
	                update_option( $field, sanitize_text_field( $_POST[ $field ] ) );
	            }
	        }
	    }
	}


    // Add a new column to the product listing table for Lightspeed Product ID
	public function add_lightspeed_product_column( $columns ) {
	    $columns['lightspeed_product_id'] = __( 'Lightspeed Product ID', 'wc-lightspeed' );
	    return $columns;
	}

	// Populate the Lightspeed Product ID in the new column
	function display_lightspeed_product_column( $column, $post_id ) {
	    if ( 'lightspeed_product_id' === $column ) {
	        // Check if the Lightspeed Product ID is already saved in product meta
	        $lightspeed_product_id = get_post_meta( $post_id, '_lightspeed_product_id', true );

	        // If Lightspeed product ID already exists, display it and do not hit the API again
	        if ( ! empty( $lightspeed_product_id ) ) {
	            echo esc_html( $lightspeed_product_id );
	            return;
	        }

	        // Load the WooCommerce product object
	        $product = wc_get_product( $post_id );

	        if ( $product ) {
	            // Get the SKU of the WooCommerce product
	            $product_sku = $product->get_sku();

	            if ( ! empty( $product_sku ) ) {
	                // Get the API key and set up Lightspeed API
	                $api_key = get_option( 'wc_lightspeed_api_key' );
	                $lightspeed_api = new Woocommerce_Lightspeed_Integration_API( $api_key );

	                // Fetch the Lightspeed product data using the SKU
	                $lightspeed_product_response = $lightspeed_api->get( 'products', [ 'sku' => $product_sku ] );

	                // Check if the API request was successful and product data exists
	                if ( ! is_wp_error( $lightspeed_product_response ) && ! empty( $lightspeed_product_response['data'] ) ) {
	                    // Get the Lightspeed Product ID from the response
	                    $lightspeed_product_id = $lightspeed_product_response['data']['id'];

	                    // Display the Lightspeed Product ID
	                    echo esc_html( $lightspeed_product_id );

	                    // Save the Lightspeed Product ID to WooCommerce product meta for future use
	                    update_post_meta( $post_id, '_lightspeed_product_id', $lightspeed_product_id );
	                } else {
	                    // Handle cases where the Lightspeed product is not found or there was an API error
	                    echo __( 'Not Found', 'wc-lightspeed' );
	                }
	            } else {
	                echo __( 'No SKU', 'wc-lightspeed' );
	            }
	        }
	    }
	}

	// Hook into the single product page to display Lightspeed inventory
	public function display_lightspeed_inventory_on_product_page() {
	   echo do_shortcode('[stock_levels]');
	}



}	
