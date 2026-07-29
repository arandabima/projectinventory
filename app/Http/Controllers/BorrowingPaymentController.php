<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Services\BorrowingPaymentService;

class BorrowingPaymentController extends Controller
{
    public function __construct(
        protected BorrowingPaymentService $paymentService
    ) {
    }


    public function initiate(Borrowing $borrowing)
    {
        $this->authorize('view', $borrowing);

        $result = $this->paymentService
            ->initiatePayment($borrowing);


        if (!empty($result['checkout_url'])) {
            return redirect($result['checkout_url']);
        }


        return back()->with(
            'error',
            'Pembayaran gagal dibuat'
        );
    }
}