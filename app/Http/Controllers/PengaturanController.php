<?php

namespace App\Http\Controllers;

use App\Models\NotifikasiWhatsApp;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function index(): View
    {
        $alamatToko = trim((string) Pengaturan::ambil('alamat', config('delivery.origin')));

        return view('pengaturan.index', [
            'notifikasiWhatsApp' => NotifikasiWhatsApp::latest()->limit(10)->get(),
            'whatsappCloudSiap' => (bool) config('services.whatsapp_cloud.access_token')
                && (bool) config('services.whatsapp_cloud.phone_number_id')
                && (bool) config('services.whatsapp_cloud.template'),
            'mapsSiap' => (bool) config('services.google_maps.server_key'),
            'alamatTokoSiap' => $alamatToko !== '' && ! str_contains(strtolower($alamatToko), 'simulasi'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_toko' => ['required', 'string', 'max:60'],
            'slogan' => ['nullable', 'string', 'max:80'],
            'whatsapp' => ['nullable', 'regex:/^[0-9]{8,15}$/'],
            'alamat' => ['nullable', 'string', 'max:200'],
            'banner' => ['nullable', 'image', 'max:2048'],
            'bank_nama' => ['nullable', 'string', 'max:50'],
            'bank_rekening' => ['nullable', 'string', 'max:30'],
            'bank_pemilik' => ['nullable', 'string', 'max:100'],
            'ewallet_provider' => ['nullable', 'string', 'max:40'],
            'ewallet_nomor' => ['nullable', 'string', 'max:30'],
            'ewallet_pemilik' => ['nullable', 'string', 'max:100'],
            'tarif_per_km_cepat' => ['sometimes', 'integer', 'min:0', 'max:5000000'],
            'tarif_per_km_hemat' => ['sometimes', 'integer', 'min:0', 'max:5000000'],
            'tarif_per_km_lambat' => ['sometimes', 'integer', 'min:0', 'max:5000000'],
            'gratis_sampai_km_hemat' => ['sometimes', 'numeric', 'min:0', 'max:100'],
        ], ['whatsapp.regex' => 'Nomor WhatsApp hanya angka, contoh: 6281234567890.']);

        foreach (['nama_toko', 'slogan', 'whatsapp', 'alamat', 'bank_nama', 'bank_rekening', 'bank_pemilik', 'ewallet_provider', 'ewallet_nomor', 'ewallet_pemilik', 'tarif_per_km_cepat', 'tarif_per_km_hemat', 'tarif_per_km_lambat', 'gratis_sampai_km_hemat'] as $kunci) {
            if (array_key_exists($kunci, $data)) {
                Pengaturan::simpan($kunci, (string) $data[$kunci]);
            }
        }

        if ($request->hasFile('banner')) {
            if ($lama = Pengaturan::ambil('banner')) {
                Storage::disk('public')->delete($lama);
            }
            Pengaturan::simpan('banner', $request->file('banner')->store('banner', 'public'));
        }

        return back()->with('success', 'Pengaturan toko disimpan.');
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'password_lama' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], ['password_lama.current_password' => 'Password lama tidak sesuai.']);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password berhasil diganti.');
    }
}
