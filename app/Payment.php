<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    protected $fillable = [
        'user_id', 'is_verified','amount','pay_ref','channel','service_id'];
      //  protected $table = 'payments';
}
