<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/*
*
* Set Cronjob: Update Order Statuses
*
*/
add_filter( 'cron_schedules', 'ctg_cronjob_update_order_statuses_schedule' );
add_action( 'ctg_cronjob_update_order_statuses_cron_hook', 'ctg_cronjob_update_orders_statuses' );

function ctg_cronjob_update_order_statuses_schedule( $schedules ) {

    if(!defined('CTGPRO_VERSION')){
        $ctg_options = get_option( 'woocommerce_card_transfer_gateway_settings' );
        if($ctg_options && !empty($ctg_options)){

            $cancellation = !empty($ctg_options['time_cancellation']) && $ctg_options['time_cancellation'] == 'yes' ? true : false;
        
            if ($cancellation) {
                $schedules['ctg_cronjob_update_orders'] = array(
                    'interval' => 300,
                    // translators: %s is replaced by the number of hours.
                    'display'  => sprintf( __( 'Every %s Minutes', 'card-transfer-gateway' ), 5 ),
                );
            }else{
                unset($schedules['ctg_cronjob_update_orders']);
            }
        }
    }else{
        unset($schedules['ctg_cronjob_update_orders']);
    }
    return $schedules;
}

$ctg_options = get_option( 'woocommerce_card_transfer_gateway_settings' );

if (!defined('CTGPRO_VERSION') && ! wp_next_scheduled( 'ctg_cronjob_update_order_statuses_cron_hook' ) && isset($ctg_options['time_cancellation']) && $ctg_options['time_cancellation'] == 'yes') {
    wp_schedule_event( time(), 'ctg_cronjob_update_orders', 'ctg_cronjob_update_order_statuses_cron_hook' );
}