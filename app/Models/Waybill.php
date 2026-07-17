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

    public function activeWbcontents()
    {
        return $this->hasMany('App\Models\Wbcontent')->where('del', 'no');
    }

    public function scopeDistributionFilter($query, ?string $filter)
    {
        if ($filter === null || $filter === '' || ! in_array($filter, static::distributionFilterOptions(), true)) {
            return $query;
        }

        return match ($filter) {
            'pending' => $query
                ->whereHas('wbcontent', fn ($q) => $q->where('del', 'no'))
                ->whereDoesntHave('wbcontent', fn ($q) => $q->where('del', 'no')->where('qty_dist', '>', 0)),
            'partial' => $query
                ->whereHas('wbcontent', fn ($q) => $q->where('del', 'no')->where('qty_dist', '>', 0))
                ->whereHas('wbcontent', fn ($q) => $q->where('del', 'no')->whereRaw('CAST(qty_dist AS UNSIGNED) < CAST(qty AS UNSIGNED)')),
            'complete' => $query
                ->whereHas('wbcontent', fn ($q) => $q->where('del', 'no'))
                ->whereDoesntHave('wbcontent', fn ($q) => $q->where('del', 'no')->whereRaw('CAST(qty_dist AS UNSIGNED) < CAST(qty AS UNSIGNED)')),
            default => $query,
        };
    }

    public function distributionRemaining(): int
    {
        return max(0, (int) ($this->qty_total ?? 0) - (int) ($this->qty_distributed ?? 0));
    }

    public function distributionStatus(): string
    {
        $itemCount = (int) ($this->item_count ?? 0);

        if ($itemCount === 0) {
            return 'none';
        }

        $remaining = $this->distributionRemaining();
        $distributed = (int) ($this->qty_distributed ?? 0);

        if ($remaining === 0) {
            return 'complete';
        }

        if ($distributed === 0) {
            return 'pending';
        }

        return 'partial';
    }

    public function hasOpenDistribution(): bool
    {
        return $this->distributionStatus() === 'pending' || $this->distributionStatus() === 'partial';
    }

    public function canDistribute(): bool
    {
        return $this->status === 'Delivered';
    }

    public function syncTotQtyFromContents(): void
    {
        $total = (int) $this->activeWbcontents()->sum('qty');
        $this->tot_qty = (string) $total;
        $this->save();
    }

    public static function syncTotQtyFor(int|string $waybillId): void
    {
        $waybill = static::find($waybillId);
        if ($waybill) {
            $waybill->syncTotQtyFromContents();
        }
    }

    public static function distributionFilterOptions(): array
    {
        return ['pending', 'partial', 'complete'];
    }

    public function scopeActive($query)
    {
        return $query->where('del', 'no');
    }

    public function scopeDeleted($query)
    {
        return $query->where('del', 'yes');
    }

    public function scopeStatusFilter($query, ?string $status)
    {
        if ($status !== null && $status !== '' && in_array($status, static::statusOptions(), true)) {
            return $query->where('status', $status);
        }

        return $query;
    }

    public function scopeDeliveryBetween($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->where('del_date', '>=', $from);
        }

        if ($to) {
            $query->where('del_date', '<=', $to);
        }

        return $query;
    }

    public function scopeOrdered($query, ?string $sort, ?string $direction)
    {
        $columns = [
            'del_date' => 'del_date',
            'created_at' => 'created_at',
        ];

        $column = $columns[$sort] ?? 'id';
        $dir = strtolower((string) $direction) === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($column, $dir);
    }

    public function scopeSearch($query, ?string $term)
    {
        if ($term === null || $term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function ($builder) use ($like) {
            $builder->where('comp_name', 'like', $like)
                ->orWhere('comp_add', 'like', $like)
                ->orWhere('comp_contact', 'like', $like)
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
