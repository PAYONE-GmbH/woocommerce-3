<?php

namespace Payone\Transaction;

use Payone\Payone\Api\DataTransfer;

class Log {
	const TABLE_NAME = 'payone_transaction_log';

	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var integer
	 */
	private $transaction_id;

	/**
	 * @var DataTransfer
	 */
	private $data;

	/**
	 * @var \DateTime
	 */
	private $created_at;

	/**
	 * @var bool
	 */
	private $transactionLogEnabled;

	public function __construct( $id = null ) {
		if ( $id ) {
			$this->id = $id;
		}
		$this->set_created_at( new \DateTime() );

		$options = get_option( 'payone_account', [ 'transaction_log' => 0 ] );

		$this->transactionLogEnabled = $options['transaction_log'] ? true : false;
	}

	public static function constructFromPostVars() {
		$transaction_id        = isset( $_POST['txid'] ) ? $_POST['txid'] : '';
		$transaction_log_entry = new Log();
		$transaction_log_entry->set_data( \Payone\Payone\Api\DataTransfer::constructFromArray( $_POST ) );
		$transaction_log_entry->set_transaction_id( $transaction_id );
		$transaction_log_entry->save();
	}

	/**
	 * @param array $row
	 *
	 * @return Log
	 */
	public static function constructFromDatabase( $row ) {
		return self::constructFromArray($row);
	}

	/**
	 * @param array $array
	 *
	 * @return Log
	 */
	public static function constructFromArray( $array ) {
		$id = isset($array['id']) ? $array['id'] : null;
		$data = isset($array['data']) ? $array['data'] : [];
		$transaction_id = isset($array['transaction_id']) ? $array['transaction_id'] : '';
		if (!$transaction_id) {
			$transaction_id = isset($array['txid']) ? $array['txid'] : '';
		}
		$created_at = isset($array['created_at']) ? $array['created_at'] : '1970-01-01 00:00:00';

		$object = new Log( $id );
		$object
			->set_data( DataTransfer::construct_from_json( $data ) )
			->set_transaction_id( $transaction_id )
			->set_created_at( new \DateTime( $created_at ) );

		return $object;
	}

	public function save() {
		if (!$this->transactionLogEnabled) {
			return ;
		}

		global $wpdb;

		$tableName = $wpdb->prefix . self::TABLE_NAME;

		$wpdb->insert(
			$tableName,
			[
				'data'    => $this->get_data()->get_serialized_parameters(),
				'transaction_id'   => $this->get_transaction_id(),
				'created_at' => $this->get_created_at()->format( 'Y-m-d H:i:s' ),
			],
			[ '%s', '%s', '%s' ]
		);
	}

	/**
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function get_transaction_id() {
		return $this->transaction_id;
	}

	/**
	 * @param string $transaction_id
	 *
	 * @return Log
	 */
	public function set_transaction_id( $transaction_id ) {
		$this->transaction_id = $transaction_id;

		return $this;
	}

	/**
	 * @return DataTransfer
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * @param DataTransfer $data
	 *
	 * @return Log
	 */
	public function set_data( $data ) {
		$this->data = $data;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function get_created_at() {
		return $this->created_at;
	}

	/**
	 * @param \DateTime $created_at
	 *
	 * @return Log
	 */
	public function set_created_at( $created_at ) {
		$this->created_at = $created_at;

		return $this;
	}
}