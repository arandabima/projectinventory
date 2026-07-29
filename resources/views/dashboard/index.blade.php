@extends('layouts.app')

@section('content')
    <div class="topbar">
        <div>
            <h1>Dashboard Inventory</h1>
            <div class="muted">Ringkasan stok, aktivitas pencatatan, dan notifikasi gudang.</div>
        </div>
        <a class="button" href="{{ route('admin.items.index') }}">Tambah Pencatatan</a>
    </div>

    <section class="grid cols-3">
        <div class="panel stat">
            <span class="muted">Total barang</span>
            <strong>{{ number_format($totalItems) }}</strong>
        </div>
        <div class="panel stat">
            <span class="muted">Total stok</span>
            <strong>{{ number_format($totalStock) }}</strong>
        </div>
        <div class="panel stat">
            <span class="muted">Butuh perhatian</span>
            <strong>{{ number_format($lowStockCount) }}</strong>
        </div>
    </section>

    <section class="grid cols-2" style="margin-top: 16px;">
        <div class="panel">
            <h2>Stok Menipis</h2>
            <table>
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lowStockItems as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                <div class="muted">{{ $item->sku }} · {{ $item->category?->name ?? 'Tanpa kategori' }}</div>
                            </td>
                            <td>{{ $item->current_stock }} / min {{ $item->minimum_stock }} {{ $item->unit }}</td>
                            <td><span @class(['badge', 'ok' => $item->status->value === 'available', 'danger' => $item->status->value === 'borrowed'])>{{ ucfirst($item->status->value) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="muted">Tidak ada stok menipis.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel">
            <h2>Mutasi Terakhir</h2>
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Barang</th>
                        <th>Mutasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestMovements as $movement)
                        <tr>
                            <td>{{ $movement->created_at->format('d M H:i') }}</td>
                            <td>{{ $movement->item?->name ?? '-' }}</td>
                            <td>{{ strtoupper($movement->type) }} · {{ $movement->stock_before }} ke {{ $movement->stock_after }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="muted">Belum ada mutasi stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
