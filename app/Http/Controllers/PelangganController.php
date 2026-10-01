<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function index(Request $request): View
    {
        $pesanan = Pesanan::with('produk')->latest()->get();
        $akun = User::where('role', 'customer')->orderBy('name')->get();
        $pelanggan = collect();

        foreach ($akun as $user) {
            $items = $pesanan->where('user_id', $user->id)->values();
            $pelanggan->push($this->ringkasanPelanggan(
                'akun-' . $user->id,
                $user->name,
                $user->email,
                $user->created_at,
                $items,
                false,
                $user
            ));
        }

        $pesananTanpaAkun = $pesanan->whereNull('user_id')->groupBy('nama_pelanggan');
        foreach ($pesananTanpaAkun as $nama => $items) {
            $pelanggan->push($this->ringkasanPelanggan(
                'tamu-' . sha1((string) $nama),
                (string) $nama,
                null,
                $items->last()?->created_at,
                $items->values(),
                true,
                null
            ));
        }

        $pelanggan = $pelanggan->sortByDesc('total_belanja')->values();
        $pelangganTerpilih = $pelanggan->firstWhere('key', $request->query('pilih')) ?? $pelanggan->first();
        $modeEdit = $request->boolean('edit') && $pelangganTerpilih?->user !== null;
        $totalPelanggan = $pelanggan->count();
        $pelangganAktif = $pelanggan->filter(fn ($item) => $item->jumlah_pesanan > 0)->count();
        $pelangganBaru = $akun->filter(fn (User $user) => $user->created_at?->greaterThanOrEqualTo(now()->subDays(30)))->count();
        $totalBelanjaSelesai = $pesanan->where('status', Pesanan::STATUS_SELESAI)->sum('total');

        return view('pelanggan.index', compact(
            'pelanggan',
            'pelangganTerpilih',
            'totalPelanggan',
            'pelangganAktif',
            'pelangganBaru',
            'totalBelanjaSelesai',
            'modeEdit'
        ));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'customer', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update($data);

        return redirect()->route('pelanggan.index', ['pilih' => 'akun-' . $user->id])
            ->with('success', 'Profil pelanggan berhasil diperbarui.');
    }

    public function toggleStatus(User $user)
    {
        abort_unless($user->role === 'customer', 404);

        $user->update(['is_active' => ! $user->is_active]);

        return redirect()->route('pelanggan.index', ['pilih' => 'akun-' . $user->id])
            ->with('success', $user->is_active ? 'Akun pelanggan diaktifkan.' : 'Akun pelanggan dinonaktifkan.');
    }

    private function ringkasanPelanggan(
        string $key,
        string $nama,
        ?string $email,
        $terdaftarPada,
        $items,
        bool $tamu,
        ?User $user
    ): object {
        $checkouts = $items->groupBy(fn (Pesanan $item) => $item->kode_pesanan ?: 'pesanan-' . $item->id)
            ->map(function ($checkout) {
                $utama = $checkout->first();

                return (object) [
                    'key' => $utama->kode_pesanan ?: 'pesanan-' . $utama->id,
                    'kode' => $utama->kode_pesanan ?: 'PSN-' . str_pad((string) $utama->id, 6, '0', STR_PAD_LEFT),
                    'created_at' => $utama->created_at,
                    'status' => $utama->status,
                    'total' => $checkout->sum('total') + (int) $utama->ongkir,
                    'items' => $checkout,
                ];
            })
            ->values();
        $pesananTerakhir = $items->first();

        return (object) [
            'key' => $key,
            'nama' => $nama,
            'email' => $email,
            'tamu' => $tamu,
            'user' => $user,
            'is_active' => $user?->is_active ?? true,
            'telepon' => $pesananTerakhir?->no_telepon,
            'alamat' => $pesananTerakhir?->alamat_pengiriman,
            'terdaftar_pada' => $terdaftarPada,
            'terakhir' => $pesananTerakhir?->created_at,
            'jumlah_pesanan' => $checkouts->count(),
            'total_belanja' => $items->where('status', Pesanan::STATUS_SELESAI)->sum('total'),
            'checkouts' => $checkouts,
        ];
    }
}