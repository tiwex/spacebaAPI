<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    //
    protected $fillable = [
        'user_id', 'subscribed_credits','subscription_start','subscription_end','credits_spent','credits_remaining','amount','is_rolled_over','credits_rolled_over','credits_rolled_in','auto_renew','transaction_ref'];
}

