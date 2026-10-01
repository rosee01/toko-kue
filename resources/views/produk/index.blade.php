@extends('layouts.template')

@section('page-title', 'Daftar Produk')
@section('tanpa-judul', true)

@section('content')
<style>
    .produk-page { padding:14px 0 22px; color:#3f302b; }
    .produk-heading { display:flex; justify-content:space-between; align-items:center; gap:16px; margin:0 4px 12px; }
    .produk-heading-main { display:flex; align-items:flex-start; flex:0 0 36%; gap:12px; }
    .produk-heading-icon { color:#8b6250; font-size:1.65rem; line-height:1.2; }
    .produk-heading h1 { margin:0; color:#50392f; font:600 1.55rem 'Playfair Display',serif; }
    .produk-heading p { margin:4px 0 0; color:#89786e; font-size:.75rem; }
    .produk-stats { display:grid; flex:1; grid-template-columns:repeat(4,minmax(0,1fr)); gap:8px; margin-bottom:0; }
    .produk-stat { display:flex; align-items:center; gap:9px; min-width:0; padding:9px 10px; background:#fff; border:1px solid #efe4dd; border-radius:9px; }
    .produk-stat-icon { display:grid; place-items:center; flex:0 0 32px; width:32px; height:32px; border-radius:50%; font-size:.85rem; }
    .produk-stat-icon.brown { background:#f3e9e2; color:#8b6250; }
    .produk-stat-icon.green { background:#e8f4e9; color:#4f9c61; }
    .produk-stat-icon.amber { background:#fff1d9; color:#d3942e; }
    .produk-stat-icon.red { background:#fbe8e7; color:#d86d65; }
    .produk-stat-label { color:#81736b; font-size:.63rem; white-space:nowrap; }
    .produk-stat-value { color:#352820; font-size:1.03rem; font-weight:700; line-height:1.3; }
    .produk-panel { padding:14px; background:#fff; border:1px solid #efe4dd; border-radius:10px; box-shadow:0 3px 12px rgba(80,48,34,.035); }
    .produk-toolbar { display:flex; align-items:center; gap:9px; margin-bottom:12px; }
    .produk-search { position:relative; flex:1; min-width:180px; }
    .produk-search i { position:absolute; top:50%; left:12px; color:#92847b; transform:translateY(-50%); }
    .produk-search input,.produk-toolbar select { height:36px; border:1px solid #e9dfd9; border-radius:7px; background:#fff; color:#5f5149; font-size:.68rem; }
    .produk-search input { width:100%; padding:7px 10px 7px 33px; }
    .produk-toolbar select { min-width:145px; padding:6px 28px 6px 10px; }
    .produk-search input:focus,.produk-toolbar select:focus { border-color:#b88b76; outline:2px solid rgba(184,139,118,.15); }
    .produk-add { display:inline-flex; align-items:center; justify-content:center; gap:7px; min-height:36px; padding:7px 13px; border:1px solid #7b5343; border-radius:7px; background:#7b5343; color:#fff !important; font-size:.69rem; text-decoration:none; white-space:nowrap; }
    .produk-add:hover { background:#5f3f32; }
    .produk-table { margin:0 !important; border-collapse:separate; border-spacing:0; }
    .produk-table thead th { padding:9px 8px !important; border-top:1px solid #eee5df !important; border-bottom:1px solid #e9dfd9 !important; background:#faf7f5 !important; color:#70594c !important; font-size:.62rem !important; font-weight:600 !important; white-space:nowrap; }
    .produk-table thead th:first-child { border-left:1px solid #eee5df !important; border-radius:7px 0 0 0; }
    .produk-table thead th:last-child { border-right:1px solid #eee5df !important; border-radius:0 7px 0 0; }
    .produk-table tbody td { padding:7px 8px !important; border-color:#f0e9e4 !important; color:#4d4038 !important; font-size:.64rem !important; vertical-align:middle !important; }
    .produk-table tbody tr:hover td { background:#fcf9f7; }
    .produk-check { width:14px; height:14px; accent-color:#7b5343; cursor:pointer; }
    .produk-photo { width:43px; height:39px; object-fit:cover; border:1px solid #eee4de; border-radius:6px; }
    .produk-photo-empty { display:grid; place-items:center; width:43px; height:39px; border-radius:6px; background:#f4ece7; color:#a78370; }
    .produk-name { display:block; color:#49382f; font-size:.65rem; font-weight:600; }
    .produk-description { display:block; max-width:220px; overflow:hidden; color:#958880; font-size:.57rem; text-overflow:ellipsis; white-space:nowrap; }
    .produk-category,.produk-stock,.produk-status { display:inline-block; padding:4px 8px; border-radius:20px; font-size:.58rem; white-space:nowrap; }
    .produk-category { background:#f5ece6; color:#8b6250; }
    .produk-stock.ok { background:#e7f4e8; color:#468955; }
    .produk-stock.low { background:#fff0d7; color:#ad741f; }
    .produk-stock.none { background:#fde6e5; color:#c64f4b; }
    .produk-status.active { background:#e3f3e5; color:#468955; }
    .produk-status.empty { background:#fde6e5; color:#c64f4b; }
    .produk-status.inactive { background:#f1e9e5; color:#8d7c72; }
    .produk-actions { display:flex; justify-content:center; gap:4px; }
    .produk-icon-button { display:inline-grid; place-items:center; width:28px; height:28px; padding:0; border:1px solid #eadfd8; border-radius:6px; background:#fff; color:#80604e; font-size:.72rem; text-decoration:none; }
    .produk-icon-button:hover { border-color:#c9a995; background:#f8f1ed; color:#5f3f32; }
    .produk-icon-button.delete { color:#a4564e; }
    .produk-panel .dataTables_wrapper { font-size:.65rem; }
    .produk-panel .dataTables_filter,.produk-panel .dataTables_length { display:none; }
    .produk-panel .dataTables_info { padding-top:10px !important; color:#8b7d74; font-size:.61rem; }
    .produk-panel .dataTables_paginate { padding-top:6px !important; }
    .produk-panel .pagination { gap:4px; }
    .produk-panel .pagination .page-link { border:1px solid #eee4de; border-radius:6px !important; color:#70594c; font-size:.65rem; padding:5px 9px; }
    .produk-panel .page-item.active .page-link { border-color:#7b5343; background:#7b5343; color:#fff; }
    .produk-detail-photo { width:100%; max-height:220px; object-fit:cover; border-radius:8px; }
    .produk-detail-label { color:#8a7a73; font-size:.68rem; }
    .produk-detail-value { color:#49382f; font-size:.78rem; font-weight:500; }
    @media (max-width:900px) {
        .produk-heading { align-items:flex-start; flex-direction:column; }
        .produk-stats { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .produk-toolbar { flex-wrap:wrap; }
        .produk-search { flex-basis:100%; }
        .produk-toolbar select { flex:1; }
        .produk-add { margin-left:auto; }
    }
    @media (max-width:540px) {
        .produk-page { padding-top:8px; }
        .produk-heading h1 { font-size:1.3rem; }
        .produk-stats { gap:7px; }
        .produk-stat { padding:9px; }
        .produk-stat-icon { flex-basis:30px; width:30px; height:30px; }
        .produk-stat-label { font-size:.58rem; }
        .produk-panel { padding:9px; }
        .produk-toolbar select { min-width:120px; }
    }
</style>

<div class="produk-page">
    <header class="produk-heading">
        <div class="produk-heading-main">
            <i class="produk-heading-icon bi bi-box-seam" aria-hidden="true"></i>
            <div>
                <h1>Daftar Produk</h1>
                <p>Kelola seluruh produk yang tersedia di toko Anda.</p>
            </div>
        </div>
        <section class="produk-stats" aria-label="Ringkasan produk">
            <article class="produk-stat"><span class="produk-stat-icon brown"><i class="bi bi-clipboard-data"></i></span><div><div class="produk-stat-label">Total Produk</div><div class="produk-stat-value">{{ $totalProduk }}</div></div></article>
            <article class="produk-stat"><span class="produk-stat-icon green"><i class="bi bi-box"></i></span><div><div class="produk-stat-label">Produk Aktif</div><div class="produk-stat-value">{{ $produkAktif }}</div></div></article>
            <article class="produk-stat"><span class="produk-stat-icon amber"><i class="bi bi-exclamation-triangle"></i></span><div><div class="produk-stat-label">Stok Menipis</div><div class="produk-stat-value">{{ $stokMenipis }}</div></div></article>
            <article class="produk-stat"><span class="produk-stat-icon red"><i class="bi bi-box2-fill"></i></span><div><div class="produk-stat-label">Stok Habis</div><div class="produk-stat-value">{{ $stokHabis }}</div></div></article>
        </section>
    </header>

    <section class="produk-panel" aria-label="Daftar produk">
        <div class="produk-toolbar">
            <label class="produk-search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input id="produk-search" type="search" placeholder="Cari nama produk, kategori, atau kode..." aria-label="Cari produk">
            </label>
            <select id="produk-category-filter" aria-label="Filter kategori">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}</option>
                @endforeach
            </select>
            <select id="produk-status-filter" aria-label="Filter status">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Habis">Habis</option>
                <option value="Nonaktif">Nonaktif</option>
            </select>
            <a href="{{ route('produk.create') }}" class="produk-add"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
        </div>

        <div class="table-responsive">
            <table class="table produk-table w-100">
                <thead>
                    <tr>
                        <th class="text-center"><input class="produk-check" id="produk-check-all" type="checkbox" aria-label="Pilih semua produk"></th>
                        <th>No.</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produks as $item)
                        @php $statusProduk = ! $item->is_active ? 'Nonaktif' : ($item->stok === 0 ? 'Habis' : 'Aktif'); @endphp
                        <tr>
                            <td class="text-center"><input class="produk-check produk-row-check" type="checkbox" aria-label="Pilih {{ $item->name_produk }}"></td>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($item->foto)
                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->name_produk }}" class="produk-photo">
                                @else
                                    <span class="produk-photo-empty"><i class="bi bi-cake2"></i></span>
                                @endif
                            </td>
                            <td><span class="produk-name">{{ $item->name_produk }}</span><span class="produk-description">{{ \Illuminate\Support\Str::limit($item->deskripsi, 64) }}</span></td>
                            <td><span class="produk-category">{{ $item->kategori->nama_kategori ?? '-' }}</span></td>
                            <td data-order="{{ $item->harga }}">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td data-order="{{ $item->stok }}"><span class="produk-stock {{ $item->stok === 0 ? 'none' : ($item->stok <= $item->stok_minimum ? 'low' : 'ok') }}">{{ $item->stok }} pcs</span></td>
                            <td><span class="produk-status {{ $statusProduk === 'Aktif' ? 'active' : ($statusProduk === 'Habis' ? 'empty' : 'inactive') }}">{{ $statusProduk }}</span></td>
                            <td>
                                <div class="produk-actions">
                                    <button type="button" class="produk-icon-button" title="Lihat detail {{ $item->name_produk }}" aria-label="Lihat detail {{ $item->name_produk }}" data-bs-toggle="modal" data-bs-target="#produkDetail{{ $item->id_produk }}"><i class="bi bi-eye"></i></button>
                                    <a href="{{ route('produk.edit', $item) }}" class="produk-icon-button" title="Edit {{ $item->name_produk }}" aria-label="Edit {{ $item->name_produk }}"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('produk.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="produk-icon-button delete btn-delete" title="Hapus {{ $item->name_produk }}" aria-label="Hapus {{ $item->name_produk }}"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @foreach($produks as $item)
        <div class="modal fade" id="produkDetail{{ $item->id_produk }}" tabindex="-1" aria-labelledby="produkDetailLabel{{ $item->id_produk }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header"><h2 class="modal-title fs-6" id="produkDetailLabel{{ $item->id_produk }}">Detail Produk</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                    <div class="modal-body">
                        @if($item->foto)<img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->name_produk }}" class="produk-detail-photo mb-3">@endif
                        <h3 class="h5 mb-3">{{ $item->name_produk }}</h3>
                        <div class="row g-3">
                            <div class="col-6"><div class="produk-detail-label">Kategori</div><div class="produk-detail-value">{{ $item->kategori->nama_kategori ?? '-' }}</div></div>
                            <div class="col-6"><div class="produk-detail-label">Harga</div><div class="produk-detail-value">Rp {{ number_format($item->harga, 0, ',', '.') }}</div></div>
                            <div class="col-6"><div class="produk-detail-label">Stok</div><div class="produk-detail-value">{{ $item->stok }} pcs</div></div>
                            <div class="col-6"><div class="produk-detail-label">Stok Minimum</div><div class="produk-detail-value">{{ $item->stok_minimum }} pcs</div></div>
                            <div class="col-6"><div class="produk-detail-label">Status</div><div class="produk-detail-value">{{ $item->stok === 0 ? 'Habis' : ($item->is_active ? 'Aktif' : 'Nonaktif') }}</div></div>
                            <div class="col-12"><div class="produk-detail-label">Deskripsi</div><div class="produk-detail-value">{{ $item->deskripsi ?: 'Belum ada deskripsi.' }}</div></div>
                        </div>
                    </div>
                    <div class="modal-footer"><a href="{{ route('produk.edit', $item) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i> Edit Produk</a></div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        const table = $('.produk-table').DataTable({
            responsive: false,
            pageLength: 10,
            autoWidth: false,
            language: {
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                zeroRecords: 'Data tidak ditemukan.',
                emptyTable: 'Belum ada produk yang tersedia.',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
            },
        });
        const search = document.getElementById('produk-search');
        const category = document.getElementById('produk-category-filter');
        const status = document.getElementById('produk-status-filter');
        const checkAll = document.getElementById('produk-check-all');

        search.addEventListener('input', () => table.search(search.value).draw());
        category.addEventListener('change', () => {
            const value = $.fn.dataTable.util.escapeRegex(category.value);
            table.column(4).search(value ? '^' + value + '$' : '', true, false).draw();
        });
        status.addEventListener('change', () => {
            const value = $.fn.dataTable.util.escapeRegex(status.value);
            table.column(7).search(value ? '^' + value + '$' : '', true, false).draw();
        });
        checkAll.addEventListener('change', () => {
            document.querySelectorAll('.produk-row-check').forEach((checkbox) => {
                checkbox.checked = checkAll.checked;
            });
        });
    });
</script>
@endsection
