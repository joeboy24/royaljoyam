<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    //
    protected $fillable = ['user_id', 'user_bv', 'order_no', 'qty', 'tot', 'pay_mode', 'buy_name', 'buy_contact', 'del_status', 'discount', 'payment', 'change', 'paid'];

    public function saleshistory(){
        return $this->hasMany('App\Models\SalesHistory');
    }

    public function salespayment(){
        return $this->hasMany('App\Models\SalesPayment');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}
