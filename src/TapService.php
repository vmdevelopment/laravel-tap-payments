<?php

namespace VMdevelopment\TapPayment;

use VMdevelopment\TapPayment\Services\Charge;

class TapService
{
	/**
	 * @return \VMdevelopment\TapPayment\Services\Charge
	 */
	public function createCharge()
	{
		return new Charge();
	}


	/**
	 * @param $id
	 *
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function findCharge( $id )
	{
		$charge = new Charge( $id );

		return $charge->find();
	}
}