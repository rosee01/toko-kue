@extends('layouts.customer')

@section('title', 'Pesanan Saya - Toko Kue')

@push('styles')
<style>
    .order-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
    .order-tab { border: 1px solid #eaded7; background: #fff; color: var(--muted); border-radius: 25px; padding: 8px 15px; font-weight: 600; font-size: 14px; cursor: pointer; transition: .2s; }
    .order-tab:hover { background: #ecddd2; }
    .order-tab.active { background: var(--brown); color: #fff; }
    .tab-count { display: inline-block; min-width: 22px; padding: 0 6px; margin-left: 6px; border-radius: 11px; background: rgba(0,0,0,.08); font-size: 12px; text-align: center; }
    .order-tab.active .tab-count { background: rgba(255,255,255,.25); }

    .order-card { background: #fff; border: 1px solid #eaded7; border-radius: 16px; padding: 18px 20px; margin-bottom: 14px; box-shadow: 0 4px 14px rgba(77,48,38,.035); }
    .order-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
    .order-doc { font-size: 28px; color: var(--brown-light); }
    .order-kode { font-weight: 700; color: var(--brown-dark); word-break: break-all; }
    .order-tgl { font-size: 13px; color: var(--muted); }
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; white-space: nowrap; }
    .status-pending { background: #f5e6e8; color: #b0636f; }
    .status-diproses { background: #fdf0d5; color: #b7791f; }
    .status-siap-diantar { background: #e6f1ff; color: #4d83c4; }
    .status-dalam-pengantaran { background: #e2f3ef; color: #438d7e; }
    .status-selesai { background: #e3f0e7; color: #4f7a5c; }
    .status-dibatalkan { background: #fde9e7; color: #c76860; }

    .order-body { display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; flex-wrap: wrap; }
    .order-item { display: flex; align-items: center; gap: 14px; margin-bottom: 10px; }
    .order-item img { width: 68px; height: 68px; object-fit: cover; object-position: center 85%; border-radius: 12px; background: #fbf5f1; }
    .order-item img.cake-photo-crop { transform: scale(1.7); transform-origin: center 90%; }
    .order-item-name { font-weight: 700; color: var(--brown-dark); }
    .order-item-qty, .order-more { font-size: 13px; color: var(--muted); }
    .order-side { text-align: right; min-width: 170px; }
    .order-total-label { font-size: 13px; color: var(--muted); }
    .order-total { font-size: 22px; font-weight: 700; color: var(--brown-dark); margin-bottom: 8px; }
    .btn-detail { border: 0; background: var(--brown); color: #fff; border-radius: 10px; padding: 9px 22px; font-weight: 600; font-size: 14px; cursor: pointer; width: 100%; }
    .btn-detail:hover { background: var(--brown-dark); }

    .order-detail { border-top: 1px dashed #e4d7cf; margin-top: 16px; padding-top: 16px; font-size: 14px; }
    .order-detail table { width: 100%; margin-bottom: 12px; }
    .order-detail th { color: var(--muted); font-weight: 600; font-size: 13px; padding: 4px 0; }
    .order-detail td { padding: 4px 0; }
    .info-penerima { background: #fbf5f1; border-radius: 12px; padding: 12px 14px; }
    .info-penerima div { padding: 2px 0; }
    .payment-history { margin-top:12px; padding:12px; border:1px solid #eaded7; border-radius:10px; background:#fffaf6; }
    .payment-history input { display:block; max-width:100%; margin:8px 0; font-size:13px; }
    .payment-history button { border:0; border-radius:8px; padding:8px 12px; background:var(--brown); color:#fff; font-size:13px; font-weight:600; }
    .kosong-info { text-align: center; padding: 50px 20px; color: var(--muted); }
    .kosong-info i { font-size: 56px; color: var(--brown-light); }

    @media (max-width: 600px) {
        .order-tabs { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 8px; margin-right: -4px; scrollbar-width: thin; }
        .order-tab { flex: 0 0 auto; padding: 8px 12px; }
        .order-card { padding: 15px; }
        .order-top { align-items: flex-start; }
        .order-top > .d-flex { gap: 9px !important; }
        .order-doc { font-size: 23px; }
        .status-pill { padding: 5px 9px; font-size: 11px; }
        .order-side { text-align: left; width: 100%; }
        .order-side .btn-detail { width: auto; min-width: 110px; }
        .order-detail { overflow-x: auto; }
        .order-detail table { min-width: 450px; }
    }
</style>
@endpush

@section('content')
@php
    $placeholder = asset('images/login-hero.jpg');
    $tabs = [
        'Pending' => 'Menunggu',
        'Diproses' => 'Diproses',
        'Siap Diantar' => 'Siap diantar',
        'Dalam Pengantaran' => 'Dalam pengantaran',
        'Selesai' => 'Selesai',
        'Dibatalkan' => 'Dibatalkan',
    ];
    $ikon = [
        'Pending' => 'bi-hourglass-split',
        'Diproses' => 'bi-clock',
        'Siap Diantar' => 'bi-box-seam',
        'Dalam Pengantaran' => 'bi-truck',
        'Selesai' => 'bi-check-circle',
        'Dibatalkan' => 'bi-x-circle',
    ];
@endphp
<section class="page-wrap">
    <div class="container-custom" style="max-width: 900px">
        <div class="panel">
            <h1 class="panel-title"><i class="bi bi-card-checklist"></i> Pesanan Saya</h1>
            <p class="panel-sub">Lihat riwayat dan status pesanan Anda di sini.</p>

            @if($orders->isEmpty())
                <div class="kosong-info">
                    <i class="bi bi-bag-x"></i>
                    <p class="mt-3 mb-4">Anda belum punya pesanan.</p>
                    <a href="{{ route('beranda') }}#menu" class="btn-detail" style="display:inline-block;width:auto;padding:12px 28px">Mulai Belanja</a>
                </div>
            @else
                <div class="order-tabs">
                    <button type="button" class="order-tab active" data-filter="all">Semua <span class="tab-count">{{ $orders->count() }}</span></button>
                    @foreach($tabs as $status => $label)
                        <button type="button" class="order-tab" data-filter="{{ $status }}">{{ $label }} <span class="tab-count">{{ $orders->where('status', $status)->count() }}</span></button>
                    @endforeach
                </div>

                @foreach($orders as $i => $o)
                    <div class="order-card" data-status="{{ $o['status'] }}">
                        <div class="order-top">
                            <div class="d-flex gap-3 align-items-start">
                                <i class="bi bi-file-earmark-text order-doc"></i>
                                <div>
                                    <div class="order-kode">#{{ $o['kode'] }}</div>
                                    <div class="order-tgl">{{ $o['tanggal']->translatedFormat('d M Y, H:i') }}</div>
                                </div>
                            </div>
                            <span class="status-pill status-{{ \Illuminate\Support\Str::slug($o['status']) }}">
                                <i class="bi {{ $ikon[$o['status']] ?? 'bi-circle' }}"></i> {{ $tabs[$o['status']] ?? $o['status'] }}
                            </span>
                        </div>

                        <div class="order-body">
                            <div>
                                @foreach($o['items']->take(2) as $item)
                                    <div class="order-item">
                                        <img src="{{ $item->produk?->foto ? asset('storage/' . $item->produk->foto) : $placeholder }}" alt="{{ $item->menu }}" class="{{ $item->produk?->foto ? '' : 'cake-photo-crop' }}">
                                        <div>
                                            <div class="order-item-name">{{ $item->menu }}</div>
                                            <div class="order-item-qty">{{ $item->jumlah }} pcs</div>
                                        </div>
                                    </div>
                                @endforeach
                                @if($o['items']->count() > 2)
                                    <div class="order-more">+{{ $o['items']->count() - 2 }} produk lainnya</div>
                                @endif
                            </div>
                            <div class="order-side">
                                <div class="order-total-label">Total Pesanan</div>
                                <div class="order-total">Rp {{ number_format($o['total'], 0, ',', '.') }}</div>
                                <div class="order-total-label mb-2">Pembayaran: {{ $o['label_metode_pembayaran'] }}</div>
                                <button type="button" class="btn-detail" data-bs-toggle="collapse" data-bs-target="#detail-{{ $i }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </div>
                        </div>

                        <div class="collapse" id="detail-{{ $i }}">
                            <div class="order-detail">
                                <table>
                                    <thead>
                                        <tr><th>Produk</th><th class="text-center">Jumlah</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($o['items'] as $item)
                                            <tr>
                                                <td>{{ $item->menu }}</td>
                                                <td class="text-center">{{ $item->jumlah }}</td>
                                                <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                                <td class="text-end">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="info-penerima">
                                    <div><strong>Penerima:</strong> {{ $o['nama'] }}</div>
                                    <div><strong>Telepon:</strong> {{ $o['telepon'] ?? '-' }}</div>
                                    <div><strong>Alamat:</strong> {{ $o['alamat'] ?? '-' }}</div>
                                    <div><strong>Jenis pengiriman:</strong> {{ $o['jenis_pengiriman'] ?? '-' }}</div>
                                    <div><strong>Jarak rute:</strong> {{ $o['jarak_pengiriman_km'] !== null ? number_format((float) $o['jarak_pengiriman_km'], 2, ',', '.').' km' : '-' }}</div>
                                    <div><strong>Jadwal diminta:</strong> {{ $o['jadwal_diminta']?->format('d M Y, H:i') ?? '-' }}</div>
                                    <div><strong>Jadwal toko:</strong> {{ $o['jadwal_dikonfirmasi'] ? $o['jadwal_pengiriman']?->format('d M Y, H:i') : 'Menunggu konfirmasi admin' }}</div>
                                    @if($o['driver'] && in_array($o['status'], [\App\Models\Pesanan::STATUS_DALAM_PENGANTARAN, \App\Models\Pesanan::STATUS_SELESAI], true))
                                        <div><strong>Driver:</strong> {{ $o['driver']->nama }} · {{ $o['driver']->no_telepon }}</div>
                                    @endif
                                    @if($o['catatan'])
                                        <div><strong>Catatan:</strong> {{ $o['catatan'] }}</div>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between"><span>Subtotal produk</span><span>Rp {{ number_format($o['subtotal'], 0, ',', '.') }}</span></div>
                                    <div class="d-flex justify-content-between"><span>Ongkos kirim</span><span>Rp {{ number_format($o['ongkir'], 0, ',', '.') }}</span></div>
                                    <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-1"><span>Total pembayaran</span><span>Rp {{ number_format($o['total'], 0, ',', '.') }}</span></div>
                                </div>
                                <div class="payment-history">
                                    <div><strong>Metode pembayaran:</strong> {{ $o['label_metode_pembayaran'] }}</div>
                                    <div><strong>Status pembayaran:</strong> {{ $o['status_pembayaran'] }}</div>
                                    @if($o['catatan_pembayaran'])
                                        <div class="text-danger"><strong>Catatan admin:</strong> {{ $o['catatan_pembayaran'] }}</div>
                                    @endif
                                    @if(in_array($o['metode_pembayaran'], [\App\Models\Pesanan::METODE_TRANSFER_BANK, \App\Models\Pesanan::METODE_E_WALLET], true))
                                        @if($o['status_pembayaran'] === \App\Models\Pesanan::PEMBAYARAN_DIBATALKAN)
                                            <div class="text-muted mt-1"><i class="bi bi-x-circle"></i> Pesanan dibatalkan; pembayaran tidak perlu dilakukan.</div>
                                        @elseif(!$o['ongkir_dikonfirmasi'] || ($o['jenis_pengiriman'] !== null && !$o['jadwal_dikonfirmasi']))
                                            <div class="text-warning mt-1"><i class="bi bi-hourglass-split"></i> Menunggu admin mengonfirmasi ongkos kirim dan jadwal sebelum pembayaran.</div>
                                        @elseif($o['status_pembayaran'] === \App\Models\Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI)
                                            <div class="text-success mt-1"><i class="bi bi-hourglass-split"></i> Bukti diterima, menunggu verifikasi admin.</div>
                                        @elseif($o['status_pembayaran'] === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                                            <div class="text-success mt-1"><i class="bi bi-check-circle"></i> Pembayaran terverifikasi.</div>
                                        @elseif($o['items']->first()->kode_pesanan)
                                            @if($o['metode_pembayaran'] === \App\Models\Pesanan::METODE_TRANSFER_BANK)
                                                <div class="mt-2"><strong>Tujuan transfer:</strong> {{ $informasiPembayaran['bank']['nama'] ?: 'Belum diatur' }} · {{ $informasiPembayaran['bank']['rekening'] ?: 'Belum diatur' }} · a.n. {{ $informasiPembayaran['bank']['pemilik'] ?: 'Belum diatur' }}</div>
                                            @else
                                                <div class="mt-2"><strong>Tujuan e-wallet:</strong> {{ $informasiPembayaran['ewallet']['provider'] ?: 'Belum diatur' }} · {{ $informasiPembayaran['ewallet']['nomor'] ?: 'Belum diatur' }} · a.n. {{ $informasiPembayaran['ewallet']['pemilik'] ?: 'Belum diatur' }}</div>
                                            @endif
                                            <form action="{{ route('order.bukti-pembayaran', $o['items']->first()->kode_pesanan) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <label class="form-label mt-2 mb-1" for="bukti-{{ $i }}">Unggah bukti transfer / e-wallet</label>
                                                <input id="bukti-{{ $i }}" name="bukti_pembayaran" type="file" accept="image/jpeg,image/png,image/webp" required>
                                                @error('bukti_pembayaran')<span class="text-danger small">{{ $message }}</span>@enderror
                                                <small class="d-block mb-2 text-muted">JPG, PNG, atau WEBP; maksimal 5 MB.</small>
                                                <button type="submit"><i class="bi bi-cloud-arrow-up"></i> {{ $o['status_pembayaran'] === \App\Models\Pesanan::PEMBAYARAN_DITOLAK ? 'Kirim ulang bukti' : 'Kirim bukti pembayaran' }}</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div id="filterKosong" class="kosong-info" style="display:none">
                    <p class="mb-0">Tidak ada pesanan dengan status ini.</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.order-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.order-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            let tampil = 0;
            document.querySelectorAll('.order-card').forEach(card => {
                const cocok = filter === 'all' || card.dataset.status === filter;
                card.style.display = cocok ? '' : 'none';
                if (cocok) tampil++;
            });
            document.getElementById('filterKosong').style.display = tampil ? 'none' : '';
        });
    });
</script>
@endpush