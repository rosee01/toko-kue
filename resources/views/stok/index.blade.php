@extends('layouts.template')

@section('page-title', 'Stok Produk')
@section('tanpa-judul', true)

@section('content')
<style>
    .stok-page { padding:9px 0 22px; color:#3f302b; }
    .stok-heading { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin:0 4px 13px; }
    .stok-heading-main { display:flex; align-items:flex-start; gap:11px; }
    .stok-breadcrumb { margin-bottom:5px; color:#9a887e; font-size:.62rem; }
    .stok-breadcrumb i { margin:0 5px; color:#b29b8d; font-size:.55rem; }
    .stok-heading-icon { color:#8b6250; font-size:1.65rem; line-height:1.2; }
    .stok-heading h1 { margin:0; color:#50392f; font:600 1.48rem 'Playfair Display',serif; }
    .stok-heading p { margin:3px 0 0; color:#89786e; font-size:.68rem; }
    .stok-date { padding-top:8px; color:#80675c; text-align:right; font-size:.63rem; white-space:nowrap; }
    .stok-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:9px; margin-bottom:10px; }
    .stok-stat { display:flex; align-items:center; gap:10px; min-width:0; padding:10px 12px; border:1px solid #efe4dd; border-radius:9px; background:#fff; }
    .stok-stat-icon { display:grid; place-items:center; flex:0 0 38px; width:38px; height:38px; border-radius:50%; font-size:.95rem; }
    .stok-stat-icon.coral { background:#fbe9ea; color:#df747d; }
    .stok-stat-icon.green { background:#e8f4e9; color:#4f9c61; }
    .stok-stat-icon.amber { background:#fff1d9; color:#d3942e; }
    .stok-stat-icon.red { background:#fbe8e7; color:#d86d65; }
    .stok-stat-label { color:#81736b; font-size:.61rem; white-space:nowrap; }
    .stok-stat-value { color:#352820; font-size:1.05rem; font-weight:700; line-height:1.3; }
    .stok-content { display:grid; grid-template-columns:minmax(0,1fr) 218px; gap:10px; align-items:start; }
    .stok-card { min-width:0; padding:12px; border:1px solid #efe4dd; border-radius:9px; background:#fff; box-shadow:0 2px 9px rgba(80,48,34,.025); }
    .stok-toolbar { display:flex; align-items:center; gap:8px; margin-bottom:10px; }
    .stok-search { position:relative; flex:1; min-width:130px; }
    .stok-search i { position:absolute; top:50%; left:10px; color:#92847b; transform:translateY(-50%); }
    .stok-search input,.stok-filter { height:33px; border:1px solid #e9dfd9; border-radius:6px; background:#fff; color:#5f5149; font-size:.61rem; }
    .stok-search input { width:100%; padding:6px 9px 6px 29px; }
    .stok-filter { min-width:105px; padding:5px 22px 5px 8px; }
    .stok-search input:focus,.stok-filter:focus { border-color:#b88b76; outline:2px solid rgba(184,139,118,.15); }
    .stok-add-button { display:inline-flex; align-items:center; justify-content:center; gap:6px; min-height:33px; padding:6px 10px; border:1px solid #7b5343; border-radius:6px; background:#7b5343; color:#fff !important; font-size:.62rem; white-space:nowrap; }
    .stok-add-button:hover { background:#5f3f32; }
    .stok-table { margin:0 !important; border-collapse:separate; border-spacing:0; }
    .stok-table thead th { padding:8px 7px !important; border-top:1px solid #eee5df !important; border-bottom:1px solid #e9dfd9 !important; background:#faf7f5 !important; color:#70594c !important; font-size:.57rem !important; font-weight:600 !important; white-space:nowrap; }
    .stok-table thead th:first-child { border-left:1px solid #eee5df !important; border-radius:6px 0 0 0; }
    .stok-table thead th:last-child { border-right:1px solid #eee5df !important; border-radius:0 6px 0 0; }
    .stok-table tbody td { padding:6px 7px !important; border-color:#f0e9e4 !important; color:#4d4038 !important; font-size:.59rem !important; vertical-align:middle !important; }
    .stok-table tbody tr:hover td { background:#fcf9f7; }
    .stok-checkbox { width:13px; height:13px; accent-color:#7b5343; }
    .stok-thumb { display:grid; place-items:center; width:34px; height:34px; overflow:hidden; border-radius:6px; background:#f4ece6; color:#9a715d; }
    .stok-thumb img { width:100%; height:100%; object-fit:cover; }
    .stok-product-name { display:block; color:#49382f; font-size:.61rem; font-weight:600; white-space:nowrap; }
    .stok-category { color:#8c6653; }
    .stok-value { color:#59473d; font-weight:600; }
    .stok-value.empty { color:#d05e5e; }
    .stok-pill { display:inline-flex; align-items:center; justify-content:center; min-width:51px; padding:4px 7px; border-radius:20px; font-size:.55rem; white-space:nowrap; }
    .stok-pill.safe { background:#e3f3e5; color:#468955; }
    .stok-pill.low { background:#fff0d7; color:#ad741f; }
    .stok-pill.out { background:#fde6e5; color:#c64f4b; }
    .stok-actions { display:flex; justify-content:center; gap:3px; }
    .stok-icon-button { display:inline-grid; place-items:center; width:25px; height:25px; padding:0; border:1px solid #eadfd8; border-radius:5px; background:#fff; color:#80604e; font-size:.65rem; text-decoration:none; }
    .stok-icon-button:hover { border-color:#c9a995; background:#f8f1ed; color:#5f3f32; }
    .stok-icon-button.delete { color:#a4564e; }
    .stok-table-wrap .dataTables_wrapper { font-size:.59rem; }
    .stok-table-wrap .dataTables_filter,.stok-table-wrap .dataTables_length { display:none; }
    .stok-table-wrap .dataTables_info { padding-top:9px !important; color:#8b7d74; font-size:.57rem; }
    .stok-table-wrap .dataTables_paginate { padding-top:5px !important; }
    .stok-table-wrap .pagination { gap:3px; }
    .stok-table-wrap .pagination .page-link { border:1px solid #eee4de; border-radius:5px !important; color:#70594c; font-size:.6rem; padding:4px 7px; }
    .stok-table-wrap .page-item.active .page-link { border-color:#7b5343; background:#7b5343; color:#fff; }
    .stok-side { display:grid; gap:9px; }
    .stok-side-title { margin:0; color:#60483b; font-size:.68rem; font-weight:600; }
    .stok-side-head { display:flex; align-items:center; justify-content:space-between; gap:6px; margin-bottom:9px; }
    .stok-side-link { color:#c77c7b; font-size:.55rem; text-decoration:none; white-space:nowrap; }
    .stok-product-alert { display:flex; align-items:center; gap:7px; padding:6px 0; border-top:1px solid #f2ebe6; }
    .stok-product-alert:first-of-type { border-top:0; }
    .stok-alert-copy { flex:1; min-width:0; }
    .stok-alert-name { overflow:hidden; color:#57483f; font-size:.56rem; text-overflow:ellipsis; white-space:nowrap; }
    .stok-alert-detail { color:#968981; font-size:.51rem; }
    .stok-alert-pill { padding:3px 6px; border-radius:20px; font-size:.51rem; white-space:nowrap; }
    .stok-alert-pill.low { background:#fff0d7; color:#ad741f; }
    .stok-alert-pill.out { background:#fde6e5; color:#c64f4b; }
    .stok-warning { border-color:#f4dfe0; background:#fff4f4; }
    .stok-warning-title { display:flex; align-items:center; gap:6px; margin:0 0 6px; color:#d76c76; font-size:.68rem; font-weight:600; }
    .stok-warning-copy { margin:0 0 8px; color:#816b65; font-size:.55rem; line-height:1.5; }
    .stok-warning-button { display:flex; justify-content:flex-end; }
    .stok-warning-button button { padding:4px 8px; border:1px solid #e87984; border-radius:5px; background:#e87984; color:#fff; font-size:.55rem; }
    .stok-history-row { display:flex; align-items:flex-start; gap:7px; padding:6px 0; border-top:1px solid #f2ebe6; }
    .stok-history-row:first-of-type { border-top:0; }
    .stok-history-amount { min-width:31px; padding:3px 5px; border-radius:5px; font-size:.53rem; font-weight:600; text-align:center; }
    .stok-history-amount.in { background:#e3f3e5; color:#468955; }
    .stok-history-amount.out { background:#fde6e5; color:#c64f4b; }
    .stok-history-copy { min-width:0; flex:1; }
    .stok-history-name { overflow:hidden; color:#57483f; font-size:.55rem; text-overflow:ellipsis; white-space:nowrap; }
    .stok-history-reason,.stok-history-time { color:#968981; font-size:.49rem; line-height:1.4; }
    .stok-empty { padding:16px 5px; color:#95877f; font-size:.6rem; text-align:center; }
    .stok-modal-label { display:block; margin-bottom:5px; color:#5c483e; font-size:.66rem; font-weight:500; }
    .stok-modal-control { width:100%; min-height:36px; padding:7px 10px; border:1px solid #eadfd8; border-radius:6px; color:#4f4038; font-size:.68rem; }
    @media (max-width:1050px) {
        .stok-content { grid-template-columns:minmax(0,1fr); }
        .stok-side { grid-template-columns:repeat(3,minmax(0,1fr)); }
    }
    @media (max-width:760px) {
        .stok-stats { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .stok-toolbar { flex-wrap:wrap; }
        .stok-search { flex-basis:100%; }
        .stok-filter { flex:1; }
        .stok-add-button { margin-left:auto; }
        .stok-side { grid-template-columns:minmax(0,1fr); }
    }
    @media (max-width:540px) {
        .stok-page { padding-top:6px; }
        .stok-heading h1 { font-size:1.3rem; }
        .stok-heading { flex-direction:column; }
        .stok-date { padding:0; text-align:left; }
        .stok-stats { gap:6px; }
        .stok-stat { gap:7px; padding:8px; }
        .stok-stat-icon { flex-basis:30px; width:30px; height:30px; font-size:.8rem; }
        .stok-card { padding:9px; }
    }
</style>

<div class="stok-page">
    <header class="stok-heading">
        <div class="stok-heading-main">
            <i class="stok-heading-icon bi bi-box-seam" aria-hidden="true"></i>
            <div>
                <div class="stok-breadcrumb"><i class="bi bi-house-door"></i> Persediaan <i class="bi bi-chevron-right"></i> Stok Produk</div>
                <h1>Stok Produk</h1>
                <p>Pantau ketersediaan stok produk secara real-time.</p>
            </div>
        </div>
        <div class="stok-date"><i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l, d F Y') }}<br>{{ now()->format('H:i') }} WIB</div>
    </header>

    <section class="stok-stats" aria-label="Ringkasan stok">
        <article class="stok-stat"><span class="stok-stat-icon coral"><i class="bi bi-box-seam"></i></span><div><div class="stok-stat-label">Total Produk</div><div class="stok-stat-value">{{ $totalProduk }}</div></div></article>
        <article class="stok-stat"><span class="stok-stat-icon green"><i class="bi bi-shield-check"></i></span><div><div class="stok-stat-label">Stok Aman</div><div class="stok-stat-value">{{ $stokAman }}</div></div></article>
        <article class="stok-stat"><span class="stok-stat-icon amber"><i class="bi bi-exclamation-triangle"></i></span><div><div class="stok-stat-label">Stok Menipis</div><div class="stok-stat-value">{{ $stokMenipis }}</div></div></article>
        <article class="stok-stat"><span class="stok-stat-icon red"><i class="bi bi-exclamation-octagon"></i></span><div><div class="stok-stat-label">Stok Habis</div><div class="stok-stat-value">{{ $stokHabis }}</div></div></article>
    </section>

    <div class="stok-content">
        <section class="stok-card stok-table-wrap" aria-label="Daftar stok produk">
            <div class="stok-toolbar">
                <label class="stok-search"><i class="bi bi-search" aria-hidden="true"></i><input id="stok-search" type="search" placeholder="Cari produk..." aria-label="Cari produk"></label>
                <select class="stok-filter" id="stok-category-filter" aria-label="Filter kategori"><option value="">Semua Kategori</option>@foreach($kategoris as $kategori)<option value="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}</option>@endforeach</select>
                <select class="stok-filter" id="stok-status-filter" aria-label="Filter status"><option value="">Semua Status</option><option value="Aman">Aman</option><option value="Menipis">Menipis</option><option value="Habis">Habis</option><option value="Perhatian">Perhatian</option></select>
                <button type="button" class="stok-add-button" data-bs-toggle="modal" data-bs-target="#tambahStokModal"><i class="bi bi-plus-lg"></i> Tambah Stok</button>
            </div>

            <div class="table-responsive">
                <table class="table stok-table w-100" id="stok-table">
                    <thead><tr><th class="text-center"><input type="checkbox" class="stok-checkbox" id="stok-check-all" aria-label="Pilih semua produk"></th><th>No.</th><th>Foto</th><th>Nama Produk</th><th>Kategori</th><th>Stok Saat Ini</th><th>Stok Minimum</th><th>Status</th><th class="text-center">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($produks as $produk)
                            @php
                                $statusStok = $produk->stok === 0 ? 'Habis' : ($produk->stok <= $produk->stok_minimum ? 'Menipis' : 'Aman');
                                $kelasStok = ['Aman' => 'safe', 'Menipis' => 'low', 'Habis' => 'out'][$statusStok];
                            @endphp
                            <tr>
                                <td class="text-center"><input type="checkbox" class="stok-checkbox stok-row-check" aria-label="Pilih {{ $produk->name_produk }}"></td>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="stok-thumb">@if($produk->foto)<img src="{{ asset('storage/' . $produk->foto) }}" alt="{{ $produk->name_produk }}">@else<i class="bi bi-cake2"></i>@endif</span></td>
                                <td><span class="stok-product-name">{{ $produk->name_produk }}</span></td>
                                <td class="stok-category">{{ $produk->kategori->nama_kategori ?? '-' }}</td>
                                <td data-order="{{ $produk->stok }}"><span class="stok-value {{ $produk->stok === 0 ? 'empty' : '' }}">{{ $produk->stok }}</span></td>
                                <td data-order="{{ $produk->stok_minimum }}">{{ $produk->stok_minimum }}</td>
                                <td><span class="stok-pill {{ $kelasStok }}">{{ $statusStok }}</span></td>
                                <td><div class="stok-actions">
                                    <button type="button" class="stok-icon-button" title="Detail {{ $produk->name_produk }}" aria-label="Detail {{ $produk->name_produk }}" data-bs-toggle="modal" data-bs-target="#detailStok{{ $produk->id_produk }}"><i class="bi bi-eye"></i></button>
                                    <a href="{{ route('produk.edit', $produk) }}" class="stok-icon-button" title="Edit {{ $produk->name_produk }}" aria-label="Edit {{ $produk->name_produk }}"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('produk.destroy', $produk) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="button" class="stok-icon-button delete btn-delete" title="Hapus {{ $produk->name_produk }}" aria-label="Hapus {{ $produk->name_produk }}"><i class="bi bi-trash3"></i></button></form>
                                </div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="stok-side">
            <section class="stok-card">
                <div class="stok-side-head"><h2 class="stok-side-title">Stok Menipis</h2><a class="stok-side-link" href="#stok-table" data-filter-status="Perhatian">Lihat Semua <i class="bi bi-arrow-right"></i></a></div>
                @forelse($produkPerhatian as $produk)
                    @php $produkHabis = $produk->stok === 0; @endphp
                    <div class="stok-product-alert"><span class="stok-thumb">@if($produk->foto)<img src="{{ asset('storage/' . $produk->foto) }}" alt="{{ $produk->name_produk }}">@else<i class="bi bi-cake2"></i>@endif</span><div class="stok-alert-copy"><div class="stok-alert-name">{{ $produk->name_produk }}</div><div class="stok-alert-detail">Stok: {{ $produk->stok }} (Min: {{ $produk->stok_minimum }})</div></div><span class="stok-alert-pill {{ $produkHabis ? 'out' : 'low' }}">{{ $produkHabis ? 'Habis' : 'Menipis' }}</span></div>
                @empty
                    <div class="stok-empty">Tidak ada stok menipis atau habis.</div>
                @endforelse
            </section>

            <section class="stok-card stok-warning">
                <h2 class="stok-warning-title"><i class="bi bi-exclamation-circle-fill"></i> Perhatian!</h2>
                @if($jumlahPerhatian > 0)
                    <p class="stok-warning-copy">Ada {{ $stokHabis }} produk yang stoknya habis dan {{ $stokMenipis }} produk yang stoknya menipis. Segera lakukan restok agar tidak kehabisan.</p>
                    <div class="stok-warning-button"><button type="button" data-filter-status="Perhatian">Lihat Detail</button></div>
                @else
                    <p class="stok-warning-copy mb-0">Semua stok produk dalam kondisi aman.</p>
                @endif
            </section>

            <section class="stok-card" id="riwayat-stok">
                <div class="stok-side-head"><h2 class="stok-side-title">Riwayat Stok Terbaru</h2><a class="stok-side-link" href="{{ $tampilkanSemuaRiwayat ? route('stok.index') . '#riwayat-stok' : route('stok.index', ['riwayat' => 1]) . '#riwayat-stok' }}">{{ $tampilkanSemuaRiwayat ? 'Ringkas' : 'Lihat Semua' }} <i class="bi bi-arrow-right"></i></a></div>
                @forelse($riwayatTerbaru as $riwayat)
                    @php $stokMasuk = $riwayat->jenis !== 'keluar'; @endphp
                    <div class="stok-history-row">
                        <span class="stok-history-amount {{ $stokMasuk ? 'in' : 'out' }}">{{ $stokMasuk ? '+' : '-' }} {{ $riwayat->jumlah }}</span>
                        <div class="stok-history-copy">
                            <div class="stok-history-name">{{ $riwayat->produk?->name_produk ?? 'Produk dihapus' }}</div>
                            <div class="stok-history-reason">{{ $riwayat->pesanan?->kode_pesanan ? 'Pesanan #' . $riwayat->pesanan->kode_pesanan : ($riwayat->catatan ?: 'Perubahan stok') }}</div>
                            <div class="stok-history-time">{{ $riwayat->created_at?->translatedFormat('d M Y H:i') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="stok-empty">Belum ada riwayat perubahan stok.</div>
                @endforelse
            </section>
        </aside>
    </div>

    <div class="modal fade" id="tambahStokModal" tabindex="-1" aria-labelledby="tambahStokTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
            <form action="{{ route('stok.tambah') }}" method="POST">
                @csrf
                <div class="modal-header"><h2 class="modal-title fs-6" id="tambahStokTitle">Tambah Stok</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body">
                    <label class="stok-modal-label" for="produk_id">Produk</label>
                    <select class="stok-modal-control mb-3" name="produk_id" id="produk_id" required><option value="">Pilih produk</option>@foreach($produks as $produk)<option value="{{ $produk->id_produk }}">{{ $produk->name_produk }} (stok {{ $produk->stok }})</option>@endforeach</select>
                    <label class="stok-modal-label" for="jumlah">Jumlah tambahan</label>
                    <input class="stok-modal-control" type="number" min="1" max="100000" name="jumlah" id="jumlah" placeholder="Masukkan jumlah stok" required>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Stok</button></div>
            </form>
        </div></div>
    </div>

    @foreach($produks as $produk)
        <div class="modal fade" id="detailStok{{ $produk->id_produk }}" tabindex="-1" aria-labelledby="detailStokTitle{{ $produk->id_produk }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                <div class="modal-header"><h2 class="modal-title fs-6" id="detailStokTitle{{ $produk->id_produk }}">Detail Stok Produk</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body"><h3 class="h5">{{ $produk->name_produk }}</h3><p class="text-muted small">{{ $produk->kategori->nama_kategori ?? '-' }}</p><p>Stok tersedia: <strong>{{ $produk->stok }} pcs</strong></p><p>Stok minimum: <strong>{{ $produk->stok_minimum }} pcs</strong></p><p>Status: <strong>{{ $produk->stok === 0 ? 'Habis' : ($produk->stok <= $produk->stok_minimum ? 'Menipis' : 'Aman') }}</strong></p></div>
                <div class="modal-footer"><a href="{{ route('produk.edit', $produk) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i> Edit Produk</a></div>
            </div></div>
        </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        const table = $('#stok-table').DataTable({
            responsive: false,
            pageLength: 10,
            autoWidth: false,
            language: {
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                zeroRecords: 'Data tidak ditemukan.',
                emptyTable: 'Belum ada produk.',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
            },
        });
        document.getElementById('stok-search').addEventListener('input', (event) => table.search(event.currentTarget.value).draw());
        document.getElementById('stok-category-filter').addEventListener('change', (event) => {
            const value = $.fn.dataTable.util.escapeRegex(event.currentTarget.value);
            table.column(4).search(value ? '^' + value + '$' : '', true, false).draw();
        });
        document.getElementById('stok-status-filter').addEventListener('change', (event) => {
            const value = $.fn.dataTable.util.escapeRegex(event.currentTarget.value);
            const query = value === 'Perhatian' ? '^(Menipis|Habis)$' : (value ? '^' + value + '$' : '');
            table.column(7).search(query, true, false).draw();
        });
        document.getElementById('stok-check-all').addEventListener('change', (event) => {
            document.querySelectorAll('.stok-row-check').forEach((checkbox) => { checkbox.checked = event.currentTarget.checked; });
        });
        document.querySelectorAll('[data-filter-status]').forEach((link) => link.addEventListener('click', () => {
            document.getElementById('stok-status-filter').value = link.dataset.filterStatus;
            document.getElementById('stok-status-filter').dispatchEvent(new Event('change'));
        }));
    });
</script>
@endsection