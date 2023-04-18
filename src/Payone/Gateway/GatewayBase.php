<?php

namespace Payone\Gateway;

use Payone\Payone\Api\Request;
use Payone\Payone\Api\TransactionStatus;
use Payone\Transaction\Capture;
use Payone\Transaction\Debit;

abstract class GatewayBase extends \WC_Payment_Gateway {
	const SESSION_KEY_SELECT_GATEWAY = 'payone_select_gateway';

	/**
	 * @var array
	 */
	protected $global_settings;

	/**
	 * @var bool
	 */
	protected $hide_when_no_shipping;

	/**
	 * @var bool
	 */
	protected $hide_when_divergent_shipping_address;

	/**
	 * @var bool
	 */
	protected $hide_when_b2b;

	/**
	 * @var string[]
	 */
	protected $supported_countries;

	/**
	 * @var string[]
	 */
    protected $supported_currencies;

	/**
	 * @var string
	 */
	protected $test_transaction_classname;

	/**
	 * @var string 0 or 1
	 */
	private $use_global_settings;

	/**
	 * @var string
	 */
	private $authorization_method;

	/**
	 * @var float
	 */
	private $min_amount;

	/**
	 * @var float
	 */
	protected $min_amount_validation = 0;

	/**
	 * @var float
	 */
	protected $max_amount_validation = 0;

	/**
	 * @var string
	 */
	private $merchant_id;

	/**
	 * @var string
	 */
	private $portal_id;

	/**
	 * @var string
	 */
	private $account_id;

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var string 0 or 1
	 */
	private $submit_cart;

	/**
	 * @var string 0 or 1
	 */
	private $activate_pdf_download;

	/**
	 * @var string
	 */
	private $dynamic_invoice_text;

	/**
	 * @var string
	 */
	private $dynamic_refund_text;

	/**
	 * @var string
	 */
	private $text_on_booking_statement;

	public function __construct( $id ) {
		$this->id                                   = $id;
		$this->has_fields                           = true;
		$this->supports                             = [ 'products', 'refunds' ];
		$this->global_settings                      = get_option( \Payone\Admin\Option\Account::OPTION_NAME );
        $this->supported_countries                  = (new \WC_Countries())->get_countries();
        $this->supported_currencies                 = []; // all
		$this->hide_when_no_shipping                = false;
		$this->hide_when_divergent_shipping_address = false;
		$this->hide_when_b2b                        = false;
		$this->test_transaction_classname           = '';

		$this->init_settings();
		$this->init_form_fields();

		$this->title                = $this->get_option( 'title' );
		$this->authorization_method = $this->settings['authorization_method'];
		$this->min_amount           = $this->settings['min_amount'];
		$this->max_amount           = $this->settings['max_amount'];
		$this->countries            = $this->settings['countries'];

		$this->process_global_settings();

		add_action( 'woocommerce_settings_api_sanitized_fields_' . $this->id, [ $this, 'validate_admin_options' ] );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
		$this->display_errors();
	}

	public static function add( $methods ) {
		$methods[] = get_called_class();

		return $methods;
	}

	public function get_mode() {
		return $this->global_settings['mode'];
	}

	public function admin_options() {
		if ( ! $this->payone_api_settings_are_valid() ) {
            $this->add_error( __( 'Connection to PAYONE API failed', 'payone-woocommerce-3' ) );
			$this->display_errors();
		}

		parent::admin_options();
	}

	public function payone_is_testable() {
		return $this->test_transaction_classname
		       && method_exists( $this->test_transaction_classname, 'test_request_successful' );
	}

	/**
	 * @return bool
	 */
	public function payone_api_settings_are_valid() {
		$test_result = true;

		if ( $this->payone_is_testable() ) {
            $test_result = ( new $this->test_transaction_classname( $this ) )
                ->set( 'mode', 'test' )
                ->test_request_successful();

            if ( ! $test_result ) {
	            $this->enabled = 'no';
	            $this->settings['enabled'] = 'no';
	            $this->update_option( 'enabled', $this->enabled );
            }
		}

		return $test_result;
	}

    protected function after_payment_successful() {
    }

