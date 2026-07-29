@extends('layouts.admin')

@section('content')
    <div class="topbar">
        <div>
            <h1>Checkout</h1>
            <div class="muted">{{ $order->order_number }} siap diproses ke pembayaran.</div>
        </div>
        <a class="button ghost" href="{{ route('admin.orders.show', $order) }}">Lihat Invoice</a>
    </div>

    <section class="grid cols-2">
        <div class="panel">
            <h2>Ringkasan</h2>
            <table>
                <tbody>
                    <tr><th>Supplier</th><td>{{ $order->supplier_name }}</td></tr>
                    <tr><th>Penerima</th><td>{{ $order->recipient_name }}</td></tr>
                    <tr><th>Total</th><td><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td></tr>
                    <tr><th>Status Order</th><td>{{ ucfirst($order->status) }}</td></tr>
                    <tr><th>Status Bayar</th><td>{{ ucfirst($order->payment?->status ?? '-') }}</td></tr>
                </tbody>
            </table>
        </div>

        <div class="panel">
            <h2>Pembayaran DOKU</h2>
            <p class="muted">Klik bayar untuk membuat sesi checkout DOKU. Callback pembayaran akan memperbarui status invoice otomatis.</p>
            <form method="post" action="{{ route('admin.payments.initiate', $order) }}" style="margin-bottom:12px;">
                @csrf
                <button type="submit" @disabled($order->payment?->status !== 'pending')>Bayar Sekarang</button>
            </form>
            @if ($order->status === 'pending')
                <form method="post" action="{{ route('admin.orders.cancel', $order) }}" onsubmit="return confirm('Batalkan order ini?');">
                    @csrf
                    <button type="submit" style="background: var(--danger);">Batalkan Order</button>
                </form>
            @endif
        </div>
    </section>

    <section class="panel" style="margin-top:16px;">
        <h2>Item Invoice</h2>
        <table>
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->lineItems as $lineItem)
                    <tr>
                        <td>{{ $lineItem->item?->sku }} - {{ $lineItem->item?->name }}</td>
                        <td>{{ $lineItem->quantity }}</td>
                        <td>Rp {{ number_format($lineItem->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($lineItem->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
@endsection
