<?php

namespace VMdevelopment\TapPayment\Resources;

class Invoice
{
	protected $attributes;


	public function __construct( $data )
	{
		$this->attributes = (array)$data;
	}


	public function isSuccess()
	{
		return isset( $this->attributes['status'] ) && strtolower( $this->attributes['status'] ) == 'captured';
	}


	public function checkHash( $hash )
	{
		// todo discuss hash checking logic
		$data = [
			'x_id'                => $this->attributes['id'] ?? null,
			'x_amount'            => $this->attributes['amount'] ?? null,
			'x_currency'          => $this->attributes['currency'] ?? null,
			'x_gateway_reference' => $this->attributes['reference']['gateway'] ?? null,
			'x_payment_reference' => $this->attributes['reference']['payment'] ?? null,
			'x_status'            => $this->attributes['status'] ?? null,
			'x_created'           => $this->attributes['transaction']['created'] ?? null,
		];

		$stringToHash = implode(
			'',
			array_map(
				function( $value, $key ) {
					return $key . '=' . (string)$value;
				},
				$data,
				array_keys( $data )
			)
		);

		$hashedString = hash_hmac( 'sha256', $stringToHash, config( 'tap-payment.auth.api_key' ) );

		return $hashedString == $hash;
	}
}