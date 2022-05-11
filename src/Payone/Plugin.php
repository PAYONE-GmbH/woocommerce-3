<?php

namespace Payone;

use Payone\Database\Migration;
use Payone\Gateway\GatewayBase;
use Payone\Gateway\Invoice;
use Payone\Gateway\KlarnaBase;
use Payone\Gateway\KlarnaInstallments;
use Payone\Gateway\KlarnaInvoice;
use Payone\Gateway\KlarnaSofort;
use Payone\Gateway\PayPalExpress;
use Payone\Gateway\SepaDirectDebit;
use Payone\Payone\Api\TransactionStatus;
use Payone\Transaction\Log;
use Payone\WooCommerceSubscription\WCSHandler;

class Plugin {
    // @deprecated
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

        add_action( 'woocommerce_api_payoneplugin', [ $this, 'handle_callback' ] );

        // @deprecated
        add_action( 'init', [ $this, 'add_callback_url' ] );

		$gateways = [
			\Payone\Gateway\CreditCard::GATEWAY_ID         => \Payone\Gateway\CreditCard::class,
			\Payone\Gateway\SepaDirectDebit::GATEWAY_ID    => \Payone\Gateway\SepaDirectDebit::class,
			\Payone\Gateway\PrePayment::GATEWAY_ID         => \Payone\Gateway\PrePayment::class,
            \Payone\Gateway\Eps::GATEWAY_ID                => \Payone\Gateway\Eps::class,
			\Payone\Gateway\Invoice::GATEWAY_ID            => \Payone\Gateway\Invoice::class,
			\Payone\Gateway\Sofort::GATEWAY_ID             => \Payone\Gateway\Sofort::class,
			\Payone\Gateway\Giropay::GATEWAY_ID            => \Payone\Gateway\Giropay::class,
			\Payone\Gateway\SafeInvoice::GATEWAY_ID        => \Payone\Gateway\SafeInvoice::class,
			\Payone\Gateway\PayPal::GATEWAY_ID             => \Payone\Gateway\PayPal::class,
            \Payone\Gateway\PayPalExpress::GATEWAY_ID      => \Payone\Gateway\PayPalExpress::class,
			\Payone\Gateway\PayDirekt::GATEWAY_ID          => \Payone\Gateway\PayDirekt::class,
			\Payone\Gateway\Alipay::GATEWAY_ID             => \Payone\Gateway\Alipay::class,
            \Payone\Gateway\KlarnaInvoice::GATEWAY_ID      => \Payone\Gateway\KlarnaInvoice::class,
            \Payone\Gateway\KlarnaInstallments::GATEWAY_ID => \Payone\Gateway\KlarnaInstallments::class,
            \Payone\Gateway\KlarnaSofort::GATEWAY_ID       => \Payone\Gateway\KlarnaSofort::class,
            \Payone\Gateway\Bancontact::GATEWAY_ID         => \Payone\Gateway\Bancontact::class,
            \Payone\Gateway\Ideal::GATEWAY_ID              => \Payone\Gateway\Ideal::class,
		];

		foreach ( $gateways as $gateway ) {
			add_filter( 'woocommerce_payment_gateways', [ $gateway, 'add' ] );
		}

		add_action( 'woocommerce_order_status_changed', [ $this, 'order_status_changed' ], 10, 3 );

		$plugin_rel_path = dirname( plugin_basename(__FILE__) ) . '/../../lang/';
		load_plugin_textdomain( 'payone-woocommerce-3', false, $plugin_rel_path);

