@extends('layouts.app')

@section('content')
    <div class="topbar">
        <div>
            <h1>Pembayaran</h1>
            <div class="muted">Pantau status pembayaran order dan lanjutkan checkout DOKU untuk invoice pending.</div>
        </div>
        <a class="button" href="{{ route('orders.create') }}">Buat Order</a>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Order</th>
                    <th>Reference</th>
                    <th>Metode</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Dibayar Pada</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                        <td>
                            @if ($payment->order)
                                <strong>{{ $payment->order->order_number }}</strong>
                                <div class="muted">{{ $payment->order->supplier_name }}</div>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $payment->doku_reference_number ?? '-' }}</td>
                        <td>{{ strtoupper($payment->payment_method) }}</td>
                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td>
                            <span @class([
                                'badge',
                                'ok' => $payment->status === 'confirmed',
                                'warn' => $payment->status === 'pending',
                                'danger' => in_array($payment->status, ['failed', 'cancelled']),
                            ])>
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td>{{ $payment->paid_at?->format('d M Y H:i') ?? '-' }}</td>
                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                @if ($payment->order)
                                    <a class="button ghost" href="{{ route('orders.show', $payment->order) }}">Invoice</a>
                                    @if ($payment->status === 'pending')
                                        <a class="button secondary" href="{{ route('orders.checkout', $payment->order) }}">Checkout</a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="muted">Belum ada data pembayaran. Buat order dulu untuk generate invoice pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $payments->links() }}</div>
    </section>
@endsection
