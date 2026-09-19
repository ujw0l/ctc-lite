<?php
/** Default confirmation email. Included by ctclHtml::createEmailBody(). */
$store = get_bloginfo('name');
$currency = strtoupper((string) get_option('ctcl_currency'));
$money = static function ($value) use ($currency) {
    return esc_html($currency . ' ' . number_format_i18n((float) $value, 2));
};
$t = static function ($text) { return esc_html__($text, 'ctc-lite'); };
$address = array_filter(array(
    trim(($data['ctcl-co-first-name'] ?? '') . ' ' . ($data['ctcl-co-last-name'] ?? '')),
    $data['checkout-street-address-1'] ?? '',
    $data['checkout-street-address-2'] ?? '',
    trim(($data['checkout-city'] ?? '') . ', ' . ($data['checkout-state'] ?? '') . ' ' . ($data['checkout-zip-code'] ?? ''), ', '),
    $data['checkout-country'] ?? '',
));
?>
<!doctype html>
<html lang="<?= esc_attr(get_bloginfo('language')) ?>" dir="<?= is_rtl() ? 'rtl' : 'ltr' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $t('Order confirmation') ?></title>
<style>@media only screen and (max-width:600px){.email-pad{padding:24px 20px!important}.email-title{font-size:28px!important}.email-shell{width:100%!important}}</style>
</head>
<body style="margin:0;padding:0;background:#f2f4f3;color:#20342e;font-family:Arial,Helvetica,sans-serif;-webkit-text-size-adjust:100%;">
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;"><?= $t('Thank you for your order. Your order details are enclosed.') ?></div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f2f4f3;"><tr><td align="center" style="padding:32px 12px;">
<!--[if mso]><table role="presentation" width="600"><tr><td><![endif]-->
<table role="presentation" class="email-shell" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;background:#ffffff;border:1px solid #dce4df;border-radius:16px;overflow:hidden;table-layout:fixed;">
<tr><td class="email-pad" style="padding:28px 36px;background:#e8ebed;color:#25313a;word-wrap:break-word;">
<p style="margin:0;font-size:19px;font-weight:bold;letter-spacing:1px;"><?= esc_html($store) ?></p>
<p style="margin:8px 0 0;color:#5d6870;font-size:11px;letter-spacing:2px;text-transform:uppercase;"><?= $t('Order confirmation') ?></p>
</td></tr>
<tr><td class="email-pad" style="padding:36px;word-wrap:break-word;">
<p style="margin:0 0 16px;color:#327259;font-size:12px;font-weight:bold;letter-spacing:1.5px;text-transform:uppercase;"><?= $t('Order received') ?></p>
<h1 class="email-title" style="margin:0 0 16px;font-size:34px;line-height:1.2;font-weight:bold;letter-spacing:-1px;"><?= $t('Thank you for your order.') ?></h1>
<p style="margin:0 0 8px;font-size:15px;line-height:1.7;"><?= esc_html(sprintf(__('Hello %s,', 'ctc-lite'), $data['ctcl-co-first-name'] ?? '')) ?></p>
<p style="margin:0;color:#64746d;font-size:15px;line-height:1.7;"><?= $t('We have received your order. Keep this email for your records; your order details are below.') ?></p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:24px;background:#f3f7f4;border:1px solid #e1e9e3;border-radius:8px;"><tr><td style="padding:16px 18px;font-size:12px;color:#64746d;">
<?= $t('ORDER NUMBER') ?><br><strong style="display:inline-block;margin-top:6px;font-size:17px;color:#20342e;word-break:break-all;">#<?= esc_html($data['order_id'] ?? '') ?></strong>
</td></tr></table>
<h2 style="margin:30px 0 16px;font-size:18px;"><?= $t('Your order') ?></h2>
<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;table-layout:fixed;font-size:14px;">
<thead><tr><th scope="col" align="left" style="width:65%;padding:0 8px 12px 0;border-bottom:1px solid #dce4df;color:#64746d;font-size:12px;font-weight:normal;"><?= $t('Item') ?></th><th scope="col" align="right" style="padding:0 0 12px;border-bottom:1px solid #dce4df;color:#64746d;font-size:12px;font-weight:normal;"><?= $t('Amount') ?></th></tr></thead>
<tbody>
<?php foreach (($data['products'] ?? array()) as $value):
    $product = is_array($value) ? $value : json_decode($value, true);
    if (!is_array($product) && is_string($value)) $product = json_decode(wp_unslash($value), true);
    if (!is_array($product)) continue;
    $variations = array_filter(array_map('trim', explode(',', $product['vari'] ?? '')), static function ($v) { return $v !== '' && strtoupper($v) !== 'N/A'; });
