<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
* Update Order Status
*/
function ctgfree_cronjob_update_orders_statuses(){
    $ctgfree_options = get_option( 'woocommerce_card_transfer_gateway_settings' );
    $hours = isset($ctgfree_options['time']) ? (int) $ctgfree_options['time'] : 0;
    
    if ($hours > 0) {
        $in_seconds = $hours * 60 * 60;
        $args = array(
            'date_created' => '<' . ( time() - $in_seconds ),
            'status'       => array( 'wc-waiting-card-pay' ),
        );
        $orders = wc_get_orders( $args );
    
        if ( ! empty ( $orders ) ) {
            foreach ( $orders as $order ) {
                // Update Status
                $order->update_status( 'wc-cancelled' );
                $order->save();
            }
        }
    }
    do_action('ctgfree_after_update_orders_statuses');

}