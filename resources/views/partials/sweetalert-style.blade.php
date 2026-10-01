<style>
    .app-alert-popup {
        width:min(390px,calc(100vw - 32px)) !important;
        padding:1.15rem !important;
        border:1px solid #eaded7 !important;
        border-radius:16px !important;
        box-shadow:0 16px 42px rgba(57,40,34,.18) !important;
        font-family:inherit !important;
    }

    .app-alert-title {
        margin-top:.2rem !important;
        color:#4d3026 !important;
        font-size:1.02rem !important;
        font-weight:600 !important;
        line-height:1.35 !important;
    }

    .app-alert-text {
        margin:.4rem 0 0 !important;
        color:#87766e !important;
        font-size:.78rem !important;
        line-height:1.55 !important;
    }

    .app-alert-popup .swal2-icon {
        width:2.65rem !important;
        height:2.65rem !important;
        margin:.15rem auto .65rem !important;
        border-width:2px !important;
    }

    .app-alert-popup .swal2-icon .swal2-icon-content {
        font-size:1.7rem !important;
    }

    .app-alert-actions {
        gap:.5rem !important;
        margin-top:1rem !important;
    }

    .app-alert-confirm,
    .app-alert-cancel {
        min-height:34px !important;
        margin:0 !important;
        padding:.48rem .85rem !important;
        border-radius:8px !important;
        font-family:inherit !important;
        font-size:.72rem !important;
        font-weight:500 !important;
        box-shadow:none !important;
        transition:filter .15s ease,transform .15s ease !important;
    }

    .app-alert-confirm:hover,
    .app-alert-cancel:hover {
        filter:brightness(.94);
        transform:translateY(-1px);
    }

    .app-alert-cancel {
        border:1px solid #e5d9d2 !important;
        background:#f7f2ee !important;
        color:#70594c !important;
    }

    .app-alert-popup.swal2-toast {
        width:min(360px,calc(100vw - 24px)) !important;
        padding:.7rem .85rem !important;
        border-radius:11px !important;
    }

    .app-alert-popup.swal2-toast .swal2-title {
        margin:0 !important;
        font-size:.76rem !important;
        font-weight:500 !important;
    }

    .app-alert-popup.swal2-toast .swal2-icon {
        width:1.5rem !important;
        height:1.5rem !important;
        margin:0 .45rem 0 0 !important;
    }

    .app-alert-popup.swal2-toast .swal2-icon .swal2-icon-content {
        font-size:1rem !important;
    }

    @media (max-width:480px) {
        .app-alert-popup {
            width:calc(100vw - 28px) !important;
            padding:1rem !important;
        }

        .app-alert-title {
            font-size:.96rem !important;
        }
    }
</style>
