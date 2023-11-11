<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    //
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    
    public function sale(){
        return $this->belongsTo('App\Models\Sale');
    }
}
