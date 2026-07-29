@extends('layouts.user')

@section('title', 'Keranjang')

@section('content')
    <div class="topbar">
        <div><h1>Keranjang Belanja</h1><div class="muted">Atur jumlah produk sebelum membuat pesanan.</div></div>
        <a class="btn ghost" href="{{ route('shop.products') }}">Lihat Produk</a>
    </div>

    @php($total = 0)
    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th></th></tr></thead>
                <tbody>
                @forelse($cart->items as $line)
                    @php($subtotal = $line->item->price * $line->quantity)
                    @php($total += $subtotal)
                    <tr>
                        <td><strong>{{ $line->item->name }}</strong><div class="muted">{{ $line->item->sku }}</div></td>
                        <td>Rp {{ number_format($line->item->price, 0, ',', '.') }}</td>
                        <td><form method="post" action="{{ route('shop.cart.update', $line) }}" style="display:flex; gap:8px; align-items:center;">@csrf @method('PATCH')<input style="max-width:86px;" type="number" name="quantity" min="1" value="{{ $line->quantity }}"><button class="btn ghost" type="submit">Update</button></form></td>
                        <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        <td><form method="post" action="{{ route('shop.cart.remove', $line) }}">@csrf @method('DELETE')<button class="btn ghost" style="color:#b91c1c;" type="submit">Hapus</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted">Keranjang masih kosong.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($cart->items->isNotEmpty())
            <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin-top:20px;">
                <div><div class="muted">Total</div><h2 style="margin:4px 0 0;">Rp {{ number_format($total, 0, ',', '.') }}</h2></div>
                <form method="post" action="{{ route('shop.checkout') }}">@csrf <button class="btn primary" type="submit">Checkout</button></form>
            </div>
        @endif
    </section>
@endsection
