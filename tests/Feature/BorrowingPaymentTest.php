<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderLineItem;
use App\Models\Payment;
use App\Models\User;
use App\Services\BorrowingPaymentService;
use App\Services\DokuPaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BorrowingPaymentTest extends TestCase
{
    public function test_borrowing_can_create_payment_with_borrowing_id(): void
    {
        $borrowing = new Borrowing();
        $borrowing->id = 123;
        $borrowing->status = 'pending';
        $borrowing->borrow_date = now()->addDay();
        $borrowing->return_date = now()->addDays(3);

        $user = new User();
        $user->name = 'Borrower';
        $user->email = 'borrower@example.com';

        $item = new Item();
        $item->name = 'Borrowable Item';

        $borrowingItem = new BorrowingItem();
        $borrowingItem->quantity = 1;
        $borrowingItem->unit_price = 100000;
        $borrowingItem->subtotal = 100000;
        $borrowingItem->setRelation('item', $item);

        $borrowing->setRelation('items', collect([$borrowingItem]));
        $borrowing->setRelation('user', $user);
        $borrowing->setRelation('payment', null);

        $payment = new Payment();
        $payment->borrowing_id = 123;
        $payment->order_id = null;
        $payment->status = 'pending';
        $payment->payment_method = 'doku';
        $payment->doku_reference_number = 'BORR-123-1234567890';

        $paymentClassName = get_class(new class {
            public static ?array $created = null;

            public static function create(array $payload)
            {
                self::$created = $payload;
                $payment = new \App\Models\Payment();
                $payment->borrowing_id = $payload['borrowing_id'];
                $payment->order_id = $payload['order_id'] ?? null;
                $payment->status = $payload['status'];
                $payment->payment_method = $payload['payment_method'];
                $payment->doku_reference_number = $payload['doku_reference_number'];

                return $payment;
            }
        });

        $service = new BorrowingPaymentService(new DokuPaymentService(), $paymentClassName);

        $result = $service->createPayment($borrowing);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertSame(123, $result->borrowing_id);
        $this->assertNull($result->order_id);
        $this->assertSame('pending', $paymentClassName::$created['status']);
        $this->assertSame('doku', $paymentClassName::$created['payment_method']);
        $this->assertStringStartsWith('BORR-123-', $paymentClassName::$created['doku_reference_number']);
    }

    public function test_doku_payment_service_supports_borrowing(): void
    {
        Http::fake([
            '*' => Http::response([
                'checkout_url' => 'https://doku.example.com/checkout',
                'reference_number' => 'BORR-123-1234567890',
            ], 200),
        ]);

        $borrower = new User();
        $borrower->name = 'Borrower';
        $borrower->email = 'borrower@example.com';

        $item = new Item();
        $item->name = 'Borrowable Item';

        $borrowingItem = new BorrowingItem();
        $borrowingItem->quantity = 2;
        $borrowingItem->unit_price = 50000;
        $borrowingItem->subtotal = 100000;
        $borrowingItem->setRelation('item', $item);

        $payment = new Payment();
        $payment->doku_reference_number = 'BORR-123-1234567890';
        $payment->amount = 100000;

        $borrowing = new Borrowing();
        $borrowing->id = 123;
        $borrowing->setRelation('user', $borrower);
        $borrowing->setRelation('items', collect([$borrowingItem]));
        $borrowing->setRelation('payment', $payment);

        Auth::login($borrower);

        $dokuService = new DokuPaymentService();
        $response = $dokuService->createPaymentRequest($borrowing);

        $this->assertSame('https://doku.example.com/checkout', $response['checkout_url']);
    }

    public function test_order_payment_legacy_flow_still_works(): void
    {
        Http::fake([
            '*' => Http::response([
                'checkout_url' => 'https://doku.example.com/legacy-checkout',
                'reference_number' => 'ORD-REF-12345',
            ], 200),
        ]);

        $admin = new User();
        $admin->email = 'admin2@example.com';

        $item = new Item();
        $item->name = 'Order Item';

        $orderItem = new OrderLineItem();
        $orderItem->quantity = 1;
        $orderItem->unit_price = 150000;
        $orderItem->setRelation('item', $item);

        $payment = new Payment();
        $payment->doku_reference_number = 'ORD-REF-12345';
        $payment->amount = 150000;

        $order = new Order();
        $order->order_number = 'ORD-12345';
        $order->supplier_name = 'Supplier A';
        $order->setRelation('lineItems', collect([$orderItem]));
        $order->setRelation('payment', $payment);

        Auth::login($admin);

        $dokuService = new DokuPaymentService();
        $response = $dokuService->createPaymentRequest($order);

        $this->assertSame('https://doku.example.com/legacy-checkout', $response['checkout_url']);
    }
}
