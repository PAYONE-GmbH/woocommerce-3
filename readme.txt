=== BS PAYONE WooCommerce ===
Contributors: bspayone
Donate link: https://www.bspayone.com/
Tags: woocommerce, payment
Requires at least: 4.6
Tested up to: 5.5
Stable tag: 1.7.1
Requires PHP: 5.6.0
License: MIT
License URI: https://opensource.org/licenses/MIT

This plugin connects your WooCommerce shop to the PAYONE payment API. You can receive payments through Credit Card, SEPA Direct Debit, and many more.

== Description ==

With the PAYONE Plugin for WooCommerce we aim to provide you with an easy to use payment plugin that supports the most relevant payment methods.
This plugin is thoroughly tested but still in an early stage, so any feedback is very much appreciated.

Seamless integration into the checkout. Supports simplified PCI DSS conformity in accordance with SAQ A.
Currently supported payment methods include:
* Credit Card
* SEPA Direct Debit
* PayPal
* Paydirekt
* Safe Invoice
* Sofort
* Giropay
* Invoice
* Prepayment

BS PAYONE GmbH is headquartered in Frankfurt am Main and is one of the leading omnichannel-payment providers in Europe. In addition to providing customer support to numerous Savings Banks (Sparkasse) the full-service payment service provider also provides cashless payment transaction services to more than 255,000 customers from stationary trade to the automated and holistic processing of e-commerce and mobile payments.

*To use this extension, a separate account with BS PAYONE is required. Additional costs incur. To create the account, please contact us through https://www.payone.com/en/contact/request-a-quote/.*

You are very welcome to contribute to this plugin on Github: https://github.com/PAYONE-GmbH/woocommerce-3

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/bs-payone-woocommerce` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Configure using the "BS PAYONE" Link in the left navigation tab

To successfully configure this plugin, you will need API credentials from BS PAYONE. Contact us through https://www.payone.com/en/contact/request-a-quote/ to get you set up.

All configuration steps are explained in our documentation that can be found at https://github.com/PAYONE-GmbH/docs.

== Frequently Asked Questions ==

= I need further assistance! =

Our customer service team is there to help. Just call them or drop them a line, you should have received the contact details with your account/test account.

= I have found a bug, what can I do? =

Please report all bugs on our Github repository at https://github.com/PAYONE-GmbH/woocommerce-3.

= I have found a really, really bad bug that shouldn't be publicly disclosed! =

If you have found anything security related, please contact our technical support team, or integrations@bspayone.com. Please bear in mind that support requests cannot be handled through that email address. We appreciate your cooperation.

== Screenshots ==


== Changelog ==

= 1.4.0 =
* Initial plugin store release

= 1.5.0 =

New Features

* more compatibility when querying order numbers
* New translations for German (informal) and Swiss
* Submit Customer IP and Shipping Data with every payment method

Bugfixes

* Discounts are now calculated correctly

Maintenance

* tested with Woocommerce 3.7
* tested with Wordpress 5.2

## A Word on PSD2

You can configure the plugin for optimized conversion when using Credit Cards and 3-D Secure 2.0. For more info see our Remark on docs.payone.com: https://docs.payone.com/display/public/INT/WooCommerce+Plugin#WooCommercePlugin-EnsuringMaximumConversionWith3DSecure2.0

= 1.6.0 =

New Features

* Use SKU in cart items where possible

Bugfixes

* Credit Card iFrames are now stylable (not just their spans)
* fixed a conflict with the third party amazon pay plugin
* use correct it[n] Parameters
* state and shipping_state only get sent when needed

Maintenance

* tested with Wordpress 5.3.2 and Woocommerce 3.8
* first Release which automatically syncs Github and Wordpress Store Releases

= 1.6.1 =

Maintenance

* fixed minor bug in CI Pipeline, no alterations to actual plugin code

= 1.6.2 =

Bugfixes

* fixed a bug where creditcard checkouts could break when using Germaized Plugin

Maintenance

* tested with woocommerce 4.0.0 and Wordpress 5.3.2

= 1.7.0 =

New Features

* New Payment Method: EPS
* B2B Mode for PAYONE safe Invoice

Bugfixes

* way better handling of appointed messages should improve customer feedback after redirect payments
* better rendering of credit card iFrames
* fixed some log errors

Maintenance

* tested with wordpress 5.5 and woocommerce 4.3.2

== 1.7.1 ==

Bugfixes

* fixed a bug where not all files of the plugin were uploaded to the wordpress store
