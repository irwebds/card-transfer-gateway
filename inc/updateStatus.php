<?php 


/*
* Update Order Status
*/
function ctg_cronjob_update_orders_statuses(){
    $ctg_options = get_option( 'woocommerce_card_transfer_gateway_settings' );
    $hours = isset($ctg_options['time']) ? (int) $ctg_options['time'] : 0;
    
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
    do_action('ctg_after_update_orders_statuses');

}