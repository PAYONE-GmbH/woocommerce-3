PAYONE Payment Plugin for WooCommerce
=====================================

[![Build Status](https://travis-ci.org/PAYONE-GmbH/woocommerce-3.svg?branch=master)](https://travis-ci.org/PAYONE-GmbH/woocommerce-3)
![GitHub](https://img.shields.io/github/license/PAYONE-GmbH/woocommerce-3)
![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/bs-payone-woocommerce)
![WordPress Plugin Rating](https://img.shields.io/wordpress/plugin/stars/bs-payone-woocommerce)

**The official PAYONE payment integration for WooCommerce. With our plugin you can integrate
numerous payment methods into your shop in just a few minutes.**

Important Features
------------------

 - Easy integration in your current checkout
 - Simplified PCI DSS conformity in accordance with SAQ A for credit card payments
 - Current supported payment methods: Credit Card, SEPA Direct Debit, PayPal, Paydirekt, Safe Invoice, Sofort, Giropay, Invoice, Prepayment

How to Get Started?
-------------------

Before you can start, you should check if all minimum requirements are met, these are:

 - At least PHP 5.6
 - Recent verion of WordPress 5
 - Recent version of the WooCommerce 3 plugin
 - PAYONE API credentials

### Stable Installation

We recommend installing the plugin via the official WordPress plugin website.
You can follow the usual installation process here:
[PAYONE Payment for WooCommerce](https://wordpress.org/plugins/bs-payone-woocommerce/)

### Development Installation

If you want to test the latest development progress, you can install the current
development state from the `master` branch of this repository.

**However, we do not recommend using this state in a live environment.**

 1. Download this repository
    ([ZIP](https://github.com/PAYONE-GmbH/woocommerce-3/archive/master.zip) /
    [TAR](https://github.com/PAYONE-GmbH/woocommerce-3/archive/master.tar.gz))
 2. Extract the contents of the downloaded archive
 3. Rename the extracted folder from `woocommerce-3-master` to `payone-woocommerce-3`
 4. Move the `payone-woocommerce-3` to your WordPress plugin directory (e.g. `wp-content/plugins`)
 5. Activate the plugin on your WordPress admin plugin page
