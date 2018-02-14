<?php

namespace App\Http\Controllers\Subscription;
use App\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'user_id' => 'required',
            
        ]);
    }

    public function store(Request $request)
   {
     $this->validator($request->all())->validate();
     //check if date is not expired , update exisitng susbcription and back date by a day
     // carry over credits if date us not expired and create subscription 
     $subscription = Subscription::create($request->all());
	 return response()->json($subscription,201);
   }
   public function checksubscription($end_date)
   {
     $this->validator($request->all())->validate();
     //check if date is not expired , update exisitng susbcription and back date by a day
     // carry over credits if date us not expired and create subscription 
     $subscription = Subscription::create($request->all());
	 return response()->json($subscription,201);
   }

    public function bulk(Request $request)
   {
   $this->validator($request->all())->validate();

    $contacts = $request->all();;

   $contacts= DB::table('users')->insert($contacts);
   return response()->json($contacts,201);
   }

      public function show(Contact $contact)
   {
    $contact = Contact::where('user_id',$contact->user_id) 
               ->orderby('created_at','desc')
               ->get();

    return response()->json($contact,200);
   }
   public function showbygroup($groupid,$user_id)
   {
//$contact = Article::Find($contact);

$contact = DB::table('contactgroups')
            ->join('groups', 'groups.id', '=', 'contactgroups.group_id')
            ->join('contacts', 'contacts.id', '=', 'contactgroups.contact_id')
            ->where([['groups.id',$groupid],['groups.user_id',$user_id]])
            ->select('contacts.*','groups.id as group_id', 'groups.name as group_name','groups.description as group_description')
            ->get();

return response()->json($contact,200);
   }
}
