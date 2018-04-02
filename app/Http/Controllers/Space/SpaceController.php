<?php

namespace App\Http\Controllers\Space;

use Cloudder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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

   public function showspace($space_id)
   {
//$contact = Article::Find($contact);
$space = DB::table('spaces')
            ->join('locations', 'spaces.id', '=', 'locations.space_id')
           ->where([['spaces.id',$space_id],['spaces.is_active',1]])
           ->select('spaces.id','spaces.name','spaces.title','spaces.description','spaces.credits_per_hour'
           ,DB::raw('(select name from states where id=locations.state_id) state')
           ,DB::raw('(select name from cities where id=locations.city_id) city')
           ,DB::raw('(select name from areas where id=locations.area_id) area'))
           ->get();
$image = DB::table('space_images')
           ->where('space_id',$space_id)
           ->select('cloudinary_id','is_featured')
           ->get();
 $type = DB::table('space_types')
           ->where('space_id',$space_id)
           ->select(DB::raw('(select name from types where id=space_types.type_id) type'))
           ->get();
           
$category = DB::table('space_categories')
           ->where('space_id',$space_id)
           ->select(DB::raw('(select name from categories where id=space_categories.category_id) category'))
           ->get();
           
$ammenities = DB::table('space_facilities')
           ->where('space_id',$space_id)
           ->select('name')
           ->get();
           
$capacities = DB::table('capacities')
           ->where('space_id',$space_id)
           ->select('capacity')
           ->get();
           
$layouts = DB::table('space_layouts')
           ->where('space_id',$space_id)
           ->select(DB::raw('(select name from layouts where id=space_layouts.layout_id) layout'),'size')
           ->get();

 $price = DB::table('prices')
           ->where('space_id',$space_id)
           ->select('id','amount','default','min_qty','max_qty','period','is_booking'
           ,(DB::raw('(select name from price_types where id=prices.type_id) type')))
           ->get();
$provider = DB::table('spaces')
            ->join('space_providers', 'spaces.space_provider_id', '=', 'space_providers.id')
           ->where('spaces.id',$space_id)
           ->select('space_providers.contact_name','space_providers.cloudinary_id','space_providers.created_at',
              DB::raw('(select count(*) from bookings where bookings.space_id='.$space_id.') +
              (select count(*) from schedule_visits s where s.space_id='.$space_id.') +
              (select count(*) from request_quotes rq where rq.space_id='.$space_id.') leads'))
           ->get();
$review = array("review"=>5,"rating"=>3);
$detail=array("space"=>$space,"image"=>$image,"type"=>$type,"category"=>$category,"ammenities"=>$ammenities,
           "capacities"=>$capacities,"layouts"=>$layouts,
            "prices"=>$price,"provider"=>$provider,"score"=>$review);
          
return response()->json($detail,200);
   }
   public function showimages($space_id)
   {
//$contact = Article::Find($contact);
$image = DB::table('space_images')
           ->where('space_id',$space_id)
           ->select('cloudinary_id','is_featured')
           ->get();


return response()->json($image,200);
   }
public function showtype($space_id)
   {
//$contact = Article::Find($contact);
$type = DB::table('space_types')
           ->where('space_id',$space_id)
           ->select(DB::raw('(select name from types where id=space_types.type_id) type'))
           ->get();


return response()->json($type,200);
   }
}
