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
//Route::post('register', ['middleware' => 'crrs','uses'=> 'Auth\RegisterController@store']);
//Route::post('login', ['middleware'=>'crrs','uses'=>'Auth\LoginController@login']);
//Route::post('login', 'Auth\LoginController@login')->middleware('crrs');
Route::post('createsubscription', ['middleware'=>'crrs','uses'=>'Subscription\SubscriptionController@store']);
//Route::post('createpayment', ['middleware'=>'crrs','uses'=>'Subscription\PaymentController@store']);
Route::post('createbooking', ['middleware'=>'crrs','uses'=>'Booking\BookingController@store']);
Route::post('createvisit', ['middleware'=>'crrs','uses'=>'Booking\VisitController@store']);
Route::post('createquote', ['middleware'=>'crrs','uses'=>'Booking\QuoteController@store']);
Route::get('getspace/{space_id}', ['middleware'=>'crrs','uses'=>'Space\SpaceController@showspace']);
Route::get('getspaces', ['middleware'=>'crrs','uses'=>'Space\SpaceController@showspaces']);
Route::get('getimages/{space_id}', ['middleware'=>'crrs','uses'=>'Space\SpaceController@showimages']);
Route::get('gettype/{space_id}', ['middleware'=>'crrs','uses'=>'Space\SpaceController@showtype']);
Route::get('getnetworksetting', ['middleware'=>'crrs','uses'=>'Space\SpaceController@shownetworksetting']);
Route::get('getclockinhistory/{userid}', ['middleware' => 'crrs','uses' => 'Account\ClockHistoryController@show']);
Route::get('getclockinhistorybydate/{userid}/{start_date}/{end_date}/{limit}', ['middleware' => 'crrs','uses' => 'Account\ClockHistoryController@showbydate']);
Route::get('getprofile/{user_id}', ['middleware'=>'crrs','uses'=>'Subscription\SubscriptionController@profile']);
Route::get('getsubscriptions/{user_id}', ['middleware'=>'crrs','uses'=>'Subscription\SubscriptionController@show']);
Route::get('getsubscriptionsbydate/{userid}/{start_date}/{end_date}/{limit}', ['middleware'=>'crrs','uses'=>'Subscription\SubscriptionController@showbydate']);
Route::group(['middleware' => ['cors']], function () {
    Route::post('register', 'Auth\RegisterController@store');
    Route::post('login', 'Auth\LoginController@login');
    //Route::post('login', 'Auth\LoginController@checkcredential');
    Route::post('check', 'Auth\LoginController@checkcredential');
    Route::post('createpayment', 'Subscription\PaymentController@store');
});