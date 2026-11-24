@extends('layouts.app')

@section('title', $scholarship->name)
@section('meta_description', \Illuminate\Support\Str::limit($scholarship->summary, 155))

@section('content')
    {{-- ============ HEADER ============ --}}
    <section class="bk-page-header py-5">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('scholarships.index') }}">Beasiswa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($scholarship->name, 40) }}</li>
                </ol>
            </nav>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bk-level bk-level-{{ strtolower($scholarship->level) }}">
                    <i class="bi bi-{{ \App\Models\Scholarship::LEVEL_ICONS[$scholarship->level] ?? 'bookmark' }} me-1"></i>{{ $scholarship->level }}
                </span>
                <span class="badge rounded-pill {{ $scholarship->badge_class }} px-3 py-2">
                    <i class="bi bi-clock me-1"></i>{{ $scholarship->countdown_label }}
                </span>
            </div>

            <h1 class="fw-bold mb-2">{{ $scholarship->name }}</h1>
            <p class="mb-0"><i class="bi bi-building me-2"></i>{{ $scholarship->organizer }}</p>
        </div>
    </section>

    <div class="container mt-4">
        <div class="row g-4">
            {{-- ============ SIDEBAR: countdown, tombol daftar, pengingat ============ --}}
            <aside class="col-lg-4 order-first order-lg-last">
                <div class="bk-sticky">
                    <div class="bk-panel shadow-sm p-4 text-center {{ $scholarship->is_urgent ? 'bk-countdown-danger' : '' }}">
                        @if ($scholarship->status === 'upcoming')
                            <div class="bk-countdown-number">{{ $scholarship->days_until_open }}</div>
                            <div class="text-muted mb-3">hari lagi pendaftaran dibuka</div>
                        @elseif ($scholarship->is_closed)
                            <i class="bi bi-lock-fill fs-1 text-secondary"></i>
                            <div class="text-muted mb-3">Pendaftaran sudah ditutup</div>
                        @else
                            <div class="bk-countdown-number">{{ $scholarship->days_left }}</div>
                            <div class="text-muted mb-3">
                                {{ $scholarship->days_left === 0 ? 'Hari terakhir pendaftaran!' : 'hari lagi sampai tenggat' }}
                            </div>
                        @endif

                        <span class="badge rounded-pill {{ $scholarship->badge_class }} fs-6 px-3 py-2 mb-3">
                            <i class="bi bi-clock me-1"></i>{{ $scholarship->countdown_label }}
                        </span>

                        <ul class="list-unstyled text-start small mb-4">
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted"><i class="bi bi-calendar-check me-2"></i>Dibuka</span>
                                <strong>{{ $scholarship->open_date->translatedFormat('d F Y') }}</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <span class="text-muted"><i class="bi bi-calendar-x me-2"></i>Tenggat</span>
                                <strong>{{ $scholarship->deadline->translatedFormat('d F Y') }}</strong>
                            </li>
                        </ul>

                        <a href="{{ $scholarship->official_url }}" target="_blank" rel="noopener noreferrer"
                           class="btn btn-primary btn-lg w-100">
                            Daftar di Situs Resmi <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                        <div class="small text-muted mt-2">Anda akan diarahkan ke situs penyelenggara.</div>
                    </div>

                    {{-- Form pengingat tenggat --}}
                    <div id="pengingat" class="bk-panel shadow-sm p-4 mt-3">
                        <h2 class="h6 fw-bold mb-1"><i class="bi bi-bell-fill text-primary me-2"></i>Ingatkan Saya</h2>

                        @if ($scholarship->is_closed)
                            <p class="small text-muted mb-0 mt-2">Pendaftaran sudah ditutup, pengingat tidak tersedia.</p>
                        @else
                            <p class="small text-muted">Masukkan email dan kami kirim pengingat sebelum tenggat.</p>

                            <form method="POST" action="{{ route('reminders.store', $scholarship) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="reminder-email" class="form-label small fw-semibold">Alamat email</label>
                                    <input type="email" id="reminder-email" name="email" value="{{ old('email') }}"
                                           class="form-control @error('email') is-invalid @enderror"
                                           placeholder="nama@email.com" maxlength="255" required autocomplete="email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="reminder-days" class="form-label small fw-semibold">Ingatkan saya</label>
                                    <select id="reminder-days" name="days_before"
                                            class="form-select @error('days_before') is-invalid @enderror">
                                        @foreach ($daysOptions as $days)
                                            <option value="{{ $days }}" @selected((int) old('days_before', 7) === $days)>
                                                {{ $days }} hari sebelum tenggat
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('days_before')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-bell me-1"></i>Daftarkan Pengingat
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </aside>

            {{-- ============ KONTEN UTAMA ============ --}}
            <div class="col-lg-8">
                <section class="bk-panel p-4 mb-4">
                    <h2 class="h5 fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Tentang Beasiswa</h2>
                    <p class="mb-0">{{ $scholarship->description }}</p>
                </section>

                <section class="bk-panel p-4 mb-4">
                    <h2 class="h5 fw-bold mb-3"><i class="bi bi-gift text-primary me-2"></i>Cakupan Bantuan</h2>
                    <ul class="bk-list">
                        @foreach ($scholarship->benefits as $benefit)
                            <li>{{ $benefit }}</li>
                        @endforeach
                    </ul>
                </section>

                <section class="mb-4">
                    <h2 class="h5 fw-bold mb-3"><i class="bi bi-list-check text-primary me-2"></i>Persyaratan</h2>
                    <div class="row g-3">
                        @foreach ($categories as $key => $meta)
                            @if ($requirementGroups->has($key))
                                <div class="col-12">
                                    <div class="bk-panel p-4">
                                        <h3 class="h6 fw-bold mb-3">
                                            <span class="bk-icon-box d-inline-grid me-2" style="width:36px;height:36px;font-size:1.1rem">
                                                <i class="bi bi-{{ $meta['icon'] }}"></i>
                                            </span>{{ $meta['label'] }}
                                        </h3>
                                        <ul class="bk-list mb-0">
                                            @foreach ($requirementGroups[$key] as $requirement)
                                                <li>{{ $requirement->description }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </section>

                <section class="bk-panel p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h2 class="h5 fw-bold mb-0"><i class="bi bi-file-earmark-check text-primary me-2"></i>Dokumen Wajib</h2>
                        @if ($scholarship->documentGuides->isNotEmpty())
                            <span id="docProgress" class="small text-muted"
                                  data-total="{{ $scholarship->documentGuides->count() }}"></span>
                        @endif
                    </div>

                    @forelse ($scholarship->documentGuides as $guide)
                        @if ($loop->first)
                            <div class="row g-2">
                        @endif
                        <div class="col-sm-6">
                            <a href="{{ route('guide') }}#doc-{{ $guide->slug }}" data-doc-chip="{{ $guide->slug }}"
                               class="d-flex align-items-center gap-3 p-3 border rounded-3 text-decoration-none h-100">
                                <i class="bi bi-{{ $guide->icon }} fs-4 text-primary"></i>
                                <span class="flex-grow-1 text-body fw-semibold small">{{ $guide->title }}</span>
                                <i class="bi bi-circle text-muted" data-doc-status aria-hidden="true"></i>
                            </a>
                        </div>
                        @if ($loop->last)
                            </div>
                        @endif
                    @empty
                        <p class="text-muted mb-0">Daftar dokumen belum tersedia. Lihat ketentuan lengkap di situs resmi.</p>
                    @endforelse

                    @if ($scholarship->documentGuides->isNotEmpty())
                        <a href="{{ route('guide') }}" class="btn btn-sm btn-outline-primary mt-3">
                            Buka panduan &amp; checklist dokumen <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @endif
                </section>

                <div class="alert alert-light border small" role="note">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    Ringkasan ini bukan pengganti ketentuan resmi. Jadwal dan persyaratan dapat berubah,
                    periksa selalu di <a href="{{ $scholarship->official_url }}" target="_blank" rel="noopener noreferrer">situs resmi penyelenggara</a>.
                </div>
            </div>
        </div>

        {{-- ============ BEASISWA TERKAIT ============ --}}
        @if ($related->isNotEmpty())
            <section class="mt-5">
                <h2 class="h4 fw-bold mb-3">Beasiswa {{ $scholarship->level }} Lainnya</h2>
                <div class="row g-4">
                    @foreach ($related as $item)
                        <div class="col-md-6 col-lg-4">
                            @include('partials.scholarship-card', ['scholarship' => $item])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Tandai dokumen yang sudah dicentang di halaman Panduan Dokumen (LocalStorage).
        (function () {
            let state = {};
            try { state = JSON.parse(localStorage.getItem('beasiswakita.checklist.v1')) || {}; } catch (e) {}

            let done = 0;
            document.querySelectorAll('[data-doc-chip]').forEach(function (chip) {
                if (state['doc:' + chip.dataset.docChip]) {
                    done++;
                    const icon = chip.querySelector('[data-doc-status]');
                    if (icon) icon.className = 'bi bi-check-circle-fill text-success';
                }
            });

            const progress = document.getElementById('docProgress');
            if (progress) {
                progress.textContent = done + ' dari ' + progress.dataset.total + ' dokumen sudah siap';
            }
        })();
    </script>
@endpush
