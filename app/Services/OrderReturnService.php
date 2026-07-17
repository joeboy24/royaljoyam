<?php

namespace App\Services;

use App\Models\Item;
use App\Models\OrderReturn;
use App\Models\Sale;
use App\Models\SalesHistory;
use App\Models\SalesPayment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderReturnService
{
    /**
     * Reverse a sale: archive line items, restore stock, void debt payments, hide the order.
     */
    public function returnSale(int $saleId): Sale
    {
        return DB::transaction(function () use ($saleId) {
            $sale = Sale::query()->where('del', 'no')->find($saleId);

            if (! $sale) {
                throw new RuntimeException('Sale not found or already returned.');
            }

            $lines = SalesHistory::query()
                ->where('sale_id', $saleId)
                ->where('del', 'no')
                ->get();

            foreach ($lines as $line) {
                OrderReturn::firstOrCreate(
                    [
                        'sale_id' => (string) $line->sale_id,
                        'item_id' => (string) $line->item_id,
                    ],
                    [
                        'user_id' => (string) $line->user_id,
                        'user_bv' => (string) $line->user_bv,
                        'item_no' => (string) $line->item_no,
                        'name' => (string) $line->name,
                        'qty' => (string) $line->qty,
                        'cost_price' => (string) $line->cost_price,
                        'unit_price' => (string) $line->unit_price,
                        'profits' => (string) $line->profits,
                        'tot' => (string) $line->tot,
                        'del_status' => (string) $line->del_status,
                        'order_date' => $line->created_at,
                    ]
                );

                $item = Item::find($line->item_id);

                if ($item) {
                    $item->restoreCartStockReservation($line->user_bv, (int) $line->qty);
                }

                $line->del = 'yes';
                $line->save();
            }

            SalesPayment::query()
                ->where('sale_id', $saleId)
                ->where('del', 'no')
                ->each(function (SalesPayment $payment) {
                    $payment->del = 'yes';
                    $payment->save();
                });

            $sale->del = 'yes';
            $sale->save();

            return $sale->fresh();
        });
    }
}
