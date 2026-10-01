@php
    $kelas = match ($status) {
        'Selesai' => 'text-bg-success',
        'Diproses' => 'text-bg-primary',
        default => 'text-bg-secondary',
    };
@endphp
<span class="badge rounded-pill {{ $kelas }}">{{ $status }}</span>
