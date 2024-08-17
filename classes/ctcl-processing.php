<?php

class ctclProcessing{
    

    /**
     * @since 1.0.0
     *
     * Send test email with ajax
     */
    public function sendSmtpTestEmail(){
        $subject = __('Test email','ctc-lite');
        $emailBody = '<p>'.__('This is test email, you may ignore it.').'</p>';
       echo  $this->sendConfirmationEmail( sanitize_email($_POST['email']),$subject,$emailBody);
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
    	$mail->smtpConnect([
    			'ssl' => [
    					'verify_peer' => false,
    					'verify_peer_name' => false,
    					'allow_self_signed' => true
    			]
    	]);
    	
    	$mail->Host       =  get_option('ctcl_smtp_host') ;
    	$mail->SMTPAuth   =  get_option('ctcl_smtp_authentication') ;
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

        $postArr = $_POST;

        $postArr['order_id'] = $date->getTimestamp();
        $postArr['checkout-email-address'] = sanitize_email( $_POST['checkout-email-address']);
        $postArr['checkout-special-instruction'] = sanitize_text_field($_POST['checkout-special-instruction']);

      $dataAfterPayment = apply_filters('ctcl_process_payment_'.$postArr['payment_option'],$postArr);

      
      if(1==$dataAfterPayment['charge_result']):
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
       $this->sendConfirmationEmail($dataAfterPayment['checkout-email-address'],get_option('ctcl_email_subject'),$emailBody);
       return "<div style='margin-left:auto;margin-right:auto;display:block;'; id='ctcl-order-sucesfully-placed'>".__('Order successfully placed. You will get email with details . <br/>Your order id is')." : {$postArr['order_id']} </div>";
      else:
        return "<p>{$processPayment['failure_message']}</p>";
      endif;
    endif;
}

    /**
     *  @since 1.0.0
     *
     * Send confirmation email
     */
    public function sendConfirmationEmail($emailAddress,$subject,$emailBody){
        global $phpmailer;
        $headers[] = 'Bcc:'. get_option('ctcl_smtp_bcc_email');
        $emailSent = wp_mail($emailAddress, $subject , $emailBody,$headers);
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
    return  $wpdb->get_var("SELECT orderDetail FROM {$wpdb->prefix}ctclOrders WHERE orderId='{$orderId}'");
    }
    /**
     * @since 1.0.0
     *
     * update vendor note
     */
    public function updateOrderVendorNote(){
        global $wpdb;
        $update = $wpdb->update($wpdb->prefix.'ctclOrders',array('vendorNote'=>sanitize_textarea_field($_POST['vendorNote'])),array('orderId'=>absint($_POST['orderId'])));

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
        global $wpdb;
        $complete = $wpdb->update($wpdb->prefix.'ctclOrders',array('orderStatus'=>'complete'),array('orderId'=>absint($_POST['orderId'])));

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
        global $wpdb;
        $delete = $wpdb->delete($wpdb->prefix.'ctclOrders', array('orderId'=>absint($_POST['orderId'])),array('%d'));
       
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

        global $wpdb;


        // Filter call.
        $value = apply_filters( 'ctcl_refund_order', true, $_POST['orderId'], );

        if(true == $value):
          $complete = $wpdb->update($wpdb->prefix.'ctclOrders',array('orderStatus'=>'refund'),array('orderId'=>absint($_POST['orderId'])));
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
        return  $wpdb->get_var("SELECT vendorNote FROM {$wpdb->prefix}ctclOrders WHERE orderId='{$orderId}'");
    }


    

}