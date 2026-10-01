@extends('layouts.template')

@section('page-title', 'Riwayat Stok')
@section('tanpa-judul', true)

@section('content')
<style>
    .history-page { padding:10px 0 24px; color:#3f302b; }
    .history-heading { display:flex; justify-content:space-between; align-items:flex-end; gap:16px; margin:0 4px 15px; }
    .history-heading-main { display:flex; align-items:flex-start; gap:11px; }
    .history-breadcrumb { margin-bottom:5px; color:#9a887e; font-size:.62rem; }
    .history-breadcrumb a { color:#997867; text-decoration:none; }
    .history-breadcrumb i { margin:0 5px; color:#b29b8d; font-size:.55rem; }
    .history-heading-icon { color:#8b6250; font-size:1.65rem; line-height:1.2; }
    .history-heading h1 { margin:0; color:#50392f; font:600 1.48rem 'Playfair Display',serif; }
    .history-heading p { margin:3px 0 0; color:#89786e; font-size:.68rem; }
    .history-back { display:inline-flex; align-items:center; gap:6px; min-height:32px; padding:6px 10px; border:1px solid #e8dcd4; border-radius:6px; color:#755a4b; font-size:.62rem; text-decoration:none; }
    .history-back:hover { background:#f8f1ed; color:#5f3f32; }
    .history-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:9px; margin-bottom:11px; }
    .history-stat { display:flex; align-items:center; gap:10px; min-width:0; padding:11px 13px; border:1px solid #efe4dd; border-radius:9px; background:#fff; }
    .history-stat-icon { display:grid; place-items:center; flex:0 0 38px; width:38px; height:38px; border-radius:50%; font-size:.95rem; }
    .history-stat-icon.brown { background:#f3e9e2; color:#8b6250; }
    .history-stat-icon.green { background:#e8f4e9; color:#4f9c61; }
    .history-stat-icon.coral { background:#fbe9ea; color:#df747d; }
    .history-stat-icon.amber { background:#fff1d9; color:#d3942e; }
    .history-stat-label { color:#81736b; font-size:.61rem; white-space:nowrap; }
    .history-stat-value { color:#352820; font-size:1.05rem; font-weight:700; line-height:1.3; }
    .history-card { padding:13px; border:1px solid #efe4dd; border-radius:9px; background:#fff; box-shadow:0 2px 9px rgba(80,48,34,.025); }
    .history-toolbar { display:flex; align-items:center; gap:9px; margin-bottom:11px; }
    .history-title { margin:0; color:#60483b; font:600 .82rem 'Playfair Display',serif; white-space:nowrap; }
    .history-search { position:relative; flex:1; min-width:160px; }
    .history-search i { position:absolute; top:50%; left:10px; color:#92847b; transform:translateY(-50%); }
    .history-search input,.history-filter { height:34px; border:1px solid #e9dfd9; border-radius:6px; background:#fff; color:#5f5149; font-size:.62rem; }
    .history-search input { width:100%; padding:6px 9px 6px 29px; }
    .history-filter { min-width:132px; padding:5px 23px 5px 9px; }
    .history-search input:focus,.history-filter:focus { border-color:#b88b76; outline:2px solid rgba(184,139,118,.15); }
    .history-table { margin:0 !important; border-collapse:separate; border-spacing:0; }
    .history-table thead th { padding:9px 8px !important; border-top:1px solid #eee5df !important; border-bottom:1px solid #e9dfd9 !important; background:#faf7f5 !important; color:#70594c !important; font-size:.61rem !important; font-weight:600 !important; white-space:nowrap; }
    .history-table thead th:first-child { border-left:1px solid #eee5df !important; border-radius:6px 0 0 0; }
    .history-table thead th:last-child { border-right:1px solid #eee5df !important; border-radius:0 6px 0 0; }
    .history-table tbody td { padding:8px !important; border-color:#f0e9e4 !important; color:#4d4038 !important; font-size:.63rem !important; vertical-align:middle !important; }
    .history-table tbody tr:hover td { background:#fcf9f7; }
    .history-number,.history-date { color:#8b7d74; white-space:nowrap; }
    .history-date small { display:block; color:#a0958e; font-size:.55rem; }
    .history-product { display:flex; align-items:center; gap:8px; min-width:150px; }
    .history-thumb { display:grid; place-items:center; width:34px; height:34px; flex:none; overflow:hidden; border-radius:6px; background:#f4ece6; color:#9a715d; }
    .history-thumb img { width:100%; height:100%; object-fit:cover; }
    .history-product-name { color:#49382f; font-size:.63rem; font-weight:600; }
    .history-product-category { color:#958880; font-size:.55rem; }
    .history-type { display:inline-block; padding:4px 8px; border-radius:20px; font-size:.56rem; white-space:nowrap; }
    .history-type.in { background:#e3f3e5; color:#468955; }
    .history-type.out { background:#fde6e5; color:#c64f4b; }
    .history-type.return { background:#fff0d7; color:#ad741f; }
    .history-change { font-weight:700; white-space:nowrap; }
    .history-change.in { color:#468955; }
    .history-change.out { color:#c64f4b; }
    .history-balance { color:#65564e; white-space:nowrap; }
    .history-source { color:#89786e; font-size:.6rem; }
    .history-empty { padding:36px 12px; color:#95877f; text-align:center; font-size:.7rem; }
    .history-empty i { display:block; margin-bottom:8px; color:#b99a88; font-size:1.5rem; }
    .history-table-wrap .dataTables_wrapper { font-size:.62rem; }
    .history-table-wrap .dataTables_filter,.history-table-wrap .dataTables_length { display:none; }
    .history-table-wrap .dataTables_info { padding-top:10px !important; color:#8b7d74; font-size:.58rem; }
    .history-table-wrap .dataTables_paginate { padding-top:5px !important; }
    .history-table-wrap .pagination { gap:3px; }
    .history-table-wrap .pagination .page-link { border:1px solid #eee4de; border-radius:5px !important; color:#70594c; font-size:.6rem; padding:4px 7px; }
    .history-table-wrap .page-item.active .page-link { border-color:#7b5343; background:#7b5343; color:#fff; }
    @media (max-width:800px) {
        .history-stats { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .history-toolbar { flex-wrap:wrap; }
        .history-title { width:100%; }
        .history-search { flex-basis:100%; }
        .history-filter { flex:1; }
    }
    @media (max-width:540px) {
        .history-page { padding-top:6px; }
        .history-heading { align-items:flex-start; flex-direction:column; }
        .history-heading h1 { font-size:1.3rem; }
        .history-stats { gap:6px; }
        .history-stat { gap:7px; padding:8px; }
        .history-stat-icon { flex-basis:30px; width:30px; height:30px; font-size:.8rem; }
        .history-card { padding:9px; }
    }
</style>

<div class="history-page">
    <header class="history-heading">
        <div class="history-heading-main">
            <i class="history-heading-icon bi bi-clock-history" aria-hidden="true"></i>
            <div>
                <div class="history-breadcrumb"><a href="{{ route('stok.index') }}"><i class="bi bi-house-door"></i> Persediaan</a><i class="bi bi-chevron-right"></i> Riwayat Stok</div>
                <h1>Riwayat Stok</h1>
                <p>Telusuri perubahan stok produk dan sumber perubahannya.</p>
            </div>
        </div>
        <a href="{{ route('stok.index') }}" class="history-back"><i class="bi bi-arrow-left"></i> Kembali ke Stok Produk</a>
    </header>

    <section class="history-stats" aria-label="Ringkasan riwayat stok">
        <article class="history-stat"><span class="history-stat-icon brown"><i class="bi bi-clock-history"></i></span><div><div class="history-stat-label">Total Perubahan</div><div class="history-stat-value">{{ $totalRiwayat }}</div></div></article>
        <article class="history-stat"><span class="history-stat-icon green"><i class="bi bi-box-arrow-in-down"></i></span><div><div class="history-stat-label">Total Stok Masuk</div><div class="history-stat-value">{{ $jumlahStokMasuk }} pcs</div></div></article>
        <article class="history-stat"><span class="history-stat-icon coral"><i class="bi bi-box-arrow-up"></i></span><div><div class="history-stat-label">Total Stok Keluar</div><div class="history-stat-value">{{ $jumlahStokKeluar }} pcs</div></div></article>
        <article class="history-stat"><span class="history-stat-icon amber"><i class="bi bi-calendar-check"></i></span><div><div class="history-stat-label">Perubahan Hari Ini</div><div class="history-stat-value">{{ $perubahanHariIni }}</div></div></article>
    </section>

    <section class="history-card history-table-wrap" aria-label="Daftar riwayat stok">
        <div class="history-toolbar">
            <h2 class="history-title">Semua Perubahan Stok</h2>
            <label class="history-search"><i class="bi bi-search" aria-hidden="true"></i><input id="history-search" type="search" placeholder="Cari produk, pesanan, atau catatan..." aria-label="Cari riwayat"></label>
            <select class="history-filter" id="history-type-filter" aria-label="Filter jenis perubahan"><option value="">Semua Perubahan</option><option value="Stok Masuk">Stok Masuk</option><option value="Stok Keluar">Stok Keluar</option><option value="Pengembalian">Pengembalian</option></select>
        </div>

        <div class="table-responsive">
            <table class="table history-table w-100" id="history-table">
                <thead><tr><th>No.</th><th>Tanggal &amp; Waktu</th><th>Produk</th><th>Jenis Perubahan</th><th>Jumlah</th><th>Stok Sebelum</th><th>Stok Sesudah</th><th>Sumber / Catatan</th></tr></thead>
                <tbody>
                    @foreach($riwayat as $item)
                        @php
                            $jenisMasuk = $item->jenis !== 'keluar';
                            $jenisLabel = match ($item->jenis) {
                                'manual' => 'Stok Masuk',
                                'keluar' => 'Stok Keluar',
                                'kembali' => 'Pengembalian',
                                default => 'Penyesuaian',
                            };
                            $jenisClass = $item->jenis === 'keluar' ? 'out' : ($item->jenis === 'kembali' ? 'return' : 'in');
                            $sumber = $item->pesanan?->kode_pesanan ? 'Pesanan #' . $item->pesanan->kode_pesanan : ($item->catatan ?: 'Perubahan stok');
                        @endphp
                        <tr>
                            <td class="history-number">{{ $loop->iteration }}</td>
                            <td class="history-date" data-order="{{ $item->created_at?->timestamp }}">{{ $item->created_at?->translatedFormat('d M Y') }}<small>{{ $item->created_at?->format('H:i') }}</small></td>
                            <td><div class="history-product"><span class="history-thumb">@if($item->produk?->foto)<img src="{{ asset('storage/' . $item->produk->foto) }}" alt="{{ $item->produk->name_produk }}">@else<i class="bi bi-cake2"></i>@endif</span><span><span class="history-product-name d-block">{{ $item->produk?->name_produk ?? 'Produk dihapus' }}</span><span class="history-product-category">{{ $item->produk?->kategori?->nama_kategori ?? '-' }}</span></span></div></td>
                            <td><span class="history-type {{ $jenisClass }}">{{ $jenisLabel }}</span></td>
                            <td data-order="{{ $jenisMasuk ? $item->jumlah : -$item->jumlah }}"><span class="history-change {{ $jenisMasuk ? 'in' : 'out' }}">{{ $jenisMasuk ? '+' : '-' }}{{ $item->jumlah }} pcs</span></td>
                            <td data-order="{{ $item->stok_sebelum }}">{{ $item->stok_sebelum }} pcs</td>
                            <td data-order="{{ $item->stok_sesudah }}">{{ $item->stok_sesudah }} pcs</td>
                            <td class="history-source">{{ $sumber }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        const table = $('#history-table').DataTable({
            responsive: false,
            pageLength: 10,
            autoWidth: false,
            order: [[1, 'desc']],
            language: {
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                zeroRecords: 'Data tidak ditemukan.',
                emptyTable: 'Belum ada perubahan stok.',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
            },
        });
        document.getElementById('history-search').addEventListener('input', (event) => table.search(event.currentTarget.value).draw());
        document.getElementById('history-type-filter').addEventListener('change', (event) => {
            const value = $.fn.dataTable.util.escapeRegex(event.currentTarget.value);
            table.column(3).search(value ? '^' + value + '$' : '', true, false).draw();
        });
    });
</script>
@endsection