@extends('layouts.app')

@section('title', 'Daftar Beasiswa')
@section('meta_description', 'Katalog lengkap beasiswa SMP, SMA, dan kuliah. Cari berdasarkan kata kunci, jenjang, dan status pendaftaran.')

@section('content')
    <section class="bk-page-header py-5">
        <div class="container">
            <h1 class="fw-bold mb-1">Daftar Beasiswa</h1>
            <p class="mb-0">Temukan beasiswa yang sesuai dengan jenjang pendidikanmu.</p>
        </div>
    </section>

    <div class="container mt-4">
        {{-- ============ FILTER ============ --}}
        <form id="filterForm" action="{{ route('scholarships.index') }}" method="GET" role="search" class="bk-panel shadow-sm p-3 p-lg-4">
            <div class="row g-3 align-items-end">
                <div class="col-lg-6">
                    <label for="q" class="form-label fw-semibold small">Kata kunci</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" id="q" name="q" value="{{ $filters['q'] }}" class="form-control"
                               placeholder="Nama beasiswa atau penyelenggara..." maxlength="100" autocomplete="off">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="status" class="form-label fw-semibold small">Status pendaftaran</label>
                    <select id="status" name="status" class="form-select">
                        <option value="" @selected($filters['status'] === null)>Semua status</option>
                        <option value="buka" @selected($filters['status'] === 'buka')>Sedang Buka</option>
                        <option value="segera" @selected($filters['status'] === 'segera')>Segera Tutup</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Terapkan</button>
                    <a href="{{ route('scholarships.index') }}" class="btn btn-outline-secondary" title="Atur ulang filter">
                        <i class="bi bi-arrow-counterclockwise"></i><span class="visually-hidden">Atur ulang</span>
                    </a>
                </div>

                <div class="col-12">
                    <div class="small fw-semibold mb-2">Jenjang pendidikan</div>
                    <div class="d-flex flex-wrap gap-2" role="radiogroup" aria-label="Jenjang pendidikan">
                        <input type="radio" class="btn-check" name="jenjang" id="jenjang-semua" value="" @checked($filters['jenjang'] === null)>
                        <label class="btn bk-pill btn-sm px-3" for="jenjang-semua">Semua</label>

                        @foreach ($levels as $level)
                            <input type="radio" class="btn-check" name="jenjang" id="jenjang-{{ strtolower($level) }}"
                                   value="{{ $level }}" @checked($filters['jenjang'] === $level)>
                            <label class="btn bk-pill btn-sm px-3" for="jenjang-{{ strtolower($level) }}">
                                <i class="bi bi-{{ \App\Models\Scholarship::LEVEL_ICONS[$level] }} me-1"></i>{{ $level }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>

        {{-- ============ HASIL ============ --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 my-4">
            <p class="mb-0 text-muted" aria-live="polite">
                @if ($scholarships->total() > 0)
                    Menampilkan <strong>{{ $scholarships->firstItem() }}–{{ $scholarships->lastItem() }}</strong>
                    dari <strong>{{ $scholarships->total() }}</strong> beasiswa
                @else
                    Tidak ada hasil
                @endif
                @if ($filters['q'] !== '')
                    untuk "<strong>{{ $filters['q'] }}</strong>"
                @endif
            </p>
            <span class="small text-muted"><i class="bi bi-sort-down me-1"></i>Diurutkan dari tenggat terdekat</span>
        </div>

        @if ($scholarships->isEmpty())
            <div class="bk-panel text-center p-5">
                <i class="bi bi-emoji-neutral fs-1 text-muted d-block mb-2"></i>
                <h2 class="h5 fw-bold">Beasiswa tidak ditemukan</h2>
                <p class="text-muted">Coba kata kunci lain, ganti jenjang, atau hapus filter yang aktif.</p>
                <a href="{{ route('scholarships.index') }}" class="btn btn-primary">Tampilkan semua beasiswa</a>
            </div>
        @else
            <div class="row g-4">
                @foreach ($scholarships as $scholarship)
                    <div class="col-md-6 col-lg-4">
                        @include('partials.scholarship-card', ['scholarship' => $scholarship])
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-5">
                {{ $scholarships->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Pencarian "real-time": kirim form otomatis saat pengguna berhenti mengetik
        // atau mengganti filter. Tombol "Terapkan" tetap berfungsi tanpa JavaScript.
        (function () {
            const form = document.getElementById('filterForm');
            const search = document.getElementById('q');
            if (!form || !search) return;

            let timer;
            search.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () { form.submit(); }, 600);
            });

            form.querySelectorAll('input[name="jenjang"], select[name="status"]').forEach(function (el) {
                el.addEventListener('change', function () { form.submit(); });
            });

            // Setelah halaman dimuat ulang oleh pencarian, kembalikan fokus ke kolom pencarian.
            if (search.value !== '') {
                search.focus();
                search.setSelectionRange(search.value.length, search.value.length);
            }
        })();
    </script>
@endpush
