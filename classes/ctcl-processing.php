<?php

class ctclProcessing{
    /** Require the same capability as the plugin settings and a session-bound nonce. */
    public static function authorizeAdminRequest(){
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to manage orders.', 'ctc-lite'), '', array('response' => 403));
        }
        check_ajax_referer('ctcl_admin', 'nonce');
    }

    public static function requestOrderId(){
        $id = isset($_POST['orderId']) && is_scalar($_POST['orderId']) ? (string) $_POST['orderId'] : '';
        if (!ctype_digit($id) || (int) $id < 1) {
            wp_die(esc_html__('Invalid order ID.', 'ctc-lite'), '', array('response' => 400));
        }
        return $id;
    }

    

    /**
     * @since 1.0.0
     *
     * Send test email with ajax
     */
    public function sendSmtpTestEmail(){
        self::authorizeAdminRequest();
        $subject = __('Test email','ctc-lite');
        $emailBody = '<p>'.__('This is test email, you may ignore it.').'</p>';
       echo  $this->sendConfirmationEmail( sanitize_email(wp_unslash(is_string($_POST['email'] ?? null) ? $_POST['email'] : '')),$subject,$emailBody);
        wp_die();
    }

    /**
     *  @since 1.0.0
     *
     * Php mailer email setup
     */
    public function smtpEmailSetting($mail){

        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        // Configure only: wp_mail() connects during send(), inside its exception handler.
        // Keep PHPMailer certificate verification enabled.

    	$mail->Host       =  get_option('ctcl_smtp_host') ;
        $mail->SMTPAuth   =  filter_var(get_option('ctcl_smtp_authentication'), FILTER_VALIDATE_BOOLEAN) ;
    	$mail->Port       =  get_option('ctcl_smtp_port') ;
    	$mail->Username   = get_option('ctcl_smtp_username') ;
    	$mail->Password   = get_option('ctcl_smtp_password') ;
        $mail->SMTPSecure = get_option('ctcl_smtp_encryption') ;
    	$mail->From       = get_option('ctcl_smtp_from_email') ;
        $mail->FromName   =  get_option('ctcl_smtp_from_name') ;
    	$mail->IsHTML(true);
    	$mail->SMTPDebug = 0;

    }


/**
 * @since 1.0.0
 *
 * Order porcessing shortcode
 */
