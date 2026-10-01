@extends('layouts.template')

@section('page-title', 'Pembayaran')
@section('tanpa-judul', true)

@section('content')
<style>
    .payments-page { padding:8px 0 22px; color:#3f302b; }
    .payments-heading { display:flex; align-items:center; gap:10px; margin:0 4px 12px; }
    .payments-heading-icon { color:#8b6250; font-size:1.5rem; }
    .payments-heading h1 { margin:0; color:#50392f; font:600 1.42rem 'Playfair Display',serif; }
    .payments-heading p { margin:2px 0 0; color:#89786e; font-size:.66rem; }
    .payments-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:9px; margin-bottom:10px; }
    .payments-stat { display:flex; align-items:center; gap:10px; min-width:0; padding:10px 12px; border:1px solid #efe4dd; border-radius:9px; background:#fff; }
    .payments-stat-icon { display:grid; place-items:center; flex:0 0 37px; width:37px; height:37px; border-radius:50%; font-size:.9rem; }
    .payments-stat-icon.amber { background:#fff1d9; color:#d3942e; }
    .payments-stat-icon.green { background:#e8f4e9; color:#4f9c61; }
    .payments-stat-icon.brown { background:#f3e9e2; color:#8b6250; }
    .payments-stat-icon.coral { background:#fbe9ea; color:#df747d; }
    .payments-stat-label { color:#81736b; font-size:.6rem; white-space:nowrap; }
    .payments-stat-value { color:#352820; font-size:.96rem; font-weight:700; line-height:1.35; white-space:nowrap; }
    .payments-tabs { display:flex; gap:7px; overflow:auto; margin-bottom:8px; padding:8px; border:1px solid #efe4dd; border-radius:8px; background:#fff; scrollbar-width:thin; }
    .payments-tab { display:flex; align-items:center; gap:6px; padding:5px 10px; border:1px solid transparent; border-radius:20px; color:#806b5e; font-size:.57rem; text-decoration:none; white-space:nowrap; }
    .payments-tab .count { display:grid; min-width:15px; height:15px; place-items:center; padding:0 4px; border-radius:12px; background:rgba(70,50,40,.1); font-size:.5rem; }
    .payments-tab.all { background:#f5ece6; color:#80604e; }
    .payments-tab.unpaid { background:#fff0dc; color:#b17a2c; }
    .payments-tab.waiting { background:#e9efff; color:#536eaf; }
    .payments-tab.paid { background:#e5f4e8; color:#4d8e59; }
    .payments-tab.cod { background:#e4f2fb; color:#4d83c4; }
    .payments-tab.rejected { background:#fde9e7; color:#c76860; }
    .payments-tab.active { border-color:currentColor; box-shadow:inset 0 0 0 1px currentColor; }
    .payments-filters { display:flex; align-items:center; gap:8px; margin-bottom:9px; padding:8px; border:1px solid #efe4dd; border-radius:8px; background:#fff; }
    .payments-search { position:relative; flex:1; min-width:150px; }
    .payments-search i { position:absolute; top:50%; left:10px; color:#92847b; transform:translateY(-50%); }
    .payments-search input,.payments-date { height:31px; border:1px solid #e9dfd9; border-radius:6px; background:#fff; color:#5f5149; font-size:.58rem; }
    .payments-search input { width:100%; padding:5px 8px 5px 29px; }
    .payments-date { min-width:130px; padding:5px 8px; }
    .payments-search input:focus,.payments-date:focus { border-color:#b88b76; outline:2px solid rgba(184,139,118,.15); }
    .payments-layout { display:grid; grid-template-columns:minmax(0,1fr) 250px; gap:9px; align-items:start; }
    .payments-card { min-width:0; padding:10px; border:1px solid #efe4dd; border-radius:8px; background:#fff; box-shadow:0 2px 9px rgba(80,48,34,.025); }
    .payments-table { margin:0 !important; border-collapse:separate; border-spacing:0; }
    .payments-table thead th { padding:8px 6px !important; border-top:1px solid #eee5df !important; border-bottom:1px solid #e9dfd9 !important; background:#faf7f5 !important; color:#70594c !important; font-size:.55rem !important; font-weight:600 !important; white-space:nowrap; }
    .payments-table thead th:first-child { border-left:1px solid #eee5df !important; border-radius:6px 0 0 0; }
    .payments-table thead th:last-child { border-right:1px solid #eee5df !important; border-radius:0 6px 0 0; }
    .payments-table tbody td { padding:7px 6px !important; border-color:#f0e9e4 !important; color:#4d4038 !important; font-size:.57rem !important; vertical-align:middle !important; }
    .payments-table tbody tr:hover td { background:#fcf9f7; }
    .payments-table tbody tr.selected td { background:#fff5ec; }
    .payments-code { color:#554236; font-weight:600; text-decoration:underline; white-space:nowrap; }
    .payments-customer { display:block; color:#49382f; font-size:.58rem; font-weight:600; white-space:nowrap; }
    .payments-muted { color:#958880; font-size:.52rem; white-space:nowrap; }
    .payments-pill { display:inline-block; padding:4px 7px; border-radius:20px; font-size:.52rem; white-space:nowrap; }
    .payments-pill.unpaid { background:#fde6e5; color:#c64f4b; }
    .payments-pill.waiting { background:#e9efff; color:#536eaf; }
    .payments-pill.paid { background:#e3f3e5; color:#468955; }
    .payments-pill.cod { background:#e4f2fb; color:#4d83c4; }
    .payments-pill.rejected { background:#fde6e5; color:#c64f4b; }
    .payments-view { display:inline-flex; align-items:center; gap:4px; padding:4px 7px; border:1px solid #eadfd8; border-radius:5px; color:#80604e; font-size:.53rem; text-decoration:none; white-space:nowrap; }
    .payments-view:hover { background:#f8f1ed; }
    .payments-table-wrap .dataTables_wrapper { font-size:.58rem; }
    .payments-table-wrap .dataTables_filter,.payments-table-wrap .dataTables_length { display:none; }
    .payments-table-wrap .dataTables_info { padding-top:8px !important; color:#8b7d74; font-size:.53rem; }
    .payments-table-wrap .dataTables_paginate { padding-top:4px !important; }
    .payments-table-wrap .pagination { gap:3px; }
    .payments-table-wrap .pagination .page-link { border:1px solid #eee4de; border-radius:5px !important; color:#70594c; font-size:.58rem; padding:3px 7px; }
    .payments-table-wrap .page-item.active .page-link { border-color:#7b5343; background:#7b5343; color:#fff; }
    .payment-detail { position:sticky; top:66px; padding:12px; }
    .payment-detail-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:9px; }
    .payment-detail-head h2 { margin:0; color:#60483b; font-size:.7rem; font-weight:600; }
    .payment-detail-code { color:#49382f; font-size:.63rem; font-weight:700; }
    .payment-detail-date { margin-top:4px; color:#8b7d74; font-size:.54rem; }
    .payment-detail-customer { display:grid; gap:7px; margin:10px 0; padding-bottom:9px; border-bottom:1px solid #f1e9e4; }
    .payment-detail-line { display:flex; gap:7px; color:#6b5a50; font-size:.55rem; line-height:1.35; }
    .payment-detail-line i { width:12px; color:#997563; }
    .payment-section-title { margin:9px 0 7px; color:#49382f; font-size:.58rem; font-weight:600; }
    .payment-order-item { display:flex; justify-content:space-between; gap:8px; padding:4px 0; color:#6b5a50; font-size:.54rem; }
    .payment-summary { margin:8px 0; padding:7px 0; border-top:1px solid #f1e9e4; }
    .payment-summary-row { display:flex; justify-content:space-between; padding:3px 0; color:#887970; font-size:.55rem; }
    .payment-summary-row.total { border-top:1px solid #f1e9e4; margin-top:3px; padding-top:6px; color:#60483b; font-size:.65rem; font-weight:700; }
    .payment-confirmed { padding:7px; border-radius:6px; background:#f2f8f2; color:#557e59; font-size:.54rem; }
    .payment-evidence-preview { display:block; margin-top:8px; overflow:hidden; border:1px solid #eadfd8; border-radius:7px; }
    .payment-evidence-preview img { display:block; width:100%; max-height:190px; object-fit:contain; background:#faf7f5; }
    .payment-reject-label { display:block; margin:8px 0 4px; color:#6b5a50; font-size:.54rem; }
    .payment-reject-input { width:100%; min-height:31px; padding:5px 7px; border:1px solid #eadfd8; border-radius:5px; color:#5f5149; font-size:.54rem; }
    .payment-actions { display:grid; gap:5px; margin-top:9px; }
    .payment-action { display:flex; min-height:30px; align-items:center; justify-content:center; gap:5px; padding:5px 6px; border:1px solid #e7dcd5; border-radius:5px; background:#fff; color:#80604e; font-size:.54rem; }
    .payment-action.verify { border-color:#7b5343; background:#7b5343; color:#fff; }
    .payment-action.cod { border-color:#d7e9f5; background:#f1f8fc; color:#4d83c4; }
    .payment-action.reject { border-color:#f0d1ce; color:#b25f59; }
    .payment-empty { padding:24px 8px; color:#95877f; text-align:center; font-size:.6rem; }
    @media (max-width:1050px) { .payments-layout { grid-template-columns:minmax(0,1fr); } .payment-detail { position:static; } }
    @media (max-width:760px) { .payments-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } .payments-tabs { gap:5px; } .payments-filters { flex-wrap:wrap; } .payments-search { flex-basis:100%; } .payments-date { flex:1; } }
    @media (max-width:540px) { .payments-page { padding-top:6px; } .payments-heading h1 { font-size:1.3rem; } .payments-card { padding:8px; } .payment-detail { padding:10px; } }
</style>

<div class="payments-page">
    <header class="payments-heading"><i class="payments-heading-icon bi bi-credit-card-2-front"></i><div><h1>Pembayaran</h1><p>Kelola dan verifikasi pembayaran pesanan pelanggan.</p></div></header>

    <section class="payments-stats" aria-label="Ringkasan pembayaran">
        <article class="payments-stat"><span class="payments-stat-icon brown"><i class="bi bi-receipt"></i></span><div><div class="payments-stat-label">Total Transaksi</div><div class="payments-stat-value">{{ $jumlahSemua }}</div></div></article>
        <article class="payments-stat"><span class="payments-stat-icon amber"><i class="bi bi-hourglass-split"></i></span><div><div class="payments-stat-label">Menunggu Verifikasi</div><div class="payments-stat-value">{{ $jumlahMenungguVerifikasi }}</div></div></article>
        <article class="payments-stat"><span class="payments-stat-icon green"><i class="bi bi-check-circle"></i></span><div><div class="payments-stat-label">Terkonfirmasi</div><div class="payments-stat-value">Rp {{ number_format($nominalTerkonfirmasi, 0, ',', '.') }}</div></div></article>
        <article class="payments-stat"><span class="payments-stat-icon coral"><i class="bi bi-cash"></i></span><div><div class="payments-stat-label">COD</div><div class="payments-stat-value">{{ $jumlahCOD }} transaksi</div></div></article>
    </section>

    <nav class="payments-tabs" aria-label="Filter status pembayaran">
        <a href="{{ route('pembayaran.index') }}" class="payments-tab all {{ !$statusFilter ? 'active' : '' }}">Semua <span class="count">{{ $jumlahSemua }}</span></a>
        @foreach([
            \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR => ['Belum Dibayar', 'unpaid'],
            \App\Models\Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI => ['Menunggu Verifikasi', 'waiting'],
            \App\Models\Pesanan::PEMBAYARAN_LUNAS => ['Lunas', 'paid'],
            \App\Models\Pesanan::PEMBAYARAN_COD => ['COD', 'cod'],
            \App\Models\Pesanan::PEMBAYARAN_DITOLAK => ['Ditolak', 'rejected'],
            \App\Models\Pesanan::PEMBAYARAN_DIBATALKAN => ['Dibatalkan', 'rejected'],
        ] as $status => [$label, $warna])
            <a href="{{ route('pembayaran.index', ['status' => $status]) }}" class="payments-tab {{ $warna }} {{ $statusFilter === $status ? 'active' : '' }}">{{ $label }} <span class="count">{{ $jumlahPerStatus[$status] ?? 0 }}</span></a>
        @endforeach
    </nav>

    <div class="payments-filters">
        <label class="payments-search"><i class="bi bi-search"></i><input type="search" id="payment-search" placeholder="Cari nomor pesanan, pelanggan, atau nomor HP..." aria-label="Cari pembayaran"></label>
        <input class="payments-date" type="date" id="payment-date" aria-label="Filter tanggal pembayaran">
    </div>

    <div class="payments-layout">
        <section class="payments-card payments-table-wrap" aria-label="Daftar pembayaran">
            <div class="table-responsive"><table class="table payments-table w-100" id="payments-table">
                <thead><tr><th class="text-center"><input type="checkbox" class="orders-check" id="payment-check-all" aria-label="Pilih semua pembayaran"></th><th>No. Pesanan</th><th>Pelanggan</th><th>Tanggal</th><th>Total</th><th>Status Pesanan</th><th>Status Pembayaran</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($pembayaran as $item)
                        @php
                            $paymentClass = match ($item->status_pembayaran) {
                                \App\Models\Pesanan::PEMBAYARAN_LUNAS => 'paid',
                                \App\Models\Pesanan::PEMBAYARAN_COD => 'cod',
                                \App\Models\Pesanan::PEMBAYARAN_DITOLAK => 'rejected',
                                \App\Models\Pesanan::PEMBAYARAN_DIBATALKAN => 'rejected',
                                \App\Models\Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI => 'waiting',
                                default => 'unpaid',
                            };
                        @endphp
                        <tr class="{{ $pembayaranTerpilih?->key === $item->key ? 'selected' : '' }}">
                            <td class="text-center"><input class="orders-check payment-row-check" type="checkbox" aria-label="Pilih {{ $item->kode }}"></td>
                            <td data-search="{{ $item->kode }}"><a class="payments-code" href="{{ route('pembayaran.index', ['status' => $statusFilter, 'pilih' => $item->key]) }}">#{{ $item->kode }}</a></td>
                            <td data-search="{{ $item->nama_pelanggan }} {{ $item->no_telepon }}"><span class="payments-customer">{{ $item->nama_pelanggan }}</span><span class="payments-muted">{{ $item->no_telepon ?: 'Nomor HP belum tersedia' }}</span></td>
                            <td data-order="{{ $item->tanggal?->timestamp }}"><span class="payments-muted">{{ $item->tanggal?->translatedFormat('d M Y') }}</span><span class="payments-muted d-block">{{ $item->tanggal?->format('H:i') }}</span></td>
                            <td data-order="{{ $item->total }}">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                            <td><span class="payments-muted">{{ $item->status_pesanan }}</span></td>
                            <td>
                                <span class="payments-pill {{ $paymentClass }}">{{ $item->status_pembayaran }}</span>
                                <span class="payments-muted d-block">{{ $item->metode_pembayaran }}</span>
                            </td>
                            <td><a class="payments-view" href="{{ route('pembayaran.index', ['status' => $statusFilter, 'pilih' => $item->key]) }}"><i class="bi bi-eye"></i> Lihat</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
        </section>

        @if($pembayaranTerpilih)
            @php
                $detailPaymentClass = match ($pembayaranTerpilih->status_pembayaran) {
                    \App\Models\Pesanan::PEMBAYARAN_LUNAS => 'paid',
                    \App\Models\Pesanan::PEMBAYARAN_COD => 'cod',
                    \App\Models\Pesanan::PEMBAYARAN_DITOLAK => 'rejected',
                    \App\Models\Pesanan::PEMBAYARAN_DIBATALKAN => 'rejected',
                    \App\Models\Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI => 'waiting',
                    default => 'unpaid',
                };
                $methodLabel = $pembayaranTerpilih->metode_pembayaran;
            @endphp
            <aside class="payments-card payment-detail" aria-label="Detail pembayaran">
                <div class="payment-detail-head"><h2>Detail Pembayaran</h2><a href="{{ route('pembayaran.index', ['status' => $statusFilter]) }}" aria-label="Tutup detail"><i class="bi bi-x-lg"></i></a></div>
                <div class="payment-detail-code">#{{ $pembayaranTerpilih->kode }}</div>
                <div class="payment-detail-date"><i class="bi bi-calendar-event me-1"></i>{{ $pembayaranTerpilih->tanggal?->translatedFormat('d M Y, H:i') }}</div>
                <div class="payment-detail-customer">
                    <div class="payment-detail-line"><i class="bi bi-person"></i><span>{{ $pembayaranTerpilih->nama_pelanggan }}<br>{{ $pembayaranTerpilih->no_telepon ?: 'Nomor HP belum tersedia' }}</span></div>
                    <div class="payment-detail-line"><i class="bi bi-geo-alt"></i><span>{{ $pembayaranTerpilih->alamat_pengiriman ?: 'Alamat belum tersedia' }}</span></div>
                </div>
                <h3 class="payment-section-title">Produk yang Dipesan</h3>
                @foreach($pembayaranTerpilih->items as $item)
                    <div class="payment-order-item"><span>{{ $item->menu }}<br><span class="payments-muted">{{ $item->jumlah }} pcs × Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span></span><strong>Rp {{ number_format($item->total, 0, ',', '.') }}</strong></div>
                @endforeach
                <div class="payment-summary">
                    <div class="payment-summary-row"><span>Subtotal Produk</span><span>Rp {{ number_format($pembayaranTerpilih->subtotal, 0, ',', '.') }}</span></div>
                    <div class="payment-summary-row"><span>Ongkir</span><span>Rp {{ number_format($pembayaranTerpilih->ongkir, 0, ',', '.') }}</span></div>
                    <div class="payment-summary-row total"><span>Total</span><span>Rp {{ number_format($pembayaranTerpilih->total, 0, ',', '.') }}</span></div>
                </div>
                <h3 class="payment-section-title">Pembayaran</h3>
                <div class="payment-detail-line"><i class="bi bi-credit-card"></i><span>{{ $methodLabel }}</span></div>
                <div class="payment-detail-line mt-2"><i class="bi bi-info-circle"></i><span class="payments-pill {{ $detailPaymentClass }}">{{ $pembayaranTerpilih->status_pembayaran }}</span></div>
                @if($pembayaranTerpilih->tanggal_pembayaran)<div class="payments-muted mt-2">Diverifikasi {{ $pembayaranTerpilih->tanggal_pembayaran->translatedFormat('d M Y, H:i') }}</div>@endif
                @if($pembayaranTerpilih->bukti_pembayaran)
                    <div class="payment-confirmed mt-2">
                        Bukti dikirim {{ $pembayaranTerpilih->pembayaran_dikirim_pada?->translatedFormat('d M Y, H:i') }}.
                        <a href="{{ route('pembayaran.bukti', $pembayaranTerpilih->utama) }}" target="_blank" rel="noopener">Lihat foto bukti pembayaran</a>
                        <a class="payment-evidence-preview" href="{{ route('pembayaran.bukti', $pembayaranTerpilih->utama) }}" target="_blank" rel="noopener" aria-label="Buka bukti pembayaran ukuran penuh">
                            <img src="{{ route('pembayaran.bukti', $pembayaranTerpilih->utama) }}" alt="Bukti pembayaran pesanan #{{ $pembayaranTerpilih->kode }}">
                        </a>
                    </div>
                @endif
                @if($pembayaranTerpilih->catatan_pembayaran)<div class="payment-confirmed mt-2">Catatan: {{ $pembayaranTerpilih->catatan_pembayaran }}</div>@endif

                @if($pembayaranTerpilih->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI && $pembayaranTerpilih->bukti_pembayaran)
                    <form action="{{ route('pembayaran.update', $pembayaranTerpilih->utama) }}" method="POST" class="payment-actions">
                        @csrf @method('PATCH')
                        <button class="payment-action verify" name="status_pembayaran" value="{{ \App\Models\Pesanan::PEMBAYARAN_LUNAS }}"><i class="bi bi-check-circle"></i> Verifikasi Pembayaran</button>
                        <label class="payment-reject-label" for="catatan_pembayaran">Alasan penolakan</label>
                        <input class="payment-reject-input" type="text" id="catatan_pembayaran" name="catatan_pembayaran" maxlength="255" placeholder="Isi jika pembayaran ditolak">
                        <button class="payment-action reject" name="status_pembayaran" value="{{ \App\Models\Pesanan::PEMBAYARAN_DITOLAK }}"><i class="bi bi-x-circle"></i> Tolak Pembayaran</button>
                    </form>
                @elseif($pembayaranTerpilih->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR && $pembayaranTerpilih->metode_pembayaran !== 'Bayar di tempat (COD)')
                    <div class="payments-muted mt-2">Menunggu customer mengunggah bukti pembayaran. Pembayaran belum dapat diverifikasi.</div>
                @elseif($pembayaranTerpilih->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_COD && $pembayaranTerpilih->status_pesanan === \App\Models\Pesanan::STATUS_SELESAI)
                    <form action="{{ route('pembayaran.update', $pembayaranTerpilih->utama) }}" method="POST" class="payment-actions">@csrf @method('PATCH')
                        <button class="payment-action verify" name="status_pembayaran" value="{{ \App\Models\Pesanan::PEMBAYARAN_LUNAS }}"><i class="bi bi-cash-coin"></i> Konfirmasi uang COD diterima</button>
                    </form>
                @endif
            </aside>
        @else
            <aside class="payments-card payment-detail"><div class="payment-empty"><i class="bi bi-credit-card d-block fs-4 mb-2"></i>Pilih transaksi untuk melihat detail.</div></aside>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        let selectedDate = '';
        $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
            if (settings.nTable.id !== 'payments-table' || !selectedDate) return true;
            const cell = settings.aoData[dataIndex]?.nTr?.cells[3];
            const timestamp = Number(cell?.dataset.order || 0);
            const date = new Date(timestamp * 1000);
            return [date.getFullYear(), String(date.getMonth() + 1).padStart(2, '0'), String(date.getDate()).padStart(2, '0')].join('-') === selectedDate;
        });
        const table = $('#payments-table').DataTable({
            responsive: false,
            pageLength: 10,
            autoWidth: false,
            order: [[3, 'desc']],
            language: { info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data', infoEmpty: 'Tidak ada pembayaran', zeroRecords: 'Pembayaran tidak ditemukan.', emptyTable: 'Belum ada transaksi pembayaran.', paginate: { previous: 'Sebelumnya', next: 'Berikutnya' } },
        });
        document.getElementById('payment-search').addEventListener('input', (event) => table.search(event.currentTarget.value).draw());
        document.getElementById('payment-date').addEventListener('change', (event) => { selectedDate = event.currentTarget.value; table.draw(); });
        document.getElementById('payment-check-all').addEventListener('change', (event) => document.querySelectorAll('.payment-row-check').forEach((checkbox) => { checkbox.checked = event.currentTarget.checked; }));
    });
</script>
@endsection