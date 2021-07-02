<?php

class ctclBillings{
    /**
     * @since 1.0.0
     *
     * payment id
     */
public $paymentId = 'ctcl_cash';

/**
 * @since 1.0.0
 *
 * payment name
 */
public $paymentName ;


public function __construct(){

    $this->paymentName = __('Cash On Delivery','ctc-lite');
    self::registerOptions();
    self::displayOptionsUser();
    self:: addRequiredFilter();
    
    
}

/**
* @since 1.0.0
*
* create radio buttons
*/
    public function displayOptionsUser(){
        if(1 == get_option('ctcl_cash_on_delivery')): 
            add_filter('ctcl_payment_options',function($val){
                $paymentOption = array('id'=>$this->paymentId,
                                        'name'=>$this->paymentName,
                                        'html'=>$this->frontendHtml()
                                    );
                array_push($val, $paymentOption);
                return  $val;
            },10,1);
        endif;
    }

    /**
     * @since 1.0.0
     *
     * register wp options wordpress
     */
    public function registerOptions(){
        register_setting('ctcl_cash_setting','ctcl_cash_on_delivery');
        register_setting('ctcl_basic_setting','ctcl_currency');
        register_setting('ctcl_basic_setting','ctcl_tax_rate');
    }

    /**
     * @since 1.0.0
     *
     * create admin panel content
     */
    public function addRequiredFilter(){
        add_filter('ctcl_admin_billings_html',array($this,'basicSettingAdminHtml'),10,1);
        add_filter('ctcl_admin_billings_html',array($this,'cashOnDeliveryAdminHtml'),20,1);
        add_filter('ctcl_process_payment_'.$this->paymentId , array($this,'processPayment'));
    }


    /**
     * @since 1.0.0
     *
     * basic setting admin html
     * 
     */
    public function basicSettingAdminHtml($val){

        $currency =  !empty(get_option('ctcl_currency'))?get_option('ctcl_currency'):'';
        $taxRate =  !empty(get_option('ctcl_tax_rate'))?get_option('ctcl_tax_rate'):'';
        $html = '<div class="ctcl-content-display ctcl-basic-business-settings">';
        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-currency"  class="ctcl-currency-label">'.__('Currency (abbr): ','ctc-lite').'</label>';
        $html .= "<span><input id='ctcl-currency' type='text' name='ctcl_currency' value='{$currency}'></span></div>";
        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-tax-rate"  class="ctcl-tax-rate-label">'.__('Tax Rate (%) : ','ctc-lite').'</label>';
        $html .= "<span><input id='ctcl-tax-rate' type='number' min='0.00' name='ctcl_tax_rate' value='{$taxRate}'></span></div>";
        $html .= '</div>';

        array_push($val,array(
                                'settingFields'=>'ctcl_basic_setting',
                                'formHeader'=>__("Basic Settings",'ctc-lite'),
                                'html'=>$html
                            )
                        );
    return $val;

    }

    /**
     * @since 1.0.0
     *
     * Cash on delivery admin html
     */
    public function cashOnDeliveryAdminHtml($val){
        $checked =  '1'=== get_option('ctcl_cash_on_delivery')? 'checked':'';

        $html = '<div class="ctcl-content-display ctcl-payment-cash">';
        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-cash-on-delivery"  class="ctcl-cash-on-delivery-label">'.__('Cash on delivery','ctc-lite').'</i></label>';
        $html .= "<span><input id='ctcl-cash-on-delivery' {$checked} type='checkbox' name='ctcl_cash_on_delivery' value='1'></span></div>";
        $html .= '</div>';

        array_push($val,array(
                                'settingFields'=>'ctcl_cash_setting',
                                'formHeader'=>__("Cash On Delivery Payment",'ctc-lite'),
                                'html'=>$html
                            )
                        );
    return $val;

    }

    /**
     * @since 1.0.0
     *
     * process payments
     */

     public function processPayment($val){
        $val['charge_result']= TRUE;
        return $val;
     }

     /**
      * @since 1.0.0
      *
      * html for frontend
      */
      public function frontendHtml(){
        return "";
      }

}