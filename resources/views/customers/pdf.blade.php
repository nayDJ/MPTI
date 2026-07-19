<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pelanggan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin: 0; color: #0F6E8C; }
        h2 { font-size: 14px; margin: 0 0 4px; color: #555; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #0F6E8C; }
        .header p { margin: 2px 0; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #0F6E8C; color: white; padding: 8px 6px; text-align: left; font-size: 11px; }
        td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 11px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .total { margin-top: 15px; text-align: right; font-weight: bold; font-size: 13px; }
        .footer { position: fixed; bottom: 10px; left: 0; right: 0; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 6px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <h1>NNQUA</h1>
        <h2>Laporan Pelanggan</h2>
        <p>Dicetak: {{ now()->isoFormat('D MMMM YYYY, HH:mm') }}</p>
        @if($search || $is_active !== null || $debt_status)
            <p>
                Filter:
                @if($search) Pencarian: "{{ $search }}" @endif
                @if($is_active !== null && $is_active !== '') Akun: {{ $is_active === '1' ? 'Aktif' : 'Nonaktif' }} @endif
                @if($debt_status) Status: {{ ucfirst($debt_status) }} @endif
            </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama Pelanggan</th>
                <th>Telepon</th>
                <th>Alamat</th>
                <th class="text-right">Total Transaksi</th>
                <th class="text-right">Total Pembelian</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $index => $customer)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone ?? '-' }}</td>
                    <td>{{ $customer->address ?? '-' }}</td>
                    <td class="text-right">{{ $customer->sales_count ?? $customer->sales->count() }}</td>
                    <td class="text-right">Rp {{ number_format($customer->total_purchase ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px;">Tidak ada data pelanggan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} Sistem Manajemen Air NNQUA
    </div>

</body>
</html>
