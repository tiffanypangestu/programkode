<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan #{{ $penjualan->penjualan_id }}</title>
</head>

<body style="font-family: sans-serif; font-size: 12px; color: #1f2937; padding: 10px; margin: 0;">

    {{-- Header --}}
    <div style="text-align: center; border-bottom: 2px solid #059669; padding-bottom: 12px; margin-bottom: 20px;">
        <h2 style="font-size: 20px; font-weight: bold; color: #111827; margin: 0; text-transform: uppercase;">
            Faktur Penjualan
        </h2>
        <p style="font-size: 11px; color: #6b7280; margin-top: 4px; margin-bottom: 0;">
            No. Transaksi: #INV-{{ str_pad($penjualan->penjualan_id, 5, '0', STR_PAD_LEFT) }}
        </p>
    </div>

    {{-- Informasi Penjualan --}}
    <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
        <tr>
            <td style="width: 15%; font-weight: bold; color: #374151; padding: 4px 0;">Tanggal</td>
            <td style="width: 35%; padding: 4px 0;">:
                {{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->translatedFormat('d F Y') }}</td>
            <td style="width: 15%; font-weight: bold; color: #374151; padding: 4px 0;">Outlet</td>
            <td style="width: 35%; padding: 4px 0;">: {{ $penjualan->outlet->nama_outlet ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #374151; padding: 4px 0;">Alamat</td>
            <td style="padding: 4px 0;">: {{ $penjualan->outlet->alamat_outlet ?? '-' }}</td>
        </tr>

        <tr>
        </tr>
    </table>

    {{-- Tabel Produk --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #f3f4f6; color: #374151; text-transform: uppercase; font-size: 10px;">
                <th style="border: 1px solid #d1d5db; padding: 8px; text-align: center; width: 5%;">No</th>
                <th style="border: 1px solid #d1d5db; padding: 8px; text-align: left;">Nama Produk</th>
                <th style="border: 1px solid #d1d5db; padding: 8px; text-align: center; width: 12%;">Jumlah</th>
                <th style="border: 1px solid #d1d5db; padding: 8px; text-align: right; width: 20%;">Harga Satuan</th>
                <th style="border: 1px solid #d1d5db; padding: 8px; text-align: right; width: 22%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksiDetails as $index => $detail)
                <tr>
                    <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #e5e7eb; padding: 8px;">{{ $detail->nama_produk ?? 'Produk' }}
                    </td>
                    <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: center;">{{ $detail->jumlah }}</td>
                    <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: right;">
                        Rp {{ number_format($detail->harga_satuan ?? 0, 0, ',', '.') }}
                    </td>
                    <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: right;">
                        Rp {{ number_format(($detail->jumlah ?? 0) * ($detail->harga_satuan ?? 0), 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5"
                        style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; color: #6b7280;">
                        Data rincian produk tidak ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #ecfdf5; color: #065f46; font-weight: bold;">
                <td colspan="4"
                    style="border: 1px solid #d1d5db; padding: 10px; text-align: right; font-size: 13px;">
                    Total Harga:
                </td>
                <td style="border: 1px solid #d1d5db; padding: 10px; text-align: right; font-size: 13px;">
                    Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
