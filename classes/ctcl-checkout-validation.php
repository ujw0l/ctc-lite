<?php
/** Resolve checkout values from published blocks, never from client prices. */
class ctclCheckoutValidation {
    private static function error() {
        return new WP_Error('ctcl_invalid_checkout', __('The cart could not be verified. Please refresh the product and checkout pages and try again.', 'ctc-lite'));
    }

    private static function blocks($postId, $name, $seen = array()) {
        if (!$postId || isset($seen[$postId])) { return array(); }
        $seen[$postId] = true;
        $post = get_post($postId);
        if (!$post || $post->post_status !== 'publish' || !empty($post->post_password)) { return array(); }
        return self::walk(parse_blocks($post->post_content), $name, $seen);
    }

    private static function walk($blocks, $name, $seen) {
        $result = array();
        foreach ($blocks as $block) {
            if ($block['blockName'] === $name) { $result[] = $block['attrs']; }
            if ($block['blockName'] === 'core/block' && !empty($block['attrs']['ref'])) {
                $result = array_merge($result, self::blocks((int) $block['attrs']['ref'], $name, $seen));
            }
            $result = array_merge($result, self::walk($block['innerBlocks'] ?? array(), $name, $seen));
        }
        return $result;
    }

    private static function amount($value) {
        return is_scalar($value) && is_numeric($value) && is_finite((float) $value) && (float) $value >= 0;
    }

    public static function validate($data) {
        if (!is_array($data['products'] ?? null) || !$data['products'] || count($data['products']) > 200) { return self::error(); }
        $products = array();
        $subtotal = 0;
        $shipping = 0;
        foreach ($data['products'] as $raw) {
            $item = is_string($raw) ? json_decode($raw, true) : null;
            if (!is_array($item) || !is_scalar($item['postId'] ?? null) || !ctype_digit((string) $item['postId']) || !is_scalar($item['quantity'] ?? null) || !ctype_digit((string) $item['quantity'])) { return self::error(); }
            $quantity = (int) $item['quantity'];
            if ($quantity < 1 || $quantity > 100000) { return self::error(); }
            $matches = array();
            foreach (self::blocks((int) $item['postId'], 'ctc-lite/ctc-lite-product-block') as $attrs) {
                if (($attrs['productName'] ?? 'Product Name') === ($item['itemName'] ?? null)) { $matches[] = $attrs; }
            }
            // Legacy carts identify products by post ID and name; ambiguous matches cannot be priced safely.
            if (count($matches) !== 1) { return self::error(); }
            $attrs = $matches[0];
            if (!empty($attrs['disableAddToCartBtn'])) { return self::error(); }
            $variations = is_string($item['vari'] ?? null) ? explode(',', $item['vari']) : array();
            if (count($variations) !== 2) { return self::error(); }
            $price = $attrs['productPrice'] ?? '0.00';
            foreach (array('variation1', 'variation2') as $index => $key) {
                $selected = $variations[$index];
                if ($selected === 'N/A') { continue; }
                $options = $attrs[$key] ?? array();
                $found = false;
                foreach ($options as $option) {
                    $parts = explode('~', $option['value'], 2);
                    if ($parts[0] === $selected) {
                        $found = true;
                        if ($index === 0) { $price = $parts[1] ?? $price; }
                        break;
                    }
                }
                if (!$found || count($options) < 2) { return self::error(); }
            }
            $delivery = $attrs['shippingCost'] ?? '0.00';
            if (!self::amount($price) || !self::amount($delivery)) { return self::error(); }
            $total = (float) $price * $quantity;
            if (!self::amount($item['itemTotal'] ?? null) || abs((float) $item['itemTotal'] - round($total, 2)) > 0.011) { return self::error(); }
            $subtotal += $total;
            $shipping += (float) $delivery * $quantity;
            $products[] = wp_json_encode(array('itemName' => $attrs['productName'] ?? 'Product Name', 'quantity' => $quantity, 'itemTotal' => number_format($total, 2, '.', ''), 'vari' => implode(',', $variations), 'postId' => (int) $item['postId']));
        }
        if ($data['shipping_option'] === 'store_pickup') { $shipping = 0; }
        $tax = $subtotal * (float) get_option('ctcl_tax_rate') / 100;
        $discount = 0;
        if (!empty($data['total-discount'])) {
            $pageId = is_scalar($data['ctcl_checkout_page'] ?? null) ? absint($data['ctcl_checkout_page']) : 0;
            $code = is_string($data['ctcl_coupon_code'] ?? null) ? $data['ctcl_coupon_code'] : '';
            $valid = false;
            foreach (self::blocks($pageId, 'ctc-lite/ctc-lite-checkout-block') as $cart) {
                if (!empty($cart['couponAvail']) && $code !== '' && ($cart['couponCode'] ?? '') === $code && self::amount($cart['amount'] ?? null) && (float) $cart['amount'] <= 100) {
                    $discount = $subtotal * (float) $cart['amount'] / 100;
                    $valid = true;
                    break;
                }
            }
            if (!$valid) { return self::error(); }
        }
        $totals = array('items-total' => $subtotal, 'shipping-total' => $shipping, 'tax-total' => $tax, 'total-discount' => $discount, 'sub-total' => $subtotal + $shipping + $tax - $discount);
        foreach ($totals as $key => $amount) {
            $submitted = $data[$key] ?? ($key === 'total-discount' ? 0 : null);
            if (!self::amount($submitted) || abs((float) $submitted - round($amount, 2)) > 0.011) { return self::error(); }
            $data[$key] = number_format($amount, 2, '.', '');
        }
        $data['products'] = $products;
        return $data;
    }
}
