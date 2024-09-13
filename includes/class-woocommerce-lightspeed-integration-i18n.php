<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://imagenwebpro.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Lightspeed_Integration
 * @subpackage Woocommerce_Lightspeed_Integration/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Lightspeed_Integration
 * @subpackage Woocommerce_Lightspeed_Integration/includes
 * @author     Imagen Web Pro <hello@imagenwebpro.com>
 */
class Woocommerce_Lightspeed_Integration_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woocommerce-lightspeed-integration',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
