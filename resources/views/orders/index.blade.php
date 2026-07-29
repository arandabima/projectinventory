@extends('layouts.admin')

@section('content')
    <div class="topbar">
        <div>
            <h1>Order</h1>
            <div class="muted">Kelola order internal/supplier, invoice, dan status pembayaran.</div>
        </div>
        <a class="button" href="{{ route('admin.orders.create') }}">Buat Order</a>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Supplier</th>
                    <th>Penerima</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->order_number }}</strong>
                            <div class="muted">{{ $order->created_at->format('d M Y H:i') }}</div>
                        </td>
                        <td>{{ $order->supplier_name }}</td>
                        <td>{{ $order->recipient_name }}</td>
                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td>
                            <span @class(['badge', 'ok' => $order->status === 'completed', 'warn' => $order->status === 'pending', 'danger' => $order->status === 'cancelled'])>
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <span @class(['badge', 'ok' => $order->payment?->status === 'confirmed', 'warn' => $order->payment?->status === 'pending', 'danger' => in_array($order->payment?->status, ['failed', 'cancelled'])])>
                                {{ ucfirst($order->payment?->status ?? '-') }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <a class="button ghost" href="{{ route('admin.orders.show', $order) }}">Detail</a>
                                @if ($order->status === 'pending')
                                    <a class="button secondary" href="{{ route('admin.orders.checkout', $order) }}">Checkout</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="muted">Belum ada order.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $orders->links() }}</div>
    </section>
@endsection
