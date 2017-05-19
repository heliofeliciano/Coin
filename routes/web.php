<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return View('welcome');
});


$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

  $api->get('coin/chart', function() {
    return View('chart');
  });
  $api->get('coin/chart_dash', function() {
    return View('chart_dash');
  });
  $api->get('coin/getid/{currency}', 'App\Http\Controllers\CoinController@getCoinIdByCurrency');

  $api->get('coin/getdatasbycurrency', 'App\Http\Controllers\CoinController@getDatasByCurrency');
  $api->get('coin/getdatasbycurrencybtc_ins', 'App\Http\Controllers\CoinController@getDatasByCurrencyBTC_ins');
  $api->get('coin/getdatasbycurrency60min', 'App\Http\Controllers\CoinController@getDatasByCurrency60minutes');
  $api->get('coin/getdatasbycurrencybtc', 'App\Http\Controllers\CoinController@getDatasByCurrencyBTC');
  $api->get('coin/getdatasbycurrencyall', 'App\Http\Controllers\CoinController@getDatasByCurrencyAll');

  $api->get('test', function () {
      return 'It is ok';
  });

});
