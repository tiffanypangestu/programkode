<table>
    <thead>
        <tr>
            <th style="font-weight: bold; background-color: #e5e7eb;">tanggal</th>
            <th style="font-weight: bold; background-color: #e5e7eb;">nomor_transaksi</th>
            <th style="font-weight: bold; background-color: #e5e7eb;">kode_outlet</th>
            <th style="font-weight: bold; background-color: #e5e7eb;">nama_produk</th>
            <th style="font-weight: bold; background-color: #e5e7eb;">jumlah</th>
            <th style="font-weight: bold; background-color: #e5e7eb;">harga_satuan</th>
            <th style="font-weight: bold; background-color: #d1d5db;">subtotal</th>
        </tr>
    </thead>
    <tbody>
        {{-- Contoh baris transaksi pertama --}}
        <tr>
            <td>2026-10-25</td>
            <td>TRX-0001</td>
            <td>OUT-0001</td>
            <td>Contoh Produk A</td>
            <td>2</td>
            <td>15000</td>
            <td></td>
        </tr>
        <tr>
            <td>2026-10-25</td>
            <td>TRX-001</td>
            <td>OUT-001</td>
            <td>Contoh Produk B</td>
            <td>1</td>
            <td>25000</td>
            <td></td>
        </tr>
        {{-- Baris kosong tambahan --}}
        @for ($i = 1; $i <= 8; $i++)
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endfor
    </tbody>
</table>
