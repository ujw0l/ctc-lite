=== CT Commerce Lite ===
Contributors: UjW0L
Donate link: https://www.paypal.com/donate/?hosted_button_id=JTGN7T2A9X3KW
Tags: ecommerce, online store, block, shopping cart, sell online
Requires at least: 5.5.2
Tested up to: 7.1
Stable tag: 2.8.3
Requires PHP: 7.4.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Create a WordPress store with product, gallery, cart, and checkout blocks. Includes order management, shipping options, coupons, and SMTP email settings.

== Description ==

CT Commerce Lite lets you build product pages and a checkout flow in the WordPress block editor. Configure your products, arrange their galleries, and manage orders from the CTC Lite dashboard.

= Build your store with blocks =

* CTC Lite Product: price, product image, quantity, variations, out-of-stock and pre-order settings.
* CTC Lite Image Gallery: customizable width and height, a live editor preview, thumbnails, and an image viewer.
* Display in column: a starting layout for a gallery, description, and product controls.
* CTC Lite Cart: responsive item summaries, coupons, totals, contact fields, and shipping/payment selection.
* CTC Lite Order Processing: connects checkout to the configured payment and shipping handlers.

= Core store features =

* Two variation groups, with optional variation-specific prices and images.
* Currency and tax-rate settings, cash on delivery, vendor delivery, and store pickup.
* Coupon configuration in the Cart block.
* Pending and completed order views, customer details, vendor notes, and print controls.
* SMTP settings and HTML order confirmations with your WordPress Site Title, order details, and delivery notes.
* Responsive product controls and cart layouts, centered shipping/payment panels, and compact coupon entry.

= Optional integrations =

Payments and other features can be extended through separate add-ons. The core plugin does not bundle card payments, PayPal, social sharing, ratings, analytics, or a floating cart.

