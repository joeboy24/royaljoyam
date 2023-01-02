<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    //
    protected $fillable = ['user_id', 'item_id', 'item_no', 'name', 'qty', 'cost_price', 'unit_price', 'profits', 'tot'];

}
