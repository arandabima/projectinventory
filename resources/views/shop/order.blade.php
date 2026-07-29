@extends('layouts.user')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="topbar">
        <div>
            <h1>Pesanan {{ $order->order_number }}</h1>
            <div class="muted">Tinjau pesanan dan lanjutkan pembayaran dengan aman melalui DOKU.</div>
        </div>
        <a class="btn ghost" href="{{ route('shop.products') }}">Lanjut Belanja</a>
    </div>

    <section class="grid cols-3">
        <div class="panel"><div class="muted">Status pesanan</div><h2 style="margin:8px 0 0;">{{ ucfirst($order->status) }}</h2></div>
        <div class="panel"><div class="muted">Total pembayaran</div><h2 style="margin:8px 0 0;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h2></div>
        <div class="panel"><div class="muted">Status pembayaran</div><h2 style="margin:8px 0 0;">{{ ucfirst($order->payment?->status ?? 'pending') }}</h2></div>
    </section>

    <section class="grid cols-2" style="margin-top:16px;">
        <div class="panel">
            <h2>Ringkasan Barang</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Barang</th><th>Jumlah</th><th>Subtotal</th></tr></thead>
                    <tbody>
                    @foreach ($order->lineItems as $line)
                        <tr><td>{{ $line->item?->name ?? 'Barang' }}</td><td>{{ $line->quantity }}</td><td>Rp {{ number_format($line->subtotal, 0, ',', '.') }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <span class="badge">DOKU SANDBOX</span>
            <h2 style="margin-top:12px;">Pembayaran</h2>
            <p class="muted">Referensi: <code>{{ $order->payment?->doku_reference_number }}</code></p>
            @if ($order->payment?->status === 'pending')
                <form method="post" action="{{ route('shop.orders.pay', $order) }}">
                    @csrf
                    <button class="btn primary" type="submit">Bayar dengan DOKU Sandbox</button>
                </form>
            @else
                <p class="muted">Pembayaran ini sudah tidak memerlukan tindakan lanjutan.</p>
            @endif
        </div>
    </section>
@endsection
