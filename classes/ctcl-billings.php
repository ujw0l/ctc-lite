<?php

class ctclBillings{
    /**
     * payment id
     */
public $paymentId = 'ctcl_cash';

/**
 * payment name
 */
public $paymentName = 'Cash';


public function __construct(){
  
    self::registerOptions();
    self::displayOptionsUser();
    self::adminPanelHtml();
   
}

/**
* create radio buttons
*/
    public function displayOptionsUser(){
        if(1 == get_option('ctcl_cash_on_delivery')): 
            add_filter('ctcl_payment_options',function($val){
                

                $paymentOption = array('payment-id'=>$this->paymentId,
                                        'payment-name'=>$this->paymentName,
                                        'option-html'=>$this->frontendHtml
                                    );

                
                array_push($val, $paymentOption);
                return  $val;
            },10,1);
        endif;
    }

    /**
     * register wp options wordpress
     */
    public function registerOptions(){
        register_setting('ctcl_cash_setting','ctcl_cash_on_delivery');
        register_setting('ctcl_basic_setting','ctcl_currency');
        register_setting('ctcl_basic_setting','ctcl_tax_rate');
    }

    /**
     * create admin panel content
     */
    public function adminPanelHtml(){

        add_filter('ctcl_admin_billings_html',function($val){
          
            $currency =  !empty(get_option('ctcl_currency'))?get_option('ctcl_currency'):'';
            $taxRate =  !empty(get_option('ctcl_tax_rate'))?get_option('ctcl_tax_rate'):'';
            $html = '<div class="ctcl-content-display ctcl-basic-business-settings">';
            $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-currency"  class="ctcl-currency-label">'.__('Currency (abbr): ','ctc-lite').'</label>';
            $html .= "<span><input id='ctcl-currency' type='text' name='ctcl_currency' value='{$currency}'></span></div>";
            $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-tax-rate"  class="ctcl-tax-rate">'.__('Tax Rate (%) : ','ctc-lite').'</label>';
            $html .= "<span><input id='ctcl-tax-rate' type='number' min='0.00' name='ctcl_tax_rate' value='{$taxRate}'></span></div>";
            $html .= '</div>';
            array_push($val,array('settingFields'=>'ctcl_basic_setting','formHeader'=>__("Basic Settings",'ctc-lite'),'html'=>$html));
      return $val;
        },10,1);

        add_filter('ctcl_admin_billings_html',function($val){
            $checked =  '1'=== get_option('ctcl_cash_on_delivery')? 'checked':'';

            $html = '<div class="ctcl-content-display ctcl-payment-cash">';
            $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-cash-on-deblivery"  class="ctcl-cash-on-delivery-label">'.__('Cash on delivery','ctc-lite').'</label>';
            $html .= "<span><input id='ctcl-cash-on-delivery' {$checked} type='checkbox' name='ctcl_cash_on_delivery' value='1'></span></div>";
            $html .= '</div>';
            array_push($val,array('settingFields'=>'ctcl_cash_setting','formHeader'=>__("Cash On Delivery Payment",'ctc-lite'),'html'=>$html));
      return $val;
        },20,1);


    }
    /**
     * process payments
     */

     public function processPayments(){


     }

     /**
      * html for frontend
      */
      public function frontendHtml(){

      }

}