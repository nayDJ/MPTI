<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
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
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .lunas { background: #d4edda; color: #155724; }
        .belum { background: #f8d7da; color: #721c24; }
        .cicil { background: #fff3cd; color: #856404; }
        .status-label { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>NNQUA</h1>
        <h2>Laporan Penjualan</h2>
        <p>Dicetak: {{ now()->isoFormat('D MMMM YYYY, HH:mm') }}</p>
        @if($period || $status || $from || $to || $search)
            <p>
                Filter:
                @if($period) Periode: {{ ucfirst($period) }} @endif
                @if($search) Pencarian: "{{ $search }}" @endif
                @if($status) Status: {{ ucfirst($status) }} @endif
                @if($from) Dari: {{ \Carbon\Carbon::parse($from)->isoFormat('D MMM YYYY') }} @endif
                @if($to) Sampai: {{ \Carbon\Carbon::parse($to)->isoFormat('D MMM YYYY') }} @endif
            </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Customer</th>
                <th>Tanggal</th>
                <th style="text-align: right;">Total</th>
                <th style="text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $index => $sale)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $sale->customer->name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($sale->sales_date)->isoFormat('D MMM YYYY') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                    <td style="text-align: center;">
                        <span class="status-label {{ $sale->payment_status }}">
                            {{ ucfirst($sale->payment_status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px;">Tidak ada data penjualan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($sales->count() > 0)
        <div class="total">
            Total Pendapatan: Rp {{ number_format($totalRevenue, 0, ',', '.') }}
        </div>
    @endif

    <div class="footer">
        © {{ date('Y') }} Sistem Manajemen Air NNQUA
    </div>

</body>
</html>
