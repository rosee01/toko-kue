@php
    $paymentName = $paymentName ?? 'metode_pembayaran';
@endphp
<div class="checkout-payment-options" role="radiogroup" aria-label="Pilih metode pembayaran">
    <label class="checkout-payment-option">
        <input type="radio" name="{{ $paymentName }}" value="{{ \App\Models\Pesanan::METODE_TRANSFER_BANK }}" required @checked(old($paymentName, \App\Models\Pesanan::METODE_TRANSFER_BANK) === \App\Models\Pesanan::METODE_TRANSFER_BANK)>
        <span class="checkout-payment-icon"><i class="bi bi-bank"></i></span>
        <span><strong>Transfer bank</strong><small>Verifikasi oleh admin</small></span>
    </label>
    <label class="checkout-payment-option">
        <input type="radio" name="{{ $paymentName }}" value="{{ \App\Models\Pesanan::METODE_E_WALLET }}" required @checked(old($paymentName, \App\Models\Pesanan::METODE_TRANSFER_BANK) === \App\Models\Pesanan::METODE_E_WALLET)>
        <span class="checkout-payment-icon"><i class="bi bi-wallet2"></i></span>
        <span><strong>E-wallet</strong><small>Verifikasi oleh admin</small></span>
    </label>
    <label class="checkout-payment-option">
        <input type="radio" name="{{ $paymentName }}" value="{{ \App\Models\Pesanan::METODE_COD }}" required @checked(old($paymentName, \App\Models\Pesanan::METODE_TRANSFER_BANK) === \App\Models\Pesanan::METODE_COD)>
        <span class="checkout-payment-icon"><i class="bi bi-cash-coin"></i></span>
        <span><strong>COD</strong><small>Bayar saat pesanan diterima</small></span>
    </label>
</div>
<div class="checkout-payment-help">
    <i class="bi bi-info-circle"></i>
    <span>Untuk transfer bank dan e-wallet, tujuan pembayaran tampil pada detail pesanan setelah ongkir dan jadwal dikonfirmasi admin.</span>
</div>
<div class="checkout-payment-details" data-payment-details="{{ \App\Models\Pesanan::METODE_COD }}" hidden>
    Bayar kepada kurir saat pesanan diterima.
</div>
