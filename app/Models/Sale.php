<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    //
    protected $fillable = [
        'user_id',
        'user_bv',
        'order_no',
        'qty',
        'tot',
        'pay_mode',
        'buy_name',
        'buy_contact',
        'del_status',
        'discount',
        'payment',
        'change',
        'paid',
        'paid_debt',
        'notes',
    ];

    /**
     * Remaining amount owed on a post-payment (debt) sale.
     */
    public function debtBalance(): float
    {
        if ($this->pay_mode !== 'Post Payment(Debt)' || $this->paid === 'Paid') {
            return 0.0;
        }

        $total = (float) $this->tot;
        $paidDebt = (float) ($this->paid_debt ?? 0);

        if ($paidDebt > 0) {
            return max($total - $paidDebt, 0.0);
        }

        $change = (float) ($this->change ?? 0);
        if ($change < 0) {
            return abs($change);
        }

        return max($total - (float) ($this->payment ?? 0), 0.0);
    }

    public function isUnderpaid(): bool
    {
        return (float) ($this->change ?? 0) < 0;
    }

    public function changeOrBalanceLabel(): string
    {
        return $this->isUnderpaid() ? 'Balance' : 'Change';
    }

    public function changeOrBalanceAmount(): float
    {
        $change = (float) ($this->change ?? 0);

        return $change < 0 ? abs($change) : $change;
    }

    public function saleshistory(){
        return $this->hasMany('App\Models\SalesHistory');
    }

    public function salespayment(){
        return $this->hasMany('App\Models\SalesPayment');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}
