<?php

class ctclPayments{
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
            add_filter('ctcl_shipping_options',function($val){
                

                $paymentOption = array('payment-id'=>$this->paymentId,
                                        'payment-name'=>$this->paymentName,
                                        'option-html'=>$this->frontendHtml
                                    );

                
                return array_push($val,$option);
            },10,1);
        endif;
    }

    /**
     * register wp options wordpress
     */
    public function registerOptions(){
        register_setting('ctcl_payment_setting','ctcl_cash_on_delivery');
    }

    /**
     * create admin panel content
     */
    public function adminPanelHtml(){

        add_filter('ctcl_admin_payment_html',function($val){
            $checked =  '1'=== get_option('ctcl_cash_on_delivery')? 'checked':'';

            $html = '<div class="ctcl-content-display ctc_payment_cash">';
            $html .=  '<label for"ctcl-cash-on-deblivery"  class="ctcl-cash-on-deblivery-label">'.__('Cash on delivery','ctc-lite').'</label>';
            $html .= "<input id='ctcl-cash-on-delivery' {$checked} type='checkbox' name='ctcl_cash_on_delivery' value='1'>";
            $html .= '</div>';
            array_push($val,array('formHeader'=>__("Cash On Delivery Payment",'ctc-lite'),'formSetting'=>'ctcl_payment_setting','html'=>$html));
      return $val;
        },10,1);
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