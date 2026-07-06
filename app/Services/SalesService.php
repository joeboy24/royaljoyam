<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SalesHistory;
use App\Models\SalesPayment;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class SalesService
{
    public function addToCart(array $data): RedirectResponse
    {
        if ($this->isAdministrator()) {
            return Redirect::to('/sales')->with('error', 'Oops...! Administrator cannot make purchase');
        }

        try {
            $item = Item::findOrFail($data['item_id']);
            $branchColumn = 'q' . auth()->user()->bv;
            $availableQty = (int) $item->{$branchColumn};
            $qty = (int) $data['qty'];
            $unitPrice = $data['price'];

            if ($qty > $availableQty) {
                return Redirect::to('/sales')->with('error', 'Sorry..! Available Stock Quantity: ' . $availableQty);
            }

            if ((float) $unitPrice == 0) {
                return Redirect::to('/sales')->with('error', 'Oops..! Define price for this item before purchase');
            }

            $existing = Cart::where([
                'user_id' => auth()->user()->id,
                'name' => $data['name'],
            ])->exists();

            if ($existing) {
                return Redirect::to('/sales')->with('error', 'Oops..! Item already added.. Edit from table ');
            }

            $costPrice = $item->cost_price;

            Cart::create([
                'user_id' => auth()->user()->id,
                'item_id' => $data['item_id'],
                'item_no' => $data['item_no'],
                'name' => $data['name'],
                'qty' => $qty,
                'profits' => ($unitPrice - $costPrice) * $qty,
                'cost_price' => $costPrice,
                'unit_price' => $unitPrice,
                'tot' => $qty * $unitPrice,
            ]);

            return Redirect::to('/sales');
        } catch (\Throwable $th) {
            return Redirect::to('/sales')->with('error', 'Oops..! Something Happened ');
        }
    }

    public function checkout(array $data): RedirectResponse
    {
        if ($this->isAdministrator()) {
            return Redirect::to('/sales')->with('error', 'Oops...! Administrator cannot make purchase');
        }

        $orderNo = 'M' . substr(str_shuffle(str_repeat('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', 4)), 0, 4) . date('is');

        try {
            $payment = (float) $data['payment'];
            $payMode = $data['pay_mode'];
            $delStatus = $data['del_status'];
            $paidStatus = $payMode === 'Post Payment(Debt)' ? 'No' : 'Paid';
            $notes = filled($data['notes'] ?? null) ? trim((string) $data['notes']) : null;

            $carts = Cart::where('user_id', auth()->user()->id)->get();

            if ($carts->isEmpty()) {
                return Redirect::to('/sales')->with('error', 'Oops..! Cart is empty');
            }

            $qty = $carts->sum('qty');
            $tot = (float) $carts->sum('tot');
            $discount = (float) ($data['discount'] ?? 0);

            if ($discount > $tot) {
                return Redirect::back()->with('error', 'Oops..! Discount cannot be greater than total amount');
            }

            $tot -= $discount;

            if ($payment < $tot && $payMode !== 'Post Payment(Debt)') {
                return Redirect::back()->with('error', 'Oops..! Amount paid cannot be less than total cost. Otherwise select the `Post Payment(Debt)` option');
            }

            $change = $payment == 0 ? 0 : $payment - $tot;

            $sale = Sale::create([
                'user_id' => auth()->user()->id,
                'user_bv' => auth()->user()->bv,
                'order_no' => $orderNo,
                'qty' => $qty,
                'tot' => $tot,
                'pay_mode' => $payMode,
                'buy_name' => $data['buy_name'],
                'buy_contact' => $data['buy_contact'],
                'del_status' => $delStatus,
                'discount' => $discount,
                'payment' => $payment,
                'change' => $change,
                'paid' => $paidStatus,
                'notes' => $notes,
            ]);

            if ($payMode === 'Post Payment(Debt)' && $payment > 0) {
                $paymentApplied = min($payment, $tot);

                SalesPayment::create([
                    'user_id' => auth()->user()->id,
                    'sale_id' => $sale->id,
                    'amt_paid' => $paymentApplied,
                    'bal' => $tot - $paymentApplied,
                ]);

                $sale->paid = $paymentApplied == $tot ? 'Paid' : 'No';
                $sale->paid_debt = $paymentApplied;
                $sale->save();
            }

            foreach ($carts as $cart) {
                SalesHistory::create([
                    'user_id' => $cart->user_id,
                    'sale_id' => $sale->id,
                    'item_id' => $cart->item_id,
                    'user_bv' => auth()->user()->bv,
                    'item_no' => $cart->item_no,
                    'name' => $cart->name,
                    'qty' => $cart->qty,
                    'cost_price' => $cart->cost_price,
                    'unit_price' => $cart->unit_price,
                    'profits' => $cart->profits,
                    'tot' => $cart->tot,
                    'del_status' => $delStatus,
                ]);

                $cart->delete();
            }

            return Redirect::to('/sales')->with('success', 'Purchase Complete..!');
        } catch (\Throwable $th) {
            return Redirect::to('/sales')->with('error', 'Oops..! Unhandled Error... ' . $th->getMessage());
        }
    }

    public function payDebt(array $data): RedirectResponse
    {
        $saleId = $data['send_id'];
        $saleTotal = (float) $data['send_tot'];
        $amountPaid = (float) $data['amt_paid'];

        if ($amountPaid > $saleTotal) {
            return Redirect::to('/sales')->with('error', 'Oops..! Amount paying cannot be greater than amount owing.');
        }

        $sumDebts = (float) SalesPayment::where('del', 'no')->where('sale_id', $saleId)->sum('amt_paid');
        $sumDebts = $sumDebts == 0 ? $amountPaid : $sumDebts + $amountPaid;
        $balance = max($saleTotal - $sumDebts, 0);

        SalesPayment::create([
            'user_id' => auth()->user()->id,
            'sale_id' => $saleId,
            'amt_paid' => $amountPaid,
            'bal' => $balance,
        ]);

        $sale = Sale::findOrFail($saleId);
        $sale->paid_debt = $sumDebts;

        if ($saleTotal == $sumDebts) {
            $sale->paid = 'Paid';
        }

        $sale->save();

        return Redirect::back()->with('success', 'Payment of Gh₵ ' . $amountPaid . ' successfull made.');
    }

    public function updateSale(Sale $sale, array $data): RedirectResponse
    {
        $payMode = $data['pay_mode'];

        if ($sale->pay_mode === 'Post Payment(Debt)' && $sale->paid !== 'Paid' && $payMode !== 'Post Payment(Debt)') {
            return Redirect::to('/sales')->with('error', 'Oops..! Pay debt in full before changing pay mode to ' . $payMode);
        }

        try {
            $sale->pay_mode = $payMode;
            $sale->buy_name = $data['buy_name'];
            $sale->buy_contact = $data['buy_contact'];

            if (array_key_exists('notes', $data)) {
                $sale->notes = filled($data['notes'] ?? null) ? trim((string) $data['notes']) : null;
            }

            $sale->save();

            return Redirect::back()->with('success', 'Update Successful');
        } catch (Exception $ex) {
            return Redirect::back()->with('error', 'Oops..! Error updating record.');
        }
    }

    public function updateCartQuantity(Cart $cart, array $data): RedirectResponse
    {
        $cartQty = (int) $cart->qty;
        $change = (int) $data['change'];
        $price = (float) $data['price'];
        $branchColumn = 'q' . auth()->user()->bv;
        $item = Item::findOrFail($cart->item_id);
        $mainQty = (int) $item->qty;
        $availableQty = (int) $item->{$branchColumn};

        if (($change - $cartQty) > $availableQty) {
            return Redirect::to('/sales')->with('error', 'Sorry..! Available Stock Quantity: ' . $availableQty);
        }

        if ($change > $cartQty) {
            $diff = $change - $cartQty;
            $availableQty -= $diff;
            $mainQty -= $diff;
        } elseif ($change < $cartQty) {
            $diff = $cartQty - $change;
            $availableQty += $diff;
            $mainQty += $diff;
        }

        try {
            $cart->qty = $change;
            $cart->profits = $change * ($cart->unit_price - $cart->cost_price);
            $cart->tot = $price * $change;
            $cart->save();

            $item->qty = $mainQty;
            $item->{$branchColumn} = $availableQty;
            $item->save();

            return Redirect::to('/sales')->with('success', ' quantity updated..');
        } catch (Exception $ex) {
            return Redirect::to('/sales')->with('error', 'Oops..! Unhandled Error!');
        }
    }

    public function removeCartItem(Cart $cart): RedirectResponse
    {
        if ((string) $cart->user_id !== (string) auth()->user()->id && auth()->user()->status !== 'Administrator') {
            return Redirect::back()->with('error', 'Cart item not found');
        }

        try {
            $item = Item::find($cart->item_id);

            if ($item) {
                $item->restoreCartStockReservation(auth()->user()->bv, (int) $cart->qty);
            }

            $cartName = $cart->name;
            $cart->delete();

            return Redirect::back()->with('success', $cartName . ' deleted successfully');
        } catch (Exception $ex) {
            return Redirect::back()->with('error', 'Oops..! Unhandled Error!');
        }
    }

    public function deliverLineItem(SalesHistory $salesHistory, int $saleId): RedirectResponse
    {
        try {
            $salesHistory->del_status = 'Delivered';
            $salesHistory->save();

            $pendingCount = SalesHistory::where([
                'sale_id' => $saleId,
                'del_status' => 'Not Delivered',
            ])->count();

            if ($pendingCount === 0) {
                $sale = Sale::findOrFail($saleId);
                $sale->del_status = 'Delivered';
                $sale->save();
            }

            return Redirect::to('/sales')->with('success', 'Item delivered');
        } catch (Exception $ex) {
            return Redirect::to('/sales')->with('error', 'Oops..! Unhandled Error! ');
        }
    }

    public function undeliverLineItem(SalesHistory $salesHistory, int $saleId): RedirectResponse
    {
        $pendingCount = SalesHistory::where([
            'sale_id' => $saleId,
            'del_status' => 'Not Delivered',
        ])->count();

        $salesHistory->del_status = 'Not Delivered';
        $salesHistory->save();

        if ($pendingCount === 0) {
            $sale = Sale::findOrFail($saleId);
            $sale->del_status = 'Not Delivered';
            $sale->save();
        }

        return Redirect::to('/sales')->with('success', 'Item undelivered');
    }

    public function deletePaidDebtPayment(SalesPayment $salesPayment): RedirectResponse
    {
        $order = Sale::findOrFail($salesPayment->sale_id);
        $order->paid_debt = $order->paid_debt - $salesPayment->amt_paid;
        $order->paid = 'No';
        $order->save();

        $salesPayment->del = 'yes';
        $salesPayment->save();

        return Redirect::back()->with('success', 'Record deletion successfull');
    }

    protected function isAdministrator(): bool
    {
        return auth()->user()->status === 'Administrator';
    }
}
