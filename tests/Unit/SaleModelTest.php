<?php

namespace Tests\Unit;

use App\Models\Sale;
use PHPUnit\Framework\TestCase;

class SaleModelTest extends TestCase
{
    public function test_debt_balance_uses_paid_debt_when_set(): void
    {
        $sale = new Sale([
            'pay_mode' => 'Post Payment(Debt)',
            'paid' => 'No',
            'tot' => 3950,
            'paid_debt' => 2700,
            'change' => -1250,
            'payment' => 2700,
        ]);

        $this->assertSame(1250.0, $sale->debtBalance());
    }

    public function test_debt_balance_falls_back_to_negative_change(): void
    {
        $sale = new Sale([
            'pay_mode' => 'Post Payment(Debt)',
            'paid' => 'No',
            'tot' => 3950,
            'paid_debt' => 0,
            'change' => -1250,
            'payment' => 2700,
        ]);

        $this->assertSame(1250.0, $sale->debtBalance());
    }

    public function test_change_or_balance_label_for_underpaid_sale(): void
    {
        $sale = new Sale(['change' => -1250]);

        $this->assertTrue($sale->isUnderpaid());
        $this->assertSame('Balance', $sale->changeOrBalanceLabel());
        $this->assertSame(1250.0, $sale->changeOrBalanceAmount());
    }

    public function test_change_or_balance_label_for_cash_sale(): void
    {
        $sale = new Sale(['change' => 30]);

        $this->assertFalse($sale->isUnderpaid());
        $this->assertSame('Change', $sale->changeOrBalanceLabel());
        $this->assertSame(30.0, $sale->changeOrBalanceAmount());
    }

    public function test_payment_status_badge_for_outstanding_debt(): void
    {
        $sale = new Sale([
            'pay_mode' => 'Post Payment(Debt)',
            'paid' => 'No',
        ]);

        $this->assertTrue($sale->hasOutstandingDebt());
        $this->assertSame([
            'class' => 'dash-sales-payment-badge--debt',
            'label' => 'Outstanding',
        ], $sale->paymentStatusBadge());
        $this->assertSame('Debt', $sale->payModeShortLabel());
        $this->assertSame('dash-sales-badge--debt', $sale->payModeBadgeClass());
    }
}
