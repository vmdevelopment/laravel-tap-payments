<?php

namespace VMdevelopment\TapPayment\Abstracts;

use GuzzleHttp\Client;

abstract class AbstractService
{
	protected $client;
	protected $endpoint;
	private $base_path = "https://api.tap.company/";
	private $version = "v2";


	public function __construct()
	{
		$this->client = new Client();
	}


	public function getPath()
	{
		return $this->base_path . $this->version . '/' . $this->endpoint;
	}
}