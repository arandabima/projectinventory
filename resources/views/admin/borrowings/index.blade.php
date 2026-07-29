@extends('layouts.admin')

@section('title', 'Borrowings')

@section('content')
    <h1>Kelola Borrowing</h1>
    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Barang</th>
                    <th>Tanggal Pinjam</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($borrowings as $borrowing)
                    <tr>
                        <td>{{ $borrowing->user?->name }}</td>
                        <td>{{ $borrowing->items->sum('quantity') }}</td>
                        <td>{{ $borrowing->borrow_date->format('d M Y') }}</td>
                        <td>{{ ucfirst($borrowing->status->value) }}</td>
                        <td style="display:flex; gap:6px; flex-wrap:wrap;">
                            <form method="post" action="{{ route('admin.borrowings.approve', $borrowing) }}">@csrf<button type="submit">Approve</button></form>
                            <form method="post" action="{{ route('admin.borrowings.reject', $borrowing) }}">@csrf<button type="submit" style="background:#b42318;">Reject</button></form>
                            <form method="post" action="{{ route('admin.borrowings.returned', $borrowing) }}">@csrf<button type="submit" style="background:#334155;">Returned</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Belum ada pengajuan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
