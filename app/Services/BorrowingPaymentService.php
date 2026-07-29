<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Payment;

class BorrowingPaymentService
{
    public function __construct(protected DokuPaymentService $dokuService, protected string $paymentModel = Payment::class)
    {
    }

    public function createPayment(Borrowing $borrowing): Payment
    {
        $borrowing->loadMissing('items', 'user', 'payment');

        if ($borrowing->payment) {
            return $borrowing->payment;
        }

        $amount = $borrowing->items->sum('subtotal');

        return ($this->paymentModel)::create([
            'borrowing_id' => $borrowing->id,
            'amount' => $amount,
            'status' => Payment::STATUS_PENDING,
            'payment_method' => 'doku',
            'doku_reference_number' => $this->generateReferenceNumber($borrowing),
        ]);
    }

    public function initiatePayment(Borrowing $borrowing): array
    {
        $payment = $this->createPayment($borrowing);

        return $this->dokuService->createPaymentRequest($borrowing);
    }

    private function generateReferenceNumber(Borrowing $borrowing): string
    {
        return sprintf('BORR-%s-%s', $borrowing->id, now()->timestamp);
    }
}
