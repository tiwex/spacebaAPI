<?php

namespace App\Http\Controllers\Subscription;
use App\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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


      $user_id=$request->input('user_id');
      $amount=$request->input('amount');
      $hora=$request->input('hora_service_id');
      $services=$request->input('services');
      $payment_id=$request->input('payment_id');
      $ppc=$setting = DB::table('space_network_settings')->value('price_per_credit');
      $subscribed_credits=$amount/$ppc;
      $is_rolled_over=0;
      $credits_rolled_in=0;
      $credits_rolled_over=0;
      $transaction_ref=uniqid('hr_', true);
      $s_subscription=array();
      $start_date= "";
      $end_date="";
      $service_id = DB::table('services')->where([['code','hora'],['id',$hora]])->value('id');

      $subscribe= DB::table('subscriptions')
                ->where([['user_id',$user_id],['subscriptions.service_id',$service_id]])
               ->latest()
               ->first();
$services= DB::table('services')
            ->join('subscriptions', 'services.id', '=', 'subscriptions.service_id')
              ->where('user_id',$user_id)
              ->whereIn('subscriptions.service_id',[$services])
              ->select('services.id','services.name','services.description','services.amount','subscriptions.start_date','subscriptions.end_date')
              ->get();

    $today = Carbon::now();
    $edate = Carbon::createFromFormat('Y-m-d', $subscribe->end_date);
    $expired=$today->gt($edate);
    $active=$today->lt($edate);


      if (empty($subscribe))
      {
          $start_date= Carbon::now();
          $end_date = $start_date->addDays(30);
          $end_date=$end_date->toDateString();
          $start_date=$start_date->toDateString();
      }

      elseif ($expired)
      {
          $start_date= Carbon::today();
         $end_date = Carbon::today()->addDays(30);
          //$end_date=$end_date->toDateString();
          //$start_date=$start_date->toDateString();
           $credits_rolled_in=9;

      }
  elseif ($active)
      {

          $start_date=$edate;
          $end_date = $start_date->addDays(30);
          $end_date=$end_date->toDateString();
          $start_date=$start_date->toDateString();
          $is_rolled_over=1;
          $usage = DB::table('clock_in_histories')
            ->join('spaces', 'spaces.id', '=', 'clock_in_histories.space_id')
            ->where([['clock_in_histories.user_id',$user_id]])
            ->whereBetween('clock_in_histories.clock_in_time',[$subscribe->start_date,$subscribe->end_date])
            ->select(DB::raw('SUM(timestampdiff(hour,clock_in_histories.clock_in_time,clock_in_histories.clock_out_time)) as hours_used,SUM(timestampdiff( hour,clock_in_histories.clock_in_time,clock_in_histories.clock_out_time)*spaces.credits_per_hour) as credit_used'))
            ->first();
          $credits_rolled_in=($subscribe->subscribed_credits)-$usage->credit_used;
          $update_credit=DB::table('subscriptions')
            ->where('id', $subscribe->id)
            ->update(['credits_rolled_over' => $credits_rolled_in]);
      }
$array =array( "user_id"=>$user_id,"service_id"=>$service_id,"payment_id"=>$payment_id,"subscribed_credits"=>$subscribed_credits,"start_date"=>$start_date,"end_date"=>$end_date,"amount"=>$amount,"is_rolled_over"=>$is_rolled_over,"credits_rolled_over"=>$credits_rolled_over,"credits_rolled_in"=>$credits_rolled_in,"transaction_ref"=>$transaction_ref);
      //$h_subscription=Subscription::create($array);
