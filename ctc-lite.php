<?php
/*
 Plugin Name:CT Commerce Lite
 Plugin URI:https://github.com/ujw0l/ctc-lite
 Description: CT Commerce Lite ecommerce plugin
 Version: 2.4.2
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
    * @since 1.0.0
    *
    * Variable for plugin precessing
    */
   public $ctclProcessing;

   /**
    *  @since 1.0.0
    *
    * Payments options
    */
    public  $ctclBillings;

    /**
     * @since 1.0.0
     *
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
* @since 1.0.0
*
* Plugin activation, deactivation and uninstall
*/
public function activeDeactivUinstall(){   
    register_activation_hook(__FILE__, array($this, 'ctcLiteActivate'));
    register_deactivation_hook(__FILE__,  array($this,'ctcLiteDeactivate'));
  
}

/**
 * @since 1.0.0
 *
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
 * @since 1.0.0
 *
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
   * @since 1.0.0
   *
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
    add_filter( 'block_categories_all', array($this,'ctcLiteBlocks'), 10, 2);
  }

  /**
   * @since 1.0.0
   *
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
   * @since 1.0.0
   *
   * eneque frontend JS files
   */

   public function enequeFrontendJs(){
   
      wp_enqueue_script('ctcOvrlayJs', CTCL_DIR_PATH.'js/ctc_overlay.js'); 
      wp_enqueue_script('ctcImgGallery', CTCL_DIR_PATH.'js/ctcl-image-gallery.js'); 
    wp_enqueue_script('ctclFrontendJs', CTCL_DIR_PATH.'js/ctcl-frontend.js',array('ctcOvrlayJs','ctcImgGallery'));
    wp_localize_script('ctclFrontendJs','ctclParams',array(
       'taxRate'=>get_option('ctcl_tax_rate'),
       'currency'=>get_option('ctcl_currency'),
       'totalShipping'=> __('Total Shipping Cost', 'ctc-lite'),
       'itemsTotal'=>__('Sub Total','ctc-lite'),
       'taxTotal'=>__('Sales Tax','ctc-lite'),
       'subTotal'=>__("Total","ctc-lite"),
       'removeItem'=>__('Remove Item','ctc-lite'),
       'itemHead'=>__('Product','ctc-lite'),
       'qtyHead'=>__('Qty','ctc-lite'),
       'priceHead'=>__('Price','ctc-lite'),
       'itemTotalHead'=>__('Item total','ctc-lite'),
       'varHead'=>__('Variation','ctc-lite'),
       
      )
   );
   
   }

   /**
   * @since 1.0.0
   *
   * eneque frontend CSS files
   */

  public function enequeFrontendCss(){
   
    wp_enqueue_style( 'ctclFrontendCss', CTCL_DIR_PATH.'css/ctcl-frontend.css', array('dashicons')); 
}

   /**
   * @since 1.0.0
   *
   * eneque admin JS files
   */

  public function enequeAdminJs(){
   wp_enqueue_script('ctclJsMasonry', CTCL_DIR_PATH.'js/js-masonry.js',array());
   wp_enqueue_script('ctclJsOverlay', CTCL_DIR_PATH.'js/js-overlay.js',array());
   wp_enqueue_script('ctcImgGallery', CTCL_DIR_PATH.'js/ctcl-image-gallery.js'); 
    wp_enqueue_script('ctclAdminJs', CTCL_DIR_PATH.'js/ctcl-admin.js',array('ctclJsMasonry','ctclJsOverlay','ctcImgGallery'));
    wp_localize_script('ctclAdminJs','ctclAdminObject',array(
                                                               'ajaxUrl'=>admin_url( 'admin-ajax.php'),
                                                               'emptyTestEmail'=>__('Please provide email for testing.','ctc-lite'),
                                                               'confirmCancelOrder'=>__("This will remove order. Are you sure?",'ctc-lite'),
                                                               'confirmRefund'=>__("This will refund order. Are you sure?",'ctc-lite'),
                                                            ));
   }

   /**
   * @since 1.0.0
   *
   * eneque frontend CSS files
   */

  public function enequeAdminCss(){
    wp_enqueue_style( 'ctclAdminCss', CTCL_DIR_PATH.'css/ctcl-admin-panel.css'); 
}
/**
 * @since 1.0.0
 *
 * Required shortcode 
 */
