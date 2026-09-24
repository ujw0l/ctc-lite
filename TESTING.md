# Validation report — 2.8.3

Validated on 19 September 2026 against the working changes based on commit `b6668c6`.

## Automated results

| Check | Result |
| --- | --- |
| JavaScript regressions (`npm test`, Node 20.12.1 / jsdom 22.1.0) | 8 passed |
| PHP email/SMTP contracts (`php tests/email.php`) | 9 passed on each of PHP 7.4.33, 8.3.14, and 8.4.1 |
| PHP syntax: main plugin, all classes, email template | Passed on PHP 7.4.33, 8.3.14, and 8.4.1 |
| JavaScript syntax: all shipped JavaScript files | Passed |

The JavaScript checks cover placeholder/custom variation images, independent product instances, add-to-cart persistence, quantity controls, cart thumbnails, localized mobile labels, tax/shipping totals, coupons, pickup totals, empty carts, checkout navigation, gallery dimensions, and reactive editor output. Saved product/gallery/cart markup is compared with the original version to guard against block-validation regressions. That check requires the repository's Git history, including `b6668c6`.

The PHP checks use WordPress function stubs. They cover dynamic site branding, escaped customer/product text, quantities and totals, unused variations, boolean SMTP authentication, configuration without a premature connection, and successful order confirmation when mail returns failure. No database writes or network mail are made by these checks.

## Browser checks

Used an isolated HTML fixture with the latest plugin CSS and frontend JavaScript, not the user's active cart or customer orders.

- At a 1280px viewport, the 223px product card and quantity controls had no horizontal content overflow.
- Shipping/payment panels measured 480px and were centered in the checkout.
- At a 320px viewport, both panels and the coupon area measured 280px inside 20px page margins. The document width stayed at 320px.
- Cart item labels appeared in the narrow layout; Next and Back changed checkout steps correctly.
- The email template was rendered in a browser with fictional order data and a sample site title.

## Limits and follow-up

These results are not a full WordPress integration, security, or accessibility certification. The `Tested up to` metadata is 7.1 as requested. The activation regression below uses the local WordPress 7.1.1 implementation; this is not a full compatibility certification.

Before a WordPress.org release, test installation/update and block editing in a staging WordPress site with the intended theme, then run an end-to-end order through the intended payment and shipping add-ons. Verify SMTP delivery and email layouts in the supported mail clients. Live payment gateways, external SMTP delivery, database order persistence, multiple themes, and all add-ons were not tested in this pass.

Keep updates separate from manual deactivation: the existing deactivation handler removes saved plugin settings. Back up first. The GitHub tag deployment workflow was not run; it also references an absent `npm run build` command and needs a separate release-workflow review before use.

## Activation regression — 2.8.1

The local WordPress log reported an existing orders table being created again during activation. The schema's missing space before `(` caused `dbDelta()` to parse the wrong table name. The schema now uses WordPress-compatible spacing and an explicitly named existing unique key.

`php tests/activation.php /path/to/wordpress` loads the installed WordPress `dbDelta()` function without bootstrapping the site. A database double exercises the actual activation callback. On PHP 8.3.14 and 8.4.1, fresh activation created one table with no output; repeat activation emitted no output or schema queries and preserved the order-data sentinel. No user database or plugin settings were changed.

The GitHub tag is a source release. Its commit skips the existing automatic deployment workflow; WordPress.org publishing has not been performed.

## Order screen regression — 2.8.2

`tests/orders.php` passes on PHP 7.4 and 8.4 with warnings treated as exceptions. Covers raw nested JSON, legacy slashed JSON, null/malformed data, missing fields, both order tables, unreadable detail actions, and nested product rendering. Tests use WordPress/database stubs; production records were not accessed or modified.

## Security regression — 2.8.3

The standalone PHP security suite tests denied low-privilege and nonce-less requests for all seven admin AJAX handlers, numeric order ID validation, prepared SQL arguments, ignored visitor payment results, unavailable payment methods, successful COD processing, canonical product/variation pricing, shipping, coupons, and escaped historical order data. Authorized mutations still call the database adapter. Uses WordPress and database stubs; no live payment, SMTP, customer records, or third-party add-ons were exercised.

Run `php tests/security.php`, `php tests/orders.php`, `php tests/email.php`, and `npm test`. PHP checks run with warnings treated as exceptions. The previously added admin CSS is included in this release.

Checkout keeps the existing post-ID/product-name cart format and validates it against published product blocks, including nested blocks and synced patterns. Unknown or ambiguous products, outdated prices, unpublished products, and invalid coupons fail closed with a refresh message. Custom integrations must register their enabled payment option and processor; server-side pricing extensions and third-party payment/shipping add-ons require integration testing before production deployment.
