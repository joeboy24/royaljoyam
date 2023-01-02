<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // 

    protected $fillable = ['std_id', 'user_id', 'fname', 'sname', 'sex', 'dob', 'class', 'guardian', 'contact', 'email', 'residence', 'photo', 'bill'];

    // public function setDateAttribute($value){
    //     $this->attributes['dob'] = (new Carbon($value))->format('d/m/y');
    // }

    public function fees(){
        return $this->hasMany('App\Models\Fee');
    }

    public function stage(){
        return $this->belongsTo('App\Models\Stage');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

}
 