<?php
/*
Plugin Name: Card Transfer Gateway
Plugin URI: https://webds.ir/card-transfer-gateway
Description: The Card Transfer Gateway plugin is a very simple plugin for users, which eliminates the need for online payment gateways.
Version: 1.0.2
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

define( 'CTG_VERSION', '1.0.2' );
define( 'CTG_DIR', plugin_dir_path(__FILE__));
define( 'CTG_URI', plugin_dir_url(__FILE__));
define( 'CTG_ASSETS_DIR', plugin_dir_path( __FILE__ ) . 'assets/');
define( 'CTG_ASSETS_URI', plugin_dir_url( __FILE__ ) . 'assets/');
define( 'CTG_INC_DIR', plugin_dir_path(__FILE__) . "inc/");
define( 'CTG_INC_URI', plugin_dir_url(__FILE__). "inc/");

load_plugin_textdomain('card-transfer-gateway', false, dirname(plugin_basename(__FILE__)) . '/languages/');

// Register Gateway
include CTG_INC_DIR . 'gateway.php';

// Register Cronjobs
include CTG_INC_DIR . 'cronjobs.php';

// Update Order Status Methods
include CTG_INC_DIR . 'updateStatus.php';

/**
 * Enqueue dashbaord Assets
 */
function ctg_dashboard_enqueue_assets() {

    wp_enqueue_style( 'ctg',  CTG_ASSETS_URI . 'css/dashboard.css','',CTG_VERSION );

}
add_action( 'admin_enqueue_scripts', 'ctg_dashboard_enqueue_assets' );

/**
 * Enqueue front-end Assets
 */
function ctg_frontend_enqueue_assets() {

    if( is_checkout() || !empty( is_wc_endpoint_url('order-received') ) || (function_exists('is_account_page') &&  is_account_page()) ){
        wp_enqueue_style( 'ctg',  CTG_ASSETS_URI . 'css/ctg-style.css','',CTG_VERSION );
    }
   
}
add_action( 'wp_enqueue_scripts', 'ctg_frontend_enqueue_assets' );