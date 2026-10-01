@extends('layouts.template')

@section('page-title', 'Pelanggan')
@section('tanpa-judul', true)

@section('content')
<style>
    .customers-page { padding:9px 0 22px; color:#3f302b; }
    .customers-heading { display:flex; align-items:flex-start; justify-content:space-between; gap:14px; margin:0 4px 13px; }
    .customers-heading-main { display:flex; align-items:flex-start; gap:10px; }
    .customers-heading-icon { color:#8b6250; font-size:1.55rem; }
    .customers-heading h1 { margin:0; color:#50392f; font:600 1.48rem 'Playfair Display',serif; }
    .customers-heading p { margin:3px 0 0; color:#89786e; font-size:.68rem; }
    .customers-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:9px; margin-bottom:10px; }
    .customers-stat { display:flex; align-items:center; gap:10px; min-width:0; padding:10px 12px; border:1px solid #efe4dd; border-radius:9px; background:#fff; }
    .customers-stat-icon { display:grid; place-items:center; flex:0 0 37px; width:37px; height:37px; border-radius:50%; font-size:.9rem; }
    .customers-stat-icon.brown { background:#f3e9e2; color:#8b6250; }
    .customers-stat-icon.green { background:#e8f4e9; color:#4f9c61; }
    .customers-stat-icon.coral { background:#fbe9ea; color:#df747d; }
    .customers-stat-icon.amber { background:#fff1d9; color:#d3942e; }
    .customers-stat-label { color:#81736b; font-size:.6rem; white-space:nowrap; }
    .customers-stat-value { color:#352820; font-size:.98rem; font-weight:700; line-height:1.35; white-space:nowrap; }
    .customers-layout { display:grid; grid-template-columns:minmax(0,1fr) 260px; gap:9px; align-items:start; }
    .customers-card { min-width:0; padding:11px; border:1px solid #efe4dd; border-radius:8px; background:#fff; box-shadow:0 2px 9px rgba(80,48,34,.025); }
    .customers-toolbar { display:flex; align-items:center; gap:8px; margin-bottom:10px; }
    .customers-title { margin:0; color:#60483b; font:600 .79rem 'Playfair Display',serif; white-space:nowrap; }
    .customers-search { position:relative; flex:1; min-width:140px; }
    .customers-search i { position:absolute; top:50%; left:10px; color:#92847b; transform:translateY(-50%); }
    .customers-search input,.customers-filter { height:32px; border:1px solid #e9dfd9; border-radius:6px; background:#fff; color:#5f5149; font-size:.59rem; }
    .customers-search input { width:100%; padding:5px 8px 5px 29px; }
    .customers-filter { min-width:135px; padding:5px 8px; }
    .customers-search input:focus,.customers-filter:focus { border-color:#b88b76; outline:2px solid rgba(184,139,118,.15); }
    .customers-table { margin:0 !important; border-collapse:separate; border-spacing:0; }
    .customers-table thead th { padding:8px 6px !important; border-top:1px solid #eee5df !important; border-bottom:1px solid #e9dfd9 !important; background:#faf7f5 !important; color:#70594c !important; font-size:.55rem !important; font-weight:600 !important; white-space:nowrap; }
    .customers-table thead th:first-child { border-left:1px solid #eee5df !important; border-radius:6px 0 0 0; }
    .customers-table thead th:last-child { border-right:1px solid #eee5df !important; border-radius:0 6px 0 0; }
    .customers-table tbody td { padding:7px 6px !important; border-color:#f0e9e4 !important; color:#4d4038 !important; font-size:.57rem !important; vertical-align:middle !important; }
    .customers-table tbody tr:hover td { background:#fcf9f7; }
    .customers-table tbody tr.selected td { background:#fff5ec; }
    .customer-person { display:flex; align-items:center; gap:7px; min-width:145px; }
    .customer-avatar { display:grid; place-items:center; width:31px; height:31px; flex:none; border-radius:50%; background:#f5e8e1; color:#8b6250; font-size:.65rem; font-weight:600; text-transform:uppercase; }
    .customer-name { display:block; color:#49382f; font-size:.58rem; font-weight:600; white-space:nowrap; }
    .customer-contact { display:block; color:#958880; font-size:.51rem; white-space:nowrap; }
    .customer-date { color:#8b7d74; font-size:.54rem; white-space:nowrap; }
    .customer-orders-count { display:inline-block; min-width:24px; padding:3px 7px; border-radius:15px; background:#f3e9e2; color:#805a47; font-size:.53rem; text-align:center; }
    .customer-spent { color:#58463c; font-weight:600; white-space:nowrap; }
    .customer-type { display:inline-block; padding:3px 6px; border-radius:14px; background:#e5f4e8; color:#4d8e59; font-size:.5rem; white-space:nowrap; }
    .customer-type.guest { background:#f3e9e2; color:#80604e; }
    .customer-view { display:inline-flex; align-items:center; gap:4px; padding:4px 7px; border:1px solid #eadfd8; border-radius:5px; color:#80604e; font-size:.52rem; text-decoration:none; white-space:nowrap; }
    .customer-view:hover { background:#f8f1ed; }
    .customer-name-link { color:inherit; text-decoration:none; }
    .customer-name-link:hover { color:#9b6651; text-decoration:underline; }
    .customer-status { display:inline-block; padding:3px 6px; border-radius:14px; background:#e5f4e8; color:#4d8e59; font-size:.5rem; white-space:nowrap; }
    .customer-status.inactive { background:#f1e9e5; color:#8d7c72; }
    .customer-actions { display:flex; justify-content:center; gap:4px; }
    .customer-action { display:inline-grid; place-items:center; width:26px; height:26px; padding:0; border:1px solid #eadfd8; border-radius:5px; background:#fff; color:#80604e; font-size:.65rem; text-decoration:none; }
    .customer-action:hover { background:#f8f1ed; color:#5f3f32; }
    .customer-action.disable { color:#ad741f; }
    .customer-action.enable { color:#468955; }
    .customers-table-wrap .dataTables_wrapper { font-size:.58rem; }
    .customers-table-wrap .dataTables_filter,.customers-table-wrap .dataTables_length { display:none; }
    .customers-table-wrap .dataTables_info { padding-top:8px !important; color:#8b7d74; font-size:.53rem; }
    .customers-table-wrap .dataTables_paginate { padding-top:4px !important; }
    .customers-table-wrap .pagination { gap:3px; }
    .customers-table-wrap .pagination .page-link { border:1px solid #eee4de; border-radius:5px !important; color:#70594c; font-size:.58rem; padding:3px 7px; }
    .customers-table-wrap .page-item.active .page-link { border-color:#7b5343; background:#7b5343; color:#fff; }
    .customer-detail { position:sticky; top:66px; padding:12px; }
    .customer-detail-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
    .customer-detail-head h2 { margin:0; color:#60483b; font-size:.7rem; font-weight:600; }
    .customer-profile { display:flex; align-items:center; gap:9px; padding-bottom:10px; border-bottom:1px solid #f1e9e4; }
    .customer-profile .customer-avatar { width:40px; height:40px; font-size:.8rem; }
    .customer-profile-name { color:#49382f; font-size:.65rem; font-weight:600; }
    .customer-profile-meta { color:#8b7d74; font-size:.53rem; }
    .customer-info { display:grid; gap:7px; padding:9px 0; border-bottom:1px solid #f1e9e4; }
    .customer-info-row { display:flex; align-items:flex-start; gap:7px; color:#6b5a50; font-size:.54rem; line-height:1.35; overflow-wrap:anywhere; }
    .customer-info-row i { width:12px; flex:none; color:#997563; }
    .customer-metrics { display:grid; grid-template-columns:1fr 1fr; gap:6px; margin:9px 0; }
    .customer-metric { padding:7px; border-radius:6px; background:#faf6f3; }
    .customer-metric span { display:block; color:#8b7d74; font-size:.5rem; }
    .customer-metric strong { display:block; margin-top:3px; color:#554236; font-size:.63rem; }
    .customer-edit-form { display:grid; gap:6px; }
    .customer-edit-label { margin-top:5px; color:#6b5a50; font-size:.56rem; font-weight:600; }
    .customer-edit-input { min-height:32px; padding:6px 8px; border:1px solid #e9dfd9; border-radius:5px; color:#514139; font-size:.58rem; }
    .customer-edit-input:focus { border-color:#b88b76; outline:2px solid rgba(184,139,118,.15); }
    .customer-save,.customer-cancel { display:flex; min-height:30px; align-items:center; justify-content:center; gap:5px; border:1px solid #7b5343; border-radius:5px; background:#7b5343; color:#fff; font-size:.55rem; text-decoration:none; }
    .customer-save:hover { background:#5f3f32; }
    .customer-cancel { border-color:#e7dcd5; background:#fff; color:#80604e; }
    .customer-history-title { margin:9px 0 6px; color:#49382f; font-size:.58rem; font-weight:600; }
    .customer-order { display:flex; justify-content:space-between; gap:6px; padding:6px 0; border-top:1px solid #f1e9e4; text-decoration:none; }
    .customer-order:first-of-type { border-top:0; }
    .customer-order-code { color:#624b3d; font-size:.53rem; font-weight:600; }
    .customer-order-meta { color:#968981; font-size:.5rem; }
    .customer-order-total { color:#5b483e; font-size:.53rem; font-weight:600; white-space:nowrap; }
    .customer-empty { padding:26px 8px; color:#95877f; text-align:center; font-size:.6rem; }
    @media (max-width:1050px) { .customers-layout { grid-template-columns:minmax(0,1fr); } .customer-detail { position:static; } }
    @media (max-width:760px) { .customers-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } .customers-toolbar { flex-wrap:wrap; } .customers-title { width:100%; } .customers-search { flex-basis:100%; } .customers-filter { flex:1; } }
    @media (max-width:540px) { .customers-page { padding-top:6px; } .customers-heading h1 { font-size:1.3rem; } .customers-card { padding:8px; } .customer-detail { padding:10px; } }
</style>

<div class="customers-page">
    <header class="customers-heading"><i class="customers-heading-icon bi bi-people"></i><div><h1>Pelanggan</h1><p>Kenali pelanggan dan lihat riwayat transaksinya.</p></div></header>

    <section class="customers-stats" aria-label="Ringkasan pelanggan">
        <article class="customers-stat"><span class="customers-stat-icon brown"><i class="bi bi-people"></i></span><div><div class="customers-stat-label">Total Pelanggan</div><div class="customers-stat-value">{{ $totalPelanggan }}</div></div></article>
        <article class="customers-stat"><span class="customers-stat-icon green"><i class="bi bi-bag-check"></i></span><div><div class="customers-stat-label">Pernah Bertransaksi</div><div class="customers-stat-value">{{ $pelangganAktif }}</div></div></article>
        <article class="customers-stat"><span class="customers-stat-icon coral"><i class="bi bi-person-plus"></i></span><div><div class="customers-stat-label">Pelanggan Baru (30 Hari)</div><div class="customers-stat-value">{{ $pelangganBaru }}</div></div></article>
        <article class="customers-stat"><span class="customers-stat-icon amber"><i class="bi bi-cash-stack"></i></span><div><div class="customers-stat-label">Penjualan Selesai</div><div class="customers-stat-value">Rp {{ number_format($totalBelanjaSelesai, 0, ',', '.') }}</div></div></article>
    </section>

    <div class="customers-layout">
        <section class="customers-card customers-table-wrap" aria-label="Daftar pelanggan">
            <div class="customers-toolbar">
                <h2 class="customers-title">Daftar Pelanggan</h2>
                <label class="customers-search"><i class="bi bi-search"></i><input id="customer-search" type="search" placeholder="Cari nama, email, atau nomor HP..." aria-label="Cari pelanggan"></label>
                <select class="customers-filter" id="customer-filter" aria-label="Filter pelanggan"><option value="">Semua Pelanggan</option><option value="Terdaftar">Akun Terdaftar</option><option value="Tamu">Pelanggan Tamu</option><option value="Pernah Memesan">Pernah Memesan</option><option value="Belum Memesan">Belum Memesan</option></select>
            </div>
            <div class="table-responsive">
                <table class="table customers-table w-100" id="customers-table">
                    <thead><tr><th class="text-center"><input type="checkbox" class="orders-check" id="customer-check-all" aria-label="Pilih semua pelanggan"></th><th>Pelanggan</th><th>Pesanan</th><th>Total Belanja</th><th>Pesanan Terakhir</th><th>Tipe</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach($pelanggan as $item)
                            @php $filterTipe = $item->tamu ? 'Tamu' : ($item->jumlah_pesanan ? 'Pernah Memesan' : 'Belum Memesan'); @endphp
                            <tr class="{{ $pelangganTerpilih?->key === $item->key ? 'selected' : '' }}" data-customer-filter="{{ $item->tamu ? 'Tamu' : ($item->jumlah_pesanan ? 'Pernah Memesan' : 'Belum Memesan') }}" data-customer-guest="{{ $item->tamu ? '1' : '0' }}">
                                <td class="text-center"><input type="checkbox" class="orders-check customer-row-check" aria-label="Pilih {{ $item->nama }}"></td>
                                <td data-search="{{ $item->nama }} {{ $item->email }} {{ $item->telepon }}"><div class="customer-person"><span class="customer-avatar">{{ \Illuminate\Support\Str::substr($item->nama, 0, 1) }}</span><span><a class="customer-name customer-name-link" href="{{ route('pelanggan.index', ['pilih' => $item->key]) }}">{{ $item->nama }}</a><span class="customer-contact">{{ $item->email ?: ($item->telepon ?: 'Email/telepon belum tersedia') }}</span></span></div></td>
                                <td data-order="{{ $item->jumlah_pesanan }}"><span class="customer-orders-count">{{ $item->jumlah_pesanan }}</span></td>
                                <td data-order="{{ $item->total_belanja }}" class="customer-spent">Rp {{ number_format($item->total_belanja, 0, ',', '.') }}</td>
                                <td data-order="{{ $item->terakhir?->timestamp ?? 0 }}"><span class="customer-date">{{ $item->terakhir?->translatedFormat('d M Y') ?? 'Belum ada' }}</span></td>
                                <td><span class="customer-type {{ $item->tamu ? 'guest' : '' }}">{{ $item->tamu ? 'Tamu' : 'Terdaftar' }}</span>@unless($item->tamu)<span class="customer-status {{ $item->is_active ? '' : 'inactive' }} d-block mt-1">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>@endunless</td>
                                <td>
                                    @if($item->user)
                                        <div class="customer-actions">
                                            <a class="customer-action" href="{{ route('pelanggan.index', ['pilih' => $item->key, 'edit' => 1]) }}" title="Edit {{ $item->nama }}" aria-label="Edit {{ $item->nama }}"><i class="bi bi-pencil"></i></a>
                                            <form action="{{ route('pelanggan.status', $item->user) }}" method="POST" class="d-inline">@csrf @method('PATCH')<button class="customer-action {{ $item->is_active ? 'disable' : 'enable' }}" type="submit" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $item->nama }}" aria-label="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $item->nama }}"><i class="bi {{ $item->is_active ? 'bi-person-slash' : 'bi-person-check' }}"></i></button></form>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        @if($pelangganTerpilih)
            <aside class="customers-card customer-detail" aria-label="Detail pelanggan">
                <div class="customer-detail-head"><h2>{{ $modeEdit ? 'Edit Pelanggan' : 'Detail Pelanggan' }}</h2><a href="{{ route('pelanggan.index', ['pilih' => $pelangganTerpilih->key]) }}" aria-label="Tutup detail"><i class="bi bi-x-lg"></i></a></div>
                @if($modeEdit && $pelangganTerpilih->user)
                    <form action="{{ route('pelanggan.update', $pelangganTerpilih->user) }}" method="POST" class="customer-edit-form">
                        @csrf @method('PUT')
                        <label class="customer-edit-label" for="customer-name">Nama</label>
                        <input class="customer-edit-input" id="customer-name" name="name" value="{{ old('name', $pelangganTerpilih->user->name) }}" required maxlength="80">
                        @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                        <label class="customer-edit-label" for="customer-email">Email</label>
                        <input class="customer-edit-input" id="customer-email" type="email" name="email" value="{{ old('email', $pelangganTerpilih->user->email) }}" required maxlength="120">
                        @error('email')<span class="text-danger small">{{ $message }}</span>@enderror
                        <button class="customer-save" type="submit"><i class="bi bi-floppy"></i> Simpan Perubahan</button>
                        <a class="customer-cancel" href="{{ route('pelanggan.index', ['pilih' => $pelangganTerpilih->key]) }}">Batal</a>
                    </form>
                @else
                    <div class="customer-profile"><span class="customer-avatar">{{ \Illuminate\Support\Str::substr($pelangganTerpilih->nama, 0, 1) }}</span><span><span class="customer-profile-name d-block">{{ $pelangganTerpilih->nama }}</span><span class="customer-profile-meta">{{ $pelangganTerpilih->tamu ? 'Pelanggan tamu' : ($pelangganTerpilih->is_active ? 'Akun aktif' : 'Akun nonaktif') }}</span></span></div>
                    <div class="customer-info">
                        <div class="customer-info-row"><i class="bi bi-envelope"></i><span>{{ $pelangganTerpilih->email ?: 'Email belum tersedia' }}</span></div>
                        <div class="customer-info-row"><i class="bi bi-telephone"></i><span>{{ $pelangganTerpilih->telepon ?: 'Nomor HP belum tersedia' }}</span></div>
                        <div class="customer-info-row"><i class="bi bi-geo-alt"></i><span>{{ $pelangganTerpilih->alamat ?: 'Alamat belum tersedia' }}</span></div>
                        <div class="customer-info-row"><i class="bi bi-calendar3"></i><span>{{ $pelangganTerpilih->tamu ? 'Pelanggan sejak' : 'Terdaftar' }} {{ $pelangganTerpilih->terdaftar_pada?->translatedFormat('d M Y') ?? '-' }}</span></div>
                    </div>
                    <div class="customer-metrics"><div class="customer-metric"><span>Total Pesanan</span><strong>{{ $pelangganTerpilih->jumlah_pesanan }}</strong></div><div class="customer-metric"><span>Total Belanja Selesai</span><strong>Rp {{ number_format($pelangganTerpilih->total_belanja, 0, ',', '.') }}</strong></div></div>
                    <h3 class="customer-history-title">Riwayat Pesanan</h3>
                    @forelse($pelangganTerpilih->checkouts->take(5) as $checkout)
                        <a class="customer-order" href="{{ route('pesanan.index', ['pilih' => $checkout->key]) }}"><span><span class="customer-order-code d-block">#{{ $checkout->kode }}</span><span class="customer-order-meta">{{ $checkout->created_at?->translatedFormat('d M Y') }} · {{ $checkout->status }}</span></span><span class="customer-order-total">Rp {{ number_format($checkout->total, 0, ',', '.') }}</span></a>
                    @empty
                        <div class="customer-empty">Belum ada riwayat pesanan.</div>
                    @endforelse
                @endif
            </aside>
        @else
            <aside class="customers-card customer-detail"><div class="customer-empty">Belum ada data pelanggan.</div></aside>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        let customerFilter = '';
        $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
            if (settings.nTable.id !== 'customers-table' || !customerFilter) return true;
            const row = settings.aoData[dataIndex]?.nTr;
            if (customerFilter === 'Akun Terdaftar') return row?.dataset.customerGuest === '0';
            return row?.dataset.customerFilter === customerFilter;
        });
        const table = $('#customers-table').DataTable({
            responsive: false,
            pageLength: 10,
            autoWidth: false,
            order: [[3, 'desc']],
            language: { info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data', infoEmpty: 'Tidak ada pelanggan', zeroRecords: 'Pelanggan tidak ditemukan.', emptyTable: 'Belum ada pelanggan.', paginate: { previous: 'Sebelumnya', next: 'Berikutnya' } },
        });
        document.getElementById('customer-search').addEventListener('input', (event) => table.search(event.currentTarget.value).draw());
        document.getElementById('customer-filter').addEventListener('change', (event) => {
            customerFilter = event.currentTarget.value;
            table.draw();
        });
        document.getElementById('customer-check-all').addEventListener('change', (event) => document.querySelectorAll('.customer-row-check').forEach((checkbox) => { checkbox.checked = event.currentTarget.checked; }));
    });
</script>
@endsection
