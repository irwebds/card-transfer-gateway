<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Ensure WooCommerce is active before proceeding
add_action( 'plugins_loaded', 'ctgfree_initialize_card_transfer_gateway', 11 );
function ctgfree_initialize_card_transfer_gateway() {
    if ( class_exists( 'WooCommerce' ) ) {
        // Register custom order status
        add_action( 'init', 'ctgfree_register_card_transfer_gateway_order_status' );
        add_filter( 'wc_order_statuses', 'ctgfree_add_card_transfer_gateway_order_status' );

        // Register the custom payment gateway
        add_filter( 'woocommerce_payment_gateways', 'ctgfree_add_card_transfer_gateway' );
        
        // Initialize the custom gateway class
        if ( ! class_exists( 'WC_Card_Transfer_Gateway' ) ) {
            class WC_Card_Transfer_Gateway extends WC_Payment_Gateway {

                public function __construct() {
                    $this->id = 'card_transfer_gateway';
                    $this->icon = ''; 
                    $this->has_fields = false;
                    $this->method_title = __('Card Transfer Gateway','card-transfer-gateway');

                    $this->init_form_fields();
                    $this->init_settings();

                    $this->title = $this->get_option( 'title' );
                    $this->description = $this->get_option( 'description' );
                    $this->cardnumber = $this->get_option( 'cardnumber' );
                    $this->cardnumberName = $this->get_option( 'cardnumber_name' );
                    $this->telegram =$this->get_option( 'telegram' ) == 'yes' ? true:false;
                    $this->telegram_support = $this->get_option( 'telegram_support' );
                    $this->whatsapp = $this->get_option( 'whatsapp' ) == 'yes' ? true:false;
                    $this->whatsapp_support = $this->get_option( 'whatsapp_support' );

                    add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
                    if(!defined('CTGPRO_VERSION')){
                        add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
                    }
                }

                public function init_form_fields() {
                    $this->form_fields = array(
                        'info' => array(
                            'title'       => '',
                            'type'        => 'title',
                            'description' => '<a href="https://wipna.com/card-transfer-gateway/" target="_blank">' .'<img src="' . esc_url( CTGFREE_ASSETS_URI . 'img/banner.jpg' ) . '" width="640" loading="lazy" alt="Banner"/>' .'</a>',
                        ),
                        'enabled' => array(
                            'title'       => __('Enable','card-transfer-gateway'),
                            'type'        => 'checkbox',
                            'label'       => __('Enable Card Transfer Gateway','card-transfer-gateway'),
                            'default'     => 'yes'
                        ),
                        'title' => array(
                            'title'       => __('Title','card-transfer-gateway'),
                            'type'        => 'text',
                            'default'     => __('Card Transfer Payment','card-transfer-gateway')
                        ),
                        'description' => array(
                            'title'       =>  __('Description','card-transfer-gateway'),
                            'type'        => 'textarea',
                            'placeholder'     => __('Enter the payment method description here.','card-transfer-gateway')
                        ),
                        'time_cancellation' => array(
                            'title'       => __('Automatic Cancellation','card-transfer-gateway'),
                            'type'        => 'checkbox',
                            'label'       => __('Enable Automatic Cancellation','card-transfer-gateway'),
                            'default'     => 'no',
                            'class'       => 'separator-before'
                        ),
                        'time' => array(
                            'title'       => __('Automatic order cancellation after hours','card-transfer-gateway'),
                            'type'        => 'number',
                            'placeholder' => __('For Example: 12','card-transfer-gateway'),
                        ),
                        'cardnumber' => array(
                            'title'       => __('Card Number','card-transfer-gateway'),
                            'type'        => 'text',
                            'class'       => 'separator-before',
                            'placeholder' => __('Card Number','card-transfer-gateway')
                        ),
                        'cardnumber_name' => array(
                            'title'       => __('Name','card-transfer-gateway'),
                            'type'        => 'text',
                            'placeholder' => __('Owner Name','card-transfer-gateway')
                        ),
                        'telegram' => array(
                            'title'       => __('Telegram Support','card-transfer-gateway'),
                            'type'        => 'checkbox',
                            'label'       => __('Enable Telegram Support','card-transfer-gateway'),
                            'default'     => 'no',
                            'class'       => 'separator-before'
                        ),
                        'telegram_support' => array(
                            'title'       => __('Telegram Number/ID Link','card-transfer-gateway'),
                            'type'        => 'text',
                            'placeholder'     => 'https://t.me/+989112223333'
                        ),
                        'whatsapp' => array(
                            'title'       => __('Whatsapp Support','card-transfer-gateway'),
                            'type'        => 'checkbox',
                            'label'       => __('Enable Whatsapp Support','card-transfer-gateway'),
                            'default'     => 'no',
                            'class'       => 'separator-before'
                        ),
                        'whatsapp_support' => array(
                            'title'       => __('Whatsapp Link','card-transfer-gateway'),
                            'type'        => 'text',
                            'placeholder'     => 'https://wa.me/+989112223333'
                        ),
                    );
                }

                public function payment_fields(){
                    ?>
                    <fieldset>
                        <p class="form-row form-row-wide card-pay-gateway">
                            <?php echo (!empty($this->description) ? esc_attr($this->description):''); ?>
                            <span class="card-number"><?php echo (!empty($this->cardnumber) ? esc_attr($this->cardnumber):esc_attr('There is no card number.','card-transfer-gateway')); ?></span>
                            <span class="card-number-owner-name">
                                <b><?php echo esc_html__('Owner Name:', 'card-transfer-gateway'); ?></b> 
                                <?php echo (!empty($this->cardnumberName) ?  esc_html( $this->cardnumberName ) : esc_attr('There is no name.', 'card-transfer-gateway') ); ?>
                            </span>
                        </p>                        
                        <div class="clear"></div>
                        <?php if($this->whatsapp): ?>
                            <a href="<?php echo esc_attr($this->whatsapp_support); ?>" target="_blank" class="card-pay-gateway-whatsapp"><svg width="40px" height="40px" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M84.7925 257.334C1.81069 106.044 237.525 -11.6463 321.16 119.453C396.366 237.339 251.357 391.573 150.736 312.145" stroke="#000000" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M89.5909 265.912C34.5405 357.344 49.8143 347.445 133.267 311.303" stroke="#000000" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M170.381 113.42C60.1005 141.74 240.793 341.184 288.582 236.047" stroke="#000000" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M177.338 175.365C186.032 197.073 208.905 214.528 227.906 227.195" stroke="#000000" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M177.338 126.062C187.693 143.231 203.319 159.586 178.602 168.412" stroke="#000000" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M285.431 228.46C262.184 210.573 250.584 200.134 232.965 225.301" stroke="#000000" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg><?php echo esc_html__('Contact via Whatsapp','card-transfer-gateway'); ?></a>
                        <?php endif; ?>
                        <?php if($this->telegram): ?>
                            <a href="<?php echo esc_attr($this->telegram_support); ?>" target="_blank" class="card-pay-gateway-telegram"><svg fill="#000000" width="40px" height="40px" viewBox="0 0 256 256" id="Flat" xmlns="http://www.w3.org/2000/svg">
                                <path d="M228.646,34.7676a11.96514,11.96514,0,0,0-12.21778-2.0752L31.87109,105.19729a11.99915,11.99915,0,0,0,2.03467,22.93457L84,138.15139v61.833a11.8137,11.8137,0,0,0,7.40771,11.08593,12.17148,12.17148,0,0,0,4.66846.94434,11.83219,11.83219,0,0,0,8.40918-3.5459l28.59619-28.59619L175.2749,217.003a11.89844,11.89844,0,0,0,7.88819,3.00195,12.112,12.112,0,0,0,3.72265-.59082,11.89762,11.89762,0,0,0,8.01319-8.73925L232.5127,46.542A11.97177,11.97177,0,0,0,228.646,34.7676ZM32.2749,116.71877a3.86572,3.86572,0,0,1,2.522-4.07617L203.97217,46.18044,87.07227,130.60769,35.47461,120.28811A3.86618,3.86618,0,0,1,32.2749,116.71877Zm66.55322,86.09375A3.99976,3.99976,0,0,1,92,199.9844V143.72048l35.064,30.85669ZM224.71484,44.7549,187.10107,208.88772a4.0003,4.0003,0,0,1-6.5415,2.10937l-86.1543-75.8164,129.66309-93.645A3.80732,3.80732,0,0,1,224.71484,44.7549Z"/>
                                </svg><?php echo esc_html__('Contact via Telegram','card-transfer-gateway'); ?></a>
                        <?php endif; ?>
                    </fieldset>
                    <?php
                }

                public function process_payment( $order_id ) {
                    $order = wc_get_order( $order_id );
                    $order->update_status( 'wc-waiting-card-pay', __('Awaiting card transfer payment','card-transfer-gateway') );
                    wc_reduce_stock_levels( $order_id );
                    WC()->cart->empty_cart();
                    return array(
                        'result'   => 'success',
                        'redirect' => $this->get_return_url( $order )
                    );
                }

                public function thankyou_page() {
                    ?>
                    <fieldset>
                        <p class="form-row form-row-wide card-pay-gateway">
                            <?php echo esc_attr($this->description); ?>
                            <span class="card-number"><?php echo esc_attr($this->cardnumber); ?></span>
                            <span class="card-number-owner-name"><b><?php echo esc_html__('Owner Name:','card-transfer-gateway') . '</b>' . esc_attr($this->cardnumberName); ?></span>
                        </p>                       
                        <div class="clear"></div>
                        <?php if($this->whatsapp): ?>
                            <a href="<?php echo esc_attr($this->whatsapp_support); ?>" target="_blank" class="card-pay-gateway-whatsapp"><svg width="40px" height="40px" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M84.7925 257.334C1.81069 106.044 237.525 -11.6463 321.16 119.453C396.366 237.339 251.357 391.573 150.736 312.145" stroke="#000000" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M89.5909 265.912C34.5405 357.344 49.8143 347.445 133.267 311.303" stroke="#000000" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M170.381 113.42C60.1005 141.74 240.793 341.184 288.582 236.047" stroke="#000000" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M177.338 175.365C186.032 197.073 208.905 214.528 227.906 227.195" stroke="#000000" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M177.338 126.062C187.693 143.231 203.319 159.586 178.602 168.412" stroke="#000000" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M285.431 228.46C262.184 210.573 250.584 200.134 232.965 225.301" stroke="#000000" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg><?php echo esc_html__('Contact via Whatsapp','card-transfer-gateway'); ?></a>
                        <?php endif; ?>
                        <?php if($this->telegram): ?>
                            <a href="<?php echo esc_attr($this->telegram_support); ?>" target="_blank" class="card-pay-gateway-telegram"><svg fill="#000000" width="40px" height="40px" viewBox="0 0 256 256" id="Flat" xmlns="http://www.w3.org/2000/svg">
                                <path d="M228.646,34.7676a11.96514,11.96514,0,0,0-12.21778-2.0752L31.87109,105.19729a11.99915,11.99915,0,0,0,2.03467,22.93457L84,138.15139v61.833a11.8137,11.8137,0,0,0,7.40771,11.08593,12.17148,12.17148,0,0,0,4.66846.94434,11.83219,11.83219,0,0,0,8.40918-3.5459l28.59619-28.59619L175.2749,217.003a11.89844,11.89844,0,0,0,7.88819,3.00195,12.112,12.112,0,0,0,3.72265-.59082,11.89762,11.89762,0,0,0,8.01319-8.73925L232.5127,46.542A11.97177,11.97177,0,0,0,228.646,34.7676ZM32.2749,116.71877a3.86572,3.86572,0,0,1,2.522-4.07617L203.97217,46.18044,87.07227,130.60769,35.47461,120.28811A3.86618,3.86618,0,0,1,32.2749,116.71877Zm66.55322,86.09375A3.99976,3.99976,0,0,1,92,199.9844V143.72048l35.064,30.85669ZM224.71484,44.7549,187.10107,208.88772a4.0003,4.0003,0,0,1-6.5415,2.10937l-86.1543-75.8164,129.66309-93.645A3.80732,3.80732,0,0,1,224.71484,44.7549Z"/>
                                </svg><?php echo esc_html__('Contact via Telegram','card-transfer-gateway'); ?></a>
                        <?php endif; ?>
                    </fieldset>
                    <?php
                }
            }
        }
    }
}

// Register Waiting for Card Transfer status
if(!function_exists('ctgfree_register_card_transfer_gateway_order_status')){
    function ctgfree_register_card_transfer_gateway_order_status() {
        register_post_status( 'wc-waiting-card-pay', array(
            'label'                     => __('Waiting for Card Transfer','card-transfer-gateway'),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            /* translators: %d is the count of orders waiting for card transfer */
            'label_count'               => _n_noop( 'Waiting for Card Transfer (%s)', 'Waiting for Card Transfer (%s)', 'card-transfer-gateway'),
        ) );
    }
}
if(!function_exists('ctgfree_add_card_transfer_gateway_order_status')){
    function ctgfree_add_card_transfer_gateway_order_status( $order_statuses ) {
        $order_statuses['wc-waiting-card-pay'] = __('Waiting for Card Transfer','card-transfer-gateway');
        return $order_statuses;
    }
}

// Add the Card Transfer Gateway
if(!function_exists('ctgfree_add_card_transfer_gateway')){
    function ctgfree_add_card_transfer_gateway( $gateways ) {
        $gateways[] = 'WC_Card_Transfer_Gateway';
        return $gateways;
    }
}