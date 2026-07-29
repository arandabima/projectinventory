<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DokuPaymentService
{
    private const CHECKOUT_PATH = '/checkout/v1/payment';

    protected string $clientId;
    protected string $secretKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->clientId = (string) config('services.doku.mall_id', '');
        $this->secretKey = (string) config('services.doku.shared_key', '');
        $this->apiUrl = rtrim((string) config('services.doku.api_url', 'https://api-sandbox.doku.com'), '/');
    }

    public function createPaymentRequest(Order|Borrowing $payable): array
    {
        $payment = $payable->payment;

        if (! $payment) {
            throw new \RuntimeException('Payment record tidak ditemukan.');
        }

        if ($this->clientId === '' || $this->secretKey === '') {
            throw new \RuntimeException('Kredensial DOKU Sandbox belum dikonfigurasi.');
        }

        $body = [
            'order' => [
                'amount' => (int) round((float) $payment->amount),
                'invoice_number' => $this->resolveInvoiceNumber($payable),
                'currency' => 'IDR',
                'callback_url' => $this->returnUrl($payable),
                'auto_redirect' => false,
                'line_items' => $this->resolveLineItems($payable),
            ],
            'payment' => ['payment_due_date' => 60],
            'customer' => [
                'id' => (string) ($payable->user?->id ?? $this->authenticatedUser()?->id ?? 'guest'),
                'name' => $this->resolveCustomerName($payable),
                'email' => $this->resolveCustomerEmail($payable),
            ],
        ];

        if ($notificationUrl = $this->publicNotificationUrl()) {
            $body['additional_info'] = ['override_notification_url' => $notificationUrl];
        }

        $requestBody = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $requestId = (string) Str::uuid();
        $timestamp = now('UTC')->format('Y-m-d\\TH:i:s\\Z');
        $signature = $this->signature($requestBody, $requestId, $timestamp, self::CHECKOUT_PATH);

        $response = Http::acceptJson()
            ->withHeaders([
                'Client-Id' => $this->clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => $signature,
            ])
            ->withBody($requestBody, 'application/json')
            ->post($this->apiUrl.self::CHECKOUT_PATH);

        if (! $response->successful()) {
            throw new \RuntimeException('DOKU API Error: '.$response->body());
        }

        $data = $response->json();
        $checkoutUrl = data_get($data, 'response.payment.url');

        if (! is_string($checkoutUrl) || $checkoutUrl === '') {
            throw new \RuntimeException('DOKU tidak mengembalikan URL checkout.');
        }

        $payment->update([
            'gateway_reference' => data_get($data, 'response.payment.token_id'),
            'doku_response_json' => $data,
        ]);

        return ['checkout_url' => $checkoutUrl, 'reference_number' => $payment->doku_reference_number];
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('Signature');
        $clientId = $request->header('Client-Id');
        $requestId = $request->header('Request-Id');
        $timestamp = $request->header('Request-Timestamp');

        if (! $signature || ! $clientId || ! $requestId || ! $timestamp) {
            return false;
        }

        $expected = $this->signature($request->getContent(), $requestId, $timestamp, $request->getPathInfo(), $clientId);

        return hash_equals($expected, $signature);
    }

    public function processPaymentCallback(Request $request): Payment
    {
        $data = $request->json()->all();
        $invoiceNumber = data_get($data, 'order.invoice_number') ?? $data['reference_number'] ?? null;
        $transactionStatus = data_get($data, 'transaction.status') ?? data_get($data, 'result.code');

        $payment = Payment::query()
            ->where('doku_reference_number', $invoiceNumber)
            ->orWhereHas('order', fn ($query) => $query->where('order_number', $invoiceNumber))
            ->firstOrFail();

        $status = match ($transactionStatus) {
            'SUCCESS', '0000' => Payment::STATUS_CONFIRMED,
            'FAILED', 'EXPIRED', 'CANCELLED', '0001' => Payment::STATUS_FAILED,
            default => Payment::STATUS_PENDING,
        };

        $payment->update([
            'status' => $status,
            'doku_transaction_id' => data_get($data, 'transaction.transaction_id') ?? $data['transaction_id'],
            'doku_response_json' => $data,
            'paid_at' => $status === Payment::STATUS_CONFIRMED ? now() : null,
        ]);

        return $payment;
    }

    private function signature(string $body, string $requestId, string $timestamp, string $target, ?string $clientId = null): string
    {
        $digest = base64_encode(hash('sha256', $body, true));
        $component = 'Client-Id:'.($clientId ?? $this->clientId)."\n"
            .'Request-Id:'.$requestId."\n"
            .'Request-Timestamp:'.$timestamp."\n"
            .'Request-Target:'.$target."\n"
            .'Digest:'.$digest;

        return 'HMACSHA256='.base64_encode(hash_hmac('sha256', $component, $this->secretKey, true));
    }

    private function resolveInvoiceNumber(Order|Borrowing $payable): string
    {
        return $payable instanceof Order ? $payable->order_number : 'BORR-'.$payable->id;
    }

    private function publicNotificationUrl(): ?string
    {
        $url = (string) config('services.doku.notification_url', '');
        $host = parse_url($url, PHP_URL_HOST);

        if ($url === '' || in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return null;
        }

        return $url;
    }

    private function returnUrl(Order|Borrowing $payable): string
    {
        return $payable instanceof Order
            ? route('shop.orders.show', $payable)
            : route('user.borrowings.show', $payable);
    }

    private function resolveCustomerName(Order|Borrowing $payable): string
    {
        return $payable instanceof Order
            ? ($payable->buyer?->name ?? $payable->recipient_name)
            : ($payable->user?->name ?? 'Borrower');
    }

    private function resolveCustomerEmail(Order|Borrowing $payable): ?string
    {
        return $payable instanceof Order
            ? ($payable->buyer?->email ?? $this->authenticatedUser()?->email)
            : ($payable->user?->email ?? $this->authenticatedUser()?->email);
    }

    private function authenticatedUser(): ?User
    {
        return auth()->user();
    }

    private function resolveLineItems(Order|Borrowing $payable): array
    {
        $items = $payable instanceof Order ? $payable->lineItems : $payable->items;

        return $items->map(fn ($line) => [
            'id' => (string) $line->item_id,
            'name' => $line->item?->name ?? 'Item',
            'quantity' => (int) $line->quantity,
            'price' => (int) round((float) $line->unit_price),
            'sku' => $line->item?->sku,
        ])->values()->all();
    }
}
