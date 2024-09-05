<?php
class ctclHtml{


/**
 * @since 1.0.0
 *
 * display admin panel content
 */
public function adminPanelHtml(){

    $additionalTabs = apply_filters('ctcl_additional_tab',array());

    $activeTab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field($_GET[ 'tab' ]) : 'info';
    $dynmTab = array();
    $arrTab=array();
?>
<div class="ctclAdminPanel">	               
<h2><span class="dashicons-before dashicons-store"></span><?=__('CTC Lite Admin Panel','ctc-lite')?></h2>
    <h4 class="nav-tab-wrapper">
        <a href="?page=ctclAdminPanel&tab=info" class="nav-tab dashicons-before dashicons-info <?=$activeTab == 'info' ? 'nav-tab-active' : ''; ?> "><?=__('Information','ctc-lite')?></a>
        <a href="?page=ctclAdminPanel&tab=billing" class="nav-tab dashicons-before dashicons-money <?=$activeTab == 'billing' ? 'nav-tab-active' : ''; ?> "><?=__('Billing','ctc-lite')?></a>
        <a href="?page=ctclAdminPanel&tab=shipping" class="nav-tab dashicons-before dashicons-car <?=$activeTab == 'shipping' ? 'nav-tab-active' : ''; ?> "><?=__('Shipping','ctc-lite')?></a>
        <a href="?page=ctclAdminPanel&tab=email" class="nav-tab dashicons-before dashicons-email-alt2 <?=$activeTab == 'email' ? 'nav-tab-active' : ''; ?> "><?=__('Email','ctc-lite')?></a>
        <a href="?page=ctclAdminPanel&tab=pending_order" class="nav-tab dashicons-before dashicons-clipboard <?=$activeTab == 'pending_order' ? 'nav-tab-active' : ''; ?> "><?=__('Pending Orders','ctc-lite')?></a>
        <a href="?page=ctclAdminPanel&tab=complete_order" class="nav-tab dashicons-before dashicons-archive <?=$activeTab == 'complete_order' ? 'nav-tab-active' : ''; ?> "><?=__('Complete Orders','ctc-lite')?></a>

    <?php
    if(!empty($additionalTabs )):
        foreach($additionalTabs as $key=>$value):
            $arrTab[] = $value['id'];
            $dynmTab[$value['id']] = $key;
            $navIcon = !empty($value['icon'])?$value['icon']:'admin-generic ';
?>
        <a href="?page=ctclAdminPanel&tab=<?=$value['id']?>" class="nav-tab dashicons-before dashicons-<?=$navIcon?>  <?=$activeTab == $value['id'] ? 'nav-tab-active' : ''; ?> "><?=$value['name']?></a>
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
 * @since 1.0.0
 *
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
* @since 1.0.0
*
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
 * @since 1.0.0
 *
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
 * @since 1.0.0
 *
 * Create pending  order tab
 */
private function pendingOrderTab(){


    $ctclProcessing = new ctclProcessing();
    $pendingOrdersCount =  $ctclProcessing->getTotalPendingOrders();
    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
    $limit = 10; 
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
            <td class="ctcl-pending-order-head"><?=__('Payment Type','ctc-lite')?></td>
            <td class="ctcl-pending-order-head"><?=__('Shipping Type','ctc-lite')?></td>
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
                <p class="ctcl-pending-special-instruct"> <?=str_replace('u2019',"'",$item['checkout-special-instruction'])?></p>
            </td>
            <td>
                <a href="Javascript:void(0)" class="ctcl-get-pending-order-data" data-order-id="<?=$item['order_id']?>"><?=__('Click Here','ctc-lite')?></a>
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
 * @since 1.0.0
 *
 * Create complete  order tab
 */
private function completeOrderTab(){

    $ctclProcessing = new ctclProcessing();
    $pendingOrdersCount =  $ctclProcessing->getTotalCompleteOrders();
    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
    $limit = 10; 
    $offset = ( $pagenum - 1 ) * $limit;
    $num_of_pages = ceil( $pendingOrdersCount / $limit );

    $items =  $ctclProcessing->getCompleteOrderEntries($offset,$limit);
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
  
  <fieldset class="ctcl-complete-orders-fieldset">
      <legend class="dashicons-before dashicons-list-view"><?=__('Complete Orders','ctc-lite')?></legend>
  <?php
    if ( $page_links ):
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
    endif;
    
?>

<table class="wp-list-table widefat fixed striped media ctcl-complete-orders">
    <thead>
        <tr>
            <td class="ctcl-complete-order-head"><?=__('Order Id','ctc-lite')?></td>
            <td class="ctcl-complete-order-head"><?=__('Order Date','ctc-lite')?></td>
            <td class="ctcl-complete-order-head"><?=__('Payment Type','ctc-lite')?></td>
            <td class="ctcl-complete-order-head"><?=__('Shipping Type','ctc-lite')?></td>
            <td class="ctcl-complete-order-head" colspan="3"><?=__('Special Instruction','ctc-lite')?></td>
            <td class="ctcl-complete-order-head"><?=__('Order Detail','ctc-lite')?></td>
</thead>
<?php

foreach ($items as $key => $value):

    $item = json_decode(stripslashes($value['orderDetail']),TRUE);
?>
    <tr id="ctcl-complete-order-<?=$item['order_id']?>" >
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
                <p class="ctcl-pending-special-instruct"> <?=str_replace('u2019',"'",$item['checkout-special-instruction'])?></p>
            </td>
            <td>
                <a href="Javascript:void(0)" class="ctcl-get-complete-order-data" data-order-id="<?=$item['order_id']?>"><?=__('Click Here','ctc-lite')?></a>
            </td>
    </tr>
<?php
endforeach;
?>
</table>
<?php
else:
    ?>
    <p class="ctc-no-complete-order" ><?=__("There is no complete order right now.",'ctc-lite')?></p>
    <?php
endif;

}



    /**
     * @since 1.0.0
     *
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
    * @since 1.0.0
    *
    * Shipping options shortcode
    */
    public function shippingOptionsShortCode(){

        $shippingOptions = apply_filters('ctcl_shipping_option_display',array());
        $html = '';
        $shippingInput ='';
        foreach($shippingOptions as $key=>$val):

            if(1==count($shippingOptions)):
                $html .= '<div class="ctcl-shipping-option-row">';
                $html .= "<p style='display:none;'><input required class='ctcl-shipping-option' data-name='{$val['name']}' type='radio' checked id='{$val['id']}' name='shipping_option' value='{$val['id']}'/></p>";
                $html .= "<label for='{$val['id']}' class=''ctcl_payment_option_label > {$val['name']}</label></div>";
                $shippingInput .= "<input id='ctcl-shipping-type' type='hidden' name='shipping_type' value='{$val['name']}'/>";
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
     * @since 1.0.0
     *
     * Construct email body
     * 
     * @param $data Order data
     * @return $body body of email to be sent to customer
     */
    public function  createEmailBody($data){

        $body = '<div style="margin-left:auto;margin-right:auto;display:block;padding:20px;">';
        $body .= '<p>'.__('Hello','ctc-lite').', '.$data['ctcl-co-first-name'].'</p>';
        $body .= '<p>'.__('Thank you for your purchase, your purchase details are as follows :','ctc-lite').'</p>';
        $body .= '<p>'.__('Order id ').' :'.$data['order_id'].'</p>';
        $body .= '<p>'.__('Your order details :','ctc-lite').'</p>';
        $body .= '<div style="width:600px;">';
        $body .= '<div style="padding-top:10px;border-bottom:1px solid rgba(255,255,255,1);display:table;height:30px;background-color:rgba(0,0,0,0.1);width:600px;text-align:center;">';
        $body .= '<span style="display:table-cell;height:30px;width:200px;" >'.__('Products','ctc-lite').'</span>';
        $body .= '<span style="display:table-cell;height:30px;width:300px;" >'.__('Variation','ctc-lite').'</span>';
        $body .='<span style="display:table-cell;height:30px;width:50px;" >'.__('Qty').'</span>';
        $body .='<span style="display:table-cell;height:30px;width:100px;" >'.__('Item Total').'</span></div>';
    
        foreach($data['products'] as $key=>$value):
            $product = json_decode(stripslashes($value),TRUE);
            $body .= "<div style='border-bottom:1px solid rgba(255,255,255,1);display:table;height:30px;background-color:rgba(0,0,0,0.1);width:600px;text-align:center;padding-top:10px;'>";
            $body .="<span  style='display:table-cell;width:200px'>{$product['itemName']}</span>";
            $body .="<span  style='display:table-cell;width:300px'>{$product['vari']}</span>";
            $body.="<span style='display:table-cell;width:50px;'>{$product['quantity']}</span>";
            $body .="<span style='display:table-cell;width:100px;' >{$product['itemTotal']}</span></div>";
        endforeach;
        $body .='<div style="padding-top:10px;border-bottom:1px solid rgba(255,255,255,1);height:30px;display:table;background-color:rgba(0,0,0,0.1);width:600px;text-align:center;">';
        $body .='<span style="display:table-cell;width:500px;text-align:right;" >'.__('Tax Rate ','ctc-lite').' : </span>';
        $body.='<span style="display:table-cell;">'.get_option('ctcl_tax_rate').' % </span></div>';
        $body .='<div style="padding-top:10px;border-bottom:1px solid rgba(255,255,255,1);height:30px;display:table;background-color:rgba(0,0,0,0.1);width:600px;text-align:center;">';
        $body .='<span style="display:table-cell;width:500px;text-align:right;">'.__('Shipping Cost ','ctc-lite').' ('.get_option('ctcl_currency').') : </span>';
        $body .= '<span style="display:table-cell;" >'.$data['shipping-total'].'</span></div>';
       

       if(isset( $data['total-discount'])) :
        $body .='<div style="padding-top:10px;border-bottom:1px solid rgba(255,255,255,1);height:30px;display:table;background-color:rgba(0,0,0,0.1);width:600px;text-align:center;">';
        $body.='<span style="display:table-cell;width:500px;text-align:right;" >'.__('Discount Total ','ctc-lite').' ('.get_option('ctcl_currency').') : </span>';
        $body.='<span style="display:table-cell;" >'.$data['total-discount'].'</span></div>';
        $body .='</div>';
       endif;
       
        $body .='<div style="padding-top:10px;border-bottom:1px solid rgba(255,255,255,1);height:30px;display:table;background-color:rgba(0,0,0,0.1);width:600px;text-align:center;">';
        $body.='<span style="display:table-cell;width:500px;text-align:right;" >'.__('Total ','ctc-lite').' ('.get_option('ctcl_currency').') : </span>';
        $body.='<span style="display:table-cell;" >'.$data['sub-total'].'</span></div>';
        $body .='</div>';
        if(isset($data['shipping_note'])):
        $body .= "<div style='font-size:15px;margin-top:15px;'><p>".__('Shipping Note :','ctc-lite')."</p>{$data['shipping_note']}</div>";
        endif;
        $body .='</div>';
        return $body;
    }

    /**
     * @since 1.0.0
     *
     * Get pending order detail 
     * 
     * 
     */
    public function getPendingOrderDetail(){

        $ctclProcessing =  new ctclProcessing();
        $orderId = sanitize_text_field( $_POST['orderId']);
        $detail =  json_decode(stripslashes($ctclProcessing->getOrderDetail($orderId)),TRUE);
        echo '<fieldset class="ctcl-order-detail-main-cont">';
        echo "<legend class='dashicons-before dashicons-clipboard ctcl-order-detail-main-cont-legend'> ".__("Order Detail for ")." {$orderId}</legend>";
        echo '<div class="ctc-order-detail-cont">';

        echo "<div class='pending-order-modal-action'>";
        submit_button( __( 'Cancel Order', 'ctc-lite' ), 'primary ctcl-detail-cancel-order','submit',false);
        submit_button( __( 'Mark Complete', 'ctc-lite' ), 'primary ctcl-detail-mark-complete','submit',false);
        echo '</div>';
      
       echo "<div class='ctcl-pending-order-detail'>";
       echo "<input id='ctcl-order-id' type='hidden' value='".$detail['order_id']."'/>";
       $this ->createOrderListSection($detail);
       $this->createCustomerInfoSection($detail);
       $this->createVendorNoteSection($ctclProcessing,$detail['order_id']);
       $this->createShippingSection($detail['order_id']);
       echo "</div>";
      
       echo "</div>";
       echo "</fieldset>";
        wp_die();
    }


    /**
     * @since 1.0.0
     *
     * Get complete detail 
     * 
     */
    public function completeOrderDetail(){

        $ctclProcessing =  new ctclProcessing();

        $orderId = sanitize_text_field( $_POST['orderId']);

        $detail =  json_decode(stripslashes($ctclProcessing->getOrderDetail($orderId )),TRUE);
        echo '<fieldset class="ctcl-order-detail-main-cont">';
        echo "<legend class='dashicons-before dashicons-clipboard ctcl-order-detail-main-cont-legend'> ".__("Order Detail for")." {$orderId}</legend>";
        echo '<div class="ctc-order-detail-cont">';

        echo "<div class='pending-order-modal-action'>";
        submit_button( __( 'Refund', 'ctc-lite' ), 'primary ctcl-detail-refund-order','submit',false);
        echo '</div>';

       echo "<div class='ctcl-complete-order-detail'>";
       echo "<input id='ctcl-order-id' type='hidden' value='".$detail['order_id']."'/>";
       $this ->createOrderListSection($detail);
       $this->createCustomerInfoSection($detail);
       $this->createVendorNoteSection($ctclProcessing,$detail['order_id']);
       $this->createShippingSection($detail['order_id']);
       echo "</div>";
       echo "</div>";
       echo "</fieldset>";
        wp_die();
    }

    /**
     * @since 1.0.0
     *
     * create order list
     */
    private function createOrderListSection($detail){

        echo "<fieldset class='ctcl-order-list'>";
        echo "<legend class='dashicons-before dashicons-list-view ctcl-orderdetail-legend'>".__('Items Details')."</legend>";
        echo '<div id="ctcl-orderlist">';
        echo "<div class='ctcl-order-list-header'>";
        echo "<span class='ctcl-order-list-header-name'>".__('Products','ctc-lite')."</span>";
        echo "<span class='ctcl-order-list-header-var'>".__('Variation','ctc-lite')."</span>";
        echo "<span class='ctcl-order-list-header-qty'>".__('Qty','ctc-lite')."</span>";
        echo "<span class='ctcl-order-list-header-total'>".__('Item Total','ctc-lite')."</span></div>";
        
        foreach($detail['products']as $key=>$order):
        $item = json_decode($order,TRUE);
        echo "<div class='ctcl-ordered-item'>";
        echo "<span class='ctcl-ordered-item-name'>{$item['itemName']}</span>";
        echo "<span class='ctcl-ordered-item-var' >{$item['vari']}</span>";
        echo "<span class='ctcl-ordered-item-qty'>{$item['quantity']}</span>";
        echo "<span class='ctcl-ordered-item-total'>{$item['itemTotal']}</span>";
        echo '</div>';
       endforeach;
       echo '<hr>';

       echo "<div class='ctcl-order-detail-total'><span>".__("Sub Total", "ctc-lite" )." : </span><span>{$detail['items-total']}</span></div>";
       echo "<div class='ctcl-order-detail-tax'><span>".__("Sales Tax", "ctc-lite" )." : </span><span>{$detail['tax-total']}</span></div>";
       echo "<div class='ctcl-order-detail-shipping'><span>".__("Shipping Total", "ctc-lite" )." : </span><span>{$detail['shipping-total']}</span></div>";
       if(isset($detail['total-discount'])):
        echo "<div class='ctcl-order-detail-discount'><span>".__("Discount", "ctc-lite" )." : </span><span>".number_format($detail['total-discount'],2)."</span></div>";
       endif;
       echo "<div class='ctcl-order-detail-sub-total'><span>".__("Sub Total","ctc-lite" )." :</span><span>{$detail['sub-total']}</span></div>";
       echo "</div>";
       echo "<div><a href='Javascript:void(0);' title='".__("Print list","ctc-lite")."' class='dashicons-before dashicons-printer' id='ctcl-print-order-list'></a></div>";
       echo "</fieldset>";

    }

    /**
     * @since 1.0.0
     *
     * Create customer info section
     */
    private function createCustomerInfoSection($detail){

       echo '<fieldset class="ctc-pending-customer-info-fieldset">';
       echo "<legend class='dashicons-before dashicons-admin-users ctcl-cust-info-legend'>".__("Customer Info","ctc-lite")."</legend>";
       echo '<div id="ctc-pending-customer-info">';
       echo "<address>";
       echo "<div class='ctcl-pending-cust-info-name' ><label>".__("Customer Name",'ctc-lite')." : </label><span>{$detail['ctcl-co-first-name']} {$detail['ctcl-co-last-name']}</span></div>";
       echo "<div class='ctcl-pending-cust-info-street-add1' ><label>".__("Street Address 1",'ctc-lite')." : </label><span>{$detail['checkout-street-address-1']}</span><div>";
       echo "<div class='ctcl-pending-cust-info-street-add2' ><label>".__("Street Address 2",'ctc-lite')." : </label><span>{$detail['checkout-street-address-2']}</span><div>";
       echo "<div class='ctcl-pending-cust-info-city' ><label>".__("City","ctc-lite")." : </label><span>{$detail['checkout-city']}</span><div>";
       echo "<div class='ctcl-pending-cust-info-state' ><label>".__("State","ctc-lite")." : </label><span>{$detail['checkout-state']}</span><div>";
       echo "<div class='ctcl-pending-cust-info-zip-code' ><label>".__("Zip Code","ctc-lite")." : </label><span>{$detail['checkout-zip-code']}</span><div>";
       echo "<div class='ctcl-pending-cust-info-country' ><label>".__("Country","ctc-lite")." : </label><span>{$detail['checkout-country']}</span><div>";
       echo "</address>";
       echo "<div class='ctcl-pending-cust-info-shhiping-type'><label>".__("Shipping Type",'ctc-lite')." : </label><span class='ctcl-pending-cust-info-shhiping-type-text' >{$detail['shipping_type']}</span></div>";
       echo "<div class='ctcl-pending-cust-info-special-instruct'><label>".__("Special Instruction",'ctc-lite')." : </label><span class='ctcl-cust-info-instruction-text' >{$detail['checkout-special-instruction']}</span></div>";
       echo "</div>";
       echo "<div><a href='Javascript:void(0);' title='".__("Print Customer Info","ctc-lite")."' class='dashicons-before dashicons-printer' id='ctcl-print-cust-info'></a></div>";
       echo "</fieldset>";
    }

    /**
     * @since 1.0.0
     *
     * create final note section of modal
     * 
     * @param $ctclProcessing CTCL Porcessing object
     * @orderId $orderId Order Id
     */
    private function createVendorNoteSection($ctclProcessing,$orderId){

        $note = $ctclProcessing->getVendorNote($orderId);
        echo "<fieldset class='ctcl-vendor-note'>";
        echo "<legend class='dashicons-before dashicons-clipboard ctcl-vendor-note-legend'>".__("Order Status  Note" ,"ctc-lite")." : </legend>";    
        echo "<textarea id='ctcl-order-status-note' rows='5' cols='60' name='ctcl-order-status-note' value=''>{$note}";
        echo"</textarea>";
        echo submit_button( __( 'Save', 'ctc-lite' ), 'primary ctcl-vendor-note-submit','submit',false );
        echo "</fieldset>";
    }

    /**
     * @since 1.0.0
     *
     * create shipping section
     */
    private function createShippingSection($orderId){
        $shippingOptions = apply_filters('ctcl_get_shipping_option',array(),$orderId);

        if(!empty($shippingOptions )):
            echo "<fieldset class='ctcl-shipping-options'>";
            echo "<legend class='dashicons-before dashicons-car ctcl-shipping-options-legend'>".__("Shipping Options",'ctc-lite')."</legend>";
            echo "<div>";
            echo "<p>".__('Available Options','ctc-lite')."</p>";
            echo "<ul>";
            foreach($shippingOptions as $key=>$value):
                echo "<li>{$value}</li>";
            endforeach;
            echo "</ul>";
            echo "</div>";
            echo "</filedset>";
        endif;

    }

/**
 * @since 1.0.0
 *
 * Create basic info tab content
 */
private function infoTab(){

    
    $infoTabHtml = array();

    array_push( $infoTabHtml,
      array(
        'title'=> __('Setup Info','ctc-lite'),
        'icon'=> 'dashicons-menu-alt3',
        'html'=> $this->setupInfo(),
    )
      );


    // Filter call.
$value = apply_filters( 'ctcl-info-tab-sub-tab', $infoTabHtml );

$reversed = array_reverse($value);

$activeSubTab = isset( $_GET[ 'sub_tab' ] ) ? $_GET[ 'sub_tab' ] : '0';

echo '<h3 class=" dashicons  dashicons-editor-insertmore">'.__('Information','ctc-lite').'</h3>';
echo '<h3 class="nav-tab-wrapper ctcSubNavTab">';
foreach($reversed as $key=>$val):
    
    $icon = isset($val['icon']) ? $val['icon'] : 'dashicons-info-outline' ;
?>

 <a href="?page=ctclAdminPanel&tab=info&sub_tab=<?=$key?>" class="nav-tab <?php echo $activeSubTab == $key ? 'nav-tab-active' : ''; ?> "><span class="dashicons <?=$icon?>"></span><?=$val['title']?></a>
 <?php  
endforeach;
echo '</h3>';

echo $reversed[$activeSubTab]['html'];
    
}

/**
 * @since 2.1.3
 * 
 * Info tab setupinfo
 */

 public function setupInfo(){
ob_start();
?>
<div class="ctcl-info-tab-main">
<h3 class=" dashicons-before dashicons-editor-help ctcl-basic-info-header"><?=__("How to operate plugin ?",'ctc-lite')?></h3>
<div class="ctcl-info-tab">



<fieldset class="ctcl-misc-setting">
<legend class="dashicons-before dashicons-admin-generic ctcl-misc-setting-legend"><?=__(' 1) Important settings','ctc-lite')?></legend>
    <ol>
    <li><?=__('Go to Billings tab and setup payment information','ctc-lite')?></li>
    <li><?=__('Go to Shipping tab and setup shipping Setting','ctc-lite')?></li>
    <li><?=__('Go to Email tab and setup email Setting to send order confirmation email.','ctc-lite')?></li>
    <br/>
    </ol>
</fieldset>


<fieldset class="ctcl-setup-product">
    <legend class="dashicons-before dashicons-products ctcl-setup-product-legend"><?=__(' 2) Set up product page','ctc-lite')?></legend>
    <ol>
        <li><?=__('Create a page','ctc-lite')?></li>
        <li><?=__('Add CTC Lite Product block to the Page.','ctc-lite')?></li>
        <li><?=__('Click Add Product Detail Button','ctc-lite')?> </li>
        <li><?=__('Fill up the required fields like name , price etc.','ctc-lite')?></li>
        <li><?=__('Save and Publish the page and you are done with creating product. Repeat for as many products','ctc-lite')?></li>
    </ol>
</fieldset>

<fieldset class="ctcl-setup-processing-page">
<legend class="dashicons-before dashicons-cart ctcl-setup-processing-page-legend"><?=__(' 3) Set up order processing page (Important)','ctc-lite')?></legend>
    <ol>
        <li><?=__('Create a page','ctc-lite')?></li>
        <li><?=__('Add CTC Lite Order Processing block to page.','ctc-lite')?></li>
        <li><?=__('Publish the page and you are done creating processing page.','ctc-lite')?></li>
        <li><?=__('This page do not need to be on menu, you can keep it hidden but accessible.','ctc-lite')?></li>
        <br/>
    </ol>
</fieldset>


<fieldset class="ctcl-setup-check-out-page">
<legend class=" dashicons-before dashicons-money ctcl-setup-check-out-page-legend"><?=__(' 4) Set up checkout page','ctc-lite')?></legend>
    <ol>
        <li><?=__('Create a page','ctc-lite')?></li>
        <li><?=__('Add CTC Lite Cart block to page.','ctc-lite')?></li>
        <li><?=__('Click Add Checkout Detail button','ctc-lite')?></li>
        <li><?=__('Get the Url of order processing page from step 3) and paste it to url field (Important).','ctc-lite')?></li>
        <li><?=__('Save and Publish the page and you are done creating checkout page.','ctc-lite')?></li>
    </ol>
</fieldset>



<fieldset class="ctcl-pending-order-info">
<legend class="dashicons-before dashicons-clipboard ctcl-pending-order-info-legend"><?=__(' 5) Pending order tab','ctc-lite')?></legend>
    <ol>
    <li><?=__('Go to Pending order tab','ctc-lite')?></li>
    <li><?=__('Click detail link','ctc-lite')?></li>
    <li><?=__('View Order detail, print order list or customer info.','ctc-lite')?></li>
    <li><?=__('Or add vendor note to the order','ctc-lite')?></li>
    <li><?=__('Complete or Cancel order','ctc-lite')?></li>
    </ol>
</fieldset>

<fieldset class="ctcl-complete-order-info">
<legend class="dashicons-before dashicons-archive ctcl-complete-order-info-legend"><?=__(' 6) Complete order tab','ctc-lite')?></legend>
    <ol>
    <li><?=__('Go to Complete order tab','ctc-lite')?></li>
    <li><?=__('Click detail link','ctc-lite')?></li>
    <li><?=__('View Order detail, print order list or customer info.','ctc-lite')?></li>
    <li><?=__('Or edit or view vendor note of the order','ctc-lite')?></li>
    
    </ol>
</fieldset>



<fieldset class="ctcl-complete-other-info">
<legend class="dashicons-before dashicons-info ctcl-complete-order-info-legend"><?=__('Addons','ctc-lite')?></legend>
    <ol>
    <li><b><?=__(' If this plugin has helped you consider buying following paid addons which will support its future development:','ctc-lite')?></b></li>
           <ol>
            <li > <a    href="https://payhip.com/b/bdiOx" target="_blank" >📲  <?=__('CTCL SMS',"ctl-lite")?></a><i><?=__( ' (Get of order notification with in your cell phone)','ctc-lite' );?></i></li>
            <li > <a  href="https://payhip.com/b/8mFPg" target="_blank" >💰 <?=__('CTCL Paypal',"ctl-lite")?></a><i><?=__( ' (Accept payment with Paypal)','ctc-lite' );?></i></li>
            <li > <a  href="https://payhip.com/b/uZ4KU" target="_blank" > 🚚 <?=__('CTCL Custom Shipping',"ctl-lite")?></a><i><?=__( ' (Display Custom Shipping option to Customer)','ctc-lite' );?></i></li>
            <li > <a  href="https://payhip.com/b/qr8fb" target="_blank" > 🎨  <?=__('CTCL Variation Swatches',"ctl-lite")?></a><i><?=__( ' (Display swatches instead of dropdown select for product variations)','ctc-lite' );?></i></li>   
            <li > <a  href="https://payhip.com/b/3PKa7" target="_blank" >👍 <?=__('CTCL Rting and Review','ctc-lite' );?></i></li>   
        </ol>
</li>   
<li><?=__('Plugin functionalities can be extended with following free Addons ','ctc-lite')?><a target="_blank" href="https://wordpress.org/plugins/tags/ctc-lite/"> <?=__('Addon on WordPress','ctc-lite')?> </a> - <a target="_blank" href="https://github.com/topics/ctc-lite"> <?=__('Addons on Github','ctc-lite') ?></a></li> 
 
    </ol>
</fieldset>



<fieldset class="ctcl-complete-other-info">
<legend class="dashicons-before dashicons-info ctcl-complete-order-info-legend"><?=__('Other info','ctc-lite')?></legend>
    <ol>
    <li><?=__('Follow CT Commerce Lite\'s Facebook page for future updates.','ctc-lite')?><a target="_blank" href="https://www.facebook.com/profile.php?id=61564422399518"> <br/><?=__('Click Here','ctc-lite')?> </a></li>
    <li><?=__('If you wish plugin developer to set up store for you for reasonable price, contact  ','ctc-lite')?><a href="mailto:bktujwol@gmail.com"><?=__('Email','ctc-lite')?> </a> </li>
    <li><?=__('Plugin works with most of the free/paid themes with  some  CSS modification','ctc-lite')?></li>
    <li><?=__('However it works best with Astra Theme ','ctc-lite')?><a target="_blank" href="https://wpastra.com/?bsf=6459"> <?=__('Click Here','ctc-lite')?> </a></li>
    </ol>
</fieldset>


</div>
</div>

<?php
return ob_get_clean();
 }






}