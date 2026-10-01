<?php

namespace App\Services;

use App\Models\Pengaturan;
class TarifPengiriman
{
    public function options(string $destination): array
    {
        $distanceMeters = app(JarakPengiriman::class)->hitung($destination);
        return $this->optionsForDistance($distanceMeters);
    }

    public function quote(string $destination, string $serviceKey): array
    {
        $distanceMeters = app(JarakPengiriman::class)->hitung($destination);
        $quote = $this->optionsForDistance($distanceMeters);
        if (! isset($quote['services'][$serviceKey])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'jenis_pengiriman' => 'Pilih jenis pengiriman yang tersedia.',
            ]);
        }

        return [
            ...$quote['services'][$serviceKey],
            'distance_meters' => $quote['distance_meters'],
            'distance_km' => $quote['distance_km'],
        ];
    }

    private function optionsForDistance(int $distanceMeters): array
    {
        $kilometers = $distanceMeters / 1000;
        $services = collect(config('delivery.services'))
            ->map(function (array $service, string $key) use ($distanceMeters, $kilometers): array {
                $rate = (int) Pengaturan::ambil($service['rate_setting'], (string) $service['default_rate']);
                $freeUntilKm = $service['free_setting']
                    ? (float) Pengaturan::ambil($service['free_setting'], (string) $service['free_until_km'])
                    : 0;
                $free = $key === 'hemat' && $kilometers <= $freeUntilKm;

                return [
                    'key' => $key,
                    'label' => $service['label'],
                    'description' => $service['description'],
                    'rate_per_km' => $rate,
                    'free_until_km' => $freeUntilKm,
                    'fee' => $free ? 0 : (int) ceil($distanceMeters * $rate / 1000),
                ];
            })
            ->all();

        return [
            'distance_meters' => $distanceMeters,
            'distance_km' => round($kilometers, 2),
            'services' => $services,
        ];
    }
}
