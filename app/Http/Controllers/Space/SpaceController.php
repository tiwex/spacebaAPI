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

$spaces = DB::table('contactgroups')
            ->join('groups', 'groups.id', '=', 'contactgroups.group_id')
            ->join('contacts', 'contacts.id', '=', 'contactgroups.contact_id')
            ->where([['groups.id',$groupid],['groups.user_id',$user_id]])
            ->select('contacts.*','groups.id as group_id', 'groups.name as group_name','groups.description as group_description')
            ->get();

return response()->json($spaces,200);
   }

   public function showspace($space_id)
   {
//$contact = Article::Find($contact);
/*$space = DB::table('space')
            ->join('locations', 'spaces.id', '=', 'locations.space_id')
           ->where([['spaces.id',$space_id],['spaces.is_active',1]])
           ->select('spaces.id','spaces.name','spaces.title','spaces.description','spaces.credits_per_hour'
           ,DB::raw('(select name from states where id=locations.state_id) state')
           ,DB::raw('(select name from cities where id=locations.city_id) city')
           ,DB::raw('(select name from areas where id=locations.area_id) area'))
           ->get();*/
           

           $space = DB::table('space')
                    ->join('merchantspace', 'space.id', '=', 'merchantspace.space_id')
                    ->join('merchant_settings', 'merchantspace.merchant_id', '=', 'merchant_settings.merchant_id')
                    ->where('space.id',$space_id)
                    ->select('space.id','space.name','space.title','space.description','merchant_settings.credits_per_hour'
                ,DB::raw('(select s.name from location_lga l,states s  where l.id=space.lga_id and l.state_id=s.id ) state')
           ,DB::raw('(select name from location_lga  where id=space.lga_id) city')
           ,DB::raw('(select name from location_area  where id=space.area_id) area'))
           ->get();


$image = DB::table('spaceimage')
           ->where('space_id',$space_id)
           ->select('cloudinary_id','is_featured')
           ->get();
           $c_img=array();

foreach ($image as $value)
{
    $url=Cloudder::secureShow($value->cloudinary_id,array("width"=>0.9, "height"=>0.9, "crop"=>"scale"));
    $featured=$value->is_featured;
    $c_img[]=array("c_img"=>$url,"is_featured"=>$featured);
}
 $type = DB::table('space_types')
           ->where('space_id',$space_id)
           ->select(DB::raw('(select name from types where id=space_types.type_id) type'))
           ->get();
           
$category = DB::table('spacecategory')
           ->where('space_id',$space_id)
           ->select(DB::raw('(select name from spacecategorylist where id=spacecategory.space_category_list_id) category'))
           ->get();
           
$ammenities = DB::table('spaceitem')
           ->where('space_id',$space_id)
           ->select(DB::raw('(select name from spaceitemlist where id=spaceitem.space_item_list_id) facility'))
           ->get();
           
$capacities = DB::table('spacesettings')
           ->where('space_id',$space_id)
           ->select('capacity')
           ->get();
           
/*$layouts = DB::table('space_layouts')
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
           ->get();*/
  $provider = DB::table('merchant')
            ->join('merchantspace', 'merchant.id', '=', 'merchantspace.merchant_id')
             ->join('merchant_settings', 'merchantspace.merchant_id', '=', 'merchant_settings.merchant_id')
           ->where('merchantspace.space_id',$space_id)
           ->select('merchant.name','merchant.address','merchant.phone','merchant.email')
           ->get();

//$review = array("review"=>5,"rating"=>3);
$detail=array("space"=>$space,"image"=>$c_img,"type"=>$type,"category"=>$category,"ammenities"=>$ammenities,
           "capacities"=>$capacities,"provider"=>$provider);
          
return response()->json($detail,200);
   }
   public function showimages($space_id)
   {
//$contact = Article::Find($contact);
$image = DB::table('spaceimage')
           ->where('space_id',$space_id)
           ->select('cloudinary_id','is_featured')
           ->get();
$c_img=array();

foreach ($image as $value)
{
    $url=Cloudder::secureShow($value->cloudinary_id,array("width"=>300, "height"=>100, "crop"=>"scale"));
    $featured=$value->is_featured;
    $c_img[]=array("c_img"=>$url,"is_featured"=>$featured);
}

return response()->json($c_img,200);
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
   public function shownetworksetting()
   {
//$contact = Article::Find($contact);
$setting = DB::table('space_network_settings')
        ->first();


return response()->json($setting,200);
   }
public function showspaces()
{
//$contact = Article::Find($contact);
$spaces = DB::table('spaces')
          ->where('spaces.is_active',1)
          ->whereNotNull('credits_per_hour')
          ->limit(100)
         ->get();
//$space=array();
    foreach ($spaces as $space)
    {
$space1 = DB::table('spaces')
           ->where('spaces.id',$space->id)
           ->select('spaces.id','spaces.name','spaces.title','spaces.description','spaces.credits_per_hour')
          ->get();

$states=DB::table('locations')
        ->where('locations.space_id',$space->id)
        ->select('state_id', DB::raw('(select name from states where id=locations.state_id and locations.space_id='.$space->id.') value'))
        ->get();
 $areas=DB::table('locations')
        ->where('locations.space_id',$space->id)
        ->select('area_id', DB::raw('(select name from areas where id=locations.area_id and locations.space_id='.$space->id.') value'))
        ->get();
$cities=DB::table('locations')
        ->where('locations.space_id',$space->id)
        ->select('city_id', DB::raw('(select name from areas where id=locations.city_id and locations.space_id='.$space->id.') value'))
        ->get();
          
$image= DB::table('space_images')
           ->where('space_id',$space->id)
           ->select('cloudinary_id','is_featured')
           ->get();
           $c_img=array();

    foreach ($image as $value)
    {
    $url=Cloudder::secureShow($value->cloudinary_id,array("width"=>0.9, "height"=>0.9, "crop"=>"scale"));
    $featured=$value->is_featured;
    $c_img[]=array("c_img"=>$url,"is_featured"=>$featured);
    }
 $type = DB::table('space_types')
           ->where('space_id',$space->id)
           ->select('id',DB::raw('(select name from types where id=space_types.type_id) type'))
           ->get();
           
$category = DB::table('space_categories')
           ->where('space_id',$space->id)
           ->select('id',DB::raw('(select name from categories where id=space_categories.category_id) category'))
           ->get();
           
$ammenities = DB::table('space_facilities')
           ->where('space_id',$space->id)
           ->select('id','name')
           ->get();
           
$capacities = DB::table('capacities')
           ->where('space_id',$space->id)
           ->select('id','capacity')
           ->get();
           
$layouts = DB::table('space_layouts')
           ->where('space_id',$space->id)
           ->select('id',DB::raw('(select name from layouts where id=space_layouts.layout_id) layout'),'size')
           ->get();

 $price = DB::table('prices')
           ->where('space_id',$space->id)
           ->select('id','amount','default','min_qty','max_qty','period','is_booking'
           ,(DB::raw('(select name from price_types where id=prices.type_id) type')))
           ->get();

   $detail[]=array("space"=>$space1,"state"=>$states,"area"=>$areas,"city"=>$cities,"type"=>$type,"category"=>$category,"ammenities"=>$ammenities,
           "capacities"=>$capacities,"layouts"=>$layouts,
            "prices"=>$price,"image"=>$c_img);

    }

    return response()->json($detail,200);
}

public function showallhoraspaces()
{

$spaces = DB::table('space')
          ->join('spacesettings', 'space.id', '=', 'spacesettings.space_id')
          ->join('space_price_setting', 'space.id', '=', 'space_price_setting.space_id')
          ->where('spacesettings.is_active',1)
          ->whereNotNull('space_price_setting.credits_per_hour')
          ->select('space.id')
          ->limit(100)
         ->get();

   foreach ($spaces as $sp)
    {

           $space = DB::table('space')
                    ->join('merchantspace', 'space.id', '=', 'merchantspace.space_id')
                    ->join('merchant_settings', 'merchantspace.merchant_id', '=', 'merchant_settings.merchant_id')
                    ->where('space.id',$sp->id)
                    ->select('space.id','space.name','space.title','space.description','merchant_settings.credits_per_hour'
                ,DB::raw('(select s.name from location_lga l,states s  where l.id=space.lga_id and l.state_id=s.id ) state')
           ,DB::raw('(select name from location_lga  where id=space.lga_id) city')
           ,DB::raw('(select name from location_area  where id=space.area_id) area'))
           ->get();
//$c_img=1;

$image = DB::table('spaceimage')
           ->where('space_id',$sp->id)
           ->select('cloudinary_id','is_featured')
           ->get();
           $c_img=array();

foreach ($image as $value)
{
    $url=Cloudder::secureShow($value->cloudinary_id,array("width"=>0.9, "height"=>0.9, "crop"=>"scale"));
    $featured=$value->is_featured;
    $c_img[]=array("c_img"=>$url,"is_featured"=>$featured);
}
 $type = DB::table('space_types')
           ->where('space_id',$sp->id)
           ->select(DB::raw('(select name from types where id=space_types.type_id) type'))
           ->get();
           
$category = DB::table('spacecategory')
           ->where('space_id',$sp->id)
           ->select(DB::raw('(select name from spacecategorylist where id=spacecategory.space_category_list_id) category'))
           ->get();
           
$ammenities = DB::table('spaceitem')
           ->where('space_id',$sp->id)
           ->select(DB::raw('(select name from spaceitemlist where id=spaceitem.space_item_list_id) facility'))
           ->get();
           
$capacities = DB::table('spacesettings')
           ->where('space_id',$sp->id)
           ->select('capacity')
           ->get();
           
  $provider = DB::table('merchant')
            ->join('merchantspace', 'merchant.id', '=', 'merchantspace.merchant_id')
             ->join('merchant_settings', 'merchantspace.merchant_id', '=', 'merchant_settings.merchant_id')
           ->where('merchantspace.space_id',$sp->id)
           ->select('merchant.name','merchant.address','merchant.phone','merchant.email')
           ->get();

//$review = array("review"=>5,"rating"=>3);
$detail[]=array("space"=>$space,"image"=>$c_img,"type"=>$type,"category"=>$category,"ammenities"=>$ammenities,
           "capacities"=>$capacities,"provider"=>$provider);

    }
//return response()->json($spaces,200);
  return response()->json($detail,200);
}

}
