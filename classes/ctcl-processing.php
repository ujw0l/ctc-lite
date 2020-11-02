<?php

class ctclProcessing{
    

    /**
     * Send test email with ajax
     */
    public function sendSmtpTestEmail(){
        $subject = __('Test email','ctc-lite');
        $emailBody = '<p>'.__('This is test email, you may ignore it.').'</p>';
       echo  $this->sendConfirmationEmail( $_POST['email'],$subject,$emailBody);
        wp_die();
    }

    /**
     * Php mailer email setup
     */
    public function smtpEmailSetting($mail){

        $mail->isSMTP();
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
 * Order porcessing shortcode
 */
public function orderProcessingShortCode(){

    if(!empty($_POST)):
        $date = new DateTime();
        $_POST['order_id'] = $date->getTimestamp();
        $_POST['checkout-email-address'] = sanitize_email( $_POST['checkout-email-address']);
        $_POST['checkout-special-instruction'] = sanitize_text_field($_POST['checkout-special-instruction']);

      $dataAfterPayment = apply_filters('ctcl_process_payment_'.$_POST['payment_option'],$_POST);

      if(1==$dataAfterPayment['charge_result']):
        apply_filters('ctcl_data_for_ml',$dataAfterPayment);
        $this->enterDataToTable($dataAfterPayment);
        $dataAfterShipping =  apply_filters('ctcl_shipping_option_'.$dataAfterPayment['shipping_option']  ,$dataAfterPayment);
        $custEmailBody = apply_filters('ctcl_custom_email_body','',$dataAfterShipping);
        if(empty( $custEmailBody)):
             $ctclHtml = new ctclHtml();
            $emailBody = $ctclHtml->createEmailBody( $dataAfterShipping);
        else:
            $emailBody = $custEmailBody;
        endif;
       $this->sendConfirmationEmail($dataAfterPayment['checkout-email-address'],get_option('ctcl_email_subject'),$emailBody);
       echo "<div id='ctcl-order-sucesfully-placed'>".__('Order successfully placed. Your order id is')." : {$_POST['order_id']} </div>";
      else:
        echo "<p>{$processPayment['failure_message']}</p>";
      endif;
    endif;
}

    /**
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
     * Enter data to the table
     */
    public function enterDataToTable($data){
     global $wpdb;
     $wpdb->insert( $wpdb->prefix."ctclOrders", array( 'orderId'=> $data['order_id'], 'orderDetail' => json_encode($data), 'orderStatus' => 'pending' ), array('%s' ,'%s', '%s' ) );
    }

    /**
     * get total pending order
     */
    public function getTotalPendingOrders(){
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(`orderId`) FROM {$wpdb->prefix}ctclOrders WHERE orderStatus= 'pending'");
    }

    /**
     * 
     */
    public function getTotalCompleteOrders(){
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(`orderId`) FROM {$wpdb->prefix}ctclOrders WHERE orderStatus= 'complete'");
    }
    /**
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
     * get order detail from database
     */
    public function getOrderDetail($orderId){
        global $wpdb;
    return  $wpdb->get_var("SELECT orderDetail FROM {$wpdb->prefix}ctclOrders WHERE orderId='{$orderId}'");
    }
    /**
     * update vendor note
     */
    public function updateOrderVendorNote(){
        global $wpdb;
        $update = $wpdb->update($wpdb->prefix.'ctclOrders',array('vendorNote'=>$_POST['vendorNote']),array('orderId'=>$_POST['orderId']));

       if(1==$update):
        _e("Note sucessfully saved","ctc-lite");
       else:
        _e("Note could not be saved at this time","ctc-lite");
       endif;
        wp_die();
    }

    /**
     * Mark order complete
     */
    public function orderMarkComplete(){
        global $wpdb;
        $complete = $wpdb->update($wpdb->prefix.'ctclOrders',array('orderStatus'=>'complete'),array('orderId'=>$_POST['orderId']));

        if(1==$complete):
            _e("Order marked complete","ctc-lite");
           else:
            _e("Order could not be marked complete at this time","ctc-lite");
           endif;
        wp_die();
    }

    /**
     * Cancel order
     */
    public function cancelOrder(){
        global $wpdb;
        $delete = $wpdb->delete($wpdb->prefix.'ctclOrders', array('orderId'=>$_POST['orderId']),array('%d'));
       
        if(1 == $delete):
            _e("Order sucessfully canceled","ctc-lite");
           else:
            _e("Order could not be cancled at this time","ctc-lite");
           endif;
        wp_die();

    }

    /**
     * Get vendor node for order
     */
    public function getVendorNote($orderId){
        global $wpdb;
        return  $wpdb->get_var("SELECT vendorNote FROM {$wpdb->prefix}ctclOrders WHERE orderId='{$orderId}'");
    }
}