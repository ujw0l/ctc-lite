<?php
/*
 Plugin Name:CTC Lite
 Plugin URI:https://github.com/ujw0l/ctc-lite
 Description: CT Commerce Lite ecommerce plugin
 Version: 1.0.0
 Author: Ujwol Bastakoti
 Author URI:https://ujw0l.github.io/
 Text Domain:  ctc-lite
 License: GPLv2
*/
require_once "classes/ctcl-html.php";
require_once "classes/ctcl-processing.php";
require_once "classes/ctcl-billings.php";
require_once "classes/ctcl-shippings.php";

class ctcLite{

   /**
    * Variable to generate html 
    */
   public $ctclHtml;

   /**
    * Variable for plugin precessing
    */
   public $ctclProcessing;

   /**
    * Payments options
    */
    public  $ctclBillings;

    /**
     * Shipping Options
     */
    public $ctclShippings;


public function __construct(){
   define('CTCL_DIR_PATH',plugin_dir_url(__FILE__) );
   $this->ctclBillings =  new ctclBillings();
   $this->ctclShippings =  new ctclShippings();
   $this->ctclHtml =  new ctclHtml();
   $this->ctclProcessing = new ctclProcessing();
  
   self::activeDeactivUinstall();
   self::requiredWpAction();
   self::requiredAjax();
   self::requiredShortCode();
   self::registerWpSetting();
  
}

/** 
* Plugin activation, deactivation and uninstall
*/
public function activeDeactivUinstall(){   
    register_activation_hook(__FILE__, array($this, 'ctcLiteActivate'));
    register_deactivation_hook(__FILE__,  array($this,'ctcLiteDeactivate'));
    register_uninstall_hook(__FILE__,array('ctcLite','ctcLiteUninstall'));
}

/**
 * Activate plugin
 */
public function ctcLiteActivate(){
global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
   $sql[] =  "CREATE TABLE `".$wpdb->prefix."ctclOrders`(
      `orderId` varchar(155) NOT NULL,
      `orderDetail` text NOT NULL,
      `orderStatus` varchar(155)NOT NULL,
      `vendorNote` text NOT NULL,
      UNIQUE KEY (`orderId`)) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
}
/**
 * Deactivate plugin
 */

 public function ctcLiteDeactivate(){
   delete_option('ctcl_email_subject');
   delete_option('ctcl_smtp_host');
   delete_option('ctcl_smtp_authentication');
   delete_option('ctcl_smtp_port');
   delete_option('ctcl_smtp_username');
   delete_option('ctcl_smtp_password');
   delete_option('ctcl_smtp_encryption');
   delete_option('ctcl_smtp_from_email');
   delete_option('ctcl_smtp_from_name');
   delete_option('ctcl_smtp_bcc_email');
   delete_option('ctcl_cash_on_delivery');
   delete_option('ctcl_currency');
   delete_option('ctcl_tax_rate');
   delete_option('ctcl_vendor_delivery_activate');
   delete_option('ctcl_vendor_delivery_note');
   delete_option('ctcl_store_pickup_activate');
   delete_option('ctcl_store_pickup_street1');
   delete_option('ctcl_store_pickup_street2');
   delete_option('ctcl_store_pickup_city');
   delete_option('ctcl_store_pickup_state');
   delete_option('ctcl_store_pickup_zip_code');
   delete_option('ctcl_store_pickup_country');
   delete_option('ctcl_store_pickup_note');
 }

 /**
  * Uninstall plugin
  */
  public function ctcLiteUnistall(){
   global $wpdb;
   $wpdb->query("DROP TABLE {$wpdb->prefix}ctclOrders;");
  }

  /**
   * Register required wp action
   */
  public function requiredWpAction(){

    add_action('admin_menu', array($this, 'adminMenu'),10);  
    add_action( 'wp_enqueue_scripts', array($this,'enequeFrontendJs' ));
    add_action( 'wp_enqueue_scripts', array($this,'enequeFrontendCss' ));
    add_action( 'admin_enqueue_scripts', array($this,'enequeAdminJs' ));
    add_action('admin_enqueue_scripts', array($this, 'enequeAdminCss'));
    add_action( 'phpmailer_init', array($this->ctclProcessing,'smtpEmailSetting' ));
    add_filter( 'wp_mail_from', function(){return get_option('ctcl_smtp_from_email');} );
    add_action( 'init', array($this,'registerGutenbergBlocks' ));
    
  }

  /**
   * Register wp form settings 
   */
  private function registerWpSetting(){

   register_setting('ctcl_email_settings','ctcl_email_subject');
   register_setting('ctcl_email_settings','ctcl_smtp_host');
   register_setting('ctcl_email_settings','ctcl_smtp_authentication');
   register_setting('ctcl_email_settings','ctcl_smtp_port');
   register_setting('ctcl_email_settings','ctcl_smtp_username');
   register_setting('ctcl_email_settings','ctcl_smtp_password');
   register_setting('ctcl_email_settings','ctcl_smtp_encryption');
   register_setting('ctcl_email_settings','ctcl_smtp_from_email');
   register_setting('ctcl_email_settings','ctcl_smtp_from_name');
   register_setting('ctcl_email_settings','ctcl_smtp_bcc_email');
  }

  /**
   * eneque frontend JS files
   */

   public function enequeFrontendJs(){
    wp_enqueue_script('ctclFrontendJs', CTCL_DIR_PATH.'js/ctcl-frontend.js');
    wp_localize_script('ctclFrontendJs','ctclParams',array(
       'taxRate'=>get_option('ctcl_tax_rate'),
       'currency'=>get_option('ctcl_currency'),
       'totalShipping'=> __('Total shipping Cost', 'ctc-lite'),
       'subTotal'=>__("Sub Total","ctc-lite"),
       'removeItem'=>__('Remove Item','ctc-lite'),
       'itemHead'=>__('Product','ctc-lite'),
       'qtyHead'=>__('Qty','ctc-lite'),
       'priceHead'=>__('Price','ctc-lite'),
       'itemTotalHead'=>__('Item total','ctc-lite'),
       
      )
   );
    wp_localize_script('ctclFrontendJs','ctclCartFunc',[]);
   }

   /**
   * eneque frontend CSS files
   */

  public function enequeFrontendCss(){
    wp_enqueue_style( 'ctclFrontendCss', CTCL_DIR_PATH.'css/ctcl-frontend.css'); 
}

