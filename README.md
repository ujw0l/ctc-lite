# CT Commerce Lite

Build a small WordPress store with blocks for products, image galleries, a shopping cart, and checkout. Arrange product pages in the block editor and manage orders from the **CTC Lite** dashboard.

[WordPress.org](https://wordpress.org/plugins/ctc-lite/) · [Support](https://wordpress.org/support/plugin/ctc-lite/) · [Report an issue](https://github.com/ujw0l/ctc-lite/issues)

## What is included

- **Product block:** product image, pricing, quantity, two variation groups, optional variation prices and images, out-of-stock and pre-order settings.
- **Image Gallery block:** configurable width and main-image height, a live editor preview, responsive thumbnails, and a frontend image viewer.
- **Display in column:** a starting layout for a gallery, description, and product controls.
- **Cart block:** responsive order rows, coupon entry, totals, contact details, and shipping/payment selection. Shipping and payment panels are centered; coupon entry is compact and right-aligned.
- **Order Processing block:** processes the submitted checkout through the configured payment and shipping handlers.
- **Store settings:** currency, tax rate, cash on delivery, vendor delivery, and store pickup.
- **Order management:** pending and completed orders, customer details, vendor notes, and print controls.
- **Order emails:** SMTP settings and an HTML confirmation with item details, totals, and delivery notes. Branding uses the WordPress Site Title.

Card payments, PayPal, floating carts, sharing, ratings, analytics, and other integrations are separate add-ons. They are not bundled with the core plugin.

## Set up a store

1. Install and activate **CT Commerce Lite** through **Plugins → Add New Plugin**, or upload the plugin ZIP.
2. Open **CTC Lite** in the WordPress admin menu. Configure **Billing**, **Shipping**, and **Email**. Enable at least one payment method and one shipping method.
3. Create a product page. Add **Display in column**, or place **CTC Lite Image Gallery** and **CTC Lite Product** separately. Use **Add Product Detail** to set the product name, image, price, and variations.
4. Create a separate page containing **CTC Lite Order Processing**. Publish it and copy its URL. Keep it accessible, even if it is not in your navigation.
5. Create a checkout page containing **CTC Lite Cart**. Use **Add Checkout Detail** to enter the processing-page URL and configure the optional coupon.
6. Publish the product and checkout pages and add them to your navigation.
7. Place a controlled test order and confirm totals, shipping, payment behavior, and email delivery before opening the store to customers.

### Product images and gallery sizing

The product's main image and its gallery are configured separately. Set the product image in **Add Product Detail**; upload gallery images through **Select Gallery Images**. Set custom images for color/style variations if needed. A variation using the placeholder now keeps the product's main image.

Gallery settings control the outer width and the main-image height at that width. Thumbnails appear below. The image scales proportionally in a narrower column or screen, and the editor preview updates as you adjust the settings.

### Email setup

Enter your provider's SMTP host, authentication setting, port, credentials, encryption value, and permitted From address in **CTC Lite → Email**. Use the provider's documented settings; PHPMailer accepts `tls` for STARTTLS and `ssl` for implicit TLS. Use the built-in test-email control to check delivery.

A failed confirmation email does not turn a successful order into a failed checkout. The confirmation page shows the order ID and a message if sending fails. Check Pending Orders before retrying an order.

The email renderer is `ctclHtml::createEmailBody()` in `classes/ctcl-html.php`. Its default design is `templates/order-confirmation.php`. Use the `ctcl_custom_email_body` filter for a custom template; direct plugin edits are overwritten during updates.

## Optional add-ons

Install only the integrations your store needs. Each add-on has its own setup and compatibility requirements.

| Add-on | Link |
| --- | --- |
| Floating Cart | [WordPress.org](https://wordpress.org/plugins/ctcl-floating-cart/) |
| Sharing | [WordPress.org](https://wordpress.org/plugins/ctcl-sharing/) |
| Stripe | [WordPress.org](https://wordpress.org/plugins/ctcl-stripe/) |
| Phone Pay | [WordPress.org](https://wordpress.org/plugins/ctcl-phone-pay/) |
| PayPal | [WordPress.org](https://wordpress.org/plugins/ctcl-paypal/) |
| Product Display | [WordPress.org](https://wordpress.org/plugins/ctcl-product-display/) |
| Analytics | [WordPress.org](https://wordpress.org/plugins/ctcl-analytics/) |
| Order Status | [GitHub](https://github.com/ujw0l/ctcl-order-status) |
| SMS Notifications | [Developer store](https://payhip.com/b/bdiOx) |
| Custom Shipping | [Developer store](https://payhip.com/b/uZ4KU) |
| Variation Swatches | [Developer store](https://payhip.com/b/qr8fb) |
| Rating & Review | [Developer store](https://payhip.com/b/3PKa7) |

## Development and validation

The plugin ships plain PHP, JavaScript, and CSS. There is no frontend compilation step. Node.js is needed only for development checks; exclude `node_modules`, tests, and development files from the installable ZIP.

```sh
npm ci
npm test
php tests/email.php
php tests/activation.php /path/to/wordpress
```

The JavaScript suite covers image selection, cart persistence, totals, coupons, pickup, checkout navigation, and gallery dimensions. PHP checks cover email rendering, SMTP configuration, and order confirmation when email fails. These use isolated fixtures, not live payment providers or customer mailboxes. See [TESTING.md](TESTING.md) for the checks performed and remaining integration coverage.

## Updating

Back up your database and plugin files, then replace the plugin through WordPress's ZIP upload/update flow. Keep the plugin active during a file-only update: the legacy deactivation handler deletes the plugin's saved settings. Review settings if you deactivate/reactivate it. Existing cart entries retain their stored product data; re-add an item to pick up a corrected image.

Theme styles and add-ons can affect the layout. Check your product page, cart, and checkout on desktop and mobile after an update.

Updating this repository does not update the WordPress.org listing. That requires a separate WordPress.org release. The existing deployment workflow is triggered by version tags; do not create a release tag until the intended release has been reviewed.

## Support and license

For help, use the [support forum](https://wordpress.org/support/plugin/ctc-lite/). For reproducible bugs, open a [GitHub issue](https://github.com/ujw0l/ctc-lite/issues) with WordPress/PHP versions, active theme and relevant add-ons, steps to reproduce, and screenshots. Do not include passwords or private customer details.

Created by [Ujwol Bastakoti](https://github.com/ujw0l). Licensed under [GPLv2 or later](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html).
