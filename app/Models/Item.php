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

    public function branchQtyTotal(int $maxBranches = 7): int
    {
        $total = 0;

        for ($i = 1; $i <= $maxBranches; $i++) {
            $total += max(0, (int) ($this->{'q' . $i} ?? 0));
        }

        return $total;
    }

    public function branchQtyColumn(int|string $branchId): string
    {
        return 'q' . $branchId;
    }

    public function restoreCartStockReservation(int|string $branchId, int $qty): void
    {
        if ($qty <= 0) {
            return;
        }

        $column = $this->branchQtyColumn($branchId);
        $this->qty = (int) $this->qty + $qty;
        $this->$column = max(0, (int) ($this->$column ?? 0)) + $qty;
        $this->save();
    }

    public function availableBranchQty(int|string $branchId): int
    {
        $column = $this->branchQtyColumn($branchId);

        return max(0, (int) ($this->$column ?? 0));
    }

    public function reserveCartStock(int|string $branchId, int $qty): bool
    {
        if ($qty <= 0) {
            return false;
        }

        $column = $this->branchQtyColumn($branchId);
        $branchQty = $this->availableBranchQty($branchId);

        if ($qty > $branchQty) {
            return false;
        }

        $this->qty = (int) $this->qty - $qty;
        $this->$column = $branchQty - $qty;
        $this->save();

        return true;
    }

    public function needsGeneralQtyRepair(): bool
    {
        $generalQty = (int) $this->qty;

        return $generalQty < 0 || $generalQty < $this->branchQtyTotal();
    }

    public function repairGeneralQty(): bool
    {
        if (!$this->needsGeneralQtyRepair()) {
            return false;
        }

        $this->qty = max($this->branchQtyTotal(), 0);
        $this->save();

        return true;
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
