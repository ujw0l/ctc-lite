<?php
// Isolated security regression checks. No database, email or payment network calls.
error_reporting(E_ALL);
set_error_handler(function($level,$message,$file,$line){throw new ErrorException($message,0,$level,$file,$line);});
function __($s,$d=null){return $s;}
function esc_html($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function esc_attr($s){return esc_html($s);}
function esc_textarea($s){return esc_html($s);}
function esc_html__($s,$d=null){return esc_html($s);}
function sanitize_text_field($s){return strip_tags($s);}
function sanitize_textarea_field($s){return strip_tags($s);}
function sanitize_email($s){return $s;}
function absint($s){return abs((int)$s);}
function wp_unslash($v){return is_array($v)?array_map('wp_unslash',$v):stripslashes($v);}
function wp_json_encode($v){return json_encode($v);}
class WP_Error {private $message;function __construct($code,$message){$this->message=$message;}function get_error_message(){return $this->message;}}
function is_wp_error($v){return $v instanceof WP_Error;}
class RequestStopped extends Exception {}
$admin=false;$nonce=false;$processor='success';$seenResult=null;$events=0;
function current_user_can($cap){global $admin;return $admin && $cap==='manage_options';}
function wp_die($message='',$title='',$args=array()){throw new RequestStopped($message);}
function check_ajax_referer($action,$key){global $nonce;if(!$nonce || $action!=='ctcl_admin' || $key!=='nonce')wp_die('Invalid nonce');}
function get_option($name){return $name==='ctcl_tax_rate'?5:'';}
function has_filter($name){return $name==='ctcl_process_payment_ctcl_cash';}
function apply_filters($name,$data){
 global $processor,$seenResult;
 if($name==='ctcl_payment_options')return array(array('id'=>'ctcl_cash','name'=>'Cash On Delivery'));
 if($name==='ctcl_shipping_option_display')return array(array('id'=>'store_pickup','name'=>'Pickup'),array('id'=>'vendor_shipping','name'=>'Delivery'));
 if($name==='ctcl_process_payment_ctcl_cash'){$seenResult=$data['charge_result'];if($processor==='success')$data['charge_result']=true;return $data;}
 if($name==='ctcl_custom_email_body')return '<p>Confirmation</p>';
 return $data;
}
function do_action($name,$data){global $events;$events++;}
function wp_mail($to,$subject,$body,$headers){return true;}
function _e($s,$d=null){echo $s;}
function submit_button($text,$type,$name,$wrap){echo '<button>'.esc_html($text).'</button>';}
$posts=array(
 8=>(object)array('post_status'=>'publish','post_password'=>'','post_content'=>json_encode(array(array('blockName'=>'ctc-lite/ctc-lite-product-block','attrs'=>array('productName'=>'Shoe','productPrice'=>'50.00','shippingCost'=>'10.00','variation1'=>array(array('value'=>'Large~60.00'),array('value'=>'Small~50.00'))),'innerBlocks'=>array())))),
 9=>(object)array('post_status'=>'publish','post_password'=>'','post_content'=>json_encode(array(array('blockName'=>'ctc-lite/ctc-lite-checkout-block','attrs'=>array('couponAvail'=>true,'couponCode'=>'SAVE','amount'=>10),'innerBlocks'=>array()))))
);
function get_post($id){global $posts;return $posts[$id]??null;}
function parse_blocks($content){return json_decode($content,true);}
class TestDb {
 public $prefix='wp_';public $calls=0;public $prepared=array();
 function prepare($sql,...$args){$this->prepared[]=array($sql,$args);return 'BOUND QUERY';}
 function get_var($sql){$this->calls++;if($sql!=='BOUND QUERY')throw new Exception('Unprepared lookup');return null;}
 function update(...$args){$this->calls++;return 1;}
 function delete(...$args){$this->calls++;return 1;}
}
$wpdb=new TestDb();
require dirname(__DIR__).'/classes/ctcl-processing.php';
require dirname(__DIR__).'/classes/ctcl-html.php';
require dirname(__DIR__).'/classes/ctcl-checkout-validation.php';
function check($condition,$message){if(!$condition)throw new Exception($message);echo "PASS: $message\n";}
class CheckoutHarness extends ctclProcessing {public $stored=array();function enterDataToTable($data){$this->stored[]=$data;}}
$p=new CheckoutHarness();$html=new ctclHtml();
$handlers=array(array($p,'sendSmtpTestEmail'),array($p,'updateOrderVendorNote'),array($p,'orderMarkComplete'),array($p,'cancelOrder'),array($p,'refundOrder'),array($html,'getPendingOrderDetail'),array($html,'completeOrderDetail'));
foreach(array(array(false,true),array(true,false)) as $auth){
 list($admin,$nonce)=$auth;
 foreach($handlers as $handler){try{$handler();throw new Exception('Request was allowed');}catch(RequestStopped $e){}}
 check($wpdb->calls===0,'Unauthorized or nonce-less requests stop before database access');
}
$admin=true;$nonce=true;$_POST=array('orderId'=>"1' OR 1=1 --");
foreach(array_slice($handlers,1) as $handler){try{$handler();throw new Exception('Invalid ID was allowed');}catch(RequestStopped $e){}}
check($wpdb->calls===0,'All order handlers reject malformed IDs');
$p->getOrderDetail("1' OR 1=1 --");$p->getVendorNote("1' OR 1=1 --");
check($wpdb->prepared[0][0]==='SELECT orderDetail FROM wp_ctclOrders WHERE orderId=%s' && $wpdb->prepared[0][1][0]==="1' OR 1=1 --",'Order lookup binds attacker input as a value');
check(strpos($wpdb->prepared[1][0],'%s')!==false,'Vendor note lookup is prepared');
$base=array('payment_option'=>'ctcl_cash','shipping_option'=>'store_pickup','payment_type'=>'<img src=x onerror=alert(1)>','charge_result'=>1,'checkout-email-address'=>'test@example.com','products'=>array(json_encode(array('postId'=>8,'itemName'=>'Shoe','quantity'=>2,'itemTotal'=>100,'vari'=>'N/A,N/A'))),'items-total'=>100,'tax-total'=>5,'shipping-total'=>0,'sub-total'=>105);
function postCheckout($data){global $p;$_POST=array_map(function($v){return is_array($v)?array_map('addslashes',$v):(is_string($v)?addslashes($v):$v);},$data);return $p->orderProcessingShortCode();}
$forged=$base;$forged['payment_option']='unregistered';postCheckout($forged);
check(count($p->stored)===0 && $events===0,'Unknown payment cannot forge a successful order');
$processor='noop';postCheckout($base);
check(count($p->stored)===0 && $seenResult===false,'Client payment result is removed even for a registered no-op processor');
$processor='success';postCheckout($base);
check(count($p->stored)===1 && $p->stored[0]['payment_type']==='Cash On Delivery','Enabled COD still places a canonical order');
$forged=$base;$forged['sub-total']=1;postCheckout($forged);
$forged=$base;$forged['products']=array(json_encode(array('postId'=>8,'itemName'=>'Shoe','quantity'=>2,'itemTotal'=>1,'vari'=>'N/A,N/A')));postCheckout($forged);
$forged=$base;$forged['total-discount']=100;postCheckout($forged);
check(count($p->stored)===1,'Forged totals, item prices and discounts are rejected');
$coupon=$base;$coupon['ctcl_checkout_page']=9;$coupon['ctcl_coupon_code']='SAVE';$coupon['total-discount']=10;$coupon['sub-total']=95;postCheckout($coupon);
check(count($p->stored)===2,'Configured coupon still applies');
$variation=$base;$variation['shipping_option']='vendor_shipping';$variation['products']=array(json_encode(array('postId'=>8,'itemName'=>'Shoe','quantity'=>2,'itemTotal'=>120,'vari'=>'Large,N/A')));$variation['items-total']=120;$variation['tax-total']=6;$variation['shipping-total']=20;$variation['sub-total']=146;postCheckout($variation);
check(count($p->stored)===3,'Variation price and vendor shipping still work');
$payload='</span><img src=x onerror=alert(1)>';
$method=new ReflectionMethod($html,'orderDisplayData');$method->setAccessible(true);
$detail=$method->invoke($html,json_encode(array('ctcl-co-first-name'=>$payload,'items-total'=>$payload,'shipping_type'=>$payload)),'123');
foreach(array('createCustomerInfoSection','createOrderListSection') as $methodName){$method=new ReflectionMethod($html,$methodName);$method->setAccessible(true);ob_start();$method->invoke($html,$detail);$out=ob_get_clean();check(strpos($out,'<img')===false && strpos($out,'&lt;img')!==false,'Historical malicious data is escaped in '.$methodName);}
// Authorized requests retain the existing update/delete/refund handlers.
$_POST=array('orderId'=>'1790121600','vendorNote'=>'Packed & ready');
$before=$wpdb->calls;
foreach(array('updateOrderVendorNote','orderMarkComplete','cancelOrder','refundOrder') as $method){
 ob_start();try{$p->$method();}catch(RequestStopped $e){}ob_end_clean();
}
check($wpdb->calls===$before+4,'Authorized order management actions still reach the database');
$forged=$base;$forged['payment_option']=array('ctcl_cash');postCheckout($forged);
$forged=$base;$forged['products']=array('null');postCheckout($forged);
$forged=$base;$forged['products']=array(json_encode(array('postId'=>8,'itemName'=>'Shoe','quantity'=>-1,'itemTotal'=>-50,'vari'=>'N/A,N/A')));postCheckout($forged);
check(count($p->stored)===3,'Malformed checkout data and negative quantities fail safely');
$posts[8]->post_status='draft';postCheckout($base);
check(count($p->stored)===3,'Unpublished products cannot be ordered');