	/**
	 * @param TransactionStatus $transaction_status
	 */
	public function process_transaction_status( TransactionStatus $transaction_status ) {
		$order = $transaction_status->get_order();

		// Update sequence number of the order if the provided
		// through the TX status notification is larger
		$sequencenumber         = $transaction_status->get_sequencenumber();
		$current_sequencenumber = (int) $order->get_meta( '_sequencenumber' );
		if ( $sequencenumber > $current_sequencenumber ) {
			$order->update_meta_data( '_sequencenumber', $sequencenumber );
			$order->save_meta_data();
		}

		if ( $transaction_status->is_invoice() ) {
			$invoice_id = $transaction_status->get_string( 'invoiceid' );
			$order->add_order_note( sprintf( '%s (Invoice ID: %s)', __( 'Order is invoiced on PAYONE.', 'payone-woocommerce-3' ), $invoice_id ) );
			$order->update_meta_data( '_invoiceid', $invoice_id );
			$order->save_meta_data();
		}

		if ( $transaction_status->is_appointed() ) {
			// Just log and flag order meta that we got an APPOINTED TX status notification
			$order->add_order_note( __( 'Received status APPOINTED from PAYONE.', 'payone-woocommerce-3' ) );
			$order->update_meta_data( '_appointed', time() );
			$order->save_meta_data();
		} elseif ( $transaction_status->is_refund() ) {
			// Refund the order if not already happened and we got a DEBIT TX status notification
			$is_already_refunded = $order->get_meta( '_refunded' );
			if ( ! $is_already_refunded ) {
				$order->update_status( 'wc-refunded', __( 'Received status DEBIT from PAYONE.', 'payone-woocommerce-3' ) );
				$order->update_meta_data( '_refunded', time() );
				$order->save_meta_data();
			}
		} elseif ( $transaction_status->is_cancelation() || $transaction_status->is_failed() ) {
			// Set order status to failed if we get a CANCELED of FAILED TX status notification
			$order->update_status( 'wc-failed', __( 'Received status CANCELATION from PAYONE with reason: ', 'payone-woocommerce-3' ) . $transaction_status->get( 'failedcause' ) );
		} elseif ( $transaction_status->is_invoice() ) {
			$order->add_order_note( __( 'The PAYONE platform has generated a receipt for this order', 'payone-woocommerce-3' ) );
		} elseif ( $transaction_status->is_reminder() ) {
			$order->add_order_note( __( 'The PAYONE dunning status has changed', 'payone-woocommerce-3' ) );
		} elseif ( $transaction_status->is_transfer() ) {
			$order->add_order_note( __( 'The PAYONE platform has registered a rebooking', 'payone-woocommerce-3' ) );
		}
	}