		add_action( 'woocommerce_after_checkout_form', [ $this, 'add_javascript' ] );
        add_action( 'woocommerce_after_cart', [ $this, 'add_javascript' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enque_javascript' ] );
		add_action( 'woocommerce_thankyou', [$this, 'add_content_to_thankyou_page'] );

        add_action( 'woocommerce_order_status_processing', [ $this, 'pre_disable_capture_mail_filter' ], 10, 2 );
        add_filter( 'woocommerce_email_enabled_customer_processing_order' , [ $this, 'disable_capture_mail_filter' ]);

		add_action( 'wp_head', [ $this, 'add_stylesheet' ] );

        add_action( 'woocommerce_order_details_after_order_table', [ $this, 'handle_woocommerce_order_details_after_order_table' ] );

        add_action( 'woocommerce_admin_order_data_after_order_details', [ $this, 'handle_woocommerce_admin_order_data_after_order_details' ] );

        if ( WCSHandler::is_wcs_active()
             && WCSHandler::is_payone_subscription_auto_failover_enabled()
             && WCSHandler::is_payone_gateway_is_available_and_subscritpion_aware( Invoice::GATEWAY_ID )
        ) {
            add_action(
                'woocommerce_subscription_renewal_payment_failed',
                [WCSHandler::class, 'process_woocommerce_subscription_renewal_payment_failed'],
                10,
                2
            );
        }
    }

    /**
     * @param \WC_Order $order
     */
    public function handle_woocommerce_order_details_after_order_table( $order ) {
        $gateway = self::get_gateway_for_order( $order );

        // Show only if PAYONE Gateway was used and there is non empty _invoiceid.
        if ( $gateway instanceof GatewayBase && $order->get_meta( '_invoiceid' ) !== '' ) {
            include PAYONE_VIEW_PATH . '/order/order-download-invoice.php';
        }
    }

    /**
     * @param \WC_Order $order
     */
    public function handle_woocommerce_admin_order_data_after_order_details( $order ) {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        $gateway = self::get_gateway_for_order( $order );

        // Show only if PAYONE Gateway was used and there is non empty _invoiceid.
        if ( $gateway instanceof GatewayBase && $order->get_meta( '_invoiceid' ) !== '' ) {
            include PAYONE_VIEW_PATH . '/admin/meta-boxes/order-download-invoice.php';
        }
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
	 * @param array $query
	 * @return string
	 */
	public static function get_callback_url( array $query ) {
        if ( get_option( 'permalink_structure' ) === '' ) {
            $url = site_url() . '/?wc-api=payoneplugin';
        } else {
            $url = site_url() . '/wc-api/payoneplugin/';
        }

		// Parse shop URL to operate on it
		$parsed_url = parse_url( $url );

		// Check if the shop URL could be parsed, return $url as fallback
		if ( !is_array( $parsed_url ) ) {
			error_log('Cannot build PAYONE callback URL, parse_url() fails to parse shop URL.');
			return $url;
		}

		$query_data = [];

		// If the shop URL contains a query string, parse it too
		if ( isset( $parsed_url['query'] ) ) {
			parse_str( $parsed_url['query'], $query_data );
		}

		// Make new query string from combined query data
		$parsed_url['query'] = http_build_query( array_merge( $query_data, $query ) );

		// Build URL from parts
		$url = self::unparse_url( $parsed_url );

		return $url;
	}

	/**
	 * Makes URL from parse_url data.
	 * @see https://www.php.net/manual/en/function.parse-url.php
	 *
	 * @param array $parsed_url Data as returned from parse_url.
	 * @return string The URL.
	 */
	private static function unparse_url( array $parsed_url ) {
		$scheme   = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '';
		$host     = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
		$port     = isset( $parsed_url['port'] ) ? ':' . $parsed_url['port'] : '';
		$user     = isset( $parsed_url['user'] ) ? $parsed_url['user'] : '';
		$pass     = isset( $parsed_url['pass'] ) ? ':' . $parsed_url['pass']  : '';
		$pass     = ( $user || $pass ) ? "$pass@" : '';
		$path     = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
		$query    = isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '';
		$fragment = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';

		return "{$scheme}{$user}{$pass}{$host}{$port}{$path}{$query}{$fragment}";
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

    // @deprecated
	public function add_callback_url() {
		add_rewrite_rule( '^' . self::CALLBACK_SLUG . '/?$', 'index.php?' . self::CALLBACK_SLUG . '=true', 'top' );
		add_filter( 'query_vars', [ $this, 'add_rewrite_var' ] );
		add_action( 'template_redirect', [ $this, 'catch_payone_callback' ] );
	}

    // @deprecated
	public function add_rewrite_var( $vars ) {
		$vars[] = self::CALLBACK_SLUG;

		return $vars;
	}

    // @deprecated
	public function catch_payone_callback() {
		if ( get_query_var( self::CALLBACK_SLUG ) ) {
            $this->handle_callback();
		}
	}

  public function handle_callback() {
      if ( $this->is_download_invoice_request() ) {
          return $this->process_callback_download_invoice();
      }
      if ( $this->is_callback_after_redirect() ) {
          return $this->process_callback_after_redirect();
      }
      if ( $this->is_manage_mandate_callback() ) {
          return $this->process_manage_mandate_callback();
      }
      if ( $this->is_manage_mandate_getfile() ) {
          return $this->process_manage_mandate_getfile();
      }
      if ( $this->is_klarna_start_session_callback() ) {
          return $this->process_klarna_start_session_callback();
      }
      if ( $this->is_paypal_express_set_checkout_callback() ) {
          return $this->process_paypal_express_set_checkout_callback();
      }
      if ( $this->is_paypal_express_get_checkout() ) {
          return $this->process_paypal_express_get_checkout();
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
					    Log::construct_from_post_vars();
			    }
	    }

      echo $response;
      exit();
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
		$order = new \WC_Order( $id );
		$gateway = self::get_gateway_for_order( $order );

        if ( $gateway && method_exists( $gateway, 'order_status_changed' ) ) {
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
    private function is_download_invoice_request() {
        return isset( $_GET['type'], $_GET['oid'] ) && $_GET['type'] === 'download-invoice' && ! empty( $_GET['oid'] );
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
     * @return array{status:string,message:string}
     */
    private function process_callback_download_invoice() {
        $order_id = (int) ( isset( $_GET['oid'] ) ? $_GET['oid'] : 0 );

        if ( $order_id < 1 ) {
            return [
                'status'  => 'error',
                'message' => __( 'Could not find order.', 'payone-woocommerce-3' ),
            ];
        }

        /** @var \WP_User|null $logged_in_user */
        $logged_in_user = wp_get_current_user();

        // User must be logged in.
        if ( ! $logged_in_user instanceof \WP_User ) {
            return [
                'status'  => 'error',
                'message' => __( 'User is not logged in.', 'payone-woocommerce-3' ),
            ];
        }

        $order = new \WC_Order( $order_id );
        /** @var \WP_User|false $order_user */
        $order_user = $order->get_user();

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            // Unless this was requested by a user that has 'manage_woocommerce' capability, check if this order
            // actually belongs to logged in user. Behave like order could not be found. Do not give out any info.
            if ( ! $order_user instanceof \WP_User || (int) $order_user->get( 'id' ) !== (int) $logged_in_user->get( 'id' ) ) {
                return [
                    'status'  => 'error',
                    'message' => __( 'Could not find order.', 'payone-woocommerce-3' ),
                ];
            }
        }

        $gateway = self::get_gateway_for_order( $order );

        if ( ! $gateway instanceof GatewayBase ) {
            return [
                'status'  => 'error',
                'message' => __( 'Could not get payment method for order.', 'payone-woocommerce-3' ),
            ];
        }

        if ( ! $gateway->is_payone_invoice_module_enabled() ) {
            return [
                'status'  => 'error',
                'message' => __( 'Invoice module is not enabled.', 'payone-woocommerce-3' ),
            ];
        }

        $splFileInfo = $gateway->get_invoice_for_order( $order );

        if ( ! $splFileInfo instanceof \SplFileInfo ) {
            return [
                'status'  => 'error',
                'message' => __( 'Could not get invoice from PAYONE gateway.', 'payone-woocommerce-3' ),
            ];
        }

        $filePath = (string) $splFileInfo->getRealPath();

        if ( ! file_exists( $filePath ) ) {
            return [
                'status'  => 'error',
                'message' => __( 'Could not find a file on server.', 'payone-woocommerce-3' ),
            ];
        }

        if ( ! is_readable( $filePath ) ) {
            return [
                'status'  => 'error',
                'message' => __( 'Could not read a file from server.', 'payone-woocommerce-3' ),
            ];
        }

        $finfo = finfo_open( FILEINFO_MIME_TYPE );
        header( sprintf( 'Content-Type: %s', (string) finfo_file( $finfo, $filePath ) ) );
        finfo_close( $finfo );

        header( sprintf( 'Content-Disposition: attachment; filename=%s', (string) basename( $filePath ) ) );
        header( 'Expires: 0' );
        header( 'Cache-Control: must-revalidate' );
        header( 'Pragma: public' );
        header( sprintf( 'Content-Length: %s', (string) filesize( $filePath ) ) );

        ob_clean();
        flush();

        readfile( $filePath );
        exit;
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
     * @return bool
     */
    private function is_klarna_start_session_callback() {
        if ( isset( $_GET['type'] ) && $_GET['type'] === 'ajax-klarna-start-session') {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    private function process_klarna_start_session_callback() {
        $payment_category = isset($_POST['category']) ? $_POST['category'] : null;
        unset($_POST['category']);

        $gateway_id = null;
        if ( $payment_category === 'pay_later' ) {
            $gateway_id = KlarnaInvoice::GATEWAY_ID;
        } elseif ( $payment_category === 'pay_over_time' ) {
            $gateway_id = KlarnaInstallments::GATEWAY_ID;
        } elseif ( $payment_category === 'direct_debit' ) {
            $gateway_id = KlarnaSofort::GATEWAY_ID;
        }

        if ( $gateway_id ) {
            $gateway = self::find_gateway( $gateway_id );
            if ( $gateway ) {
                set_transient( KlarnaBase::TRANSIENT_KEY_SESSION_STARTED, true, 60 * 20 );

                return $gateway->process_start_session($_POST);
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    private function is_paypal_express_set_checkout_callback() {
        if ( isset( $_GET['type'] ) && $_GET['type'] === 'ajax-paypal-express-set-checkout') {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    private function process_paypal_express_set_checkout_callback() {
        $gateway = self::find_gateway( PayPalExpress::GATEWAY_ID );
        if ( $gateway ) {
            return $gateway->process_set_checkout();
        }

        return null;
    }

    /**
     * @return bool
     */
    private function is_paypal_express_get_checkout() {
        if ( isset( $_GET['type'] ) && $_GET['type'] === 'paypal-express-get-checkout') {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    private function process_paypal_express_get_checkout() {
        $gateway = self::find_gateway( PayPalExpress::GATEWAY_ID );
        if ( $gateway ) {
            $workorderid = get_transient( PayPalExpress::TRANSIENT_KEY_WORKORDERID );

            return $gateway->process_get_checkout( $workorderid );
        }

        return null;
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
		} elseif ( is_cart() ) {
            include PAYONE_VIEW_PATH . '/gateway/common/cart.js.php';
        }
	}

	public function enque_javascript() {
		if ( is_checkout() ) {
			wp_enqueue_script( 'payone_hosted', 'https://secure.pay1.de/client-api/js/v1/payone_hosted_min.js' );
		}
	}

	public function add_stylesheet() {
		if ( is_checkout() ) {
			echo "\n<style>\n";
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
