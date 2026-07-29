@extends('layouts.user')

@section('title', 'Detail Transaksi')

@section('content')

    <section class="panel">
        <h1>Status: {{ ucfirst($borrowing->status->value) }}</h1>
        <p>Tanggal pinjam: {{ $borrowing->borrow_date->format('d M Y') }}</p>
        <p>Tanggal kembali: {{ $borrowing->return_date->format('d M Y') }}</p>
        <p>Catatan: {{ $borrowing->notes ?: '-' }}</p>
        <p>Disetujui oleh: {{ $borrowing->approver?->name ?: '-' }}</p>
    </section>


    <section class="panel" style="margin-top:16px;">
        <h2>Barang</h2>

        <table>
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Qty</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($borrowing->items as $lineItem)

                    <tr>
                        <td>{{ $lineItem->item?->name }}</td>
                        <td>{{ $lineItem->quantity }}</td>
                    </tr>

                @endforeach
            </tbody>
        </table>
    </section>


    @if($borrowing->status->value === 'approved' && !$borrowing->payment)

        <section class="panel" style="margin-top:16px;">

            <h2>Pembayaran</h2>

            <form action="{{ route('user.borrowings.payment', $borrowing) }}" method="POST">

                @csrf

                <button type="submit">
                    Bayar Sekarang
                </button>

            </form>

        </section>

    @endif

@endsection