<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://imagenwebpro.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Lightspeed_Integration
 * @subpackage Woocommerce_Lightspeed_Integration/admin/partials
 */

/**
 * Settings Page Template for WooCommerce Lightspeed Integration.
 */
?>

<div class="wc-lightspeed-settings-wrap">
    <h2><?php esc_html_e( 'WooCommerce Lightspeed Integration Settings', 'wc-lightspeed' ); ?></h2>

    <form method="post" action="options.php">
        <?php
        // Output the hidden fields and nonce for the settings group.
        settings_fields( 'wc_lightspeed_settings_group' );
        
        // Output the settings sections and fields registered for the 'wc-lightspeed-settings' page.
        do_settings_sections( 'wc-lightspeed-settings' );
        ?>

        <div class="wc-lightspeed-form-field">
            <label for="wc_lightspeed_api_key"><?php esc_html_e( 'Lightspeed API Key', 'wc-lightspeed' ); ?></label>
            <input type="password" id="wc_lightspeed_api_key" name="wc_lightspeed_api_key" value="<?php echo esc_attr( get_option( 'wc_lightspeed_api_key' ) ); ?>" class="regular-text">
            <p class="description"><?php esc_html_e( 'Enter your Lightspeed API key.', 'wc-lightspeed' ); ?></p>
        </div>

        <div class="wc-lightspeed-form-field">
            <label for="wc_lightspeed_in_stock_message"><?php esc_html_e( 'In Stock Message', 'wc-lightspeed' ); ?></label>
            <div class="wc-lightspeed-message-wrapper">
                <input type="text" id="wc_lightspeed_in_stock_message" name="wc_lightspeed_in_stock_message" value="<?php echo esc_attr( get_option( 'wc_lightspeed_in_stock_message' ) ); ?>" class="regular-text">
                <input type="color" id="wc_lightspeed_in_stock_color" name="wc_lightspeed_in_stock_color" value="<?php echo esc_attr( get_option( 'wc_lightspeed_in_stock_color', '#19A963' ) ); ?>" class="color-picker">
            </div>
            <p class="description"><?php esc_html_e( 'Enter your In Stock Message and select a color.', 'wc-lightspeed' ); ?></p>
        </div>

        <div class="wc-lightspeed-form-field">
            <label for="wc_lightspeed_low_stock_message"><?php esc_html_e( 'Low Stock Message', 'wc-lightspeed' ); ?></label>
            <div class="wc-lightspeed-message-wrapper">
                <input type="text" id="wc_lightspeed_low_stock_message" name="wc_lightspeed_low_stock_message" value="<?php echo esc_attr( get_option( 'wc_lightspeed_low_stock_message' ) ); ?>" class="regular-text">
                <input type="color" id="wc_lightspeed_low_stock_color" name="wc_lightspeed_low_stock_color" value="<?php echo esc_attr( get_option( 'wc_lightspeed_low_stock_color', '#EA9D2A' ) ); ?>" class="color-picker">
            </div>
            <p class="description"><?php esc_html_e( 'Enter your Low Stock Message and select a color.', 'wc-lightspeed' ); ?></p>
        </div>

        <div class="wc-lightspeed-form-field">
            <label for="wc_lightspeed_zero_stock_message"><?php esc_html_e( 'Zero Stock Message', 'wc-lightspeed' ); ?></label>
            <div class="wc-lightspeed-message-wrapper">
                <input type="text" id="wc_lightspeed_zero_stock_message" name="wc_lightspeed_zero_stock_message" value="<?php echo esc_attr( get_option( 'wc_lightspeed_zero_stock_message' ) ); ?>" class="regular-text">
                <input type="color" id="wc_lightspeed_zero_stock_color" name="wc_lightspeed_zero_stock_color" value="<?php echo esc_attr( get_option( 'wc_lightspeed_zero_stock_color', '#F14A4A' ) ); ?>" class="color-picker">
            </div>
            <p class="description"><?php esc_html_e( 'Enter your Zero Stock Message and select a color.', 'wc-lightspeed' ); ?></p>
        </div>


        <!-- If more fields are needed, add them here -->
        <!-- Example field -->
        <!--
        <div class="wc-lightspeed-form-field">
            <label for="some_other_option"><?php esc_html_e( 'Some Other Option', 'wc-lightspeed' ); ?></label>
            <input type="text" id="some_other_option" name="some_other_option" value="<?php echo esc_attr( get_option( 'some_other_option' ) ); ?>" class="regular-text">
        </div>
        -->

        <?php submit_button(); ?>
    </form>
</div>
