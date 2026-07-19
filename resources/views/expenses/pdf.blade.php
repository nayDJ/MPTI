<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengeluaran</title>
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
        <h2>Laporan Pengeluaran</h2>
        <p>Dicetak: {{ now()->isoFormat('D MMMM YYYY, HH:mm') }}</p>
        @if($search || $category || $from || $to)
            <p>
                Filter:
                @if($search) Pencarian: "{{ $search }}" @endif
                @if($category) Kategori: {{ $category }} @endif
                @if($from) Dari: {{ \Carbon\Carbon::parse($from)->isoFormat('D MMM YYYY') }} @endif
                @if($to) Sampai: {{ \Carbon\Carbon::parse($to)->isoFormat('D MMM YYYY') }} @endif
            </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $index => $expense)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->isoFormat('D MMM YYYY') }}</td>
                    <td class="text-right">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px;">Tidak ada data pengeluaran</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($expenses->count() > 0)
        <div class="total">
            Total Pengeluaran: Rp {{ number_format($totalExpense, 0, ',', '.') }}
        </div>
    @endif

    <div class="footer">
        © {{ date('Y') }} Sistem Manajemen Air NNQUA
    </div>

</body>
</html>
