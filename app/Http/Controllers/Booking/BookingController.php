<?php

namespace App\Http\Controllers\Booking;


use App\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    //
   //
   protected function validator(array $data)
   {
       return Validator::make($data, [
           'user_id' => 'required',
           
       ]);
   }

   public function store(Request $request)
  {
    //$this->validator($request->all())->validate();
    $booking = Booking::create($request->all());
    
    //send email based on payment type , if card , card will be billed after confirmation
    // Make payment
    return response()->json($booking,201);

  }
  
}
