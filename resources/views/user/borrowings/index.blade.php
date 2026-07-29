@extends('layouts.user')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="topbar">
        <div>
            <h1>Riwayat Transaksi</h1>
            <div class="muted">Semua pengajuan milik Anda.</div>
        </div>
        <a class="button" href="{{ route('user.items.index') }}">Pilih Barang</a>
    </div>
    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Tanggal Pinjam</th>
                    <th>Kembali</th>
                    <th>Status</th>
                    <th>Barang</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($borrowings as $borrowing)
                    <tr>
                        <td>{{ $borrowing->borrow_date->format('d M Y') }}</td>
                        <td>{{ $borrowing->return_date->format('d M Y') }}</td>
                        <td><span class="badge">{{ ucfirst($borrowing->status->value) }}</span></td>
                        <td>{{ $borrowing->items->sum('quantity') }}</td>
                        <td><a class="button ghost" href="{{ route('user.borrowings.show', $borrowing) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $borrowings->links() }}</div>
    </section>
@endsection
