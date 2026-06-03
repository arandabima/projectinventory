@extends('layouts.app')

@section('content')
    <div class="topbar">
        <div>
            <h1>Pencatatan</h1>
            <div class="muted">Kelola master barang dan catat mutasi stok masuk, keluar, atau penyesuaian.</div>
        </div>
        <form method="get" action="{{ route('items.index') }}" style="display: flex; gap: 8px; width: min(420px, 100%);">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari SKU, barang, lokasi">
            <button type="submit">Cari</button>
        </form>
    </div>

    <section class="grid cols-2">
        <div class="panel">
            <h2>Tambah Barang</h2>
            <form method="post" action="{{ route('items.store') }}" class="form-grid">
                @csrf
                <div class="field">
                    <label for="sku">SKU</label>
                    <input id="sku" name="sku" value="{{ old('sku') }}" required>
                </div>
                <div class="field">
                    <label for="name">Nama Barang</label>
                    <input id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="field">
                    <label for="category_id">Kategori</label>
                    <select id="category_id" name="category_id">
                        <option value="">Tanpa kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="unit">Satuan</label>
                    <input id="unit" name="unit" value="{{ old('unit', 'pcs') }}" required>
                </div>
                <div class="field">
                    <label for="current_stock">Stok Awal</label>
                    <input id="current_stock" type="number" min="0" name="current_stock" value="{{ old('current_stock', 0) }}" required>
                </div>
                <div class="field">
                    <label for="minimum_stock">Minimum Stok</label>
                    <input id="minimum_stock" type="number" min="0" name="minimum_stock" value="{{ old('minimum_stock', 0) }}" required>
                </div>
                <div class="field">
                    <label for="location">Lokasi</label>
                    <input id="location" name="location" value="{{ old('location') }}">
                </div>
                <div class="field full">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description">{{ old('description') }}</textarea>
                </div>
                <div class="field full">
                    <button type="submit">Simpan Barang</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <h2>Mutasi Stok</h2>
            <form method="post" action="{{ route('movements.store') }}" class="form-grid">
                @csrf
                <div class="field full">
                    <label for="item_id">Barang</label>
                    <select id="item_id" name="item_id" required>
                        <option value="">Pilih barang</option>
                        @foreach ($movementItems as $item)
                            <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>{{ $item->sku }} · {{ $item->name }} (stok {{ $item->current_stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="type">Jenis</label>
                    <select id="type" name="type" required>
                        <option value="in" @selected(old('type') === 'in')>Masuk</option>
                        <option value="out" @selected(old('type') === 'out')>Keluar</option>
                        <option value="adjustment" @selected(old('type') === 'adjustment')>Penyesuaian</option>
                    </select>
                </div>
                <div class="field">
                    <label for="quantity">Jumlah</label>
                    <input id="quantity" type="number" name="quantity" value="{{ old('quantity', 1) }}" required>
                </div>
                <div class="field">
                    <label for="recorded_by">Dicatat Oleh</label>
                    <input id="recorded_by" name="recorded_by" value="{{ old('recorded_by', 'operator') }}">
                </div>
                <div class="field full">
                    <label for="notes">Catatan</label>
                    <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
                </div>
                <div class="field full">
                    <button type="submit">Simpan Mutasi</button>
                </div>
            </form>
        </div>
    </section>

    <section class="panel" style="margin-top: 16px;">
        <h2>Daftar Barang</h2>
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Barang</th>
                    <th>Stok</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->sku }}</td>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            <div class="muted">{{ $item->category?->name ?? 'Tanpa kategori' }}</div>
                        </td>
                        <td>{{ $item->current_stock }} {{ $item->unit }}<div class="muted">Min {{ $item->minimum_stock }}</div></td>
                        <td>{{ $item->location ?? '-' }}</td>
                        <td><span @class(['badge', 'ok' => $item->status === 'Aman', 'warn' => $item->status === 'Menipis', 'danger' => $item->status === 'Habis'])>{{ $item->status }}</span></td>
                        <td><a class="button ghost" href="{{ route('items.edit', $item) }}">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted">Belum ada data barang.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $items->links() }}</div>
    </section>
@endsection
