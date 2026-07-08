<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    protected $fillable = [
        'user_id',
        'sale_id',
        'amt_paid',
        'bal',
        'del',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    
    public function sale(){
        return $this->belongsTo('App\Models\Sale');
    }
}