/**
   * eneque admin JS files
   */

  public function enequeAdminJs(){
   wp_enqueue_script('ctclJsMasonry', CTCL_DIR_PATH.'js/js-masonry.js',array());
   wp_enqueue_script('ctclJsOverlay', CTCL_DIR_PATH.'js/js-overlay.js',array());
    wp_enqueue_script('ctclAdminJs', CTCL_DIR_PATH.'js/ctcl-admin.js',array('ctclJsMasonry','ctclJsOverlay'));
    wp_localize_script('ctclAdminJs','ctclAdminObject',array(
                                                               'ajaxUrl'=>admin_url( 'admin-ajax.php'),
                                                               'emptyTestEmail'=>'Please provide email for testing.'
                                                            ));
   }

   /**
   * eneque frontend CSS files
   */

  public function enequeAdminCss(){
    wp_enqueue_style( 'ctclAdminCss', CTCL_DIR_PATH.'css/ctcl-admin-panel.css'); 
}
/**
 * Required shortcode 
 */
public function requiredShortCode(){
   add_shortcode('ctcl_payment_options', array($this->ctclHtml,'paymentOptionsShortCode'));
   add_shortcode('ctcl_shipping_options', array($this->ctclHtml,'shippingOptionsShortCode'));
   add_shortcode('ctcl_order_page',array($this->ctclProcessing,'orderProcessingShortCode'));
}

/**
 * Required AJAX hooks 
 */
public function requiredAjax(){
   add_action( 'wp_ajax_sendTestEmail', array($this->ctclProcessing,'sendSmtpTestEmail') );
   add_action('wp_ajax_pendingOrderDetail',array($this->ctclHtml,'getPendingOrderDetail'));
}

   /**
   * create admin menu 
   */

  public function adminMenu(){
    if ( is_admin()):
        add_menu_page( 'CTC Lite','CTC Lite ','administrator','ctclAdminPanel',array($this->ctclHtml, 'adminPanelHtml'),'dashicons-store','2');
    endif;
}

  /**
   * Register gutenberg block
   * 
   */
  public function registerGutenbergBlocks(){
  


	// Block Editor Script.
wp_register_script(
    'ctcl-block-editor',
    plugins_url( 'js/ctcl-block.js',__FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-edit-post','wp-components', 'wp-i18n','wp-data' ),
 );
 
 wp_localize_script( 'ctcl-block-editor', 'ctcLiteParams', array('defaultPic'=>plugins_url( 'misc/image/ctclite-default.png',__FILE__ )));
  // Block front end styles.
  wp_register_style(
     'ctcl-block-frontend-styles',
     plugins_url( 'css/ctcl-frontend.css',__FILE__ ),
 
  );
 
  // Block editor styles.
  wp_register_style(
     'ctcl-block-editor-styles',
     plugins_url( 'css/ctcl-admin-block.css',__FILE__ ),
     array( 'wp-edit-blocks','dashicons' ),
  );
 
 register_block_type(
     'ctc-lite/ctc-lite-block',
    array(
       'style'         => 'ctcl-block-frontend-styles',
       'editor_style'  => 'ctcl-block-editor-styles',
       'editor_script' => 'ctcl-block-editor',
    )
 );

  }


}

new ctcLite();