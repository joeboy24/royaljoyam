<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public const PAY_MODE_CASH = 'Cash';

    public const PAY_MODE_CHEQUE = 'Cheque';

    public const PAY_MODE_MOBILE_MONEY = 'Mobile Money';

    public const PAY_MODE_DEBT = 'Post Payment(Debt)';

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

    public function hasOutstandingDebt(): bool
    {
        return $this->pay_mode === 'Post Payment(Debt)' && $this->paid !== 'Paid';
    }

    public function payModeBadgeClass(): string
    {
        return match ($this->pay_mode) {
            'Cash' => 'dash-sales-badge--cash',
            'Cheque' => 'dash-sales-badge--cheque',
            'Mobile Money' => 'dash-sales-badge--momo',
            'Post Payment(Debt)' => 'dash-sales-badge--debt',
            default => 'dash-sales-badge--neutral',
        };
    }

    public function payModeShortLabel(): string
    {
        return match ($this->pay_mode) {
            'Post Payment(Debt)' => 'Debt',
            'Mobile Money' => 'MoMo',
            default => (string) $this->pay_mode,
        };
    }

    /** @return array{class: string, label: string}|null */
    public function paymentStatusBadge(): ?array
    {
        if ($this->hasOutstandingDebt()) {
            return [
                'class' => 'dash-sales-payment-badge--debt',
                'label' => 'Outstanding',
            ];
        }

        if ($this->pay_mode === 'Post Payment(Debt)' && $this->paid === 'Paid') {
            return [
                'class' => 'dash-sales-payment-badge--paid',
                'label' => 'Debt cleared',
            ];
        }

        if ($this->isUnderpaid()) {
            return [
                'class' => 'dash-sales-payment-badge--warn',
                'label' => 'Underpaid',
            ];
        }

        return null;
    }

    /**
     * Match pay_mode values stored with alternate casing (legacy rows).
     */
    public function scopeMatchingPayMode($query, string $payMode)
    {
        $variants = match ($payMode) {
            self::PAY_MODE_CASH => [self::PAY_MODE_CASH, 'cash'],
            self::PAY_MODE_CHEQUE => [self::PAY_MODE_CHEQUE, 'cheque'],
            self::PAY_MODE_MOBILE_MONEY => [self::PAY_MODE_MOBILE_MONEY],
            self::PAY_MODE_DEBT => [self::PAY_MODE_DEBT],
            default => [$payMode],
        };

        return $query->whereIn('pay_mode', $variants);
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
