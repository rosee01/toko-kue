<?php

namespace App\Jobs;

use App\Models\NotifikasiWhatsApp;
use App\Models\Pesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $notificationId)
    {
    }

    public function backoff(): array
    {
        return [10, 60, 180];
    }

    public function handle(): void
    {
        $notification = NotifikasiWhatsApp::find($this->notificationId);
        if (! $notification || $notification->status !== 'pending') {
            return;
        }

        $pesanan = $notification->pesanan_id ? Pesanan::find($notification->pesanan_id) : null;
        if (! $pesanan || ! $pesanan->izin_notifikasi_whatsapp) {
            $notification->update(['status' => 'skipped', 'pesan_error' => 'Persetujuan WhatsApp tidak tersedia.']);

            return;
        }

        $version = config('services.whatsapp_cloud.api_version');
        $template = config('services.whatsapp_cloud.template');
        $response = Http::withToken(config('services.whatsapp_cloud.access_token'))
            ->acceptJson()
            ->timeout(12)
            ->post("https://graph.facebook.com/{$version}/".config('services.whatsapp_cloud.phone_number_id').'/messages', [
                'messaging_product' => 'whatsapp',
                'to' => $notification->telepon,
                'type' => 'template',
                'template' => [
                    'name' => $template,
                    'language' => ['code' => config('services.whatsapp_cloud.language')],
                    'components' => [[
                        'type' => 'body',
                        'parameters' => array_map(
                            fn (string $text) => ['type' => 'text', 'text' => Str::limit($text, 900, '')],
                            [
                                $pesanan->nama_pelanggan,
                                $notification->kode_pesanan ?: 'PSN-'.$pesanan->id,
                                $notification->status_pesan,
                                'Rp '.number_format((int) $pesanan->ongkir, 0, ',', '.').' · '.$notification->detail_pesan,
                                $pesanan->jadwal_pengiriman?->format('d/m/Y H:i') ?? 'menunggu konfirmasi',
                            ]
                        ),
                    ]],
                ],
            ]);

        if ($response->status() === 429 || $response->serverError()) {
            throw new RuntimeException(Str::limit((string) ($response->json('error.message') ?? 'WhatsApp Cloud API sementara tidak tersedia.'), 500));
        }

        if (! $response->successful()) {
            $notification->update([
                'status' => 'failed',
                'pesan_error' => Str::limit((string) ($response->json('error.message') ?? 'WhatsApp Cloud API menolak pesan.'), 500),
            ]);

            return;
        }

        $notification->update([
            'status' => 'sent',
            'provider_message_id' => $response->json('messages.0.id'),
            'dikirim_pada' => now(),
            'pesan_error' => null,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        NotifikasiWhatsApp::whereKey($this->notificationId)->update([
            'status' => 'failed',
            'pesan_error' => Str::limit($exception?->getMessage() ?? 'Pengiriman WhatsApp gagal setelah dicoba ulang.', 500),
        ]);
    }
}
