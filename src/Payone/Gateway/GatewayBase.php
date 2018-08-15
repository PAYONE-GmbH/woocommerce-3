<?php

namespace Payone\Gateway;

use Payone\Payone\Api\TransactionStatus;
use Payone\Transaction\Capture;

abstract class GatewayBase extends \WC_Payment_Gateway {
	/**
	 * @var array
	 */
	private $global_settings;

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
		$this->id              = $id;
		$this->has_fields      = true;
		$this->supports        = [ 'products', 'refunds' ];
		$this->global_settings = get_option( \Payone\Admin\Option\Account::OPTION_NAME );

		$this->init_settings();
		$this->init_form_fields();

		$this->title                = $this->get_option( 'title' );
		$this->authorization_method = $this->settings['authorization_method'];
		$this->min_amount           = $this->settings['min_amount'];
		$this->max_amount           = $this->settings['max_amount'];
		$this->countries            = $this->settings['countries'];

		$this->process_global_settings();

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
	}

	public static function add( $methods ) {
		$methods[] = get_called_class();

		return $methods;
	}

	/**
	 * @param TransactionStatus $transaction_status
	 */
	public function process_transaction_status( TransactionStatus $transaction_status ) {
		$order = $transaction_status->get_order();
		$sequencenumber = $transaction_status->get_sequencenumber();
		if ( $sequencenumber ) {
			$order->update_meta_data( '_sequencenumber', $sequencenumber );
			$order->save_meta_data();
		}

		if ( $transaction_status->is_refund() ) {
			$is_already_refunded = $order->get_meta( '_refunded' );
			if ( ! $is_already_refunded ) {
				$order->update_status( 'wc-refunded', __( 'Debit successfull', 'payone-woocommerce-3' ) );
				$order->update_meta_data( '_refunded', time() );
				$order->save_meta_data();
			}
		} elseif ( $transaction_status->is_cancelation() ) {
			$order->add_order_note( __( 'Payment was canceled: ', 'payone-woocommerce-3' ) . $transaction_status->get( 'failedcause' ));
			$order->update_status( 'wc-failed', __( 'Payment failed.', 'payone-woocommerce-3' ) );
		}
	}

	/**
	 * @param \WC_Order $order
	 */
	public function add_content_to_thankyou_page( \WC_Order $order ) {}

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

	protected function add_data_to_capture( Capture $capture, \WC_Order $order ) { }

	/**
	 * @param int $order_id
	 * @param float|null $amount
	 * @param string $reason
	 *
	 * @return bool|\WP_Error
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
	    if ( (float)$amount <= 0.0 ) {
	        return new \WP_Error( 1, __( 'Debit amount must be greater than zero.', 'payone-woocommerce-3' ) );
        }
		$order = new \WC_Order( $order_id );

		$transaction = new \Payone\Transaction\Debit( $this );

		return $transaction->execute( $order, - $amount );
	}

	/**
	 * @todo Es ist nicht klar, warum das nicht ohne eigenen Code funktioniert. Die Doku zu $this->countries sieht
	 *       eigentlich so aus, als ob es funktionieren müsste.
	 *
	 * @return bool
	 */
	public function is_available() {
		$is_available = parent::is_available();

		if ( $is_available && $this->min_amount > $this->get_order_total() ) {
			$is_available = false;
		}

		if ( $is_available ) {
			$order_id = absint( get_query_var( 'order-pay' ) );

			if ( $order_id ) {
				$order   = wc_get_order( $order_id );
				$country = (string) $order->get_billing_country();
			} elseif ( WC()->customer && WC()->customer->get_billing_country() ) {
				$country = (string) WC()->customer->get_billing_country();
			}

			$is_available = in_array( $country, $this->countries );
		}

		return $is_available;
	}

	/**
	 * @param string $label
	 */
	public function init_common_form_fields( $label ) {
		$default_merchant_id = $this->global_settings['merchant_id'];
		$default_portal_id   = $this->global_settings['portal_id'];
		$default_account_id  = $this->global_settings['account_id'];
		$default_key         = $this->global_settings['key'];

		$countries = new \WC_Countries();

		$this->form_fields = [
			'enabled'                   => [
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this payment method', 'payone-woocommerce-3' ),
				'default' => 'yes',
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
				'default' => '',
			],
			'min_amount'                => [
				'title'   => __( 'Minimum order value', 'payone-woocommerce-3' ),
				'type'    => 'text',
				'default' => '0',
			],
			'max_amount'                => [
				'title'   => __( 'Maximum order value', 'payone-woocommerce-3' ),
				'type'    => 'text',
				'default' => '0',
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
				'type'    => 'multiselect',
				'options' => $countries->get_countries(),
				'default' => [ 'DE', 'AT', 'CH' ],
                'css'     => 'height:100px',
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

	protected function add_email_meta_hook($callable = null) {
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
			$this->merchant_id               = $this->global_settings['merchant_id'];
			$this->portal_id                 = $this->global_settings['portal_id'];
			$this->account_id                = $this->global_settings['account_id'];
			$this->key                       = $this->global_settings['key'];
			$this->submit_cart               = $this->global_settings['submit_cart'];
			$this->activate_pdf_download     = $this->global_settings['activate_pdf_download'];
			$this->dynamic_invoice_text      = $this->global_settings['dynamic_invoice_text'];
			$this->dynamic_refund_text       = $this->global_settings['dynamic_refund_text'];
			$this->text_on_booking_statement = $this->global_settings['text_on_booking_statement'];
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
	 * This is a copy of $this->generate_select_html(), but without the table_markup
	 *
	 * @param string $key
	 * @param array $data
	 *
	 * @return string
	 */
	public function generate_select_html_without_table_markup( $key, $data ) {
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
			<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
			<select class="select <?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>>
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
				<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
				<input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( $this->get_option( $key ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
				<?php echo $this->get_description_html( $data ); ?>
			</fieldset>
		<?php

		return ob_get_clean();
	}

	/**
	 * @param \WC_Order $order
	 * @param bool $sent_to_admin
	 * @param string $plain_text
	 * @param string $email
	 */
	public function email_meta_action( \WC_Order $order, $sent_to_admin, $plain_text, $email = '' ) {
		$clearing_info = @json_decode($order->get_meta( '_clearing_info' ), true );
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
}