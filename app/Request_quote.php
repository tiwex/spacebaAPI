<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Request_quote extends Model
{
    //
    protected $fillable = [
        'space_id','user_id','contact_id','city_id','company_name','category','type','checklist','capacity','other_note','start_date','end_date'];
}
