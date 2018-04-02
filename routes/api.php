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
Route::post('register', ['middleware' => 'crrs','uses'=> 'Auth\RegisterController@store']);
Route::post('login', ['middleware'=>'crrs','uses'=>'Auth\LoginController@login']);
Route::post('createsubscription', ['middleware'=>'crrs','uses'=>'Subscription\SubscriptionController@store']);
Route::post('createpayment', ['middleware'=>'crrs','uses'=>'Subscription\PaymentController@store']);
Route::post('createbooking', ['middleware'=>'crrs','uses'=>'Booking\BookingController@store']);
Route::post('createvisit', ['middleware'=>'crrs','uses'=>'Booking\VisitController@store']);
Route::post('createquote', ['middleware'=>'crrs','uses'=>'Booking\QuoteController@store']);
Route::get('getspace/{space_id}', ['middleware'=>'crrs','uses'=>'Space\SpaceController@showspace']);
Route::get('getimages/{space_id}', ['middleware'=>'crrs','uses'=>'Space\SpaceController@showimages']);
Route::get('gettype/{space_id}', ['middleware'=>'crrs','uses'=>'Space\SpaceController@showtype']);
Route::get('getclockinhistory/{userid}', ['middleware' => 'crrs','uses' => 'Account\ClockHistoryController@show']);
