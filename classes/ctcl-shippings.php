<?php
class ctclShippings{

    /**
     * Shipping option id
     */
    public $shippingId ;

    /**
     * Shipinng name 
     */
    public $shippingsName;

    public function __construct(){

        self::adminPanelHtmlFilters();
        self::registerOptions();
        self::frontendShiipingOptions();
        self::processShipping();
    }

    /**
     * Register options
     */
    public function registerOptions(){
        register_setting('ctcl_vendor_devlivery_setting','ctcl_vendor_delivery_activate');
        register_setting('ctcl_vendor_devlivery_setting','ctcl_vendor_delivery_note');

        register_setting('ctcl_store_pickup_setting','ctcl_store_pickup_activate');
        register_setting('ctcl_store_pickup_setting','ctcl_store_pickup_street1');
        register_setting('ctcl_store_pickup_setting','ctcl_store_pickup_street2');
        register_setting('ctcl_store_pickup_setting','ctcl_store_pickup_city');
        register_setting('ctcl_store_pickup_setting','ctcl_store_pickup_state');
        register_setting('ctcl_store_pickup_setting','ctcl_store_pickup_zip_code');
        register_setting('ctcl_store_pickup_setting','ctcl_store_pickup_country');
        register_setting('ctcl_store_pickup_setting','ctcl_store_pickup_note');

        
    }

    /**
     * Forms for admin panel setting
     */

     public function adminPanelHtmlFilters(){
        add_filter('ctcl_admin_shipping_html' , array($this,'vendorDelivery'),20,1);
        add_filter('ctcl_admin_shipping_html' , array($this,'storePickup'),10,1);
     }

     /**
      * Create vendor delivery form
      */
      public function vendorDelivery($val){

        $vendorDelivery =  '1'== get_option('ctcl_vendor_delivery_activate')?'checked':'';
    
        
        $html = '<div class="ctcl-content-display ctcl-basic-business-settings">';
        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-vendor-delivery-activate"  class="ctcl-vendor-delivery-activate-label">'.__('Vendor Delivery: ','ctc-lite').'</label>';
        $html .= "<span><input id='ctcl-vendor-delivery-activate' type='checkbox' name='ctcl_vendor_delivery_activate' {$vendorDelivery} value='1'>";
        $html .= "<i>".__("Check if offering shipping.",'ctc-lite')."</i></span></div>";
        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-vendor-delivery-note"  class="ctcl-vendor-delivery-note-label">'.__('Shipping Note : ','ctc-lite').'</label>';
        $html .= "<span><textarea id='ctcl-vendor-delivery-note' type='text'  name='ctcl_vendor_delivery_note' value='".get_option('ctcl_vendor_delivery_note')."'>".get_option('ctcl_vendor_delivery_note')."</textarea></span></div>";
        $html .= '</div>';

        array_push($val,array(
                                'settingFields'=>'ctcl_vendor_devlivery_setting',
                                'formHeader'=>__("Vendor Delivery",'ctc-lite'),
                                'html'=>$html
                            )
                        );
  return $val;
      }
/**
 * Store pickup
 */
      public function storePickup($val){

        $storePickup =  '1'== get_option('ctcl_store_pickup_activate')?'checked':'';

        $html = '<div class="ctcl-content-display ctcl-basic-business-settings">';
        
        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-store-pickup-activate"  class="ctcl-store-pickup-activate-label">'.__('Store Pickup: ','ctc-lite').'</label>';
        $html .= "<span><input id='ctcl-store-pickup-activate' type='checkbox' name='ctcl_store_pickup_activate' {$storePickup} value='1'>";
        $html.=  "<i>".__('Check if offering store pickup.','ctc-lite')."</i></span></div>";

        $html .=  '<div class="ctcl-business-setting-row ctcl-store-address"><h4 class="dashicons-before dashicons-store">'.__('Store Adddress :','ctc-lite').'</h4></div>';

        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-store-pickup-street1"  class="ctcl-store-pickup-street1-label">'.__('Street Address 1: ','ctc-lite').'</label>';
        $html .= "<span><input id='ctcl-store-pickup-street1' type='text' name='ctcl_store_pickup_street1' value='".get_option('ctcl_store_pickup_street1')."'></span></div>";
        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-store-pickup-street2"  class="ctcl-store-pickup-street2-label">'.__('Street Address 2: ','ctc-lite').'</label>';
        $html .= "<span><input id='ctcl-store-pickup-street2' type='text' name='ctcl_store_pickup_street2' value='".get_option('ctcl_store_pickup_street2')."'></span></div>";
        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-store-pickup-city"  class="ctcl-store-pickup-city-label">'.__('City : ','ctc-lite').'</label>';
        $html .= "<span><input id='ctcl-store-pickup-city' type='text' name='ctcl_store_pickup_city' value='".get_option('ctcl_store_pickup_city')."'></span></div>";
        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-store-pickup-state"  class="ctcl-store-pickup-city-label">'.__('State/Province : ','ctc-lite').'</label>';
        $html .= "<span><input id='ctcl-store-pickup-state' type='text' name='ctcl_store_pickup_state' value='".get_option('ctcl_store_pickup_state')."'></span></div>";
        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-store-pickup-zip-code"  class="ctcl-store-pickup-zip-code-label">'.__('Zip Code : ','ctc-lite').'</label>';
        $html .= "<span><input id='ctcl-store-pickup-zip-code' type='text' name='ctcl_store_pickup_zip_code' value='".get_option('ctcl_store_pickup_zip_code')."'></span></div>";
        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-store-pickup-zip-country"  class="ctcl-store-pickup-zip-code-label">'.__('Country: ','ctc-lite').'</label>';
        $html .= "<span><input id='ctcl-store-pickup-country' type='text' name='ctcl_store_pickup_country' value='".get_option('ctcl_store_pickup_country')."'></span></div>";
        $html .=  '<div class="ctcl-business-setting-row"><label for"ctcl-store-pickup-note"  class="ctcl-store-pickup-note-label">'.__('Store Pickup Note : ','ctc-lite').'</label>';
        $html .= "<span><textarea id='ctcl-store-pickup-note' type='text'  name='ctcl_store_pickup_note' value='".get_option('ctcl_store_pickup_note')."'>".get_option('ctcl_store_pickup_note')."</textarea></span></div>";
        $html .= '</div>';

        array_push($val,array(
                                'settingFields'=>'ctcl_store_pickup_setting',
                                'formHeader'=>__("Store Pickup",'ctc-lite'),
                                'html'=>$html
                            )
                        );
  return $val;

      }

