@extends('layouts.user')

@section('title', 'Dashboard User')

@section('content')
    <section class="hero">
        <h1>Welcome, {{ $user?->name ?? 'User' }}</h1>
        <p>Belanja produk, pantau pesanan, dan lanjutkan pembayaran melalui DOKU Sandbox.</p>
    </section>

    <section class="grid cols-3">
        <div class="panel">
            <div class="muted">Jumlah transaksi</div>
            <h2 style="font-size:34px;">{{ number_format($totalTransactions) }}</h2>
        </div>
        <div class="panel">
            <div class="muted">Pesanan menunggu pembayaran</div>
            <h2 style="font-size:34px;">{{ number_format($pendingOrders) }}</h2>
        </div>
        <div class="panel">
            <div class="muted">Aksi cepat</div>
            <a class="btn primary" href="{{ route('shop.products') }}">Mulai Belanja</a>
        </div>
    </section>
@endsection
