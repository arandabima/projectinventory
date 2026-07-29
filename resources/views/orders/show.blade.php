@extends('layouts.admin')

@section('content')
    <div class="topbar">
        <div>
            <h1>{{ $order->order_number }}</h1>
            <div class="muted">Invoice dan detail order.</div>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a class="button ghost" href="{{ route('admin.transactions.index') }}">Daftar Order</a>
            @if ($order->status === 'pending')
                <a class="button secondary" href="{{ route('admin.orders.checkout', $order) }}">Checkout</a>
            @endif
        </div>
    </div>

    <section class="grid cols-3">
        <div class="panel stat"><span class="muted">Supplier</span><strong style="font-size:20px;">{{ $order->supplier_name }}</strong></div>
        <div class="panel stat"><span class="muted">Status order</span><strong style="font-size:20px;">{{ ucfirst($order->status) }}</strong></div>
        <div class="panel stat"><span class="muted">Total</span><strong style="font-size:20px;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></div>
    </section>

    <section class="grid cols-2" style="margin-top:16px;">
        <div class="panel">
            <h2>Invoice</h2>
            <table>
                <tbody>
                    <tr><th>Nomor</th><td>{{ $order->order_number }}</td></tr>
                    <tr><th>Tanggal</th><td>{{ $order->created_at->format('d M Y H:i') }}</td></tr>
                    <tr><th>Penerima</th><td>{{ $order->recipient_name }}</td></tr>
                    <tr><th>Dibuat Oleh</th><td>{{ $order->createdBy?->username ?? $order->createdBy?->name }}</td></tr>
                    <tr><th>Catatan</th><td>{{ $order->notes ?: '-' }}</td></tr>
                </tbody>
            </table>
        </div>

        <div class="panel">
            <h2>Pembayaran</h2>
            <table>
                <tbody>
                    <tr><th>Status</th><td>{{ ucfirst($order->payment?->status ?? '-') }}</td></tr>
                    <tr><th>Metode</th><td>{{ strtoupper($order->payment?->payment_method ?? '-') }}</td></tr>
                    <tr><th>Reference</th><td>{{ $order->payment?->doku_reference_number ?? '-' }}</td></tr>
                    <tr><th>Transaction ID</th><td>{{ $order->payment?->doku_transaction_id ?? '-' }}</td></tr>
                    <tr><th>Dibayar Pada</th><td>{{ $order->payment?->paid_at?->format('d M Y H:i') ?? '-' }}</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel" style="margin-top:16px;">
        <h2>Item</h2>
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->lineItems as $lineItem)
                    <tr>
                        <td>{{ $lineItem->item?->sku }}</td>
                        <td>{{ $lineItem->item?->name }}</td>
                        <td>{{ $lineItem->quantity }}</td>
                        <td>Rp {{ number_format($lineItem->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($lineItem->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
@endsection
