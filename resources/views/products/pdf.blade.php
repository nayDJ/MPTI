<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produk</title>
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
        <h2>Laporan Produk</h2>
        <p>Dicetak: {{ now()->isoFormat('D MMMM YYYY, HH:mm') }}</p>
        @if($search || $category)
            <p>
                Filter:
                @if($search) Pencarian: "{{ $search }}" @endif
                @if($category) Kategori: {{ $category }} @endif
            </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th class="text-right">Stok</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category ?? '-' }}</td>
                    <td class="text-right">{{ $product->stock }}</td>
                    <td class="text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($product->stock * $product->price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px;">Tidak ada data produk</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($products->count() > 0)
        <div class="total">
            Total Nilai Inventaris: Rp {{ number_format($totalValuation, 0, ',', '.') }}
        </div>
    @endif

    <div class="footer">
        © {{ date('Y') }} Sistem Manajemen Air NNQUA
    </div>

</body>
</html>
