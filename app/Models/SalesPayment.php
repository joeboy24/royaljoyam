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

    public function scopePaidDebtSearch($query, ?string $term)
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function ($builder) use ($like) {
            $builder->whereHas('sale', function ($saleQuery) use ($like) {
                $saleQuery->where('order_no', 'like', $like)
                    ->orWhere('buy_name', 'like', $like)
                    ->orWhere('buy_contact', 'like', $like);
            })->orWhereHas('user', function ($userQuery) use ($like) {
                $userQuery->where('name', 'like', $like);
            });
        });
    }
}
