<?php
class ctclHtml{


/**
 * display admin panel content
 */
public function adminPanelHtml(){

    $additionalTabs = apply_filters('ctcl-additional_tab',array());

    $activeTab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'info';
    $dynmTab = array();
    $arrTab=array();
?>
<div class="ctclAdminPanel">	               
<h2><span class="dashicons-before dashicons-store"></span>CTC Lite Admin Panel</h2>
    <h4 class="nav-tab-wrapper">
        <a href="?page=ctclAdminPanel&tab=info" class="nav-tab dashicons-before dashicons-info <?=$activeTab == 'info' ? 'nav-tab-active' : ''; ?> ">Information</a>
        <a href="?page=ctclAdminPanel&tab=billing" class="nav-tab dashicons-before dashicons-money <?=$activeTab == 'billing' ? 'nav-tab-active' : ''; ?> ">Billing</a>
        <a href="?page=ctclAdminPanel&tab=shipping" class="nav-tab dashicons-before dashicons-car <?=$activeTab == 'shipping' ? 'nav-tab-active' : ''; ?> ">Shipping</a>
        <a href="?page=ctclAdminPanel&tab=email" class="nav-tab dashicons-before dashicons-email-alt2 <?=$activeTab == 'email' ? 'nav-tab-active' : ''; ?> ">Email</a>
        <a href="?page=ctclAdminPanel&tab=pending_order" class="nav-tab dashicons-before dashicons-clipboard <?=$activeTab == 'pending_order' ? 'nav-tab-active' : ''; ?> ">Pending Orders</a>
        <a href="?page=ctclAdminPanel&tab=complete_order" class="nav-tab dashicons-before dashicons-archive <?=$activeTab == 'complete_order' ? 'nav-tab-active' : ''; ?> ">Complete Orders</a>

    <?php
    if(!empty($additionalTabs )):
        foreach($additionalTabs as $key=>$value):
            $arrTab[] = $value['id'];
            $dynmTab[$value['id']] = $key;
?>
        <a href="?page=ctclAdminPanel&tab=<?=$value['id']?>" class="nav-tab dashicons-before dashicons-archive <?=$activeTab == $value['id'] ? 'nav-tab-active' : ''; ?> "><?=$value['name']?></a>
  <?php 
        endforeach;
   endif;  
    
   
    ?>
 </h4>
 </div>
 <?php
if(  in_array($activeTab,$arrTab)):
    $additionalTabs[$dynmTab[$activeTab]]['callback_func']();
else:
    switch($activeTab):

        case 'info':
        $this->infoTab();
        break;
        case 'billing':
            $this->paymentTab();
        break;
        case 'shipping':
            $this->shippingTab();
        break;
        case 'email':
            $this->emailTab();
        break;
        case 'complete_order':
            $this->completeOrderTab();
        break;
        case 'pending_order':
            $this->pendingOrderTab();
        break;
    endswitch;
endif;

}


/**
 * Create basic info tab content
 */
private function infoTab(){

    echo "this is basic info tab";
}

/**
 * Create payment tab
 */
private function paymentTab(){
$payments = apply_filters('ctcl_admin_billings_html',array());
?>
<div class="ctcl-payment-tab-content">
<fieldset class="ctcl-payment-tab-fieldset">
 <legend class="dashicons-before dashicons-admin-generic ctcl-payment-tab-fieldset-legend"><strong><?=__('Billing Settings','ctc-lite')?></strong></legend>
<?php
for($i=0;$i<count($payments);$i++):
    echo '<form method="post" action="options.php" autocomplete="on">';   
    echo "<fieldset><legend class='dashicons-before dashicons-money'><b>{$payments[$i]['formHeader']}</b></legend>";
    do_settings_sections($payments[$i]['settingFields']);
    settings_fields($payments[$i]['settingFields']);
    echo ($payments[$i]['html']);
    echo '<div class="ctcl-form-submit-button">';
    submit_button( __( 'Save', 'ctc-lite' ), 'primary','submit',false );
    echo '</div></fieldset></form>';
endfor;
?>
</fieldset>
</div>
<?php
}

/**
 * Create shipping tab
 */
private function shippingTab(){
    $shippings = apply_filters('ctcl_admin_shipping_html',array());

    ?>
<div class="ctcl-shipping-tab-content">
<fieldset class="ctcl-shipping-tab-fieldset">
 <legend class="dashicons-before dashicons-admin-generic ctcl-shipping-tab-fieldset-legend"><strong><?=__('Shipping Settings','ctc-lite')?></strong></legend>
<?php
for($i=0;$i<count($shippings );$i++):
    echo '<form method="post" action="options.php" autocomplete="on">';   
    echo "<fieldset><legend class='dashicons-before dashicons-car'><b>{$shippings[$i]['formHeader']}</b></legend>";
    do_settings_sections($shippings [$i]['settingFields']);
    settings_fields($shippings [$i]['settingFields']);
    echo ($shippings [$i]['html']);
    echo '<div class="ctcl-form-submit-button">';
    submit_button( __( 'Save', 'ctc-lite' ), 'primary','submit',false );
    echo '</div></fieldset></form>';
endfor;
?>
</fieldset>
</div>
<?php


}

/**
 * Create email tab and setting form
 */
private function emailTab(){
    $trueSelected = 'true' === get_option('ctcl_smtp_authentication') ? 'selected':'';
    $falseSelected = 'false' === get_option('ctcl_smtp_authentication') ? 'selected':'';
?>
<fieldset class="ctc-smtp-email-setting">
<legend class="dashicons-before dashicons-admin-generic ctcl-email-tab-fieldset-legend"><?=__('SMTP Email Setting','ctc-lite')?></legend>
<div class="ctcl-smtp-email-setting">
<fieldset class="ctcl-email-setting">
<legend class="dashicons-before dashicons-email-alt2"><?=__('Settings','ctc-lite')?></legend>
<form method="post" action="options.php" autocomplete="on">
<?php 
do_settings_sections('ctcl_email_settings');
settings_fields('ctcl_email_settings');
?>

<div class="ctcl-email-setting-row">
<label for="ctcl-email-subject" class="ctcl-email-subject-label" ><?=__('Email Subject :','ctc-lite')?></label>
<span><input id="ctcl-email-subject" required type="text" name='ctcl_email_subject' value="<?=get_option('ctcl_email_subject')?>"/></span>
</div>

<div class="ctcl-email-setting-row">
<label for="ctcl-smtp-host" class="ctcl-smtp-host-label" ><?=__('SMTP Host :','ctc-lite')?></label>
<span><input id="ctcl-smtp-host" required type="text" name='ctcl_smtp_host' value="<?=get_option('ctcl_smtp_host')?>"/></span>
</div>
<div class="ctcl-email-setting-row">
<label for="ctcl-smtp-authentication" class="ctcl-smtp-authentication-label" ><?=__('Use SMTP Authentication :','ctc-lite')?></label>
<span>
    <select name="ctcl_smtp_authentication">
	<option <?=$trueSelected?>  value="true">True</option>
  	<option <?=$falseSelected?>  value="false">False</option>       
</select>
</span>		         			
</div>

<div class="ctcl-email-setting-row">
<label for="ctcl-smtp-port" class="ctcl-smtp-port-label" ><?=__('SMTP Port :','ctc-lite')?></label>
<span><input id="ctcl-smtp-port" type="text" required name='ctcl_smtp_port' value="<?=get_option('ctcl_smtp_port')?>"/></span>
</div>

<div class="ctcl-email-setting-row">
<label for="ctcl-smtp-username" class="ctcl-smtp-username-label" ><?=__('Email Username :','ctc-lite')?></label>
<span><input id="ctcl-smtp-username" type="text" required name='ctcl_smtp_username' value="<?=get_option('ctcl_smtp_username')?>"/></span>
</div>


<div class="ctcl-email-setting-row">
<label for="ctcl-smtp-password" class="ctcl-smtp-password-label" ><?=__('Email Password :','ctc-lite')?></label>
<span><input id="ctcl-smtp-password" type="password" required name='ctcl_smtp_password' value="<?=get_option('ctcl_smtp_password')?>"/><span>
</div>

<div class="ctcl-email-setting-row">
<label for="ctcl-smtp-encryption" class="ctcl-smtp-encryption-label" ><?=__('SMTP Encryption :','ctc-lite')?></label>
<span><input id="ctcl-smtp-encryption" type="text" required name='ctcl_smtp_encryption' value="<?=get_option('ctcl_smtp_encryption')?>"/></span>
</div>
<div class="ctcl-email-setting-row">
<label for="ctcl-smtp-bcc-email" class="ctcl-smtp-bcc-email-label" ><?=__('BCC Email Address :','ctc-lite')?></label>
<span><input id="ctcl-smtp-bcc-email" type="email" name='ctcl_smtp_bcc_email' value="<?=get_option('ctcl_smtp_bcc_email')?>"/><span>
</div>
<div class="ctcl-email-setting-row">
<label for="ctcl-smtp-from-name" class="ctcl-smtp-from-name-label" ><?=__('From :','ctc-lite')?></label>
<span><input id="ctcl-smtp-from-name" type="text" required name='ctcl_smtp_from_name' value="<?=get_option('ctcl_smtp_from_name')?>"/></span>
</div>
<div class="ctcl-email-setting-row">
<label for="ctcl-smtp-from-email" class="ctcl-smtp-from-email-label" ><?=__('From Email :','ctc-lite')?></label>
<span><input id="ctcl-smtp-from-email" type="email" required name='ctcl_smtp_from_email' value="<?=get_option('ctcl_smtp_from_email')?>"/><span>
</div>
<div class="ctcl-email-submit-row">
<?=submit_button( __( 'Save', 'ctc-lite' ), 'primary','submit',false );?>
</div>
</form>
<div class="ctcl-test-email-row">
    <label for="ctcl-test-email" ><?=__('Enter test email :' ,'ctc-lite')?></label>
<input id="ctcl-test-email" type="email" name='ctcl_test_email' value=""/>
<a id="ctcl-send-test-email" href="Javascript:void(0)" class="ctcl-smtp-from-email-label" ><?=__('Send Email','ctc-lite')?></a>
</div>
</fieldset>


</div>
</fieldset>
<?php
}


/**
 * Create pending  order tab
 */
private function pendingOrderTab(){

    

    $ctclProcessing = new ctclProcessing();
    $pendingOrdersCount =  $ctclProcessing->getTotalPedingOrders();
    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
    $limit = 10; // number of rows in page
    $offset = ( $pagenum - 1 ) * $limit;
    $num_of_pages = ceil( $pendingOrdersCount / $limit );

    $items =  $ctclProcessing->getPendingOrderEntries($offset,$limit);
    if(0<count($items)):   
        $page_links = paginate_links( array(
            'base' => add_query_arg( 'pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __( '&laquo;', 'ctc-lite' ),
            'next_text' => __( '&raquo;', 'ctc-lite' ),
            'total' => $num_of_pages,
            'current' => $pagenum
        ) );

?>
  
  <fieldset class="ctcl-pending-orders-fieldset">
      <legend class="dashicons-before dashicons-list-view"><?=__('Pending Orders','ctc-lite')?></legend>
  <?php
    if ( $page_links ):
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
    endif;
    
?>

<table class="wp-list-table widefat fixed striped media ctcl-pending-orders">
    <thead>
        <tr>
            <td class="ctcl-pending-order-head"><?=__('Order Id','ctc-lite')?></td>
            <td class="ctcl-pending-order-head"><?=__('Order Date','ctc-lite')?></td>
            <td class="ctcl-pending-order-head"><?=__('Shipping Type','ctc-lite')?></td>
            <td class="ctcl-pending-order-head"><?=__('Payment Type','ctc-lite')?></td>
            <td class="ctcl-pending-order-head" colspan="3"><?=__('Special Instruction','ctc-lite')?></td>
            <td class="ctcl-pending-order-head"><?=__('Order Detail','ctc-lite')?></td>
</thead>
<?php

foreach ($items as $key => $value):
    $item = json_decode(stripslashes($value['orderDetail']),TRUE);
?>
    <tr id="ctcl-pending-order-<?=$item['order_id']?>" >
            <td>
                <?=$item['order_id']?>
            </td>
            <td>
                <?=date('m/d/Y',$item['order_id'])?>
            </td>
            <td>
            <?=$item['payment_type']?>
            </td>
            <td>
              <?=$item['shipping_type']?>
            </td>
            <td colspan="3">
                <p class="ctcl-pending-special-instruct"> <?=$item['checkout-special-instruction']?></p>
            </td>
            <td>
                <a href="Javascript:void(0)" class="ctcl-get-order-data" data-order-id="<?=$item['order_id']?>"><?=__('Click Here','ctc-lite')?></a>
            </td>
    </tr>
<?php
endforeach;
?>
</table>
<?php
else:
    ?>
    <p class="ctc-no-pending-order" ><?=__("There is no pending order right now.",'ctc-lite')?></p>
    <?php
endif;
}

/**
 * Create complete  order tab
 */
private function completeOrderTab(){

}



    /**
     * Adds payment options shortcode
     */
   public function paymentOptionsShortCode(){
    $paymentOptions = apply_filters( 'ctcl_payment_options', array());
    $htmlArr = array();
    $html =  '';
 
        foreach($paymentOptions as $k=>$val):
            if(1==count($paymentOptions)):
                $html .= '<div class="ctcl_payment_option_row">';
                $html .= "<p style='display:none;'><input required class='ctcl-payment-option' data-name='{$val['name']}' type='radio' checked id='{$val['id']}' name='payment_option' value='{$val['id']}'/></p>";
                $html .= "<label for='{$val['id']}' class=''ctcl_payment_option_label >{$val['name']}</label></div>";
            else:    
                $html .= '<div class="ctcl_payment_option_row">';
                $html .= "<input required class='ctcl-payment-option' data-name='{$val['name']}' type='radio' id='{$val['id']}' name='payment_option' value='{$val['id']}'/>";
                $html .= "<label for='{$val['id']}' class=''ctcl_payment_option_label >{$val['name']}</label></div>";
            endif;
            array_push($htmlArr,array('id'=>$val['id'],'html'=>$val['html']));
        endforeach;


    foreach($htmlArr as $ky =>$value):
       if(!empty($value['html']) || null != $value['html']):
        $html .="<div id='{$value['id']}_container' class='ctcl_payment_container' >{$value['html']}</div>";
       endif;
    endforeach;
    $html .= '<input id="ctcl-payment-type" type="hidden" name="payment_type" value=""/>';
    return $html;
   }

   /**
    * Shipping options shortcode
    */
    public function shippingOptionsShortCode(){

        $shippingOptions = apply_filters('ctcl_shipping_option_display',array());
        $html = '';
        foreach($shippingOptions as $key=>$val):

            if(1==count($shippingOptions)):
                $html .= '<div class="ctcl-shipping-option-row">';
                $html .= "<p style='display:none;'><input required class='ctcl-shipping-option' data-name='{$val['name']}' type='radio' checked id='{$val['id']}' name='shipping_option' value='{$val['id']}'/></p>";
                $html .= "<label for='{$val['id']}' class=''ctcl_payment_option_label > {$val['name']}</label></div>";
                $shippingInput = "<input id='ctcl-shipping-type' type='hidden' name='shipping_type' value='{$val['name']}'/>";
            else:    
                $html .= '<div class="ctcl-shipping-option-row">';
                $html .= "<input required class='ctcl-shipping-option' data-name='{$val['name']}' type='radio' id='{$val['id']}' name='shipping_option' value='{$val['id']}'/>";
                $html .= "<label for='{$val['id']}' class=''ctcl_shipping_option_label > {$val['name']}</label></div>";
                $shippingInput = '<input id="ctcl-shipping-type" type="hidden" name="shipping_type" value=""/>';
            endif;
        endforeach;
      $html .= $shippingInput ;
return $html;
    }

    /**
     * Construct email body
     * 
     * @param $data Order data
     * @return $body body of email to be sent to customer
     */
    public function  createEmailBody($data){
        $body = '<div>';
        $body .= '<p>'.__('Hello','ctc-lite').', '.$data['ctcl-co-name'].'</p>';
        $body .= '<p>'.__('Thank you for your purchase, your purchase details are as follows :','ctc-lite').'</p>';
        $body .= '<p>'.__('Order id ').' :'.$data['order_id'].'</p>';
        $body .= '<p>'.__('You order details :','ctc-lite').'</p>';
        $body .= '<div style="margin-left:auto;margin-right:auto;display:block;"><table style="border=1px solid black;border-collapse:collapse;"><thead';
        $body .= '<tr style="height:40px;"><th style="height:40px;text-align:center;border: 1px solid black;border-collapse: collapse;width:300px;" colspan="11">'.__('Products','ctc-lite').'</th><th style="height:40px;text-align:center;border: 1px solid black;border-collapse: collapse;width:50px;" colspan="2">'.__('Qty').'</th><th style="height:40px;text-align:center;border: 1px solid black;border-collapse: collapse;width:100px;" colspan="2">'.__('Item Total').'</th></tr>';
        $body .= '<thead><tbody>';
        foreach($data['products'] as $key=>$value):
            $product = json_decode(stripslashes($value),TRUE);
            $body .= "<tr style='height:30px;'><td  style='text-align:center;border: 1px solid black;border-collapse:collapse;' colspan='11'>{$product['itemName']}</td><td style='text-align:center;border: 1px solid black;border-collapse: collapse;' colspan='2'>{$product['quantity']}</td><td style='text-align:center;border: 1px solid black;border-collapse: collapse;' colspan='2'>{$product['itemTotal']}</td></tr>";
        endforeach;
        $body .='<tr style="height:40px;"><td style="text-align:right;border: 1px solid black;border-collapse:collapse;" colspan="13">'.__('Tax Rate ','ctc-lite').'</td><td style="text-align:center;border: 1px solid black;border-collapse: collapse;"colspan="4" >'.get_option('ctcl_tax_rate').' % </td></tr>';
        $body .='<tr style="height:40px;"><td style="text-align:right;border: 1px solid black;border-collapse:collapse;" colspan="13">'.__('Shipping Cost ','ctc-lite').' ('.get_option('ctcl_currency').') </td><td style="text-align:center;border: 1px solid black;border-collapse: collapse;" colspan="4" >'.$data['shipping-total'].'</td></tr>';
        $body .='<tr style="height:40px;"><td style="text-align:right;border: 1px solid black;border-collapse:collapse;" colspan="13">'.__('Sub Total ','ctc-lite').' ('.get_option('ctcl_currency').') </td><td style="text-align:center;border: 1px solid black;border-collapse: collapse;" colspan="4" >'.$data['sub-total'].'</td></tr>';
        $body .='</tbody></table></div>';
        $body .= "<div style='font-size:15px;margin-top:15px;'><p>".__('Shipping Note :','ctc-lite')."</p>{$data['shipping_note']}<div>";
        $body .='</div>';
        return $body;
    }

    /**
     * Get order detail 
     * 
     * @param $id order id
     */
    public function getPendingOrderDetail(){

        $ctclProcessing =  new ctclProcessing();

        print_r($_POST);
        //print_r ( $ctclProcessing->getOrderDetail($_POST['orderId']));
  
        wp_die();
    }

}