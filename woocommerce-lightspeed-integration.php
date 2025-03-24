<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://muhammadkarrar.com
 * @since             1.0.0
 * @package           Woocommerce_Lightspeed_Integration
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce LightSpeed Integration
 * Plugin URI:        https://muhammadkarrar.com
 * Description:       WooCommerce Lightspeed Integration is a custom solution that connects your WooCommerce store with the Lightspeed POS system. It allows you to sync product inventory from multiple Lightspeed outlets and display stock levels directly on WooCommerce product pages. This integration helps you manage inventory more efficiently by automatically fetching data from Lightspeed and keeping your WooCommerce store updated with real-time stock availability across different locations.
 * Version:           1.0.0
 * Author:            Muhammad Karrar
 * Author URI:        https://muhammadkarrar.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-lightspeed-integration
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
define( 'WOOCOMMERCE_LIGHTSPEED_INTEGRATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-lightspeed-integration-activator.php
 */
function activate_woocommerce_lightspeed_integration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-lightspeed-integration-activator.php';
	Woocommerce_Lightspeed_Integration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-lightspeed-integration-deactivator.php
 */
function deactivate_woocommerce_lightspeed_integration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-lightspeed-integration-deactivator.php';
	Woocommerce_Lightspeed_Integration_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_lightspeed_integration' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_lightspeed_integration' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-lightspeed-integration.php';

function woocommerce_lightspeed_integration_require_dependency() {
    
    // Check if WooCommerce is enabled
    if ( file_exists( ABSPATH . 'wp-admin/includes/plugin.php' ) ){
        include_once ( ABSPATH . 'wp-admin/includes/plugin.php' );
    }

    if ( ! class_exists( 'WooCommerce' ) ) {
        unset($_GET['activate']);  //unset this to hide default Plugin activated. notice
        deactivate_plugins( plugin_basename( __FILE__ ) , true );
        $class = 'notice is-dismissible error notice-rating';
        $message = __( 'WooCommerce Lightspeed Integration Addon requires <a href="https://www.woocommerce.com">WooCommerce</a> plugin to be activated.', 'woocommerce-lightspeed-integration' );
        printf( "<div id='message' class='%s'> <p>%s</p></div>", $class, $message );
    }
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_lightspeed_integration_addon() {

	$plugin = new woocommerce_lightspeed_integration();
	$plugin->run();
}

function woocommerce_lightspeed_integration_load(){

    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'woocommerce_lightspeed_integration_require_dependency' );
        return false;
    } else {
        run_woocommerce_lightspeed_integration_addon();
    }
}

add_action( 'plugins_loaded', 'woocommerce_lightspeed_integration_load' );

