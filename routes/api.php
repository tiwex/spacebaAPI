<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register', 'Auth\RegisterController@store');
Route::post('login', 'Auth\LoginController@login');
Route::post('createsubscription', 'Subscription\SubscriptionController@store');
Route::post('createpayment', 'Subscription\PaymentController@store');
Route::post('createbooking', 'Booking\BookingController@store');
Route::post('createvisit', 'Booking\VisitController@store');
Route::post('createquote', 'Booking\QuoteController@store');
Route::get('getclockinhistory/{userid}', 'Account\ClockHistoryController@show');
Route::get('getspace/{space_id}', 'Space\SpaceController@showspace');
Route::get('getimages/{space_id}', 'Space\SpaceController@showimages');
Route::get('gettype/{space_id}', 'Space\SpaceController@showtype');
//Route::options('getclockinhistory/{userid}', 'Account\ClockHistoryController@show');
//Route::get('getclockinhistory/{userid}', ['middleware' => 'cors','uses' => 'Account\ClockHistoryController@show']);

