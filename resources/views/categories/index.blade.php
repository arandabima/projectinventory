@extends('layouts.app')

@section('content')
    <div class="topbar">
        <div>
            <h1>Kategori</h1>
            <div class="muted">Kelola master kategori untuk pengelompokan barang.</div>
        </div>
        <form method="get" action="{{ route('categories.index') }}" style="display: flex; gap: 8px; width: min(420px, 100%);">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari kategori">
            <button type="submit">Cari</button>
        </form>
    </div>

    <section class="grid cols-2">
        <div class="panel">
            <h2>Tambah Kategori</h2>
            <form method="post" action="{{ route('categories.store') }}" class="form-grid">
                @csrf
                <div class="field full">
                    <label for="name">Nama Kategori</label>
                    <input id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="field full">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description">{{ old('description') }}</textarea>
                </div>
                <div class="field full">
                    <button type="submit">Simpan Kategori</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <h2>Ringkasan</h2>
            <div class="stat">
                <span class="muted">Total kategori</span>
                <strong>{{ number_format($categories->total()) }}</strong>
            </div>
        </div>
    </section>

    <section class="panel" style="margin-top: 16px;">
        <h2>Daftar Kategori</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Barang</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>{{ $category->description ?? '-' }}</td>
                        <td>{{ number_format($category->items_count) }}</td>
                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <a class="button ghost" href="{{ route('categories.edit', $category) }}">Edit</a>
                                <form method="post" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini? Barang di kategori ini akan menjadi tanpa kategori.');">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" style="background: var(--danger);">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted">Belum ada data kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $categories->links() }}</div>
    </section>
@endsection
