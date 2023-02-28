=== PAYONE WooCommerce ===
Contributors: PAYONE
Donate link: https://www.payone.com/
Tags: woocommerce, payment
Requires at least: 5.0
Tested up to: 6.1.1
Stable tag: 2.4.1
Requires PHP: 7.4.0
License: MIT
License URI: https://opensource.org/licenses/MIT

This plugin connects your WooCommerce shop to the PAYONE payment API. You can receive payments through Credit Card, Direct Debit and many more.

== Description ==

With the PAYONE Plugin for WooCommerce we aim to provide you with an easy to use payment plugin that supports the most relevant payment methods.
This plugin is thoroughly tested but still in an early stage, so any feedback is very much appreciated.

Seamless integration into the checkout. Supports simplified PCI DSS conformity in accordance with SAQ A.
Currently supported payment methods include:
* Credit Card
* PAYONE Direct Debit
* PayPal
* paydirekt
* PAYONE Secured Invoice
* PAYONE Secured Installment
* Sofort
* PAYONE Open Invoice
* PAYONE Prepayment
* Klarna
* Ratepay
* Trustly
* P24
* iDEAL
* Bancontact
* Alipay

PAYONE GmbH is headquartered in Frankfurt am Main and is one of the leading omnichannel-payment providers in Europe. In addition to providing customer support to numerous Savings Banks (Sparkasse) the full-service payment service provider also provides cashless payment transaction services to more than 255,000 customers from stationary trade to the automated and holistic processing of e-commerce and mobile payments.

*To use this extension, a separate account with PAYONE is required. Additional costs incur. To create the account, please contact us through https://www.payone.com/en/contact/request-a-quote/.*

You are very welcome to contribute to this plugin on Github: https://github.com/PAYONE-GmbH/woocommerce-3

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/payone-woocommerce` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Configure using the "PAYONE" Link in the left navigation tab

To successfully configure this plugin, you will need API credentials from PAYONE. Contact us through https://www.payone.com/en/contact/request-a-quote/ to get you set up.

All configuration steps are explained in our documentation that can be found at https://github.com/PAYONE-GmbH/docs.

== Frequently Asked Questions ==

= I need further assistance! =

Our customer service team is there to help. Just call them or drop them a line, you should have received the contact details with your account/test account.

= I have found a bug, what can I do? =

Please report all bugs on our Github repository at https://github.com/PAYONE-GmbH/woocommerce-3.

= I have found a really, really bad bug that shouldn't be publicly disclosed! =

If you have found anything security related, please contact our technical support team, or integrations@payone.com. Please bear in mind that support requests cannot be handled through that email address. We appreciate your cooperation.

== Screenshots ==


== Changelog ==

== 2.4.1 ==

Bugfixes

* Fixed problem with PAYONE Credit Card
* Fixed BNPL Token
* Fixed problem with rounding errors in checkout
 
Maintenance
 
* tested with

wordpress version: 6.1.1
woocommerce version: 7.4.0
php version: 8.1.0

== 2.4.0 ==

New Features

* New payment method: PAYONE Secured Invoice
* New payment method: PAYONE Secured Installment
* Implement a API test in backend
 
Maintenance
 
* Rename global js variables and functions
* Better error handling for options

tested with

wordpress version: 6.1.1
woocommerce version: 7.4.0
php version: 8.1.0

== 2.3.0 ==

New Features

* New payment method: Przelewy24
* New payment method: Trustly
* Removed field for BIC for PAYONE Direct Debit

Bugfixes

* Fixed article list for refunds
* Fixed translations

Maintenance

* Applying wordpress coding styleguide
* Removed subscription (wcs) support
* Removed checkbox for PAYONE Invoice module

tested with
wordpress version: 6.0.2
woocommerce version: 6.9.4
php version: 8.1

== 2.2.0 ==

New Features

* New payment method: Ratepay Open Invoice
* New payment method: Ratepay Direct Debit
* New payment method: Ratepay Installments

Bugfixes

* Parameter shipping_state is now transmitted for paydirekt orders
* Fixed custom parameters for credit card settings

Maintenance

* Payment methods no longer enabled by default
* Include landing page in backend

tested with
wordpress version: 6.0.1
woocommerce version: 6.8.0
php version: 8.1.0

== 2.1.0 ==

New Features
 
* New payment method: PayPal Express
* New payment method: Bancontact
* New payment method: iDEAL
* Using WC_API for callbacks
  The Transactionstatus URL has to be changed to <SHOPURL>/wc-api/payoneplugin/. 
  The old Transactionstatus URL is temporarily still reachable.
 
Bugfixes
 
* Better generating of unique reference numbers
* TX-Log corrections
 
tested with
wordpress version: 5.9.2
woocommerce version: 6.4.0

== 2.0.3 ==

Bugfixes

* payment method logos are now shown correctly in checkout

== 2.0.0 ==

New Features

* New payment method: Klarna (pay later, direct debit, slice it)
* New payment method: Alipay
* Payment Logos for every payment method in checkout
* Cardholder field for creditcard
* Experimental Support for WC subscribtions

Bugfixes

* fixed translations
* fixed txstatus handling
* fixed error message handling
* fixed broken plugin header
* fixed credit card settings

Maintenance

* Update names of the payment methods
* Update readme.txt
* PHP8 compatibility
* Rework PAYONE callback URL
* Rework creditcard fields
* Change activation hooks

Tested with:
wordpress version: 5.9.2
woocommerce version: 6.3.1

== 1.7.1 ==

Bugfixes

* fixed a bug where not all files of the plugin were uploaded to the wordpress store

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

= 1.6.1 =

Maintenance

* fixed minor bug in CI Pipeline, no alterations to actual plugin code

= 1.6.2 =

Bugfixes

* fixed a bug where creditcard checkouts could break when using Germaized Plugin

Maintenance

* tested with woocommerce 4.0.0 and Wordpress 5.3.2

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

= 1.4.0 =
* Initial plugin store release
