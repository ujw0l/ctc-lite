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
 * Payment porcessing shortcode
 */
public function orderProcessingShortCode(){
    if(isset($_POST)):
        $date = new DateTime();
        $_POST['order_id'] = $date->getTimestamp();
        $_POST['checkout-email-address'] = sanitize_email( $_POST['checkout-email-address']);
        $_POST['checkout-special-instruction'] = sanitize_text_field($_POST['checkout-special-instruction']);

      $dataAfterPayment = apply_filters('ctcl_process_payment_'.$_POST['payment_option'],$_POST);

      if(1==$dataAfterPayment['charge_result']):
        $dataAfterShipping =  apply_filters('ctcl_shipping_option_'.$dataAfterPayment['shipping_option']  ,$dataAfterPayment);
        $ctclHtml = new ctclHtml();
        $emailBody = $ctclHtml->createEmailBody( $dataAfterShipping);
       
        echo $emailBody;
       $this->sendConfirmationEmail($dataAfterPayment['checkout-email-address'],get_option('ctcl_email_subject'),$emailBody);
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
            return __("Email couldn't be sent,please check email settings.",'ctc-lite' );
        endif;
    }
    
}