@extends('layouts.admin')

@section('content')
    <div class="topbar">
        <div>
            <h1>Cetak Laporan</h1>
            <div class="muted">Export laporan stok dan mutasi dalam format CSV.</div>
        </div>
    </div>

    <section class="grid cols-3">
        <div class="panel stat"><span class="muted">Total stok tercatat</span><strong>{{ number_format($stockValue) }}</strong></div>
        <div class="panel stat"><span class="muted">Mutasi stok</span><strong>{{ number_format($movementCount) }}</strong></div>
        <div class="panel stat"><span class="muted">Stok menipis</span><strong>{{ number_format($lowStockCount) }}</strong></div>
        <div class="panel stat"><span class="muted">Total borrowing</span><strong>{{ number_format($borrowingCount) }}</strong></div>
        <div class="panel stat"><span class="muted">Pembayaran pending</span><strong>{{ number_format($pendingPaymentCount) }}</strong></div>
    </section>

    <section class="grid cols-2" style="margin-top: 16px;">
        <div class="panel">
            <h2>Buat Export</h2>
            <form method="post" action="{{ route('admin.reports.export') }}" class="form-grid">
                @csrf
                <div class="field full">
                    <label for="report_type">Jenis Laporan</label>
                    <select id="report_type" name="report_type" required>
                        <option value="stock">Stok Barang</option>
                        <option value="movement">Mutasi Stok</option>
                        <option value="borrowings">Riwayat Borrowing</option>
                        <option value="payments">Rekonsiliasi Pembayaran Borrowing</option>
                        <option value="orders">Riwayat Order (Legacy)</option>
                    </select>
                </div>
                <div class="field">
                    <label for="period_start">Tanggal Awal</label>
                    <input id="period_start" type="date" name="period_start" value="{{ old('period_start') }}">
                </div>
                <div class="field">
                    <label for="period_end">Tanggal Akhir</label>
                    <input id="period_end" type="date" name="period_end" value="{{ old('period_end') }}">
                </div>
                <div class="field full">
                    <label for="generated_by">Dibuat Oleh</label>
                    <input id="generated_by" name="generated_by" value="{{ old('generated_by', 'operator') }}">
                </div>
                <div class="field full">
                    <button type="submit">Download CSV</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <h2>Riwayat Export</h2>
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Jenis</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($exports as $export)
                        <tr>
                            <td>{{ $export->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $export->report_type }}</td>
                            <td>{{ $export->file_name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="muted">Belum ada export.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
