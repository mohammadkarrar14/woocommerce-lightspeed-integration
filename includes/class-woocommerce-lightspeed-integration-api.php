<?php

/**
 * The core plugin class for LightSpeed API integration.
 *
 * This class handles the communication with the Lightspeed API, including authentication,
 * making requests, and handling responses.
 *
 * @since      1.0.0
 * @package    Woocommerce_Lightspeed_Integration
 * @subpackage Woocommerce_Lightspeed_Integration/includes
 * @author     Imagen Web Pro <hello@imagenwebpro.com>
 */
class Woocommerce_Lightspeed_Integration_API {

    /**
     * Base URL for the Lightspeed API.
     *
     * @var string
     */
    private $api_base_url = 'https://factorysound.retail.lightspeed.app/api/2.0/';

    /**
     * API Key for Lightspeed.
     *
     * @var string
     */
    private $api_key;

    /**
     * Constructor to initialize the class with the Lightspeed API key.
     *
     * @param string $api_key The Lightspeed API key.
     */
    public function __construct( $api_key ) {
        $this->api_key = $api_key;
    }

    /**
     * Perform a GET request to the Lightspeed API.
     *
     * @param string $endpoint The endpoint of the API (e.g., 'Account/{account_id}/Outlet').
     * @param array  $params   Optional query parameters to append to the URL.
     *
     * @return array|WP_Error The response data from the API or WP_Error on failure.
     */
    public function get( $endpoint, $params = [] ) {
        $url = $this->build_url( $endpoint, $params );
        $response = wp_remote_get( $url, $this->get_request_headers() );
        
        return $this->handle_response( $response );
    }

    /**
     * Perform a POST request to the Lightspeed API.
     *
     * @param string $endpoint The endpoint of the API (e.g., 'Account/{account_id}/Product').
     * @param array  $body     The data to send in the body of the request.
     *
     * @return array|WP_Error The response data from the API or WP_Error on failure.
     */
    public function post( $endpoint, $body = [] ) {
        $url = $this->build_url( $endpoint );
        $response = wp_remote_post( $url, array_merge( $this->get_request_headers(), [
            'body' => wp_json_encode( $body ),
        ]));
        
        return $this->handle_response( $response );
    }

    /**
     * Build the full API URL with endpoint and query parameters.
     *
     * @param string $endpoint The API endpoint (e.g., 'Account/{account_id}/Outlet').
     * @param array  $params   Optional query parameters.
     *
     * @return string The full URL to request.
     */
    private function build_url( $endpoint, $params = [] ) {
        $url = $this->api_base_url . $endpoint;

        if ( ! empty( $params ) ) {
            $url = add_query_arg( $params, $url );
        }

        return $url;
    }

    /**
     * Get the headers required for Lightspeed API requests.
     *
     * @return array The array of headers including the Authorization token.
     */
    private function get_request_headers() {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type'  => 'application/json',
            ],
        ];
    }

    /**
     * Handle the response from the API.
     *
     * @param WP_Error|array $response The response from wp_remote_get or wp_remote_post.
     *
     * @return array|WP_Error Parsed response data or a WP_Error.
     */
    private function handle_response( $response ) {
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );

        if ( 200 !== $code && 201 !== $code ) {
            return new WP_Error( 'api_error', __( 'API request failed.', 'wc-lightspeed' ), $body );
        }

        return json_decode( $body, true );
    }

    /**
     * Get the list of outlets from Lightspeed.
     *
     * @return array|WP_Error List of outlets or WP_Error on failure.
     */
    public function get_outlets() {
        return $this->get( 'outlets' );
    }

    /**
     * Get the inventory of a specific product from Lightspeed.
     *
     * @param int $product_id The ID of the product to retrieve inventory for.
     *
     * @return array|WP_Error Inventory data or WP_Error on failure.
     */
    public function get_product_inventory( $product_id ) {
        return $this->get( 'Inventory', ['itemID' => $product_id] );
    }

    /**
     * Get a product by ID from Lightspeed.
     *
     * @param int $product_id The ID of the product to retrieve.
     *
     * @return array|WP_Error Product data or WP_Error on failure.
     */
    public function get_product( $product_id ) {
        return $this->get( 'Item/' . $product_id );
    }

}
