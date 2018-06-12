<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class ClockHistoryController extends Controller
{
    //
    public function show($user_id)
   {
//$contact = Article::Find($contact);

$history = DB::table('clock_in_histories')
            ->join('spaces', 'spaces.id', '=', 'clock_in_histories.space_id')
            ->where([['clock_in_histories.user_id',$user_id]])
            ->select(DB::raw('clock_in_histories.clock_in_time,clock_in_histories.clock_out_time,
            timestampdiff(hour,clock_in_histories.clock_in_time,clock_in_histories.clock_out_time) as hours_used,
            timestampdiff(hour,clock_in_histories.clock_in_time,clock_in_histories.clock_out_time)*spaces.credits_per_hour as credit_used,
             spaces.credits_per_hour'),
             DB::raw('(select sp.name from space_providers sp,spaces s where sp.id=s.space_provider_id
              and s.id=clock_in_histories.space_id) provider'))
            ->orderBy('clock_in_histories.created_at','desc')
            ->get();

return response()->json($history,200);
   }

    public function showbydate($user_id,$start_date,$end_date,$limit)
   {
//$contact = Article::Find($contact);
if (empty($start_date) && empty($end_date))
{
$history = DB::table('clock_in_histories')
            ->join('spaces', 'spaces.id', '=', 'clock_in_histories.space_id')
            ->where([['clock_in_histories.user_id',$user_id]])
            ->select(DB::raw('clock_in_histories.clock_in_time,clock_in_histories.clock_out_time,
            timestampdiff(hour,clock_in_histories.clock_in_time,clock_in_histories.clock_out_time) as hours_used,
            timestampdiff(hour,clock_in_histories.clock_in_time,clock_in_histories.clock_out_time)*spaces.credits_per_hour as credit_used,
             spaces.credits_per_hour'),
             DB::raw('(select sp.name from space_providers sp,spaces s where sp.id=s.space_provider_id
              and s.id=clock_in_histories.space_id) provider'))
            ->orderBy('clock_in_histories.created_at','desc')
            ->get();
}
else
{
    if ($start_date==$end_date)

    {
        $end_date=Carbon::createFromFormat('Y-m-d',$end_date)->addDays(1)->toDateString();
    }

  $history = DB::table('clock_in_histories')
            ->join('spaces', 'spaces.id', '=', 'clock_in_histories.space_id')
            ->where([['clock_in_histories.user_id',$user_id]])
            ->whereBetween('clock_in_histories.clock_in_time',[$start_date,$end_date])
            ->select(DB::raw('clock_in_histories.clock_in_time,clock_in_histories.clock_out_time,
            timestampdiff(hour,clock_in_histories.clock_in_time,clock_in_histories.clock_out_time) as hours_used,
            timestampdiff(hour,clock_in_histories.clock_in_time,clock_in_histories.clock_out_time)*spaces.credits_per_hour as credit_used,
             spaces.credits_per_hour'),
             DB::raw('(select sp.name from space_providers sp,spaces s where sp.id=s.space_provider_id
              and s.id=clock_in_histories.space_id) provider'))
            ->orderBy('clock_in_histories.created_at','desc')
            ->limit($limit);
            ->get();
}
return response()->json($history,200);
   
   }

  
}
