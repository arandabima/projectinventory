<?php
namespace App\Services;
use App\Models\Order;
use App\Models\Payment;
class PaymentGatewayService { public function createPayment(Order $order): Payment { return $order->payment()->updateOrCreate([], ['amount' => $order->total_amount, 'status' => Payment::STATUS_PENDING, 'payment_method' => 'doku_sandbox', 'doku_reference_number' => 'PAY-'.$order->order_number]); } }
