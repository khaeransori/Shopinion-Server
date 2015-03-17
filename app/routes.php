<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

Route::api('v1', function () {
	Route::group(['prefix' => 'backend'], function () {
		# BEGIN SETTINGS GROUPS #
		Route::resource('bank_accounts', 'BankAccountsController');
		# END SETTINGS GROUPS #
		
		# BEGIN CATALOG GROUPS #
		Route::resource('attribute_groups', 'AttributeGroupsController');
		Route::resource('attributes', 'AttributesController');

		Route::resource('features', 'FeaturesController');
		Route::resource('feature_values', 'FeatureValuesController');

		Route::resource('categories', 'CategoriesController');

		Route::resource('manufacturers', 'ManufacturersController');

		Route::resource('products', 'ProductsController');
		Route::resource('product_attributes', 'ProductAttributesController');
		Route::resource('product_images', 'ProductImagesController');
		# END CATALOG GROUPS #
		
		# BEGIN CUSTOMERS GROUPS #
		Route::resource('customers', 'CustomersController');
		Route::resource('customer_addresses', 'CustomerAddressesController');
		# END CUSTOMERS GROUPS #
		
		# BEGIN STOCK GROUPS #
		Route::resource('stock_movements', 'StockMovementsController');
		Route::resource('stock_movement_reasons', 'StockMovementReasonsController');
		# END STOCK GROUPS #
		
		# BEGIN CART GROUPS #
		Route::resource('carts', 'CartsController');
		Route::resource('cart_products', 'CartProductsController');
		Route::resource('carriers', 'CarriersController');
		Route::resource('payments', 'PaymentsController');
		Route::resource('order_states', 'OrderStatesController');
		# END CART GROUPS #
		
		Route::resource('orders', 'OrdersController');

		Route::get('reports', 'ReportsController@index');

    });
});//

Route::api('v1', function () {
	Route::group(['prefix' => 'mobile'], function () {
		Route::resource('manufacturers', 'MobileManufacturersController');
		Route::resource('products', 'MobileProductsController');
    });
});//

// Confide routes
Route::get('users/create', 'UsersController@create');
Route::post('users', 'UsersController@store');
Route::get('users/login', 'UsersController@login');
Route::post('users/login', 'UsersController@doLogin');
Route::get('users/confirm/{code}', 'UsersController@confirm');
Route::get('users/forgot_password', 'UsersController@forgotPassword');
Route::post('users/forgot_password', 'UsersController@doForgotPassword');
Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
Route::post('users/reset_password', 'UsersController@doResetPassword');
Route::get('users/logout', 'UsersController@logout');
