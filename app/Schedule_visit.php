<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule_visit extends Model
{
    //
    protected $fillable = [
        'space_id','user_id','company_name','contact_id','visit_date','othernotes'];
}
