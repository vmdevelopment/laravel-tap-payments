# Tap Payment package for Laravel

Laravel package for https://www.tap.company

## Installation

Add `vmdevelopment/laravel-tap-payments` to your `composer.json`.
```
"vmdevelopment/laravel-tap-payments": "dev-master"
```

Run `composer update` to pull down the latest version of package.

OR simply run
```
composer require "vmdevelopment/laravel-tap-payments"
```

For laravel >=5.5 that's all. This package supports Laravel new Package Discovery.

Otherwise you need to open up `/config/app.php` and add the service provider to your `providers` array.
```php
'providers' => [
	\VMdevelopment\TapPayment\TapPaymentServiceProvider::class
]
```

Now add the alias.
```php
'aliases' => [
	'TapPayment' => \VMdevelopment\TapPayment\Facade\TapPayment::class
]
```

## Configuration
To publish config run
```
php artisan vendor:publish --provider="VMdevelopment\TapPayment\TapPaymentServiceProvider"
```
and modify the config file with your own information. File is located in `/config/tap-payment.php`

you can use this environment variables in your `.env` file
```
TAP_PAYMENT_API_KEY=your_api_key
```

## Current version Functions

* `TapPayment::createCharge()` - Creating an ApiInvoice
* `TapPayment::findCharge($id)` - Finding an ApiInvoice by ID

## Usage example

### Creating Charge(make payment)
```php
use VMdevelopment\TapPayment\Facade\TapPayment;

public function pay()
{
	try{

		$payment = TapPayment::createCharge();

		$payment->setCustomerName( "John Doe" );
		
		$payment->setCustomerPhone( "123456789" );
		
		$payment->setDescription( "Some description" );

		$payment->setAmount( 123 );

		$payment->setCurrency( "KWD" );
		
		$payment->setSource( "src_kw.knet" );
        
		$payment->setRedirectUrl( "https://example.com" );

		$payment->setPostUrl( "https://example.com" ); // if you are using post request to handle payment updates

		$payment->setMetaData( [ 'package' => json_encode( $package ) ] ); // if you want to send metadata

		$invoice = $payment->pay();
		
	} catch( \Exception $exception ){
		// your handling of request failure
	}
    
    $payment->isSuccess() // check if TapPayment has successfully handled request.
}
```
### Find ApiInvoice
```php
public function check( $id )
{
	try{
	
		 $invoice = TapPayment::findCharge( $id );;
		 
	 } catch( \Exception $exception ){
		// your handling of request failure
	}
	$invoice->checkHash($request->header('Hashstring')); // check hashstring to make sure that request comes from Tap
	$invoice->isSuccess(); // check if invoice is paid
	$invoice->isInitiated(); // check if invoice is unpaid yet
}
```