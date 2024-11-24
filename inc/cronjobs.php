<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
*
* Set Cronjob: Update Order Statuses
*
*/
add_filter( 'cron_schedules', 'ctgfree_cronjob_update_order_statuses_schedule' );
add_action( 'ctgfree_cronjob_update_order_statuses_cron_hook', 'ctgfree_cronjob_update_orders_statuses' );

function ctgfree_cronjob_update_order_statuses_schedule( $schedules ) {

    if(!defined('CTGPRO_VERSION')){
        $ctgfree_options = get_option( 'woocommerce_card_transfer_gateway_settings' );
        if($ctgfree_options && !empty($ctgfree_options)){

            $cancellation = !empty($ctgfree_options['time_cancellation']) && $ctgfree_options['time_cancellation'] == 'yes' ? true : false;
        
            if ($cancellation) {
                $schedules['ctgfree_cronjob_update_orders'] = array(
                    'interval' => 300,
                    // translators: %s is replaced by the number of hours.
                    'display'  => sprintf( __( 'Every %s Minutes', 'card-transfer-gateway' ), 5 ),
                );
            }else{
                unset($schedules['ctgfree_cronjob_update_orders']);
            }
        }
    }else{
        unset($schedules['ctgfree_cronjob_update_orders']);
    }
    return $schedules;
}

$ctgfree_options = get_option( 'woocommerce_card_transfer_gateway_settings' );

if (!defined('CTGPRO_VERSION') && ! wp_next_scheduled( 'ctgfree_cronjob_update_order_statuses_cron_hook' ) && isset($ctgfree_options['time_cancellation']) && $ctgfree_options['time_cancellation'] == 'yes') {
    wp_schedule_event( time(), 'ctgfree_cronjob_update_orders', 'ctgfree_cronjob_update_order_statuses_cron_hook' );
}