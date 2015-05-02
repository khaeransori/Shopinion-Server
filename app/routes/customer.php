<?php

Route::api('v1', function () {
	Route::group(['prefix' => 'api'], function () {
		Route::group(['prefix' => 'customers'], function ()
		{
			Route::group(['protected' => true], function () {
				/**
				 * BEGIN ACCOUNT
				 */
				Route::get('detail', '\App\Core\Entities\Customer\CustomersController@detailCustomer');
				Route::post('update', '\App\Core\Entities\Customer\CustomersController@updateCustomer');

				Route::group(['prefix' => 'addresses'], function () {
					Route::get('/{id}', '\App\Core\Entities\CustomerAddress\CustomerAddressesController@showCustomer');
					Route::get('/', '\App\Core\Entities\CustomerAddress\CustomerAddressesController@getCustomer');
					Route::post('/', '\App\Core\Entities\CustomerAddress\CustomerAddressesController@storeCustomer');
					Route::put('/{id}', '\App\Core\Entities\CustomerAddress\CustomerAddressesController@updateCustomer');
					Route::delete('/{id}', '\App\Core\Entities\CustomerAddress\CustomerAddressesController@destroy');
				});
				/**
				 * END ACCOUNT
				 */
				
				/**
				 * BEGIN ORDER
				 */
				Route::resource('wishlists', '\App\Core\Entities\Wishlists\WishlistsController', ['only' => ['index', 'store', 'destroy']]);
				Route::resource('payment_confirmation', '\App\Core\Entities\PaymentConfirmation\PaymentConfirmationsController', ['only' => ['store']]);

				Route::get('carts', '\App\Core\Entities\Cart\CartsController@getCustomer');
				Route::get('carts/{id}', '\App\Core\Entities\Cart\CartsController@show');

				Route::group(['prefix' => 'cart_products'], function () {
					Route::get('/', '\App\Core\Entities\CartProduct\CartProductsController@index');
					Route::post('/', '\App\Core\Entities\CartProduct\CartProductsController@storeCustomer');
					Route::delete('/{id}', '\App\Core\Entities\CartProduct\CartProductsController@destroyCustomer');
				});

				Route::group(['prefix' => 'orders'], function () {
					Route::get('/', '\App\Core\Entities\Order\OrdersController@getCustomer');
					Route::get('/{id}', '\App\Core\Entities\Order\OrdersController@show');
					Route::post('/', '\App\Core\Entities\Order\OrdersController@storeCustomer');
				});
				/**
				 * END ORDER
				 */
			});

			
			Route::group(['protected' => false], function () {
				/**
				 * BEGIN ACCOUNT
				 */
				Route::post('init', '\App\Core\Entities\Customer\CustomersController@initCustomer');
				Route::post('forgot_password', '\App\Core\Entities\Customer\CustomersController@forgotPassword');
				Route::post('login', '\App\Core\Entities\Customer\CustomersController@loginCustomer');
				Route::post('register', '\App\Core\Entities\Customer\CustomersController@store');
				/**
				 * END ACCOUNT
				 */
			
				/**
				 * BEGIN CATALOG
				 */
				Route::resource('products', '\App\Core\Entities\Product\ProductsController', ['only' => ['index', 'show']]);
				Route::resource('categories', '\App\Core\Entities\Category\CategoriesController', ['only' => ['index', 'show']]);
				/**
				 * END CATALOG
				 */
				
				/**
				 * BEGIN CONFIGURATIONS
				 */
				Route::resource('bank_accounts', '\App\Core\Entities\BankAccount\BankAccountsController', ['only' => ['index']]);
				Route::resource('carriers', '\App\Core\Entities\Carrier\CarriersController', ['only' => ['index']]);
				Route::resource('payments', '\App\Core\Entities\Payment\PaymentsController', ['only' => ['index']]);
				/**
				 * END CONFIGURATIONS
				 */
			});
		});
	});
});