	/**
	 * @param \WC_Order $order
	 */
	public function add_content_to_thankyou_page( \WC_Order $order ) {
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return null|\Payone\Payone\Api\Response
	 */
	public function capture( \WC_Order $order ) {
		$capture = new Capture( $this );
		$this->add_data_to_capture( $capture, $order );

		return $capture->execute( $order );
	}

    protected function get_error_message( \Payone\Payone\Api\Response $response ) {
        return __( 'Payment error: ', 'payone-woocommerce-3' ) . $response->get_error_message();
    }

	protected function add_data_to_capture( Capture $capture, \WC_Order $order ) {
	}

	/**
	 * @param int $order_id
	 * @param float|null $amount
	 * @param string $reason
	 *
	 * @return bool|\WP_Error
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		if ( (float) $amount <= 0.0 ) {
			return new \WP_Error( 1, __( 'Debit amount must be greater than zero.', 'payone-woocommerce-3' ) );
		}
		$order = new \WC_Order( $order_id );
		// The first item in the array is the refund for this call
		$refund = $order->get_refunds()[0];

		$order->add_order_note( __( 'Refund is issued through PAYONE', 'payone-woocommerce-3' ) );

		$debit = new Debit( $this );
		$debit->set_refund( $refund );
		$this->add_data_to_debit( $debit, $order );

		return $debit->execute( $order, - $amount );
	}

	protected function add_data_to_debit( Debit $capture, \WC_Order $order ) {
	}

	/**
	 * @return bool
	 */
	public function is_available() {
		$is_available = parent::is_available();

		if ( $is_available && WC()->cart && $this->min_amount > $this->get_order_total() ) {
			$is_available = false;
		}

        if ( $is_available && $this->supported_currencies ) {
            $is_available = in_array( get_woocommerce_currency(), $this->supported_currencies, true );
        }

		if ( $is_available && $this->hide_when_no_shipping ) {
			if ( ! wc_shipping_enabled() || wc_get_shipping_method_count() < 1 ) {
				$is_available = false;
			}
		}

		if ( $is_available && $this->hide_when_b2b && $this->is_b2b() ) {
			$is_available = false;
		}

		if ( $is_available && $this->hide_when_divergent_shipping_address && $this->has_divergent_shipping_address() ) {
			$is_available = false;
		}

		if ( $is_available ) {
			$order_id = absint( get_query_var( 'order-pay' ) );

			if ( $order_id ) {
				$order   = wc_get_order( $order_id );
				$country = (string) $order->get_billing_country();
			} elseif ( WC()->customer && WC()->customer->get_billing_country() ) {
				$country = (string) WC()->customer->get_billing_country();
			} else {
				$country = '';
			}

			$is_available = in_array( $country, $this->countries, true );
		}

		return $is_available;
	}

	/**
	 * @param string $label
	 */
	public function init_common_form_fields( $label ) {
		$default_merchant_id = isset( $this->global_settings['merchant_id'] ) ? $this->global_settings['merchant_id'] : '';
		$default_portal_id   = isset( $this->global_settings['portal_id'] ) ? $this->global_settings['portal_id'] : '';
		$default_account_id  = isset( $this->global_settings['account_id'] ) ? $this->global_settings['account_id'] : '';
		$default_key         = isset( $this->global_settings['key'] ) ? $this->global_settings['key'] : '';

		$this->form_fields = [
			'enabled'                   => [
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this payment method', 'payone-woocommerce-3' ),
				'default' => 'no',
			],
			'title'                     => [
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => $label,
				'desc_tip'    => true,
			],
			'description'               => [
				'title'   => __( 'Customer Message', 'payone-woocommerce-3' ),
				'type'    => 'textarea',
                'css'       => 'width: 400px;',
				'default' => '',
			],
			'min_amount'                => [
				'title'   => __( 'Minimum order value', 'payone-woocommerce-3' ),
				'type'    => 'text',
				'default' => $this->min_amount_validation,
			],
			'max_amount'                => [
				'title'   => __( 'Maximum order value', 'payone-woocommerce-3' ),
				'type'    => 'text',
				'default' => $this->max_amount_validation,
			],
			'authorization_method'      => [
				'title'   => __( 'Method of Authorization', 'payone-woocommerce-3' ),
				'type'    => 'select',
				'options' => [
					'authorization'    => __( 'Authorization', 'payone-woocommerce-3' ),
					'preauthorization' => __( 'Preauthorization', 'payone-woocommerce-3' ),
				],
				'default' => 'authorization',
			],
			'countries'                 => [
				'title'   => __( 'Active Countries', 'payone-woocommerce-3' ),
				'type'    => 'cc_countries',
				'options' => $this->supported_countries,
				'default' => [ 'DE', 'AT', 'CH' ],
			],
			'use_global_settings'       => [
				'title'   => __( 'Use global settings', 'payone-woocommerce-3' ),
				'type'    => 'select',
				'options' => [
					'0' => __( 'No', 'payone-woocommerce-3' ),
					'1' => __( 'Yes', 'payone-woocommerce-3' ),
				],
				'default' => '1',
			],
			'merchant_id'               => [
				'title'   => __( 'Merchant ID', 'payone-woocommerce-3' ),
				'type'    => 'text',
				'default' => $default_merchant_id,
			],
			'portal_id'                 => [
				'title'   => __( 'Portal ID', 'payone-woocommerce-3' ),
				'type'    => 'text',
				'default' => $default_portal_id,
			],
			'account_id'                => [
				'title'   => __( 'Account ID', 'payone-woocommerce-3' ),
				'type'    => 'text',
				'default' => $default_account_id,
			],
			'key'                       => [
				'title'   => __( 'Key', 'payone-woocommerce-3' ),
				'type'    => 'text',
				'default' => $default_key,
			],
			'submit_cart'               => [
				'title'   => __( 'Submit cart', 'payone-woocommerce-3' ),
				'type'    => 'select',
				'options' => [
					'0' => __( 'No', 'payone-woocommerce-3' ),
					'1' => __( 'Yes', 'payone-woocommerce-3' ),
				],
				'default' => '0',
			],
			'activate_pdf_download'     => [
				'title'   => __( 'Activate PDF download', 'payone-woocommerce-3' ),
				'type'    => 'select',
				'options' => [
					'0' => __( 'No', 'payone-woocommerce-3' ),
					'1' => __( 'Yes', 'payone-woocommerce-3' ),
				],
				'default' => '0',
			],
			'dynamic_invoice_text'      => [
				'title'   => __( 'Dynamic invoice text', 'payone-woocommerce-3' ),
				'type'    => 'text',
				'default' => __( 'Your order No. {{order}}', 'payone-woocommerce-3' ),
			],
			'dynamic_refund_text'       => [
				'title'   => __( 'Dynamic refund text', 'payone-woocommerce-3' ),
				'type'    => 'text',
				'default' => __( 'Your refund No. {{order}}', 'payone-woocommerce-3' ),
			],
			'text_on_booking_statement' => [
				'title'   => __( 'Text on booking statement', 'payone-woocommerce-3' ),
				'type'    => 'text',
				'default' => __( 'Your refund No. {{order}}', 'payone-woocommerce-3' ),
			],
		];

		if ( $this->id === PrePayment::GATEWAY_ID ) {
			unset( $this->form_fields['authorization_method']['options']['authorization'] );
			$this->form_fields['authorization_method']['default'] = 'preauthorization';
		}
	}

	public function validate_admin_options( $options ) {
		if ( (int) round( $this->min_amount_validation ) !== 0 ) {
			$min_amount = isset( $options['min_amount'] ) ? $options['min_amount'] : $this->min_amount_validation;
			if ( $min_amount < $this->min_amount_validation ) {
				\WC_Admin_Settings::add_error( sprintf( __( 'The minimum order value must not be lower than %d', 'payone-woocommerce-3' ), $this->min_amount_validation ) );
				$options['min_amount'] = $this->min_amount_validation;
			}
		}
		if ( (int) round( $this->max_amount_validation ) !== 0 ) {
			$max_amount = isset( $options['max_amount'] ) ? $options['max_amount'] : $this->max_amount_validation;
			if ( $max_amount > $this->max_amount_validation || $max_amount < 0.01 ) {
				\WC_Admin_Settings::add_error( sprintf( __( 'The maximum order value must not be higher than %d', 'payone-woocommerce-3' ), $this->max_amount_validation ) );
				$options['max_amount'] = $this->max_amount_validation;
			}
		}

		return $options;
	}

	/**
	 * @return string
	 */
	public function get_authorization_method() {
		return $this->authorization_method;
	}

	/**
	 * @return string
	 */
	public function get_merchant_id() {
		return $this->merchant_id;
	}

	/**
	 * @return string
	 */
	public function get_portal_id() {
		return $this->portal_id;
	}

	/**
	 * @return string
	 */
	public function get_account_id() {
		return $this->account_id;
	}

	/**
	 * @return string
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * @return bool
	 */
	public function should_submit_cart() {
		return $this->submit_cart === '1';
	}

	/**
	 * @return string
	 */
	public function get_activate_pdf_download() {
		return $this->activate_pdf_download;
	}

	/**
	 * @return string
	 */
	public function get_dynamic_invoice_text() {
		return $this->dynamic_invoice_text;
	}

	/**
	 * @return string
	 */
	public function get_dynamic_refund_text() {
		return $this->dynamic_refund_text;
	}

	/**
	 * @return string
	 */
	public function get_text_on_booking_statement() {
		return $this->text_on_booking_statement;
	}

	protected function add_email_meta_hook( $callable = null ) {
		if ( $callable ) {
			add_action( 'woocommerce_email_order_meta', $callable, 10, 3 );
		}
	}

	private function process_global_settings() {
		$this->use_global_settings = $this->settings['use_global_settings'];
		if ( $this->use_global_settings ) {
			unset (
				$this->form_fields['merchant_id'],
				$this->form_fields['portal_id'],
				$this->form_fields['account_id'],
				$this->form_fields['key'],
				$this->form_fields['submit_cart'],
				$this->form_fields['activate_pdf_download'],
				$this->form_fields['dynamic_invoice_text'],
				$this->form_fields['dynamic_refund_text'],
				$this->form_fields['text_on_booking_statement']
			);
		}
		if ( $this->use_global_settings ) {
			$this->merchant_id               = isset( $this->global_settings['merchant_id'] ) ? $this->global_settings['merchant_id'] : '';
			$this->portal_id                 = isset( $this->global_settings['portal_id'] ) ? $this->global_settings['portal_id'] : '';
			$this->account_id                = isset( $this->global_settings['account_id'] ) ? $this->global_settings['account_id'] : '';
			$this->key                       = isset( $this->global_settings['key'] ) ? $this->global_settings['key'] : '';
			$this->submit_cart               = isset( $this->global_settings['submit_cart'] ) ? $this->global_settings['submit_cart'] : '';
			$this->activate_pdf_download     = isset( $this->global_settings['activate_pdf_download'] ) ? $this->global_settings['activate_pdf_download'] : '';
			$this->dynamic_invoice_text      = isset( $this->global_settings['dynamic_invoice_text'] ) ? $this->global_settings['dynamic_invoice_text'] : '';
			$this->dynamic_refund_text       = isset( $this->global_settings['dynamic_refund_text'] ) ? $this->global_settings['dynamic_refund_text'] : '';
			$this->text_on_booking_statement = isset( $this->global_settings['text_on_booking_statement'] ) ? $this->global_settings['text_on_booking_statement'] : '';
		} else {
			$this->merchant_id               = isset( $this->settings['merchant_id'] ) ? $this->settings['merchant_id'] : '';
			$this->portal_id                 = isset( $this->settings['portal_id'] ) ? $this->settings['portal_id'] : '';
			$this->account_id                = isset( $this->settings['account_id'] ) ? $this->settings['account_id'] : '';
			$this->key                       = isset( $this->settings['key'] ) ? $this->settings['key'] : '';
			$this->submit_cart               = isset( $this->settings['submit_cart'] ) ? $this->settings['submit_cart'] : '';
			$this->activate_pdf_download     = isset( $this->settings['activate_pdf_download'] ) ? $this->settings['activate_pdf_download'] : '';
			$this->dynamic_invoice_text      = isset( $this->settings['dynamic_invoice_text'] ) ? $this->settings['dynamic_invoice_text'] : '';
			$this->dynamic_refund_text       = isset( $this->settings['dynamic_refund_text'] ) ? $this->settings['dynamic_refund_text'] : '';
			$this->text_on_booking_statement = isset( $this->settings['text_on_booking_statement'] ) ? $this->settings['text_on_booking_statement'] : '';
		}
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \SplFileInfo|null
	 */
	public function get_invoice_for_order( $order ) {
		$invoice_id = (string) $order->get_meta( '_invoiceid' );
		$invoice_id = trim( $invoice_id );

		if ( empty( $invoice_id ) ) {
			return null;
		}

		$request = new Request();
		$request->set( 'request', 'getinvoice' );
		$request->set( 'invoice_title', $invoice_id );
		$response = $request->submit();

		if ( ! $response->is_ok() ) {
			wc_add_notice( $this->get_error_message( $response ), 'error' );

			return null;
		}

		$pdfFilePath = sprintf( '%s/Invoice.%s.pdf', sys_get_temp_dir(), $invoice_id );

		$bytes = file_put_contents( $pdfFilePath, $response->get( '_DATA' ) );

		if ( $bytes === false || $bytes < 1 ) {
			return null;
		}

		return new \SplFileInfo( $pdfFilePath );
	}

	/**
	 * This is a copy of $this->generate_select_html(), but without the table_markup
	 *
	 * @param string $key
	 * @param array $data
	 *
	 * @return string
	 */
	public function generate_select_html_without_table_markup( $key, $data, $title = '&nbsp;' ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'options'           => array(),
		);

		$data = wp_parse_args( $data, $defaults );
		ob_start();
		?>
        <fieldset>
            <label style="display: block; font-weight: bold;"><?php echo ( isset( $title ) ? $title : $data['title'] ); ?></label>
            <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
            <select class="select <?php echo esc_attr( $data['class'] ); ?>"
                    name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>"
                    style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>>
				<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
                    <option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, esc_attr( $this->get_option( $key ) ) ); ?>><?php echo esc_attr( $option_value ); ?></option>
				<?php endforeach; ?>
            </select>
			<?php echo $this->get_description_html( $data ); ?>
        </fieldset>
		<?php

		return ob_get_clean();
	}

