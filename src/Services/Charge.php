<?php

namespace VMdevelopment\TapPayment\Services;

use Illuminate\Support\Facades\Validator;
use VMdevelopment\TapPayment\Abstracts\AbstractService;

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
						'Authorization' => 'Bearer sk_test_XKokBfNWv6FIYuTMg5sLPjhJ',
						'Accept'        => 'application/json',
					]
				]
			);

			return json_decode( $response->getBody()->getContents() );
		}
		catch ( \Throwable $exception ) {
			throw new \Exception( $exception->getMessage() );
		}
	}


	/**
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException|\Exception
	 */
	public function pay()
	{
		if (
		$this->validateAttributes(
			[
				'id'                          => 'regex:/^$/i',
				'amount'                      => 'required',
				'currency'                    => 'required',
				'customer.first_name'         => 'required',
				'customer.email'              => 'required',
				'customer.phone'              => 'required',
				'customer.phone.country_code' => 'required',
				'customer.phone.number'       => 'required',
				'source.id'                   => 'required',
				'redirect.url'                => 'required',
			], [
				'id.regex' => "ID should be empty when you create a new Charge."
			]
		)
		)
			return $this->send();
	}
}