<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DokuPaymentService
{
    protected string $mallId;
    protected string $sharedKey;
    protected string $sandboxUrl;

    public function __construct()
    {
        $config = config('services.doku');
        $this->mallId = $config['mall_id'];
        $this->sharedKey = $config['shared_key'];
        $this->sandboxUrl = $config['sandbox_url'];
    }

    public function createPaymentRequest(Order $order): array
    {
        $payment = $order->payment;

        $requestBody = json_encode([
            'mall_id' => $this->mallId,
            'reference_number' => $payment->doku_reference_number,
            'amount' => (int) ($payment->amount * 100),
            'currency' => 'IDR',
            'invoice_number' => $order->order_number,
            'customer' => [
                'name' => $order->supplier_name,
                'email' => auth()->user()->email,
            ],
            'line_items' => $order->lineItems->map(fn ($item) => [
                'name' => $item->item->name,
                'quantity' => $item->quantity,
                'price' => (int) ($item->unit_price * 100),
            ])->toArray(),
            'callback_url' => config('services.doku.notification_url'),
        ]);

        $signature = hash_hmac('sha256', $requestBody, $this->sharedKey);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Signature' => 'sha256=' . $signature,
        ])->post($this->sandboxUrl . '/checkout/payment/create', json_decode($requestBody, true));

        if (! $response->successful()) {
            throw new \Exception('DOKU API Error: ' . $response->body());
        }

        $data = $response->json();

        return [
            'checkout_url' => $data['checkout_url'] ?? null,
            'reference_number' => $data['reference_number'] ?? null,
        ];
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        $body = $request->getContent();
        $signature = $request->header('X-Signature');

        if (! $signature) {
            return false;
        }

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $body, $this->sharedKey);

        return hash_equals($signature, $expectedSignature);
    }

    public function processPaymentCallback(Request $request): Payment
    {
        $data = $request->json()->all();

        $referenceNumber = $data['reference_number'] ?? null;
        $transactionId = $data['transaction_id'] ?? null;
        $resultCode = $data['result']['code'] ?? null;

        $payment = Payment::where('doku_reference_number', $referenceNumber)->firstOrFail();

        $status = match ($resultCode) {
            '0000' => Payment::STATUS_CONFIRMED,
            '0001' => Payment::STATUS_FAILED,
            default => Payment::STATUS_PENDING,
        };

        $payment->update([
            'status' => $status,
            'doku_transaction_id' => $transactionId,
            'doku_response_json' => $data,
            'paid_at' => $status === Payment::STATUS_CONFIRMED ? now() : null,
        ]);

        if ($status === Payment::STATUS_CONFIRMED) {
            $payment->order->update(['status' => 'completed']);
        }

        return $payment;
    }
}