	/**
	 * This is a copy of $this->generate_text_html(), but without the table_markup
	 *
	 * @param string $key
	 * @param array $data
	 *
	 * @return string
	 */
	public function generate_text_html_without_table_markup( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
        <fieldset>
            <label style="display: block; font-weight: bold;"><?php echo $data['title']; ?></label>
            <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
            <input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>"
                   type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>"
                   id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>"
                   value="<?php echo esc_attr( $this->get_option( $key ) ); ?>"
                   placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
			<?php echo $this->get_description_html( $data ); ?>
        </fieldset>
		<?php

		return ob_get_clean();
	}

    public function generate_cc_countries_html( $key, $data ) {
        $defaults  = array(
            'title'             => '',
            'disabled'          => false,
            'class'             => '',
            'css'               => '',
            'placeholder'       => '',
            'type'              => 'text',
            'desc_tip'          => false,
            'description'       => '',
            'custom_attributes' => array(),
            'select_buttons'    => false,
            'options'           => array(),
        );
        $settings  = wp_parse_args( $data, $defaults );
        $value = (array) $this->get_option( $key, array() );
        $field_key = $this->get_field_key( $key );
        $selections = $value;
        $description = \WC_Settings_API::get_description_html($settings);
        $tooltip_html = \WC_Settings_API::get_tooltip_html($settings);

        if ( ! empty( $settings['options'] ) ) {
            $countries = $settings['options'];
        } else {
            $countries = WC()->countries->countries;
        }

        asort( $countries );

        ob_start();
        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo esc_html( $settings['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
            </th>
            <td class="forminp">
                <select multiple="multiple" name="<?php echo esc_attr( $field_key ); ?>[]" style="width:350px" data-placeholder="<?php esc_attr_e( 'Choose countries / regions&hellip;', 'woocommerce' ); ?>" aria-label="<?php esc_attr_e( 'Country / Region', 'woocommerce' ); ?>" class="wc-enhanced-select">
                    <?php
                    if ( ! empty( $countries ) ) {
                        foreach ( $countries as $key => $val ) {
                            echo '<option value="' . esc_attr( $key ) . '"' . wc_selected( $key, $selections ) . '>' . esc_html( $val ) . '</option>'; // WPCS: XSS ok.
                        }
                    }
                    ?>
                </select> <?php echo ( $description ) ? $description : ''; // WPCS: XSS ok. ?>
                <br /><a class="select_all button" href="#"><?php esc_html_e( 'Select all', 'woocommerce' ); ?></a>
                <a class="select_none button" href="#"><?php esc_html_e( 'Select none', 'woocommerce' ); ?></a>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    public function validate_cc_countries_field( $key, $value ) {
        return $this->validate_multiselect_field( $key, (array) $value );
    }

	/**
	 * @param \WC_Order $order
	 * @param bool $sent_to_admin
	 * @param string $plain_text
	 * @param string $email
	 */
	public function email_meta_action( \WC_Order $order, $sent_to_admin, $plain_text, $email = '' ) {
		$clearing_info = @json_decode( $order->get_meta( '_clearing_info' ), true );
		if ( $clearing_info ) {
			echo '<strong>' . __( 'IBAN', 'payone-woocommerce-3' ) . ':</strong> ';
			echo $clearing_info['bankiban'] . '<br>';
			echo '<strong>' . __( 'BIC', 'payone-woocommerce-3' ) . ':</strong> ';
			echo $clearing_info['bankbic'] . '<br>';
			echo '<strong>' . __( 'pp.bankaccount', 'payone-woocommerce-3' ) . ':</strong> ';
			echo $clearing_info['bankaccount'] . '<br>';
			echo '<strong>' . __( 'pp.bankaccountholder', 'payone-woocommerce-3' ) . ':</strong> ';
			echo $clearing_info['bankaccountholder'] . '<br>';
			echo '<strong>' . __( 'pp.reference', 'payone-woocommerce-3' ) . ':</strong> ';
			echo $clearing_info['reference'] . '<br>';
			echo '<strong>' . __( 'pp.bankname', 'payone-woocommerce-3' ) . ':</strong> ';
			echo $clearing_info['bankname'] . '<br><br>';
		}
	}

	/**
	 * @return bool
	 */
	private function has_divergent_shipping_address() {
		$post_data_string = isset( $_POST['post_data'] ) ? $_POST['post_data'] : '';
		$post_data        = [];
		parse_str( $post_data_string, $post_data );

		$payone_ship_to_different_address_checkbox = isset( $post_data['payone_ship_to_different_address_checkbox'] ) ? $post_data['payone_ship_to_different_address_checkbox'] : 0;
		if ( $payone_ship_to_different_address_checkbox ) {
			$billing_first_name  = isset( $post_data['billing_first_name'] ) ? $post_data['billing_first_name'] : '';
			$shipping_first_name = isset( $post_data['shipping_first_name'] ) ? $post_data['shipping_first_name'] : '';
			$billing_last_name   = isset( $post_data['billing_last_name'] ) ? $post_data['billing_last_name'] : '';
			$shipping_last_name  = isset( $post_data['shipping_last_name'] ) ? $post_data['shipping_last_name'] : '';
			$billing_company     = isset( $post_data['billing_company'] ) ? $post_data['billing_company'] : '';
			$shipping_company    = isset( $post_data['shipping_company'] ) ? $post_data['shipping_company'] : '';
			$billing_address_1   = isset( $post_data['billing_address_1'] ) ? $post_data['billing_address_1'] : '';
			$shipping_address_1  = isset( $post_data['shipping_address_1'] ) ? $post_data['shipping_address_1'] : '';
			$billing_address_2   = isset( $post_data['billing_address_2'] ) ? $post_data['billing_address_2'] : '';
			$shipping_address_2  = isset( $post_data['shipping_address_2'] ) ? $post_data['shipping_address_2'] : '';
			$billing_city        = isset( $post_data['billing_city'] ) ? $post_data['billing_city'] : '';
			$shipping_city       = isset( $post_data['shipping_city'] ) ? $post_data['shipping_city'] : '';
			$billing_postcode    = isset( $post_data['billing_postcode'] ) ? $post_data['billing_postcode'] : '';
			$shipping_postcode   = isset( $post_data['shipping_postcode'] ) ? $post_data['shipping_postcode'] : '';
			$billing_country     = isset( $post_data['billing_country'] ) ? $post_data['billing_country'] : '';
			$shipping_country    = isset( $post_data['shipping_country'] ) ? $post_data['shipping_country'] : '';

			return $billing_first_name !== $shipping_first_name
			       || $billing_last_name !== $shipping_last_name
			       || $billing_company !== $shipping_company
			       || $billing_address_1 !== $shipping_address_1
			       || $billing_address_2 !== $shipping_address_2
			       || $billing_city !== $shipping_city
			       || $billing_postcode !== $shipping_postcode
			       || $billing_country !== $shipping_country;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	private function is_b2b() {
		$post_data_string = isset( $_POST['post_data'] ) ? $_POST['post_data'] : '';
		$post_data        = [];
		parse_str( $post_data_string, $post_data );

		$billing_company = isset( $post_data['billing_company'] ) ? $post_data['billing_company'] : '';

		return $billing_company !== '';
	}
}
