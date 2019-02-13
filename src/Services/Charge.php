<?php

namespace VMdevelopment\TapPayment\Services;

use Illuminate\Support\Facades\Validator;
use VMdevelopment\TapPayment\Abstracts\AbstractService;
use VMdevelopment\TapPayment\Resources\Invoice;

class Charge extends AbstractService
{
	protected $endpoint = 'charges/';
	protected $method = 'post';
	protected $attributes = [];


	public function __construct( $id = null )
	{
		if ( $id ) {
			$this->attributes['id'] = $id;
			$this->setEndpoint( $id );
		}
		parent::__construct();
	}


	protected function setEndpoint( $endpoint )
	{
		$this->endpoint .= $endpoint;
	}


	public function setAmount( $amount )
	{
		$this->attributes['amount'] = $amount;
	}


	public function setCurrency( $currency )
	{
		$this->attributes['currency'] = $currency;
	}


	public function setThreeDSecure( $threeDSecure )
	{
		$this->attributes['threeDSecure'] = $threeDSecure;
	}


	public function setSave_card( $save_card )
	{
		$this->attributes['save_card'] = $save_card;
	}


	public function setDescription( $description )
	{
		$this->attributes['description'] = $description;
	}


	public function setCustomerName( $name )
	{
		$name = explode( ' ', $name );
		$this->attributes['customer']['first_name'] = array_shift( $name );
		$this->attributes['customer']['last_name'] = implode( ' ', $name );
	}


	public function setCustomerEmail( $email )
	{
		$this->attributes['customer']['email'] = $email;
	}


	public function setCustomerPhone( $country_code, $phone )
	{
		$this->attributes['customer']['phone'] = [
			'country_code' => $country_code,
			'number'       => $phone,
		];
	}


	public function setRedirectUrl( $url )
	{
		$this->attributes['redirect']['url'] = $url;
	}


	public function setPostUrl( $url )
	{
		$this->attributes['post']['url'] = $url;
	}


	public function setSource( $source )
	{
		$this->attributes['source']['id'] = $source;
	}


	public function setMetaData( array $meta )
	{
		$this->attributes['metadata'] = $meta;
	}


	public function setRawAttributes( array $attributes )
	{
		$this->attributes = $attributes;
	}


	/**
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException|\Exception
	 */
	public function find()
	{
		$this->setMethod( 'get' );
		if (
		$this->validateAttributes(
			[
				'id' => 'required'
			]
		)
		)
			return $this->send();
	}


	protected function setMethod( $method )
	{
		$this->method = $method;
	}


	/**
	 * @param $rules
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function validateAttributes( array $rules, $messages = [] )
	{
		$validator = Validator::make( $this->attributes, $rules, $messages );

		if ( $validator->fails() )
			throw new \Exception( $validator->errors()->first() );

		return true;
	}


	/**
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException|\Exception
	 */
	protected function send()
	{
		try {
			$response = $this->client->request(
				$this->method,
				$this->getPath(),
				[
					'form_params' => $this->attributes,
					'headers'     => [
						'Authorization' => 'Bearer ' . config( 'tap-payment.auth.api_key' ),
						'Accept'        => 'application/json',
					]
				]
			);

			return new Invoice( json_decode( $response->getBody()->getContents(), true ) );
		}
		catch ( \Throwable $exception ) {
			throw new \Exception( $exception->getMessage() );
		}
	}


	/**
	 * @return Invoice
	 * @throws \GuzzleHttp\Exception\GuzzleException|\Exception
	 */
	public function pay()
	{
		$rules = [
			'id'           => 'regex:/^$/i',
			'amount'       => 'required',
			'currency'     => 'required',
			'source.id'    => 'required',
			'redirect.url' => 'required',
		];
		foreach ( config( 'tap-payment.customer.requirements' ) as $item ) {
			if ( $item == 'mobile' ) {
				$rules['customer.phone'] = 'required';
				$rules['customer.phone.country_code'] = [ 'required', 'numeric' ];
				$rules['customer.phone.number'] = [ 'required', 'numeric' ];
			} else {
				$rules[ 'customer.' . $item ] = 'required';
			}
		}

		if (
		$this->validateAttributes(
			$rules,
			[
				'id.regex' => "ID should be empty when you create a new Charge."
			]
		)
		)
			return $this->send();
	}
}