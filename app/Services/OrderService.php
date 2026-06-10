<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderLineItem;
use App\Models\Payment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data, Collection $lineItems): Order
    {
        return DB::transaction(function () use ($data, $lineItems) {
            $totalAmount = $this->calculateTotal($lineItems);

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'supplier_name' => $data['supplier_name'],
                'recipient_name' => $data['recipient_name'],
                'notes' => $data['notes'] ?? null,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            foreach ($lineItems as $item) {
                OrderLineItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            $referenceNumber = 'ORD-' . $order->id . '-' . now()->timestamp;

            Payment::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'status' => Payment::STATUS_PENDING,
                'payment_method' => 'doku',
                'doku_reference_number' => $referenceNumber,
            ]);

            return $order;
        });
    }

    public function calculateTotal(Collection $lineItems): float
    {
        return $lineItems->sum(fn ($item) => $item['subtotal'] ?? 0);
    }

    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $order->payment->update(['status' => Payment::STATUS_CANCELLED]);
        });
    }

    protected function generateOrderNumber(): string
    {
        $date = now()->format('YmdHis');
        $random = str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);

        return 'ORD-' . $date . '-' . $random;
    }
}
