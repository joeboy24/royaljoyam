<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wbdistribution extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'waybill_id', 'item_id', 'q1', 'q2', 'q3', 'q4', 'q5', 'q6', 'q7'];

    public function waybill(){
        return $this->belongsTo('App\Models\Waybill');
    }

    public function item(){
        return $this->belongsTo('App\Models\Item');
    }

}
