<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesHistory extends Model
{
    //
    protected $fillable = ['user_id', 'sale_id', 'item_id', 'user_bv', 'item_no', 'name', 'qty', 'cost_price', 'unit_price', 'profits', 'tot', 'del_status'];

    public function sales(){
        return $this->belongsTo('App\Models\Sales');
    }

    public function item(){
        return $this->belongsTo('App\Models\Item');
    }
}
