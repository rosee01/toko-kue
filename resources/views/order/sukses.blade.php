@extends('layouts.customer')

@section('title', 'Pesanan Berhasil - '.\App\Models\Pengaturan::ambil('nama_toko', config('app.name')))

@push('styles')
<style>
    .checkout-success { min-height:calc(100vh - 80px); padding:50px 0 70px; background:linear-gradient(160deg,#f3e3d6 0%,#f8eee7 55%,#efdccd 100%); }
    .success-card { width:min(680px,calc(100% - 32px)); margin:0 auto; overflow:hidden; border:1px solid #ecdcd2; border-radius:20px; background:rgba(255,255,255,.94); box-shadow:var(--shadow-md); }
    .success-intro { padding:30px 28px 24px; text-align:center; }
    .success-icon { display:grid; place-items:center; width:64px; height:64px; margin:0 auto 14px; border-radius:50%; background:#e6f3e8; color:#4f8e5b; font-size:1.8rem; }
    .success-eyebrow { margin:0 0 5px; color:#6d9872; font-size:.68rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
    .success-intro h1 { margin:0; color:var(--brown-dark); font-size:1.65rem; font-weight:700; }
    .success-intro p { margin:8px 0 0; color:var(--muted); font-size:.88rem; }
    .success-code { margin:0 24px; padding:13px 16px; border:1px dashed #d9c6ba; border-radius:10px; background:#fbf6f2; text-align:center; }
    .success-code span { display:block; color:#89786e; font-size:.68rem; }
    .success-code strong { display:block; margin-top:3px; color:#604536; font-size:1.08rem; letter-spacing:.04em; }
    .success-details { padding:18px 24px 4px; }
    .success-details h2 { margin:0 0 10px; color:#5b4035; font-size:.9rem; font-weight:700; }
    .success-item { display:flex; justify-content:space-between; gap:12px; padding:9px 0; border-bottom:1px solid #f1e9e4; color:#63554d; font-size:.76rem; }
    .success-item-name { min-width:0; }
    .success-item-name small { display:block; margin-top:2px; color:#978a82; font-size:.67rem; }
    .success-item-price { flex:none; color:#594237; font-weight:600; white-space:nowrap; }
    .success-total { display:flex; justify-content:space-between; gap:12px; padding:13px 0; color:#5b4035; font-size:.82rem; font-weight:700; }
    .success-note { margin:0 24px 20px; padding:11px 12px; border-radius:8px; background:#f8f2ed; color:#75645a; font-size:.7rem; line-height:1.5; }
    .success-note i { margin-right:5px; color:#9d715b; }
    .success-payment { margin:0 24px 16px; padding:12px 14px; border:1px solid #eaded7; border-radius:10px; background:#fffaf6; color:#66564d; font-size:.76rem; line-height:1.55; }
    .success-payment strong { color:#50392f; }
    .success-upload { margin:0 24px 16px; padding:13px 14px; border:1px solid #eaded7; border-radius:10px; background:#fff; color:#66564d; font-size:.75rem; }
    .success-upload label { display:block; margin-bottom:7px; color:#50392f; font-weight:700; }
    .success-upload input { display:block; width:100%; margin:8px 0; font-size:.72rem; }
    .success-upload button { min-height:35px; padding:7px 11px; border:0; border-radius:7px; background:#7b5343; color:#fff; font-size:.7rem; font-weight:600; }
    .success-upload-error { display:block; margin-top:5px; color:#b34e4e; font-size:.68rem; }
    .success-payment-title { display:flex; align-items:center; gap:7px; margin-bottom:5px; color:#50392f; font-size:.82rem; font-weight:700; }
    .success-actions { display:flex; justify-content:center; gap:9px; padding:0 24px 24px; }
    .success-button { display:inline-flex; align-items:center; justify-content:center; gap:7px; min-height:40px; padding:9px 15px; border:1px solid var(--brown); border-radius:9px; background:var(--brown); color:#fff; font-size:.75rem; font-weight:600; text-decoration:none; }
    .success-button:hover { border-color:var(--brown-dark); background:var(--brown-dark); color:#fff; }
    .success-button.secondary { background:#fff; color:var(--brown); }
    .success-button.secondary:hover { background:var(--cream); color:var(--brown-dark); }
    @media (max-width:520px) {
        .checkout-success { padding-top:25px; }
        .success-intro { padding:24px 18px 20px; }
        .success-details { padding-right:18px; padding-left:18px; }
        .success-code,.success-note { margin-right:18px; margin-left:18px; }
        .success-payment { margin-right:18px; margin-left:18px; }
        .success-upload { margin-right:18px; margin-left:18px; }
        .success-actions { flex-direction:column; padding-right:18px; padding-left:18px; }
    }
</style>
@endpush

@section('content')
<main class="checkout-success">
    <article class="success-card">
        <header class="success-intro">
            <span class="success-icon"><i class="bi bi-check-lg"></i></span>
            <p class="success-eyebrow">Checkout berhasil</p>
            <h1>Terima kasih sudah memesan!</h1>
            <p>Pesanan Anda sudah kami terima dan sedang menunggu konfirmasi toko.</p>
        </header>

        <div class="success-code">
            <span>Kode pesanan</span>
            <strong>#{{ $kode }}</strong>
        </div>

        <section class="success-details">
            <h2>Ringkasan pesanan</h2>
            @foreach($pesanan as $item)
                <div class="success-item">
                    <span class="success-item-name">
                        {{ $item->menu }}
                        <small>{{ $item->jumlah }} pcs &times; Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</small>
                    </span>
                    <span class="success-item-price">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <div class="success-item"><span>Ongkos kirim</span><span>Rp {{ number_format($ongkir, 0, ',', '.') }}</span></div>
            <div class="success-item"><span>Jenis pengiriman</span><span>{{ $pesanan->first()->jenis_pengiriman }}</span></div>
            <div class="success-item"><span>Jarak rute</span><span>{{ $pesanan->first()->jarak_pengiriman_km !== null ? number_format((float) $pesanan->first()->jarak_pengiriman_km, 2, ',', '.').' km' : '-' }}</span></div>
            <div class="success-item"><span>Jadwal diminta</span><span>{{ $pesanan->first()->jadwal_diminta?->format('d M Y, H:i') }}</span></div>
            <div class="success-item"><span>Jadwal toko</span><span>Menunggu konfirmasi admin</span></div>
            <div class="success-total">
                <span>Total pembayaran</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </section>

        @php($metodePembayaran = $pesanan->first()->metode_pembayaran)
        <section class="success-payment">
            <div class="success-payment-title"><i class="bi bi-credit-card"></i> Metode: {{ $pesanan->first()->label_metode_pembayaran }}</div>
            @if(in_array($metodePembayaran, [\App\Models\Pesanan::METODE_TRANSFER_BANK, \App\Models\Pesanan::METODE_E_WALLET], true) && $pesanan->first()->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_DIBATALKAN)
                Pesanan ini sudah dibatalkan; pembayaran tidak perlu dilakukan.
            @elseif(in_array($metodePembayaran, [\App\Models\Pesanan::METODE_TRANSFER_BANK, \App\Models\Pesanan::METODE_E_WALLET], true) && (!$pesanan->first()->ongkir_dikonfirmasi || ($pesanan->first()->jenis_pengiriman !== null && !$pesanan->first()->jadwal_dikonfirmasi)))
                Ongkir dan waktu pengiriman masih menunggu konfirmasi toko. Detail pembayaran akan tampil di halaman pesanan setelah dikonfirmasi.
            @elseif($metodePembayaran === \App\Models\Pesanan::METODE_TRANSFER_BANK)
                @if($informasiPembayaran['bank']['nama'] && $informasiPembayaran['bank']['rekening'] && $informasiPembayaran['bank']['pemilik'])
                    Transfer ke <strong>{{ $informasiPembayaran['bank']['nama'] }}</strong>, rekening <strong>{{ $informasiPembayaran['bank']['rekening'] }}</strong> atas nama <strong>{{ $informasiPembayaran['bank']['pemilik'] }}</strong>.
                @else
                    Tujuan rekening belum diatur. Hubungi admin{{ $informasiPembayaran['whatsapp'] ? ' di WhatsApp '.$informasiPembayaran['whatsapp'] : ' melalui kontak toko' }}.
                @endif
            @elseif($metodePembayaran === \App\Models\Pesanan::METODE_E_WALLET)
                @if($informasiPembayaran['ewallet']['provider'] && $informasiPembayaran['ewallet']['nomor'] && $informasiPembayaran['ewallet']['pemilik'])
                    Bayar melalui <strong>{{ $informasiPembayaran['ewallet']['provider'] }}</strong> ke nomor <strong>{{ $informasiPembayaran['ewallet']['nomor'] }}</strong> atas nama <strong>{{ $informasiPembayaran['ewallet']['pemilik'] }}</strong>.
                @else
                    Tujuan e-wallet belum diatur. Hubungi admin{{ $informasiPembayaran['whatsapp'] ? ' di WhatsApp '.$informasiPembayaran['whatsapp'] : ' melalui kontak toko' }}.
                @endif
            @else
                Bayar langsung kepada kurir saat pesanan diterima.
            @endif
            @if(in_array($metodePembayaran, [\App\Models\Pesanan::METODE_TRANSFER_BANK, \App\Models\Pesanan::METODE_E_WALLET], true) && $informasiPembayaran['whatsapp'])
                <div class="mt-2">WhatsApp toko: <strong>{{ $informasiPembayaran['whatsapp'] }}</strong></div>
            @endif
        </section>

        @php($pesananUtama = $pesanan->first())
        @if(in_array($metodePembayaran, [\App\Models\Pesanan::METODE_TRANSFER_BANK, \App\Models\Pesanan::METODE_E_WALLET], true))
            <section class="success-upload">
                @if($pesananUtama->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_DIBATALKAN)
                    <strong><i class="bi bi-x-circle"></i> Pesanan ini sudah dibatalkan.</strong>
                @elseif(!$pesananUtama->ongkir_dikonfirmasi || ($pesananUtama->jenis_pengiriman !== null && !$pesananUtama->jadwal_dikonfirmasi))
                    <strong><i class="bi bi-hourglass-split"></i> Menunggu konfirmasi ongkos kirim dan jadwal.</strong>
                    <span>Tujuan pembayaran dan formulir bukti akan tersedia setelah konfirmasi. Periksa kembali halaman Pesanan Saya.</span>
                @elseif($pesananUtama->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_MENUNGGU_VERIFIKASI)
                    <strong><i class="bi bi-hourglass-split"></i> Bukti pembayaran sudah dikirim.</strong>
                    <span>Admin akan memeriksa dan memperbarui status pembayaran.</span>
                @elseif($pesananUtama->status_pembayaran === \App\Models\Pesanan::PEMBAYARAN_LUNAS)
                    <strong><i class="bi bi-check-circle"></i> Pembayaran sudah terverifikasi.</strong>
                @else
                    @if($pesananUtama->catatan_pembayaran)
                        <p class="mb-2 text-danger">Bukti sebelumnya ditolak: {{ $pesananUtama->catatan_pembayaran }} Silakan unggah bukti yang benar.</p>
                    @endif
                    <form action="{{ route('order.bukti-pembayaran', $kode) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="bukti_pembayaran">Unggah bukti transfer / e-wallet</label>
                        <input id="bukti_pembayaran" name="bukti_pembayaran" type="file" accept="image/jpeg,image/png,image/webp" required>
                        @error('bukti_pembayaran')<span class="success-upload-error">{{ $message }}</span>@enderror
                        <small>Format JPG, PNG, atau WEBP; maksimal 5 MB.</small>
                        <button type="submit"><i class="bi bi-cloud-arrow-up"></i> Kirim bukti pembayaran</button>
                    </form>
                @endif
            </section>
        @endif

        <p class="success-note"><i class="bi bi-info-circle"></i> Tunggu konfirmasi ongkos kirim dari admin sebelum membayar. Pembayaran transfer dan e-wallet diproses setelah bukti diunggah dan diverifikasi.</p>

        <div class="success-actions">
            <a href="{{ route('order.riwayat') }}" class="success-button"><i class="bi bi-card-checklist"></i> Lihat pesanan saya</a>
            <a href="{{ route('beranda') }}#menu" class="success-button secondary"><i class="bi bi-shop"></i> Kembali belanja</a>
        </div>
    </article>
</main>
@endsection