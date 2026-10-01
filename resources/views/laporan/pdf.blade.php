<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - {{ config('app.name') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 24px; color: #333; font-size: 12px; }
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { font-size: 22px; margin: 0 0 4px; color: #5b3923; }
        .header p { margin: 3px 0; color: #7f8c8d; }
        .ringkasan { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin: 0 -8px 16px; }
        .ringkasan td { background: #f8f6f2; border-left: 4px solid #8b5e34; padding: 10px 12px; width: 33%; }
        .ringkasan .label { font-size: 10px; color: #7f8c8d; text-transform: uppercase; }
        .ringkasan .nilai { font-size: 17px; font-weight: bold; color: #2c3e50; margin-top: 4px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #8b5e34; color: #fff; }
        table.data th, table.data td { padding: 7px 8px; border: 1px solid #ddd; text-align: left; }
        table.data tbody tr:nth-child(even) { background: #f8f6f2; }
        .kanan { text-align: right !important; }
        .tengah { text-align: center !important; }
        .footer { margin-top: 28px; text-align: right; font-size: 10px; color: #7f8c8d; }
        @if($preview)
        body { background: #eee; padding: 0 0 80px; }
        .kertas { max-width: 960px; margin: 24px auto; background: #fff; padding: 40px; box-shadow: 0 0 20px rgba(0,0,0,.1); }
        .aksi { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 12px; text-align: center; box-shadow: 0 -2px 10px rgba(0,0,0,.1); }
        .aksi a, .aksi button { display: inline-block; margin: 0 4px; padding: 9px 22px; border: 0; border-radius: 50px; font: inherit; font-weight: 600; text-decoration: none; cursor: pointer; }
        .unduh { background: #8b5e34; color: #fff; }
        .cetak { background: #6c757d; color: #fff; }
        @media print { .aksi { display: none; } body { background: #fff; } .kertas { box-shadow: none; margin: 0; } }
        @endif
    </style>
</head>
<body>
@if($preview)<div class="kertas">@endif

    <div class="header">
        <h1>{{ strtoupper(config('app.name')) }}</h1>
        <p>Laporan Penjualan</p>
        <p>
            @if($dari || $sampai)
                Periode:
                {{ $dari ? \Illuminate\Support\Carbon::parse($dari)->translatedFormat('d F Y') : 'awal' }}
                s/d
                {{ $sampai ? \Illuminate\Support\Carbon::parse($sampai)->translatedFormat('d F Y') : 'sekarang' }}
            @else
                Semua periode
            @endif
        </p>
    </div>

    <table class="ringkasan">
        <tr>
            <td><div class="label">Total Pesanan</div><div class="nilai">{{ $totalPesanan }}</div></td>
            <td><div class="label">Pesanan Selesai</div><div class="nilai">{{ $pesananSelesai }}</div></td>
            <td><div class="label">Pendapatan (selesai)</div><div class="nilai">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th>Pelanggan</th>
                <th>Menu</th>
                <th class="tengah">Jumlah</th>
                <th class="kanan">Total</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesanan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_pelanggan }}</td>
                    <td>{{ $item->menu }}</td>
                    <td class="tengah">{{ $item->jumlah }}</td>
                    <td class="kanan">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->created_at->translatedFormat('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="tengah">Tidak ada data pesanan pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} WITA</div>

@if($preview)
</div>
<div class="aksi">
    <a class="unduh" href="{{ route('laporan.download', request()->query()) }}">Unduh PDF</a>
    <button class="cetak" onclick="window.print()">Cetak</button>
</div>
@endif
</body>
</html>
