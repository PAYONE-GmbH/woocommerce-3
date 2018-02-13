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
	private $createdAt;

	/**
	 * @var bool
	 */
	private $transactionLogEnabled;

	public function __construct( $id = null ) {
		if ( $id ) {
			$this->id = $id;
		}
		$this->setCreatedAt( new \DateTime() );

		$options = get_option( 'payone_account', [ 'transaction_log' => 0 ] );

		$this->transactionLogEnabled = $options['transaction_log'] ? true : false;
	}

	public static function constructFromPostVars() {
		$transaction_id        = isset( $_POST['txid'] ) ? $_POST['txid'] : '';
		$transaction_log_entry = new Log();
		$transaction_log_entry->setData( \Payone\Payone\Api\DataTransfer::constructFromArray( $_POST ) );
		$transaction_log_entry->setTransactionId( $transaction_id );
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
			->setData( DataTransfer::constructFromJson( $data ) )
			->setTransactionId( $transaction_id )
			->setCreatedAt( new \DateTime( $created_at ) );

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
				'data'    => $this->getData()->getSerializedParameters(),
				'transaction_id'   => $this->getTransactionId(),
				'created_at' => $this->getCreatedAt()->format( 'Y-m-d H:i:s' ),
			],
			[ '%s', '%s', '%s' ]
		);
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTransactionId() {
		return $this->transaction_id;
	}

	/**
	 * @param string $transactionId
	 *
	 * @return Log
	 */
	public function setTransactionId( $transactionId ) {
		$this->transaction_id = $transactionId;

		return $this;
	}

	/**
	 * @return DataTransfer
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param DataTransfer $data
	 *
	 * @return Log
	 */
	public function setData( $data ) {
		$this->data = $data;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * @param \DateTime $createdAt
	 *
	 * @return Log
	 */
	public function setCreatedAt( $createdAt ) {
		$this->createdAt = $createdAt;

		return $this;
	}
}