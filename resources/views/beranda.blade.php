@extends('layouts.customer')

@section('title', $toko['nama'].' - '.$toko['slogan'])

@push('styles')
<style>
    .hero, .menu-section, .category-section, .about-section { scroll-margin-top: 92px; }

    /* HERO */
    .hero { min-height: 500px; background: linear-gradient(110deg, #fbf5f0 0%, #f8eee7 52%, #efd9c8 100%); position: relative; overflow: hidden; }
    .hero-inner { min-height: 500px; display: grid; grid-template-columns: 46% 54%; align-items: center; }
    .hero-content { padding: 64px 28px 64px 0; position: relative; z-index: 3; }
    .hero-label { display: inline-flex; padding: 8px 16px; background: #f1dfd2; border: 1px solid #ead7ca; border-radius: 30px; color: var(--brown); font-size: 12px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; margin-bottom: 19px; }
    .hero-title { font-size: clamp(42px, 4.2vw, 56px); line-height: 1.08; font-weight: 700; color: var(--brown-dark); margin: 0 0 18px; max-width: 550px; }
    .hero-title span { color: var(--brown-light); }
    .hero-description { max-width: 450px; font-size: 16px; line-height: 1.75; color: #6f625c; margin-bottom: 26px; }
    .btn-hero { display: inline-flex; align-items: center; gap: 11px; background: var(--brown); color: white; padding: 13px 23px; border-radius: 10px; font-size: 15px; font-weight: 600; transition: .25s; box-shadow: 0 8px 20px rgba(77,48,38,.14); }
    .btn-hero:hover { background: var(--brown-dark); color: white; transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .hero-image-area { height: 480px; min-width: 0; margin-right: 18px; position: relative; overflow: visible; }
    .hero-image { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; }
    .hero-image.cake-photo-crop { object-position: center bottom; transform: scale(1.24); transform-origin: center 84%; mix-blend-mode: multiply; -webkit-mask-image: radial-gradient(ellipse 52% 72% at 50% 55%, #000 45%, rgba(0,0,0,.92) 64%, transparent 96%); mask-image: radial-gradient(ellipse 52% 72% at 50% 55%, #000 45%, rgba(0,0,0,.92) 64%, transparent 96%); }
    .hero-image.has-store-banner { border-radius: 24px 0 0 24px; object-position: center; }
    .hero-badge { position: absolute; z-index: 5; background: white; padding: 14px 20px; border-radius: 16px; box-shadow: var(--shadow-md); display: flex; align-items: center; gap: 12px; }
    .hero-badge i { color: #d39c32; font-size: 28px; }
    .hero-badge strong { display: block; color: var(--brown-dark); font-size: 20px; }
    .hero-badge span { display: block; color: var(--muted); font-size: 12px; }
    .badge-rating { top: 50px; right: 70px; }
    .badge-delivery { left: 30px; bottom: 50px; }
    .badge-delivery i { color: var(--brown); }

    /* STATS */
    .stats { background: #fffaf7; border-bottom: 1px solid #f0e3da; }
    .stats-inner { min-height: 100px; display: grid; grid-template-columns: repeat(3, 1fr); }
    .stat { display: flex; align-items: center; justify-content: center; gap: 16px; position: relative; }
    .stat:not(:last-child)::after { content: ""; position: absolute; right: 0; top: 25px; width: 1px; height: 50px; background: #eadbd2; }

    .stat-icon { width: 50px; height: 50px; border-radius: 50%; background: #f8e9df; display: flex; align-items: center; justify-content: center; color: var(--brown); font-size: 24px; }
    .stat-number { font-size: 28px; line-height: 1; font-weight: 700; color: var(--brown-dark); }
    .stat-label { font-size: 14px; color: var(--muted); margin-top: 4px; }

    /* SECTIONS GENERAL */
    .section-label { display: inline-block; font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--brown-light); background: #f5e6dc; border-radius: 20px; padding: 6px 16px; margin-bottom: 10px; }
    .section-title { color: var(--brown-dark); font-size: 36px; line-height: 1.2; font-weight: 700; margin: 0; }
    .section-link { color: var(--brown); font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
    .section-link:hover { color: var(--brown-dark); }

    /* MENU SECTION */
    .menu-section { background: #fff; padding: 80px 0; }
    .menu-header { display: flex; justify-content: space-between; align-items: end; margin-bottom: 30px; }
    .category-filter { display: flex; gap: 12px; margin-bottom: 30px; flex-wrap: wrap; }
    .filter-btn { background: var(--cream-light); border: 1px solid #eaded7; color: var(--brown); padding: 10px 20px; border-radius: 25px; font-size: 14px; font-weight: 600; cursor: pointer; transition: .2s; }
    .filter-btn:hover, .filter-btn.active { background: var(--brown); color: white; border-color: var(--brown); }
    .menu-products { display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px; }
    .product-card { background: white; border: 1px solid #eaded7; border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-sm); transition: .25s; }
    .product-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-md); }
    .product-image-wrap { height: 210px; position: relative; overflow: hidden; background: #fbf5f1; }
    .product-image { width: 100%; height: 100%; object-fit: cover; object-position: center; transition: .35s; }
    .product-card:hover .product-image { transform: scale(1.06); }
    .product-image.cake-photo-crop { transform: scale(1.7); transform-origin: center 88%; }
    .product-card:hover .product-image.cake-photo-crop { transform: scale(1.82); }
    .product-card.soldout .product-image { filter: grayscale(1); opacity: .65; }
    .product-tag { position: absolute; top: 12px; left: 12px; background: rgba(255,255,255,.95); padding: 6px 12px; border-radius: 15px; color: var(--brown); font-size: 11px; font-weight: 600; }
    .product-soldout { position: absolute; top: 12px; right: 12px; background: #c8574f; color: #fff; padding: 6px 12px; border-radius: 15px; font-size: 11px; font-weight: 700; }
    .product-body { padding: 17px; }
    .product-name { color: var(--brown-dark); font-size: 18px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 10px; }
    .product-price { color: var(--brown); font-size: 20px; font-weight: 700; margin-bottom: 8px; }
    .product-stock { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #5f8a6b; margin-bottom: 15px; }
    .product-stock.menipis { color: #b7791f; }
    .product-stock.habis { color: #c8574f; }
    .product-actions { display: flex; gap: 10px; }

    .btn-action { flex: 1; border: none; border-radius: 8px; padding: 10px; font-size: 13px; font-weight: 600; transition: .2s; display: flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; }
    .btn-action:disabled { background: #e5dcd6; color: #9a8c84; cursor: not-allowed; }
    .btn-order { background: var(--brown); color: white; }
    .btn-order:hover:not(:disabled) { background: var(--brown-dark); }
    .btn-cart { background: var(--cream); color: var(--brown); border: 1px solid var(--brown); }
    .btn-cart:hover { background: var(--brown); color: white; }
    .btn-view-all { display: block; margin: 40px auto 0; background: var(--brown); color: white; border: none; padding: 14px 32px; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; transition: .2s; }
    .btn-view-all:hover { background: var(--brown-dark); }

    /* CATEGORY SECTION */
    .category-section { background: #fbf5f1; padding: 80px 0; }
    .category-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; }
    .category-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    .category-item { width: 100%; background: #fff; border: 1px solid #eee0d8; border-radius: 16px; min-height: 195px; padding: 20px; color: inherit; font: inherit; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: .25s; cursor: pointer; }
    .category-item:hover { transform: translateY(-8px); border-color: #e2c9ba; box-shadow: var(--shadow-md); }
    .category-icon { width: 76px; height: 76px; overflow: hidden; border-radius: 16px; background: #f5e5da; display: flex; align-items: center; justify-content: center; color: var(--brown); font-size: 32px; margin-bottom: 12px; }
    .category-photo { width: 100%; height: 100%; object-fit: cover; }
    .category-number { color: #bd927b; font-size: 14px; margin-bottom: 8px; }
    .category-name { color: var(--brown-dark); font-size: 18px; font-weight: 600; margin-bottom: 8px; }
    .category-count { color: var(--muted); font-size: 14px; }
    .category-arrow { margin-top: 15px; width: 35px; height: 35px; border-radius: 50%; background: var(--brown); color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; }

    /* ABOUT */
    .about-section { background: linear-gradient(100deg, #f7e9df, #fbf3ed); padding: 80px 0; }
    .about-grid { display: grid; grid-template-columns: 1fr 1.3fr; gap: 60px; align-items: center; }
    .about-image-wrapper { position: relative; overflow: hidden; border-radius: 20px; background: #f1dfd2; box-shadow: var(--shadow-md); }
    .about-image { width: 100%; height: 400px; object-fit: cover; object-position: center 78%; transform: scale(1.8); transform-origin: center 90%; }
    .about-badge { position: absolute; bottom: 25px; left: 25px; background: white; border-radius: 14px; padding: 18px 25px; box-shadow: var(--shadow-md); display: flex; align-items: center; gap: 15px; }
    .about-badge i { color: var(--brown); font-size: 32px; }
    .about-badge strong { display: block; font-size: 28px; color: var(--brown); }
    .about-badge span { display: block; font-size: 13px; color: var(--muted); }
    .about-content { position: relative; }
    .about-title { font-size: 42px; line-height: 1.1; color: var(--brown-dark); max-width: 500px; margin: 0 0 20px; font-weight: 700; }

    .about-description { color: var(--muted); font-size: 16px; line-height: 1.7; max-width: 550px; margin-bottom: 28px; }
    .features { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
    .feature { display: flex; gap: 18px; }
    .feature-icon { flex-shrink: 0; width: 60px; height: 60px; background: white; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: var(--brown); font-size: 28px; }
    .feature-title { font-size: 16px; font-weight: 700; color: var(--brown-dark); margin-bottom: 5px; }
    .feature-text { font-size: 14px; line-height: 1.5; color: var(--muted); margin: 0; }
    .about-button { position: absolute; right: 0; top: 0; background: var(--brown); color: white; padding: 14px 28px; border-radius: 10px; font-size: 14px; font-weight: 600; }
    .about-button:hover { color: white; background: var(--brown-dark); }

    /* FOOTER */
    .footer { background: var(--brown-dark); color: white; padding: 60px 0 30px; }
    .footer-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; }
    .footer-text { color: rgba(255,255,255,.65); font-size: 14px; line-height: 1.8; }
    .footer-link { display: block; color: rgba(255,255,255,.65); font-size: 14px; margin-bottom: 10px; }
    .footer-link:hover { color: white; }
    .footer-bottom { border-top: 1px solid rgba(255,255,255,.1); padding-top: 20px; margin-top: 40px; text-align: center; color: rgba(255,255,255,.4); font-size: 13px; }

    /* ORDER MODAL */
    .order-modal-dialog { width:min(560px,calc(100% - 28px)); max-width:none; }
    .order-modal-content { overflow:hidden; border:1px solid #e9ddd5; border-radius:16px; background:#fff; box-shadow:0 22px 64px rgba(57,40,34,.22); }
    .order-modal-header { align-items:center; padding:16px 22px; border-bottom:1px solid #eee6e1; background:linear-gradient(120deg,#fcf8f5,#f8f0ea); }
    .order-modal-title { display:flex; align-items:center; gap:10px; margin:0; color:var(--brown-dark); font-size:18px; font-weight:700; }
    .order-modal-title i { display:grid; place-items:center; width:34px; height:34px; border:1px solid #ead8cc; border-radius:9px; background:#f4e8df; color:var(--brown); font-size:16px; }
    .order-modal-header .btn-close { width:13px; height:13px; padding:9px; opacity:.55; }
    .order-modal-body { max-height:min(72vh,680px); overflow-y:auto; padding:19px 22px 12px; }
    .order-product-summary { margin-bottom:16px; padding:13px 15px; border:1px solid #eee3dc; border-radius:10px; background:#fcf9f7; }
    .order-product-label { color:#82746c; font-size:12px; }
    .order-product-name { margin-top:2px; color:var(--brown-dark); font-size:17px; font-weight:700; }
    .order-product-meta { display:flex; align-items:center; justify-content:space-between; gap:8px; margin-top:4px; }
    .order-product-price { color:#6d574b; font-size:12px; font-weight:500; }
    .order-product-stock { color:#5f8a6b; font-size:11px; font-weight:600; white-space:nowrap; }
    .order-fields { display:grid; grid-template-columns:1fr 1fr; gap:14px 16px; }
    .order-field { min-width:0; margin:0 !important; }
    .order-field.full { grid-column:1 / -1; }
    .order-field .form-label { display:block; margin-bottom:6px; color:#57443b; font-size:13px; font-weight:600; }
    .order-field .form-control { min-height:40px; padding:8px 11px; border:1px solid #e5d9d2; border-radius:8px; color:#45372f; font-size:13px; }
    .order-field textarea.form-control { min-height:74px; resize:vertical; }
    .order-field .form-control:focus { border-color:#a97c66; box-shadow:0 0 0 3px rgba(117,75,58,.1); }
    .delivery-help { display:flex; align-items:flex-start; gap:7px; margin:9px 0 0; color:#7d716a; font-size:11px; line-height:1.5; }
    .delivery-help i { flex:none; margin-top:1px; color:#9a705a; }
    .delivery-state { display:flex; align-items:flex-start; gap:10px; padding:12px; border:1px solid #e8e0db; border-radius:9px; background:#faf8f6; color:#5f554f; font-size:12px; line-height:1.5; }
    .delivery-state-icon { display:grid; place-items:center; width:30px; height:30px; flex:none; border-radius:8px; background:#f0e9e4; color:#775644; }
    .delivery-state strong { display:block; margin-bottom:2px; color:#49382f; font-size:12px; }
    .delivery-state p { margin:0; color:#756a64; }
    .delivery-state.is-error { border-color:#eed8d2; background:#fff8f6; }
    .delivery-state.is-error .delivery-state-icon { background:#f8e6e1; color:#a34f43; }
    .delivery-state.is-loading { border-color:#e8e0db; background:#fbfaf9; }
    .delivery-options-list { display:grid; gap:8px; margin-top:9px; }
    .delivery-option { display:flex; gap:10px; align-items:flex-start; padding:11px 12px; border:1px solid #e7dfda; border-radius:9px; background:#fff; cursor:pointer; transition:border-color .15s ease,background .15s ease,box-shadow .15s ease; }
    .delivery-option:hover { border-color:#c9aa99; background:#fdfaf8; }
    .delivery-option:has(input:checked) { border-color:#9c715b; background:#fbf5f1; box-shadow:0 0 0 2px rgba(117,75,58,.07); }
    .delivery-option input { flex:none; margin-top:3px; accent-color:var(--brown); }
    .delivery-option-copy { min-width:0; flex:1; }
    .delivery-option-heading { display:flex; justify-content:space-between; gap:10px; color:#49372e; font-size:13px; font-weight:700; }
    .delivery-option-price { color:#704b3b; white-space:nowrap; }
    .delivery-option-description { display:block; margin-top:3px; color:#81756e; font-size:11px; line-height:1.4; }
    .delivery-distance { margin-top:8px; color:#5d6f62; font-size:11px; font-weight:600; }
    .delivery-calculate { min-height:36px; padding:7px 11px; border-color:#d9cbc2; border-radius:7px; color:#624a3e; font-size:12px; font-weight:600; }
    .delivery-calculate:hover { border-color:#9b715d; background:#faf5f1; color:#503a30; }
    .order-modal-footer { display:flex; justify-content:flex-end; gap:9px; padding:13px 22px 16px; border-top:1px solid #eee6e1; background:#fdfcfb; }
    .order-modal-footer .btn { min-height:38px; padding:8px 14px; border-radius:8px; font-size:13px; }
    .order-modal-footer .btn-secondary { border:1px solid #e2d8d1; background:#fff; color:#70594c; }
    .order-modal-footer .btn-secondary:hover { background:#f7f2ee; color:#5f493f; }
    .btn-submit { display:inline-flex; align-items:center; justify-content:center; gap:7px; border:1px solid var(--brown); background:var(--brown); color:#fff; font-size:13px; font-weight:600; }
    .btn-submit:hover { border-color:var(--brown-dark); background:var(--brown-dark); color:#fff; }
    @media (max-width:520px) {
        .order-modal-dialog { width:calc(100% - 20px); margin:10px auto; }
        .order-modal-body { max-height:calc(100dvh - 170px); padding:15px 15px 9px; }
        .order-modal-header { padding:13px 15px; }
        .order-modal-footer { padding:11px 15px 13px; }
        .order-fields { gap:11px; }
        .order-field .form-label { font-size:12px; }
        .delivery-option-heading { flex-wrap:wrap; }
    }

    @media (max-width: 1100px) { .menu-products, .category-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 850px) { .hero-inner { grid-template-columns: 1fr; } .hero-content { padding: 48px 0 8px; } .hero-image-area { height: clamp(300px, 58vw, 420px); margin: 0 0 24px; } .hero-image.cake-photo-crop { transform: scale(1.18); -webkit-mask-image: radial-gradient(ellipse 62% 72% at 50% 55%, #000 45%, rgba(0,0,0,.92) 64%, transparent 96%); mask-image: radial-gradient(ellipse 62% 72% at 50% 55%, #000 45%, rgba(0,0,0,.92) 64%, transparent 96%); } .hero-image.has-store-banner { border-radius: 18px; } .stats-inner { grid-template-columns: repeat(3, 1fr); } .stat { padding: 20px 0; gap: 10px; } .stat:nth-child(2)::after { display: none; } .about-grid { grid-template-columns: 1fr; gap: 34px; } .about-image { height: 360px; } .about-button { position: static; display: inline-block; margin-bottom: 20px; } .menu-products, .category-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .hero-title { font-size: 40px; } .hero-image-area { height: clamp(260px, 72vw, 340px); margin-bottom: 20px; } .hero-image.cake-photo-crop { transform: scale(1.12); } .menu-products, .category-grid { grid-template-columns: 1fr; } .product-image-wrap { height: 230px; } .features { grid-template-columns: 1fr; gap: 18px; } .section-title { font-size: 29px; } .menu-header, .category-header { align-items: flex-start; flex-direction: column; gap: 14px; } .menu-section, .category-section, .about-section { padding: 54px 0; } .stats-inner { grid-template-columns: repeat(2, 1fr); } .stat:last-child { grid-column: 1 / -1; } .stat:nth-child(2)::after { display: none; } .stat-number { font-size: 23px; } .stat-label { font-size: 12px; } .about-image { height: 290px; } }
</style>
@endpush


@section('nav-search')
    <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" id="productSearch" placeholder="Cari kue favoritmu...">
    </div>
@endsection

@section('content')

<!-- HERO -->
<section class="hero" id="beranda">
    <div class="container-custom hero-inner">
        <div class="hero-content">
            <div class="hero-label">{{ $toko['nama'] }}</div>
            <h1 class="hero-title">Kue dan roti untuk <span>setiap perayaan.</span></h1>
            <p class="hero-description">Pilih kue favorit untuk merayakan momen spesial atau menemani waktu santai. Pesanan dapat dikirim langsung melalui website.</p>
            <a href="#menu" class="btn-hero"><i class="bi bi-cake2"></i> Lihat Menu <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="hero-image-area">
            <img src="{{ $toko['banner'] ? asset('storage/'.$toko['banner']) : asset('images/login-hero.jpg') }}" alt="Foto kue dari {{ $toko['nama'] }}" class="hero-image {{ $toko['banner'] ? 'has-store-banner' : 'cake-photo-crop' }}">
        </div>
    </div>
</section>


<!-- STATS -->
<section class="stats">
    <div class="container-custom stats-inner">
        <div class="stat">
            <div class="stat-icon"><i class="bi bi-cake2"></i></div>
            <div><div class="stat-number">{{ number_format($totalMenu) }}</div><div class="stat-label">Pilihan Menu</div></div>
        </div>
        <div class="stat">
            <div class="stat-icon"><i class="bi bi-grid"></i></div>
            <div><div class="stat-number">{{ number_format($totalKategori) }}</div><div class="stat-label">Kategori Aktif</div></div>
        </div>
        <div class="stat">
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            <div><div class="stat-number">{{ number_format($totalStokTersedia) }}</div><div class="stat-label">Menu tersedia</div></div>
        </div>
    </div>
</section>

<!-- MENU SECTION -->
<section class="menu-section" id="menu">
    <div class="container-custom">
        <div class="menu-header">
            <div>
                <div class="section-label">Menu Favorit</div>
                <h2 class="section-title">Pilihan terbaik untuk hari spesialmu.</h2>
            </div>
            <a href="#menu" class="section-link" id="viewAllMenuLink">Lihat semua menu <i class="bi bi-arrow-right"></i></a>

        </div>

        <div class="category-filter" id="categoryFilter">
            <button class="filter-btn active" data-filter="all">Semua</button>
            @foreach($kategoris as $kat)
                <button class="filter-btn" data-filter="{{ strtolower($kat->nama_kategori) }}">{{ $kat->nama_kategori }}</button>
            @endforeach
        </div>

        <div class="menu-products" id="menuProducts">
            @php $allProducts = $menu->flatten(); @endphp

            @foreach($allProducts as $index => $produk)
                @php
                    $stok = (int) $produk->stok;
                    $foto = $produk->foto ? asset('storage/' . $produk->foto) : asset('images/login-hero.jpg');
                @endphp
                <div class="product-card {{ $stok <= 0 ? 'soldout' : '' }}"
                     data-category="{{ strtolower($produk->kategori->nama_kategori ?? '') }}"
                     data-id="{{ $produk->id_produk }}"
                     data-name="{{ $produk->name_produk }}"
                     data-price="{{ $produk->harga }}"
                     data-stok="{{ $stok }}"
                     data-image="{{ $foto }}"
                     style="{{ $index >= 8 ? 'display: none;' : '' }}">

                    <div class="product-image-wrap">
                        <img src="{{ $foto }}" alt="{{ $produk->name_produk }}" class="product-image {{ $produk->foto ? '' : 'cake-photo-crop' }}">
                        <span class="product-tag">{{ $produk->kategori->nama_kategori ?? 'Kue' }}</span>
                        @if($stok <= 0)<span class="product-soldout">Habis</span>@endif

                    </div>
                    <div class="product-body">
                        <div class="product-name">{{ $produk->name_produk }}</div>
                        <div class="product-price">Rp {{ number_format($produk->harga, 0, ',', '.') }}</div>
                        <div class="product-stock {{ $stok <= 0 ? 'habis' : ($stok <= $produk->stok_minimum ? 'menipis' : '') }}">
                            <i class="bi bi-box-seam"></i>
                            @if($stok > 0)
                                <span>Stok: {{ $stok }}</span>
                            @else
                                <span>Stok habis</span>
                            @endif
                        </div>
                        <div class="product-actions">
                            @if($stok <= 0)
                                <button class="btn-action btn-order" disabled><i class="bi bi-x-circle"></i> Tidak tersedia</button>
                            @elseif(auth()->check())
                                <button class="btn-action btn-order" onclick="handleOrder(this)">
                                    <i class="bi bi-bag-check"></i> Pesan
                                </button>
                                <button class="btn-action btn-cart" onclick="handleAddToCart(this)">
                                    <i class="bi bi-cart-plus"></i> Keranjang
                                </button>
                            @else
                                <button class="btn-action btn-order" onclick="showLoginAlert()">
                                    <i class="bi bi-lock-fill"></i> Pesan
                                </button>
                                <button class="btn-action btn-cart" onclick="showLoginAlert()">
                                    <i class="bi bi-lock-fill"></i> Keranjang
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <p id="menuEmptyState" class="text-center text-muted mt-4" style="display:none">Tidak ada menu yang cocok dengan pencarian atau kategori ini.</p>
        <button class="btn-view-all" id="btnViewAllMenu" onclick="toggleAllMenu()">
            <i class="bi bi-grid"></i> Lihat Semua Menu
        </button>
    </div>
</section>

<!-- CATEGORY SECTION -->
<section class="category-section" id="kategori">
    <div class="container-custom">
        <div class="category-header">
            <div>
                <div class="section-label">Kategori Kami</div>
                <h2 class="section-title">Temukan kue favoritmu berdasarkan kategori.</h2>
            </div>
            <a href="#kategori" class="section-link">Lihat semua kategori <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="category-grid">
            @foreach($kategoris as $index => $kat)
                <button type="button" class="category-item" data-filter="{{ strtolower($kat->nama_kategori) }}" onclick="filterByCategory(this.dataset.filter)">
                    <div class="category-icon">
                        @if($kat->foto)
                            <img src="{{ asset('storage/'.$kat->foto) }}" alt="" class="category-photo">
                        @else
                            <img src="{{ asset('images/login-hero.jpg') }}" alt="" class="category-photo cake-photo-crop">
                        @endif
                    </div>
                    <div class="category-number">0{{ $index + 1 }}</div>
                    <div class="category-name">{{ $kat->nama_kategori }}</div>
                    <div class="category-count">{{ $kat->produk_aktif_count }} Produk</div>
                    <div class="category-arrow"><i class="bi bi-arrow-right"></i></div>
                </button>
            @endforeach

        </div>
    </div>
</section>

<!-- ABOUT -->
<section class="about-section" id="tentang">
    <div class="container-custom">
        <div class="about-grid">
            <div class="about-image-wrapper">
                <img src="{{ asset('images/login-hero.jpg') }}" alt="Aneka kue dan roti pilihan dari {{ $toko['nama'] }}" class="about-image">
                <div class="about-badge">
                    <i class="bi bi-cake2"></i>
                    <div><strong>{{ number_format($totalMenu) }}</strong><span>Menu pilihan</span></div>
                </div>
            </div>
            <div class="about-content">
                <a href="#menu" class="about-button">Jelajahi Menu <i class="bi bi-arrow-right"></i></a>
                <div class="section-label">Tentang {{ $toko['nama'] }}</div>
                <h2 class="about-title">Temukan pilihan manis untuk setiap momen.</h2>
                <p class="about-description">Jelajahi koleksi kue dan roti yang tersedia, periksa informasi stok, lalu pesan dengan mudah melalui website {{ $toko['nama'] }}.</p>
                <div class="features">
                    <div class="feature">
                        <div class="feature-icon"><i class="bi bi-grid"></i></div>
                        <div><div class="feature-title">Pilihan Menu</div><p class="feature-text">{{ number_format($totalMenu) }} produk tersedia untuk dijelajahi.</p></div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon"><i class="bi bi-box-seam"></i></div>
                        <div><div class="feature-title">Stok Terpantau</div><p class="feature-text">Informasi ketersediaan menu ditampilkan sebelum memesan.</p></div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon"><i class="bi bi-bag-check"></i></div>
                        <div><div class="feature-title">Pesan Praktis</div><p class="feature-text">Pantau status pesanan dari akun Anda setelah checkout.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@auth
<!-- ORDER MODAL (pesan satu produk) -->
<div class="modal fade order-modal" id="orderModal" tabindex="-1" aria-labelledby="orderModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable order-modal-dialog">
        <div class="modal-content order-modal-content">
            <div class="modal-header order-modal-header">
                <h5 class="modal-title order-modal-title" id="orderModalTitle"><i class="bi bi-bag-check"></i> Form Pemesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="{{ route('order.store') }}" method="POST">
                @csrf
                <div class="modal-body order-modal-body">
                    <input type="hidden" name="produk_id" id="modal_produk_id">
                    <div class="order-product-summary">
                        <div class="order-product-label">Produk yang dipesan</div>
                        <div class="order-product-name" id="modal_produk_name">-</div>
                        <div class="order-product-meta">
                            <span class="order-product-price" id="modal_produk_price">Rp 0</span>
                            <span class="order-product-stock"><i class="bi bi-box-seam"></i> Stok <span id="modal_produk_stok">0</span></span>
                        </div>
                    </div>
                    <div class="order-fields">
                        <div class="order-field">
                            <label class="form-label" for="modal_nama">Nama penerima</label>
                            <input id="modal_nama" type="text" name="nama_pelanggan" class="form-control" required maxlength="255" autocomplete="name" value="{{ $kontakTerakhir?->nama_pelanggan ?? auth()->user()->name }}" placeholder="Nama lengkap">
                        </div>
                        <div class="order-field">
                            <label class="form-label" for="modal_telepon">WhatsApp / telepon</label>
                            <input id="modal_telepon" type="tel" name="no_telepon" class="form-control" required maxlength="20" autocomplete="tel" value="{{ $kontakTerakhir?->no_telepon }}" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="order-field full">
                            <label class="form-label" for="modal_alamat">Alamat pengiriman</label>
                            <textarea id="modal_alamat" name="alamat_pengiriman" class="form-control" rows="2" required autocomplete="street-address" placeholder="Jalan, nomor rumah, kelurahan, kecamatan">{{ $kontakTerakhir?->alamat_pengiriman }}</textarea>
                        </div>
                        <div class="order-field full">
                            <label class="form-label">Pilihan pengiriman</label>
                            <div id="modalDeliveryOptions" class="delivery-state" role="status" aria-live="polite">
                                <span class="delivery-state-icon"><i class="bi bi-geo-alt"></i></span>
                                <div><strong>Hitung biaya pengiriman</strong><p>Masukkan alamat lengkap untuk melihat jarak rute dan pilihan layanan.</p></div>
                            </div>
                            <div id="modalDeliveryDistance" class="delivery-distance"></div>
                            <button type="button" class="btn btn-outline-secondary delivery-calculate mt-2" id="modalHitungOngkir"><i class="bi bi-calculator"></i> Hitung ongkir</button>
                            <small class="delivery-help"><i class="bi bi-info-circle"></i><span>Jarak dihitung melalui Google Maps dari alamat toko. Alamat tujuan dikirim ke Google hanya untuk menghitung rute.</span></small>
                        </div>
                        <div class="order-field">
                            <label class="form-label" for="modal_jadwal">Waktu pengiriman yang diminta</label>
                            <input id="modal_jadwal" type="datetime-local" name="jadwal_diminta" class="form-control" min="{{ now()->addHour()->startOfMinute()->addMinutes(2)->format('Y-m-d\TH:i') }}" max="{{ now()->addDays(14)->format('Y-m-d\TH:i') }}" value="{{ now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i') }}" required>
                            <small class="text-muted">Jadwal dikonfirmasi admin setelah pesanan diterima.</small>
                        </div>
                        <div class="order-field full">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="modal_whatsapp" name="izin_notifikasi_whatsapp" value="1">
                                <label class="form-check-label" for="modal_whatsapp">Saya setuju menerima pembaruan pesanan melalui WhatsApp.</label>
                            </div>
                        </div>
                        <div class="order-field full">
                            <label class="form-label">Metode Pembayaran</label>
                            @include('partials.payment-method-options', ['informasiPembayaran' => $informasiPembayaran])
                        </div>
                        <div class="order-field">
                            <label class="form-label" for="modal_jumlah">Jumlah pesanan</label>
                            <input type="number" name="jumlah" id="modal_jumlah" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="order-field">
                            <label class="form-label" for="modal_catatan">Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                            <input id="modal_catatan" type="text" name="catatan" class="form-control" placeholder="Contoh: ucapan pada kue">
                        </div>
                    </div>
                </div>
                <div class="modal-footer order-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="modalSubmitOrder" class="btn-submit btn" disabled><i class="bi bi-check2-circle"></i> Kirim pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

@endsection

@section('footer')
@php
    $nomorWhatsApp = preg_replace('/\D+/', '', (string) $toko['whatsapp']);
@endphp
<footer class="footer" id="kontak">
    <div class="container-custom">
        <div class="row">
            <div class="col-lg-4 mb-4">

                <div class="brand text-white mb-3">
                    <span class="brand-icon"><i class="bi bi-cake2"></i></span>
                    {{ $toko['nama'] }}
                </div>
                <p class="footer-text">{{ $toko['slogan'] }}. Menyediakan kue dan roti untuk setiap perayaan Anda.</p>
            </div>
            <div class="col-lg-2 col-md-4 mb-4">
                <div class="footer-title">Menu</div>
                <a href="#menu" class="footer-link">Semua Menu</a>
                <a href="#kategori" class="footer-link">Kategori</a>
                <a href="#tentang" class="footer-link">Tentang</a>
            </div>
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="footer-title">Kontak</div>
                @if($toko['alamat'])
                    <div class="footer-text"><i class="bi bi-geo-alt"></i> {{ $toko['alamat'] }}</div>
                @endif
                @if($nomorWhatsApp)
                    <a class="footer-link" href="https://wa.me/{{ $nomorWhatsApp }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i> Hubungi via WhatsApp</a>
                @endif
                @unless($toko['alamat'] || $nomorWhatsApp)
                    <div class="footer-text">Informasi kontak toko belum tersedia.</div>
                @endunless
            </div>
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="footer-title">Pesanan</div>
                <div class="footer-text">Informasi status dan tindak lanjut pesanan tersedia di halaman Pesanan Saya.</div>
                @auth
                    <a href="{{ route('order.riwayat') }}" class="footer-link"><i class="bi bi-card-checklist"></i> Buka Pesanan Saya</a>
                @endauth
            </div>
        </div>
        <div class="footer-bottom">&copy; {{ date('Y') }} {{ $toko['nama'] }}. Hak cipta dilindungi.</div>
    </div>
</footer>
@endsection

@push('scripts')
<script>
    const DELIVERY_QUOTE_URL = @json(route('order.delivery-quote'));
    const DELIVERY_CSRF = @json(csrf_token());
    let modalDeliveryQuote = null;
    let modalQuotedAddress = '';
    let modalSelectedService = 'hemat';
    let modalQuoteRequest = 0;

    function setDeliveryState(title, message, type = 'info') {
        const state = document.getElementById('modalDeliveryOptions');
        const icon = type === 'error' ? 'bi-exclamation-circle' : (type === 'loading' ? 'bi-arrow-repeat' : 'bi-geo-alt');
        state.className = `delivery-state${type === 'error' ? ' is-error' : (type === 'loading' ? ' is-loading' : '')}`;
        state.replaceChildren();

        const iconWrap = document.createElement('span');
        iconWrap.className = 'delivery-state-icon';
        const iconElement = document.createElement('i');
        iconElement.className = `bi ${icon}`;
        iconWrap.append(iconElement);

        const content = document.createElement('div');
        const heading = document.createElement('strong');
        heading.textContent = title;
        const description = document.createElement('p');
        description.textContent = message;
        content.append(heading, description);
        state.append(iconWrap, content);
    }

    // ============================================
    // PESAN SATU PRODUK (modal)
    // ============================================
    function handleOrder(button) {
        const card = button.closest('.product-card');
        const stok = parseInt(card.dataset.stok);

        document.getElementById('modal_produk_id').value = card.dataset.id;
        document.getElementById('modal_produk_name').textContent = card.dataset.name;
        document.getElementById('modal_produk_price').textContent = rupiah(card.dataset.price);
        document.getElementById('modal_produk_stok').textContent = stok;

        const jumlah = document.getElementById('modal_jumlah');
        jumlah.max = stok;
        jumlah.value = 1;

        new bootstrap.Modal(document.getElementById('orderModal')).show();
        if (document.getElementById('modal_alamat').value.trim().length >= 10) hitungOngkirModal();
    }

    async function hitungOngkirModal() {
        const requestId = ++modalQuoteRequest;
        const address = document.getElementById('modal_alamat').value.trim();
        const options = document.getElementById('modalDeliveryOptions');
        const button = document.getElementById('modalHitungOngkir');
        if (address.length < 10) {
            setDeliveryState('Alamat belum lengkap', 'Masukkan jalan, nomor rumah, kelurahan, kecamatan, dan kota untuk menghitung rute.');
            return;
        }
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Menghitung rute';
        setDeliveryState('Sedang menghitung rute', 'Mohon tunggu, jarak dan tarif sedang dihitung dari alamat toko.', 'loading');
        document.getElementById('modalSubmitOrder').disabled = true;
        try {
            const response = await fetch(DELIVERY_QUOTE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': DELIVERY_CSRF },
                body: JSON.stringify({ alamat_pengiriman: address })
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.errors ? Object.values(data.errors).flat()[0] : 'Rute alamat belum dapat dihitung.');
            if (requestId !== modalQuoteRequest || document.getElementById('modal_alamat').value.trim() !== address) return;
            modalDeliveryQuote = data;
            modalQuotedAddress = address;
            modalSelectedService = 'hemat';
            document.getElementById('modalDeliveryDistance').textContent = `Jarak rute: ${Number(data.distance_km).toLocaleString('id-ID')} km`;
            options.replaceChildren();
            options.className = 'delivery-options-list';
            Object.values(data.services).forEach(option => {
                const label = document.createElement('label');
                label.className = 'delivery-option';
                const radio = document.createElement('input');
                radio.type = 'radio';
                radio.name = 'jenis_pengiriman';
                radio.value = option.key;
                radio.required = true;
                radio.checked = option.key === modalSelectedService;
                radio.addEventListener('change', () => {
                    modalSelectedService = radio.value;
                });
                const copy = document.createElement('span');
                copy.className = 'delivery-option-copy';
                const heading = document.createElement('span');
                heading.className = 'delivery-option-heading';
                const title = document.createElement('strong');
                title.textContent = option.label;
                const price = document.createElement('span');
                price.className = 'delivery-option-price';
                price.textContent = rupiah(option.fee);
                const description = document.createElement('span');
                description.className = 'delivery-option-description';
                description.textContent = `${option.description} Tarif ${rupiah(option.rate_per_km)}/km`;
                heading.append(title, price);
                copy.append(heading, description);
                label.append(radio, copy);
                options.append(label);
            });
            document.getElementById('modalSubmitOrder').disabled = false;
        } catch (error) {
            if (requestId !== modalQuoteRequest) return;
            modalDeliveryQuote = null;
            modalQuotedAddress = '';
            const message = error.message || 'Periksa kembali alamat tujuan lalu coba lagi.';
            if (message.toLowerCase().includes('alamat toko')) {
                setDeliveryState('Alamat toko belum diatur', 'Admin perlu mengisi alamat asal di Pengaturan → Kontak & lokasi sebelum ongkir dapat dihitung.', 'error');
            } else if (message.toLowerCase().includes('google maps api key')) {
                setDeliveryState('Layanan peta belum aktif', 'Admin perlu mengatur GOOGLE_MAPS_SERVER_KEY dan mengaktifkan Google Routes API.', 'error');
            } else if (message.toLowerCase().includes('kuota google maps')) {
                setDeliveryState('Kuota layanan peta habis', message, 'error');
            } else if (message.toLowerCase().includes('google maps routes api')) {
                setDeliveryState('Layanan Google Maps belum siap', message, 'error');
            } else {
                setDeliveryState('Rute belum dapat dihitung', message, 'error');
            }
            document.getElementById('modalDeliveryDistance').textContent = '';
        } finally {
            if (requestId === modalQuoteRequest) {
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-calculator"></i> Hitung ongkir';
            }
        }
    }

    const orderForm = document.querySelector('#orderModal form');
    const modalAddress = document.getElementById('modal_alamat');
    const modalCalculateButton = document.getElementById('modalHitungOngkir');
    if (orderForm && modalAddress && modalCalculateButton) {
        modalCalculateButton.addEventListener('click', hitungOngkirModal);
        modalAddress.addEventListener('blur', hitungOngkirModal);
        modalAddress.addEventListener('input', () => {
            modalQuoteRequest++;
            modalDeliveryQuote = null;
            modalQuotedAddress = '';
            document.getElementById('modalSubmitOrder').disabled = true;
            setDeliveryState('Alamat berubah', 'Hitung ulang ongkir untuk mendapatkan jarak dan biaya yang sesuai alamat baru.');
            document.getElementById('modalDeliveryDistance').textContent = '';
            modalCalculateButton.disabled = false;
            modalCalculateButton.innerHTML = '<i class="bi bi-calculator"></i> Hitung ongkir';
        });
        orderForm.addEventListener('submit', event => {
            if (!modalDeliveryQuote || modalQuotedAddress !== modalAddress.value.trim()) {
                event.preventDefault();
                window.AppAlert.fire({ icon: 'warning', title: 'Hitung ongkir dahulu', text: 'Isi alamat lengkap dan hitung ongkir sebelum mengirim pesanan.', confirmButtonColor: '#754b3a' });
                return;
            }
            const selected = document.querySelector('#orderModal input[name="jenis_pengiriman"]:checked');
            if (!selected) {
                event.preventDefault();
                window.AppAlert.fire({ icon: 'warning', title: 'Pilih pengiriman', text: 'Pilih salah satu jenis pengiriman.', confirmButtonColor: '#754b3a' });
            }
        });
    }

    // ============================================
    // TAMBAH KE KERANJANG (dibatasi stok)
    // ============================================
    function handleAddToCart(button) {
        const card = button.closest('.product-card');
        const stok = parseInt(card.dataset.stok);
        const cart = getCart();
        const ada = cart.find(item => String(item.id) === card.dataset.id);
        const sudah = ada ? ada.quantity : 0;

        if (sudah + 1 > stok) {
            window.AppAlert.fire({
                icon: 'warning',

                title: 'Stok terbatas',
                text: `Stok ${card.dataset.name} tinggal ${stok}, dan ${sudah} sudah ada di keranjang.`,
                confirmButtonColor: '#754b3a'
            });
            return;
        }

        if (ada) {
            ada.quantity += 1;
        } else {
            cart.push({
                id: card.dataset.id,
                name: card.dataset.name,
                price: parseInt(card.dataset.price),
                image: card.dataset.image,
                quantity: 1
            });
        }

        saveCart(cart);

        window.AppAlert.fire({
            icon: 'success', title: 'Berhasil!',
            text: `${card.dataset.name} ditambahkan ke keranjang`,
            timer: 1500, showConfirmButton: false, toast: true, position: 'top-end'
        });
    }

    // ============================================
    // LIHAT SEMUA, FILTER, SEARCH, NAV
    // ============================================
    const productCards = [...document.querySelectorAll('.product-card')];
    const viewAllMenuButton = document.getElementById('btnViewAllMenu');
    const menuEmptyState = document.getElementById('menuEmptyState');
    let allMenuShown = false;
    let activeCategory = 'all';
    let searchKeyword = '';

    function toggleAllMenu() {
        allMenuShown = !allMenuShown;
        renderProductCards();
    }

    function renderProductCards() {
        const matchedCards = productCards.filter(card => {
            const cocokKategori = activeCategory === 'all' || card.dataset.category === activeCategory;
            const cocokPencarian = card.dataset.name.toLowerCase().includes(searchKeyword);
            return cocokKategori && cocokPencarian;
        });
        const tampilkanSemua = allMenuShown || activeCategory !== 'all' || searchKeyword !== '';

        productCards.forEach(card => {
            const index = matchedCards.indexOf(card);
            card.style.display = index !== -1 && (tampilkanSemua || index < 8) ? '' : 'none';
        });

        viewAllMenuButton.style.display = activeCategory === 'all' && searchKeyword === '' && productCards.length > 8 ? '' : 'none';
        viewAllMenuButton.innerHTML = allMenuShown
            ? '<i class="bi bi-arrow-up"></i> Tampilkan Lebih Sedikit'
            : '<i class="bi bi-grid"></i> Lihat Semua Menu';
        menuEmptyState.style.display = matchedCards.length === 0 ? '' : 'none';
    }

    function filterByCategory(category) {
        activeCategory = category;
        searchKeyword = '';
        allMenuShown = category !== 'all';
        if (searchInput) searchInput.value = '';
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === category) btn.classList.add('active');
        });
        renderProductCards();
        document.getElementById('menu').scrollIntoView({ behavior: 'smooth' });
    }

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() { filterByCategory(this.dataset.filter); });
    });

    const searchInput = document.getElementById('productSearch');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchKeyword = this.value.trim().toLowerCase();
            activeCategory = 'all';
            allMenuShown = false;
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.filter === 'all');
            });
            renderProductCards();
        });
    }

    renderProductCards();

    const navItems = document.querySelectorAll('.nav-item-custom');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
        });
    });

    const sections = document.querySelectorAll('section[id], footer[id]');
    window.addEventListener('scroll', () => {
        let current = '';
        const scrollPos = window.scrollY + 150;
        sections.forEach(section => {
            if (scrollPos >= section.offsetTop && scrollPos < section.offsetTop + section.offsetHeight) {
                current = section.getAttribute('id');
            }
        });
        if (window.scrollY < 100) current = 'beranda';
        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.dataset.target === current) item.classList.add('active');
        });
    });


</script>
@endpush