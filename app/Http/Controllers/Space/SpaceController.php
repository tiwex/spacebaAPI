<?php

namespace App\Http\Controllers\Space;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpaceController extends Controller
{
    //
    public function gethoraspaces($location)
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
