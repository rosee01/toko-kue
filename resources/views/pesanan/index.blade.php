@extends('layouts.template')

@section('page-title', 'Pesanan')
@section('tanpa-judul', true)

@section('content')
<style>
    .orders-page { padding:8px 0 22px; color:#3f302b; }
    .orders-heading { display:flex; align-items:center; gap:10px; margin:0 4px 12px; }
    .orders-heading-icon { color:#8b6250; font-size:1.5rem; }
    .orders-heading h1 { margin:0; color:#50392f; font:600 1.42rem 'Playfair Display',serif; }
    .orders-heading p { margin:2px 0 0; color:#89786e; font-size:.66rem; }
    .orders-tabs { display:flex; gap:8px; overflow:auto; margin-bottom:9px; padding:9px; border:1px solid #efe4dd; border-radius:8px; background:#fff; scrollbar-width:thin; }
    .orders-tab { display:flex; align-items:center; gap:7px; padding:6px 12px; border:1px solid transparent; border-radius:20px; color:#806b5e; font-size:.61rem; text-decoration:none; white-space:nowrap; }
    .orders-tab .count { display:inline-grid; min-width:15px; height:15px; place-items:center; padding:0 4px; border-radius:12px; background:rgba(70,50,40,.1); font-size:.52rem; }
    .orders-tab.all { background:#fbe8e9; color:#b45f69; }
    .orders-tab.new { background:#e6f1ff; color:#4d83c4; }
    .orders-tab.processing { background:#fff0dc; color:#b17a2c; }
    .orders-tab.ready { background:#f1e9fb; color:#8666b2; }
    .orders-tab.delivery { background:#e2f3ef; color:#438d7e; }
    .orders-tab.done { background:#e5f4e8; color:#4d8e59; }
    .orders-tab.cancelled { background:#fde9e7; color:#c76860; }
    .orders-tab.active { border-color:currentColor; box-shadow:inset 0 0 0 1px currentColor; }
    .orders-filters { display:flex; align-items:center; gap:8px; margin-bottom:9px; padding:9px; border:1px solid #efe4dd; border-radius:8px; background:#fff; }
    .orders-search { position:relative; flex:1; min-width:160px; }
    .orders-search i { position:absolute; top:50%; left:10px; color:#92847b; transform:translateY(-50%); }
    .orders-search input,.orders-date,.orders-sort { height:32px; border:1px solid #e9dfd9; border-radius:6px; background:#fff; color:#5f5149; font-size:.59rem; }
    .orders-search input { width:100%; padding:5px 8px 5px 29px; }
    .orders-date,.orders-sort { min-width:122px; padding:5px 8px; }
    .orders-search input:focus,.orders-date:focus,.orders-sort:focus { border-color:#b88b76; outline:2px solid rgba(184,139,118,.15); }
    .orders-layout { display:grid; grid-template-columns:minmax(0,1fr) 245px; gap:9px; align-items:start; }
    .orders-card { min-width:0; padding:10px; border:1px solid #efe4dd; border-radius:8px; background:#fff; box-shadow:0 2px 9px rgba(80,48,34,.025); }
    .orders-table { margin:0 !important; border-collapse:separate; border-spacing:0; }
    .orders-table thead th { padding:8px 6px !important; border-top:1px solid #eee5df !important; border-bottom:1px solid #e9dfd9 !important; background:#faf7f5 !important; color:#70594c !important; font-size:.55rem !important; font-weight:600 !important; white-space:nowrap; }
    .orders-table thead th:first-child { border-left:1px solid #eee5df !important; border-radius:6px 0 0 0; }
    .orders-table thead th:last-child { border-right:1px solid #eee5df !important; border-radius:0 6px 0 0; }
    .orders-table tbody td { padding:7px 6px !important; border-color:#f0e9e4 !important; color:#4d4038 !important; font-size:.57rem !important; vertical-align:middle !important; }
    .orders-table tbody tr:hover td { background:#fcf9f7; }
    .orders-table tbody tr.selected td { background:#fff9f3; }
    .orders-check { width:13px; height:13px; accent-color:#7b5343; }
    .orders-code { color:#554236; font-weight:600; text-decoration:underline; white-space:nowrap; }
    .orders-customer { display:block; color:#49382f; font-size:.58rem; font-weight:600; white-space:nowrap; }
    .orders-phone,.orders-date-text { display:block; color:#958880; font-size:.52rem; white-space:nowrap; }
    .orders-status,.orders-payment { display:inline-block; padding:4px 7px; border-radius:20px; font-size:.52rem; white-space:nowrap; }
    .orders-status.pending { background:#fff0dc; color:#b17a2c; }
    .orders-status.processing { background:#fff0dc; color:#b17a2c; }
    .orders-status.ready { background:#f1e9fb; color:#8666b2; }
    .orders-status.delivery { background:#e4f2fb; color:#4d83c4; }
    .orders-status.done { background:#e5f4e8; color:#4d8e59; }
    .orders-status.cancelled { background:#fde9e7; color:#c76860; }
    .orders-payment.paid,.orders-payment.cod { background:#e3f3e5; color:#468955; }
    .orders-payment.unpaid,.orders-payment.rejected { background:#fde6e5; color:#c64f4b; }
    .orders-payment.waiting { background:#e9efff; color:#536eaf; }
    .orders-see { display:inline-flex; align-items:center; gap:5px; padding:4px 8px; border:1px solid #eadfd8; border-radius:5px; color:#80604e; font-size:.54rem; text-decoration:none; white-space:nowrap; }
    .orders-see:hover { background:#f8f1ed; }
    .orders-table-wrap .dataTables_wrapper { font-size:.58rem; }
    .orders-table-wrap .dataTables_filter,.orders-table-wrap .dataTables_length { display:none; }
    .orders-table-wrap .dataTables_info { padding-top:8px !important; color:#8b7d74; font-size:.53rem; }
    .orders-table-wrap .dataTables_paginate { padding-top:4px !important; }
    .orders-table-wrap .pagination { gap:3px; }
    .orders-table-wrap .pagination .page-link { border:1px solid #eee4de; border-radius:5px !important; color:#70594c; font-size:.58rem; padding:3px 7px; }
    .orders-table-wrap .page-item.active .page-link { border-color:#7b5343; background:#7b5343; color:#fff; }
    .order-detail { position:sticky; top:66px; padding:12px; }
    .order-detail-head { display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:10px; }
    .order-detail-head h2 { margin:0; color:#60483b; font-size:.7rem; font-weight:600; }
    .order-detail-close { color:#9a7b6a; text-decoration:none; }
    .order-detail-code { color:#49382f; font-size:.64rem; font-weight:700; }
    .order-detail-date { margin-top:4px; color:#8b7d74; font-size:.55rem; }
    .order-detail-contact { display:grid; gap:8px; margin:10px 0; padding-bottom:10px; border-bottom:1px solid #f1e9e4; }
    .order-detail-contact-row { display:flex; align-items:flex-start; gap:8px; color:#6b5a50; font-size:.57rem; line-height:1.35; }
    .order-detail-contact-row i { width:13px; color:#997563; }
    .orders-side-whatsapp { display:inline-flex; align-items:center; gap:4px; margin-top:4px; color:#4f9c61; font-size:.53rem; text-decoration:none; }
    .orders-side-whatsapp i { color:#4f9c61; }
    .order-detail-section-title { margin:9px 0 7px; color:#49382f; font-size:.59rem; font-weight:600; }
    .order-detail-item { display:flex; align-items:center; gap:7px; margin-bottom:7px; }
    .order-detail-thumb { display:grid; place-items:center; width:37px; height:37px; flex:none; overflow:hidden; border-radius:6px; background:#f4ece6; color:#9a715d; }
    .order-detail-thumb img { width:100%; height:100%; object-fit:cover; }
    .order-detail-item-copy { flex:1; min-width:0; }
    .order-detail-item-name { overflow:hidden; color:#55463e; font-size:.56rem; font-weight:600; text-overflow:ellipsis; white-space:nowrap; }
    .order-detail-item-meta { color:#8f827a; font-size:.51rem; }
    .order-detail-stock { color:#468955; font-size:.49rem; white-space:nowrap; }
    .order-summary { margin-top:9px; padding:8px 0; border-top:1px solid #f1e9e4; }
    .order-summary-row { display:flex; justify-content:space-between; padding:3px 0; color:#887970; font-size:.56rem; }
    .order-summary-row.total { margin-top:3px; padding-top:7px; border-top:1px solid #f1e9e4; color:#60483b; font-size:.66rem; font-weight:700; }
    .order-timeline { margin:8px 0 10px 5px; padding-left:12px; border-left:1px solid #ebddd4; }
    .order-timeline-step { position:relative; padding:0 0 9px 3px; color:#a1958d; font-size:.53rem; }
    .order-timeline-step::before { position:absolute; top:2px; left:-17px; width:8px; height:8px; border:1px solid #e5d7cd; border-radius:50%; background:#fff; content:''; }
    .order-timeline-step.done { color:#6d8c70; }
    .order-timeline-step.done::before { border-color:#71b77d; background:#71b77d; }
    .order-timeline-step.current { color:#79533f; font-weight:600; }
    .order-timeline-step.current::before { border-color:#df7d86; background:#df7d86; box-shadow:0 0 0 2px #fbe8e9; }
    .order-payment-row { display:flex; justify-content:space-between; align-items:center; gap:6px; margin:7px 0 9px; }
    .order-detail-actions { display:grid; grid-template-columns:1fr 1fr; gap:6px; padding-top:9px; border-top:1px solid #f1e9e4; }
    .order-action { display:flex; min-height:30px; align-items:center; justify-content:center; gap:5px; padding:5px 6px; border:1px solid #e7dcd5; border-radius:5px; background:#fff; color:#80604e; font-size:.53rem; }
    .order-action.primary { border-color:#7b5343; background:#7b5343; color:#fff; }
    .order-action.cancel { color:#b25f59; }
    .order-empty { padding:26px 8px; color:#95877f; text-align:center; font-size:.62rem; }
    @media (max-width:1050px) { .orders-layout { grid-template-columns:minmax(0,1fr); } .order-detail { position:static; } }
    @media (max-width:760px) { .orders-tabs { gap:5px; } .orders-filters { flex-wrap:wrap; } .orders-search { flex-basis:100%; } .orders-date,.orders-sort { flex:1; } }
    @media (max-width:540px) { .orders-page { padding-top:6px; } .orders-heading h1 { font-size:1.3rem; } .orders-layout { gap:8px; } .orders-card { padding:8px; } .order-detail { padding:10px; } }
</style>

<div class="orders-page">
    <header class="orders-heading">
        <i class="orders-heading-icon bi bi-receipt-cutoff" aria-hidden="true"></i>
        <div><h1>Pesanan</h1><p>Kelola semua pesanan pelanggan dengan mudah.</p></div>
    </header>

    <nav class="orders-tabs" aria-label="Filter status pesanan">
        <a href="{{ route('pesanan.index') }}" class="orders-tab all {{ !$statusFilter ? 'active' : '' }}">Semua <span class="count">{{ $jumlahSemua }}</span></a>
        @php
            $tabStatus = [
                \App\Models\Pesanan::STATUS_PENDING => ['Pesanan Baru', 'new'],
                \App\Models\Pesanan::STATUS_DIPROSES => ['Diproses', 'processing'],
                \App\Models\Pesanan::STATUS_SIAP_DIANTAR => ['Siap Diantar', 'ready'],
                \App\Models\Pesanan::STATUS_DALAM_PENGANTARAN => ['Dalam Pengantaran', 'delivery'],
                \App\Models\Pesanan::STATUS_SELESAI => ['Selesai', 'done'],
                \App\Models\Pesanan::STATUS_DIBATALKAN => ['Dibatalkan', 'cancelled'],
            ];
        @endphp
        @foreach($tabStatus as $status => [$label, $color])
            <a href="{{ route('pesanan.index', ['status' => $status]) }}" class="orders-tab {{ $color }} {{ $statusFilter === $status ? 'active' : '' }}">{{ $label }} <span class="count">{{ $jumlahPerStatus[$status] ?? 0 }}</span></a>
        @endforeach
    </nav>

    <div class="orders-filters">
        <label class="orders-search"><i class="bi bi-search"></i><input id="orders-search" type="search" placeholder="Cari nomor pesanan, nama pelanggan, atau nomor HP..." aria-label="Cari pesanan"></label>
        <input class="orders-date" type="date" id="orders-date-filter" aria-label="Filter tanggal">
        <select class="orders-sort" id="orders-sort" aria-label="Urutkan pesanan"><option value="newest">Urutkan: Terbaru</option><option value="oldest">Terlama</option><option value="total-desc">Total terbesar</option><option value="total-asc">Total terkecil</option></select>
    </div>

    <div class="orders-layout">
        <section class="orders-card orders-table-wrap" aria-label="Daftar pesanan">
            <div class="table-responsive">
                <table class="table orders-table w-100" id="orders-table">
                    <thead><tr><th class="text-center"><input id="orders-check-all" type="checkbox" class="orders-check" aria-label="Pilih semua"></th><th>No. Pesanan</th><th>Pelanggan</th><th>Tanggal</th><th>Total</th><th>Status Pesanan</th><th>Status Pembayaran</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach($pesanan as $item)
                            @php
                                $statusClass = match ($item->status) {
                                    \App\Models\Pesanan::STATUS_PENDING => 'pending',
                                    \App\Models\Pesanan::STATUS_DIPROSES => 'processing',
                                    \App\Models\Pesanan::STATUS_SIAP_DIANTAR => 'ready',
                                    \App\Models\Pesanan::STATUS_DALAM_PENGANTARAN => 'delivery',
                                    \App\Models\Pesanan::STATUS_SELESAI => 'done',
                                    default => 'cancelled',
                                };
                                $paymentClass = match ($item->status_pembayaran) {
                                    \App\Models\Pesanan::PEMBAYARAN_LUNAS => 'paid',
                                    \App\Models\Pesanan::PEMBAYARAN_COD => 'cod',
                                    \App\Models\Pesanan::PEMBAYARAN_DITOLAK => 'rejected',
                                    \App\Models\Pesanan::PEMBAYARAN_DIBATALKAN => 'rejected',
                                    \App\Models\Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI => 'waiting',
                                    default => 'unpaid',
                                };
                            @endphp
                            <tr class="{{ $pesananTerpilih?->key === $item->key ? 'selected' : '' }}">
                                <td class="text-center"><input class="orders-check order-row-check" type="checkbox" aria-label="Pilih {{ $item->kode }}"></td>
                                <td data-search="{{ $item->kode }}"><a class="orders-code" href="{{ route('pesanan.index', ['status' => $statusFilter, 'pilih' => $item->key]) }}">#{{ $item->kode }}</a></td>
                                <td data-search="{{ $item->nama_pelanggan }} {{ $item->no_telepon }}"><span class="orders-customer">{{ $item->nama_pelanggan }}</span><span class="orders-phone">{{ $item->no_telepon ?: 'Nomor HP belum tersedia' }}</span></td>
                                <td class="orders-date-cell" data-order="{{ $item->created_at?->timestamp }}"><span class="orders-date">{{ $item->created_at?->translatedFormat('d M Y') }}</span><span class="orders-date-text">{{ $item->created_at?->format('H:i') }}</span></td>
                                <td data-order="{{ $item->total }}">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td><span class="orders-status {{ $statusClass }}">{{ $item->status === \App\Models\Pesanan::STATUS_PENDING ? 'Pesanan Baru' : $item->status }}</span></td>
                                <td><span class="orders-payment {{ $paymentClass }}">{{ $item->status_pembayaran }}</span></td>
                                <td><a class="orders-see" href="{{ route('pesanan.index', ['status' => $statusFilter, 'pilih' => $item->key]) }}"><i class="bi bi-eye"></i> Lihat</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        @if($pesananTerpilih)
            @php
                $teksWhatsApp = "Halo {$pesananTerpilih->nama_pelanggan}, pesanan #{$pesananTerpilih->kode} sedang kami proses.";
                $detailPaymentClass = match ($pesananTerpilih->status_pembayaran) {
                    \App\Models\Pesanan::PEMBAYARAN_LUNAS => 'paid',
                    \App\Models\Pesanan::PEMBAYARAN_COD => 'cod',
                    \App\Models\Pesanan::PEMBAYARAN_DITOLAK => 'rejected',
                    \App\Models\Pesanan::PEMBAYARAN_DIBATALKAN => 'rejected',
                    \App\Models\Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI => 'waiting',
                    default => 'unpaid',
                };
                $progres = [
                    \App\Models\Pesanan::STATUS_PENDING,
                    \App\Models\Pesanan::STATUS_DIPROSES,
                    \App\Models\Pesanan::STATUS_SIAP_DIANTAR,
                    \App\Models\Pesanan::STATUS_DALAM_PENGANTARAN,
                    \App\Models\Pesanan::STATUS_SELESAI,
                ];
                $posisiProgres = array_search($pesananTerpilih->status, $progres, true);
                $statusBerikutnya = match ($pesananTerpilih->status) {
                    \App\Models\Pesanan::STATUS_PENDING => \App\Models\Pesanan::STATUS_DIPROSES,
                    \App\Models\Pesanan::STATUS_DIPROSES => \App\Models\Pesanan::STATUS_SIAP_DIANTAR,
                    default => null,
                };
            @endphp
            <aside class="orders-card order-detail" aria-label="Detail pesanan">
                <div class="order-detail-head"><h2>Detail Pesanan</h2><a class="order-detail-close" href="{{ route('pesanan.index', ['status' => $statusFilter]) }}" aria-label="Tutup detail"><i class="bi bi-x-lg"></i></a></div>
                <div class="order-detail-code">#{{ $pesananTerpilih->kode }}</div>
                <div class="order-detail-date"><i class="bi bi-calendar-event me-1"></i>{{ $pesananTerpilih->created_at?->translatedFormat('d M Y, H:i') }}</div>

                <div class="order-detail-contact">
                    <div class="order-detail-contact-row"><i class="bi bi-person"></i><span>{{ $pesananTerpilih->nama_pelanggan }}<br><span class="text-muted">{{ $pesananTerpilih->no_telepon ?: 'Nomor HP belum tersedia' }}</span>@if($pesananTerpilih->utama->whatsapp_url)<br><a href="{{ $pesananTerpilih->utama->whatsapp_url }}?text={{ rawurlencode($teksWhatsApp) }}" target="_blank" rel="noopener" class="orders-side-whatsapp"><i class="bi bi-whatsapp"></i> Hubungi via WhatsApp</a>@endif</span></div>
                    <div class="order-detail-contact-row"><i class="bi bi-geo-alt"></i><span>{{ $pesananTerpilih->alamat_pengiriman ?: 'Alamat belum tersedia' }}</span></div>
                    @if($pesananTerpilih->catatan)<div class="order-detail-contact-row"><i class="bi bi-chat-left-text"></i><span>{{ $pesananTerpilih->catatan }}</span></div>@endif
                </div>

                <h3 class="order-detail-section-title">Produk yang Dipesan</h3>
                @foreach($pesananTerpilih->items as $item)
                    <div class="order-detail-item">
                        <span class="order-detail-thumb">@if($item->produk?->foto)<img src="{{ asset('storage/' . $item->produk->foto) }}" alt="{{ $item->menu }}">@else<i class="bi bi-cake2"></i>@endif</span>
                        <span class="order-detail-item-copy"><span class="order-detail-item-name d-block">{{ $item->menu }}</span><span class="order-detail-item-meta">{{ $item->jumlah }} pcs × Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span></span>
                        @if($item->produk)<span class="order-detail-stock">Stok: {{ $item->produk->stok }}</span>@endif
                    </div>
                @endforeach

                <div class="order-summary">
                    <div class="order-summary-row"><span>Subtotal Produk</span><span>Rp {{ number_format($pesananTerpilih->subtotal, 0, ',', '.') }}</span></div>
                    <div class="order-summary-row"><span>Ongkir</span><span>Rp {{ number_format($pesananTerpilih->ongkir, 0, ',', '.') }}</span></div>
                    <div class="order-summary-row total"><span>Total</span><span>Rp {{ number_format($pesananTerpilih->total, 0, ',', '.') }}</span></div>
                </div>
                <div class="payments-muted">
                    Pengiriman: {{ $pesananTerpilih->jenis_pengiriman ?? 'Lama / belum dipilih' }}.
                    @if($pesananTerpilih->jarak_pengiriman_km !== null)
                        Jarak rute: {{ number_format((float) $pesananTerpilih->jarak_pengiriman_km, 2, ',', '.') }} km.
                    @endif
                    Jadwal diminta: {{ $pesananTerpilih->jadwal_diminta?->format('d M Y, H:i') ?? '-' }}.
                    @if($pesananTerpilih->jadwal_dikonfirmasi)
                        Jadwal dikonfirmasi: {{ $pesananTerpilih->jadwal_pengiriman?->format('d M Y, H:i') }}.
                    @endif
                </div>
                <div class="payments-muted mb-2">Ongkir dan jadwal {{ $pesananTerpilih->utama->ongkir_dikonfirmasi && $pesananTerpilih->utama->jadwal_dikonfirmasi ? 'sudah dikonfirmasi' : 'belum dikonfirmasi' }}.</div>
                @if($pesananTerpilih->status === \App\Models\Pesanan::STATUS_PENDING
                    && !in_array($pesananTerpilih->status_pembayaran, [
                        \App\Models\Pesanan::PEMBAYARAN_LUNAS,
                        \App\Models\Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
                    ], true)
                    && !$pesananTerpilih->bukti_pembayaran)
                    <form action="{{ route('pesanan.ongkir', $pesananTerpilih->utama) }}" method="POST" class="d-grid gap-2 mb-3">@csrf @method('PATCH')
                        <label class="small text-muted" for="ongkir">Konfirmasi ongkos kirim sebelum customer membayar</label>
                        <div class="input-group input-group-sm"><span class="input-group-text">Rp</span><input class="form-control" id="ongkir" name="ongkir" type="number" min="0" max="5000000" step="1" value="{{ $pesananTerpilih->ongkir }}" required></div>
                        <label class="small text-muted" for="jadwal_pengiriman">Konfirmasi jadwal pengiriman</label>
                        <input class="form-control form-control-sm" id="jadwal_pengiriman" name="jadwal_pengiriman" type="datetime-local" min="{{ now()->addMinute()->format('Y-m-d\TH:i') }}" max="{{ now()->addDays(14)->format('Y-m-d\TH:i') }}" value="{{ ($pesananTerpilih->jadwal_diminta ?? now()->addDay())->format('Y-m-d\TH:i') }}" required>
                        @error('ongkir')<span class="small text-danger">{{ $message }}</span>@enderror
                        @error('jadwal_pengiriman')<span class="small text-danger">{{ $message }}</span>@enderror
                        <button type="submit" class="order-action primary"><i class="bi bi-save"></i> Konfirmasi ongkir &amp; jadwal</button>
                    </form>
                @endif

                <h3 class="order-detail-section-title">Status Pesanan</h3>
                @if($pesananTerpilih->status === \App\Models\Pesanan::STATUS_DIBATALKAN)
                    <div class="order-timeline-step current">Pesanan Dibatalkan</div>
                @else
                    <div class="order-timeline">
                        @foreach($progres as $index => $tahap)
                            <div class="order-timeline-step {{ $posisiProgres !== false && $index < $posisiProgres ? 'done' : '' }} {{ $tahap === $pesananTerpilih->status ? 'current' : '' }}">{{ $tahap === \App\Models\Pesanan::STATUS_PENDING ? 'Pesanan Baru' : $tahap }}</div>
                        @endforeach
                    </div>
                @endif

                <h3 class="order-detail-section-title">Status Pembayaran</h3>
                <div class="order-payment-row"><span>Metode</span><strong>{{ $pesananTerpilih->metode_pembayaran }}</strong></div>
                <div class="order-payment-row"><span class="orders-payment {{ $detailPaymentClass }}">{{ $pesananTerpilih->status_pembayaran }}</span>
                    @if($pesananTerpilih->bukti_pembayaran)
                        <a href="{{ route('pembayaran.bukti', $pesananTerpilih->utama) }}" target="_blank" rel="noopener" class="orders-see"><i class="bi bi-image"></i> Lihat bukti</a>
                    @endif
                </div>

                <div class="order-detail-actions">
                    @if($statusBerikutnya)
                        @if($statusBerikutnya === \App\Models\Pesanan::STATUS_DIPROSES && (
                            !$pesananTerpilih->utama->ongkir_dikonfirmasi
                            || $pesananTerpilih->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_DITOLAK
                        ))
                            <span class="text-muted small">
                                @if(!$pesananTerpilih->utama->ongkir_dikonfirmasi || !$pesananTerpilih->utama->jadwal_dikonfirmasi)
                                    Konfirmasi ongkos kirim dan jadwal sebelum memproses pesanan.
                                @else
                                    Tunggu customer mengirim ulang bukti pembayaran yang benar.
                                @endif
                            </span>
                        @else
                            <form action="{{ route('pesanan.status', $pesananTerpilih->utama) }}" method="POST">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $statusBerikutnya }}"><button type="submit" class="order-action primary"><i class="bi bi-arrow-right"></i> {{ $statusBerikutnya === \App\Models\Pesanan::STATUS_DIPROSES ? 'Proses Pesanan' : $statusBerikutnya }}</button></form>
                        @endif
                    @else
                        @if($pesananTerpilih->status === \App\Models\Pesanan::STATUS_SIAP_DIANTAR)
                            @if($pesananTerpilih->utama->kurir_id === null)
                                @if($driversAktif->isNotEmpty())
                                    <form action="{{ route('pesanan.driver', $pesananTerpilih->utama) }}" method="POST" class="d-grid gap-2">@csrf
                                        <label class="small text-muted" for="driver_id">Pilih driver sebelum pengantaran</label>
                                        <select class="form-select form-select-sm" id="driver_id" name="driver_id" required><option value="">Pilih driver aktif</option>@foreach($driversAktif as $driver)<option value="{{ $driver->id }}">{{ $driver->nama }} · {{ $driver->no_telepon }}</option>@endforeach</select>
                                        <button type="submit" class="order-action primary"><i class="bi bi-truck"></i> Tugaskan dan mulai pengantaran</button>
                                    </form>
                                @else
                                    @if($jumlahDriverAktif > 0)
                                        <a href="{{ route('driver.index') }}" class="order-action primary"><i class="bi bi-truck"></i> Semua driver sedang mengantar</a>
                                    @else
                                        <a href="{{ route('driver.index', ['tambah' => 1]) }}" class="order-action primary"><i class="bi bi-person-plus"></i> Tambah driver untuk mengantar</a>
                                    @endif
                                @endif
                            @else
                                <span class="text-muted small">Sudah ditugaskan kepada {{ $pesananTerpilih->driver?->nama ?? 'driver' }}.</span>
                            @endif
                        @elseif($pesananTerpilih->status === \App\Models\Pesanan::STATUS_DALAM_PENGANTARAN)
                            <span class="text-muted small">Pesanan sedang diantar oleh {{ $pesananTerpilih->driver?->nama ?? 'driver' }}. Tandai selesai melalui menu Driver setelah diterima pelanggan.</span>
                        @else
                            <span class="text-muted small">Pesanan ini sudah ditutup dan tidak dapat diedit.</span>
                        @endif
                    @endif
                    @if(in_array($pesananTerpilih->status, [
                        \App\Models\Pesanan::STATUS_PENDING,
                        \App\Models\Pesanan::STATUS_DIPROSES,
                        \App\Models\Pesanan::STATUS_SIAP_DIANTAR,
                    ], true)
                        && !$pesananTerpilih->bukti_pembayaran
                        && !in_array($pesananTerpilih->status_pembayaran, [
                            \App\Models\Pesanan::PEMBAYARAN_LUNAS,
                            \App\Models\Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI,
                        ], true))
                        <form action="{{ route('pesanan.status', $pesananTerpilih->utama) }}" method="POST">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ \App\Models\Pesanan::STATUS_DIBATALKAN }}"><button type="submit" class="order-action cancel"><i class="bi bi-x-circle"></i> Batal Pesanan</button></form>
                    @elseif($pesananTerpilih->bukti_pembayaran && in_array($pesananTerpilih->status, [
                        \App\Models\Pesanan::STATUS_PENDING,
                        \App\Models\Pesanan::STATUS_DIPROSES,
                        \App\Models\Pesanan::STATUS_SIAP_DIANTAR,
                    ], true))
                        <span class="text-muted small">Pesanan belum dapat dibatalkan karena bukti pembayaran sudah dikirim.</span>
                    @endif
                </div>
            </aside>
        @else
            <aside class="orders-card order-detail"><div class="order-empty"><i class="bi bi-receipt"></i><br>Pilih pesanan untuk melihat detail.</div></aside>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        let selectedDate = '';
        $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
            if (settings.nTable.id !== 'orders-table' || !selectedDate) return true;
            const row = settings.aoData[dataIndex]?.nTr;
            const timestamp = Number(row?.querySelector('.orders-date-cell')?.dataset.order || 0);
            const date = new Date(timestamp * 1000);
            const localDate = [date.getFullYear(), String(date.getMonth() + 1).padStart(2, '0'), String(date.getDate()).padStart(2, '0')].join('-');
            return localDate === selectedDate;
        });
        const table = $('#orders-table').DataTable({
            responsive: false,
            pageLength: 10,
            autoWidth: false,
            order: [[3, 'desc']],
            language: {
                info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada pesanan',
                zeroRecords: 'Pesanan tidak ditemukan.',
                emptyTable: 'Belum ada pesanan.',
                paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
            },
        });
        document.getElementById('orders-search').addEventListener('input', (event) => table.search(event.currentTarget.value).draw());
        document.getElementById('orders-sort').addEventListener('change', (event) => {
            const orders = { newest: [[3, 'desc']], oldest: [[3, 'asc']], 'total-desc': [[4, 'desc']], 'total-asc': [[4, 'asc']] };
            table.order(orders[event.currentTarget.value]).draw();
        });
        document.getElementById('orders-date-filter').addEventListener('change', (event) => {
            selectedDate = event.currentTarget.value;
            table.draw();
        });
        document.getElementById('orders-check-all').addEventListener('change', (event) => {
            document.querySelectorAll('.order-row-check').forEach((checkbox) => { checkbox.checked = event.currentTarget.checked; });
        });
    });
</script>
@endsection