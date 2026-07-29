@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="topbar">
        <div>
            <h1>Dashboard Admin</h1>
            <div class="muted">Statistik cepat inventory dan transaksi berjalan.</div>
        </div>
        <a class="btn primary" href="{{ route('admin.borrowings.index') }}">Kelola Borrowing</a>
    </div>

    <section class="kpi-grid">
        <div class="card metric">
            <span class="muted">Total barang</span>
            <strong>{{ number_format($totalItems) }}</strong>
        </div>
        <div class="card metric">
            <span class="muted">Barang tersedia</span>
            <strong>{{ number_format($availableItems) }}</strong>
        </div>
        <div class="card metric">
            <span class="muted">Barang sedang dipinjam</span>
            <strong>{{ number_format($borrowedItems) }}</strong>
        </div>
        <div class="card metric">
            <span class="muted">Total borrowing</span>
            <strong>{{ number_format($totalBorrowings) }}</strong>
        </div>
        <div class="card metric">
            <span class="muted">Borrowing pending</span>
            <strong>{{ number_format($pendingBorrowings) }}</strong>
        </div>
        <div class="card metric">
            <span class="muted">Borrowing aktif</span>
            <strong>{{ number_format($activeBorrowings) }}</strong>
        </div>
        <div class="card metric">
            <span class="muted">Borrowing selesai</span>
            <strong>{{ number_format($completedBorrowings) }}</strong>
        </div>
    </section>
@endsection