?>
<tr><td valign="top" style="padding:18px 12px 18px 0;border-bottom:1px solid #e7ece8;word-wrap:break-word;">
<strong style="font-size:15px;line-height:1.5;"><?= esc_html($product['itemName'] ?? '') ?></strong>
<?php if ($variations): ?><br><span style="color:#64746d;font-size:13px;line-height:1.8;"><?= esc_html(implode(' / ', $variations)) ?></span><?php endif; ?>
<br><span style="color:#64746d;font-size:12px;line-height:1.8;"><?= $t('Quantity') ?>: <?= esc_html($product['quantity'] ?? '') ?></span>
</td><td align="right" valign="top" style="padding:18px 0;border-bottom:1px solid #e7ece8;font-weight:bold;line-height:1.5;word-wrap:break-word;"><?= $money($product['itemTotal'] ?? 0) ?></td></tr>
<?php endforeach; ?>
</tbody></table>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:16px;font-size:14px;table-layout:fixed;">
<?php foreach (array('items-total' => 'Subtotal', 'shipping-total' => 'Shipping', 'tax-total' => 'Tax', 'total-discount' => 'Discount') as $key => $label): if (!isset($data[$key])) continue; ?>
<tr><td style="padding:7px 8px 7px 0;color:#64746d;"><?= $t($label) ?></td><td align="right" style="padding:7px 0;word-wrap:break-word;"><?= $key === 'total-discount' ? '&minus; ' : '' ?><?= $money($data[$key]) ?></td></tr>
<?php endforeach; ?>
<tr><td style="padding:18px 0 0;border-top:1px solid #dce4df;font-size:18px;font-weight:bold;"><?= $t('Order total') ?></td><td align="right" style="padding:18px 0 0;border-top:1px solid #dce4df;font-size:20px;font-weight:bold;word-wrap:break-word;"><?= $money($data['sub-total'] ?? 0) ?></td></tr>
</table>
<?php if ($address): ?>
<h2 style="margin:32px 0 12px;font-size:18px;"><?= $t('Contact address') ?></h2>
<p style="margin:0;color:#64746d;font-size:14px;line-height:1.8;"><?= implode('<br>', array_map('esc_html', $address)) ?></p>
<?php endif; ?>
<?php if (!empty($data['shipping_note'])): ?>
<h2 style="margin:24px 0 12px;font-size:18px;"><?= $t('Delivery / pickup details') ?></h2>
<div style="padding:16px;background:#f3f7f4;border-radius:8px;font-size:14px;line-height:1.7;color:#52675c;word-wrap:break-word;"><?= wp_kses_post($data['shipping_note']) ?></div>
<?php endif; ?>
<?php if (!empty($data['checkout-special-instruction'])): ?>
<h2 style="margin:24px 0 10px;font-size:16px;"><?= $t('Your order note') ?></h2>
<p style="margin:0;font-size:14px;line-height:1.7;color:#64746d;"><?= nl2br(esc_html($data['checkout-special-instruction'])) ?></p>
<?php endif; ?>
</td></tr>
<tr><td class="email-pad" style="padding:24px 36px;background:#f8faf8;border-top:1px solid #e7ece8;font-size:13px;line-height:1.7;color:#64746d;word-wrap:break-word;">
<strong style="color:#20342e;"><?= $t('Questions about your order?') ?></strong><br>
<?= $t('Contact the store and include your order number.') ?><br>
<a href="<?= esc_url(home_url('/')) ?>" style="display:inline-block;margin-top:12px;color:#245c45;font-weight:bold;text-decoration:underline;"><?= $t('Visit our store') ?></a>
</td></tr>
</table>
<!--[if mso]></td></tr></table><![endif]-->
<p style="margin:20px 0 0;font-size:12px;color:#74817a;"><?= esc_html($store) ?> &middot; <?= $t('Thank you for shopping with us.') ?></p>
</td></tr></table>
</body></html>
