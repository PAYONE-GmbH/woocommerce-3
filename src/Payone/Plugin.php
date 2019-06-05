<?php

namespace Payone;

use Payone\Database\Migration;
use Payone\Gateway\GatewayBase;
use Payone\Gateway\SepaDirectDebit;
use Payone\Payone\Api\TransactionStatus;
use Payone\Transaction\Log;

class Plugin {
	const CALLBACK_SLUG = 'payone-callback';

	const PAYONE_IP_RANGES = [
		'213.178.72.196', '213.178.72.197', '217.70.200.0/24', '185.60.20.0/24'
	];

    /**
     * Wird benutzt, um die Capture-Mail zu verhindern, wenn das Capture nicht erfolgreich war.
     *
     * @var bool
     */
	public static $send_mail_after_capture = true;

	/**
	 * @todo Evtl. Zugriff über file_get_contents('php://input') realisieren, wenn der Server file_get_contents zulässt
	 *
	 * @return array
	 */
	public static function get_post_vars() {
		return $_POST;
	}

	public function init() {
		$migration = new Migration();
		$migration->run();

		if ( is_admin() ) {
			$settings = new \Payone\Admin\Settings();
			$settings->init();
		}

		$gateways = [
			\Payone\Gateway\CreditCard::GATEWAY_ID      => \Payone\Gateway\CreditCard::class,
			\Payone\Gateway\SepaDirectDebit::GATEWAY_ID => \Payone\Gateway\SepaDirectDebit::class,
			\Payone\Gateway\PrePayment::GATEWAY_ID      => \Payone\Gateway\PrePayment::class,
			\Payone\Gateway\Invoice::GATEWAY_ID         => \Payone\Gateway\Invoice::class,
			\Payone\Gateway\Sofort::GATEWAY_ID          => \Payone\Gateway\Sofort::class,
			\Payone\Gateway\Giropay::GATEWAY_ID         => \Payone\Gateway\Giropay::class,
			\Payone\Gateway\SafeInvoice::GATEWAY_ID     => \Payone\Gateway\SafeInvoice::class,
			\Payone\Gateway\PayPal::GATEWAY_ID          => \Payone\Gateway\PayPal::class,
			\Payone\Gateway\PayDirekt::GATEWAY_ID       => \Payone\Gateway\PayDirekt::class,
		];

		foreach ( $gateways as $gateway ) {
			add_filter( 'woocommerce_payment_gateways', [ $gateway, 'add' ] );
		}

		add_action( 'woocommerce_order_status_changed', [ $this, 'order_status_changed' ], 10, 3 );

		$plugin_rel_path = dirname( plugin_basename(__FILE__) ) . '/../../lang/';
		load_plugin_textdomain( 'payone-woocommerce-3', false, $plugin_rel_path);

		add_action( 'woocommerce_after_checkout_form', [ $this, 'add_javascript' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enque_javascript' ] );
		add_action( 'woocommerce_thankyou', [$this, 'add_content_to_thankyou_page'] );

        add_action( 'woocommerce_order_status_processing', [ $this, 'pre_disable_capture_mail_filter' ], 10, 2 );
        add_filter( 'woocommerce_email_enabled_customer_processing_order' , [ $this, 'disable_capture_mail_filter' ]);

		add_action( 'wp_head', [ $this, 'add_stylesheet' ] );
	}

    /**
     * Wenn die Bestellung den Status zu "processing" ändert und es sich um eine Bestellung mit "preauthorization"
     * handelt, wird ein Capture nur manuell erfolgen. Deshalb wird self::$send_mail_after_capture auf false
     * gesetzt, damit innerhalb von Capture::execute() nur die manuell verschickte Mail rausgeht - und dann auch nur,
     * wenn das Capture erfolgreich war.
     *
     * @param $order_id
     * @param $order
     */
    public function pre_disable_capture_mail_filter( $order_id, $order ) {
	    if ( ! $order ) {
	        $order = wc_get_order( $order_id );
        }

        $authorization_method = $order->get_meta( '_authorization_method' );
        if ( $authorization_method === 'preauthorization' ) {
            self::$send_mail_after_capture = false;
        }
    }

    /**
     * Sorgt dafür, dass über das Setzen von self::$send_mail_after_capture getriggert werden kann, ob die
     * processing-Mail verschickt wird, oder nicht.
     *
     * @param $value
     *
     * @return bool
     */
	public function disable_capture_mail_filter( $value ) {
        $screen = isset( $GLOBALS[ 'current_screen' ] ) ? $GLOBALS[ 'current_screen' ] : '';
        if ( $screen && $screen->id === 'woocommerce_page_wc-settings' ) {
	        return $value;
        }

        return $value && self::$send_mail_after_capture;
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	public static function get_callback_url( $type = 'transaction' ) {
		$url = get_home_url( null, self::CALLBACK_SLUG . '/' );
		if ($type !== 'transaction') {
			$url .= '?type=' . $type;
		}

		return esc_url( $url );
	}

	/**
	 * https://gist.github.com/tott/7684443
	 * 
	 * @param string $ip_address
	 * @param string $range
	 *
	 * @return bool
	 */
	public static function ip_address_is_in_range( $ip_address, $range ) {
	    if ( strpos( $ip_address, '::ffff:' ) === 0 ) {
	        $ip_address = substr( $ip_address, 7 );
        }

		if ( strpos( $range, '/' ) === false ) {
			$range .= '/32';
		}
		list( $range, $netmask ) = explode( '/', $range, 2 );
		$range_decimal    = ip2long( $range );
		$ip_decimal       = ip2long( $ip_address );
		$wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
		$netmask_decimal  = ~$wildcard_decimal;

		return ( $ip_decimal & $netmask_decimal ) === ( $range_decimal & $netmask_decimal );
	}

	public function add_callback_url() {
		add_rewrite_rule( '^' . self::CALLBACK_SLUG . '/?$', 'index.php?' . self::CALLBACK_SLUG . '=true', 'top' );
		add_filter( 'query_vars', [ $this, 'add_rewrite_var' ] );
		add_action( 'template_redirect', [ $this, 'catch_payone_callback' ] );
	}

	public function add_rewrite_var( $vars ) {
		$vars[] = self::CALLBACK_SLUG;

		return $vars;
	}

	public function catch_payone_callback() {
		if ( get_query_var( self::CALLBACK_SLUG ) ) {

			if ( $this->is_callback_after_redirect() ) {
				return $this->process_callback_after_redirect();
			} elseif ( $this->is_manage_mandate_callback() ) {
				return $this->process_manage_mandate_callback();
			} elseif ( $this->is_manage_mandate_getfile() ) {
				return $this->process_manage_mandate_getfile();
			}

			$response = 'ERROR';
			if ( $this->request_is_from_payone() ) {
				do_action( 'payone_transaction_callback' );

				try {
					$response = $this->process_callback();
				} catch (\Exception $e) {
					$response .= ' (' . $e->getMessage() . ')';
				}

				if ( $response === 'TSOK' ) {
					Log::constructFromPostVars();
				}
			}

			echo $response;
			exit();
		}
	}

	/**
	 * @return string
	 */
	public function process_callback() {
		$transaction_status = TransactionStatus::construct_from_post_parameters();
		
		if ( ! $transaction_status->has_valid_order() ) {
		    if ( ! apply_filters( 'payone_do_throw_error_on_invalid_order', true ) ) {
		        return 'TSOK';
            }

			return 'Order for reference ' . $transaction_status->get( 'reference' ) . ' not found';
		}

		$do_process_callback = true;
		$do_process_callback = apply_filters( 'payone_do_process_callback', $do_process_callback, $transaction_status );

		if ( $do_process_callback ) {
			$gateway = $transaction_status->get_gateway();
			if ( $transaction_status->get( 'key' ) === hash( 'md5', $gateway->get_key() ) ) {
				$gateway->process_transaction_status( $transaction_status );
			} else {
				return 'ERROR: Wrong key';
			}
		}

		return 'TSOK';
	}

	public function order_status_changed( $id, $from_status, $to_status ) {
		$order   = new \WC_Order( $id );
		$gateway = $this->get_gateway_for_order( $order );

		if ( method_exists( $gateway, 'order_status_changed' ) ) {
			$gateway->order_status_changed( $order, $from_status, $to_status );
		}
	}

	/**
	 * @return bool
	 */
	private function request_is_from_payone()
	{
		$ip_address = $_SERVER['REMOTE_ADDR'];

		$result = false;

		foreach ( self::PAYONE_IP_RANGES as $range ) {
			if ( self::ip_address_is_in_range( $ip_address, $range ) ) {
				$result = true;
				break;
			}
		}

		return apply_filters( 'payone_request_is_from_payone',  $result );
	}

	/**
	 * @return bool
	 */
	private function is_callback_after_redirect() {
		$allowed_redirect_types = [ 'success', 'error', 'back' ];
		if ( isset( $_GET['type'] ) && in_array( $_GET['type'], $allowed_redirect_types, true)
		     && isset( $_GET['oid'] ) && (int)$_GET['oid']
		) {
			return true;
		}

		return false;
	}

	/**
	 * @return array
	 */
	private function process_callback_after_redirect() {
		$order_id = (int)$_GET['oid'];

		$order = new \WC_Order( $order_id );
		$gateway = self::get_gateway_for_order( $order );

		return $gateway->process_payment( $order_id );
	}

	/**
	 * @return bool
	 */
	private function is_manage_mandate_callback() {
		if ( isset( $_GET['type'] ) && $_GET['type'] === 'ajax-manage-mandate') {
			return true;
		}

		return false;
	}

	/**
	 * @return array
	 */
	private function process_manage_mandate_callback() {
		$gateway = self::find_gateway( SepaDirectDebit::GATEWAY_ID );

		return $gateway->process_manage_mandate( $_POST );
	}

	/**
	 * @return bool
	 */
	private function is_manage_mandate_getfile() {
		if ( isset( $_GET['type'] ) && $_GET['type'] === 'manage-mandate-getfile') {
			return true;
		}

		return false;
	}

	/**
	 * @return array
	 */
	private function process_manage_mandate_getfile() {
		$gateway = self::find_gateway( SepaDirectDebit::GATEWAY_ID );

		return $gateway->process_manage_mandate_getfile( $_GET );
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return null|GatewayBase
	 */
	public static function get_gateway_for_order( \WC_Order $order ) {
		// @todo Was tun, wenn es das Gateway nicht gibt?
		return self::find_gateway( $order->get_payment_method() );
	}

	public function add_javascript() {
		if ( is_checkout() ) {
			include PAYONE_VIEW_PATH . '/gateway/common/checkout.js.php';
		}
	}

	public function enque_javascript() {
		if ( is_checkout() ) {
			wp_enqueue_script( 'payone_hosted', 'https://secure.pay1.de/client-api/js/v1/payone_hosted_min.js' );
		}
	}

	public function add_stylesheet() {
		if ( is_checkout() ) {
			echo "\n<style type='text/css'>\n";
			include PAYONE_VIEW_PATH . '/gateway/common/checkout.css';
			echo "\n</style>\n";
		}
	}

	public function add_content_to_thankyou_page( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order ) {
			$gateway = self::get_gateway_for_order( $order );
			if ( $gateway instanceof GatewayBase ) {
                $gateway->add_content_to_thankyou_page($order);
            }
		}
	}

    /**
     * @param array $item_data
     *
     * @return float
     */
    public static function get_tax_rate_for_item_data( $item_data ) {
	    $all_tax_classes = \WC_Tax::get_tax_classes();
	    $all_tax_classes[] = '';
        $all_tax_rates = [];
        foreach ( $all_tax_classes as $tax_class ) {
            $all_tax_rates[] = \WC_Tax::get_rates_for_tax_class( $tax_class );
        }

        if ( $item_data[ 'total_tax' ] == 0 ) {
            return 0.0;
        }

        $calculated_tax_rate = ( int ) ( 100 * round( 100 * $item_data[ 'total_tax' ] / $item_data[ 'total' ], 0 ) );

        foreach ( $all_tax_rates as $tax_rates ) {
            foreach ( $tax_rates as $tax_rate ) {
                $the_tax_rate = ( int ) round( 100 * $tax_rate->tax_rate, 0 );
                if ( $the_tax_rate === $calculated_tax_rate ) {
                    return $tax_rate->tax_rate;
                }
            }
        }

        return 0.0;
    }

    public static function sanitize_reference( $reference ) {
        $sanitized = preg_replace('/[^A-Za-z0-9-\.\/_\-]+/', '-', $reference);
        $sanitized = preg_replace('~-+~', '-', $sanitized);
        $sanitized = trim( $sanitized, '-');

        return $sanitized;
    }

	/**
	 * @param string $gateway_id
	 *
	 * @return null|GatewayBase
	 */
	private static function find_gateway( $gateway_id ) {
		$payment_gateways = WC()->payment_gateways->payment_gateways();
		foreach ( $payment_gateways as $payment_gateway_id => $payment_gateway ) {
			if ( $gateway_id === $payment_gateway_id ) {
				return $payment_gateway;
			}
		}

		return null;
	}
}
