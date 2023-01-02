<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    //  

    protected $fillable = ['student_id', 'fullname', 'user_id', 'class', 'term', 'year'];

    public function student(){
        return $this->belongsTo('App\Models\Student');
    }

}
 