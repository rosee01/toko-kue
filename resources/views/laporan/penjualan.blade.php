@extends('layouts.template')

@section('page-title', 'Laporan Penjualan')
@section('tanpa-judul', true)

@section('content')
<style>
    .report-page { padding:10px 0 24px; color:#3f302b; }
    .report-heading { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin:0 2px 16px; }
    .report-heading-copy { display:flex; align-items:flex-start; gap:12px; }
    .report-heading-icon { display:grid; place-items:center; width:42px; height:42px; flex:none; border:1px solid #efdfd5; border-radius:12px; background:#fff; color:#a96d5c; font-size:1.15rem; }
    .report-eyebrow { margin:0 0 3px; color:#a07c6b; font-size:.62rem; font-weight:600; letter-spacing:.1em; text-transform:uppercase; }
    .report-heading h1 { margin:0; color:#50392f; font:600 1.48rem 'Playfair Display',serif; }
    .report-heading p { margin:4px 0 0; color:#89786e; font-size:.7rem; }
    .report-actions { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:7px; }
    .report-action { display:inline-flex; align-items:center; justify-content:center; gap:6px; min-height:34px; padding:7px 11px; border:1px solid #e9ddd6; border-radius:7px; background:#fff; color:#705447; font-size:.66rem; font-weight:500; text-decoration:none; transition:background .15s ease,border-color .15s ease; }
    .report-action:hover { border-color:#d8c3b7; background:#fbf7f4; color:#563c30; }
    .report-action.primary { border-color:#7b5343; background:#7b5343; color:#fff; }
    .report-action.primary:hover { border-color:#654337; background:#654337; color:#fff; }
    .report-filter { margin-bottom:12px; padding:12px 14px; border:1px solid #efe4dd; border-radius:9px; background:#fff; box-shadow:0 2px 8px rgba(80,48,34,.025); }
    .report-filter-form { display:grid; grid-template-columns:minmax(150px,1fr) minmax(150px,1fr) auto; align-items:end; gap:10px; }
    .report-field label { display:block; margin-bottom:4px; color:#76675f; font-size:.64rem; font-weight:500; }
    .report-field input { width:100%; height:34px; padding:6px 9px; border:1px solid #e9dfd9; border-radius:6px; background:#fff; color:#55463e; font:inherit; font-size:.67rem; }
    .report-field input:focus { border-color:#b88b76; outline:2px solid rgba(184,139,118,.15); }
    .report-filter-actions { display:flex; gap:6px; }
    .report-filter-actions .report-action { min-height:34px; cursor:pointer; font-family:inherit; }
    .report-filter-actions .apply { border-color:#7b5343; background:#7b5343; color:#fff; }
    .report-filter-actions .apply:hover { border-color:#654337; background:#654337; }
    .report-error { margin-top:4px; color:#c64f4b; font-size:.61rem; }
    .report-metrics { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:9px; margin-bottom:10px; }
    .report-metric { display:flex; align-items:center; gap:11px; min-width:0; min-height:88px; padding:13px; border:1px solid #efe4dd; border-radius:9px; background:#fff; box-shadow:0 2px 8px rgba(80,48,34,.025); }
    .report-metric-icon { display:grid; place-items:center; width:37px; height:37px; flex:none; border-radius:10px; font-size:.94rem; }
    .report-metric-icon.revenue { background:#eaf4ec; color:#4d8e59; }
    .report-metric-icon.orders { background:#fbe9ea; color:#bb6470; }
    .report-metric-icon.completed { background:#fff3da; color:#bd8b2c; }
    .report-metric-icon.products { background:#f1ecf8; color:#866bb6; }
    .report-metric-copy { min-width:0; }
    .report-metric-label { color:#83736a; font-size:.62rem; line-height:1.4; }
    .report-metric-value { overflow:hidden; margin-top:2px; color:#3f302b; font-size:1.02rem; font-weight:700; text-overflow:ellipsis; white-space:nowrap; }
    .report-metric-value.currency { font-size:.9rem; }
    .report-metric-note { margin-top:2px; color:#a09289; font-size:.56rem; }
    .report-analytics { display:grid; grid-template-columns:minmax(0,1.5fr) minmax(230px,.85fr); gap:10px; margin-bottom:10px; }
    .report-card { min-width:0; border:1px solid #efe4dd; border-radius:9px; background:#fff; box-shadow:0 2px 8px rgba(80,48,34,.025); }
    .report-card-head { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; padding:12px 14px 0; }
    .report-card-title { margin:0; color:#5c4238; font-size:.77rem; font-weight:600; }
    .report-card-subtitle { margin:3px 0 0; color:#958880; font-size:.59rem; }
    .report-card-body { padding:10px 14px 13px; }
    .report-chart-wrap { position:relative; height:210px; }
    .report-side-cards { display:grid; grid-template-rows:1fr 1.2fr; gap:10px; }
    .report-status-row { display:flex; justify-content:space-between; align-items:center; gap:8px; padding:6px 0; border-bottom:1px solid #f3ece8; color:#76675f; font-size:.63rem; }
    .report-status-row:last-child { border-bottom:0; }
    .report-status-name { display:flex; align-items:center; gap:7px; }
    .report-status-dot { width:7px; height:7px; border-radius:50%; background:#d9a13b; }
    .report-status-dot.done { background:#6eaa76; }
    .report-status-dot.cancelled { background:#d87970; }
    .report-status-row strong { color:#55463e; font-weight:600; }
    .report-average { display:flex; justify-content:space-between; align-items:center; margin-top:8px; padding-top:8px; border-top:1px solid #f1e9e4; color:#76675f; font-size:.61rem; }
    .report-average strong { color:#61483c; font-size:.71rem; }
    .report-product-row { display:grid; grid-template-columns:23px minmax(0,1fr) auto; align-items:center; gap:7px; padding:6px 0; border-bottom:1px solid #f3ece8; }
    .report-product-row:last-child { border-bottom:0; }
    .report-product-rank { display:grid; place-items:center; width:21px; height:21px; border-radius:50%; background:#f6eee8; color:#805a47; font-size:.57rem; font-weight:600; }
    .report-product-name { overflow:hidden; color:#55463e; font-size:.62rem; text-overflow:ellipsis; white-space:nowrap; }
    .report-product-meta { color:#958880; font-size:.55rem; white-space:nowrap; }
    .report-empty { padding:12px 4px; color:#95877f; font-size:.62rem; text-align:center; }
    .report-table-card { overflow:hidden; }
    .report-table-wrap { padding:0 12px 10px; }
    .report-table { margin:0 !important; border-collapse:separate; border-spacing:0; }
    .report-table thead th { padding:8px 7px !important; border-top:1px solid #eee5df !important; border-bottom:1px solid #e9dfd9 !important; background:#faf7f5 !important; color:#70594c !important; font-size:.59rem !important; font-weight:600 !important; white-space:nowrap; }
    .report-table thead th:first-child { border-left:1px solid #eee5df !important; border-radius:6px 0 0 0; }
    .report-table thead th:last-child { border-right:1px solid #eee5df !important; border-radius:0 6px 0 0; }
    .report-table tbody td { padding:8px 7px !important; border-color:#f0e9e4 !important; color:#55463e !important; font-size:.61rem !important; vertical-align:middle !important; }
    .report-table tbody tr:hover td { background:#fcf9f7; }
    .report-table .customer-name { color:#49382f; font-weight:600; }
    .report-table-wrap .dataTables_wrapper { font-size:.6rem; }
    .report-table-wrap .dataTables_filter,.report-table-wrap .dataTables_length { display:none; }
    .report-table-wrap .dataTables_info { padding-top:8px !important; color:#8b7d74; font-size:.56rem; }
    .report-table-wrap .dataTables_paginate { padding-top:4px !important; }
    .report-table-wrap .pagination { gap:3px; }
    .report-table-wrap .pagination .page-link { padding:3px 7px; border:1px solid #eee4de; border-radius:5px !important; color:#70594c; font-size:.58rem; }
    .report-table-wrap .page-item.active .page-link { border-color:#7b5343; background:#7b5343; color:#fff; }
    @media (max-width:1000px) {
        .report-metrics { grid-template-columns:repeat(2,minmax(0,1fr)); }
    }
    @media (max-width:760px) {
        .report-heading { flex-direction:column; }
        .report-actions { justify-content:flex-start; }
        .report-filter-form { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .report-filter-actions { grid-column:1 / -1; }
        .report-analytics { grid-template-columns:minmax(0,1fr); }
        .report-side-cards { grid-template-columns:repeat(2,minmax(0,1fr)); grid-template-rows:auto; }
        .report-chart-wrap { height:190px; }
    }
    @media (max-width:520px) {
        .report-heading-copy { gap:9px; }
        .report-heading h1 { font-size:1.3rem; }
        .report-filter-form { grid-template-columns:minmax(0,1fr); }
        .report-filter-actions { grid-column:auto; }
        .report-filter-actions .report-action { flex:1; }
        .report-metrics { gap:7px; }
        .report-metric { min-height:78px; gap:8px; padding:10px; }
        .report-metric-icon { width:32px; height:32px; }
        .report-metric-value { font-size:.91rem; }
        .report-metric-value.currency { font-size:.76rem; }
        .report-side-cards { grid-template-columns:minmax(0,1fr); }
        .report-table-wrap { padding:0 8px 8px; }
    }
</style>

<div class="report-page">
    <header class="report-heading">
        <div class="report-heading-copy">
            <span class="report-heading-icon" aria-hidden="true"><i class="bi bi-bar-chart-line"></i></span>
            <div>
                <div class="report-eyebrow">Analitik toko</div>
                <h1>Laporan Penjualan</h1>
                <p>
                    @if($dari || $sampai)
                        Periode
                        {{ $dari ? \Illuminate\Support\Carbon::parse($dari)->translatedFormat('d M Y') : 'awal' }}
                        &ndash;
                        {{ $sampai ? \Illuminate\Support\Carbon::parse($sampai)->translatedFormat('d M Y') : 'sekarang' }}
                    @else
                        Ringkasan seluruh periode penjualan
                    @endif
                </p>
            </div>
        </div>
        <div class="report-actions">
            <a class="report-action" href="{{ route('laporan.preview', request()->query()) }}" target="_blank" rel="noopener">
                <i class="bi bi-eye"></i> Preview
            </a>
            <a class="report-action primary" href="{{ route('laporan.download', request()->query()) }}">
                <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
            </a>
        </div>
    </header>

    <section class="report-filter" aria-label="Filter periode laporan">
        <form method="GET" action="{{ route('laporan.penjualan') }}" class="report-filter-form">
            <div class="report-field">
                <label for="dari">Dari tanggal</label>
                <input type="date" id="dari" name="dari" value="{{ $dari }}" max="{{ $sampai }}">
                @error('dari')<div class="report-error">{{ $message }}</div>@enderror
            </div>
            <div class="report-field">
                <label for="sampai">Sampai tanggal</label>
                <input type="date" id="sampai" name="sampai" value="{{ $sampai }}" min="{{ $dari }}">
                @error('sampai')<div class="report-error">{{ $message }}</div>@enderror
            </div>
            <div class="report-filter-actions">
                <button type="submit" class="report-action apply"><i class="bi bi-funnel"></i> Terapkan</button>
                <a class="report-action" href="{{ route('laporan.penjualan') }}">Reset</a>
            </div>
        </form>
    </section>

    <section class="report-metrics" aria-label="Ringkasan penjualan">
        <article class="report-metric">
            <span class="report-metric-icon revenue"><i class="bi bi-cash-stack"></i></span>
            <div class="report-metric-copy">
                <div class="report-metric-label">Pendapatan pesanan selesai</div>
                <div class="report-metric-value currency">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                <div class="report-metric-note">Dari transaksi yang tuntas</div>
            </div>
        </article>
        <article class="report-metric">
            <span class="report-metric-icon orders"><i class="bi bi-receipt-cutoff"></i></span>
            <div class="report-metric-copy">
                <div class="report-metric-label">Total pesanan</div>
                <div class="report-metric-value">{{ number_format($totalPesanan, 0, ',', '.') }}</div>
                <div class="report-metric-note">Semua status</div>
            </div>
        </article>
        <article class="report-metric">
            <span class="report-metric-icon completed"><i class="bi bi-check2-circle"></i></span>
            <div class="report-metric-copy">
                <div class="report-metric-label">Pesanan selesai</div>
                <div class="report-metric-value">{{ number_format($pesananSelesai, 0, ',', '.') }}</div>
                <div class="report-metric-note">Transaksi berhasil</div>
            </div>
        </article>
        <article class="report-metric">
            <span class="report-metric-icon products"><i class="bi bi-box-seam"></i></span>
            <div class="report-metric-copy">
                <div class="report-metric-label">Produk terjual</div>
                <div class="report-metric-value">{{ number_format($produkTerjual, 0, ',', '.') }}</div>
                <div class="report-metric-note">Dari pesanan selesai</div>
            </div>
        </article>
    </section>

    <section class="report-analytics" aria-label="Analisis penjualan">
        <article class="report-card">
            <div class="report-card-head">
                <div>
                    <h2 class="report-card-title">Tren pendapatan</h2>
                    <p class="report-card-subtitle">Pendapatan harian dari pesanan yang selesai</p>
                </div>
                <i class="bi bi-graph-up-arrow" aria-hidden="true" style="color:#bd8170"></i>
            </div>
            <div class="report-card-body">
                <div class="report-chart-wrap">
                    <canvas id="reportSalesChart" role="img" aria-label="Grafik tren pendapatan harian"></canvas>
                </div>
            </div>
        </article>

        <div class="report-side-cards">
            <article class="report-card">
                <div class="report-card-head">
                    <div>
                        <h2 class="report-card-title">Status pesanan</h2>
                        <p class="report-card-subtitle">Rangkuman berdasarkan checkout</p>
                    </div>
                </div>
                <div class="report-card-body">
                    <div class="report-status-row"><span class="report-status-name"><span class="report-status-dot done"></span>Selesai</span><strong>{{ number_format($pesananSelesai, 0, ',', '.') }}</strong></div>
                    <div class="report-status-row"><span class="report-status-name"><span class="report-status-dot"></span>Menunggu</span><strong>{{ number_format($pesananMenunggu, 0, ',', '.') }}</strong></div>
                    <div class="report-status-row"><span class="report-status-name"><span class="report-status-dot cancelled"></span>Dibatalkan</span><strong>{{ number_format($pesananDibatalkan, 0, ',', '.') }}</strong></div>
                    <div class="report-average"><span>Rata-rata transaksi selesai</span><strong>Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</strong></div>
                </div>
            </article>

            <article class="report-card">
                <div class="report-card-head">
                    <div>
                        <h2 class="report-card-title">Produk terlaris</h2>
                        <p class="report-card-subtitle">Produk terjual terbanyak pada periode ini</p>
                    </div>
                </div>
                <div class="report-card-body">
                    @forelse($produkTerlaris as $produk)
                        <div class="report-product-row">
                            <span class="report-product-rank">{{ $loop->iteration }}</span>
                            <span class="report-product-name">{{ $produk->menu }}</span>
                            <span class="report-product-meta">{{ number_format($produk->jumlah, 0, ',', '.') }} terjual</span>
                        </div>
                    @empty
                        <div class="report-empty">Belum ada produk terjual pada periode ini.</div>
                    @endforelse
                </div>
            </article>
        </div>
    </section>

    <section class="report-card report-table-card" aria-label="Rincian pesanan">
        <div class="report-card-head">
            <div>
                <h2 class="report-card-title">Rincian pesanan</h2>
                <p class="report-card-subtitle">Daftar transaksi dalam periode yang dipilih</p>
            </div>
            <span class="report-card-subtitle">{{ number_format($pesanan->count(), 0, ',', '.') }} item</span>
        </div>
        <div class="report-card-body report-table-wrap">
            <div class="table-responsive">
                <table class="table table-hover datatable report-table w-100">
                    <thead>
                        <tr>
                            <th style="width:45px">No</th>
                            <th>Pelanggan</th>
                            <th>Menu</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesanan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="customer-name">{{ $item->nama_pelanggan }}</td>
                                <td>{{ $item->menu }}</td>
                                <td class="text-center">{{ $item->jumlah }}</td>
                                <td class="text-end" data-order="{{ $item->total }}">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td>@include('partials.status', ['status' => $item->status])</td>
                                <td data-order="{{ $item->created_at->timestamp }}">{{ $item->created_at->translatedFormat('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada pesanan pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const reportCanvas = document.getElementById('reportSalesChart');

    if (reportCanvas) {
        new Chart(reportCanvas, {
            type: 'line',
            data: {
                labels: @json($labelsGrafik),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($nilaiGrafik),
                    borderColor: '#b87362',
                    backgroundColor: 'rgba(184, 115, 98, .12)',
                    borderWidth: 2,
                    fill: true,
                    tension: .35,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#b87362',
                }],
            },
            options: {
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => ` Rp ${new Intl.NumberFormat('id-ID').format(context.parsed.y)}`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#958880', maxRotation: 0, autoSkip: true, font: { size: 10 } },
                        border: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3ece8' },
                        ticks: {
                            color: '#958880',
                            font: { size: 10 },
                            callback: (value) => `Rp ${new Intl.NumberFormat('id-ID', { notation: 'compact', maximumFractionDigits: 1 }).format(value)}`,
                        },
                        border: { display: false },
                    },
                },
            },
        });
    }
</script>
@endsection
