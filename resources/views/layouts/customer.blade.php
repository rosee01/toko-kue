<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Toko Kue - Manisnya Setiap Momen')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @include('partials.sweetalert-style')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --cream: #f8eee7; --cream-light: #fdf9f6; --brown: #754b3a;
            --brown-dark: #4d3026; --brown-light: #9b6a54; --text: #392822;
            --muted: #87766e; --shadow-sm: 0 5px 20px rgba(117, 75, 58, 0.06);
            --shadow-md: 0 12px 35px rgba(117, 75, 58, 0.10);
        }
        * { box-sizing: border-box; font-family: 'Source Sans 3', sans-serif; }
        html { scroll-behavior: smooth; }
        body { margin: 0; background: var(--cream-light); color: var(--text); overflow-x: hidden; }
        a { text-decoration: none; }
        img { max-width: 100%; display: block; }
        .container-custom { width: min(1200px, calc(100% - 40px)); margin: auto; }

        /* NAVBAR */
        .main-navbar { min-height: 80px; background: rgba(255, 255, 255, 0.97); border-bottom: 1px solid #eee4de; position: sticky; top: 0; z-index: 1000; }
        .navbar-inner { min-height: 80px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .brand { display: flex; align-items: center; gap: 12px; color: var(--brown-dark); font-size: 28px; font-weight: 700; white-space: nowrap; }
        .brand-icon { width: 45px; height: 45px; border: 2px solid var(--brown); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--brown); font-size: 24px; }

        .nav-center { display: flex; align-items: center; gap: 28px; height: 100%; }
        .nav-item-custom { position: relative; color: var(--text); font-size: 16px; font-weight: 600; padding: 27px 0; transition: .2s; cursor: pointer; }
        .nav-item-custom:hover, .nav-item-custom.active { color: var(--brown); }
        .nav-item-custom.active::after { content: ""; position: absolute; left: 0; right: 0; bottom: 14px; height: 3px; background: var(--brown); border-radius: 5px; }
        .nav-right { display: flex; align-items: center; gap: 14px; }
        .search-box { width: 220px; height: 42px; border: 1px solid #e4d7cf; border-radius: 25px; display: flex; align-items: center; gap: 10px; padding: 0 15px; }
        .search-box i { font-size: 20px; color: var(--brown); }
        .search-box input { border: none; outline: none; background: transparent; width: 100%; color: var(--text); font-size: 14px; }
        .nav-icon { color: var(--brown-dark); font-size: 24px; position: relative; cursor: pointer; }
        .nav-icon:hover, .nav-icon.active { color: var(--brown); }
        .cart-badge { position: absolute; top: -8px; right: -10px; min-width: 20px; height: 20px; padding: 0 5px; border-radius: 10px; background: var(--brown); color: white; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
        .user-chip { display: flex; align-items: center; gap: 10px; border: 0; background: none; color: var(--brown-dark); font-weight: 600; font-size: 15px; cursor: pointer; }
        .avatar-circle { width: 38px; height: 38px; border-radius: 50%; background: var(--brown); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .dropdown-menu { border: 1px solid #eaded7; border-radius: 12px; box-shadow: var(--shadow-md); padding: 8px; }
        .dropdown-item { border-radius: 8px; font-size: 14px; font-weight: 600; color: var(--text); padding: 9px 12px; }
        .dropdown-item:hover { background: var(--cream); color: var(--brown); }
        .mobile-nav-toggle { display: none; width: 38px; height: 38px; border: 1px solid #eaded7; border-radius: 9px; background: #fff; color: var(--brown); font-size: 19px; }
        .mobile-nav { border-top: 1px solid #f0e6df; background: #fff; }
        .mobile-nav-inner { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 4px; padding: 10px 0 13px; }
        .mobile-nav .nav-item-custom { padding: 10px 8px; border-radius: 7px; font-size: 14px; text-align: center; }
        .mobile-nav .nav-item-custom:hover, .mobile-nav .nav-item-custom.active { background: var(--cream); }
        .mobile-nav .nav-item-custom.active::after { display: none; }
        :focus-visible { outline: 3px solid rgba(117,75,58,.38); outline-offset: 3px; }
        .search-box:focus-within { border-color: var(--brown); box-shadow: 0 0 0 3px rgba(117,75,58,.08); }
        .checkout-payment-options { display:grid; gap:8px; }
        .checkout-payment-option { display:flex; align-items:center; gap:10px; min-height:54px; padding:9px 11px; border:1px solid #e8dcd4; border-radius:10px; background:#fff; cursor:pointer; transition:border-color .15s ease,background .15s ease; }
        .checkout-payment-option:hover { border-color:#c5a493; background:#fffdfa; }
        .checkout-payment-option:has(input:checked) { border-color:var(--brown); background:#fbf4ef; box-shadow:0 0 0 2px rgba(117,75,58,.07); }
        .checkout-payment-option input { width:16px; height:16px; flex:none; accent-color:var(--brown); }
        .checkout-payment-icon { display:grid; place-items:center; width:34px; height:34px; flex:none; border-radius:9px; background:#f5e9e1; color:var(--brown); }
        .checkout-payment-option strong,.checkout-payment-option small { display:block; }
        .checkout-payment-option strong { color:var(--brown-dark); font-size:13px; }
        .checkout-payment-option small { margin-top:2px; color:var(--muted); font-size:11px; }
        .checkout-payment-help { display:flex; align-items:flex-start; gap:7px; margin-top:9px; color:var(--muted); font-size:12px; line-height:1.45; }
        .checkout-payment-help i { flex:none; color:var(--brown-light); }
        .checkout-payment-details { margin-top:8px; padding:9px 11px; border:1px solid #e9ded6; border-radius:8px; background:#fff; color:#6f5b50; font-size:12px; line-height:1.5; }

        /* PANEL HALAMAN CUSTOMER (Keranjang & Pesanan Saya) */
        .page-wrap { background: linear-gradient(160deg, #f5eae2 0%, #fbf5f1 55%, #f2e3d8 100%); min-height: calc(100vh - 80px); padding: 36px 0 60px; }
        .panel { background: rgba(255,255,255,.92); border: 1px solid #ecdcd2; border-radius: 20px; box-shadow: var(--shadow-md); padding: 26px; }
        .panel-title { display: flex; align-items: center; gap: 11px; color: var(--brown-dark); font-size: 25px; font-weight: 700; margin: 0; }
        .panel-title i { font-size: 30px; color: var(--brown); }
        .panel-sub { color: var(--muted); margin: 5px 0 20px; }

        /* Responsive */
        @media (max-width: 1200px) { .nav-center { gap: 16px; } .nav-item-custom { font-size: 15px; } .search-box { width: 180px; } .brand { font-size: 24px; } }
        @media (max-width: 1080px) { .nav-center { display: none; } .mobile-nav-toggle { display: inline-grid; place-items: center; } .search-box { display: none; } }
        @media (max-width: 600px) { .container-custom { width: min(100% - 24px, 1120px); } .brand { font-size: 20px; gap: 8px; } .brand-icon { width: 38px; height: 38px; } .panel { padding: 17px; border-radius: 16px; } .panel-title { font-size: 22px; } .panel-title i { font-size: 25px; } .nav-right { gap: 11px; } .mobile-nav-inner { grid-template-columns: repeat(2,minmax(0,1fr)); } }
    </style>
    @stack('styles')
</head>
<body>


<!-- NAVBAR -->
<nav class="main-navbar">
    <div class="container-custom navbar-inner">
        <a href="{{ route('beranda') }}" class="brand">
            <span class="brand-icon"><i class="bi bi-cake2"></i></span>
            {{ \App\Models\Pengaturan::ambil('nama_toko', config('app.name')) }}
        </a>
        <div class="nav-center">
            <a href="{{ route('beranda') }}#beranda" class="nav-item-custom {{ request()->routeIs('beranda') ? 'active' : '' }}" data-target="beranda">Beranda</a>
            <a href="{{ route('beranda') }}#menu" class="nav-item-custom" data-target="menu">Menu</a>
            <a href="{{ route('beranda') }}#kategori" class="nav-item-custom" data-target="kategori">Kategori</a>
            <a href="{{ route('beranda') }}#tentang" class="nav-item-custom" data-target="tentang">Tentang</a>
            <a href="{{ route('beranda') }}#kontak" class="nav-item-custom" data-target="kontak">Kontak</a>
            @auth
                <a href="{{ route('order.riwayat') }}" class="nav-item-custom {{ request()->routeIs('order.riwayat') ? 'active' : '' }}">Pesanan</a>
            @else
                <a href="#" class="nav-item-custom" onclick="showLoginAlert(); return false;">Pesanan</a>
            @endauth
        </div>
        <div class="nav-right">
            @yield('nav-search')
            <button class="mobile-nav-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#customerMobileNav" aria-controls="customerMobileNav" aria-expanded="false" aria-label="Buka navigasi">
                <i class="bi bi-list"></i>
            </button>

            @auth
                <a href="{{ route('keranjang') }}" class="nav-icon {{ request()->routeIs('keranjang') ? 'active' : '' }}" title="Keranjang">
                    <i class="bi bi-bag"></i>
                    <span class="cart-badge" id="cartBadge">0</span>
                </a>
                <div class="dropdown">
                    <button class="user-chip dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar-circle"><i class="bi bi-person-fill"></i></span>
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>

                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if(auth()->user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard admin</a></li>
                        @endif
                        <li><a class="dropdown-item" href="{{ route('order.riwayat') }}"><i class="bi bi-card-checklist me-2"></i>Pesanan Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="nav-icon" title="Login"><i class="bi bi-person"></i></a>
                <div class="nav-icon" onclick="showLoginAlert()" title="Login untuk membuka keranjang">
                    <i class="bi bi-bag"></i>
                    <span class="cart-badge" id="cartBadge">0</span>
                </div>
            @endauth
        </div>
    </div>
    <div class="collapse mobile-nav" id="customerMobileNav">
        <div class="container-custom mobile-nav-inner">
            <a href="{{ route('beranda') }}#beranda" class="nav-item-custom {{ request()->routeIs('beranda') ? 'active' : '' }}" data-target="beranda">Beranda</a>
            <a href="{{ route('beranda') }}#menu" class="nav-item-custom" data-target="menu">Menu</a>
            <a href="{{ route('beranda') }}#kategori" class="nav-item-custom" data-target="kategori">Kategori</a>
            <a href="{{ route('beranda') }}#tentang" class="nav-item-custom" data-target="tentang">Tentang</a>
            <a href="{{ route('beranda') }}#kontak" class="nav-item-custom" data-target="kontak">Kontak</a>
            @auth
                <a href="{{ route('order.riwayat') }}" class="nav-item-custom {{ request()->routeIs('order.riwayat') ? 'active' : '' }}">Pesanan</a>
            @else
                <a href="{{ route('login') }}" class="nav-item-custom">Login untuk melihat pesanan</a>
            @endauth
        </div>
    </div>
</nav>

@yield('content')

@yield('footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.AppAlert = Swal.mixin({
        customClass: {
            popup: 'app-alert-popup',
            title: 'app-alert-title',
            htmlContainer: 'app-alert-text',
            actions: 'app-alert-actions',
            confirmButton: 'app-alert-confirm',
            cancelButton: 'app-alert-cancel',
        },
        confirmButtonColor: '#754b3a',
        cancelButtonColor: '#6c757d',
    });

    document.addEventListener('error', function (event) {
        const image = event.target;
        if (image instanceof HTMLImageElement && !image.dataset.fallbackApplied) {
            image.dataset.fallbackApplied = 'true';
        image.classList.add('cake-photo-crop');
        image.src = @json(asset('images/login-hero.jpg'));
    }
    }, true);

    const SUDAH_LOGIN = @json(auth()->check());
    const CART_KEY = 'tokoKueCart';
    const URL_LOGIN = @json(route('login'));

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function rupiah(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    // Keranjang disimpan di browser dan hanya untuk yang sudah login
    function getCart() {
        if (!SUDAH_LOGIN) return [];
        try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; } catch (e) { return []; }
    }

    function saveCart(cart) {
        if (SUDAH_LOGIN) localStorage.setItem(CART_KEY, JSON.stringify(cart));
        updateCartBadge(cart);
    }

    function updateCartBadge(cart = getCart()) {
        const el = document.getElementById('cartBadge');
        if (el) el.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    }

    function showLoginAlert() {
        window.AppAlert.fire({
            icon: 'warning',
            title: 'Login Diperlukan',

            text: 'Anda harus login terlebih dahulu untuk memesan atau menambah ke keranjang.',
            confirmButtonText: 'Login Sekarang',
            cancelButtonText: 'Batal',
            showCancelButton: true,
            confirmButtonColor: '#754b3a',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = URL_LOGIN;
        });
    }

    // Kosongkan keranjang saat logout supaya tidak terbaca akun lain di browser yang sama
    document.querySelectorAll('form[action="{{ route('logout') }}"]').forEach(f => {
        f.addEventListener('submit', () => localStorage.removeItem(CART_KEY));
    });

    document.querySelectorAll('#customerMobileNav .nav-item-custom').forEach(link => {
        link.addEventListener('click', () => {
            const menu = document.getElementById('customerMobileNav');
            bootstrap.Collapse.getOrCreateInstance(menu).hide();
        });
    });

    function updatePaymentDetails(container) {
        const group = container.querySelector('.checkout-payment-options');
        if (!group) return;
        const selected = group.querySelector('input[name="metode_pembayaran"]:checked');
        container.querySelectorAll('[data-payment-details]').forEach(detail => {
            detail.hidden = detail.dataset.paymentDetails !== selected?.value;
        });
    }

    document.querySelectorAll('.checkout-payment-options').forEach(group => {
        group.addEventListener('change', () => updatePaymentDetails(group.parentElement));
        updatePaymentDetails(group.parentElement);
    });

    document.addEventListener('DOMContentLoaded', function () {
        updateCartBadge();

        @if(session('error'))
            window.AppAlert.fire({ icon: 'error', title: 'Oops', text: @json(session('error')), confirmButtonColor: '#754b3a' });
        @elseif(session('success'))
            window.AppAlert.fire({ icon: 'success', title: @json(session('success')), confirmButtonColor: '#754b3a' });
        @elseif($errors->any())
            window.AppAlert.fire({ icon: 'error', title: 'Data tidak valid', text: @json($errors->first()), confirmButtonColor: '#754b3a' });
        @endif
    });
</script>
@stack('scripts')
</body>
</html>