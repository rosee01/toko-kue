@extends('layouts.template')

@section('page-title', 'Dashboard')
@section('tanpa-judul', true)

@section('content')

<style>
    .dashboard-wrap { padding: 12px 0 24px; }
    .dashboard-intro { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:18px; }
    .dashboard-intro h1 { margin:0; color:#694b3f; font:600 1.55rem 'Playfair Display',serif; }
    .dashboard-intro p { margin:3px 0 0; color:#8a7a73; font-size:.78rem; }
    .dashboard-date { color:#80675c; text-align:right; font-size:.72rem; }
    .dashboard-grid { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:10px; margin-bottom:12px; }
    .dashboard-card { min-width:0; background:#fff; border:1px solid #efe4dd; border-radius:9px; padding:14px; box-shadow:0 2px 8px rgba(80,48,34,.025); }
    .metric-card { display:flex; align-items:center; gap:10px; min-height:94px; }
    .metric-icon { flex:0 0 40px; width:40px; height:40px; display:grid; place-items:center; border-radius:50%; font-size:1rem; }
    .metric-icon.coral { color:#df747d; background:#fbe9ea; }
    .metric-icon.green { color:#44a65c; background:#eaf6ec; }
    .metric-icon.violet { color:#8b70c5; background:#f1ecf8; }
    .metric-icon.gold { color:#d29a25; background:#fff3da; }
    .metric-icon.rose { color:#bc6b9b; background:#f7eaf3; }
    .metric-label { color:#76675f; font-size:.67rem; line-height:1.3; }
    .metric-value { color:#332922; font-size:1.18rem; font-weight:700; line-height:1.45; white-space:nowrap; }
    .metric-note { color:#43a35a; font-size:.62rem; }
    .dashboard-row { display:grid; gap:10px; margin-bottom:10px; }
    .dashboard-row-top { grid-template-columns:minmax(0,1.45fr) minmax(220px,.9fr) minmax(220px,.95fr); }
    .dashboard-row-bottom { grid-template-columns:minmax(0,1.45fr) minmax(210px,.78fr) minmax(220px,.92fr); }
    .dashboard-card-head { display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:10px; }
    .dashboard-card-title { display:flex; align-items:center; gap:8px; margin:0; color:#5c4238; font-size:.83rem; font-weight:600; }
    .dashboard-card-title i { color:#bb796d; font-size:.95rem; }
    .dashboard-subtitle { color:#8a7a73; font-size:.64rem; margin-top:-6px; margin-bottom:7px; }
    .dashboard-link { color:#c77c7b; font-size:.63rem; text-decoration:none; white-space:nowrap; }
    .dashboard-link:hover { color:#8b534a; }
    .chart-wrap { height:188px; }
    .category-wrap { height:188px; }
    .order-row { display:flex; align-items:center; gap:9px; padding:9px 0; border-bottom:1px solid #f3ece8; }
    .order-row:last-child { border-bottom:0; padding-bottom:0; }
    .order-thumb { width:36px; height:36px; flex:0 0 36px; display:grid; place-items:center; border-radius:7px; background:#f5ece6; color:#9b7160; font-size:1rem; }
    .order-content { flex:1; min-width:0; }
    .order-code { color:#78675e; font-size:.61rem; }
    .order-name { overflow:hidden; color:#4c3d35; font-size:.65rem; text-overflow:ellipsis; white-space:nowrap; }
    .order-total { color:#4b3b33; font-size:.62rem; }
    .order-time { color:#9a8d86; font-size:.58rem; white-space:nowrap; }
    .status-pill { display:inline-block; padding:3px 7px; border-radius:20px; font-size:.56rem; white-space:nowrap; }
    .status-pending { color:#bd5964; background:#fde8eb; }
    .status-diproses { color:#9a681a; background:#fff1d5; }
    .status-selesai { color:#43834d; background:#e4f5e7; }
    .attention-table { width:100%; border-collapse:collapse; }
    .attention-table th { padding:7px 5px; background:#faf6f3; color:#78675e; font-size:.58rem; font-weight:500; text-align:left; white-space:nowrap; }
    .attention-table td { padding:7px 5px; color:#5f5149; border-bottom:1px solid #f3ece8; font-size:.59rem; white-space:nowrap; }
    .attention-table tr:last-child td { border-bottom:0; }
    .attention-table a { color:#775c4e; }
    .stock-row { display:grid; grid-template-columns:minmax(0,1fr) auto auto; gap:8px; align-items:center; padding:8px 0; border-bottom:1px solid #f3ece8; }
    .stock-row:last-child { border-bottom:0; }
    .stock-name { overflow:hidden; color:#55473f; font-size:.63rem; text-overflow:ellipsis; white-space:nowrap; }
    .stock-amount { color:#82736b; font-size:.6rem; white-space:nowrap; }
    .stock-pill { border-radius:20px; padding:3px 7px; color:#bd5964; background:#fde8eb; font-size:.56rem; white-space:nowrap; }
    .stock-pill.safe { color:#43834d; background:#e4f5e7; }
    .notice-heading { display:flex; align-items:center; gap:7px; }
    .notice-count { display:grid; place-items:center; min-width:18px; height:18px; padding:0 5px; border-radius:10px; background:#fde8eb; color:#b85f69; font-size:.55rem; font-weight:600; }
    .notice-list { display:grid; gap:5px; }
    .notice-row { display:flex; align-items:center; gap:8px; min-width:0; padding:8px; border:1px solid #f3ece8; border-radius:7px; background:#fffdfc; text-decoration:none; transition:border-color .15s ease,background .15s ease; }
    .notice-row:hover { border-color:#e7d6cb; background:#fbf7f4; }
    .notice-row:last-child { border-bottom:1px solid #f3ece8; }
    .notice-icon { width:28px; height:28px; flex:0 0 28px; display:grid; place-items:center; border-radius:8px; color:#bd6670; background:#fdebed; font-size:.72rem; }
    .notice-icon.stock { color:#b17a2c; background:#fff2dc; }
    .notice-content { flex:1; min-width:0; }
    .notice-text { display:block; overflow:hidden; color:#56483f; font-size:.6rem; line-height:1.4; text-overflow:ellipsis; }
    .notice-detail { display:block; margin-top:2px; color:#9a8d86; font-size:.53rem; }
    .notice-time { color:#9a8d86; font-size:.53rem; white-space:nowrap; }
    .notice-arrow { color:#b6a69c; font-size:.62rem; }
    .empty-state { padding:22px 8px; color:#95877f; font-size:.68rem; text-align:center; }
    @media (max-width:1100px) {
        .dashboard-grid { grid-template-columns:repeat(3,minmax(0,1fr)); }
        .dashboard-row-top,.dashboard-row-bottom { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .dashboard-row-top > :first-child,.dashboard-row-bottom > :first-child { grid-column:span 2; }
    }
    @media (max-width:650px) {
        .dashboard-intro { align-items:flex-start; }
        .dashboard-date { max-width:115px; }
        .dashboard-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .dashboard-row-top,.dashboard-row-bottom { grid-template-columns:minmax(0,1fr); }
        .dashboard-row-top > :first-child,.dashboard-row-bottom > :first-child { grid-column:auto; }
        .dashboard-card { padding:12px; }
        .chart-wrap,.category-wrap { height:175px; }
    }
</style>

<div class="dashboard-wrap">
    <header class="dashboard-intro">
        <div>
            <h1>Dashboard <span aria-hidden="true">👋🏻</span></h1>
            <p>Selamat datang kembali, {{ auth()->user()->name }}!</p>
        </div>
        <div class="dashboard-date"><i class="bi bi-calendar3 me-1"></i> {{ now()->translatedFormat('l, d F Y') }}<br>{{ now()->format('H:i') }} WIB</div>
    </header>

    <section class="dashboard-grid" aria-label="Ringkasan toko">
        <article class="dashboard-card metric-card"><span class="metric-icon coral"><i class="bi bi-receipt-cutoff"></i></span><div><div class="metric-label">Total Pesanan Hari Ini</div><div class="metric-value">{{ $pesananHariIni }}</div><div class="metric-note">Pesanan masuk hari ini</div></div></article>
        <article class="dashboard-card metric-card"><span class="metric-icon green"><i class="bi bi-clock-history"></i></span><div><div class="metric-label">Pesanan Diproses</div><div class="metric-value">{{ $pesananDiproses }}</div><div class="metric-note">Sedang disiapkan</div></div></article>
        <article class="dashboard-card metric-card"><span class="metric-icon violet"><i class="bi bi-box-seam"></i></span><div><div class="metric-label">Stok Menipis</div><div class="metric-value">{{ $stokMenipis }}</div><div class="metric-note" style="color:#c78b33">Perlu perhatian</div></div></article>
        <article class="dashboard-card metric-card"><span class="metric-icon gold"><i class="bi bi-check2-circle"></i></span><div><div class="metric-label">Pesanan Selesai Hari Ini</div><div class="metric-value">{{ $pesananSelesaiHariIni }}</div><div class="metric-note">Tuntas hari ini</div></div></article>
        <article class="dashboard-card metric-card"><span class="metric-icon rose"><i class="bi bi-cash-stack"></i></span><div><div class="metric-label">Pendapatan Hari Ini</div><div class="metric-value" style="font-size:1rem">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</div><div class="metric-note">Dari pesanan selesai</div></div></article>
    </section>

    <section class="dashboard-row dashboard-row-top">
        <article class="dashboard-card">
            <div class="dashboard-card-head"><div><h2 class="dashboard-card-title"><i class="bi bi-graph-up-arrow"></i> Grafik Penjualan</h2><div class="dashboard-subtitle">Penjualan pesanan selesai, 7 hari terakhir</div></div><a class="dashboard-link" href="{{ route('laporan.penjualan') }}">Lihat laporan <i class="bi bi-arrow-right"></i></a></div>
            <div class="chart-wrap"><canvas id="salesChart" aria-label="Grafik penjualan tujuh hari terakhir"></canvas></div>
        </article>
        <article class="dashboard-card">
            <div class="dashboard-card-head"><h2 class="dashboard-card-title"><i class="bi bi-pie-chart"></i> Penjualan per Kategori</h2></div>
            <div class="category-wrap"><canvas id="categoryChart" aria-label="Diagram penjualan per kategori"></canvas></div>
        </article>
        <article class="dashboard-card">
            <div class="dashboard-card-head"><h2 class="dashboard-card-title"><i class="bi bi-clipboard-data"></i> Pesanan Terbaru</h2><a class="dashboard-link" href="{{ route('pesanan.index') }}">Lihat Semua <i class="bi bi-arrow-right"></i></a></div>
            @forelse($pesananTerbaru as $pesanan)
                <div class="order-row">
                    <span class="order-thumb"><i class="bi bi-cake2"></i></span>
                    <div class="order-content"><div class="order-code">#{{ $pesanan->kode_pesanan ?: str_pad($pesanan->id, 6, '0', STR_PAD_LEFT) }}</div><div class="order-name">{{ $pesanan->nama_pelanggan }}</div><div class="order-total">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</div></div>
                    <div class="text-end"><span class="status-pill status-{{ strtolower($pesanan->status) }}">{{ $pesanan->status }}</span><div class="order-time mt-1">{{ $pesanan->created_at?->format('H:i') }}</div></div>
                </div>
            @empty
                <div class="empty-state">Belum ada pesanan.</div>
            @endforelse
        </article>
    </section>

    <section class="dashboard-row dashboard-row-bottom">
        <article class="dashboard-card">
            <div class="dashboard-card-head"><h2 class="dashboard-card-title"><i class="bi bi-exclamation-triangle"></i> Pesanan Perlu Perhatian</h2><a class="dashboard-link" href="{{ route('pesanan.index') }}">Kelola <i class="bi bi-arrow-right"></i></a></div>
            <div class="table-responsive"><table class="attention-table"><thead><tr><th>No. Pesanan</th><th>Nama Pelanggan</th><th>Total</th><th>Status</th><th>Waktu</th><th>Aksi</th></tr></thead><tbody>
                @forelse($pesananPerluPerhatian as $pesanan)
                    <tr><td><a href="{{ route('pesanan.edit', $pesanan) }}">#{{ $pesanan->kode_pesanan ?: str_pad($pesanan->id, 6, '0', STR_PAD_LEFT) }}</a></td><td>{{ $pesanan->nama_pelanggan }}</td><td>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td><td><span class="status-pill status-pending">{{ $pesanan->status }}</span></td><td>{{ $pesanan->created_at?->format('H:i') }}</td><td><a href="{{ route('pesanan.edit', $pesanan) }}" aria-label="Lihat pesanan">Lihat</a></td></tr>
                @empty
                    <tr><td colspan="6" class="empty-state">Tidak ada pesanan yang menunggu perhatian.</td></tr>
                @endforelse
            </tbody></table></div>
        </article>
        <article class="dashboard-card">
            <div class="dashboard-card-head"><h2 class="dashboard-card-title"><i class="bi bi-box2-heart"></i> Stok Menipis</h2><a class="dashboard-link" href="{{ route('produk.index') }}">Lihat Produk <i class="bi bi-arrow-right"></i></a></div>
            @forelse($produkStokMenipis as $produk)
                <div class="stock-row"><span class="stock-name">{{ $produk->name_produk }}</span><span class="stock-amount">Stok</span><span class="stock-pill {{ $produk->stok > $produk->stok_minimum ? 'safe' : '' }}">{{ $produk->stok }} pcs</span></div>
            @empty
                <div class="empty-state">Semua stok dalam kondisi aman.</div>
            @endforelse
        </article>
        @php
            $notifikasiPesanan = $pesananTerbaru->where('status', \App\Models\Pesanan::STATUS_PENDING)->take(3);
            $notifikasiStok = $produkStokMenipis->take(3);
            $jumlahNotifikasi = $notifikasiPesanan->count() + $notifikasiStok->count();
        @endphp
        <article class="dashboard-card" id="notifikasi" aria-label="Notifikasi dan hal yang perlu ditindaklanjuti">
            <div class="dashboard-card-head">
                <div class="notice-heading">
                    <h2 class="dashboard-card-title"><i class="bi bi-bell"></i> Notifikasi</h2>
                    @if($jumlahNotifikasi > 0)<span class="notice-count">{{ $jumlahNotifikasi }}</span>@endif
                </div>
                <a class="dashboard-link" href="{{ route('pesanan.index') }}">Pesanan <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="notice-list">
                @foreach($notifikasiPesanan as $pesanan)
                    <a class="notice-row" href="{{ route('pesanan.edit', $pesanan) }}">
                        <span class="notice-icon"><i class="bi bi-bag-plus"></i></span>
                        <span class="notice-content">
                            <span class="notice-text">Pesanan baru dari {{ $pesanan->nama_pelanggan }}</span>
                            <span class="notice-detail">#{{ $pesanan->kode_pesanan ?: str_pad($pesanan->id, 6, '0', STR_PAD_LEFT) }} &middot; Perlu diproses</span>
                        </span>
                        <span class="notice-time">{{ $pesanan->created_at?->format('H:i') }}</span>
                        <i class="notice-arrow bi bi-chevron-right" aria-hidden="true"></i>
                    </a>
                @endforeach
                @foreach($notifikasiStok as $produk)
                    <a class="notice-row" href="{{ route('produk.index') }}">
                        <span class="notice-icon stock"><i class="bi bi-exclamation-triangle"></i></span>
                        <span class="notice-content">
                            <span class="notice-text">Stok {{ $produk->name_produk }} menipis</span>
                            <span class="notice-detail">Tersisa {{ $produk->stok }} pcs &middot; Perlu restok</span>
                        </span>
                        <span class="notice-time">Stok</span>
                        <i class="notice-arrow bi bi-chevron-right" aria-hidden="true"></i>
                    </a>
                @endforeach
                @if($jumlahNotifikasi === 0)
                    <div class="empty-state"><i class="bi bi-check2-circle d-block mb-1"></i>Semua aman, belum ada notifikasi baru.</div>
                @endif
            </div>
        </article>
    </section>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Grafik penjualan tujuh hari terakhir.
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Penjualan',

                data: {!! json_encode($dataPenjualan) !!},
                borderColor: '#ee7f88',
                backgroundColor: 'rgba(238, 127, 136, 0.12)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#ee7f88',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f0e8e3' }, ticks: { font: { size: 9 }, callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); } } },
                x: { grid: { display: false }, ticks: { font: { size: 9 } } }
            }
        }
    });

    // Diagram penjualan per kategori.
    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($kategoriLabels) !!},
            datasets: [{
                data: {!! json_encode($kategoriDataValues) !!},
                backgroundColor: ['#b58978', '#ef9c72', '#f1cc76', '#8dcea0', '#98b5dc', '#b283cf'],
                borderWidth: 0,
                hoverOffset: 4

            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: { usePointStyle: true, padding: 9, boxWidth: 7, font: { size: 9 } }
                }
            }
        }
    });
</script>
@endsection