$h_subscription=$array;
       


       if (!empty($services))
    {

      foreach ($services as $value) 
      {
        $edate=$value->end_date;
        $edate = Carbon::createFromFormat('Y-m-d', $edate);
        $expired=$today->gt($edate);
        $active=$today->lt($edate);

      if ($expired)
      {
          $start_date= Carbon::now();
          $end_date = $start_date->addDays(30);
          $end_date=$end_date->toDateString();
          $start_date=$start_date->toDateString();

      }
       elseif ($active)
      {

          $start_date=$edate;
          $end_date = $start_date->addDays(30);
           $end_date=$end_date->toDateString();
          $start_date=$start_date->toDateString();

      }
      $transaction_ref=uniqid('sr_', true);
       $array=array( "user_id"=>$user_id,"service_id"=>$value->id,"payment_id"=>$payment_id,"subscribed_credits"=>$subscribed_credits,"start_date"=>$start_date,"end_date"=>$end_date,"amount"=>$amount,"is_rolled_over"=>$is_rolled_over,"credits_rolled_over"=>$credits_rolled_over,"credits_rolled_in"=>$credits_rolled_over,"transaction_ref"=>$transaction_ref);
       $s_subscription[] = Subscription::create($array);
      }
    }

  $subscription=array("h_susbcription"=>$h_subscription,"s_subscription"=>$s_subscription);
     //send email on subscription
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

    public function profile ($user_id)
   {
//$contact = Article::Find($contact);

    $service_id = DB::table('services')->where('code','hora')->value('id');
  
$subscribe= DB::table('subscriptions')
                ->where([['user_id',$user_id],['subscriptions.service_id',$service_id]])
               ->latest()
               ->first();
if (empty($subscribe))

{
  $name= DB::table('users')
              ->where('id',$user_id)
              ->value('name');
  if(empty($name)) $status ="user doesnt exist";
    $status="no active subscription";
  $profile= array("name"=>$name,"status"=>$status);
}
else
{
$name= DB::table('users')
            ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
              ->where('user_id',$user_id)
              ->value('name');

$services= DB::table('services')
            ->join('subscriptions', 'services.id', '=', 'subscriptions.service_id')
              ->where('user_id',$user_id)
              ->whereNotIn('subscriptions.service_id',[$service_id])
              ->select('services.id','services.code','services.name','services.description','services.amount','subscriptions.start_date','subscriptions.end_date')
              ->get();
              
$today = Carbon::now();
$edate = Carbon::createFromFormat('Y-m-d', $subscribe->end_date);
$sdate = Carbon::createFromFormat('Y-m-d', $subscribe->start_date);
$s_valid=$today->gt($edate);


$edate=$edate->toDateTimeString();
$credit=$subscribe->subscribed_credits;
$rolled_in=$subscribe->credits_rolled_in;

$usage = DB::table('clock_in_histories')
            ->join('spaces', 'spaces.id', '=', 'clock_in_histories.space_id')
            ->where([['clock_in_histories.user_id',$user_id]])
            ->whereBetween('clock_in_histories.clock_in_time',[$subscribe->start_date,$subscribe->end_date])
            ->select(DB::raw('SUM(timestampdiff(hour,clock_in_histories.clock_in_time,clock_in_histories.clock_out_time)) as hours_used,SUM(timestampdiff( hour,clock_in_histories.clock_in_time,clock_in_histories.clock_out_time)*spaces.credits_per_hour) as credit_used'))
            ->first();

if ($s_valid==true) 
  {
    $balance = 0;

  }
else $balance = ($credit+$credits_rolled_in) - $usage->credit_used ;

$profile= array("name"=>$name,"subscription"=>$subscribe,"services"=>$services,"usage"=>$usage,"balance"=>$balance);
}
//$profile=$sdate;

return response()->json($profile,200);
   }
public function show($user_id)
{
//$contact = Article::Find($contact);

$history= DB::table('subscriptions')
              ->where('user_id',$user_id)
              ->select('start_date','end_date','subscribed_credits','amount','created_at',DB::raw('(select channel from payments where payments.id=subscriptions.payment_id) channel ,
                (select name from services where services.id=subscriptions.service_id) name'))
              ->get();
return response()->json($history,200);
   }
   public function showbydate($user_id,$start_date,$end_date,$limit)
{
//$contact = Article::Find($contact);
if ($start_date=="d" && $end_date=="d")
{


$history= DB::table('subscriptions')
              ->where('user_id',$user_id)
              ->select('start_date','end_date','subscribed_credits','amount','created_at',DB::raw('(select channel from payments where payments.id=subscriptions.payment_id) channel ,
                (select name from services where services.id=subscriptions.service_id) name'))
              ->get();
  }

  else
  {
     if ($start_date==$end_date)

    {
        $end_date=Carbon::createFromFormat('Y-m-d',$end_date)->addDays(1)->toDateString();
    }

      $history= DB::table('subscriptions')
              ->where('user_id',$user_id)
              ->whereBetween('created_at',[$start_date,$end_date])
              ->select('start_date','end_date','subscribed_credits','amount','created_at',DB::raw('(select channel from payments where payments.id=subscriptions.payment_id) channel ,
                (select name from services where services.id=subscriptions.service_id) name'))
              ->limit($limit)
              ->get();

  }
return response()->json($history,200);
   }

}
