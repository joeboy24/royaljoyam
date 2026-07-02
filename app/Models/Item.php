<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    public const LOW_STOCK_THRESHOLD = 5;

    protected $fillable = ['item_no', 'user_id', 'name', 'desc', 'cat', 'brand', 'barcode', 'qty', 'price', 'cost_price', 'q1', 'q2', 'q3', 'b1', 'b2', 'b3'];

    public function stockLevel(int $lowStockThreshold = self::LOW_STOCK_THRESHOLD): string
    {
        $qty = (int) $this->qty;

        if ($qty <= 0) {
            return 'out';
        }

        if ($qty <= $lowStockThreshold) {
            return 'low';
        }

        return 'ok';
    }

    public function stockBadgeLabel(int $lowStockThreshold = self::LOW_STOCK_THRESHOLD): string
    {
        return match ($this->stockLevel($lowStockThreshold)) {
            'out' => 'Out of stock',
            'low' => 'Low stock',
            default => 'In stock',
        };
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function category(){
        return $this->belongsTo('App\Models\Category');
    }

    public function itemimage(){
        return $this->belongsTo('App\Models\ItemImage');
    }

    public function wbdistribution(){
        return $this->hasMany('App\Models\Wbdistribution');
    }
}
