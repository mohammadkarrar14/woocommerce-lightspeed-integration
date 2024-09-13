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
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wc-lightspeed-outlets-wrap">
    <h2><?php esc_html_e( 'Lightspeed Outlets', 'wc-lightspeed' ); ?></h2>

    <?php
    // Check if outlets data is available and display the table
    if ( ! empty( $outlets ) ) {
    ?>
    <table class="wc-lightspeed-outlets-table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Outlet Name', 'wc-lightspeed' ); ?></th>
                <th><?php esc_html_e( 'Address', 'wc-lightspeed' ); ?></th>
                <th><?php esc_html_e( 'City', 'wc-lightspeed' ); ?></th>
                <th><?php esc_html_e( 'State', 'wc-lightspeed' ); ?></th>
                <th><?php esc_html_e( 'Postal Code', 'wc-lightspeed' ); ?></th>
                <th><?php esc_html_e( 'Physical Suburb', 'wc-lightspeed' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ( $outlets as $outlet ) {
                ?>
                <tr>
                    <td><?php echo esc_html( $outlet['name'] ); ?></td>
                    <td><?php echo esc_html( $outlet['physical_address_1'] ); ?></td>
                    <td><?php echo esc_html( $outlet['physical_city'] ); ?></td>
                    <td><?php echo esc_html( $outlet['physical_state'] ); ?></td>
                    <td><?php echo esc_html( $outlet['physical_postcode'] ); ?></td>
                    <td><?php echo esc_html( $outlet['physical_suburb'] ); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
    } else {
        // Handle case where no outlets are returned
        echo '<p>' . esc_html__( 'No outlets found.', 'wc-lightspeed' ) . '</p>';
    }
    ?>
</div>
