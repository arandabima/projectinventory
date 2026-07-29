@extends('layouts.user')

@section('title', 'Produk')

@section('content')
    <div class="topbar">
        <div><h1>Produk Tersedia</h1><div class="muted">Pilih produk yang tersedia dan tambahkan ke keranjang.</div></div>
        <a class="btn ghost" href="{{ route('shop.cart') }}">Lihat Keranjang</a>
    </div>

    <section class="card-grid">
        @forelse ($products as $product)
            <article class="product-card">
                <div class="product-media">
                    @if ($product->image_url)<img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="width:100%; height:100%; object-fit:cover;">@endif
                </div>
                <div class="product-body">
                    <div><h2 style="margin:0 0 4px;">{{ $product->name }}</h2><div class="muted">{{ $product->sku }}</div></div>
                    <div style="display:flex; justify-content:space-between; align-items:center;"><strong>Rp {{ number_format($product->price, 0, ',', '.') }}</strong><span class="badge ok">{{ $product->current_stock }} stok</span></div>
                    <form method="post" action="{{ route('shop.cart.add', $product) }}" style="display:flex; gap:8px; align-items:center;">@csrf <input style="max-width:78px;" type="number" name="quantity" min="1" max="{{ $product->current_stock }}" value="1"><button class="btn primary" type="submit">Tambah</button></form>
                </div>
            </article>
        @empty
            <div class="panel muted">Belum ada produk yang tersedia.</div>
        @endforelse
    </section>
    <div class="pagination">{{ $products->links() }}</div>
@endsection