public function orderProcessingShortCode(){

    if(!empty($_POST)):

       
        $date = new DateTime();

        $postArr = wp_unslash($_POST);
        // A visitor must never supply the processor's result or select an arbitrary hook.
        unset($postArr['charge_result'], $postArr['failure_message']);
        $paymentId = is_string($postArr['payment_option'] ?? null) ? $postArr['payment_option'] : '';
        $shippingId = is_string($postArr['shipping_option'] ?? null) ? $postArr['shipping_option'] : '';
        $payments = apply_filters('ctcl_payment_options', array());
        $shippings = apply_filters('ctcl_shipping_option_display', array());
        $payment = null;
        $shipping = null;
        foreach ((array) $payments as $option) {
            if (isset($option['id']) && $option['id'] === $paymentId) { $payment = $option; }
        }
        foreach ((array) $shippings as $option) {
            if (isset($option['id']) && $option['id'] === $shippingId) { $shipping = $option; }
        }
        if (!$payment || !$shipping || !has_filter('ctcl_process_payment_' . $paymentId)) {
            return '<p>' . esc_html__('Please select an available payment and shipping option.', 'ctc-lite') . '</p>';
        }
        $postArr['order_id'] = $date->getTimestamp();
        $postArr['payment_type'] = sanitize_text_field($payment['name']);
        $postArr['shipping_type'] = sanitize_text_field($shipping['name']);
        $postArr['checkout-email-address'] = sanitize_email(is_string($postArr['checkout-email-address'] ?? null) ? $postArr['checkout-email-address'] : '');
        foreach (array('ctcl-co-first-name', 'ctcl-co-last-name', 'checkout-street-address-1', 'checkout-street-address-2', 'checkout-city', 'checkout-state', 'checkout-zip-code', 'checkout-country', 'checkout-special-instruction') as $field) {
            $postArr[$field] = sanitize_text_field(is_string($postArr[$field] ?? null) ? $postArr[$field] : '');
        }
        require_once __DIR__ . '/ctcl-checkout-validation.php';
        $postArr = ctclCheckoutValidation::validate($postArr);
        if (is_wp_error($postArr)) {
            return '<p>' . esc_html($postArr->get_error_message()) . '</p>';
        }
        $postArr['charge_result'] = false;
        $dataAfterPayment = apply_filters('ctcl_process_payment_' . $paymentId, $postArr);

      if(is_array($dataAfterPayment) && in_array($dataAfterPayment['charge_result'] ?? false, array(true, 1, '1'), true)):
        apply_filters('ctcl_data_for_ml',$dataAfterPayment);
        $this->enterDataToTable($dataAfterPayment);
        do_action('ctcl-order-placed', $dataAfterPayment);
        $dataAfterShipping =  apply_filters('ctcl_shipping_option_'.$dataAfterPayment['shipping_option']  ,$dataAfterPayment);
        $custEmailBody = apply_filters('ctcl_custom_email_body','',$dataAfterShipping);
        if(empty( $custEmailBody)):
             $ctclHtml = new ctclHtml();
            $emailBody = $ctclHtml->createEmailBody( $dataAfterShipping);
        else:
            $emailBody = $custEmailBody;
        endif;
       $emailSent = $this->sendConfirmationEmail($dataAfterPayment['checkout-email-address'],get_option('ctcl_email_subject'),$emailBody, true);
       $message = $emailSent
           ? __('Order successfully placed. Your confirmation email has been sent.', 'ctc-lite')
           : __('Order successfully placed, but the confirmation email could not be sent. Please contact the store with your order ID. Do not submit the order again.', 'ctc-lite');
       return "<div id='ctcl-order-sucesfully-placed'>" . esc_html($message) . '<br/>'
           . esc_html__('Your order ID is', 'ctc-lite') . ' : ' . esc_html($postArr['order_id']) . '</div>';
      else:
        return '<p>' . esc_html(is_array($dataAfterPayment) && is_string($dataAfterPayment['failure_message'] ?? null) ? $dataAfterPayment['failure_message'] : __('Payment could not be confirmed. Please try again.', 'ctc-lite')) . '</p>';
      endif;
    endif;
}

    /**
     *  @since 1.0.0
     *
     * Send confirmation email
     */
    public function sendConfirmationEmail($emailAddress,$subject,$emailBody,$returnStatus = false){
        $headers = array();
        $bcc = get_option('ctcl_smtp_bcc_email');
        if (!empty($bcc)) {
            $headers[] = 'Bcc:' . $bcc;
        }
        $emailSent = wp_mail($emailAddress, $subject , $emailBody,$headers);
        if ($returnStatus) {
            return $emailSent;
        }
		if($emailSent):
            return __('Email sent sucessfully','ctc-lite');
        else:
            return __("Email couldn't be sent,please check email settings and retry.",'ctc-lite' );
        endif;
    }

    /**
     * @since 1.0.0
     *
     * Enter data to the table
     */
    public function enterDataToTable($data){
     global $wpdb;
     $wpdb->insert( $wpdb->prefix."ctclOrders", array( 'orderId'=> $data['order_id'], 'orderDetail' => json_encode($data), 'orderStatus' => 'pending' ), array('%s' ,'%s', '%s' ) );
    }

    /**
     * @since 1.0.0
     *
     * get total pending order
     */
    public function getTotalPendingOrders(){
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(`orderId`) FROM {$wpdb->prefix}ctclOrders WHERE orderStatus= 'pending'");
    }

    /**
     *@since 1.0.0
     *
     * Get Complete order list
     */
    public function getTotalCompleteOrders(){
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(`orderId`) FROM {$wpdb->prefix}ctclOrders WHERE orderStatus= 'complete'");
    }
    /**
     * @since 1.0.0
     *
     * get list of pending orders
     * 
     * @param $offset database table offset
     * @param $limit databse row limit
     * 
     * @return Item list between $offset and $limit
     */
    public function getPendingOrderEntries($offset,$limit){
         global $wpdb;
       return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctclOrders  WHERE orderStatus= 'pending' LIMIT $offset, $limit",ARRAY_A );

    }

    /**
     * @since 1.0.0
     *
     * get list of complete orders
     * 
     * @param $offset database table offset
     * @param $limit databse row limit
     * 
     * @return Item list between $offset and $limit
     */
    public function getCompleteOrderEntries($offset,$limit){
        global $wpdb;
      return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctclOrders  WHERE orderStatus= 'complete' LIMIT $offset, $limit",ARRAY_A );

   }

    /**
     * @since 1.0.0
     *
     * get order detail from database
     */
    public function getOrderDetail($orderId){
        global $wpdb;
    return  $wpdb->get_var($wpdb->prepare("SELECT orderDetail FROM {$wpdb->prefix}ctclOrders WHERE orderId=%s", $orderId));
    }
    /**
     * @since 1.0.0
     *
     * update vendor note
     */
    public function updateOrderVendorNote(){
        self::authorizeAdminRequest();
        $orderId = self::requestOrderId();
        global $wpdb;
        $update = $wpdb->update($wpdb->prefix.'ctclOrders',array('vendorNote'=>sanitize_textarea_field(wp_unslash(is_string($_POST['vendorNote'] ?? null) ? $_POST['vendorNote'] : ''))),array('orderId'=>$orderId));

       if(1==$update):
        _e("Note sucessfully saved","ctc-lite");
       else:
        _e("Note could not be saved at this time","ctc-lite");
       endif;
        wp_die();
    }

    /**
     * @since 1.0.0
     *
     * Mark order complete
     */
    public function orderMarkComplete(){
        self::authorizeAdminRequest();
        $orderId = self::requestOrderId();
        global $wpdb;
        $complete = $wpdb->update($wpdb->prefix.'ctclOrders',array('orderStatus'=>'complete'),array('orderId'=>$orderId));

        if(1==$complete):
            _e("Order marked complete","ctc-lite");
           else:
            _e("Order could not be marked complete at this time","ctc-lite");
           endif;
        wp_die();
    }

    /**
     * @since 1.0.0
     *
     * Cancel order
     */
    public function cancelOrder(){
        self::authorizeAdminRequest();
        $orderId = self::requestOrderId();
        global $wpdb;
        $delete = $wpdb->delete($wpdb->prefix.'ctclOrders', array('orderId'=>$orderId),array('%d'));
       
        if(1 == $delete):
            _e("Order sucessfully canceled","ctc-lite");
           else:
            _e("Order could not be cancled at this time","ctc-lite");
           endif;
        wp_die();

    }

    /**
     * @since 2.5.0
     * 
     * Refund Order
     */
    public function refundOrder(){
        self::authorizeAdminRequest();
        $orderId = self::requestOrderId();

        global $wpdb;


        // Filter call.
        $value = apply_filters( 'ctcl_refund_order', true, $orderId, );

        if(true == $value):
          $complete = $wpdb->update($wpdb->prefix.'ctclOrders',array('orderStatus'=>'refund'),array('orderId'=>$orderId));
          if(1==$complete): 
                _e("Order sucessfully refunded","ctc-lite");
          else:
            _e("Order couldn't be refuned","ctc-lite");
          endif;
        else:
            _e("Order couldn't be refuned","ctc-lite");
        endif;  

      
        wp_die();
    }

    /**
     * @since 1.0.0
     *
     * Get vendor note for order
     */
    public function getVendorNote($orderId){
        global $wpdb;
        return  $wpdb->get_var($wpdb->prepare("SELECT vendorNote FROM {$wpdb->prefix}ctclOrders WHERE orderId=%s", $orderId));
    }


    

}