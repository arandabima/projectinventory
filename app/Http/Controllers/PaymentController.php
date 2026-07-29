<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\DokuPaymentService;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(protected DokuPaymentService $dokuService) {}

    public function index(): View
    {
        $payments = Payment::with('order', 'borrowing')
            ->latest()
            ->paginate(15);

        return view('payments.index', compact('payments'));
    }

    public function initiatePayment(Order $order): RedirectResponse
    {
        if ($order->payment->status !== 'pending') {
            return back()->withErrors(['payment' => 'Pembayaran ini sudah diproses.']);
        }

        try {
            $paymentData = $this->dokuService->createPaymentRequest($order);

            if (! isset($paymentData['checkout_url'])) {
                return back()->withErrors(['payment' => 'Gagal membuat payment request ke DOKU.']);
            }

            return redirect()->away($paymentData['checkout_url']);
        } catch (\Exception $e) {
            return back()->withErrors(['payment' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function handleDokuCallback(Request $request, InventoryService $inventory): Response
    {
        if (! $this->dokuService->verifyWebhookSignature($request)) {
            return response('Signature Invalid', 401);
        }

        try {
            $reference = $request->json('reference_number');
            $existing = Payment::where('doku_reference_number', $reference)->first();
            $wasConfirmed = $existing?->status === Payment::STATUS_CONFIRMED;
            $payment = $this->dokuService->processPaymentCallback($request);
            if ($payment->status === Payment::STATUS_CONFIRMED && ! $wasConfirmed && $payment->order_id) {
                DB::transaction(function () use ($payment, $inventory) {
                    $order = Order::with('lineItems.item')->lockForUpdate()->findOrFail($payment->order_id);
                    foreach ($order->lineItems as $line) { $inventory->decreaseStock($line->item, $line->quantity); }
                    $order->update(['status' => 'paid']);
                });
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            return response('Error: ' . $e->getMessage(), 400);
        }
    }
}
