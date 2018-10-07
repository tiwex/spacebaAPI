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
      $ppc=DB::table('space_network_settings')->value('price_per_credit');
      $subscribed_credits=$amount/$ppc;
      $is_rolled_over=0;
      $credits_rolled_in=0;
      $credits_rolled_over=0;
      $transaction_ref=uniqid('hr_', true);
      $s_subscription=array();
      $start_date= "";
      $end_date="";
      $usage=-1;
      $service_id = DB::table('services')->where([['code','hora'],['id',$hora]])->value('id');

      $subscribe= DB::table('subscription')
                ->where([['user_id',$user_id],['subscription.service_id',$service_id]])
               ->orderBy('create_time','desc')
               ->first();

    $today = Carbon::now();
    $edate = Carbon::createFromFormat('Y-m-d', $subscribe->subscription_end);
    $expired=$today->gt($edate);
    $active=$today->lt($edate);
 $s_date = $subscribe->subscription_start;
  $e_date =$subscribe->subscription_end;
 // $s_date->toDateString();
//  $e_date->toDateString();
      if (empty($subscribe))
      {
          $start_date= Carbon::now();
          $end_date = $start_date->addMonths(1);
          $end_date=$end_date->toDateString();
          $start_date=$start_date->toDateString();

          //add services subscription herre 
      }

      elseif ($expired)
      {
          $start_date= Carbon::today();
         $end_date = Carbon::today()->addMonths(1);
          //$end_date=$end_date->toDateString();
          //$start_date=$start_date->toDateString();
           //$credits_rolled_in=9;

      }
  elseif ($active)
      {

          //$start_date=Carbon::createFromFormat('Y-m-d', $subscribe->subscription_end);
          //$end_date = Carbon::createFromFormat('Y-m-d', $subscribe->subscription_end)->addDays(30);
        $start_date= Carbon::today();

         $end_date = Carbon::today()->addMonths(1);
         
          $end_date->toDateString();
          $start_date->toDateString();
          $is_rolled_over=1;
          $usage = DB::table('clock_in_history')
            ->join('merchant_settings', 'merchant_settings.merchant_id', '=', 'clock_in_history.space_provider_id')
            ->where([['clock_in_history.user_id',$user_id]])
            ->whereBetween('clock_in_history.create_time',array($s_date,$e_date))
            ->select(DB::raw('SUM(if (timestampdiff(minute,clock_in_history.clock_in_time,clock_in_history.clock_out_time) < 6,0,
                            merchant_settings.credits_per_hour * ceil(timestampdiff(minute,clock_in_history.clock_in_time,clock_in_history.clock_out_time)/60 ))) as credit_used'))
            ->first();
          $credits_rolled_in=($subscribe->subscribed_credits)-$usage->credit_used;
          $update_credit=DB::table('subscription')
            ->where('id', $subscribe->id)
            ->update(['credits_rolled_over' => $credits_rolled_in]);
            $update_date=DB::table('subscription')
            ->where('id', $subscribe->id)
            ->update(['subscription_end'=>Carbon::today()->addDays(-1)->toDateString()]);
      }
$array =array( "user_id"=>$user_id,"service_id"=>$service_id,"payment_id"=>$payment_id,"subscribed_credits"=>$subscribed_credits,"subscription_start"=>$start_date,"subscription_end"=>$end_date,"price"=>$amount,"is_rolled_over"=>$is_rolled_over,"credits_rolled_over"=>$credits_rolled_over,"credits_rolled_in"=>$credits_rolled_in,"transaction_ref"=>$transaction_ref,"create_time"=>Carbon::today());
      $h_subscription=Subscription::create($array);
      //$h_subscription=$array;
foreach($services as $value)
{
 $sid=$value["id"];
 $smth=$value["month"];
 $days[]=$smth*30;
 $amount=0;
       
$c_services= DB::table('services')
            ->join('subscription', 'services.id', '=', 'subscription.service_id')
              ->where('user_id',$user_id)
              ->where('subscription.service_id',$sid)
              ->select('services.id','services.name','services.description','services.amount','subscription.subscription_start','subscription.subscription_end')
              ->orderBy('create_time','desc')
              ->first();
 
  if (!empty($c_services))
  {
        $amount=$smth*$value->amount;
        $edate=$value->subscription_end;
        $edate = Carbon::createFromFormat('Y-m-d', $edate);
        $expired=$today->gt($edate);
        $active=$today->lt($edate);
        
        if ($expired)
      {
          $s_date= Carbon::now();
          $e_date = Carbon::now()->addMonths($smth);
          $e_date->toDateString();
          $s_date->toDateString();

      }
       elseif ($active)
      {

          $s_date= Carbon::now();
          $e_date = Carbon::now()->addMonths($smth);
           $e_date->toDateString();
          $s_date->toDateString();
          //update dates

      }

  }
 
  else
  {
            $amount=DB::table('services')->where('id',$sid)->value('amount');
            $amount=$smth*$amount;
            $s_date= Carbon::now();
          $e_date = Carbon::now()->addMonths($smth);
           $e_date->toDateString();
          $s_date->toDateString();
  }

  $transaction_ref=uniqid('sr_', true);
      $array =array( "user_id"=>$user_id,"service_id"=>$sid,"payment_id"=>$payment_id,"subscribed_credits"=>0,"subscription_start"=>$s_date,"subscription_end"=>$e_date,"price"=>$amount,"is_rolled_over"=>0,"credits_rolled_over"=>0,"credits_rolled_in"=>0,"transaction_ref"=>$transaction_ref,"create_time"=>Carbon::today());
       $s_subscription[] = Subscription::create($array);

}
  
  $subscription=array("h_susbcription"=>$h_subscription,"s_subscription"=>$s_subscription,"usage"=>$days);
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
  
