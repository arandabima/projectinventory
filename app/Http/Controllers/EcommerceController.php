<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderLineItem;
use App\Services\PaymentGatewayService;
use App\Services\DokuPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EcommerceController extends Controller
{
    public function products(): View
    {
        return view('shop.products', ['products' => Item::where('status', 'available')->where('current_stock', '>', 0)->paginate(12)]);
    }

    public function cart(Request $request): View
    {
        return view('shop.cart', ['cart' => $this->cartFor($request)->load('items.item')]);
    }

    public function addToCart(Request $request, Item $item): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        if ($item->current_stock < $data['quantity'] || $item->status->value !== 'available') {
            return back()->withErrors(['quantity' => 'Produk tidak tersedia dalam jumlah tersebut.']);
        }
        $cart = $this->cartFor($request);
        $line = $cart->items()->firstOrNew(['item_id' => $item->id]);
        $line->quantity = ($line->exists ? $line->quantity : 0) + $data['quantity'];
        $line->save();
        return redirect()->route('shop.cart')->with('status', 'Produk ditambahkan ke keranjang.');
    }

    public function updateCart(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 403);
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        $cartItem->update($data);
        return back()->with('status', 'Keranjang diperbarui.');
    }

    public function removeFromCart(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 403);
        $cartItem->delete();
        return back()->with('status', 'Produk dihapus dari keranjang.');
    }

    public function checkout(Request $request, PaymentGatewayService $gateway): RedirectResponse
    {
        $order = DB::transaction(function () use ($request, $gateway) {
            $cart = $this->cartFor($request)->load('items.item');
            abort_if($cart->items->isEmpty(), 422, 'Keranjang masih kosong.');
            $total = 0;
            foreach ($cart->items as $line) {
                $item = Item::lockForUpdate()->findOrFail($line->item_id);
                abort_if($item->current_stock < $line->quantity, 422, "Stok {$item->name} tidak mencukupi.");
                $total += $item->price * $line->quantity;
            }
            $order = Order::create(['order_number' => 'EC-'.now()->format('YmdHis').'-'.random_int(100, 999), 'buyer_id' => $request->user()->id, 'created_by' => $request->user()->id, 'supplier_name' => 'E-Commerce', 'recipient_name' => $request->user()->name, 'total_amount' => $total, 'status' => 'pending']);
            foreach ($cart->items as $line) {
                OrderLineItem::create(['order_id' => $order->id, 'item_id' => $line->item_id, 'quantity' => $line->quantity, 'unit_price' => $line->item->price, 'subtotal' => $line->item->price * $line->quantity]);
            }
            $gateway->createPayment($order);
            $cart->items()->delete();
            return $order;
        });
        return redirect()->route('shop.orders.show', $order)->with('status', 'Order dibuat. Lanjutkan pembayaran melalui DOKU Sandbox.');
    }

    public function order(Request $request, Order $order): View
    {
        abort_unless($order->buyer_id === $request->user()->id, 403);
        return view('shop.order', ['order' => $order->load('lineItems.item', 'payment')]);
    }

    public function pay(Request $request, Order $order, DokuPaymentService $doku): RedirectResponse
    {
        abort_unless($order->buyer_id === $request->user()->id, 403);
        if ($order->payment?->status !== 'pending') { return back()->withErrors(['payment' => 'Pembayaran ini sudah diproses.']); }
        try {
            $payment = $doku->createPaymentRequest($order);
            return redirect()->away($payment['checkout_url']);
        } catch (\Throwable $exception) {
            report($exception);
            return back()->withErrors(['payment' => 'DOKU Sandbox belum dapat membuat checkout. Periksa kredensial dan URL webhook.']);
        }
    }

    private function cartFor(Request $request): Cart { return Cart::firstOrCreate(['user_id' => $request->user()->id]); }
}
