<?php
/*
 Plugin Name:CTC Lite
 Plugin URI:https://github.com/ujw0l/ctc-rating
 Description: CT commerce lite, ecommerce plugin
 Version: 1.0.0
 Author: Ujwol Bastakoti
 Author URI:https://ujw0l.github.io/
 Text Domain:  ctc-lite
 License: GPLv2
*/
require_once "classes/ctcl-html.php";
require_once "classes/ctcl-processing.php";
require_once "classes/ctcl-payments.php";

class ctcLite{

   /**
    * Variable to generate html 
    */
   public $ctcHtml;

   /**
    * Variable for plugin precessing
    */
   public $ctclProcessing;

   /**
    * Payments options
    */
    public  $ctclPayments;

public function __construct(){
   define('CTCL_DIR_PATH',plugin_dir_url(__FILE__) );
   $this->ctclPayments =  new ctclPayments();
   $this->ctcHtml =  new ctclHtml();
   $this->ctclProcessing = new ctclProcessing();
  
   self::activeDeactivUinstall();
   self::requiredWpAction();
   self::requiredAjax();
   self::requiredShortCode();
  
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

}
/**
 * Deactivate plugin
 */

 public function ctcLiteDeactivate(){

 }

 /**
  * Uninstall plugin
  */
  public function ctcLiteUnistall(){


  }

  /**
   * Register required wp action
   */
  public function requiredWpAction(){

   //$ctcHtml =  new ctclHtml();

    add_action('admin_menu', array($this, 'adminMenu'),10);  
    add_action( 'wp_enqueue_scripts', array($this,'enequeFrontendJs' ));
    add_action( 'wp_enqueue_scripts', array($this,'enequeFrontendCss' ));
    add_action( 'admin_enqueue_scripts', array($this,'enequeAdminJs' ));
    add_action('admin_enqueue_scripts', array($this, 'enequeAdminCss'));
    add_action( 'init', array($this,'registerGutenbergBlocks' ));
    
  }

  /**
   * eneque frontend JS files
   */

   public function enequeFrontendJs(){
    wp_enqueue_script('ctclFrontendJs', CTCL_DIR_PATH.'js/ctcl-frontend.js');
    wp_localize_script('ctclFrontendJs','ctclParams',array(
       'taxRate'=>7,
       'currency'=>'usd',
       'totalShipping'=> __('Total shipping Cost', 'ctc-lite'),
       'subTotal'=>__("Sub Total","ctc-lite"),
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
    wp_enqueue_script('ctclAdminJs', CTCL_DIR_PATH.'js/ctcl-admin.js');
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
   add_shortcode('ctcl_payment_options', array($this->ctcHtml,'paymentOptionsShortCode'));

}

/**
 * Required AJAX hooks 
 */
public function requiredAjax(){

}

   /**
   * create admin menu 
   */

  public function adminMenu(){
    if ( is_admin()):
        add_menu_page( 'CTC Lite','CTC Lite ','administrator','ctclAdminPanel',array($this->ctcHtml, 'adminPanelHtml'),'dashicons-store','2');
    endif;
}

   /**
   * create admin menu content
   */
  public function adminPanelContent(){
    echo "This is admin panel content";

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