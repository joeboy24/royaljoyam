<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Waybill extends Model
{
    protected $fillable = ['user_id', 'stock_no', 'comp_name', 'comp_add', 'comp_contact', 'drv_name', 'drv_contact', 'vno', 'bill_no', 'weight', 'nop', 'tot_qty', 'del_date', 'status', 'del'];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function wbcontent(){
        return $this->hasMany('App\Models\Wbcontent');
    }

    public function scopeActive($query)
    {
        return $query->where('del', 'no');
    }

    public function scopeSearch($query, ?string $term)
    {
        if ($term === null || $term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function ($builder) use ($like) {
            $builder->where('comp_name', 'like', $like)
                ->orWhere('vno', 'like', $like)
                ->orWhere('drv_name', 'like', $like)
                ->orWhere('drv_contact', 'like', $like)
                ->orWhere('bill_no', 'like', $like)
                ->orWhere('stock_no', 'like', $like);
        });
    }

    public function formattedDeliveryDate(): string
    {
        if (empty($this->del_date) || $this->del_date === '0000-00-00') {
            return '—';
        }

        try {
            return Carbon::parse($this->del_date)->format('M. d, Y');
        } catch (\Throwable $th) {
            return '—';
        }
    }

    public static function suggestBillNo(): string
    {
        do {
            $candidate = 'WB-'.now()->format('Ymd').'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('bill_no', $candidate)->exists());

        return $candidate;
    }

    public static function statusOptions(): array
    {
        return ['Pending', 'In Transit', 'Delivered'];
    }
}
