<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waybill extends Model
{
    //
    protected $fillable = ['user_id', 'stock_no', 'comp_name', 'comp_add', 'comp_contact', 'drv_name', 'drv_contact', 'vno', 'bill_no', 'weight', 'nop', 'tot_qty', 'del_date', 'status'];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function wbcontent(){
        return $this->hasMany('App\Models\Wbcontent');
    }
    
}
