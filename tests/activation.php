<?php
/** Run with: php tests/activation.php /path/to/wordpress
 * Uses the installed WordPress dbDelta implementation and an in-memory database
 * double. Does not bootstrap WordPress or access/change its database/settings.
 */
error_reporting(E_ALL);
set_error_handler(function($severity,$message,$file,$line){throw new ErrorException($message,0,$severity,$file,$line);});
$wp = $argv[1] ?? getenv('WORDPRESS_PATH');
if (!$wp || !is_file($wp.'/wp-admin/includes/upgrade.php')) {fwrite(STDERR,"Provide a WordPress source directory.\n");exit(1);}
$source=file_get_contents($wp.'/wp-admin/includes/upgrade.php');
if (!preg_match('/^function dbDelta\(.*?^\}/ms',$source,$match)) throw new Exception('Cannot locate dbDelta');
// Load the actual core function without executing admin bootstrap code.
eval($match[0]);
function apply_filters($name,$value){return $value;}
function add_action(...$args){} function add_filter(...$args){} function add_shortcode(...$args){}
function register_setting(...$args){} function register_deactivation_hook(...$args){}
function register_activation_hook($file,$callback){$GLOBALS['activation']=$callback;}
function plugin_dir_url($file){return 'https://example.test/plugins/ctc-lite/';}
function get_option($key){return false;} function __($text,$domain=null){return $text;}
class ActivationDatabase {
 public $prefix='wp_';public $exists=false;public $queries=[];public $orders=['existing-order'];
 function get_charset_collate(){return 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';}
 function tables($scope){return [];}
 function db_version(){return '8.0.35';}function db_server_info(){return '8.0.35';}
 function suppress_errors($value=true){return false;}
 function get_results($sql){
  if(!$this->exists)return [];
  if($sql==='DESCRIBE wp_ctclOrders;')return array_map(function($name){return (object)['Field'=>$name,'Type'=>in_array($name,['orderId','orderStatus'])?'varchar(155)':'text','Default'=>null];},['orderId','orderDetail','orderStatus','vendorNote']);
  if($sql==='SHOW INDEX FROM wp_ctclOrders;')return [(object)['Key_name'=>'orderId','Column_name'=>'orderId','Sub_part'=>null,'Non_unique'=>0,'Index_type'=>'BTREE']];
  return [];
 }
 function query($sql){
  if(strpos($sql,'CREATE TABLE')===0){if($this->exists)throw new Exception('Table already exists');$this->exists=true;}
  $this->queries[]=$sql;return true;
 }
}
// Only a harmless include shim is needed because dbDelta is already loaded.
$temp=sys_get_temp_dir().'/ctcl-activation-'.uniqid();mkdir($temp.'/wp-admin/includes',0777,true);
file_put_contents($temp.'/wp-admin/includes/upgrade.php','<?php // dbDelta loaded by test');
define('ABSPATH',$temp.'/');$wpdb=new ActivationDatabase();
try {
 ob_start();require __DIR__.'/../ctc-lite.php';$output=ob_get_clean();
 if($output!=='')throw new Exception('Plugin bootstrap produced output');
 ob_start();call_user_func($GLOBALS['activation']);$output=ob_get_clean();
 if($output!==''||count($wpdb->queries)!==1)throw new Exception('Fresh activation failed');
 echo "PASS: fresh activation creates one table without output\n";
 $wpdb->queries=[];
 ob_start();call_user_func($GLOBALS['activation']);$output=ob_get_clean();
 if($output!==''||$wpdb->queries!==[])throw new Exception('Repeat activation changed existing schema');
 if($wpdb->orders!==['existing-order'])throw new Exception('Order data changed');
 echo "PASS: repeat activation emits no output or schema queries; existing orders preserved\n";
} finally {unlink($temp.'/wp-admin/includes/upgrade.php');rmdir($temp.'/wp-admin/includes');rmdir($temp.'/wp-admin');rmdir($temp);}
