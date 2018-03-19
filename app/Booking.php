<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //
    protected $fillable = [
        'space_id','user_id','company_name','price_id','total_price','start_date','end_date','guest'];
}