      /**
       * Display shipping option on frontend
       */
public function frontendShiipingOptions(){
    if('1'== get_option('ctcl_store_pickup_activate')):
    add_filter('ctcl_shipping_option_display',function($val){
        
        $shippingArr =  array(
                                'id'=>'store_pickup',
                                'name'=>'Store Pickup'
        );

        array_push($val,$shippingArr);

        return $val;
    },10,1);
    endif;

    if( '1'== get_option('ctcl_vendor_delivery_activate')):

        add_filter('ctcl_shipping_option_display',function($val){
        
            $shippingArr =  array(
                                    'id'=>'vendor_shipping',
                                    'name'=>'Vendor Shipping'
            );
    
            array_push($val,$shippingArr);
            return $val;
        },10,1);

    endif;
}

/**
 * Process shipping on checkout
 */
public function processShipping(){
    if('1'== get_option('ctcl_store_pickup_activate')):
        add_filter('ctcl_shipping_option_store_pickup',function($val){

            $html = '<p>'.__('Pickup your order at : ').'</p>';
            $html .= '<address>';
            $html .= '<div>'.get_option('ctcl_store_pickup_street1').'</div>';
            $html .= !empty(get_option('ctcl_store_pickup_street2'))?:'<div>'.get_option('ctcl_store_pickup_street2').'</div>';
            $html .= '<div>'.get_option('ctcl_store_pickup_city').'</div>';
            $html .= '<div>'.get_option('ctcl_store_pickup_state').'</div>';
            $html .= '<div>'.get_option('ctcl_store_pickup_zip_code').'</div>';
            $html .= '<div>'.get_option('ctcl_store_pickup_zip_country').'</div>';
            $html .= !empty('ctcl_store_pickup_note')?'<div><span>'.__('Note :','ctc-lite').'</span><span>'.get_option('ctcl_store_pickup_note').'</span></div>':'';
            $val['shipping_note'] = $html; 
            return $val;
        },10,1);
        endif;

        if( '1'== get_option('ctcl_vendor_delivery_activate')):

            add_filter('ctcl_shipping_option_vendor_shipping',function($val){

                $val['shipping_note'] =  $html .= !empty('ctcl_vendor_delivery_note')?'<div><span>'.__('Note :','ctc-lite').'</span><span>'.get_option('ctcl_vendor_delivery_note').'</span></div>':'';;
                return $val;
            },10,1);
    
        endif;   

}
     

}