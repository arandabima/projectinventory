@extends('layouts.admin')

@section('content')
    <div class="topbar">
        <div>
            <h1>Pencatatan</h1>
            <div class="muted">Kelola master barang dan catat mutasi stok masuk, keluar, atau penyesuaian.</div>
        </div>
        <form method="get" action="{{ route('admin.items.index') }}" style="display: flex; gap: 8px; width: min(420px, 100%);">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari SKU, barang, lokasi">
            <button type="submit">Cari</button>
        </form>
    </div>

    @if (session('status'))
        <div class="alert ok">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert danger">
            <ul style="margin:0; padding-left:16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="grid cols-2">
        {{-- FORM TAMBAH BARANG --}}
        <div class="panel">
            <h2>Tambah Barang</h2>
            <form method="post" action="{{ route('admin.items.store') }}" class="form-grid" enctype="multipart/form-data">
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
                    <label for="price">Harga</label>
                    <input id="price" type="number" min="0" step="0.01" name="price" value="{{ old('price', 0) }}" required>
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
                {{-- UPLOAD GAMBAR/VIDEO --}}
                <div class="field full">
                    <label for="image">Foto / Video Barang</label>
                    <input id="image" type="file" name="image" accept="image/*,video/*">
                    <div class="muted" style="font-size:12px; margin-top:4px;">Format: JPG, PNG, WebP, MP4, MOV. Maks 20MB.</div>
                </div>
                <div class="field full">
                    <button type="submit">Simpan Barang</button>
                </div>
            </form>
        </div>

        {{-- FORM MUTASI STOK --}}
        <div class="panel">
            <h2>Mutasi Stok</h2>
            <form method="post" action="{{ route('admin.movements.store') }}" class="form-grid" enctype="multipart/form-data">
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
                {{-- UPLOAD DOKUMEN --}}
                <div class="field full">
                    <label for="document">Dokumen / Bukti Transaksi</label>
                    <input id="document" type="file" name="document" accept="image/*,video/*,.pdf">
                    <div class="muted" style="font-size:12px; margin-top:4px;">Format: JPG, PNG, PDF, MP4, MOV. Maks 20MB.</div>
                </div>
                <div class="field full">
                    <button type="submit">Simpan Mutasi</button>
                </div>
            </form>
        </div>
    </section>

    {{-- DAFTAR BARANG --}}
    <section class="panel" style="margin-top: 16px;">
        <h2>Daftar Barang</h2>
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
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
                        <td>
                            @if ($item->image_url)
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                     style="width:48px; height:48px; object-fit:cover; border-radius:6px;">
                            @else
                                <div style="width:48px; height:48px; background:#f0f0f0; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#aaa; font-size:20px;">📦</div>
                            @endif
                        </td>
                        <td>{{ $item->sku }}</td>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            <div class="muted">{{ $item->category?->name ?? 'Tanpa kategori' }}</div>
                        </td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>{{ $item->current_stock }} {{ $item->unit }}
                            <div class="muted">Min {{ $item->minimum_stock }}</div>
                        </td>
                        <td>{{ $item->location ?? '-' }}</td>
                        <td>
                            <span @class(['badge', 'ok' => $item->status->value === 'available', 'danger' => $item->status->value === 'borrowed'])>
                                {{ ucfirst($item->status->value) }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <a class="button ghost" href="{{ route('admin.items.edit', $item) }}">Edit</a>
                                <form method="post" action="{{ route('admin.items.destroy', $item) }}" onsubmit="return confirm('Hapus barang ini? Semua riwayat mutasi barang ini juga akan terhapus.');">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" style="background: var(--danger);">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="muted">Belum ada data barang.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $items->links() }}</div>
    </section>
@endsection