$subscribe= DB::table('subscriptio'n)
                ->where([['user_id',$user_id],['subscription.service_id',$service_id]])
               ->orderBy('create_time','desc')
               ->first();
if (empty($subscribe))

{
  $name= DB::table('user')
              ->where('id',$user_id)
              ->value('name');
  if(empty($name)) $status ="user doesnt exist";
    $status="no active subscription";
  $profile= array("name"=>$name,"status"=>$status);
}
else
{
$name= DB::table('user')
            ->join('subscription', 'user.id', '=', 'subscription.user_id')
              ->where('user_id',$user_id)
              ->value('name');

$services= DB::table('services')
            ->join('subscription', 'services.id', '=', 'subscription.service_id')
              ->where('user_id',$user_id)
              ->whereNotIn('subscription.service_id',[$service_id])
              ->select('services.id','services.code','services.name','services.description','services.amount','subscription.subscription_start','subscription.subscription_end')
              ->get();
              
$today = Carbon::now();
$sdate = Carbon::createFromFormat('Y-m-d', $subscribe->subscription_start);
$edate = Carbon::createFromFormat('Y-m-d', $subscribe->subscription_end);
$s_valid=$today->gt($edate);


$edate=$edate->toDateTimeString();
$credit=$subscribe->subscribed_credits;
$rolled_in=$subscribe->credits_rolled_in;

/*$usage = DB::table('clock_in_histories')
            ->join('spaces', 'spaces.id', '=', 'clock_in_histories.space_id')
            ->where([['clock_in_histories.user_id',$user_id]])
            ->whereBetween('clock_in_histories.clock_in_time',[$subscribe->start_date,$subscribe->end_date])
            ->select(DB::raw('SUM(timestampdiff(hour,clock_in_histories.clock_in_time,clock_in_histories.clock_out_time)) as hours_used,SUM(timestampdiff( hour,clock_in_histories.clock_in_time,clock_in_histories.clock_out_time)*spaces.credits_per_hour) as credit_used'))
            ->first();*/
   $usage = DB::table('clock_in_history')
            ->join('merchant_settings', 'merchant_settings.merchant_id', '=', 'clock_in_history.space_provider_id')
            ->where([['clock_in_history.user_id',$user_id]])
            ->whereBetween('clock_in_history.create_time',[$subscribe->subscription_start,$subscribe->subscription_end])
            ->select(DB::raw('SUM(if (timestampdiff(minute,clock_in_history.clock_in_time,clock_in_history.clock_out_time) < 6,0,
                            merchant_settings.credits_per_hour * ceil(timestampdiff(minute,clock_in_history.clock_in_time,clock_in_history.clock_out_time)/60 ))) as credit_used'))
            ->first();

if ($s_valid==true) 
  {
    $balance = 0;

  }
else $balance = ($credit+$rolled_in) - $usage->credit_used ;

$profile= array("name"=>$name,"subscription"=>$subscribe,"services"=>$services,"usage"=>$usage,"balance"=>$balance);
//send an email after subscription c
}
//$profile=$sdate;

return response()->json($profile,200);
   }
public function show($user_id)
{
//$contact = Article::Find($contact);

$history= DB::table('subscription')
              ->where('user_id',$user_id)
              ->select('subscription_start as start_date','subscription_start as end_date','subscribed_credits','price as amount','create_time as created_at',DB::raw('(select channel from payment where payment.id=subscription.payment_id) channel ,
                (select name from services where services.id=subscription.service_id) name'))
              ->get();
return response()->json($history,200);
   }
   public function showbydate($user_id,$start_date,$end_date,$limit)
{
//$contact = Article::Find($contact);
if ($start_date=="d" && $end_date=="d")
{


$history= DB::table('subscription')
              ->where('user_id',$user_id)
              ->select('subscription_start as start_date','subscription_start as end_date','subscribed_credits','price as amount','create_time as created_at',DB::raw('(select channel from payment where payment.id=subscription.payment_id) channel ,
                (select name from services where services.id=subscription.service_id) name'))
              ->get();
  }

  else
  {
     if ($start_date==$end_date)

    {
        $end_date=Carbon::createFromFormat('Y-m-d',$end_date)->addDays(1)->toDateString();
    }

      /*$history= DB::table('subscriptions')
              ->where('user_id',$user_id)
              ->whereBetween('created_at',[$start_date,$end_date])
              ->select('start_date','end_date','subscribed_credits','amount','created_at',DB::raw('(select channel from payments where payments.id=subscriptions.payment_id) channel ,
                (select name from services where services.id=subscriptions.service_id) name'))
              ->limit($limit)
              ->get();*/
        $history= DB::table('subscription')
              ->where('user_id',$user_id)
               ->whereBetween('create_time',[$start_date,$end_date])
              ->select('subscription_start as start_date','subscription_start as end_date','subscribed_credits','price as amount','create_time as created_at',DB::raw('(select channel from payment where payment.id=subscription.payment_id) channel ,
                (select name from services where services.id=subscription.service_id) name'))
               ->limit($limit)
              ->get();

  }
return response()->json($history,200);
   }
 public function showservices()
{
//$contact = Article::Find($contact);

$service= DB::table('services')
              ->select('*')
              ->get();

 
return response()->json($service,200);
   }

   public function testservices(Request $request)
{
//$contact = Article::Find($contact);

  $services=$request->input('services');

 print_r($services);
//return response()->json($services,200);
   }
}
