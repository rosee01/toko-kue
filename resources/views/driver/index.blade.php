@extends('layouts.template')

@section('page-title', 'Driver')
@section('tanpa-judul', true)

@section('content')
<style>
    .drivers-page { padding:9px 0 22px; color:#3f302b; }
    .drivers-heading { display:flex; align-items:center; gap:10px; margin:0 4px 13px; }
    .drivers-heading-icon { color:#8b6250; font-size:1.5rem; }
    .drivers-heading h1 { margin:0; color:#50392f; font:600 1.42rem 'Playfair Display',serif; }
    .drivers-heading p { margin:2px 0 0; color:#89786e; font-size:.66rem; }
    .drivers-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:9px; margin-bottom:10px; }
    .drivers-stat { display:flex; align-items:center; gap:10px; min-width:0; padding:10px 12px; border:1px solid #efe4dd; border-radius:9px; background:#fff; }
    .drivers-stat-icon { display:grid; place-items:center; flex:0 0 37px; width:37px; height:37px; border-radius:50%; font-size:.9rem; }
    .drivers-stat-icon.brown { background:#f3e9e2; color:#8b6250; }
    .drivers-stat-icon.green { background:#e8f4e9; color:#4f9c61; }
    .drivers-stat-icon.amber { background:#fff1d9; color:#d3942e; }
    .drivers-stat-icon.coral { background:#fbe9ea; color:#df747d; }
    .drivers-stat-label { color:#81736b; font-size:.6rem; white-space:nowrap; }
    .drivers-stat-value { color:#352820; font-size:.98rem; font-weight:700; line-height:1.35; }
    .drivers-layout { display:grid; grid-template-columns:minmax(0,1fr) 270px; gap:9px; align-items:start; }
    .drivers-card { min-width:0; padding:11px; border:1px solid #efe4dd; border-radius:8px; background:#fff; box-shadow:0 2px 9px rgba(80,48,34,.025); }
    .drivers-toolbar { display:flex; align-items:center; gap:8px; margin-bottom:10px; }
    .drivers-title { margin:0; color:#60483b; font:600 .79rem 'Playfair Display',serif; white-space:nowrap; }
    .drivers-search { position:relative; flex:1; min-width:130px; }
    .drivers-search i { position:absolute; top:50%; left:10px; color:#92847b; transform:translateY(-50%); }
    .drivers-search input,.drivers-filter { height:32px; border:1px solid #e9dfd9; border-radius:6px; background:#fff; color:#5f5149; font-size:.58rem; }
    .drivers-search input { width:100%; padding:5px 8px 5px 29px; }
    .drivers-filter { min-width:120px; padding:5px 8px; }
    .drivers-add { display:inline-flex; align-items:center; justify-content:center; gap:6px; min-height:32px; padding:6px 10px; border:1px solid #7b5343; border-radius:6px; background:#7b5343; color:#fff !important; font-size:.58rem; text-decoration:none; white-space:nowrap; }
    .drivers-add:hover { background:#5f3f32; }
    .drivers-table { margin:0 !important; border-collapse:separate; border-spacing:0; }
    .drivers-table thead th { padding:8px 7px !important; border-top:1px solid #eee5df !important; border-bottom:1px solid #e9dfd9 !important; background:#faf7f5 !important; color:#70594c !important; font-size:.56rem !important; font-weight:600 !important; white-space:nowrap; }
    .drivers-table thead th:first-child { border-left:1px solid #eee5df !important; border-radius:6px 0 0 0; }
    .drivers-table thead th:last-child { border-right:1px solid #eee5df !important; border-radius:0 6px 0 0; }
    .drivers-table tbody td { padding:7px !important; border-color:#f0e9e4 !important; color:#4d4038 !important; font-size:.58rem !important; vertical-align:middle !important; }
    .drivers-table tbody tr:hover td { background:#fcf9f7; }
    .drivers-table tbody tr.selected td { background:#fff5ec; }
    .driver-profile { display:flex; align-items:center; gap:7px; min-width:130px; }
    .driver-avatar { display:grid; place-items:center; width:31px; height:31px; flex:none; border-radius:50%; background:#f3e9e2; color:#8b6250; font-size:.62rem; font-weight:600; }
    .driver-name { display:block; color:#49382f; font-size:.59rem; font-weight:600; white-space:nowrap; }
    .driver-phone { display:block; color:#958880; font-size:.51rem; white-space:nowrap; }
    .driver-vehicle { color:#6d5b50; white-space:nowrap; }
    .driver-status { display:inline-flex; align-items:center; gap:5px; padding:4px 7px; border-radius:20px; font-size:.51rem; white-space:nowrap; }
    .driver-status::before { width:5px; height:5px; border-radius:50%; background:currentColor; content:''; }
    .driver-status.available { background:#e3f3e5; color:#468955; }
    .driver-status.delivering { background:#fff0d7; color:#ad741f; }
    .driver-status.inactive { background:#f1e9e5; color:#8d7c72; }
    .driver-count { display:inline-block; min-width:23px; padding:3px 6px; border-radius:14px; background:#f3e9e2; color:#805a47; font-size:.52rem; text-align:center; }
    .driver-actions { display:flex; justify-content:center; gap:4px; }
    .driver-action { display:inline-grid; place-items:center; width:26px; height:26px; padding:0; border:1px solid #eadfd8; border-radius:5px; background:#fff; color:#80604e; font-size:.65rem; text-decoration:none; }
    .driver-action:hover { background:#f8f1ed; }
    .driver-action.deactivate { color:#ad741f; }
    .driver-action.activate { color:#468955; }
    .drivers-table-wrap .dataTables_wrapper { font-size:.58rem; }
    .drivers-table-wrap .dataTables_filter,.drivers-table-wrap .dataTables_length { display:none; }
    .drivers-table-wrap .dataTables_info { padding-top:8px !important; color:#8b7d74; font-size:.53rem; }
    .drivers-table-wrap .dataTables_paginate { padding-top:4px !important; }
    .drivers-table-wrap .pagination { gap:3px; }
    .drivers-table-wrap .pagination .page-link { border:1px solid #eee4de; border-radius:5px !important; color:#70594c; font-size:.58rem; padding:3px 7px; }
    .drivers-table-wrap .page-item.active .page-link { border-color:#7b5343; background:#7b5343; color:#fff; }
    .driver-detail { position:sticky; top:66px; padding:12px; }
    .driver-detail-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
    .driver-detail-head h2 { margin:0; color:#60483b; font-size:.7rem; font-weight:600; }
    .driver-detail-profile { display:flex; align-items:center; gap:9px; padding-bottom:10px; border-bottom:1px solid #f1e9e4; }
    .driver-detail-profile .driver-avatar { width:40px; height:40px; font-size:.8rem; }
    .driver-detail-name { color:#49382f; font-size:.65rem; font-weight:600; }
    .driver-detail-sub { color:#8b7d74; font-size:.53rem; }
    .driver-detail-info { display:grid; gap:7px; padding:9px 0; border-bottom:1px solid #f1e9e4; }
    .driver-info-row { display:flex; gap:7px; color:#6b5a50; font-size:.55rem; }
    .driver-info-row i { width:12px; color:#997563; }
    .driver-detail-stats { display:grid; grid-template-columns:1fr 1fr; gap:6px; margin:9px 0; }
    .driver-detail-stat { padding:7px; border-radius:6px; background:#faf6f3; }
    .driver-detail-stat span { display:block; color:#8b7d74; font-size:.5rem; }
    .driver-detail-stat strong { display:block; margin-top:3px; color:#554236; font-size:.63rem; }
    .driver-panel-title { margin:9px 0 6px; color:#49382f; font-size:.58rem; font-weight:600; }
    .driver-task { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:6px; padding:6px 0; border-top:1px solid #f1e9e4; text-decoration:none; }
    .driver-task form { grid-column:1 / -1; }
    .driver-task-code { color:#624b3d; font-size:.53rem; font-weight:600; }
    .driver-task-copy { color:#968981; font-size:.5rem; }
    .driver-task-status { color:#ad741f; font-size:.51rem; white-space:nowrap; }
    .driver-task-complete { margin-top:5px; padding:4px 7px; border:1px solid #d8e9da; border-radius:5px; background:#f2f8f2; color:#4c8153; font-size:.52rem; }
    .driver-assign { display:grid; gap:6px; margin-top:9px; padding-top:9px; border-top:1px solid #f1e9e4; }
    .driver-assign-list { display:grid; gap:5px; max-height:180px; overflow:auto; padding:7px; border:1px solid #e9dfd9; border-radius:5px; }
    .driver-assign-option { display:flex; align-items:flex-start; gap:6px; color:#5f5149; font-size:.54rem; }
    .driver-assign-option input { margin-top:2px; accent-color:#7b5343; }
    .driver-assign-hint { color:#887970; font-size:.52rem; line-height:1.4; }
    .driver-assign button,.driver-save { display:flex; min-height:30px; align-items:center; justify-content:center; gap:5px; border:1px solid #7b5343; border-radius:5px; background:#7b5343; color:#fff; font-size:.54rem; }
    .driver-form { display:grid; gap:7px; }
    .driver-form label { margin-top:3px; color:#6b5a50; font-size:.55rem; font-weight:600; }
    .driver-form input,.driver-form select { min-height:31px; padding:5px 8px; border:1px solid #e9dfd9; border-radius:5px; color:#5f5149; font-size:.57rem; }
    .driver-form-error { color:#c44f58; font-size:.52rem; }
    .driver-cancel { display:flex; min-height:28px; align-items:center; justify-content:center; border:1px solid #e7dcd5; border-radius:5px; color:#80604e; font-size:.54rem; text-decoration:none; }
    .driver-empty { padding:24px 8px; color:#95877f; text-align:center; font-size:.6rem; }
    @media (max-width:1050px) { .drivers-layout { grid-template-columns:minmax(0,1fr); } .driver-detail { position:static; } }
    @media (max-width:760px) { .drivers-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } .drivers-toolbar { flex-wrap:wrap; } .drivers-title { width:100%; } .drivers-search { flex-basis:100%; } .drivers-filter { flex:1; } }
    @media (max-width:540px) { .drivers-page { padding-top:6px; } .drivers-heading h1 { font-size:1.3rem; } .drivers-card { padding:8px; } .driver-detail { padding:10px; } }
</style>

<div class="drivers-page">
    <header class="drivers-heading"><i class="drivers-heading-icon bi bi-truck"></i><div><h1>Driver</h1><p>Kelola driver dan pantau tugas pengiriman.</p></div></header>

    <section class="drivers-stats" aria-label="Ringkasan driver">
        <article class="drivers-stat"><span class="drivers-stat-icon brown"><i class="bi bi-people"></i></span><div><div class="drivers-stat-label">Total Driver</div><div class="drivers-stat-value">{{ $totalDriver }}</div></div></article>
        <article class="drivers-stat"><span class="drivers-stat-icon green"><i class="bi bi-person-check"></i></span><div><div class="drivers-stat-label">Tersedia</div><div class="drivers-stat-value">{{ $driverTersedia }}</div></div></article>
        <article class="drivers-stat"><span class="drivers-stat-icon amber"><i class="bi bi-truck"></i></span><div><div class="drivers-stat-label">Sedang Mengantar</div><div class="drivers-stat-value">{{ $driverMengantar }}</div></div></article>
        <article class="drivers-stat"><span class="drivers-stat-icon coral"><i class="bi bi-person-slash"></i></span><div><div class="drivers-stat-label">Nonaktif</div><div class="drivers-stat-value">{{ $driverNonaktif }}</div></div></article>
    </section>

    <div class="drivers-layout">
        <section class="drivers-card drivers-table-wrap" aria-label="Daftar driver">
            <div class="drivers-toolbar">
                <h2 class="drivers-title">Daftar Driver</h2>
                <label class="drivers-search"><i class="bi bi-search"></i><input id="driver-search" type="search" placeholder="Cari nama, telepon, atau plat nomor..." aria-label="Cari driver"></label>
                <select class="drivers-filter" id="driver-filter" aria-label="Filter status"><option value="">Semua Status</option><option value="Tersedia">Tersedia</option><option value="Sedang Mengantar">Sedang Mengantar</option><option value="Nonaktif">Nonaktif</option></select>
                <a href="{{ route('driver.index', ['tambah' => 1]) }}" class="drivers-add"><i class="bi bi-plus-lg"></i> Tambah Driver</a>
            </div>
            <div class="table-responsive"><table class="table drivers-table w-100" id="drivers-table">
                <thead><tr><th class="text-center"><input type="checkbox" id="driver-check-all" class="orders-check" aria-label="Pilih semua driver"></th><th>Driver</th><th>Kendaraan</th><th>Tugas Aktif</th><th>Total Tugas</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($drivers as $driver)
                        @php $statusDriver = ! $driver->is_active ? 'Nonaktif' : ($driver->tugas_aktif > 0 ? 'Sedang Mengantar' : 'Tersedia'); @endphp
                        <tr class="{{ $driverTerpilih?->id === $driver->id ? 'selected' : '' }}">
                            <td class="text-center"><input type="checkbox" class="orders-check driver-row-check" aria-label="Pilih {{ $driver->nama }}"></td>
                            <td><a href="{{ route('driver.index', ['pilih' => $driver->id]) }}" class="driver-profile text-decoration-none"><span class="driver-avatar">{{ \Illuminate\Support\Str::substr($driver->nama, 0, 1) }}</span><span><span class="driver-name">{{ $driver->nama }}</span><span class="driver-phone">{{ $driver->no_telepon }}</span></span></a></td>
                            <td class="driver-vehicle">{{ $driver->jenis_kendaraan ?: 'Kendaraan belum dicatat' }}<br><span class="driver-phone">{{ $driver->plat_nomor ?: '-' }}</span></td>
                            <td><span class="driver-count">{{ $driver->tugas_aktif }}</span></td>
                            <td>{{ $driver->total_tugas }}</td>
                            <td><span class="driver-status {{ $statusDriver === 'Tersedia' ? 'available' : ($statusDriver === 'Nonaktif' ? 'inactive' : 'delivering') }}">{{ $statusDriver }}</span></td>
                            <td><div class="driver-actions">
                                <a href="{{ route('driver.index', ['pilih' => $driver->id, 'edit' => 1]) }}" class="driver-action" title="Edit {{ $driver->nama }}" aria-label="Edit {{ $driver->nama }}"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('driver.status', $driver) }}" method="POST" class="d-inline">@csrf @method('PATCH')<button class="driver-action {{ $driver->is_active ? 'deactivate' : 'activate' }}" type="submit" title="{{ $driver->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $driver->nama }}" aria-label="{{ $driver->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $driver->nama }}"><i class="bi {{ $driver->is_active ? 'bi-person-slash' : 'bi-person-check' }}"></i></button></form>
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
        </section>

        @if($formOpen)
            @php $isEditing = $formDriver->exists; @endphp
            <aside class="drivers-card driver-detail">
                <div class="driver-detail-head"><h2>{{ $isEditing ? 'Edit Driver' : 'Tambah Driver' }}</h2><a href="{{ route('driver.index') }}" aria-label="Tutup"><i class="bi bi-x-lg"></i></a></div>
                <form class="driver-form" action="{{ $isEditing ? route('driver.update', $formDriver) : route('driver.store') }}" method="POST">
                    @csrf @if($isEditing) @method('PUT') @endif
                    <label for="nama">Nama Driver</label><input id="nama" name="nama" value="{{ old('nama', $formDriver->nama) }}" required maxlength="120">@error('nama')<span class="driver-form-error">{{ $message }}</span>@enderror
                    <label for="no_telepon">Nomor Telepon</label><input id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $formDriver->no_telepon) }}" required maxlength="20">@error('no_telepon')<span class="driver-form-error">{{ $message }}</span>@enderror
                    <label for="jenis_kendaraan">Jenis Kendaraan</label><input id="jenis_kendaraan" name="jenis_kendaraan" value="{{ old('jenis_kendaraan', $formDriver->jenis_kendaraan) }}" placeholder="Motor / Mobil" maxlength="60">
                    <label for="plat_nomor">Plat Nomor</label><input id="plat_nomor" name="plat_nomor" value="{{ old('plat_nomor', $formDriver->plat_nomor) }}" placeholder="Contoh: B 1234 ABC" maxlength="16">
                    <button type="submit" class="driver-save"><i class="bi bi-floppy"></i> {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Driver' }}</button>
                    <a class="driver-cancel" href="{{ route('driver.index') }}">Batal</a>
                </form>
            </aside>
        @elseif($driverTerpilih)
            @php $statusTerpilih = ! $driverTerpilih->is_active ? 'Nonaktif' : ($driverTerpilih->tugas_aktif > 0 ? 'Sedang Mengantar' : 'Tersedia'); @endphp
            <aside class="drivers-card driver-detail">
                <div class="driver-detail-head"><h2>Detail Driver</h2><a href="{{ route('driver.index') }}" aria-label="Tutup detail"><i class="bi bi-x-lg"></i></a></div>
                <div class="driver-detail-profile"><span class="driver-avatar">{{ \Illuminate\Support\Str::substr($driverTerpilih->nama, 0, 1) }}</span><span><span class="driver-detail-name d-block">{{ $driverTerpilih->nama }}</span><span class="driver-detail-sub">{{ $statusTerpilih }}</span></span></div>
                <div class="driver-detail-info"><div class="driver-info-row"><i class="bi bi-telephone"></i>{{ $driverTerpilih->no_telepon }}</div><div class="driver-info-row"><i class="bi bi-truck"></i>{{ $driverTerpilih->jenis_kendaraan ?: 'Kendaraan belum dicatat' }}</div><div class="driver-info-row"><i class="bi bi-card-text"></i>{{ $driverTerpilih->plat_nomor ?: 'Plat nomor belum dicatat' }}</div></div>
                <div class="driver-detail-stats"><div class="driver-detail-stat"><span>Tugas Aktif</span><strong>{{ $driverTerpilih->tugas_aktif }}</strong></div><div class="driver-detail-stat"><span>Total Tugas</span><strong>{{ $driverTerpilih->total_tugas }}</strong></div></div>
                <h3 class="driver-panel-title">Tugas Pengiriman</h3>
                @forelse($tugasDriver as $pesanan)
                    <div class="driver-task"><span><a class="driver-task-code d-block text-decoration-none" href="{{ route('pesanan.index', ['pilih' => $pesanan->kode_pesanan ?: 'pesanan-' . $pesanan->id]) }}">#{{ $pesanan->kode_pesanan ?: 'PSN-' . $pesanan->id }}</a><span class="driver-task-copy">{{ $pesanan->nama_pelanggan }} · {{ $pesanan->jumlah_item }} jenis produk</span></span><span class="driver-task-status">{{ $pesanan->status }}</span>
                        @if($pesanan->status === \App\Models\Pesanan::STATUS_DALAM_PENGANTARAN)
                            <form action="{{ route('driver.pesanan.selesai', [$driverTerpilih, $pesanan]) }}" method="POST">@csrf @method('PATCH')<button class="driver-task-complete" type="submit"><i class="bi bi-check-circle"></i> Tandai sudah diantar</button></form>
                        @endif
                    </div>
                @empty
                    <div class="driver-empty">Belum ada tugas pengiriman.</div>
                @endforelse
                @if($driverTerpilih->sedang_mengantar)
                    <div class="driver-empty"><i class="bi bi-sign-stop"></i><br>Driver sedang dalam perjalanan. Tugas baru bisa diberikan setelah pengantaran aktif selesai.</div>
                @elseif($driverTerpilih->is_active && $pesananSiapDiantar->isNotEmpty())
                    <form action="{{ route('driver.assign', $driverTerpilih) }}" method="POST" class="driver-assign">@csrf
                        <div class="driver-assign-hint">Pilih satu atau beberapa pesanan yang akan diantar bersamaan dalam perjalanan ini.</div>
                        <div class="driver-assign-list">
                            @foreach($pesananSiapDiantar as $pesanan)
                                <label class="driver-assign-option"><input type="checkbox" name="pesanan_ids[]" value="{{ $pesanan->id }}"><span>#{{ $pesanan->kode_pesanan ?: $pesanan->id }} · {{ $pesanan->nama_pelanggan }}</span></label>
                            @endforeach
                        </div>
                        <button type="submit"><i class="bi bi-send"></i> Tugaskan sebagai satu perjalanan</button>
                    </form>
                @endif
            </aside>
        @else
            <aside class="drivers-card driver-detail"><div class="driver-empty">Belum ada driver. Tambahkan driver pertama.</div></aside>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        const table = $('#drivers-table').DataTable({
            responsive: false,
            pageLength: 10,
            autoWidth: false,
            language: { info: 'Menampilkan _START_ - _END_ dari _TOTAL_ driver', infoEmpty: 'Tidak ada driver', zeroRecords: 'Driver tidak ditemukan.', emptyTable: 'Belum ada driver.', paginate: { previous: 'Sebelumnya', next: 'Berikutnya' } },
        });
        document.getElementById('driver-search').addEventListener('input', (event) => table.search(event.currentTarget.value).draw());
        document.getElementById('driver-filter').addEventListener('change', (event) => {
            const value = $.fn.dataTable.util.escapeRegex(event.currentTarget.value);
            table.column(5).search(value ? '^' + value + '$' : '', true, false).draw();
        });
        document.getElementById('driver-check-all').addEventListener('change', (event) => document.querySelectorAll('.driver-row-check').forEach((checkbox) => { checkbox.checked = event.currentTarget.checked; }));
    });
</script>
@endsection