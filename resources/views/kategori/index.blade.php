@extends('layouts.template')

@section('page-title', 'Kategori')
@section('tanpa-judul', true)

@section('content')
<style>
    .kategori-page { padding:8px 0 22px; color:#3f302b; }
    .kategori-shell { display:grid; grid-template-columns:minmax(0,1fr); gap:10px; align-items:start; }
    .kategori-shell.with-panel { grid-template-columns:minmax(0,1fr) 310px; }
    .kategori-main { min-width:0; }
    .kategori-heading { display:block; margin:0 4px 12px; }
    .kategori-heading-main { display:flex; align-items:flex-start; gap:10px; }
    .kategori-heading-icon { color:#8b6250; font-size:1.65rem; line-height:1.2; }
    .kategori-heading h1 { margin:0; color:#50392f; font:600 1.55rem 'Playfair Display',serif; }
    .kategori-heading p { margin:4px 0 0; color:#89786e; font-size:.75rem; }
    .kategori-page .create-breadcrumb { margin-bottom:4px; color:#9a887e; font-size:.62rem; }
    .kategori-page .create-breadcrumb i { margin:0 5px; color:#b29b8d; font-size:.55rem; }
    .kategori-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:8px; margin-top:12px; }
    .kategori-stat { display:flex; align-items:center; gap:10px; min-width:0; padding:11px 13px; background:#fff; border:1px solid #efe4dd; border-radius:9px; }
    .kategori-stat-icon { display:grid; place-items:center; flex:0 0 40px; width:40px; height:40px; border-radius:50%; font-size:1rem; }
    .kategori-stat-icon.brown { background:#f3e9e2; color:#8b6250; }
    .kategori-stat-icon.green { background:#e8f4e9; color:#4f9c61; }
    .kategori-stat-icon.amber { background:#fff1d9; color:#d3942e; }
    .kategori-stat-icon.red { background:#fbe8e7; color:#d86d65; }
    .kategori-stat-label { color:#81736b; font-size:.63rem; white-space:nowrap; }
    .kategori-stat-value { color:#352820; font-size:1.03rem; font-weight:700; line-height:1.3; }
    .kategori-panel { padding:14px; background:#fff; border:1px solid #efe4dd; border-radius:10px; box-shadow:0 3px 12px rgba(80,48,34,.035); }
    .kategori-toolbar { display:flex; align-items:center; gap:9px; margin-bottom:12px; }
    .kategori-panel-heading { flex:1; margin:0; color:#60483b; font:600 .82rem 'Playfair Display',serif; white-space:nowrap; }
    .kategori-search { position:relative; flex:1; min-width:180px; }
    .kategori-search i { position:absolute; top:50%; left:12px; color:#92847b; transform:translateY(-50%); }
    .kategori-search input { width:100%; height:36px; padding:7px 10px 7px 33px; border:1px solid #e9dfd9; border-radius:7px; background:#fff; color:#5f5149; font-size:.68rem; }
    .kategori-search input:focus { border-color:#b88b76; outline:2px solid rgba(184,139,118,.15); }
    .kategori-add { display:inline-flex; align-items:center; justify-content:center; gap:7px; min-height:36px; padding:7px 13px; border:1px solid #7b5343; border-radius:7px; background:#7b5343; color:#fff !important; font-size:.69rem; text-decoration:none; white-space:nowrap; }
    .kategori-add:hover { background:#5f3f32; }
    .kategori-table { margin:0 !important; border-collapse:separate; border-spacing:0; }
    .kategori-table thead th { padding:10px 9px !important; border-top:1px solid #eee5df !important; border-bottom:1px solid #e9dfd9 !important; background:#faf7f5 !important; color:#70594c !important; font-size:.66rem !important; font-weight:600 !important; white-space:nowrap; }
    .kategori-table thead th:first-child { border-left:1px solid #eee5df !important; border-radius:7px 0 0 0; }
    .kategori-table thead th:last-child { border-right:1px solid #eee5df !important; border-radius:0 7px 0 0; }
    .kategori-table tbody td { padding:10px 9px !important; border-color:#f0e9e4 !important; color:#4d4038 !important; font-size:.68rem !important; vertical-align:middle !important; }
    .kategori-table tbody tr:hover td { background:#fcf9f7; }
    .kategori-check,.kategori-check-all { width:14px; height:14px; accent-color:#7b5343; cursor:pointer; }
    .kategori-number { color:#8b7d74; }
    .kategori-name { color:#49382f; font-size:.72rem; font-weight:600; }
    .kategori-description { display:block; max-width:170px; overflow:hidden; color:#958880; font-size:.56rem; text-overflow:ellipsis; white-space:nowrap; }
    .kategori-count { display:inline-block; min-width:28px; padding:4px 9px; border-radius:20px; background:#f3e9e2; color:#805a47; font-size:.62rem; text-align:center; }
    .kategori-products { color:#8b7d74; font-size:.63rem; }
    .kategori-products-list { display:flex; flex-wrap:wrap; gap:5px; max-width:420px; }
    .kategori-product-chip { padding:4px 8px; border-radius:20px; background:#f6f0eb; color:#80604e; font-size:.58rem; }
    .kategori-actions { display:flex; justify-content:center; gap:5px; }
    .kategori-icon-button { display:inline-grid; place-items:center; width:29px; height:29px; padding:0; border:1px solid #eadfd8; border-radius:6px; background:#fff; color:#80604e; font-size:.72rem; text-decoration:none; }
    .kategori-icon-button:hover { border-color:#c9a995; background:#f8f1ed; color:#5f3f32; }
    .kategori-icon-button.delete { color:#a4564e; }
    .kategori-panel .dataTables_wrapper { font-size:.65rem; }
    .kategori-panel .dataTables_filter,.kategori-panel .dataTables_length { display:none; }
    .kategori-panel .dataTables_info { padding-top:10px !important; color:#8b7d74; font-size:.61rem; }
    .kategori-panel .dataTables_paginate { padding-top:6px !important; }
    .kategori-panel .pagination { gap:4px; }
    .kategori-panel .pagination .page-link { border:1px solid #eee4de; border-radius:6px !important; color:#70594c; font-size:.65rem; padding:5px 9px; }
    .kategori-panel .page-item.active .page-link { border-color:#7b5343; background:#7b5343; color:#fff; }
    .kategori-empty { padding:28px 12px; color:#95877f; font-size:.7rem; text-align:center; }
    .kategori-modal-products { margin:0; padding-left:18px; color:#65564e; font-size:.72rem; }
    .kategori-modal-products li+li { margin-top:5px; }
    .kategori-thumb { width:36px; height:36px; display:grid; place-items:center; overflow:hidden; border-radius:6px; background:#f4ece6; color:#9a715d; }
    .kategori-thumb img { width:100%; height:100%; object-fit:cover; }
    .kategori-status { display:inline-flex; align-items:center; gap:5px; padding:4px 8px; border-radius:20px; background:#e3f3e5; color:#468955; font-size:.58rem; white-space:nowrap; }
    .kategori-status::before { width:5px; height:5px; border-radius:50%; background:currentColor; content:''; }
    .kategori-status.inactive { background:#f1e9e5; color:#8d7c72; }
    .kategori-drawer { position:sticky; top:65px; min-width:0; overflow:hidden; border:1px solid #eadfd8; border-radius:9px; background:#fff; box-shadow:0 5px 18px rgba(80,48,34,.09); }
    .kategori-drawer-header { display:flex; align-items:center; gap:10px; padding:15px 16px 12px; border-bottom:1px solid #f0e8e3; }
    .kategori-drawer-header i { color:#8b6250; font-size:1rem; }
    .kategori-drawer-title { flex:1; margin:0; color:#60483b; font:600 1rem 'Playfair Display',serif; }
    .kategori-drawer-close { color:#9a7b6a; font-size:.86rem; text-decoration:none; }
    .kategori-drawer-subtitle { margin:0; padding:0 16px 12px 42px; color:#8a7a73; font-size:.62rem; }
    .kategori-form { display:flex; min-height:530px; flex-direction:column; }
    .kategori-form-body { display:grid; gap:14px; padding:15px 16px; }
    .kategori-form-label { display:block; margin-bottom:6px; color:#5c483e; font-size:.62rem; font-weight:500; }
    .kategori-form-label .required { color:#dc7377; }
    .kategori-form-control { width:100%; min-height:34px; padding:7px 10px; border:1px solid #eadfd8; border-radius:6px; background:#fff; color:#4f4038; font-size:.63rem; }
    .kategori-form-control.with-icon { padding-left:30px; }
    .kategori-form .create-input-wrap { position:relative; }
    .kategori-form .create-input-icon { position:absolute; z-index:1; top:50%; left:10px; color:#a18473; font-size:.7rem; transform:translateY(-50%); pointer-events:none; }
    .kategori-form-control:focus { border-color:#bd927d; outline:2px solid rgba(189,146,125,.15); }
    .kategori-form-control.is-invalid { border-color:#dc7377; }
    .kategori-form-control.description { min-height:68px; resize:vertical; }
    .kategori-form-error { margin-top:4px; color:#c44f58; font-size:.58rem; }
    .kategori-photo-drop { display:flex; min-height:96px; flex-direction:column; align-items:center; justify-content:center; gap:5px; border:1px dashed #e7bfc1; border-radius:7px; background:#fdf7f6; color:#8b6250; cursor:pointer; text-align:center; }
    .kategori-photo-drop i { display:grid; place-items:center; width:32px; height:32px; border-radius:8px; background:#faecea; color:#a67261; font-size:1rem; }
    .kategori-photo-drop strong { font-size:.62rem; font-weight:500; }
    .kategori-photo-drop small { color:#98877e; font-size:.54rem; }
    .kategori-photo-input { display:none; }
    .kategori-photo-preview { width:100%; height:100px; margin-top:7px; border-radius:6px; object-fit:cover; }
    .kategori-photo-preview[hidden] { display:none; }
    .kategori-toggle-row { display:flex; align-items:center; gap:10px; }
    .kategori-toggle { position:relative; width:30px; height:17px; flex:none; }
    .kategori-toggle input { position:absolute; width:1px; height:1px; opacity:0; }
    .kategori-toggle span { position:absolute; inset:0; border-radius:20px; background:#c7c1bc; cursor:pointer; }
    .kategori-toggle span::before { position:absolute; top:2px; left:2px; width:13px; height:13px; border-radius:50%; background:#fff; content:''; transition:transform .15s; }
    .kategori-toggle input:checked+span { background:#36a66d; }
    .kategori-toggle input:checked+span::before { transform:translateX(13px); }
    .kategori-toggle input:focus-visible+span { outline:2px solid #8b6250; outline-offset:2px; }
    .kategori-toggle-copy { color:#4d4038; font-size:.62rem; }
    .kategori-toggle-copy small { display:block; margin-top:2px; color:#98877e; font-size:.53rem; }
    .kategori-drawer-footer { display:grid; grid-template-columns:1fr 1.2fr; gap:8px; margin-top:auto; padding:11px 16px; border-top:1px solid #f0e8e3; }
    .kategori-cancel,.kategori-save { display:flex; min-height:33px; align-items:center; justify-content:center; gap:6px; border:1px solid #e7dcd5; border-radius:6px; font-size:.61rem; text-decoration:none; }
    .kategori-cancel { background:#fff; color:#80604e; }
    .kategori-save { border-color:#7b5343; background:#7b5343; color:#fff; }
    .kategori-save:hover { background:#5f3f32; color:#fff; }
    @media (max-width:900px) {
        .kategori-stats { width:100%; grid-template-columns:repeat(2,minmax(0,1fr)); }
        .kategori-shell.with-panel { grid-template-columns:minmax(0,1fr); }
        .kategori-drawer { position:static; }
        .kategori-toolbar { flex-wrap:wrap; }
        .kategori-search { flex-basis:100%; }
        .kategori-add { margin-left:auto; }
    }
    @media (max-width:540px) {
        .kategori-page { padding-top:8px; }
        .kategori-heading h1 { font-size:1.3rem; }
        .kategori-stats { grid-template-columns:minmax(0,1fr); gap:7px; }
        .kategori-stat { padding:9px; }
        .kategori-panel { padding:9px; }
    }
</style>

<div class="kategori-page">
    <div class="kategori-shell {{ $panelOpen ? 'with-panel' : '' }}">
        <div class="kategori-main">
            <header class="kategori-heading">
                <div class="kategori-heading-main">
                    <i class="kategori-heading-icon bi bi-tags" aria-hidden="true"></i>
                    <div>
                        <div class="create-breadcrumb"><i class="bi bi-house-door"></i> Produk <i class="bi bi-chevron-right"></i> Kategori</div>
                        <h1>Kategori</h1>
                        <p>Kelola kategori produk untuk memudahkan pengelolaan menu kue.</p>
                    </div>
                </div>
                <section class="kategori-stats" aria-label="Ringkasan kategori">
                    <article class="kategori-stat"><span class="kategori-stat-icon" style="background:#fbe9ea;color:#df747d"><i class="bi bi-tags"></i></span><div><div class="kategori-stat-label">Total Kategori</div><div class="kategori-stat-value">{{ $totalKategori }}</div></div></article>
                    <article class="kategori-stat"><span class="kategori-stat-icon green"><i class="bi bi-box-seam"></i></span><div><div class="kategori-stat-label">Produk Terdaftar</div><div class="kategori-stat-value">{{ $totalProduk }}</div></div></article>
                    <article class="kategori-stat"><span class="kategori-stat-icon amber"><i class="bi bi-check-circle"></i></span><div><div class="kategori-stat-label">Kategori Aktif</div><div class="kategori-stat-value">{{ $kategoriAktif }}</div></div></article>
                </section>
            </header>

            <section class="kategori-panel" aria-label="Daftar kategori">
                <div class="kategori-toolbar">
                    <h2 class="kategori-panel-heading">Daftar Kategori</h2>
                    <label class="kategori-search"><i class="bi bi-search" aria-hidden="true"></i><input id="kategori-search" type="search" placeholder="Cari kategori..." aria-label="Cari kategori"></label>
                </div>

                <div class="table-responsive">
                    <table class="table kategori-table w-100" id="kategori-table">
                        <thead>
                            <tr><th class="text-center"><input type="checkbox" class="kategori-check-all" aria-label="Pilih semua kategori"></th><th>No.</th><th>Gambar</th><th>Nama Kategori</th><th>Jumlah Produk</th><th>Status</th><th class="text-center">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td class="text-center"><input type="checkbox" class="kategori-check" aria-label="Pilih {{ $category->nama_kategori }}"></td>
                                    <td class="kategori-number">{{ $loop->iteration }}</td>
                                    <td><span class="kategori-thumb">@if($category->foto)<img src="{{ asset('storage/' . $category->foto) }}" alt="{{ $category->nama_kategori }}">@else<i class="bi bi-tag"></i>@endif</span></td>
                                    <td><span class="kategori-name">{{ $category->nama_kategori }}</span><span class="kategori-description">{{ \Illuminate\Support\Str::limit($category->deskripsi, 42) }}</span></td>
                                    <td data-order="{{ $category->produk_count }}"><span class="kategori-count">{{ $category->produk_count }} produk</span></td>
                                    <td><span class="kategori-status {{ $category->is_active ? '' : 'inactive' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                    <td>
                                        <div class="kategori-actions">
                                            <button type="button" class="kategori-icon-button" title="Lihat {{ $category->nama_kategori }}" aria-label="Lihat {{ $category->nama_kategori }}" data-bs-toggle="modal" data-bs-target="#kategoriDetail{{ $category->id_kategori }}"><i class="bi bi-eye"></i></button>
                                            <a href="{{ route('kategori.edit', $category) }}" class="kategori-icon-button" title="Edit {{ $category->nama_kategori }}" aria-label="Edit {{ $category->nama_kategori }}"><i class="bi bi-pencil"></i></a>
                                            <form action="{{ route('kategori.destroy', $category) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="button" class="kategori-icon-button delete btn-delete" title="Hapus {{ $category->nama_kategori }}" aria-label="Hapus {{ $category->nama_kategori }}"><i class="bi bi-trash3"></i></button></form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="kategori-empty">Belum ada kategori. Tambahkan kategori pertama untuk mengelompokkan produk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        @if($panelOpen)
            @php
                $isEditing = $formKategori->exists;
                $formAction = $isEditing ? route('kategori.update', $formKategori) : route('kategori.store');
            @endphp
            <aside class="kategori-drawer" aria-label="{{ $isEditing ? 'Edit Kategori' : 'Tambah Kategori' }}">
                <div class="kategori-drawer-header">
                    <i class="bi bi-tag"></i>
                    <h2 class="kategori-drawer-title">{{ $isEditing ? 'Edit Kategori' : 'Tambah Kategori' }}</h2>
                    <a href="{{ route('kategori.index') }}" class="kategori-drawer-close" aria-label="Tutup"><i class="bi bi-x-lg"></i></a>
                </div>
                <p class="kategori-drawer-subtitle">Lengkapi informasi kategori produk baru.</p>
                <form class="kategori-form" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($isEditing) @method('PUT') @endif
                    <div class="kategori-form-body">
                        <div>
                            <label class="kategori-form-label" for="nama_kategori">Nama Kategori <span class="required">*</span></label>
                            <div class="create-input-wrap"><i class="create-input-icon bi bi-tag"></i><input class="kategori-form-control with-icon @error('nama_kategori') is-invalid @enderror" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori', $formKategori->nama_kategori) }}" placeholder="Contoh: Brownies" required></div>
                            @error('nama_kategori')<div class="kategori-form-error">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="kategori-form-label" for="deskripsi">Deskripsi</label>
                            <textarea class="kategori-form-control description @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" maxlength="200" placeholder="Jelaskan tentang kategori ini...">{{ old('deskripsi', $formKategori->deskripsi) }}</textarea>
                            <div class="kategori-form-label text-end mt-1 mb-0"><span id="kategori-description-count">{{ strlen(old('deskripsi', $formKategori->deskripsi ?? '')) }}</span>/200</div>
                            @error('deskripsi')<div class="kategori-form-error">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="kategori-form-label" for="foto">Gambar / Ikon Kategori</label>
                            <label class="kategori-photo-drop" for="foto"><i class="bi bi-image"></i><strong>Klik untuk upload gambar</strong><small>PNG, JPG (maks. 2 MB)</small></label>
                            <input class="kategori-photo-input" type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/webp">
                            <img class="kategori-photo-preview" id="kategori-photo-preview" src="{{ $formKategori->foto ? asset('storage/' . $formKategori->foto) : '' }}" alt="Preview gambar kategori" @if(!$formKategori->foto) hidden @else style="display:block" @endif>
                            @error('foto')<div class="kategori-form-error">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="kategori-form-label" for="is_active">Status</label>
                            <div class="kategori-toggle-row">
                                <label class="kategori-toggle"><input type="hidden" name="is_active" value="0"><input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $formKategori->is_active ?? true))><span></span></label>
                                <div class="kategori-toggle-copy"><span id="kategori-status-label">{{ old('is_active', $formKategori->is_active ?? true) ? 'Aktif' : 'Nonaktif' }}</span><small>Kategori ditampilkan pada aplikasi.</small></div>
                            </div>
                            @error('is_active')<div class="kategori-form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="kategori-drawer-footer">
                        <a href="{{ route('kategori.index') }}" class="kategori-cancel">Batal</a>
                        <button type="submit" class="kategori-save"><i class="bi bi-floppy"></i> Simpan Kategori</button>
                    </div>
                </form>
            </aside>
        @endif
    </div>

    @foreach($categories as $category)
        <div class="modal fade" id="kategoriDetail{{ $category->id_kategori }}" tabindex="-1" aria-labelledby="kategoriDetailLabel{{ $category->id_kategori }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                <div class="modal-header"><h2 class="modal-title fs-6" id="kategoriDetailLabel{{ $category->id_kategori }}">Detail Kategori</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body">
                    @if($category->foto)<img src="{{ asset('storage/' . $category->foto) }}" alt="{{ $category->nama_kategori }}" class="w-100 mb-3 rounded">@endif
                    <h3 class="h5 mb-1">{{ $category->nama_kategori }}</h3>
                    <p class="text-muted small mb-3">{{ $category->deskripsi ?: 'Belum ada deskripsi kategori.' }}</p>
                    <p class="text-muted small mb-2">{{ $category->produk_count }} produk · {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                    @if($category->produk->isNotEmpty())<ul class="kategori-modal-products">@foreach($category->produk as $produk)<li>{{ $produk->name_produk }}</li>@endforeach</ul>@endif
                </div>
                <div class="modal-footer"><a href="{{ route('kategori.edit', $category) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i> Edit Kategori</a></div>
            </div></div>
        </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        const table = $('#kategori-table').DataTable({
            responsive: false,
            pageLength: 10,
            autoWidth: false,
            language: {
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                zeroRecords: 'Data tidak ditemukan.',
                emptyTable: 'Belum ada kategori yang tersedia.',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
            },
        });
        document.getElementById('kategori-search').addEventListener('input', (event) => {
            table.search(event.currentTarget.value).draw();
        });

        const description = document.getElementById('deskripsi');
        const imageInput = document.getElementById('foto');
        const statusInput = document.getElementById('is_active');
        if (description) {
            description.addEventListener('input', () => {
                document.getElementById('kategori-description-count').textContent = description.value.length;
            });
        }
        if (imageInput) {
            imageInput.addEventListener('change', () => {
                const [file] = imageInput.files;
                if (!file) return;
                const preview = document.getElementById('kategori-photo-preview');
                preview.src = URL.createObjectURL(file);
                preview.hidden = false;
                preview.style.display = 'block';
            });
        }
        if (statusInput) {
            statusInput.addEventListener('change', () => {
                document.getElementById('kategori-status-label').textContent = statusInput.checked ? 'Aktif' : 'Nonaktif';
            });
        }
    });
</script>
@endsection
