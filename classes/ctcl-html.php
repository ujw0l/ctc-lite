<?php
class ctclHtml{


/**
 * display admin panel content
 */
public function adminPanelHtml(){

    $additionalTabs = apply_filters('ctcl-additional_tab',array());
    $activeTab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'info';
    $dynmTab = array();
?>
<div class="ctclAdminPanel">	               
<h2><span class="dashicons-before dashicons-store"></span>CTC Lite Admin Panel</h2>
    <h4 class="nav-tab-wrapper">
        <a href="?page=ctclAdminPanel&tab=info" class="nav-tab dashicons-before dashicons-info <?=$activeTab == 'info' ? 'nav-tab-active' : ''; ?> ">Information</a>
        <a href="?page=ctclAdminPanel&tab=payment" class="nav-tab dashicons-before dashicons-money <?=$activeTab == 'payment' ? 'nav-tab-active' : ''; ?> ">Payment</a>
        <a href="?page=ctclAdminPanel&tab=shipping" class="nav-tab dashicons-before dashicons-cart <?=$activeTab == 'shipping' ? 'nav-tab-active' : ''; ?> ">Shipping</a>
        <a href="?page=ctclAdminPanel&tab=email" class="nav-tab dashicons-before dashicons-email-alt2 <?=$activeTab == 'email' ? 'nav-tab-active' : ''; ?> ">Email</a>
        <a href="?page=ctclAdminPanel&tab=complete_order" class="nav-tab dashicons-before dashicons-clipboard <?=$activeTab == 'pending_order' ? 'nav-tab-active' : ''; ?> ">Pending Orders</a>
        <a href="?page=ctclAdminPanel&tab=pending_order" class="nav-tab dashicons-before dashicons-archive <?=$activeTab == 'complete_order' ? 'nav-tab-active' : ''; ?> ">Complete Orders</a>

    <?php
    if(!empty($additionalTabs )):
        foreach($additionalTabs as $key=>$value):
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

if(  in_array($activeTab,$dynmTab)):
    $additionalTabs[$dynmTab[$activeTab]]['callbac_func']();
else:
    switch($activeTab):

        case 'info':
        $this->infoTab();
        break;
        case 'payment':
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
$payments = apply_filters('ctcl_admin_payment_html',array());
?>
<div class="ctcl-payment-tab-content">

 
<?php
for($i=0;$i<count($payments);$i++):
    echo '<form method="post" action="options.php" autocomplete="on">';   
    echo "<fieldset><legend>{$payments[$i]['formHeader']}</legend>";
    do_settings_sections($payments[$i]['formSetting']);
    settings_fields($payments[$i]['formSetting']);
    echo ($payments[$i]['html']);
    submit_button( __( 'Submitt', 'ctc-lite' ), 'primary' );
    echo '</fieldset></form>';
endfor;
?>
</div>
<?php
}

/**
 * Create shipping tab
 */
private function shippingTab(){

}

/**
 * Create email tab
 */
private function emailTab(){

}


/**
 * Create pending  order tab
 */
private function pendingOrderTab(){

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
    $shippingOptions = apply_filters( 'ctcl_shipping_options', array());


$html =  '';
    for($i=0;$i<=count($shippingOptions)-1;$i++):
        $html .= "Value is {$shippingOptions[$i]}";
    endfor;

    return $html;
   }

}