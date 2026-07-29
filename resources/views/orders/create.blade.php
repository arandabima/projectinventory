@extends('layouts.admin')

@section('content')
    <div class="topbar">
        <div>
            <h1>Buat Order</h1>
            <div class="muted">Pilih barang, jumlah, dan harga untuk membuat invoice pembayaran.</div>
        </div>
        <a class="button ghost" href="{{ route('admin.transactions.index') }}">Kembali</a>
    </div>

    <form method="post" action="{{ route('admin.orders.store') }}" class="stack">
        @csrf
        <section class="panel">
            <h2>Informasi Order</h2>
            <div class="form-grid">
                <div class="field">
                    <label for="supplier_name">Supplier / Vendor</label>
                    <input id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" required>
                </div>
                <div class="field">
                    <label for="recipient_name">Penerima</label>
                    <input id="recipient_name" name="recipient_name" value="{{ old('recipient_name', auth()->user()?->name) }}" required>
                </div>
                <div class="field full">
                    <label for="notes">Catatan</label>
                    <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="toolbar" style="margin-bottom: 12px;">
                <h2 style="margin:0;">Item Order</h2>
                <button type="button" class="button ghost" onclick="addOrderLine()">Tambah Baris</button>
            </div>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th style="width:130px;">Jumlah</th>
                            <th style="width:180px;">Harga Satuan</th>
                            <th style="width:180px;">Subtotal</th>
                            <th style="width:80px;"></th>
                        </tr>
                    </thead>
                    <tbody id="order-lines">
                        @for ($i = 0; $i < max(1, count(old('item_ids', []))); $i++)
                            <tr>
                                <td>
                                    <select name="item_ids[]" required>
                                        <option value="">Pilih barang</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" @selected(old("item_ids.$i") == $item->id)>
                                                {{ $item->sku }} - {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" min="1" name="quantities[]" value="{{ old("quantities.$i", 1) }}" oninput="recalculateOrder()" required></td>
                                <td><input type="number" min="0" step="0.01" name="unit_prices[]" value="{{ old("unit_prices.$i") }}" oninput="recalculateOrder()" required></td>
                                <td class="line-subtotal">Rp 0</td>
                                <td><button type="button" class="button ghost" onclick="removeOrderLine(this)">Hapus</button></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <div class="toolbar" style="margin-top: 14px;">
                <span class="muted">Total invoice dihitung dari jumlah x harga satuan.</span>
                <strong id="order-total">Rp 0</strong>
            </div>
        </section>

        <div>
            <button type="submit">Generate Invoice</button>
        </div>
    </form>

    <template id="order-line-template">
        <tr>
            <td>
                <select name="item_ids[]" required>
                    <option value="">Pilih barang</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}">{{ $item->sku }} - {{ $item->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" min="1" name="quantities[]" value="1" oninput="recalculateOrder()" required></td>
            <td><input type="number" min="0" step="0.01" name="unit_prices[]" oninput="recalculateOrder()" required></td>
            <td class="line-subtotal">Rp 0</td>
            <td><button type="button" class="button ghost" onclick="removeOrderLine(this)">Hapus</button></td>
        </tr>
    </template>

    <script>
        const rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });

        function addOrderLine() {
            document.getElementById('order-lines').appendChild(document.getElementById('order-line-template').content.cloneNode(true));
            recalculateOrder();
        }

        function removeOrderLine(button) {
            const rows = document.querySelectorAll('#order-lines tr');
            if (rows.length > 1) {
                button.closest('tr').remove();
            }
            recalculateOrder();
        }

        function recalculateOrder() {
            let total = 0;
            document.querySelectorAll('#order-lines tr').forEach((row) => {
                const quantity = Number(row.querySelector('[name="quantities[]"]').value || 0);
                const price = Number(row.querySelector('[name="unit_prices[]"]').value || 0);
                const subtotal = quantity * price;
                total += subtotal;
                row.querySelector('.line-subtotal').textContent = rupiah.format(subtotal);
            });
            document.getElementById('order-total').textContent = rupiah.format(total);
        }

        recalculateOrder();
    </script>
@endsection
