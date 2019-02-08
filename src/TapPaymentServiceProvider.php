<?php

namespace VMdevelopment\TapPayment;

use Illuminate\Support\ServiceProvider;

class TapPaymentServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap TapPayment application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes(
			[
				__DIR__ . '/Publishing/config.php' => config_path( 'tap-payment.php' ),
			]
		);
		$this->mergeConfigFrom(
			__DIR__ . '/Publishing/config.php',
			'tap-payment'
		);
	}


	/**
	 * Register TapPayment application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(
			'tap-payment', function() {
			return new TapService();
		}
		);
	}
}