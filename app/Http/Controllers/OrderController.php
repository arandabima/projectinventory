<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(): View
    {
        $orders = Order::with('payment', 'createdBy')
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function create(): View
    {
        $items = Item::orderBy('name')->get();

        return view('orders.create', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateOrderData($request);
        $lineItems = $this->parseLineItems($request);

        if ($lineItems->isEmpty()) {
            return back()->withInput()->withErrors(['items' => 'Tambahkan minimal 1 item.']);
        }

        $order = $this->orderService->createOrder($data, $lineItems);

        return redirect()->route('orders.checkout', $order)->with('status', 'Order dibuat. Lanjutkan ke pembayaran.');
    }

    public function show(Order $order): View
    {
        $order->load('lineItems.item', 'payment', 'createdBy');

        return view('orders.show', compact('order'));
    }

    public function checkout(Order $order): View
    {
        $order->load('lineItems.item', 'payment');

        return view('orders.checkout', compact('order'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return back()->withErrors(['status' => 'Hanya order pending yang bisa dibatalkan.']);
        }

        $this->orderService->cancelOrder($order);

        return redirect()->route('orders.show', $order)->with('status', 'Order dibatalkan.');
    }

    private function validateOrderData(Request $request): array
    {
        return $request->validate([
            'supplier_name' => 'required|string|max:120',
            'recipient_name' => 'required|string|max:120',
            'notes' => 'nullable|string|max:1000',
        ]);
    }

    private function parseLineItems(Request $request)
    {
        $itemIds = $request->input('item_ids', []);
        $quantities = $request->input('quantities', []);
        $unitPrices = $request->input('unit_prices', []);

        $lineItems = collect();

        foreach ($itemIds as $index => $itemId) {
            if (empty($itemId) || empty($quantities[$index])) {
                continue;
            }

            $item = Item::findOrFail($itemId);
            $quantity = max(0, (int) $quantities[$index]);
            $unitPrice = max(0, (float) ($unitPrices[$index] ?? 0));

            if ($quantity <= 0 || $unitPrice <= 0) {
                continue;
            }

            $lineItems->push([
                'item_id' => $itemId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $quantity * $unitPrice,
            ]);
        }

        return $lineItems;
    }
}