* [Floating Cart](https://wordpress.org/plugins/ctcl-floating-cart/)
* [Sharing](https://wordpress.org/plugins/ctcl-sharing/)
* [Stripe](https://wordpress.org/plugins/ctcl-stripe/)
* [Phone Pay](https://wordpress.org/plugins/ctcl-phone-pay/)
* [PayPal](https://wordpress.org/plugins/ctcl-paypal/)
* [Product Display](https://wordpress.org/plugins/ctcl-product-display/)
* [Analytics](https://wordpress.org/plugins/ctcl-analytics/)
* [Order Status](https://github.com/ujw0l/ctcl-order-status)
* [SMS Notifications](https://payhip.com/b/bdiOx)
* [Custom Shipping](https://payhip.com/b/uZ4KU)
* [Variation Swatches](https://payhip.com/b/qr8fb)
* [Rating & Review](https://payhip.com/b/3PKa7)

Each add-on has its own setup and compatibility requirements. Review your theme and add-ons with a test order before using the store in production.

= Support =

Use the [WordPress.org support forum](https://wordpress.org/support/plugin/ctc-lite/) for setup questions or [GitHub issues](https://github.com/ujw0l/ctc-lite/issues) for reproducible bugs.

== Installation ==

1. Install CT Commerce Lite from Plugins > Add New Plugin, or upload its ZIP, then activate it.
2. Open CTC Lite in the admin menu. Configure Billing, Shipping, and Email. Enable a payment method and a shipping method.
3. Create a product page with Display in column, or add the Product and Image Gallery blocks individually. Open Add Product Detail and enter the product information.
4. Publish a separate page containing CTC Lite Order Processing and copy its URL.
5. Add CTC Lite Cart to a checkout page. Open Add Checkout Detail and set the processing-page URL. Configure a coupon if needed.
6. Publish your pages, add navigation links, and place a controlled test order before accepting customer orders.

== Frequently Asked Questions ==

= Do I need WooCommerce? =

No. CT Commerce Lite provides its own product blocks, cart, order processing, and order-management screens.

= Where do I configure checkout? =

In the CTC Lite Cart block, use Add Checkout Detail to set the URL of a published page containing CTC Lite Order Processing. That processing page must remain accessible, even if you leave it out of your navigation.

= How do I set the product image used in the cart? =

Set the main product image through Add Product Detail. Gallery images are configured separately. You can also assign custom variation images. Variations that use the default placeholder keep the main product image. Existing cart items retain their stored image until they are removed and added again.

= Can I resize the gallery? =

Yes. Select the gallery block and adjust Gallery width and Main image height. The editor previews changes immediately. Thumbnails sit below the main image; narrower screens scale the image proportionally.

= How do I configure confirmation emails? =

Enter your provider's SMTP settings under CTC Lite > Email and use the test-email control. The server must be reachable and your sender address must be permitted by the provider. A successful send does not guarantee inbox delivery. If sending fails after an order is placed, checkout shows the order ID and an email warning instead of crashing.

= Can I customize the confirmation email? =

The default email uses your WordPress Site Title and the order data. Developers can use the ctcl_custom_email_body filter to replace it. Direct edits to plugin files are overwritten during updates.

= What happens if I deactivate the plugin? =

The legacy deactivation handler removes saved store and SMTP settings. Back up your settings before deactivating. For a routine update, replace the plugin through WordPress's upload/update flow without manually deactivating it.

= Will it work with my theme? =

The blocks include responsive styles, but theme CSS, column widths, and add-ons can affect their appearance. Check product and checkout pages on both desktop and mobile with your chosen theme.

== Screenshots ==

1. Product page with gallery and purchase controls.
2. Cart and checkout page.
3. Billing and currency settings.
4. Shipping settings.
5. Email and SMTP settings.
6. Pending orders.
7. Order details.
8. Product block in the editor.
9. Cart block in the editor.
10. Order Processing block in the editor.

== Changelog ==

= 2.8.3 =
* Security: escape stored order fields and vendor notes in administration screens.
* Security: require administrator capability and a nonce for order-management and test-email AJAX requests.
* Security: prepare order and vendor-note database lookups.
* Security: reject client-supplied payment success and unavailable payment/shipping methods; validate checkout products and totals against published block settings.
* Refresh admin section headings, cards, forms, and responsive layouts.

= 2.8.2 =
* Fix order JSON decoding in pending and completed order screens, preserving escaped product data and quoted text.
* Show a clear message for unreadable orders without changing stored records.

= 2.8.1 =
* Fixed activation output when the orders table already exists by correcting the dbDelta schema format.
* Updated Tested up to metadata to WordPress 7.1.

= 2.8.0 =
* Refreshed responsive styling for product, gallery, cart, and checkout blocks.
* Added a reactive gallery editor preview and respected configured image dimensions.
* Improved narrow-column product layouts, control sizing, and content containment.
* Centered compact shipping/payment panels and right-aligned coupon entry.
* Preserved the product image when a variation uses the default placeholder.
* Fixed premature SMTP connections and authentication toggle handling.
* Kept successful order confirmation visible when email sending fails.
* Added an HTML order email with dynamic site branding and a gray header.
* Added regression checks and updated setup documentation.

= 2.7.0 =
* Previous published release.

= 2.6.1 =
- Bug fixes and improvements to the image overlay.

= 2.6.0 =
- Bug fixes

= 2.5.5 =
- Better looking UX for Checkout

= 2.5.0 =
- Added coupon code functionality
- Replaced sidebar with modal for order details
- Display blocks on top of the page
- Conditional variation display
- Added PayPal, SMS, and Custom Shipping Option add-ons
- Minor bug fixes

= 2.4.2 =
- Minor fixes

= 2.4.1 =
- Minor fixes

= 2.4.0 =
- Various bug fixes

= 2.3.5 =
- Improved gallery appearance

= 2.3.0 =
- Added block for displaying products and galleries in inner blocks

= 2.2.0 =
- Added sub-tabs with filters in the info tab

= 2.1.2 =
- Fixed data select issues

= 2.1.1 =
- Minor updates

= 2.1.0 =
- Added floating cart add-on
- Added out-of-stock option for product blocks
- Enabled refund processing from the admin section
- Minor bug fixes

= 2.0.2 =
- Minor bug fixes and enhancements

= 2.0.1 =
- Minor bug fixes

= 2.0.0 =
- Added different prices and images for variations
- Integrated CTC Lite Image Gallery block
- Added CTC Overlay for image viewing
- Made checkout page multipart
- Minor bug fixes

= 1.1.2 =
- Added missing translations

= 1.1.0 =
- Minor bug fixes

= 1.0.0 Beta =
- Minor bug fixes and tweaks

= 1.0.0 Alpha =

== Upgrade Notice ==

= 2.8.3 =
Security update for order management and checkout. Clear cached plugin scripts and refresh checkout/admin pages after installing.

= 2.8.2 =
Fixes PHP warnings in order management. Replace the installed plugin files to update.

= 2.8.1 =
Fixes activation output when an orders table already exists. Update in place to preserve plugin settings.

= 2.8.0 =
Updated storefront styles, gallery editor, order emails, and SMTP handling. Back up your store, update in place, and review checkout with your theme and add-ons.
