<?php

namespace App\Http\Controllers\Booking;

use App\Schedule_visit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VisitController extends Controller
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
     $visit = Schedule_visit::create($request->all());
     
     //send email to provider and user when visit is scheduled 
     return response()->json($visit,201);
   }
}
