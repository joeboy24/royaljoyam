<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutSaleRequest;
use App\Http\Requests\PayDebtRequest;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartQuantityRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Cart;
use App\Models\Sale;
use App\Models\SalesHistory;
use App\Models\SalesPayment;
use App\Services\SalesService;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function __construct(private SalesService $salesService)
    {
        $this->middleware(['auth', 'load_auth']);
    }

    public function addToCart(StoreCartItemRequest $request)
    {
        return $this->salesService->addToCart($request->validated());
    }

    public function checkout(CheckoutSaleRequest $request)
    {
        return $this->salesService->checkout($request->validated());
    }

    public function payDebt(PayDebtRequest $request)
    {
        return $this->salesService->payDebt($request->validated());
    }

    public function updateSale(UpdateSaleRequest $request, Sale $sale)
    {
        return $this->salesService->updateSale($sale, $request->validated());
    }

    public function updateCartQuantity(UpdateCartQuantityRequest $request, Cart $cart)
    {
        if ((string) $cart->user_id !== (string) auth()->user()->id && auth()->user()->status !== 'Administrator') {
            abort(404);
        }

        return $this->salesService->updateCartQuantity($cart, $request->validated());
    }

    public function removeCartItem(Cart $cart)
    {
        if ((string) $cart->user_id !== (string) auth()->user()->id && auth()->user()->status !== 'Administrator') {
            abort(404);
        }

        return $this->salesService->removeCartItem($cart);
    }

    public function deliverLineItem(Request $request, SalesHistory $salesHistory)
    {
        $request->validate([
            'send_sale_id' => 'required|integer|exists:sales,id',
        ]);

        return $this->salesService->deliverLineItem($salesHistory, (int) $request->input('send_sale_id'));
    }

    public function undeliverLineItem(Request $request, SalesHistory $salesHistory)
    {
        $request->validate([
            'send_sale_id' => 'required|integer|exists:sales,id',
        ]);

        return $this->salesService->undeliverLineItem($salesHistory, (int) $request->input('send_sale_id'));
    }

    public function deletePaidDebtPayment(SalesPayment $salesPayment)
    {
        return $this->salesService->deletePaidDebtPayment($salesPayment);
    }
}
