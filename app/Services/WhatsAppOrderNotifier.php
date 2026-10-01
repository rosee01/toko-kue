<?php

namespace App\Services;

use App\Jobs\SendWhatsAppNotification;
use App\Models\NotifikasiWhatsApp;
use App\Models\Pesanan;

class WhatsAppOrderNotifier
{
    public function notify(Pesanan $pesanan, string $event, string $status, string $detail): void
    {
        $pesanan->refresh();
        if (! $pesanan->izin_notifikasi_whatsapp) {
            return;
        }

        $phone = $this->normalizePhone($pesanan->no_telepon);
        $notification = NotifikasiWhatsApp::create([
            'pesanan_id' => $pesanan->id,
            'kode_pesanan' => $pesanan->kode_pesanan,
            'telepon' => $phone,
            'peristiwa' => $event,
            'status_pesan' => $status,
            'detail_pesan' => $detail,
            'status' => 'pending',
        ]);

        $token = config('services.whatsapp_cloud.access_token');
        $phoneNumberId = config('services.whatsapp_cloud.phone_number_id');
        $template = config('services.whatsapp_cloud.template');
        $language = config('services.whatsapp_cloud.language');
        if (! $token || ! $phoneNumberId || ! $template || ! $language) {
            $notification->update([
                'status' => 'not_configured',
                'pesan_error' => 'Lengkapi kredensial WhatsApp Cloud API dan template pesan yang sudah disetujui Meta.',
            ]);

            return;
        }

        SendWhatsAppNotification::dispatch($notification->id);
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }
        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }
}
