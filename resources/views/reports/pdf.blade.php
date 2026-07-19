<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Sistem - {{ $periodLabel }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 20px; }
        h1 { font-size: 18px; margin: 0; color: #0F6E8C; }
        h2 { font-size: 13px; margin: 15px 0 6px; color: #0F6E8C; border-bottom: 1px solid #0F6E8C; padding-bottom: 3px; }
        h3 { font-size: 11px; margin: 10px 0 4px; color: #555; }
        .header { text-align: center; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #0F6E8C; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .summary-table td { width: 25%; padding: 8px; text-align: center; border: 1px solid #ddd; }
        .summary-table .label { font-size: 10px; color: #666; }
        .summary-table .value { font-size: 14px; font-weight: bold; margin-top: 2px; }
        .bar-table { width: 100%; border-collapse: collapse; margin: 8px 0 15px; }
        .bar-table td { text-align: center; vertical-align: bottom; width: 8.33%; height: 160px; font-size: 0; }
        .bar-table .bar { width: 70%; margin: 0 auto; border-radius: 3px 3px 0 0; }
        .bar-table .month-label { font-size: 8px; color: #666; padding-top: 3px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 6px; margin-bottom: 12px; }
        .data-table th { background: #0F6E8C; color: white; padding: 5px 4px; text-align: left; font-size: 10px; }
        .data-table td { padding: 4px; border-bottom: 1px solid #ddd; font-size: 10px; }
        .data-table tr:nth-child(even) { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { position: fixed; bottom: 10px; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 4px; }
        .page-break { page-break-before: always; }
        .section-title { font-size: 12px; font-weight: bold; color: #0F6E8C; margin: 12px 0 4px; padding-bottom: 2px; border-bottom: 1px solid #ccc; }
        .color-primary { color: #0F6E8C; }
        .color-profit { color: #8f5919; }
        .color-danger { color: #ba1a1a; }
    </style>
</head>
<body>

    <div class="header">
        <h1>NNQUA</h1>
        <p style="font-size: 13px; font-weight: bold; margin: 4px 0;">Laporan Sistem</p>
        <p>Periode: {{ $periodLabel }} &nbsp;|&nbsp; Dicetak: {{ now()->isoFormat('D MMMM YYYY, HH:mm') }}</p>
    </div>

    <h2>Ringkasan Keuangan</h2>
    <table class="summary-table">
        <tr>
            <td style="border-top: 3px solid #0F6E8C;">
                <div class="label">Total Pendapatan</div>
                <div class="value" style="color:#0F6E8C;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </td>
            <td style="border-top: 3px solid #545f73;">
                <div class="label">Total Pengeluaran</div>
                <div class="value" style="color:#545f73;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </td>
            <td style="border-top: 3px solid #8f5919;">
                <div class="label">Laba Bersih</div>
                <div class="value" style="color:#8f5919;">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
            </td>
            <td style="border-top: 3px solid #ba1a1a;">
                <div class="label">Total Piutang</div>
                <div class="value" style="color:#ba1a1a;">Rp {{ number_format($outstandingReceivables, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Grafik Pendapatan (Pemasukan)</div>
    <table class="bar-table">
        <tr>
            @php $incMax = $maxIncome > 0 ? $maxIncome : 1; @endphp
            @for($m = 0; $m < 12; $m++)
                @php
                    $h = max(3, ($chartIncome[$m] / $incMax) * 140);
                    $isCur = ($m + 1) === $currentMonth;
                @endphp
                <td>
                    <div class="bar" style="height: {{ round($h) }}px; background: {{ $isCur ? '#0F6E8C' : 'rgba(15,110,140,0.35)' }};"></div>
                    <div class="month-label" style="{{ $isCur ? 'font-weight:bold;color:#0F6E8C;' : '' }}">{{ $monthNames[$m] }}</div>
                </td>
            @endfor
        </tr>
    </table>

    <div class="section-title">Grafik Pengeluaran</div>
    <table class="bar-table">
        <tr>
            @php $expMax = $maxExpense > 0 ? $maxExpense : 1; @endphp
            @for($m = 0; $m < 12; $m++)
                @php
                    $h = max(3, ($chartExpense[$m] / $expMax) * 140);
                    $isCur = ($m + 1) === $currentMonth;
                @endphp
                <td>
                    <div class="bar" style="height: {{ round($h) }}px; background: {{ $isCur ? '#0F6E8C' : 'rgba(15,110,140,0.35)' }};"></div>
                    <div class="month-label" style="{{ $isCur ? 'font-weight:bold;color:#0F6E8C;' : '' }}">{{ $monthNames[$m] }}</div>
                </td>
            @endfor
        </tr>
    </table>

    <div class="section-title">Produk Terlaris <span style="font-weight:normal;font-size:10px;color:#666;">({{ number_format($totalUnitsSold) }} total terjual)</span></div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th>Nama Produk</th>
                <th class="text-right">Terjual</th>
                <th class="text-right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right">{{ number_format($item->total_sold) }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center" style="padding:15px;">Belum ada data produk</td></tr>
            @endforelse
        </tbody>
    </table>

    <table style="width:100%;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right:6px;">
                <div class="section-title">Pelanggan Teratas</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:30px;">No</th>
                            <th>Nama</th>
                            <th class="text-right">Pesanan</th>
                            <th class="text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCustomers as $i => $c)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $c->customer->name }}</td>
                                <td class="text-right">{{ $c->total_orders }}</td>
                                <td class="text-right">Rp {{ number_format($c->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center" style="padding:10px;">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td style="width:50%; vertical-align:top; padding-left:6px;">
                <div class="section-title">Debitur Teratas</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:30px;">No</th>
                            <th>Nama</th>
                            <th class="text-right">Transaksi</th>
                            <th class="text-right">Sisa Utang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topDebtors as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $d->customer->name }}</td>
                                <td class="text-right">{{ $d->total_transaksi }}</td>
                                <td class="text-right">Rp {{ number_format($d->sisa_utang, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center" style="padding:10px;">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    @if($view === 'income')
        <div class="section-title">Detail Transaksi Pemasukan</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:30px;">No</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th class="text-right">Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $i => $sale)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($sale->sales_date)->isoFormat('D MMM YYYY') }}</td>
                        <td>{{ $sale->customer->name ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($sale->payment_status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center" style="padding:20px;">Tidak ada data penjualan</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="text-align:right;font-weight:bold;margin-top:6px;font-size:11px;">
            Total Pendapatan: Rp {{ number_format($totalRevenue, 0, ',', '.') }}
        </div>
    @else
        <div class="section-title">Detail Transaksi Pengeluaran</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:30px;">No</th>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                    <th>Kategori</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $i => $expense)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($expense->expense_date)->isoFormat('D MMM YYYY') }}</td>
                        <td>{{ $expense->description }}</td>
                        <td>{{ $expense->category }}</td>
                        <td class="text-right">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center" style="padding:20px;">Tidak ada data pengeluaran</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="text-align:right;font-weight:bold;margin-top:6px;font-size:11px;">
            Total Pengeluaran: Rp {{ number_format($totalExpense, 0, ',', '.') }}
        </div>
    @endif

    <div class="footer">
        © {{ date('Y') }} Sistem Manajemen Air NNQUA — Laporan digital ini dicetak secara otomatis
    </div>

</body>
</html>
