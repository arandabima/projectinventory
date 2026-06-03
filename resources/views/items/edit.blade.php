@extends('layouts.app')

@section('content')
    <div class="topbar">
        <div>
            <h1>Edit Barang</h1>
            <div class="muted">{{ $item->sku }} · {{ $item->name }}</div>
        </div>
        <a class="button ghost" href="{{ route('items.index') }}">Kembali</a>
    </div>

    <section class="panel">
        <form method="post" action="{{ route('items.update', $item) }}" class="form-grid">
            @csrf
            @method('put')
            <div class="field">
                <label for="sku">SKU</label>
                <input id="sku" name="sku" value="{{ old('sku', $item->sku) }}" required>
            </div>
            <div class="field">
                <label for="name">Nama Barang</label>
                <input id="name" name="name" value="{{ old('name', $item->name) }}" required>
            </div>
            <div class="field">
                <label for="category_id">Kategori</label>
                <select id="category_id" name="category_id">
                    <option value="">Tanpa kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $item->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="unit">Satuan</label>
                <input id="unit" name="unit" value="{{ old('unit', $item->unit) }}" required>
            </div>
            <div class="field">
                <label for="current_stock">Stok Saat Ini</label>
                <input id="current_stock" type="number" min="0" name="current_stock" value="{{ old('current_stock', $item->current_stock) }}" required>
            </div>
            <div class="field">
                <label for="minimum_stock">Minimum Stok</label>
                <input id="minimum_stock" type="number" min="0" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock) }}" required>
            </div>
            <div class="field">
                <label for="location">Lokasi</label>
                <input id="location" name="location" value="{{ old('location', $item->location) }}">
            </div>
            <div class="field full">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description">{{ old('description', $item->description) }}</textarea>
            </div>
            <div class="field full">
                <button type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </section>
@endsection
