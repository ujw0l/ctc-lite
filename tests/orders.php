<?php
// Standalone order-screen regression checks; no WordPress database is changed.
error_reporting(E_ALL);
set_error_handler(function ($level, $message, $file, $line) {
    throw new ErrorException($message, 0, $level, $file, $line);
});
function __($text, $domain = null) { return $text; }
function esc_html($text) { return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'); }
function esc_attr($text) { return esc_html($text); }
function absint($value) { return abs((int) $value); }
function paginate_links($args) { return ''; }
function add_query_arg($key, $value) { return ''; }
function sanitize_text_field($value) { return $value; }
function wp_die() {}
class ctclProcessing {
    public static $rows = array();
    public static $raw;
    public function getTotalPendingOrders() { return count(self::$rows); }
    public function getTotalCompleteOrders() { return count(self::$rows); }
    public function getPendingOrderEntries($offset, $limit) { return self::$rows; }
    public function getCompleteOrderEntries($offset, $limit) { return self::$rows; }
    public function getOrderDetail($id) { return self::$raw; }
}
require dirname(__DIR__) . '/classes/ctcl-html.php';
$html = new ctclHtml();
function invokeOrderMethod($name, $args = array()) {
    global $html;
    $method = new ReflectionMethod($html, $name);
    $method->setAccessible(true);
    return $method->invokeArgs($html, $args);
}
function check($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
}
$product = json_encode(array('itemName' => 'Shoe "Classic"', 'vari' => 'Blue', 'quantity' => 2, 'itemTotal' => 80));
$order = array('order_id' => '1790121600', 'products' => array($product),
    'payment_type' => 'Cash', 'shipping_type' => 'Pickup',
    'checkout-special-instruction' => 'Don’t remove "quotes" or C:\\orders');
$raw = json_encode($order);
check(invokeOrderMethod('decodeOrderJson', array($raw)) === $order, 'Valid nested JSON must retain escapes');
check(invokeOrderMethod('decodeOrderJson', array(addslashes($raw))) === $order, 'Legacy slashed JSON should decode');
foreach (array(null, '', '{bad', 'null', '[]', '"scalar"') as $invalid) {
    check(invokeOrderMethod('decodeOrderJson', array($invalid)) === null, 'Invalid JSON should be unavailable');
}
ctclProcessing::$rows = array(
    array('orderId' => '1790121600', 'orderDetail' => $raw),
    array('orderId' => '1790121601', 'orderDetail' => addslashes($raw)),
    array('orderId' => '1790121602', 'orderDetail' => null),
    array('orderId' => '1790121603', 'orderDetail' => json_encode(array('payment_type' => null, 'shipping_type' => array()))),
);
foreach (array('pendingOrderTab', 'completeOrderTab') as $tab) {
    ob_start(); invokeOrderMethod($tab); $output = ob_get_clean();
    check(strpos($output, 'Cash') !== false, 'Valid order must render');
    check(strpos($output, 'Don’t remove &quot;quotes&quot; or C:\\orders') !== false, 'Instructions must preserve content');
    check(strpos($output, '1790121602') !== false && strpos($output, 'saved data could not be read') !== false, 'Unreadable row must remain visible');
    check(strpos($output, '1790121603') !== false, 'Missing JSON order ID must use database ID');
}
$_POST['orderId'] = '1790121602';
foreach (array('getPendingOrderDetail', 'completeOrderDetail') as $method) {
    ctclProcessing::$raw = null;
    ob_start(); $html->$method(); $output = ob_get_clean();
    check(strpos($output, 'saved data could not be read') !== false, 'Unreadable detail should report failure');
    check(strpos($output, 'submit') === false, 'Unreadable detail must not offer order actions');
}
$detail = invokeOrderMethod('orderDisplayData', array($raw));
$detail['products'][] = '{broken';
ob_start(); invokeOrderMethod('createOrderListSection', array($detail)); $output = ob_get_clean();
check(strpos($output, 'Shoe &quot;Classic&quot;') !== false, 'Nested product JSON must render');
check(strpos($output, 'Product details are unavailable') !== false, 'Invalid product must not cause warnings');
echo "Order screen regression checks passed.\n";
