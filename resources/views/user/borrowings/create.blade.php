@extends('layouts.user')

@section('title', 'Checkout Barang')

@section('content')
    <div class="topbar">
        <div>
            <h1>Checkout Barang</h1>
            <div class="muted">Isi tanggal pinjam, tanggal kembali, dan catatan.</div>
        </div>
        <a class="button ghost" href="{{ route('user.items.index') }}">Kembali</a>
    </div>

    <form method="post" action="{{ route('user.borrowings.store') }}" class="stack">
        @csrf
        <section class="panel">
            <div class="form-grid">
                <div class="field">
                    <label for="borrow_date">Tanggal Pinjam</label>
                    <input type="date" id="borrow_date" name="borrow_date" value="{{ old('borrow_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="field">
                    <label for="return_date">Tanggal Kembali</label>
                    <input type="date" id="return_date" name="return_date" value="{{ old('return_date', now()->addDay()->format('Y-m-d')) }}" required>
                </div>
                <div class="field full">
                    <label for="notes">Catatan</label>
                    <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
                </div>
            </div>
        </section>

        <section class="panel">
            <h2>Barang Dipilih</h2>
            <table>
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Harga</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                            </td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td><input type="number" min="1" name="quantities[]" value="1" required></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <button type="submit">Ajukan Peminjaman</button>
    </form>
@endsection
