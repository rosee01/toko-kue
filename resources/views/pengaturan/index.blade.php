@extends('layouts.template')

@section('page-title', 'Pengaturan')
@section('tanpa-judul', true)

@section('content')
<style>
    .settings-page { padding:10px 0 24px; color:#3f302b; }
    .settings-heading { display:flex; justify-content:space-between; align-items:flex-start; gap:14px; margin:0 2px 16px; }
    .settings-heading-copy { display:flex; align-items:flex-start; gap:12px; }
    .settings-heading-icon { display:grid; place-items:center; width:42px; height:42px; flex:none; border:1px solid #efdfd5; border-radius:12px; background:#fff; color:#a96d5c; font-size:1.1rem; }
    .settings-eyebrow { margin:0 0 3px; color:#a07c6b; font-size:.62rem; font-weight:600; letter-spacing:.1em; text-transform:uppercase; }
    .settings-heading h1 { margin:0; color:#50392f; font:600 1.48rem 'Playfair Display',serif; }
    .settings-heading p { margin:4px 0 0; color:#89786e; font-size:.69rem; }
    .settings-grid { display:grid; grid-template-columns:minmax(0,1.45fr) minmax(280px,.85fr); gap:12px; align-items:start; }
    .settings-main { display:grid; gap:12px; min-width:0; }
    .settings-card { min-width:0; overflow:hidden; border:1px solid #efe4dd; border-radius:9px; background:#fff; box-shadow:0 2px 8px rgba(80,48,34,.025); }
    .settings-card-head { display:flex; align-items:flex-start; gap:10px; padding:14px 16px; border-bottom:1px solid #f1e9e4; }
    .settings-card-icon { display:grid; place-items:center; width:31px; height:31px; flex:none; border-radius:8px; background:#f7eee8; color:#9b6a56; font-size:.8rem; }
    .settings-card-head h2 { margin:0; color:#5c4238; font-size:.78rem; font-weight:600; }
    .settings-card-head p { margin:3px 0 0; color:#958880; font-size:.59rem; line-height:1.45; }
    .settings-card-body { padding:15px 16px; }
    .settings-fields { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
    .settings-field { min-width:0; }
    .settings-field.full { grid-column:1 / -1; }
    .settings-field label { display:block; margin-bottom:5px; color:#76675f; font-size:.64rem; font-weight:500; }
    .settings-field label .optional { color:#a09289; font-weight:400; }
    .settings-control { width:100%; min-height:36px; padding:7px 9px; border:1px solid #e9dfd9; border-radius:6px; background:#fff; color:#55463e; font:inherit; font-size:.66rem; }
    .settings-control:focus { border-color:#b88b76; outline:2px solid rgba(184,139,118,.15); }
    textarea.settings-control { min-height:72px; resize:vertical; }
    .settings-help { margin:5px 0 0; color:#978a82; font-size:.57rem; line-height:1.45; }
    .settings-error { margin-top:4px; color:#c64f4b; font-size:.59rem; }
    .settings-control.is-invalid { border-color:#dc7880; }
    .settings-card-foot { display:flex; justify-content:flex-end; padding:11px 16px; border-top:1px solid #f1e9e4; background:#fdfbf9; }
    .settings-submit-row { display:flex; justify-content:flex-end; }
    .settings-submit { display:inline-flex; align-items:center; justify-content:center; gap:6px; min-height:34px; padding:7px 12px; border:1px solid #7b5343; border-radius:7px; background:#7b5343; color:#fff; font:500 .65rem 'Poppins',sans-serif; cursor:pointer; transition:background .15s ease; }
    .settings-submit:hover { border-color:#654337; background:#654337; }
    .settings-banner { display:grid; grid-template-columns:minmax(0,1fr) 150px; align-items:center; gap:12px; }
    .settings-banner-preview { display:grid; place-items:center; width:100%; height:82px; overflow:hidden; border:1px dashed #e4d6cd; border-radius:7px; background:#faf6f3; color:#aa9385; font-size:.59rem; text-align:center; }
    .settings-banner-preview img { width:100%; height:100%; object-fit:cover; }
    .settings-security { position:sticky; top:76px; }
    .settings-security .settings-fields { grid-template-columns:minmax(0,1fr); gap:11px; }
    .settings-security-note { display:flex; gap:8px; margin-top:13px; padding:9px; border:1px solid #eee5df; border-radius:7px; background:#fbf8f6; color:#83736a; font-size:.58rem; line-height:1.5; }
    .settings-security-note i { flex:none; color:#a87960; }
    .settings-checklist { display:grid; gap:9px; }
    .settings-check { display:flex; align-items:flex-start; gap:8px; color:#74665e; font-size:.6rem; line-height:1.45; }
    .settings-check i { flex:none; color:#6eaa76; }
    .settings-check strong { color:#57483f; font-weight:600; }
    .delivery-setup-status { display:grid; gap:8px; margin-top:14px; padding:12px; border:1px solid #eee4de; border-radius:8px; background:#fcfaf8; }
    .delivery-setup-row { display:flex; align-items:flex-start; gap:9px; color:#75675f; font-size:.62rem; line-height:1.5; }
    .delivery-setup-row i { flex:none; margin-top:1px; font-size:.78rem; }
    .delivery-setup-row strong { color:#554238; font-weight:600; }
    .delivery-setup-row.is-ready i { color:#5b9867; }
    .delivery-setup-row.is-pending i { color:#b87936; }
    @media (max-width:850px) {
        .settings-grid { grid-template-columns:minmax(0,1fr); }
        .settings-security { position:static; }
        .settings-security .settings-fields { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .settings-security .settings-field.full { grid-column:1 / -1; }
    }
    @media (max-width:520px) {
        .settings-heading-copy { gap:9px; }
        .settings-heading h1 { font-size:1.3rem; }
        .settings-fields,.settings-security .settings-fields { grid-template-columns:minmax(0,1fr); }
        .settings-field.full,.settings-security .settings-field.full { grid-column:auto; }
        .settings-banner { grid-template-columns:minmax(0,1fr); }
        .settings-banner-preview { height:115px; }
        .settings-card-body { padding:13px; }
    }
</style>

@php
    $value = fn ($key, $default = null) => old($key, \App\Models\Pengaturan::ambil($key, $default));
    $banner = \App\Models\Pengaturan::ambil('banner');
@endphp

<div class="settings-page">
    <header class="settings-heading">
        <div class="settings-heading-copy">
            <span class="settings-heading-icon" aria-hidden="true"><i class="bi bi-gear"></i></span>
            <div>
                <div class="settings-eyebrow">Kelola toko</div>
                <h1>Pengaturan</h1>
                <p>Atur informasi yang ditampilkan kepada pelanggan dan keamanan akun admin.</p>
            </div>
        </div>
    </header>

    <div class="settings-grid">
        <form class="settings-main" action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <section class="settings-card" id="profil-toko">
                <div class="settings-card-head">
                    <span class="settings-card-icon"><i class="bi bi-shop"></i></span>
                    <div>
                        <h2>Profil toko</h2>
                        <p>Nama dan slogan akan digunakan pada identitas toko di halaman admin dan etalase.</p>
                    </div>
                </div>
                <div class="settings-card-body">
                    <div class="settings-fields">
                        <div class="settings-field">
                            <label for="nama_toko">Nama toko</label>
                            <input id="nama_toko" type="text" name="nama_toko" maxlength="60" required value="{{ $value('nama_toko', config('app.name')) }}" class="settings-control @error('nama_toko') is-invalid @enderror" placeholder="Contoh: Toko Kue Manis">
                            @error('nama_toko')<div class="settings-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="settings-field">
                            <label for="slogan">Slogan <span class="optional">(opsional)</span></label>
                            <input id="slogan" type="text" name="slogan" maxlength="80" value="{{ $value('slogan', 'Manisnya Setiap Momen') }}" class="settings-control @error('slogan') is-invalid @enderror" placeholder="Contoh: Manisnya Setiap Momen">
                            @error('slogan')<div class="settings-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="settings-card" id="kontak-toko">
                <div class="settings-card-head">
                    <span class="settings-card-icon"><i class="bi bi-chat-dots"></i></span>
                    <div>
                        <h2>Kontak &amp; lokasi</h2>
                        <p>Informasi ini membantu pelanggan menghubungi dan menemukan toko Anda.</p>
                    </div>
                </div>
                <div class="settings-card-body">
                    <div class="settings-fields">
                        <div class="settings-field">
                            <label for="whatsapp">Nomor WhatsApp <span class="optional">(opsional)</span></label>
                            <input id="whatsapp" type="tel" name="whatsapp" inputmode="numeric" maxlength="15" value="{{ $value('whatsapp') }}" class="settings-control @error('whatsapp') is-invalid @enderror" placeholder="6281234567890">
                            @error('whatsapp')<div class="settings-error">{{ $message }}</div>@enderror
                            <p class="settings-help">Gunakan kode negara tanpa tanda + atau spasi, misalnya 6281234567890.</p>
                        </div>
                        <div class="settings-field">
                            <label for="alamat">Alamat toko <span class="optional">(wajib untuk ongkir otomatis)</span></label>
                            <textarea id="alamat" name="alamat" maxlength="200" rows="3" class="settings-control @error('alamat') is-invalid @enderror" placeholder="Alamat lengkap toko">{{ $value('alamat', config('delivery.origin')) }}</textarea>
                            @error('alamat')<div class="settings-error">{{ $message }}</div>@enderror
                            <p class="settings-help">Masukkan alamat operasional toko yang benar dan lengkap, termasuk nomor, kelurahan, kecamatan, dan kota. Google Maps menggunakannya sebagai titik awal rute ke pembeli.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="settings-card" id="metode-pembayaran">
                <div class="settings-card-head">
                    <span class="settings-card-icon"><i class="bi bi-credit-card"></i></span>
                    <div>
                        <h2>Metode pembayaran</h2>
                        <p>Isi tujuan transfer agar pelanggan melihat detail yang benar setelah memilih metode pembayaran.</p>
                    </div>
                </div>
                <div class="settings-card-body">
                    <div class="settings-fields">
                        <div class="settings-field">
                            <label for="bank_nama">Nama bank</label>
                            <input id="bank_nama" name="bank_nama" maxlength="50" value="{{ $value('bank_nama') }}" class="settings-control @error('bank_nama') is-invalid @enderror" placeholder="Contoh: BCA">
                            @error('bank_nama')<div class="settings-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="settings-field">
                            <label for="bank_rekening">Nomor rekening</label>
                            <input id="bank_rekening" name="bank_rekening" maxlength="30" value="{{ $value('bank_rekening') }}" class="settings-control @error('bank_rekening') is-invalid @enderror" placeholder="Nomor rekening toko">
                            @error('bank_rekening')<div class="settings-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="settings-field full">
                            <label for="bank_pemilik">Nama pemilik rekening</label>
                            <input id="bank_pemilik" name="bank_pemilik" maxlength="100" value="{{ $value('bank_pemilik') }}" class="settings-control @error('bank_pemilik') is-invalid @enderror" placeholder="Nama sesuai rekening">
                            @error('bank_pemilik')<div class="settings-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="settings-field">
                            <label for="ewallet_provider">Nama e-wallet</label>
                            <input id="ewallet_provider" name="ewallet_provider" maxlength="40" value="{{ $value('ewallet_provider') }}" class="settings-control @error('ewallet_provider') is-invalid @enderror" placeholder="Contoh: DANA / GoPay">
                            @error('ewallet_provider')<div class="settings-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="settings-field">
                            <label for="ewallet_nomor">Nomor e-wallet</label>
                            <input id="ewallet_nomor" name="ewallet_nomor" maxlength="30" value="{{ $value('ewallet_nomor') }}" class="settings-control @error('ewallet_nomor') is-invalid @enderror" placeholder="Nomor akun toko">
                            @error('ewallet_nomor')<div class="settings-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="settings-field full">
                            <label for="ewallet_pemilik">Nama pemilik e-wallet</label>
                            <input id="ewallet_pemilik" name="ewallet_pemilik" maxlength="100" value="{{ $value('ewallet_pemilik') }}" class="settings-control @error('ewallet_pemilik') is-invalid @enderror" placeholder="Nama pemilik akun">
                            @error('ewallet_pemilik')<div class="settings-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <p class="settings-help">Metode transfer dan e-wallet akan menunggu verifikasi admin. Jangan isi nomor contoh; gunakan hanya rekening dan akun resmi toko.</p>
                </div>
            </section>

            <section class="settings-card" id="tarif-pengiriman">
                <div class="settings-card-head">
                    <span class="settings-card-icon"><i class="bi bi-truck"></i></span>
                    <div>
                        <h2>Tarif per jenis pengiriman</h2>
                        <p>Harga dihitung otomatis dari jarak rute Google Maps dikalikan tarif per kilometer.</p>
                    </div>
                </div>
                <div class="settings-card-body">
                    <div class="settings-fields">
                        <div class="settings-field">
                            <label for="tarif_per_km_cepat">Cepat · tarif per km</label>
                            <input id="tarif_per_km_cepat" name="tarif_per_km_cepat" type="number" min="0" max="5000000" step="1" required value="{{ $value('tarif_per_km_cepat', 3500) }}" class="settings-control @error('tarif_per_km_cepat') is-invalid @enderror">
                            @error('tarif_per_km_cepat')<div class="settings-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="settings-field">
                            <label for="tarif_per_km_hemat">Hemat · tarif per km</label>
                            <input id="tarif_per_km_hemat" name="tarif_per_km_hemat" type="number" min="0" max="5000000" step="1" required value="{{ $value('tarif_per_km_hemat', 1000) }}" class="settings-control @error('tarif_per_km_hemat') is-invalid @enderror">
                            @error('tarif_per_km_hemat')<div class="settings-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="settings-field">
                            <label for="gratis_sampai_km_hemat">Hemat · gratis sampai jarak (km)</label>
                            <input id="gratis_sampai_km_hemat" name="gratis_sampai_km_hemat" type="number" min="0" max="100" step="0.1" required value="{{ $value('gratis_sampai_km_hemat', 3) }}" class="settings-control @error('gratis_sampai_km_hemat') is-invalid @enderror">
                            @error('gratis_sampai_km_hemat')<div class="settings-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="settings-field">
                            <label for="tarif_per_km_lambat">Lambat · tarif per km</label>
                            <input id="tarif_per_km_lambat" name="tarif_per_km_lambat" type="number" min="0" max="5000000" step="1" required value="{{ $value('tarif_per_km_lambat', 500) }}" class="settings-control @error('tarif_per_km_lambat') is-invalid @enderror">
                            @error('tarif_per_km_lambat')<div class="settings-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <p class="settings-help">Ongkir Hemat gratis sampai jarak yang ditentukan. Di atas batas itu, semua ongkir dihitung berdasarkan jarak rute aktual dan tarif per km. Admin tetap mengonfirmasi tarif final.</p>
                    <div class="delivery-setup-status" aria-label="Status kesiapan hitung ongkir">
                        <div class="delivery-setup-row {{ $alamatTokoSiap ? 'is-ready' : 'is-pending' }}">
                            <i class="bi bi-{{ $alamatTokoSiap ? 'check-circle-fill' : 'exclamation-circle-fill' }}"></i>
                            <span><strong>Alamat asal toko:</strong> {{ $alamatTokoSiap ? 'sudah diisi.' : 'belum diisi. Lengkapi pada bagian Kontak & lokasi.' }}</span>
                        </div>
                        <div class="delivery-setup-row {{ $mapsSiap ? 'is-ready' : 'is-pending' }}">
                            <i class="bi bi-{{ $mapsSiap ? 'check-circle-fill' : 'exclamation-circle-fill' }}"></i>
                            <span><strong>Google Maps Routes API:</strong> {{ $mapsSiap ? 'kunci server tersedia; pastikan Routes API aktif pada Google Cloud.' : 'belum siap. Atur GOOGLE_MAPS_SERVER_KEY pada file .env dan aktifkan Routes API.' }}</span>
                        </div>
                    </div>
                    <p class="settings-help">Penghitungan baru dapat berjalan setelah kedua persyaratan tersedia. Kunci API disimpan di server dan tidak ditampilkan kepada pelanggan.</p>
                </div>
            </section>

            <section class="settings-card" id="tampilan-toko">
                <div class="settings-card-head">
                    <span class="settings-card-icon"><i class="bi bi-image"></i></span>
                    <div>
                        <h2>Banner etalase</h2>
                        <p>Gambar ini tampil di bagian utama halaman beranda customer. Format gambar, maksimal 2 MB.</p>
                    </div>
                </div>
                <div class="settings-card-body">
                    <div class="settings-banner">
                        <div class="settings-field">
                            <label for="banner">Pilih gambar banner <span class="optional">(opsional)</span></label>
                            <input id="banner" type="file" name="banner" accept="image/*" class="settings-control @error('banner') is-invalid @enderror">
                            @error('banner')<div class="settings-error">{{ $message }}</div>@enderror
                            <p class="settings-help">Gambar yang sudah ada akan diganti setelah pengaturan disimpan.</p>
                        </div>
                        <div class="settings-banner-preview" id="banner-preview" aria-label="Pratinjau banner">
                            @if($banner)
                                <img src="{{ asset('storage/'.$banner) }}" alt="Banner toko saat ini">
                            @else
                                <span><i class="bi bi-image me-1"></i>Belum ada banner</span>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <div class="settings-submit-row">
                <button type="submit" class="settings-submit"><i class="bi bi-check2"></i> Simpan pengaturan toko</button>
            </div>
        </form>

        <div class="settings-main">
            <section class="settings-card settings-security" id="keamanan-akun">
                <div class="settings-card-head">
                    <span class="settings-card-icon"><i class="bi bi-shield-lock"></i></span>
                    <div>
                        <h2>Keamanan akun admin</h2>
                        <p>Ganti password secara berkala untuk menjaga akun tetap aman.</p>
                    </div>
                </div>
                <form action="{{ route('pengaturan.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="settings-card-body">
                        <div class="settings-fields">
                            <div class="settings-field">
                                <label for="password_lama">Password saat ini</label>
                                <input id="password_lama" type="password" name="password_lama" autocomplete="current-password" required class="settings-control @error('password_lama') is-invalid @enderror" placeholder="Masukkan password saat ini">
                                @error('password_lama')<div class="settings-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="settings-field">
                                <label for="password">Password baru</label>
                                <input id="password" type="password" name="password" autocomplete="new-password" minlength="8" required class="settings-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter">
                                @error('password')<div class="settings-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="settings-field full">
                                <label for="password_confirmation">Ulangi password baru</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required class="settings-control" placeholder="Ketik ulang password baru">
                            </div>
                        </div>
                        <div class="settings-security-note"><i class="bi bi-info-circle"></i><span>Pastikan password baru minimal 8 karakter dan berbeda dari password yang mudah ditebak.</span></div>
                    </div>
                    <div class="settings-card-foot">
                        <button type="submit" class="settings-submit"><i class="bi bi-key"></i> Ganti password</button>
                    </div>
                </form>
            </section>

            <section class="settings-card">
                <div class="settings-card-head">
                    <span class="settings-card-icon"><i class="bi bi-list-check"></i></span>
                    <div>
                        <h2>Yang bisa diatur</h2>
                        <p>Pengaturan yang tersedia pada aplikasi saat ini.</p>
                    </div>
                </div>
                <div class="settings-card-body">
                    <div class="settings-checklist">
                        <div class="settings-check"><i class="bi bi-check-circle-fill"></i><span><strong>Identitas toko</strong><br>Nama dan slogan toko.</span></div>
                        <div class="settings-check"><i class="bi bi-check-circle-fill"></i><span><strong>Informasi pelanggan</strong><br>WhatsApp dan alamat toko.</span></div>
                        <div class="settings-check"><i class="bi bi-check-circle-fill"></i><span><strong>Pembayaran</strong><br>Tujuan transfer bank dan e-wallet.</span></div>
                        <div class="settings-check"><i class="bi bi-check-circle-fill"></i><span><strong>Tampilan</strong><br>Gambar banner toko.</span></div>
                        <div class="settings-check"><i class="bi bi-check-circle-fill"></i><span><strong>Keamanan</strong><br>Perubahan password akun admin.</span></div>
                        <div class="settings-check"><i class="bi bi-{{ $whatsappCloudSiap ? 'check-circle-fill' : 'exclamation-circle-fill' }}"></i><span><strong>WhatsApp Cloud API</strong><br>{{ $whatsappCloudSiap ? 'Kredensial tersedia.' : 'Belum dikonfigurasi; isi variabel WHATSAPP_CLOUD_* di .env.' }}</span></div>
                    </div>
                </div>
            </section>
            <section class="settings-card mt-3">
                <div class="settings-card-head">
                    <span class="settings-card-icon"><i class="bi bi-chat-square-text"></i></span>
                    <div><h2>Log WhatsApp terbaru</h2><p>Gagal atau belum dikonfigurasi akan terlihat di sini; pesan hanya dikirim kepada customer yang menyetujui.</p></div>
                </div>
                <div class="settings-card-body">
                    @forelse($notifikasiWhatsApp as $notifikasi)
                        <div class="d-flex justify-content-between gap-2 border-bottom py-2 small">
                            <span>{{ $notifikasi->kode_pesanan ?? 'Pesanan' }} · {{ $notifikasi->peristiwa }}<br><span class="text-muted">{{ $notifikasi->pesan_error ?? $notifikasi->telepon }}</span></span>
                            <strong>{{ $notifikasi->status }}</strong>
                        </div>
                    @empty
                        <p class="settings-help mb-0">Belum ada pesan WhatsApp.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const bannerInput = document.getElementById('banner');
    const bannerPreview = document.getElementById('banner-preview');

    bannerInput?.addEventListener('change', () => {
        const [file] = bannerInput.files || [];

        if (!file) {
            return;
        }

        const preview = document.createElement('img');
        preview.src = URL.createObjectURL(file);
        preview.alt = 'Pratinjau banner baru';
        preview.onload = () => URL.revokeObjectURL(preview.src);
        bannerPreview.replaceChildren(preview);
    });
</script>
@endsection