public function requiredShortCode(){
   add_shortcode('ctcl_payment_options', array($this->ctclHtml,'paymentOptionsShortCode'));
   add_shortcode('ctcl_shipping_options', array($this->ctclHtml,'shippingOptionsShortCode'));

}

/**
 * @since 1.0.0
 *
 * Required AJAX hooks 
 */
public function requiredAjax(){

   add_action( 'wp_ajax_sendTestEmail', array($this->ctclProcessing,'sendSmtpTestEmail') );
   add_action('wp_ajax_pendingOrderDetail',array($this->ctclHtml,'getPendingOrderDetail'));
   add_action('wp_ajax_updateVendorNote',array($this->ctclProcessing,'updateOrderVendorNote'));
   add_action('wp_ajax_orderMarkComplete',array($this->ctclProcessing,'orderMarkComplete'));
   add_action('wp_ajax_completeOrderDetail',array($this->ctclHtml,'completeOrderDetail'));
   add_action('wp_ajax_cancelOrder',array($this->ctclProcessing,'cancelOrder'));
   add_action('wp_ajax_refundOrder',array($this->ctclProcessing,'refundOrder'));
}

   /**
   * @since 1.0.0
   *
   * create admin menu 
   */

  public function adminMenu(){
    if ( is_admin()):
      $pendingOrder = "<span '".__('Pending Orders','ctc-lite')."' class='update-plugins'><span class='plugin-count'> {$this->ctclProcessing->getTotalPendingOrders()} </span><span>";
        add_menu_page( __('CTC Lite', 'ctc-lite' ),__('CTC Lite ', 'ctc-lite').$pendingOrder,'administrator','ctclAdminPanel',array($this->ctclHtml, 'adminPanelHtml'),'dashicons-store','2');
    endif;
}

  /**
   * @since 1.0.0
   *
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

 wp_set_script_translations('ctcl-block-editor', 'ctc-lite',  dirname(plugin_basename(__FILE__)) . '/languages/');
 
 wp_localize_script( 'ctcl-block-editor', 'ctcLiteParams', array(
    'currency'=> !empty(get_option('ctcl_currency'))?get_option('ctcl_currency'): '',
    'defaultPic'=>plugins_url( 'misc/image/ctclite-default.png',__FILE__ )
   ));
  // Block front end styles.
  wp_register_style(
     'ctcl-block-frontend-styles',
     plugins_url( 'css/ctcl-frontend.css',__FILE__ ),
     array('dashicons')
 
  );
 
  // Block editor styles.
  wp_register_style(
     'ctcl-block-editor-styles',
     plugins_url( 'css/ctcl-admin-block.css',__FILE__ ),
     array( 'wp-edit-blocks','dashicons' ),
  );
 
 register_block_type(
     'ctc-lite/ctc-lite',
    array(
       'style'         => 'ctcl-block-frontend-styles',
       'editor_style'  => 'ctcl-block-editor-styles',
       'editor_script' => 'ctcl-block-editor',
      
    ),

    register_block_type(
      'ctc-lite/column-display',
    ),

 );


 register_block_type(
   'ctc-lite/ctc-lite-order-processing',
   array(
      'render_callback'=>array($this->ctclProcessing,'orderProcessingShortCode'),
   ),

);

  }

/**
 * @since 1.0.0
 *
 * Create block category for plugin
 */
  public function ctcLiteBlocks( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'ctc-lite-blocks',
				'title' => __( 'CT Commerce Lite', 'ctc-lite' ),
			),
		)
	);
}

}

new ctcLite();

/**
 * 
 * On deletion run this function
 * 
 */

register_uninstall_hook(__FILE__,'ctcLiteUninstall');

 /**
  * @since 1.0.0
  *
  * Uninstall plugin
  */
  function ctcLiteUnistall(){
   global $wpdb;
   $wpdb->query("DROP TABLE {$wpdb->prefix}ctclOrders;");
  }