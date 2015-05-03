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
require app_path().'/routes/customer.php';

Route::get('/', function()
{
	return View::make('hello');
});



Route::api('v1', function () {
	Route::group(['prefix' => 'api', 'protected' => true], function () {
		# BEGIN SETTINGS GROUPS #
		Route::resource('bank_accounts', '\App\Core\Entities\BankAccount\BankAccountsController');
		# END SETTINGS GROUPS #
		
		# BEGIN CATALOG GROUPS #
		Route::resource('attribute_groups', '\App\Core\Entities\AttributeGroup\AttributeGroupsController');
		Route::resource('attributes', '\App\Core\Entities\Attribute\AttributesController');

		Route::resource('features', '\App\Core\Entities\Feature\FeaturesController');
		Route::resource('feature_values', '\App\Core\Entities\FeatureValue\FeatureValuesController');

		Route::resource('categories', '\App\Core\Entities\Category\CategoriesController');

		Route::resource('manufacturers', '\App\Core\Entities\Manufacturer\ManufacturersController');

		Route::resource('products', '\App\Core\Entities\Product\ProductsController');
		Route::resource('product_attributes', '\App\Core\Entities\ProductAttribute\ProductAttributesController');
		Route::resource('product_images', '\App\Core\Entities\ProductImage\ProductImagesController');
		# END CATALOG GROUPS #
		
		# BEGIN CUSTOMERS GROUPS #
		Route::resource('customers', '\App\Core\Entities\Customer\CustomersController');
		Route::resource('customer_addresses', '\App\Core\Entities\CustomerAddress\CustomerAddressesController');
		# END CUSTOMERS GROUPS #
		
		# BEGIN STOCK GROUPS #
		Route::resource('stock_movements', '\App\Core\Entities\StockMovement\StockMovementsController');
		Route::resource('stock_movement_reasons', '\App\Core\Entities\StockMovementReason\StockMovementReasonsController');
		# END STOCK GROUPS #
		
		# BEGIN CART GROUPS #
		Route::resource('carts', '\App\Core\Entities\Cart\CartsController');
		Route::resource('cart_products', '\App\Core\Entities\CartProduct\CartProductsController');
		Route::resource('carriers', '\App\Core\Entities\Carrier\CarriersController');
		Route::resource('payments', '\App\Core\Entities\Payment\PaymentsController');
		Route::resource('order_states', '\App\Core\Entities\OrderState\OrderStatesController', ['only' => 'index']);
		# END CART GROUPS #
		
		Route::resource('orders', '\App\Core\Entities\Order\OrdersController');
		Route::resource('reports', '\App\Core\Entities\Report\ReportsController', ['only' => 'index']);
		
		Route::resource('users', '\App\Core\Entities\User\UsersController');

		Route::get('users/reset_password/{token}', ['protected' => false, 'uses' => '\App\Core\Entities\User\UsersController@resetPassword']);
		Route::post('users/reset_password', ['protected' => false, 'uses' => '\App\Core\Entities\User\UsersController@doResetPassword']);
		Route::get('users/confirm/{code}', ['protected' => false, 'uses' => '\App\Core\Entities\User\UsersController@confirm']);
		Route::post('login', ['protected' => false, 'uses' => '\App\Core\Entities\User\UsersController@login']);
    });
});

// Confide routes
// Route::get('users/create', 'UsersController@create');
// Route::post('users', 'UsersController@store');
// Route::get('users/login', 'UsersController@login');
// Route::post('users/login', 'UsersController@doLogin');
// Route::get('users/confirm/{code}', 'UsersController@confirm');
// Route::get('users/forgot_password', 'UsersController@forgotPassword');
// Route::post('users/forgot_password', 'UsersController@doForgotPassword');
// Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
// Route::post('users/reset_password', 'UsersController@doResetPassword');
// Route::get('users/logout', 'UsersController@logout');
