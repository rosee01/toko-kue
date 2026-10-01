<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500&family=Playfair+Display:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    :root {
        --cokelat:#7b5343;
        --cokelat-tua:#5f3f32;
        --krem:#faf6f3;
        --sidebar:#f3eae4;
        --garis:#efe4dd;
        --teks:#3f302b;
        --redup:#8a7a73;
        --lte-sidebar-width:200px;
        --bs-body-font-family:'Poppins',sans-serif;
    }

    body {
        font-family:'Poppins',sans-serif;
        background:var(--krem);
        color:var(--teks);
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    .judul-serif {
        font-family:'Playfair Display',Georgia,serif;
        font-weight:600;
    }


    /* =========================================
       SIDEBAR
       TIDAK DIUBAH
       ========================================= */

    .app-sidebar {
        background:var(--sidebar);
        border-right:1px solid var(--garis);
        box-shadow:none !important;
        width:200px !important;
    }

    .app-sidebar .sidebar-brand {
        border:0 !important;
        padding:11px 14px 8px !important;
        border-bottom:1px solid var(--garis) !important;
    }

    .brand-logo {
        width:42px;
        height:42px;
        border-radius:50%;
        background:#fff;
        color:var(--cokelat);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.35rem;
        border:1px solid var(--garis);
    }

    .brand-nama {
        font:700 1.3rem 'Playfair Display',serif;
        color:var(--teks);
        line-height:1.1;
    }

    .brand-tag {
        font-size:.65rem;
        color:var(--redup);
    }

    .app-sidebar .sidebar-menu {
        padding:0 9px 90px;
    }

    .app-sidebar .nav-link {
        color:var(--teks);
        border-radius:8px;
        min-height:28px;
        padding:.38rem .62rem;
        margin-bottom:2px;
        display:flex;
        align-items:center;
        gap:7px;
        font-size:.7rem;
    }

    .app-sidebar .nav-link:hover {
        background:rgba(123,83,67,.08);
        color:var(--cokelat-tua);
    }

    .app-sidebar .nav-link.active {
        background:#f7dddd;
        color:#69483d;
    }

    .app-sidebar .nav-link p {
        margin:0;
        flex:1;
        font-size:.7rem;
    }

    .app-sidebar .nav-link .nav-icon {
        font-size:.82rem;
        width:1.05rem;
        text-align:center;
    }

    .app-sidebar .sidebar-parent {
        text-transform:uppercase;
        font-size:.64rem;
        font-weight:500;
    }

    .app-sidebar .sidebar-parent p {
        font-size:.64rem;
    }

    .app-sidebar .sidebar-parent .nav-arrow {
        width:auto;
        margin-left:auto;
        font-size:.58rem;
    }

    .app-sidebar .nav-treeview {
        padding-left:12px;
        margin-bottom:3px;
    }

    .app-sidebar .nav-treeview .nav-link {
        min-height:25px;
        padding:.3rem .5rem;
        font-size:.65rem;
    }

    .app-sidebar .nav-treeview .nav-link p {
        font-size:.65rem;
    }

    .app-sidebar .nav-link-disabled {
        color:#9b8c84;
        cursor:default;
    }

    .app-sidebar .nav-link-disabled:hover {
        background:transparent;
        color:#9b8c84;
    }

    .badge-hitung {
        background:#e8867f;
        color:#fff;
        border-radius:50%;
        min-width:16px;
        height:16px;
        padding:0 3px;
        font-size:.56rem;
        display:flex;
        align-items:center;
        justify-content:center;
        line-height:1;
    }

    .sidebar-quote {
        position:absolute;
        left:20px;
        bottom:20px;
        font:500 .95rem/1.3 'Caveat',cursive;
        color:#8a6a5c;
        transform:rotate(-6deg);
    }


    /* =========================================
       HEADER ATAS
       TIDAK DIUBAH
       ========================================= */

    .app-header {
        background:var(--krem) !important;
        border:0 !important;
        box-shadow:none;
        padding:9px 10px;
    }

    .app-header .nav-link {
        padding:.4rem .6rem;
    }

    .app-header .bi-list {
        font-size:1.25rem !important;
    }

    .cari {
        position:relative;
        width:min(400px,100%);
    }

    .cari input {
        width:100%;
        border:0;
        background:#f0e8e3;
        border-radius:10px;
        padding:9px 14px 9px 38px;
        font-size:.78rem;
        outline:0;
    }

    .cari i {
        position:absolute;
        left:13px;
        top:50%;
        transform:translateY(-50%);
        color:var(--redup);
    }

    .lonceng {
        position:relative;
        display:grid;
        place-items:center;
        width:36px;
        height:36px;
        padding:0;
        border:1px solid transparent;
        border-radius:50%;
        background:transparent;
        cursor:pointer;
        line-height:1;
        text-decoration:none;
        transition:background .15s ease,border-color .15s ease;
    }

    .lonceng:hover,
    .lonceng[aria-expanded="true"] {
        border-color:#eaded7;
        background:#fff;
    }

    .lonceng i {
        position:relative;
        color:var(--teks);
        font-size:1.05rem;
    }

    .notification-badge {
        position:absolute;
        top:-1px;
        right:-3px;
        display:grid;
        place-items:center;
        min-width:16px;
        height:16px;
        padding:0 4px;
        border:2px solid var(--krem);
        border-radius:12px;
        background:#d76870;
        color:#fff;
        font-size:.52rem;
        font-weight:600;
        line-height:1;
    }

    .notification-dropdown {
        width:min(350px,calc(100vw - 24px));
        margin-top:9px !important;
        padding:0;
        overflow:hidden;
        border:1px solid var(--garis);
        border-radius:11px;
        box-shadow:0 12px 34px rgba(65,43,32,.14);
    }

    .notification-dropdown-head {
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:10px;
        padding:13px 14px;
        border-bottom:1px solid #f1e9e4;
    }

    .notification-dropdown-head strong,
    .notification-dropdown-head span {
        display:block;
    }

    .notification-dropdown-head strong {
        color:#50392f;
        font-size:.76rem;
        font-weight:600;
    }

    .notification-dropdown-head div > span {
        margin-top:2px;
        color:#958880;
        font-size:.59rem;
    }

    .notification-total {
        display:grid;
        place-items:center;
        min-width:23px;
        height:23px;
        padding:0 6px;
        border-radius:50%;
        background:#fbe9ea;
        color:#b45f69;
        font-size:.62rem;
        font-weight:600;
    }

    .notification-dropdown-body {
        max-height:min(360px,60vh);
        overflow-y:auto;
        padding:5px 8px;
    }

    .notification-item {
        display:flex;
        align-items:center;
        gap:9px;
        padding:9px 7px;
        border-radius:7px;
        color:inherit;
        text-decoration:none;
        transition:background .15s ease;
    }

    .notification-item + .notification-item {
        border-top:1px solid #f4eeea;
        border-radius:0;
    }

    .notification-item:hover {
        background:#fbf7f4;
        color:inherit;
    }

    .notification-item-icon {
        display:grid;
        place-items:center;
        width:30px;
        height:30px;
        flex:0 0 30px;
        border-radius:8px;
        font-size:.75rem;
    }

    .notification-item-icon.order {
        background:#fdebed;
        color:#bd6670;
    }

    .notification-item-icon.stock {
        background:#fff2dc;
        color:#b17a2c;
    }

    .notification-item-copy {
        flex:1;
        min-width:0;
    }

    .notification-item-copy strong,
    .notification-item-copy small {
        display:block;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .notification-item-copy strong {
        color:#55463e;
        font-size:.63rem;
        font-weight:500;
    }

    .notification-item-copy small {
        margin-top:3px;
        color:#958880;
        font-size:.55rem;
    }

    .notification-chevron {
        color:#b6a69c;
        font-size:.62rem;
    }

    .notification-empty {
        display:grid;
        justify-items:center;
        gap:4px;
        padding:22px 14px;
        color:#958880;
        text-align:center;
    }

    .notification-empty i {
        color:#70a678;
        font-size:1.2rem;
    }

    .notification-empty strong {
        color:#5d5149;
        font-size:.66rem;
        font-weight:600;
    }

    .notification-empty span {
        font-size:.57rem;
    }

    .notification-dropdown-foot {
        display:flex;
        justify-content:space-between;
        gap:8px;
        padding:9px 13px;
        border-top:1px solid #f1e9e4;
        background:#fdfbf9;
    }

    .notification-dropdown-foot a {
        color:#8a6250;
        font-size:.59rem;
        font-weight:500;
        text-decoration:none;
    }

    .notification-dropdown-foot a:hover {
        color:#5f3f32;
    }

    .avatar {
        width:34px;
        height:34px;
        border-radius:50%;
        background:#7b5343;
        color:#fff;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        font-size:.85rem;
    }

    .user-menu .nav-link {
        font-size:.82rem;
    }

    .user-menu .dropdown-menu {
        font-size:.78rem;
    }


    /* =========================================
       ISI HALAMAN
       DIPERKECIL
       SIDEBAR & HEADER TIDAK TERKENA
       ========================================= */

    .app-main {
        font-size:14px;
    }


    /* -----------------------------------------
       JUDUL HALAMAN
       ----------------------------------------- */

    .app-content-header {
        background:transparent;
        padding-top:18px !important;
        padding-bottom:10px !important;
    }

    .app-content-header h1,
    .app-content-header h2,
    .app-content-header h3 {
        font-size:28px !important;
        line-height:1.2;
        margin-bottom:0 !important;
    }


    /* -----------------------------------------
       AREA CONTENT
       ----------------------------------------- */

    .app-content {
        font-size:14px;
    }

    .app-content .container-fluid {
        font-size:14px;
    }


    /* -----------------------------------------
       CARD
       ----------------------------------------- */

    .app-main .card {
        background:#fff;
        border:1px solid var(--garis);
        border-radius:14px;
        box-shadow:0 4px 18px rgba(123,83,67,.05);
    }

    .app-main .card-header {
        background:transparent;
        border-bottom:1px solid var(--garis);
        padding:15px 18px;
    }

    .app-main .card-body {
        padding:16px 18px;
    }


    /* -----------------------------------------
       JUDUL CARD
       ----------------------------------------- */

    .app-main .card-title {
        font-family:'Playfair Display',serif;
        font-size:20px !important;
        margin-bottom:0;
    }


    /* -----------------------------------------
       TEKS UMUM DALAM CONTENT
       ----------------------------------------- */

    .app-main p {
        font-size:14px;
    }

    .app-main label {
        font-size:13px;
    }

    .app-main small {
        font-size:12px;
    }


    /* -----------------------------------------
       BUTTON
       ----------------------------------------- */

    .app-main .btn {
        border-radius:10px;
        font-weight:500;
        font-size:13px;
        padding:7px 12px;
    }

    .app-main .btn-sm {
        font-size:12px;
        padding:6px 9px;
    }

    .btn-primary {
        background:var(--cokelat);
        border-color:var(--cokelat);
    }

    .btn-primary:hover,
    .btn-primary:focus {
        background:var(--cokelat-tua);
        border-color:var(--cokelat-tua);
    }

    .btn-outline-primary {
        color:var(--cokelat);
        border-color:var(--cokelat);
    }

    .btn-outline-primary:hover {
        background:var(--cokelat);
        border-color:var(--cokelat);
        color:#fff;
    }

    .btn-success {
        background:#6f9a82;
        border-color:#6f9a82;
        color:#fff;
    }

    .btn-warning {
        background:#e9b872;
        border-color:#e9b872;
        color:#fff;
    }

    .btn-danger {
        background:#c8574f;
        border-color:#c8574f;
    }


    /* -----------------------------------------
       INPUT & FORM
       ----------------------------------------- */

    .app-main .form-control,
    .app-main .form-select {
        font-size:13px;
        padding:7px 10px;
        min-height:36px;
    }

    .app-main .form-label {
        font-size:13px;
        margin-bottom:5px;
    }


    /* -----------------------------------------
       TABEL
       ----------------------------------------- */

    .app-main .table {
        font-size:13px;
    }

    .app-main .table thead th {
        background:#f5ede8;
        color:var(--teks);
        border-bottom:1px solid var(--garis);
        font-weight:500;
        font-size:13px;
        padding:9px 10px;
    }

    .app-main .table tbody td {
        font-size:13px;
        padding:8px 10px;
        vertical-align:middle;
    }

    .table-striped > tbody > tr:nth-of-type(odd) > * {
        background:transparent;
    }

    .app-main .table tbody tr.selected > * {
        --bs-table-bg-state:#fff5ec !important;
        --bs-table-color-state:var(--teks) !important;
        box-shadow:inset 0 0 0 9999px #fff5ec !important;
    }


    /* -----------------------------------------
       DATATABLES
       ----------------------------------------- */

    .app-main .dataTables_wrapper {
        font-size:13px;
    }

    .app-main .dataTables_wrapper label {
        font-size:13px;
    }

    .app-main .dataTables_wrapper select,
    .app-main .dataTables_wrapper input {
        font-size:13px;
    }

    .app-main .dataTables_length select {
        min-width:60px;
        height:34px;
    }

    .app-main .dataTables_filter input {
        height:34px;
        padding:5px 9px;
    }

    .app-main .dataTables_info {
        font-size:13px;
    }


    /* -----------------------------------------
       PAGINATION
       ----------------------------------------- */

    .app-main .pagination {
        margin-bottom:0;
    }

    .app-main .pagination .page-link {
        font-size:13px;
        padding:7px 10px;
    }

    .page-item.active .page-link {
        background:var(--cokelat);
        border-color:var(--cokelat);
    }

    .page-link,
    .app-main a {
        color:var(--cokelat);
    }


    /* -----------------------------------------
       FOTO PRODUK
       ----------------------------------------- */

    .foto-thumb {
        width:45px;
        height:45px;
        object-fit:cover;
        border-radius:8px;
    }

    .foto-kosong {
        width:45px;
        height:45px;
        border-radius:8px;
        background:#f3eae4;
        color:#b9a59b;
        display:inline-flex;
        align-items:center;
        justify-content:center;
    }


    /* =========================================
       DASHBOARD
       MEMBUAT ISINYA LEBIH PROPORSIONAL
       ========================================= */

    .app-main .dashboard-card,
    .app-main .stat-card {
        font-size:13px;
    }

    .app-main .dashboard-card h3,
    .app-main .stat-card h3 {
        font-size:22px;
    }

    .app-main .dashboard-card h4,
    .app-main .stat-card h4 {
        font-size:18px;
    }

    .app-main .dashboard-card .angka,
    .app-main .stat-card .angka {
        font-size:24px;
    }


    /* =========================================
       FOOTER
       ========================================= */

    .app-footer {
        background:transparent;
        border:0;
        color:var(--redup);
        font-size:13px;
    }
    /* =========================================
   TOMBOL AKSI - TULISAN SELALU TERLIHAT
   ========================================= */

/* Tombol utama */
.btn-primary {
    background: var(--cokelat) !important;
    border-color: var(--cokelat) !important;
    color: #fff !important;
}

/* Tulisan dan icon di dalam tombol */
.btn-primary i,
.btn-primary span {
    color: #fff !important;
}

/* Saat mouse diarahkan */
.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active {
    background: var(--cokelat-tua) !important;
    border-color: var(--cokelat-tua) !important;
    color: #fff !important;
}

.btn-primary:hover i,
.btn-primary:hover span,
.btn-primary:focus i,
.btn-primary:focus span,
.btn-primary:active i,
.btn-primary:active span {
    color: #fff !important;
}
/* =========================================================
   LAPORAN PENJUALAN
   Warna dibuat menyatu dengan tema Toko Kue
   ========================================================= */


/* =========================================================
   1. TOMBOL LAPORAN
   ========================================================= */

/* Tombol RESET */
.app-main .btn-secondary {
    background: #f5eee9 !important;
    border: 1px solid #ddcec5 !important;
    color: #6b554b !important;
    box-shadow: none !important;
}

.app-main .btn-secondary:hover,
.app-main .btn-secondary:focus,
.app-main .btn-secondary:active {
    background: #e9ddd6 !important;
    border-color: #cdbbb0 !important;
    color: #59443b !important;
}


/* Tombol PREVIEW */
.app-main .btn-outline-primary {
    background: #fff !important;
    border: 1px solid #9a7767 !important;
    color: #7b5343 !important;
    box-shadow: none !important;
}

.app-main .btn-outline-primary:hover,
.app-main .btn-outline-primary:focus,
.app-main .btn-outline-primary:active {
    background: #7b5343 !important;
    border-color: #7b5343 !important;
    color: #fff !important;
}


/* Tombol UNDUH PDF */
.app-main .btn-success {
    background: #7b9b86 !important;
    border: 1px solid #7b9b86 !important;
    color: #fff !important;
    box-shadow: none !important;
}

.app-main .btn-success:hover,
.app-main .btn-success:focus,
.app-main .btn-success:active {
    background: #668572 !important;
    border-color: #668572 !important;
    color: #fff !important;
}


/* Tombol TERAPKAN */
.app-main .btn-primary {
    background: #7b5343 !important;
    border: 1px solid #7b5343 !important;
    color: #fff !important;
    box-shadow: none !important;
}

.app-main .btn-primary:hover,
.app-main .btn-primary:focus,
.app-main .btn-primary:active {
    background: #654337 !important;
    border-color: #654337 !important;
    color: #fff !important;
}


/* Semua tombol laporan */
.app-main .btn {
    border-radius: 10px !important;
    font-weight: 500;
    transition: all .2s ease;
}


/* =========================================================
   2. KARTU TOTAL PESANAN
   Mengganti biru terang
   ========================================================= */

.app-main .bg-primary {
    background: #8b6b5c !important;
    color: #fff !important;
    border: none !important;
}


/* =========================================================
   3. KARTU PESANAN SELESAI
   Mengganti kuning terang
   ========================================================= */

.app-main .bg-warning {
    background: #d6b878 !important;
    color: #fff !important;
    border: none !important;
}


/* =========================================================
   4. KARTU PENDAPATAN
   Mengganti hijau terang
   ========================================================= */

.app-main .bg-success {
    background: #78947f !important;
    color: #fff !important;
    border: none !important;
}


/* =========================================================
   5. TAMPILAN KARTU STATISTIK
   ========================================================= */

.app-main .bg-primary,
.app-main .bg-warning,
.app-main .bg-success {
    border-radius: 14px !important;
    box-shadow: 0 5px 16px rgba(123, 83, 67, .08) !important;
}


/* Angka besar di kartu */
.app-main .bg-primary h2,
.app-main .bg-primary h3,
.app-main .bg-primary h4,
.app-main .bg-primary .display-6,
.app-main .bg-warning h2,
.app-main .bg-warning h3,
.app-main .bg-warning h4,
.app-main .bg-warning .display-6,
.app-main .bg-success h2,
.app-main .bg-success h3,
.app-main .bg-success h4,
.app-main .bg-success .display-6 {
    color: #fff !important;
}


/* =========================================================
   6. CARD FILTER TANGGAL
   ========================================================= */

.app-main .card {
    border-color: #eaded7;
}


/* Input tanggal */
.app-main input[type="date"] {
    border-color: #dfd3cc !important;
    border-radius: 9px !important;
    color: #4b3932;
}

.app-main input[type="date"]:focus {
    border-color: #9a7767 !important;
    box-shadow: 0 0 0 3px rgba(123, 83, 67, .08) !important;
}


/* =========================================================
   7. BAGIAN RINCIAN PESANAN
   ========================================================= */

.app-main .card-header {
    border-bottom-color: #eaded7 !important;
}


/* Header tabel */
.app-main .table thead th {
    background: #f5eee9 !important;
    color: #5f493f !important;
    border-bottom-color: #dfd2ca !important;
}


/* Isi tabel */
.app-main .table tbody td {
    border-color: #eee5df !important;
}


/* Hover tabel */
.app-main .table tbody tr:hover {
    background: #fcf8f5 !important;
}


/* =========================================================
   8. BADGE STATUS
   ========================================================= */

/* Pending */
.app-main .badge.bg-secondary {
    background: #9b8d86 !important;
    color: #fff !important;
}


/* Diproses */
.app-main .badge.bg-primary {
    background: #8b6b5c !important;
    color: #fff !important;
}


/* Selesai */
.app-main .badge.bg-success {
    background: #78947f !important;
    color: #fff !important;
}


/* =========================================================
   9. JARAK & TAMPILAN TOMBOL
   ========================================================= */

.app-main .btn i {
    margin-right: 4px;
}


/* Supaya teks tombol SELALU terlihat */
.app-main a.btn,
.app-main button.btn {
    opacity: 1 !important;
    visibility: visible !important;
}
</style>