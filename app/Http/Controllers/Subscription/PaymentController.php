<?php

namespace App\Http\Controllers\Subscription;
use App\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    //
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'user_id' => 'required',
            
        ]);
    }

    public function store(Request $request)
   {
	 $this->validator($request->all())->validate();
	 $payment = Payment::create($request->all());
     return response()->json($payment,201);
     
     //update subscription or bookings table 
   }

   
}
