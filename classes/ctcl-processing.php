<?php

class ctclProcessing{
    

    /**
     * send test email with ajax
     */
    public function sendSmtpTestEmail(){
        $subject = __('Test email','ctc-lite');
        $emaiBody = '<p>'.__('This is test email, you may ignore it.').'</p>';
        $this->sendConfirmationEmail( $emailBody,$subject,sanitize_email($_POST['email']));
        wp_die();

    }

    /**
     * Php mailer email setup
     */
    public function smtpEmailSetting(){

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
    	$mail->From       = sanitize_email(get_option('ctcl_smtp_from_email')) ;
        $mail->FromName   =  get_option('ctcl_smtp_from_name') ;
        $mail->addBCC(sanitize_email(get_option('ctcl_smtp_from_email')), __('Admin','ctc-lite'));
    	$mail->IsHTML(true);
    	$mail->SMTPDebug = 1;

    }

    /**
     * Send confirmation email
     */
    public function sendConfirmationEmail($emailBody,$emailAddress,$subject){
		global $phpmailer;
		wp_mail($emailAddress, $subject , $emailBody);
    }
    
}