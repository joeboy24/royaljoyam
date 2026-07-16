<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    //
    protected $fillable = ['user_id', 'sale_id', 'item_id', 'user_bv', 'item_no', 'name', 'qty', 'cost_price', 'unit_price', 'profits', 'tot', 'del_status', 'order_date'];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function scopeReportSearch($query, ?string $term)
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function ($builder) use ($like) {
            $builder->where('item_no', 'like', $like)
                ->orWhere('name', 'like', $like)
                ->orWhere('order_date', 'like', $like)
                ->orWhereHas('user', function ($userQuery) use ($like) {
                    $userQuery->where('name', 'like', $like);
                });
        });
    }
    
}
