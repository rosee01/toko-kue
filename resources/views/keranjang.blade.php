@extends('layouts.customer')

@section('title', 'Keranjang Belanja - Toko Kue')

@push('styles')
<style>
    .cart-table { border: 1px solid #eaded7; border-radius: 16px; background: #fff; overflow: hidden; }
    .cart-head, .cart-row { display: grid; grid-template-columns: minmax(0, 2.6fr) 1fr 1.3fr 1fr 36px; gap: 12px; align-items: center; padding: 14px 16px; }
    .cart-head { background: #fbf5f1; color: var(--muted); font-size: 14px; font-weight: 600; }
    .cart-row { border-top: 1px solid #f0e3da; }
    .cart-prod { display: flex; align-items: center; gap: 14px; min-width: 0; }
    .cart-prod img { width: 68px; height: 68px; object-fit: cover; object-position: center 85%; border-radius: 12px; flex-shrink: 0; background: #fbf5f1; }
    .cart-prod img.cake-photo-crop { transform: scale(1.7); transform-origin: center 90%; }
    .cart-prod-name { font-weight: 700; color: var(--brown-dark); }
    .cart-prod-stok { font-size: 13px; color: #5f8a6b; font-weight: 600; }
    .cart-prod-stok.menipis { color: #b7791f; }
    .cart-price, .cart-sub { font-weight: 600; color: var(--brown-dark); }
    .stepper { display: inline-flex; align-items: center; border: 1px solid #e4d7cf; border-radius: 10px; overflow: hidden; background: #fff; }
    .stepper button { width: 36px; height: 36px; border: 0; background: #fff; color: var(--brown); font-size: 18px; cursor: pointer; }
    .stepper button:hover { background: var(--cream); }
    .stepper span { min-width: 38px; text-align: center; font-weight: 600; }
    .btn-hapus { border: 0; background: none; color: #b07a84; font-size: 18px; cursor: pointer; }
    .btn-hapus:hover { color: #c8574f; }

    .cart-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; margin-top: 22px; }
    .sub-panel { background: #fbf5f1; border: 1px solid #eee0d8; border-radius: 16px; padding: 20px; }
    .sub-panel h5 { font-size: 17px; font-weight: 700; color: var(--brown-dark); margin-bottom: 14px; }
    .sub-panel .form-label { color: var(--brown-dark); font-size: 14px; font-weight: 600; margin-bottom: 4px; }
    .sub-panel .form-control { border-color: #dfcec4; border-radius: 10px; padding: 10px 14px; font-size: 14px; background: #fff; }
    .sub-panel .form-control:focus { border-color: var(--brown); box-shadow: 0 0 0 3px rgba(117,75,58,.08); }
    .delivery-message { padding:10px 12px; border:1px solid #e8e0db; border-radius:8px; background:#faf8f6; color:#685b53; font-size:12px; line-height:1.5; }
    .delivery-message.is-error { border-color:#eed8d2; background:#fff8f6; color:#894c43; }
    .delivery-option { display:block; margin-top:8px; padding:10px 12px; border:1px solid #e7dfda; border-radius:8px; background:#fff; cursor:pointer; }
    .delivery-option:has(input:checked) { border-color:#9c715b; background:#fbf5f1; }
    .delivery-option strong { margin-left:5px; color:#574136; }
    .sum-row { display: flex; justify-content: space-between; padding: 8px 0; }
    .sum-row.total { border-top: 1px solid #e4d7cf; margin-top: 8px; padding-top: 16px; font-weight: 700; font-size: 18px; color: var(--brown-dark); align-items: baseline; }
    .sum-row.total strong { font-size: 28px; }

    .btn-pesan { width: 100%; margin-top: 22px; background: var(--brown); color: #fff; border: 0; border-radius: 12px; padding: 16px; font-size: 16px; font-weight: 700; cursor: pointer; transition: .2s; }
    .btn-pesan:hover:not(:disabled) { background: var(--brown-dark); }
    .btn-pesan:disabled { opacity: .6; cursor: not-allowed; }
    .btn-lanjut { display: block; width: 100%; margin-top: 12px; text-align: center; background: #fff; color: var(--brown-dark); border: 1px solid #e4d7cf; border-radius: 12px; padding: 14px; font-weight: 600; }
    .btn-lanjut:hover { background: var(--cream); color: var(--brown-dark); }
    .cart-kosong { text-align: center; padding: 50px 20px; color: var(--muted); }
    .cart-kosong i { font-size: 56px; color: var(--brown-light); }
    .cart-step-label { display: none; color: var(--muted); font-size: 11px; }

    @media (max-width: 850px) {
        .cart-head { display: none; }
        .cart-row { display: grid; grid-template-columns: minmax(0,1fr) auto auto; gap: 10px 14px; padding: 14px; }
        .cart-prod { flex: 1 1 100%; }
        .cart-prod { grid-column: 1 / -1; }
        .cart-price { grid-column: 1; }
        .cart-row > div:nth-child(3) { grid-column: 2; }
        .cart-sub { grid-column: 1 / 3; grid-row: 3; }
        .btn-hapus { grid-column: 3; grid-row: 3; justify-self: end; }
        .cart-step-label { display: block; }
        .cart-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 520px) {
        .page-wrap { padding: 24px 0 42px; }
        .panel { padding: 16px; border-radius: 16px; }
        .panel-title { font-size: 22px; }
        .panel-title i { font-size: 25px; }
        .cart-grid { gap: 12px; }
        .sub-panel { padding: 16px; }
        .sum-row.total { align-items: flex-start; font-size: 15px; }
        .sum-row.total strong { font-size: 20px; white-space: nowrap; }
    }
</style>
@endpush

@section('content')
<section class="page-wrap">
    <div class="container-custom" style="max-width: 1000px">
        <div class="panel">
            <h1 class="panel-title"><i class="bi bi-bag"></i> Keranjang Belanja</h1>
            <p class="panel-sub">Pastikan pesanan Anda sudah benar sebelum dipesan.</p>

            <div id="cartKosong" class="cart-kosong" style="display:none">
                <i class="bi bi-bag-x"></i>
                <p class="mt-3 mb-4">Keranjang masih kosong.</p>
                <a href="{{ route('beranda') }}#menu" class="btn-pesan" style="display:inline-block;width:auto;margin:0;padding:12px 28px">Mulai Belanja</a>
            </div>

            <div id="cartIsi" style="display:none">
                <div class="cart-table">
                    <div class="cart-head">
                        <div>Produk</div><div>Harga</div><div>Jumlah</div><div>Subtotal</div><div></div>
                    </div>
                    <div id="cartRows"></div>
                </div>

                <div class="cart-grid">
                    <div class="sub-panel">
                        <h5><i class="bi bi-geo-alt"></i> Data Penerima</h5>
                        <div class="mb-2">
                            <label class="form-label" for="f_nama">Nama Penerima</label>
                            <input type="text" id="f_nama" class="form-control" value="{{ $kontakTerakhir?->nama_pelanggan ?? auth()->user()->name }}" placeholder="Contoh: Budi Santoso" autocomplete="name">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="f_telepon">No. WhatsApp / Telepon</label>
                            <input type="tel" id="f_telepon" class="form-control" value="{{ $kontakTerakhir?->no_telepon }}" placeholder="Contoh: 08123456789" autocomplete="tel">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="f_alamat">Alamat Pengiriman</label>
                            <textarea id="f_alamat" class="form-control" rows="2" placeholder="Masukkan alamat lengkap" autocomplete="street-address">{{ $kontakTerakhir?->alamat_pengiriman }}</textarea>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Rute dihitung otomatis dari {{ $alamatToko ?: 'alamat toko yang belum diatur' }}. Alamat tujuan diproses Google Maps untuk menghitung jarak rute.</small>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Pilihan pengiriman</label>
                            <div id="deliveryOptions" class="delivery-message" role="status" aria-live="polite">Isi alamat lengkap untuk menghitung jarak dan ongkir.</div>
                            <div id="deliveryDistance" class="small text-muted mt-1"></div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="btnHitungOngkir">Hitung ongkir dari alamat</button>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="f_jadwal">Waktu pengiriman yang diminta</label>
                            <input type="datetime-local" id="f_jadwal" class="form-control" min="{{ now()->addHour()->startOfMinute()->addMinutes(2)->format('Y-m-d\TH:i') }}" max="{{ now()->addDays(14)->format('Y-m-d\TH:i') }}" value="{{ now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i') }}">
                            <small class="text-muted">Jadwal masih berupa permintaan sampai dikonfirmasi admin.</small>
                        </div>
                        <div>
                            <label class="form-label" for="f_catatan">Catatan</label>
                            <textarea id="f_catatan" class="form-control" rows="2" placeholder="Contoh: Tuliskan Happy Birthday"></textarea>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="f_whatsapp">
                            <label class="form-check-label small" for="f_whatsapp">Saya setuju menerima pembaruan pesanan melalui WhatsApp.</label>
                        </div>
                        <div class="mt-3">
                            <h5><i class="bi bi-credit-card"></i> Metode Pembayaran</h5>
                            @include('partials.payment-method-options', ['informasiPembayaran' => $informasiPembayaran])
                        </div>
                    </div>

                    <div class="sub-panel">
                        <h5><i class="bi bi-receipt"></i> Ringkasan</h5>
                        <div class="sum-row"><span id="labelItem">Total Harga</span><span id="totalHarga">Rp 0</span></div>
                        <div class="sum-row"><span>Estimasi Ongkos Kirim</span><span id="ongkirEstimasi">Hitung alamat</span></div>
                        <div class="sum-row total"><span>Total Estimasi</span><strong id="totalBayar">Hitung ongkir</strong></div>
                        <div class="small text-muted mt-2">Tarif dihitung dari jarak rute Google Maps dan jenis layanan. Admin mengonfirmasi tarif final sebelum pembayaran.</div>
                    </div>
                </div>

                <button type="button" id="btnPesan" class="btn-pesan" onclick="pesanSekarang()">
                    <i class="bi bi-bag-check"></i> Pesan Sekarang
                </button>
                <a href="{{ route('beranda') }}#menu" class="btn-lanjut"><i class="bi bi-arrow-left"></i> Lanjut Belanja</a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const DAFTAR = @json($produk);
    const PRODUK = Object.fromEntries(DAFTAR.map(p => [String(p.id), p]));
    const URL_PESAN = @json(route('order.keranjang'));
    const URL_HITUNG_ONGKIR = @json(route('order.delivery-quote'));
    const CSRF = @json(csrf_token());

    let cart = getCart();
    let deliveryQuote = null;
    let selectedDelivery = null;
    let quotedDeliveryAddress = '';
    let deliveryQuoteRequest = 0;

    function setDeliveryMessage(message, type = 'info') {
        const options = document.getElementById('deliveryOptions');
        options.className = `delivery-message${type === 'error' ? ' is-error' : ''}`;
        options.textContent = message;
    }

    function toast(pesan, icon = 'info') {
        window.AppAlert.fire({ toast: true, position: 'top-end', icon, title: pesan, timer: 2200, showConfirmButton: false });
    }

    // Samakan keranjang dengan harga dan stok terbaru dari toko
    function sinkronDenganStok() {
        const pesan = [];
        cart = cart.filter(item => {
            const p = PRODUK[String(item.id)];
            if (!p) { pesan.push(`${item.name} sudah tidak tersedia.`); return false; }
            if (p.stok <= 0) { pesan.push(`Stok ${p.nama} habis, dihapus dari keranjang.`); return false; }
            item.id = String(p.id);
            item.name = p.nama;
            item.price = p.harga;
            item.image = p.foto;
            if (item.quantity > p.stok) {
                item.quantity = p.stok;
                pesan.push(`Jumlah ${p.nama} disesuaikan dengan stok (${p.stok}).`);
            }
            return true;
        });
        saveCart(cart);

        if (pesan.length) {
            window.AppAlert.fire({ icon: 'info', title: 'Keranjang diperbarui', html: pesan.map(esc).join('<br>'), confirmButtonColor: '#754b3a' });
        }
    }

    function render() {
        const kosong = cart.length === 0;
        document.getElementById('cartKosong').style.display = kosong ? '' : 'none';
        document.getElementById('cartIsi').style.display = kosong ? 'none' : '';
        if (kosong) return;

        let totalItem = 0;
        let totalHarga = 0;

        document.getElementById('cartRows').innerHTML = cart.map(item => {
            const p = PRODUK[String(item.id)];
            totalItem += item.quantity;
            totalHarga += p.harga * item.quantity;
            return `
                <div class="cart-row">
                    <div class="cart-prod">
                        <img src="${esc(p.foto)}" alt="${esc(p.nama)}" class="${p.foto_default ? 'cake-photo-crop' : ''}">
                        <div>
                            <div class="cart-prod-name">${esc(p.nama)}</div>
                            <div class="cart-prod-stok ${p.stok <= 5 ? 'menipis' : ''}"><i class="bi bi-box-seam"></i> Stok tersisa: ${p.stok}</div>
                        </div>
                    </div>
                    <div class="cart-price"><span class="cart-step-label">Harga</span>${rupiah(p.harga)}</div>
                    <div>
                        <span class="cart-step-label">Jumlah</span>
                        <div class="stepper">
                            <button type="button" onclick="ubahJumlah('${p.id}', -1)" aria-label="Kurangi">&minus;</button>
                            <span>${item.quantity}</span>
                            <button type="button" onclick="ubahJumlah('${p.id}', 1)" aria-label="Tambah">+</button>
                        </div>
                    </div>
                    <div class="cart-sub"><span class="cart-step-label">Subtotal</span>${rupiah(p.harga * item.quantity)}</div>
                    <button type="button" class="btn-hapus" onclick="hapusItem('${p.id}')" aria-label="Hapus"><i class="bi bi-trash"></i></button>
                </div>`;
        }).join('');

        document.getElementById('labelItem').textContent = `Total Harga (${totalItem} item)`;
        document.getElementById('totalHarga').textContent = rupiah(totalHarga);
        const ongkir = deliveryQuote?.services?.[selectedDelivery]?.fee;
        document.getElementById('ongkirEstimasi').textContent = Number.isInteger(ongkir) ? rupiah(ongkir) : 'Hitung alamat';
        document.getElementById('totalBayar').textContent = Number.isInteger(ongkir) ? rupiah(totalHarga + ongkir) : 'Hitung ongkir';
        document.getElementById('btnPesan').disabled = !Number.isInteger(ongkir);
    }

    function ubahJumlah(id, delta) {
        const item = cart.find(i => String(i.id) === String(id));
        const p = PRODUK[String(id)];
        if (!item || !p) return;

        const baru = item.quantity + delta;
        if (baru < 1) return;
        if (baru > p.stok) { toast(`Stok ${p.nama} hanya ${p.stok}.`, 'warning'); return; }

        item.quantity = baru;
        saveCart(cart);
        render();
    }

    function hapusItem(id) {
        cart = cart.filter(i => String(i.id) !== String(id));
        saveCart(cart);
        render();
    }

    async function pesanSekarang() {
        const nama = document.getElementById('f_nama').value.trim();
        const telepon = document.getElementById('f_telepon').value.trim();
        const alamat = document.getElementById('f_alamat').value.trim();
        const catatan = document.getElementById('f_catatan').value.trim();
        const jadwal = document.getElementById('f_jadwal').value;

        if (cart.length === 0) return;
        if (!nama || !telepon || !alamat || !jadwal || !selectedDelivery || !deliveryQuote || quotedDeliveryAddress !== alamat) {
            window.AppAlert.fire({ icon: 'warning', title: 'Data belum lengkap', text: 'Lengkapi penerima, alamat, jadwal, hitung ongkir, dan pilih jenis pengiriman.', confirmButtonColor: '#754b3a' });
            return;
        }

        const btn = document.getElementById('btnPesan');
        btn.disabled = true;

        try {
            const res = await fetch(URL_PESAN, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    nama_pelanggan: nama,
                    no_telepon: telepon,
                    alamat_pengiriman: alamat,
                    catatan: catatan,
                    metode_pembayaran: document.querySelector('input[name="metode_pembayaran"]:checked')?.value,
                    jenis_pengiriman: selectedDelivery,
                    jadwal_diminta: jadwal,
                    izin_notifikasi_whatsapp: document.getElementById('f_whatsapp').checked,
                    items: cart.map(i => ({ produk_id: i.id, jumlah: i.quantity }))
                })
            });
            const data = await res.json().catch(() => ({}));

            if (res.status === 401) { showLoginAlert(); return; }
            if (res.status === 419) {
                window.AppAlert.fire({ icon: 'info', title: 'Sesi habis', text: 'Muat ulang halaman lalu coba lagi.', confirmButtonColor: '#754b3a' })
                    .then(() => location.reload());
                return;
            }

            if (!res.ok) {
                const pesan = data.errors ? Object.values(data.errors).flat()[0] : (data.message || 'Terjadi kesalahan.');
                window.AppAlert.fire({ icon: 'error', title: 'Pesanan gagal', text: pesan, confirmButtonColor: '#754b3a' });
                return;
            }

            cart = [];
            saveCart(cart);
            window.location.href = data.redirect;
        } catch (e) {
            window.AppAlert.fire({ icon: 'error', title: 'Gagal terhubung', text: 'Periksa koneksi internet lalu coba lagi.', confirmButtonColor: '#754b3a' });
        } finally {
            btn.disabled = false;
        }
    }

    async function hitungOngkir() {
        const requestId = ++deliveryQuoteRequest;
        const alamat = document.getElementById('f_alamat').value.trim();
        const options = document.getElementById('deliveryOptions');
        const button = document.getElementById('btnHitungOngkir');
        if (alamat.length < 10) {
            setDeliveryMessage('Masukkan alamat lengkap: jalan, nomor rumah, kelurahan, kecamatan, dan kota.');
            return;
        }
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Menghitung rute';
        setDeliveryMessage('Mohon tunggu, jarak rute sedang dihitung dari alamat toko.');
        deliveryQuote = null;
        selectedDelivery = null;
        quotedDeliveryAddress = '';
        render();
        try {
            const response = await fetch(URL_HITUNG_ONGKIR, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ alamat_pengiriman: alamat })
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.errors ? Object.values(data.errors).flat()[0] : 'Rute alamat belum dapat dihitung.');
            if (requestId !== deliveryQuoteRequest || document.getElementById('f_alamat').value.trim() !== alamat) return;
            deliveryQuote = data;
            quotedDeliveryAddress = alamat;
            selectedDelivery = 'hemat';
            document.getElementById('deliveryDistance').textContent = `Jarak rute: ${Number(data.distance_km).toLocaleString('id-ID')} km`;
            options.innerHTML = Object.values(data.services).map(option => `
                <label class="delivery-option">
                    <input type="radio" name="jenis_pengiriman" value="${esc(option.key)}" ${option.key === selectedDelivery ? 'checked' : ''}>
                    <strong>${esc(option.label)} · ${rupiah(option.fee)}</strong><br>
                    <span>${esc(option.description)} · ${rupiah(option.rate_per_km)}/km</span>
                </label>`).join('');
            options.className = 'delivery-options-list';
            options.querySelectorAll('input[name="jenis_pengiriman"]').forEach(input => input.addEventListener('change', () => {
                selectedDelivery = input.value;
                render();
            }));
            render();
        } catch (error) {
            if (requestId !== deliveryQuoteRequest) return;
            const message = error.message || 'Periksa alamat tujuan lalu coba lagi.';
            if (message.toLowerCase().includes('alamat toko')) {
                setDeliveryMessage('Alamat asal toko belum diatur. Admin perlu mengisinya pada Pengaturan → Kontak & lokasi.', 'error');
            } else if (message.toLowerCase().includes('google maps api key')) {
                setDeliveryMessage('Layanan peta belum aktif. Admin perlu mengatur GOOGLE_MAPS_SERVER_KEY dan mengaktifkan Routes API.', 'error');
            } else {
                setDeliveryMessage(message, 'error');
            }
            document.getElementById('deliveryDistance').textContent = '';
        } finally {
            if (requestId === deliveryQuoteRequest) {
                button.disabled = false;
                button.innerHTML = 'Hitung ongkir dari alamat';
            }
        }
    }

    document.getElementById('f_alamat').addEventListener('blur', hitungOngkir);
    document.getElementById('f_alamat').addEventListener('input', () => {
        deliveryQuoteRequest++;
        deliveryQuote = null;
        selectedDelivery = null;
        quotedDeliveryAddress = '';
        setDeliveryMessage('Alamat berubah. Hitung ulang ongkir untuk memperbarui jarak dan biaya.');
        document.getElementById('deliveryDistance').textContent = '';
        const button = document.getElementById('btnHitungOngkir');
        button.disabled = false;
        button.innerHTML = 'Hitung ongkir dari alamat';
        render();
    });
    document.getElementById('btnHitungOngkir').addEventListener('click', hitungOngkir);
    if (document.getElementById('f_alamat').value.trim().length >= 10) hitungOngkir();
    sinkronDenganStok();
    render();
</script>
@endpush