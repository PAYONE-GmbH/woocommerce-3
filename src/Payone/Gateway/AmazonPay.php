<?php

namespace Payone\Gateway;

use Payone\Plugin;

class AmazonPay extends AmazonPayBase {

	const GATEWAY_ID = 'payone_amazonpay';
	const PUBLIC_KEY_ID = 'AE5E5B7B2SAERURYEH6DKDAZ';

	public function __construct() {
		parent::__construct( self::GATEWAY_ID );

		$this->icon               = PAYONE_PLUGIN_URL . 'assets/icon-amazon-pay.png';
		$this->method_title       = 'PAYONE ' . __( 'Amazon Pay', 'payone-woocommerce-3' );
		$this->method_description = '';
		$this->supports[]         = 'blocks';

		// Set a default description for better UX if none is configured
		if ( empty( $this->get_option( 'description' ) ) ) {
			$this->update_option( 'description', __( 'Sie werden zu Amazon weitergeleitet, um die Zahlung sicher zu autorisieren.', 'payone-woocommerce-3' ) );
		}
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		$is_available = parent::is_available();

		if ( $is_available ) {
			$is_available = Plugin::get_session_value( AmazonPayBase::SESSION_KEY_WORKORDERID ) === null
			  || Plugin::get_session_value( AmazonPayExpress::SESSION_KEY_AMAZONPAY_EXPRESS_USED ) === null;
		}

		return $is_available;
	}

	public function init_form_fields() {
		$this->init_common_form_fields( 'PAYONE ' . __( 'Amazon Pay', 'payone-woocommerce-3' ) );
		$this->add_amazon_merchant_id_field();
	}

	/**
	 * Check if we're in a Block-based checkout context.
	 * For Blocks, payment_fields() is NOT called - the Block component handles rendering.
	 * This method is only for Classic/Shortcode checkout.
	 *
	 * @return bool
	 */
	private function is_block_checkout() {
		// Check if WooCommerce Blocks utility exists
		if ( class_exists( '\Automattic\WooCommerce\Blocks\Package' ) &&
		     class_exists( '\Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils' ) ) {
			return \Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::is_checkout_block_default();
		}

		// Fallback: Check if checkout page has the block
		if ( function_exists( 'has_block' ) && is_checkout() ) {
			return has_block( 'woocommerce/checkout' );
		}

		return false;
	}

	public function payment_fields() {
		include PAYONE_VIEW_PATH . '/gateway/common/checkout-form-fields.php';

		// For Classic/Shortcode checkout: Don't show button, only description
		// User will be redirected to Amazon after clicking "Place order"
		// This matches PayPal behavior
		if ( ! $this->is_block_checkout() ) {
			// Show description for Classic checkout
			if ( $this->get_description() ) {
				echo '<div class="payone-amazonpay-description">';
				echo '<p>' . wp_kses_post( $this->get_description() ) . '</p>';
				echo '</div>';
			}
		} else {
			// For Blocks: Button is rendered by the Block component
			// This code path should not be reached, but include the form as fallback
			include PAYONE_VIEW_PATH . '/gateway/amazonpay/payment-form.php';
		}
	}

