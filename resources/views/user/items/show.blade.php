@extends('layouts.user')

@section('title', $item->name)

@section('content')
    <section class="grid cols-2">
        <div class="panel" style="padding:0; overflow:hidden;">
            @if ($item->image_url)
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="width:100%; height:100%; min-height:380px; object-fit:cover;">
            @else
                <div style="min-height:380px; display:flex; align-items:center; justify-content:center; color:var(--muted);">Tidak ada gambar</div>
            @endif
        </div>
        <div class="panel">
            <span class="badge">{{ $item->category?->name ?? 'Tanpa kategori' }}</span>
            <h1 style="margin-top:14px;">{{ $item->name }}</h1>
            <div class="muted">{{ $item->sku }}</div>
            <p>{{ $item->description ?: 'Tidak ada deskripsi.' }}</p>
            <div class="grid cols-2" style="margin:20px 0;">
                <div class="card">
                    <div class="muted">Harga</div>
                    <strong>Rp {{ number_format($item->price, 0, ',', '.') }}</strong>
                </div>
                <div class="card">
                    <div class="muted">Stok</div>
                    <strong>{{ $item->current_stock }}</strong>
                </div>
            </div>
            <p><strong>Lokasi:</strong> {{ $item->location ?: '-' }}</p>
            <div class="actions">
                <a class="btn primary" href="{{ route('user.borrowings.create', ['item_ids[]' => $item->id]) }}">Checkout</a>
                <a class="btn ghost" href="{{ route('user.items.index') }}">Kembali ke katalog</a>
            </div>
        </div>
    </section>
@endsection
