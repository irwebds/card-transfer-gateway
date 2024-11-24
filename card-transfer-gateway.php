<?php
/*
Plugin Name: Card Transfer Gateway
Plugin URI: https://webds.ir/card-transfer-gateway
Description: The Card Transfer Gateway plugin is a very simple plugin for users, which eliminates the need for online payment gateways.
Version: 1.0.4
Author: Webds
Author URI: https://webds.ir
Text Domain: card-transfer-gateway
Domain Path: /languages
License: GPL v3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CTGFREE_VERSION', '1.0.4' );
define( 'CTGFREE_DIR', plugin_dir_path(__FILE__));
define( 'CTGFREE_URI', plugin_dir_url(__FILE__));
define( 'CTGFREE_ASSETS_DIR', plugin_dir_path( __FILE__ ) . 'assets/');
define( 'CTGFREE_ASSETS_URI', plugin_dir_url( __FILE__ ) . 'assets/');
define( 'CTGFREE_INC_DIR', plugin_dir_path(__FILE__) . "inc/");
define( 'CTGFREE_INC_URI', plugin_dir_url(__FILE__). "inc/");

load_plugin_textdomain('card-transfer-gateway', false, dirname(plugin_basename(__FILE__)) . '/languages/');

// Register Gateway
include CTGFREE_INC_DIR . 'gateway.php';

// Register Cronjobs
include CTGFREE_INC_DIR . 'cronjobs.php';

// Update Order Status Methods
include CTGFREE_INC_DIR . 'updateStatus.php';

/**
 * Enqueue dashbaord Assets
 */
if(!function_exists('ctgfree_dashboard_enqueue_assets')){
    function ctgfree_dashboard_enqueue_assets() {

        wp_enqueue_style( 'ctg',  CTGFREE_ASSETS_URI . 'css/dashboard.css','',CTGFREE_VERSION );

    }
}
add_action( 'admin_enqueue_scripts', 'ctgfree_dashboard_enqueue_assets' );

/**
 * Enqueue front-end Assets
 */
if(!function_exists('ctgfree_frontend_enqueue_assets')){
    function ctgfree_frontend_enqueue_assets() {

        if( is_checkout() || !empty( is_wc_endpoint_url('order-received') ) || (function_exists('is_account_page') &&  is_account_page()) ){
            wp_enqueue_style( 'ctg',  CTGFREE_ASSETS_URI . 'css/ctg-style.css','',CTGFREE_VERSION );
        }
       
    }
}
add_action( 'wp_enqueue_scripts', 'ctgfree_frontend_enqueue_assets' );