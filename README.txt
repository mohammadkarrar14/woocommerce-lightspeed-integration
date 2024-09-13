=== WooCommerce Lightspeed Integration ===
Developer: https://imagenwebpro.com/
Tags: woocommerce, lightspeed, inventory, sync, stock levels
Requires at least: 3.0.1
Tested up to: 6.6.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin connects WooCommerce with Lightspeed POS to sync inventory and display stock levels across multiple outlets.

== Description ==

WooCommerce Lightspeed Integration is a custom solution that connects your WooCommerce store with Lightspeed POS, syncing product inventory from multiple outlets and displaying stock levels directly on WooCommerce product pages. It helps manage inventory more efficiently by fetching real-time stock data from Lightspeed.

Key Features:
* Sync product inventory from multiple Lightspeed outlets
* Display real-time stock levels on WooCommerce product pages
* Efficient inventory management with automatic data fetching from Lightspeed

== Installation ==

To install the plugin, follow these steps:

1. Upload `woocommerce-lightspeed-integration.php` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure your Lightspeed API settings in WooCommerce settings.
4. Place `<?php do_action('plugin_name_hook'); ?>` in your templates where you want to display stock levels.

== Frequently Asked Questions ==

= How do I configure the Lightspeed API settings? =

Go to WooCommerce > Settings > Lightspeed Integration and add your API key.

= How often is the inventory data synced? =

The plugin fetches real-time inventory data when a customer views a product page. You can enable caching if required.

== Screenshots ==

1. Inventory sync settings in WooCommerce.
2. Stock levels displayed on a product page.

== Changelog ==

= 1.0 =
* Initial release of the WooCommerce Lightspeed Integration plugin.

== Upgrade Notice ==

= 1.0 =
This version includes the initial release. Upgrade if you're using a previous development version.

== Arbitrary section ==

You can customize the messages for stock levels (e.g., "In Stock," "Low Stock," "Out of Stock") and the corresponding color codes in the WooCommerce settings under Lightspeed Integration.

== A brief Markdown Example ==

Ordered list:

1. Sync inventory with Lightspeed POS
2. Display stock levels for multiple outlets
3. Efficiently manage your WooCommerce store's stock

Unordered list:

* Sync multiple outlets
* Display stock on product pages
* Customize stock messages

For more details, visit [WooCommerce](https://woocommerce.com/) or check [Lightspeed's API Documentation][lightspeed api].

[lightspeed api]: https://developers.lightspeedhq.com/retail/
