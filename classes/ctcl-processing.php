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