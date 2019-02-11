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
		$data = [
			'x_id'                => $this->attributes['id'] ?? null,
			'x_amount'            => $this->attributes['amount'] ?? null,
			'x_currency'          => $this->attributes['currency'] ?? null,
			'x_gateway_reference' => $this->attributes['reference']['gateway'] ?? null,
			'x_payment_reference' => $this->attributes['reference']['payment'] ?? null,
			'x_status'            => $this->attributes['status'] ?? null,
			'x_created'           => $this->attributes['transaction']['created'] ?? null,
		];

		$decimals = $data['x_currency'] == 'KWD' ? 3 : 2;

		$data['x_amount'] = number_format( $data['x_amount'], $decimals, '.', '' );

		$stringToHash = implode(
			'',
			array_map(
				function( $value, $key ) {
					return $key . $value;
				},
				$data,
				array_keys( $data )
			)
		);

		$key = config( 'tap-payment.auth.api_key' );

		$hashedString = hash_hmac( 'sha256', $stringToHash, $key );

		return $hashedString == $hash;
	}
}