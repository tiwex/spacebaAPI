<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    //
    protected $fillable = [
        'user_id','service_id','payment_id','subscribed_credits','start_date','end_date','amount','is_rolled_over','credits_rolled_over','credits_rolled_in','transaction_ref'];
}