	/**
	 * @param int $order_id
	 *
	 * @return array
	 * @throws \WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		// Check if this is a Blocks checkout with existing workorderid from Amazon
		$workorderid = Plugin::get_session_value( self::SESSION_KEY_WORKORDERID );

		if ( $workorderid ) {
			// Blocks flow: workorderid already exists from blocks-success callback
			// Execute authorization with existing workorderid
			$transaction = new \Payone\Transaction\AmazonPayExpress( $this );
			$transaction->set( 'workorderid', $workorderid );

			$response = $transaction->execute( $order );

			// Store transaction data
			$order->set_transaction_id( $response->get( 'txid' ) );
			$order->add_meta_data( '_payone_userid', $response->get( 'userid', '' ) );
			$order->update_meta_data( '_authorization_method', $transaction->get( 'request' ) );
			$order->save();

			// Check if authorization was successful
			if ( $response->is_approved() ) {
				// Clear session data
				Plugin::delete_session_value( self::SESSION_KEY_WORKORDERID );
				Plugin::delete_session_value( self::SESSION_KEY_AMAZONPAY_SESSION_ID );

				// Handle successful payment
				$this->handle_successfull_payment( $order );

				return [
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				];
			} else {
				// Authorization failed
				$error_message = $response->get_error_message();
				wc_add_notice(
					__( 'Payment failed: ', 'payone-woocommerce-3' ) . $error_message,
					'error'
				);

				return [
					'result'   => 'failure',
					'messages' => $error_message,
				];
			}
		}

		// Classic checkout flow: redirect to button page
		Plugin::set_session_value( self::SESSION_KEY_ORDER_ID, $order_id );

		return [
			'result'   => 'success',
			'redirect' => Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'button' ] ),
		];
	}

	public function process_button( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order ) {
			$button_config = $this->create_button_config( $order );

			include PAYONE_VIEW_PATH . '/gateway/amazonpay/button-payment-form.php';
			exit;
		}

		wp_redirect( wc_get_checkout_url() );
		exit;
	}

	public function process_success( $order_id ) {
		$order = wc_get_order( $order_id );
		$this->handle_successfull_payment( $order );

		$target_url = $this->get_return_url( $order );

		wp_redirect( $target_url );
		exit;
	}

	public function create_button_config( \WC_Order $order) {
		$transaction = new \Payone\Transaction\AmazonPay( $this );
		$transaction
			->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'get-checkout' ] ) )
			->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'error' ] ) )
			->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'back' ] ) );

		$response = $transaction->execute( $order );
		Plugin::set_session_value( self::SESSION_KEY_WORKORDERID, $response->get( 'workorderid' ) );
		Plugin::delete_session_value( AmazonPayExpress::SESSION_KEY_AMAZONPAY_EXPRESS_USED );

		$order->update_meta_data( '_authorization_method', $transaction->get( 'request' ) );
		$order->set_transaction_id( $response->get( 'txid' ) );
		$order->save();

		return [
			'sandbox' => $this->get_mode() === 'test',
			'merchantId' => $this->get_amazon_merchant_id(),
			'publicKeyId' => self::PUBLIC_KEY_ID,
			'ledgerCurrency' => get_woocommerce_currency(),
			'checkoutLanguage' => get_locale(),
			'productType' => 'PayAndShip',
			'placement' => 'Cart',
			'buttonColor' => $this->get_button_color(),
			'estimatedOrderAmount' => [
				'amount' => $order->get_total( 'number' ),
				'currencyCode' => get_woocommerce_currency(),
			],
			'createCheckoutSessionConfig' => [
				'payloadJSON' => $response->get( 'add_paydata[payload]' ),
				'signature' => $response->get( 'add_paydata[signature]' ),
			],
		];
	}

	/**
	 * Process successful return from Amazon for Blocks checkout.
	 * This is called when Amazon redirects back after the user completes checkout.
	 *
	 * @param string $workorderid The workorder ID from the session
	 */
	public function process_blocks_get_checkout( $workorderid ) {
		if ( ! $workorderid ) {
			wc_add_notice( __( 'Payment session expired. Please try again.', 'payone-woocommerce-3' ), 'error' );
			wp_redirect( wc_get_checkout_url() );
			exit;
		}

		// Get checkout session details from PAYONE
		$transaction = new \Payone\Transaction\AmazonPayExpressGetCheckoutSession( $this );
		$transaction
			->set( 'workorderid', $workorderid )
			->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'blocks-success' ] ) )
			->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'blocks-error' ] ) )
			->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'blocks-back' ] ) );

		$response = $transaction->execute( WC()->cart );

		// Check if response is successful
		if ( ! $response || $response->is_error() ) {
			error_log( 'PAYONE AmazonPay Blocks: Get checkout session failed. Response: ' . print_r( $response ? $response->toArray() : 'null', true ) );
			wc_add_notice( __( 'Unable to retrieve payment information. Please try again.', 'payone-woocommerce-3' ), 'error' );
			wp_redirect( wc_get_checkout_url() );
			exit;
		}

		// Update WooCommerce customer data with Amazon Pay details
		WC()->customer->set_billing_first_name( $response->get( 'add_paydata[billing_firstname]' ) );
		WC()->customer->set_billing_last_name( $response->get( 'add_paydata[billing_lastname]' ) );
		WC()->customer->set_billing_company( '' );
		WC()->customer->set_billing_address_1( $response->get( 'add_paydata[billing_street]' ) );
		WC()->customer->set_billing_address_2( '' );
		WC()->customer->set_billing_city( $response->get( 'add_paydata[billing_city]' ) );
		WC()->customer->set_billing_state( '' );
		WC()->customer->set_billing_postcode( $response->get( 'add_paydata[billing_zip]' ) );
		WC()->customer->set_billing_country( $response->get( 'add_paydata[billing_country]' ) );
		WC()->customer->set_billing_phone( $response->get( 'add_paydata[billing_telephonenumber]' ) );
		WC()->customer->set_billing_email( $response->get( 'add_paydata[email]' ) );

		WC()->customer->set_shipping_first_name( $response->get( 'add_paydata[shipping_firstname]' ) );
		WC()->customer->set_shipping_last_name( $response->get( 'add_paydata[shipping_lastname]' ) );
		WC()->customer->set_shipping_company( '' );
		WC()->customer->set_shipping_address_1( $response->get( 'add_paydata[shipping_street]' ) );
		WC()->customer->set_shipping_address_2( '' );
		WC()->customer->set_shipping_city( $response->get( 'add_paydata[shipping_city]' ) );
		WC()->customer->set_shipping_state( '' );
		WC()->customer->set_shipping_postcode( $response->get( 'add_paydata[shipping_zip]' ) );
		WC()->customer->set_shipping_country( $response->get( 'add_paydata[shipping_country]' ) );
		WC()->customer->set_shipping_phone( $response->get( 'add_paydata[shipping_telephonenumber]' ) );
		WC()->customer->save();

		// Store Amazon session data for order processing
		Plugin::set_session_value( self::SESSION_KEY_AMAZONPAY_SESSION_ID, $response->get( 'add_paydata[amazonCheckoutSessionId]' ) );
		Plugin::set_session_value( self::SESSION_KEY_SELECT_GATEWAY, self::GATEWAY_ID );

		// Redirect to checkout page with Amazon Pay pre-selected
		wp_redirect( wc_get_checkout_url() . '?amazon_pay_return=1' );
		exit;
	}

	/**
	 * Process Blocks create session request.
	 * This is called via AJAX from the Block frontend.
	 *
	 * @return array Button configuration for Amazon Pay SDK
	 */
	public function process_blocks_create_session() {
		// Create checkout session from cart (Blocks don't have an order yet)
		$cart = WC()->cart;
		if ( ! $cart ) {
			return [
				'error' => __( 'Cart not found', 'payone-woocommerce-3' ),
			];
		}

		// Use Express transaction for cart-based sessions (same as Express checkout)
		$transaction = new \Payone\Transaction\AmazonPayExpressCreateCheckoutSession( $this );
		$transaction
			->set( 'successurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'blocks-success' ] ) )
			->set( 'errorurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'blocks-error' ] ) )
			->set( 'backurl', Plugin::get_callback_url( [ 'type' => 'amazonpay', 'a' => 'blocks-back' ] ) );

		$response = $transaction->execute( $cart );
		$workorderid = $response->get( 'workorderid' );

		// Validate response data
		$payload = $response->get( 'add_paydata[payload]' );
		$signature = $response->get( 'add_paydata[signature]' );

		if ( ! $payload || ! $signature ) {
			error_log( 'PAYONE AmazonPay Blocks: Missing payload or signature. Response: ' . print_r( $response->toArray(), true ) );
			return [
				'error' => __( 'Payment configuration error. Please try another payment method or contact support.', 'payone-woocommerce-3' ),
			];
		}

		Plugin::set_session_value( self::SESSION_KEY_WORKORDERID, $workorderid );
		Plugin::delete_session_value( AmazonPayExpress::SESSION_KEY_AMAZONPAY_EXPRESS_USED );

		return [
			'workorderId' => $workorderid,
			'sandbox' => $this->get_mode() === 'test',
			'merchantId' => $this->get_amazon_merchant_id(),
			'publicKeyId' => self::PUBLIC_KEY_ID,
			'ledgerCurrency' => get_woocommerce_currency(),
			'checkoutLanguage' => get_locale(),
			'productType' => 'PayAndShip',
			'placement' => 'Checkout',
			'buttonColor' => $this->get_button_color(),
			'createCheckoutSessionConfig' => [
				'payloadJSON' => $payload,
				'signature' => $signature,
			],
		];
	}
}
