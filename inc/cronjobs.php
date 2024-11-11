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
    $ctg_options = get_option( 'woocommerce_card_transfer_gateway_settings' );
    if($ctg_options && !empty($ctg_options)){

        $cancellation = !empty($ctg_options['time_cancellation']) && $ctg_options['time_cancellation'] == 'yes' ? true : false;
        $time = !empty($ctg_options['time']) ? intval($ctg_options['time']) : 6;
        
        $time = $time * 60 * 60;

        if ($cancellation) {
            $schedules['ctg_cronjob_update_orders'] = array(
                'interval' => $time,
                // translators: %s is replaced by the number of hours.
                'display'  => sprintf( __( 'Each %s Hour', 'card-transfer-gateway' ), ($time / 60 / 60) ),
            );
        }else{
          unset($schedules['ctg_cronjob_update_orders']);
        }
    }

    return $schedules;
}

$ctg_options = get_option( 'woocommerce_card_transfer_gateway_settings' );

if ( ! wp_next_scheduled( 'ctg_cronjob_update_order_statuses_cron_hook' ) && isset($ctg_options['time_cancellation']) && $ctg_options['time_cancellation'] == 'yes') {
    wp_schedule_event( time(), 'ctg_cronjob_update_orders', 'ctg_cronjob_update_order_statuses_cron_hook' );
}