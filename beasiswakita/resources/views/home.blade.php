@extends('layouts.app')

@section('title', 'Temukan Peluang, Raih Masa Depan')
@section('meta_description', 'Cari beasiswa SMP, SMA, dan kuliah di satu tempat: persyaratan terstruktur, kalender pendaftaran, pengingat tenggat, dan panduan dokumen.')

@section('content')
    {{-- ============ HERO ============ --}}
    <section class="bk-hero pt-5 pb-5">
        <div class="container pt-lg-4 pb-5 mb-4">
            <div class="row justify-content-center text-center">
                <div class="col-lg-9 col-xl-8">
                    <span class="badge rounded-pill text-bg-primary bg-opacity-75 px-3 py-2 mb-3">
                        <i class="bi bi-stars me-1"></i>Pusat Informasi Kesempatan Pendidikan
                    </span>
                    <h1 class="display-4 fw-bold mb-3">Temukan Peluang,<br>Raih Masa Depan.</h1>
                    <p class="lead mb-4">
                        Satu website untuk mencari beasiswa, memahami persyaratan, memantau tenggat,
                        dan menyiapkan dokumen pendaftaran.
                    </p>

                    {{-- Search bar utama --}}
                    <form action="{{ route('scholarships.index') }}" method="GET" role="search" class="mb-4">
                        <div class="input-group input-group-lg shadow-lg rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-search text-muted"></i></span>
                            <input type="search" name="q" class="form-control border-0"
                                   placeholder="Cari nama beasiswa atau penyelenggara..."
                                   aria-label="Cari beasiswa" maxlength="100">
                            <button class="btn btn-primary px-4" type="submit">Cari</button>
                        </div>
                    </form>

                    {{-- Filter cepat jenjang --}}
                    <div class="d-flex flex-wrap justify-content-center gap-2" aria-label="Filter cepat jenjang">
                        <a href="{{ route('scholarships.index') }}" class="bk-chip">
                            <i class="bi bi-grid"></i>Semua
                            <span class="count">{{ $stats['total'] }}</span>
                        </a>
                        @foreach ($levels as $level)
                            <a href="{{ route('scholarships.index', ['jenjang' => $level]) }}" class="bk-chip">
                                <i class="bi bi-{{ \App\Models\Scholarship::LEVEL_ICONS[$level] }}"></i>{{ $level }}
                                <span class="count">{{ $levelCounts[$level] ?? 0 }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ STATISTIK ============ --}}
    <section class="bk-stats">
        <div class="container">
            <div class="row g-3">
                @php
                    $statItems = [
                        ['icon' => 'collection', 'value' => $stats['total'], 'label' => 'Beasiswa Terdaftar'],
                        ['icon' => 'door-open', 'value' => $stats['open'], 'label' => 'Pendaftaran Dibuka'],
                        ['icon' => 'building', 'value' => $stats['organizers'], 'label' => 'Penyelenggara'],
                        ['icon' => 'file-earmark-check', 'value' => $stats['guides'], 'label' => 'Panduan Dokumen'],
                    ];
                @endphp
                @foreach ($statItems as $item)
                    <div class="col-6 col-lg-3">
                        <div class="bk-panel shadow-sm p-3 p-lg-4 h-100 d-flex align-items-center gap-3">
                            <div class="bk-icon-box"><i class="bi bi-{{ $item['icon'] }}"></i></div>
                            <div>
                                <div class="bk-stat-number">{{ $item['value'] }}</div>
                                <div class="small text-muted">{{ $item['label'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ BEASISWA SEGERA TUTUP ============ --}}
    <section class="container mt-5 pt-3">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
            <div>
                <h2 class="h3 fw-bold mb-1">
                    <i class="bi bi-alarm-fill text-danger me-2"></i>{{ $isUrgent ? 'Beasiswa Segera Tutup' : 'Tenggat Terdekat' }}
                </h2>
                <p class="text-muted mb-0">
                    {{ $isUrgent ? 'Jangan sampai terlewat. Segera siapkan berkasmu!' : 'Beasiswa dengan batas pendaftaran paling dekat.' }}
                </p>
            </div>
            <a href="{{ route('scholarships.index', ['status' => 'segera']) }}" class="btn btn-outline-primary btn-sm">
                Lihat semua <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        @if ($closingSoon->isEmpty())
            <div class="bk-panel p-5 text-center text-muted">
                <i class="bi bi-calendar-check fs-1 d-block mb-2"></i>
                Belum ada beasiswa yang sedang dibuka saat ini.
            </div>
        @else
            <div class="row g-4">
                @foreach ($closingSoon as $scholarship)
                    <div class="col-md-6 col-lg-4">
                        @include('partials.scholarship-card', ['scholarship' => $scholarship])
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ============ FITUR ============ --}}
    <section class="container mt-5 pt-4">
        <div class="text-center mb-4">
            <h2 class="h3 fw-bold">Semua yang Kamu Butuhkan, di Satu Tempat</h2>
            <p class="text-muted">Dari mencari hingga menyiapkan pendaftaran.</p>
        </div>
        <div class="row g-4">
            @php
                $features = [
                    ['icon' => 'search', 'title' => 'Pencarian & Filter', 'text' => 'Cari berdasarkan nama atau penyelenggara, lalu saring menurut jenjang SMP, SMA, hingga kuliah.', 'url' => route('scholarships.index')],
                    ['icon' => 'list-check', 'title' => 'Persyaratan Terstruktur', 'text' => 'Syarat akademik, administrasi, dan umum disusun rapi sehingga mudah dipahami.', 'url' => route('scholarships.index')],
                    ['icon' => 'calendar3', 'title' => 'Kalender & Pengingat', 'text' => 'Lihat jadwal buka-tutup dalam satu kalender dan daftarkan email agar tidak terlewat.', 'url' => route('calendar')],
                    ['icon' => 'file-earmark-check', 'title' => 'Panduan Dokumen', 'text' => 'Langkah menyiapkan CV, motivation letter, rekomendasi, hingga SKTM lengkap dengan checklist.', 'url' => route('guide')],
                ];
            @endphp
            @foreach ($features as $feature)
                <div class="col-md-6 col-lg-3">
                    <a href="{{ $feature['url'] }}" class="text-decoration-none">
                        <div class="bk-card p-4 h-100">
                            <div class="bk-icon-box mb-3"><i class="bi bi-{{ $feature['icon'] }}"></i></div>
                            <h3 class="h6 fw-bold mb-2">{{ $feature['title'] }}</h3>
                            <p class="small text-muted mb-0">{{ $feature['text'] }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="container mt-5 pt-3">
        <div class="bk-hero rounded-4 p-4 p-lg-5">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <h2 class="h3 fw-bold mb-2">Sudah punya beasiswa incaran?</h2>
                    <p class="mb-0 lead">Mulai siapkan dokumen pendaftaranmu sekarang dan centang yang sudah siap. Progresmu tersimpan di browser.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('guide') }}" class="btn btn-light btn-lg fw-semibold">
                        <i class="bi bi-file-earmark-check me-1"></i>Buka Panduan Dokumen
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
