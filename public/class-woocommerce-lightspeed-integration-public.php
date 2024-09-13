<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://imagenwebpro.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Lightspeed_Integration
 * @subpackage Woocommerce_Lightspeed_Integration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woocommerce_Lightspeed_Integration
 * @subpackage Woocommerce_Lightspeed_Integration/public
 * @author     Imagen Web Pro <hello@imagenwebpro.com>
 */
class Woocommerce_Lightspeed_Integration_Public {

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_shortcode('stock_levels', [ $this, 'stock_levels_shortcode' ] );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-lightspeed-integration-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-lightspeed-integration-public.js', array( 'jquery' ), $this->version, false );

		$data = $this->get_localize_data();
		wp_localize_script( $this->plugin_name, 'WLSIVars', $data );
	}

	/**
	 * Get the localized data for JavaScript.
	 *
	 * @return array Localized data.
	 */
	public function get_localize_data() {
		$data = array(
			'ajaxurl'      => esc_url( admin_url( 'admin-ajax.php' ) ),
			'siteURL'      => site_url(),
			'_ajax_nonce'  => wp_create_nonce( 'woocommerce_lightspeed_integration_nonce' ),
			'err_msg1'     => __( 'Something went wrong!', 'woocommerce-lightspeed-integration' ),
		);

		return $data;
	}

	// Shortcode to display stock levels by location (only South Melbourne, Melbourne Warehouse, and North Parramatta)
	public function stock_levels_shortcode() {
	    global $product;

	    // Get the Lightspeed Product ID from WooCommerce product meta
	    $lightspeed_product_id = get_post_meta( $product->get_id(), '_lightspeed_product_id', true );
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
	    // Commenting out the cache logic for now
	    // Generate a unique transient name using the product ID
	    // $transient_name = 'lightspeed_inventory_' . $lightspeed_product_id;

	    // Check if cached inventory data exists
	    // $cached_inventory_data = get_transient( $transient_name );

	    // if ( $cached_inventory_data === false ) {
	        // Get the API key and set up Lightspeed API
	        $api_key = get_option( 'wc_lightspeed_api_key' );
	        $lightspeed_api = new Woocommerce_Lightspeed_Integration_API( $api_key );

	        // Make API call to fetch inventory for the Lightspeed product
	        $inventory_data = $lightspeed_api->get( "products/{$lightspeed_product_id}/inventory" );

	        // Check if the API call was successful and data exists
	        if ( ! is_wp_error( $inventory_data ) && ! empty( $inventory_data['data'] ) ) {
	            // $cached_inventory_data = $inventory_data['data']; // Use data directly without caching
	        } else {
	            return '<p>' . __( 'Inventory data not available.', 'wc-lightspeed' ) . '</p>';
	        }
	    // }

	    // Output the inventory data
	    ob_start(); // Start output buffering
	    ?>
	    <div class="stock-levels-div">
	        <h4><?php esc_html_e( 'Stock levels by location', 'wc-lightspeed' ); ?></h4>
	        <div class="stock-levels">
	            <?php
	            // Loop through each inventory outlet and display stock levels for specific locations
	            foreach ( $inventory_data['data'] as $inventory ) {
	                // Filter only for South Melbourne, Melbourne Warehouse, and North Parramatta
	                if ( in_array( $inventory['outlet_id'], [ '067f7b40-1ee9-11ee-f1f6-b6a5f3749595', '067f7b40-1e4d-11ee-e36a-3bed7eb7a7d7', '067f7b40-1ee9-11ee-f1f6-921cc1f7c19a' ] ) ) {
	                    // Map outlet IDs to location names
	                    $location_name = $this->get_location_name_by_outlet_id( $inventory['outlet_id'] );

	                    // Get dynamic messages and colors from options
	                    $in_stock_message = get_option( 'wc_lightspeed_in_stock_message', 'Looking good' );
	                    $in_stock_color = get_option( 'wc_lightspeed_in_stock_color', '#00FF00' );

	                    $low_stock_message = get_option( 'wc_lightspeed_low_stock_message', 'Limited Stock' );
	                    $low_stock_color = get_option( 'wc_lightspeed_low_stock_color', '#FFA500' );

	                    $zero_stock_message = get_option( 'wc_lightspeed_zero_stock_message', 'Available in 3-5 Days' );
	                    $zero_stock_color = get_option( 'wc_lightspeed_zero_stock_color', '#FF0000' );

	                    // Determine stock status
	                    $inventory_level = (int) $inventory['inventory_level'];

	                    if ( $inventory_level > 0 ) {
	                        $status = '<span class="status good" style="color:' . esc_attr( $in_stock_color ) . ';">' . esc_html( $in_stock_message ) . '</span>';
	                    } elseif ( $inventory_level < 0 ) {
	                        $status = '<span class="status low" style="color:' . esc_attr( $low_stock_color ) . ';">' . esc_html( $low_stock_message ) . '</span>';
	                    } else {
	                        $status = '<span class="status eta" style="color:' . esc_attr( $zero_stock_color ) . ';">' . esc_html( $zero_stock_message ) . '</span>';
	                    }

	                    // Display the location and status
	                    echo '<div class="location">';
	                    echo '<span class="location-name">' . esc_html( $location_name ) . '</span>';
	                    echo $status;
	                    echo '</div>';
	                }
	            }

	            // If a location is missing, still show it with zero stock message
	            $locations = [
	                'South Melbourne' => '067f7b40-1ee9-11ee-f1f6-b6a5f3749595',
	                'Melbourne Warehouse' => '067f7b40-1e4d-11ee-e36a-3bed7eb7a7d7',
	                'North Parramatta' => '067f7b40-1ee9-11ee-f1f6-921cc1f7c19a',
	            ];

	            foreach ( $locations as $location_name => $outlet_id ) {
	                if ( ! in_array( $outlet_id, array_column( $inventory_data['data'], 'outlet_id' ) ) ) {
	                    echo '<div class="location">';
	                    echo '<span class="location-name">' . esc_html( $location_name ) . '</span>';
	                    echo '<span class="status eta" style="color:' . esc_attr( $zero_stock_color ) . ';">' . esc_html( $zero_stock_message ) . '</span>';
	                    echo '</div>';
	                }
	            }
	            ?>
	        </div>
	    </div>
	    <?php
	    return ob_get_clean(); // Return the buffer content
	}

	// Helper function to map outlet IDs to human-readable location names
	public function get_location_name_by_outlet_id( $outlet_id ) {
	    $locations = [
	        '067f7b40-1ee9-11ee-f1f6-b6a5f3749595' => 'South Melbourne',
	        '067f7b40-1e4d-11ee-e36a-3bed7eb7a7d7' => 'Melbourne Warehouse',
	        '067f7b40-1ee9-11ee-f1f6-921cc1f7c19a' => 'North Parramatta',
	        // You can add more outlet mappings here if needed
	    ];

	    return isset( $locations[$outlet_id] ) ? $locations[$outlet_id] : 'Unknown Location';
	}


}
