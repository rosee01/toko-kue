<?php

namespace App\Services;

use App\Models\Pengaturan;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class JarakPengiriman
{
    public function hitung(string $tujuan): int
    {
        $asal = trim((string) Pengaturan::ambil('alamat', config('delivery.origin')));
        if ($asal === '' || str_contains(strtolower($asal), 'simulasi')) {
            throw ValidationException::withMessages([
                'alamat_pengiriman' => 'Admin harus mengatur alamat toko yang sebenarnya sebelum ongkir dapat dihitung.',
            ]);
        }

        $apiKey = config('services.google_maps.server_key');
        if (! is_string($apiKey) || $apiKey === '') {
            throw ValidationException::withMessages([
                'alamat_pengiriman' => 'Perhitungan jarak belum tersedia. Admin perlu mengonfigurasi Google Maps API key.',
            ]);
        }
        $apiKey = trim($apiKey);
        if ($apiKey === '') {
            throw ValidationException::withMessages([
                'alamat_pengiriman' => 'Perhitungan jarak belum tersedia. Admin perlu mengonfigurasi Google Maps API key.',
            ]);
        }

        $cacheKey = 'delivery-route:'.hash('sha256', mb_strtolower($asal)."\0".mb_strtolower(trim($tujuan)));

        return Cache::remember($cacheKey, (int) config('delivery.distance_cache_seconds'), function () use ($asal, $tujuan, $apiKey): int {
            try {
                $response = Http::withHeaders([
                    'X-Goog-Api-Key' => $apiKey,
                    'X-Goog-FieldMask' => 'routes.distanceMeters',
                ])->timeout(10)->post('https://routes.googleapis.com/directions/v2:computeRoutes', [
                    'origin' => ['address' => $asal],
                    'destination' => ['address' => trim($tujuan)],
                    'travelMode' => 'DRIVE',
                    'routingPreference' => 'TRAFFIC_UNAWARE',
                    'languageCode' => 'id',
                    'regionCode' => 'ID',
                ]);
            } catch (ConnectionException $exception) {
                Log::warning('Google Routes API connection failed while calculating delivery distance.', [
                    'exception' => $exception::class,
                ]);

                throw ValidationException::withMessages([
                    'alamat_pengiriman' => 'Layanan perhitungan jarak sedang tidak dapat dihubungi. Coba lagi sebentar.',
                ]);
            }

            if (! $response->successful()) {
                $providerCode = strtoupper((string) $response->json('error.status'));
                Log::warning('Google Routes API rejected a delivery distance request.', [
                    'status' => $response->status(),
                    'provider_code' => $providerCode,
                ]);

                $message = match ($providerCode) {
                    'PERMISSION_DENIED', 'API_KEY_INVALID', 'SERVICE_DISABLED' => 'Google Maps Routes API tidak dapat diakses. Admin perlu memeriksa API key, izin, billing, dan status Routes API di Google Cloud.',
                    'RESOURCE_EXHAUSTED' => 'Kuota Google Maps Routes API habis atau batas permintaan terlampaui. Coba lagi nanti atau periksa kuota.',
                    default => 'Rute alamat tidak dapat dihitung. Periksa alamat tujuan atau hubungi toko.',
                };

                throw ValidationException::withMessages([
                    'alamat_pengiriman' => $message,
                ]);
            }

            $distance = $response->json('routes.0.distanceMeters');
            if (! is_numeric($distance) || (int) $distance < 0) {
                throw ValidationException::withMessages([
                    'alamat_pengiriman' => 'Rute tidak ditemukan untuk alamat tersebut. Periksa alamat tujuan dengan lebih lengkap.',
                ]);
            }

            return (int) $distance;
        });
    }
}
