<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wbcontent extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'waybill_id', 'item_id', 'qty', 'qty_dist'];

    public function waybill(){
        return $this->belongsTo('App\Models\Waybill');
    }

    public function item(){
        return $this->belongsTo('App\Models\Item');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}
