<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemAudit extends Model
{
    //

    protected $fillable = ['item_no', 'user_id', 'name', 'desc', 'cat', 'brand', 'barcode', 'qty', 'price', 'cost_price', 'q1', 'q2', 'q3', 'b1', 'b2', 'b3'];

}
