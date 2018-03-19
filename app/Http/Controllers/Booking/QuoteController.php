<?php

namespace App\Http\Controllers\Booking;

use App\Request_quote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuoteController extends Controller
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
     //$this->validator($request->all())->validate();
     $visit = Request_quote::create($request->all());
     
     //send email to provider and alert admin if space info is not available
     return response()->json($visit,201);
   }
}
