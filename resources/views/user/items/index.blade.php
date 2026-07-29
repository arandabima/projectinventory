@extends('layouts.user')

@section('title', 'Katalog Barang')

@section('content')
    <div class="topbar">
        <div>
            <h1>Katalog Barang</h1>
            <div class="muted">Cari barang, filter kategori, lalu lanjut ke detail atau checkout.</div>
        </div>
        <form method="get" action="{{ route('user.items.index') }}" style="display:flex; gap:8px; width:min(620px,100%); flex-wrap:wrap;">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari barang">
            <select name="category">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button type="submit">Cari</button>
        </form>
    </div>

    <section class="card-grid">
        @forelse ($items as $item)
            <article class="product-card">
                <div class="product-media">
                    @if ($item->image_url)
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="width:100%; height:100%; object-fit:cover;">
                    @endif
                </div>
                <div class="product-body">
                    <div>
                        <h2 style="margin:0 0 6px;">{{ $item->name }}</h2>
                        <div class="muted">{{ $item->category?->name ?? 'Tanpa kategori' }}</div>
                    </div>
                    <p class="muted" style="margin:0;">{{ \Illuminate\Support\Str::limit($item->description, 100) }}</p>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                    <strong>Rp {{ number_format($item->price, 0, ',', '.') }}</strong>
                    <span class="badge {{ $item->current_stock > 0 ? 'ok' : 'danger' }}">{{ $item->current_stock }} stok</span>
                    </div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <a class="button ghost" href="{{ route('user.items.show', $item) }}">Detail</a>
                    <a class="button" href="{{ route('user.borrowings.create', ['item_ids[]' => $item->id]) }}">Checkout</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="panel">Belum ada barang.</div>
        @endforelse
    </section>

    <div class="pagination">{{ $items->links() }}</div>
@endsection
