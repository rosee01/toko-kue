<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Pesanan;
use App\Services\PesananService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function __construct(private PesananService $service)
    {
    }

    public function index(Request $request): View
    {
        $drivers = Driver::orderBy('nama')->get();
        $drivers->each(function (Driver $driver): void {
            $driver->tugas_aktif = $driver->pesanan()
                ->where('status', Pesanan::STATUS_DALAM_PENGANTARAN)
                ->get(['id', 'kode_pesanan'])
                ->map(fn (Pesanan $item) => $item->kode_pesanan ?: 'pesanan-' . $item->id)
                ->unique()
                ->count();
            $driver->sedang_mengantar = $driver->tugas_aktif > 0;
            $driver->total_tugas = $driver->pesanan()
                ->get(['id', 'kode_pesanan'])
                ->map(fn (Pesanan $item) => $item->kode_pesanan ?: 'pesanan-' . $item->id)
                ->unique()
                ->count();
        });
        $totalDriver = $drivers->count();
        $driverNonaktif = $drivers->where('is_active', false)->count();
        $driverMengantar = $drivers->where('is_active', true)->filter(fn (Driver $driver) => $driver->tugas_aktif > 0)->count();
        $driverTersedia = $drivers->where('is_active', true)->count() - $driverMengantar;
        $pesananSiapDiantar = Pesanan::with('produk')
            ->where('status', Pesanan::STATUS_SIAP_DIANTAR)
            ->whereNull('kurir_id')
            ->latest()
            ->get()
            ->groupBy(fn (Pesanan $item) => $item->kode_pesanan ?: 'pesanan-' . $item->id)
            ->map(fn ($items) => $items->first());

        $formOpen = $request->boolean('tambah') || $request->filled('edit');
        $formDriver = $request->boolean('tambah')
            ? new Driver(['is_active' => true])
            : ($request->filled('edit') ? Driver::findOrFail($request->query('edit')) : null);
        $driverTerpilih = $request->filled('pilih')
            ? $drivers->firstWhere('id', (int) $request->query('pilih'))
            : $drivers->first();
        $tugasDriver = $driverTerpilih
            ? $driverTerpilih->pesanan()->with('produk')->latest()->get()
                ->groupBy(fn (Pesanan $item) => $item->kode_pesanan ?: 'pesanan-' . $item->id)
                ->map(function ($items) {
                    $utama = $items->first();
                    $utama->jumlah_item = $items->count();

                    return $utama;
                })
                ->take(6)
            : collect();

        return view('driver.index', compact(
            'drivers',
            'totalDriver',
            'driverTersedia',
            'driverMengantar',
            'driverNonaktif',
            'pesananSiapDiantar',
            'formOpen',
            'formDriver',
            'driverTerpilih',
            'tugasDriver'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['is_active'] = true;
        $driver = Driver::create($data);

        return redirect()->route('driver.index', ['pilih' => $driver->id])->with('success', 'Driver berhasil ditambahkan.');
    }

    public function update(Request $request, Driver $driver): RedirectResponse
    {
        $driver->update($this->validasi($request));

        return redirect()->route('driver.index', ['pilih' => $driver->id])->with('success', 'Data driver berhasil diperbarui.');
    }

    public function toggleStatus(Driver $driver): RedirectResponse
    {
        if ($driver->is_active && $driver->pesanan()->where('status', Pesanan::STATUS_DALAM_PENGANTARAN)->exists()) {
            return redirect()->route('driver.index', ['pilih' => $driver->id])
                ->with('error', 'Driver yang sedang mengantar tidak dapat dinonaktifkan.');
        }

        $driver->update(['is_active' => ! $driver->is_active]);

        return redirect()->route('driver.index', ['pilih' => $driver->id])
            ->with('success', $driver->is_active ? 'Driver diaktifkan.' : 'Driver dinonaktifkan.');
    }

    public function tugaskan(Request $request, Driver $driver): RedirectResponse
    {
        $data = $request->validate([
            'pesanan_ids' => ['required', 'array', 'min:1'],
            'pesanan_ids.*' => ['required', 'integer', 'distinct', 'exists:pesanan,id'],
        ]);
        $this->service->tugaskanPesananBersamaan($data['pesanan_ids'], $driver);

        return redirect()->route('driver.index', ['pilih' => $driver->id])
            ->with('success', 'Pesanan yang dipilih berhasil ditugaskan sebagai satu perjalanan.');
    }

    public function tugaskanPesanan(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $data = $request->validate(['driver_id' => ['required', 'exists:drivers,id']]);
        $driver = Driver::findOrFail($data['driver_id']);
        $this->service->tugaskanDriver($pesanan, $driver);

        return redirect()->route('pesanan.index', ['pilih' => $pesanan->kode_pesanan ?: 'pesanan-' . $pesanan->id])
            ->with('success', 'Pesanan berhasil ditugaskan ke ' . $driver->nama . '.');
    }

    public function selesaikanPesanan(Driver $driver, Pesanan $pesanan): RedirectResponse
    {
        $this->service->selesaikanPengantaran($pesanan, $driver);

        return redirect()->route('driver.index', ['pilih' => $driver->id])
            ->with('success', 'Pengantaran pesanan berhasil diselesaikan.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'no_telepon' => ['required', 'string', 'max:20'],
            'jenis_kendaraan' => ['nullable', 'string', 'max:60'],
            'plat_nomor' => ['nullable', 'string', 'max:16'],
        ]);
    }
}