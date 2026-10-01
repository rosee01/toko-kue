<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Dashboard') | {{ \App\Models\Pengaturan::ambil('nama_toko', config('app.name')) }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" crossorigin="anonymous">
    @include('partials.theme')
    @include('partials.sweetalert-style')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">
    @php
        $jumlahPesananPerStatus = \App\Models\Pesanan::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $jumlahStokMenipis = \App\Models\Produk::stokMenipis()->count();
        $jumlahNotifikasi = ($jumlahPesananPerStatus[\App\Models\Pesanan::STATUS_PENDING] ?? 0) + $jumlahStokMenipis;
        $jumlahPembayaranMenunggu = \App\Models\Pesanan::where('status_pembayaran', \App\Models\Pesanan::PEMBAYARAN_BELUM_DIBAYAR)->count();
        $notifikasiPesanan = \App\Models\Pesanan::where('status', \App\Models\Pesanan::STATUS_PENDING)
            ->latest()
            ->limit(5)
            ->get();
        $notifikasiStok = \App\Models\Produk::stokMenipis()
            ->orderBy('stok')
            ->limit(5)
            ->get();
        $statusMenuPesanan = [
            ['Pending', 'Pesanan Baru', 'bi-inbox'],
            ['Diproses', 'Diproses', 'bi-hourglass-split'],
            ['Siap Diantar', 'Siap Diantar', 'bi-box-seam'],
            ['Dalam Pengantaran', 'Dalam Pengantaran', 'bi-truck'],
            ['Selesai', 'Selesai', 'bi-check2-circle'],
            ['Dibatalkan', 'Dibatalkan', 'bi-x-circle'],
        ];
    @endphp

    <nav class="app-header navbar navbar-expand">
        <div class="container-fluid">
            <ul class="navbar-nav me-3">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Buka/tutup menu"><i class="bi bi-list fs-4"></i></a>
                </li>
            </ul>

            {{-- SEARCH BAR GLOBAL (SUDAH DIPERBAIKI) --}}
            <form action="{{ route('search.index') }}" method="GET" class="cari d-none d-md-block d-flex align-items-center">
                <i class="bi bi-search me-2"></i>
                <input
                    type="text"
                    name="cari"
                    placeholder="Cari menu, pesanan, atau pelanggan..."
                    value="{{ request('cari') }}"
                    class="form-control form-control-sm border-0 bg-transparent"
                    style="min-width: 250px;"
                >
            </form>

            <ul class="navbar-nav ms-auto align-items-center gap-3">
                <li class="nav-item dropdown notification-menu">
                    <button type="button" class="lonceng" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Buka notifikasi">
                        <i class="bi bi-bell" aria-hidden="true"></i>
                        @if($jumlahNotifikasi > 0)<span class="notification-badge">{{ $jumlahNotifikasi > 99 ? '99+' : $jumlahNotifikasi }}</span>@endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                        <div class="notification-dropdown-head">
                            <div>
                                <strong>Perlu ditindaklanjuti</strong>
                                <span>Pesanan baru dan stok menipis</span>
                            </div>
                            <span class="notification-total">{{ $jumlahNotifikasi }}</span>
                        </div>
                        <div class="notification-dropdown-body">
                            @forelse($notifikasiPesanan as $pesanan)
                                <a class="notification-item" href="{{ route('pesanan.edit', $pesanan) }}">
                                    <span class="notification-item-icon order"><i class="bi bi-bag-plus"></i></span>
                                    <span class="notification-item-copy">
                                        <strong>Pesanan baru dari {{ $pesanan->nama_pelanggan }}</strong>
                                        <small>#{{ $pesanan->kode_pesanan ?: str_pad($pesanan->id, 6, '0', STR_PAD_LEFT) }} &middot; {{ $pesanan->created_at?->diffForHumans() }}</small>
                                    </span>
                                    <i class="bi bi-chevron-right notification-chevron" aria-hidden="true"></i>
                                </a>
                            @empty
                            @endforelse
                            @forelse($notifikasiStok as $produk)
                                <a class="notification-item" href="{{ route('produk.index') }}">
                                    <span class="notification-item-icon stock"><i class="bi bi-exclamation-triangle"></i></span>
                                    <span class="notification-item-copy">
                                        <strong>Stok {{ $produk->name_produk }} menipis</strong>
                                        <small>Tersisa {{ $produk->stok }} pcs &middot; Perlu restok</small>
                                    </span>
                                    <i class="bi bi-chevron-right notification-chevron" aria-hidden="true"></i>
                                </a>
                            @empty
                            @endforelse
                            @if($jumlahNotifikasi === 0)
                                <div class="notification-empty">
                                    <i class="bi bi-check2-circle"></i>
                                    <strong>Semua aman</strong>
                                    <span>Belum ada pesanan baru atau stok menipis.</span>
                                </div>
                            @endif
                        </div>
                        <div class="notification-dropdown-foot">
                            <a href="{{ route('pesanan.index', ['status' => \App\Models\Pesanan::STATUS_PENDING]) }}">Pesanan baru <i class="bi bi-arrow-right"></i></a>
                            <a href="{{ route('produk.index') }}">Kelola stok <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <span class="avatar"><i class="bi bi-person-fill"></i></span>
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2 small text-muted">{{ auth()->user()->email }}</li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="px-3 pb-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-box-arrow-right"></i> Keluar</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <aside class="app-sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-3 text-decoration-none">
                <span class="brand-logo"><i class="bi bi-cake2"></i></span>
                <span>
                    <span class="brand-nama d-block">{{ \App\Models\Pengaturan::ambil('nama_toko', config('app.name')) }}</span>
                    <span class="brand-tag">{{ \App\Models\Pengaturan::ambil('slogan', 'Manisnya Setiap Momen') }}</span>
                </span>
            </a>
        </div>
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" role="navigation" aria-label="Menu utama" id="sidebar-navigation">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-house-door-fill"></i><p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link sidebar-parent" data-lte-toggle="treeview" aria-expanded="true">
                            <i class="nav-icon bi bi-grid-fill"></i><p>Produk<i class="nav-arrow bi bi-chevron-down"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('produk.index') }}" class="nav-link {{ request()->routeIs('produk.index', 'produk.show', 'produk.edit') ? 'active' : '' }}"><i class="nav-icon bi bi-box-seam"></i><p>Daftar Produk</p></a></li>
                            <li class="nav-item"><a href="{{ route('produk.create') }}" class="nav-link {{ request()->routeIs('produk.create') ? 'active' : '' }}"><i class="nav-icon bi bi-plus-lg"></i><p>Tambah Produk</p></a></li>
                            <li class="nav-item"><a href="{{ route('kategori.create') }}" class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}"><i class="nav-icon bi bi-tags"></i><p>Kategori</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link sidebar-parent" data-lte-toggle="treeview" aria-expanded="true">
                            <i class="nav-icon bi bi-boxes"></i><p>Persediaan<i class="nav-arrow bi bi-chevron-down"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('stok.index') }}" class="nav-link {{ request()->routeIs('stok.*') ? 'active' : '' }}"><i class="nav-icon bi bi-box2"></i><p>Stok Produk</p></a></li>
                            <li class="nav-item"><a href="{{ route('stok.riwayat') }}" class="nav-link {{ request()->routeIs('stok.riwayat') ? 'active' : '' }}"><i class="nav-icon bi bi-clock-history"></i><p>Riwayat Stok</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item menu-open {{ request()->routeIs('pesanan.*') ? 'active-group' : '' }}">
                        <a href="#" class="nav-link sidebar-parent" data-lte-toggle="treeview" aria-expanded="true">
                            <i class="nav-icon bi bi-bag-check-fill"></i><p>Pesanan<i class="nav-arrow bi bi-chevron-down"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @foreach($statusMenuPesanan as [$status, $label, $icon])
                                <li class="nav-item">
                                    <a href="{{ route('pesanan.index', ['status' => $status]) }}" class="nav-link {{ request()->routeIs('pesanan.index') && request('status') === $status ? 'active' : '' }}">
                                        <i class="nav-icon bi {{ $icon }}"></i><p>{{ $label }}</p>
                                        @if(($jumlahPesananPerStatus[$status] ?? 0) > 0)<span class="badge-hitung">{{ $jumlahPesananPerStatus[$status] }}</span>@endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('pembayaran.index') }}" class="nav-link {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}"><i class="nav-icon bi bi-credit-card"></i><p>Pembayaran</p>
                            @if(($jumlahPembayaranMenunggu ?? 0) > 0)<span class="badge-hitung">{{ $jumlahPembayaranMenunggu }}</span>@endif
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('pelanggan.index') }}" class="nav-link {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}"><i class="nav-icon bi bi-person"></i><p>Pelanggan</p></a>
                    </li>

                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link sidebar-parent" data-lte-toggle="treeview" aria-expanded="true"><i class="nav-icon bi bi-truck"></i><p>Pengiriman<i class="nav-arrow bi bi-chevron-down"></i></p></a>
                        <ul class="nav nav-treeview"><li class="nav-item"><a href="{{ route('driver.index') }}" class="nav-link {{ request()->routeIs('driver.*') ? 'active' : '' }}"><i class="nav-icon bi bi-person-badge"></i><p>Driver</p></a></li></ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('laporan.penjualan') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}"><i class="nav-icon bi bi-bar-chart-line"></i><p>Laporan</p></a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('pengaturan.index') }}" class="nav-link {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}"><i class="nav-icon bi bi-gear"></i><p>Pengaturan</p></a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="sidebar-quote">Kue enak,<br>untuk hari<br>yang lebih manis</div>
    </aside>

    <main class="app-main">
        @unless(View::hasSection('tanpa-judul'))
        <div class="app-content-header">
            <div class="container-fluid">
                <h3 class="mb-0">@yield('page-title', 'Dashboard')</h3>
            </div>
        </div>
        @endunless
        <div class="app-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </main>

    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">Dibangun dengan Laravel &amp; AdminLTE</div>
        <strong>&copy; {{ date('Y') }} {{ \App\Models\Pengaturan::ambil('nama_toko', config('app.name')) }}.</strong>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('assets/dist/js/adminlte.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        confirmButtonColor: '#7b5343',
        cancelButtonColor: '#6c757d',
    });

    // Scrollbar sidebar (dinonaktifkan di layar kecil agar tidak mengganggu sentuhan)
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.querySelector('.sidebar-wrapper');
        const os = window.OverlayScrollbarsGlobal?.OverlayScrollbars;
        if (sidebar && os && window.innerWidth > 992) {
            os(sidebar, { scrollbars: { theme: 'os-theme-dark', autoHide: 'leave', clickScroll: true } });
        }
    });

    // Tabel dengan pencarian, urutan, dan pagination
    $('.datatable').DataTable({
        responsive: true,
        pageLength: 10,
        autoWidth: false,
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            zeroRecords: 'Data tidak ditemukan.',
            emptyTable: 'Belum ada data yang tersedia.',
            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
        },
    });

    // Konfirmasi sebelum menghapus
    document.querySelectorAll('.btn-delete').forEach((button) => {
        button.addEventListener('click', () => {
            window.AppAlert.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c8574f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) button.closest('form').submit();
            });
        });
    });

    @if(session('success'))
        window.AppAlert.fire({ icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 1800 });
    @endif
    @if(session('error'))
        window.AppAlert.fire({ icon: 'error', title: 'Gagal', text: @json(session('error')) });
    @endif
</script>
@yield('scripts')
</body>
</html>