<?php

return [
    'origin' => env('TOKO_ALAMAT'),
    'services' => [
        'cepat' => [
            'label' => 'Cepat',
            'description' => 'Diprioritaskan untuk pengantaran lebih cepat.',
            'default_rate' => 3500,
            'rate_setting' => 'tarif_per_km_cepat',
            'free_until_km' => 0,
            'free_setting' => null,
        ],
        'hemat' => [
            'label' => 'Hemat',
            'description' => 'Tarif ringan, gratis ongkir untuk alamat terdekat.',
            'default_rate' => 1000,
            'rate_setting' => 'tarif_per_km_hemat',
            'free_until_km' => 3,
            'free_setting' => 'gratis_sampai_km_hemat',
        ],
        'lambat' => [
            'label' => 'Lambat',
            'description' => 'Tidak diprioritaskan dengan tarif paling rendah.',
            'default_rate' => 500,
            'rate_setting' => 'tarif_per_km_lambat',
            'free_until_km' => 0,
            'free_setting' => null,
        ],
    ],
    'distance_cache_seconds' => 21600,
];
