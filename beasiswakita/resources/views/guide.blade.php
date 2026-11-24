@extends('layouts.app')

@section('title', 'Panduan Dokumen Pendaftaran')
@section('meta_description', 'Panduan langkah demi langkah menyiapkan CV, motivation letter, surat rekomendasi, transkrip/rapor, SKTM, dan dokumen beasiswa lain, lengkap dengan checklist interaktif.')

@section('content')
    <section class="bk-page-header py-5">
        <div class="container">
            <h1 class="fw-bold mb-1">Panduan Dokumen Pendaftaran</h1>
            <p class="mb-0">Siapkan berkas beasiswamu langkah demi langkah dan centang yang sudah siap.</p>
        </div>
    </section>

    <div class="container mt-4">
        <div class="row g-4">
            {{-- ============ CHECKLIST RINGKAS ============ --}}
            <aside class="col-lg-4">
                <div class="bk-sticky">
                    <div class="bk-panel shadow-sm p-4">
                        <h2 class="h6 fw-bold mb-3"><i class="bi bi-check2-square text-primary me-2"></i>Checklist Dokumenku</h2>

                        <div class="d-flex justify-content-between small mb-1">
                            <span id="progressText" class="fw-semibold">0 dari {{ $guides->count() }} dokumen siap</span>
                            <span id="progressPercent" class="text-muted">0%</span>
                        </div>
                        <div class="progress mb-3" role="progressbar" aria-label="Progres dokumen"
                             aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="progressBar" style="height:10px">
                            <div class="progress-bar" id="progressFill" style="width:0%"></div>
                        </div>

                        <div class="d-grid gap-1">
                            @foreach ($guides as $guide)
                                <div class="form-check bk-step">
                                    <input class="form-check-input" type="checkbox" id="side-doc-{{ $guide->slug }}"
                                           data-check="doc:{{ $guide->slug }}">
                                    <label class="form-check-label small" for="side-doc-{{ $guide->slug }}">
                                        {{ $guide->title }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <hr>
                        <button type="button" id="resetChecklist" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset checklist
                        </button>
                        <p class="small text-muted mt-3 mb-0">
                            <i class="bi bi-hdd me-1"></i>Progres disimpan di browser ini (LocalStorage) dan tidak dikirim ke server.
                        </p>
                    </div>
                </div>
            </aside>

            {{-- ============ PANDUAN PER DOKUMEN ============ --}}
            <div class="col-lg-8">
                @forelse ($guides as $guide)
                    <article id="doc-{{ $guide->slug }}" class="bk-panel p-4 mb-4" style="scroll-margin-top:90px">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                            <div class="d-flex gap-3">
                                <div class="bk-icon-box"><i class="bi bi-{{ $guide->icon }}"></i></div>
                                <div>
                                    <h2 class="h5 fw-bold mb-1">{{ $guide->title }}</h2>
                                    <p class="text-muted small mb-0">{{ $guide->summary }}</p>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="doc-{{ $guide->slug }}-ready"
                                       data-check="doc:{{ $guide->slug }}">
                                <label class="form-check-label small fw-semibold" for="doc-{{ $guide->slug }}-ready">Dokumen siap</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="h6 fw-bold mb-0">Langkah persiapan</h3>
                            <span class="badge text-bg-light border" data-steps-progress="{{ $guide->slug }}"
                                  data-total="{{ count($guide->steps) }}">0/{{ count($guide->steps) }} langkah</span>
                        </div>

                        <div class="mb-3">
                            @foreach ($guide->steps as $index => $step)
                                <div class="form-check bk-step">
                                    <input class="form-check-input" type="checkbox"
                                           id="step-{{ $guide->slug }}-{{ $index }}"
                                           data-check="step:{{ $guide->slug }}:{{ $index }}"
                                           data-guide="{{ $guide->slug }}">
                                    <label class="form-check-label" for="step-{{ $guide->slug }}-{{ $index }}">
                                        <span class="fw-semibold text-primary me-1">{{ $loop->iteration }}.</span>{{ $step }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        @if (! empty($guide->tips))
                            <div class="rounded-3 p-3" style="background:#EFF6FF">
                                <div class="fw-bold small mb-2" style="color:#1E40AF">
                                    <i class="bi bi-lightbulb-fill me-1"></i>Tips
                                </div>
                                <ul class="small mb-0 ps-3">
                                    @foreach ($guide->tips as $tip)
                                        <li class="mb-1">{{ $tip }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="bk-panel p-5 text-center text-muted">Panduan dokumen belum tersedia.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Checklist interaktif, disimpan di LocalStorage.
        // Kunci: "doc:{slug}" (dokumen siap) dan "step:{slug}:{index}" (langkah selesai).
        (function () {
            const STORAGE_KEY = 'beasiswakita.checklist.v1';

            function load() {
                try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}; } catch (e) { return {}; }
            }
            function save(state) {
                try { localStorage.setItem(STORAGE_KEY, JSON.stringify(state)); } catch (e) { /* penyimpanan tidak tersedia */ }
            }

            let state = load();
            const boxes = Array.from(document.querySelectorAll('[data-check]'));
            const docKeys = Array.from(new Set(
                boxes.map(function (b) { return b.dataset.check; })
                     .filter(function (k) { return k.indexOf('doc:') === 0; })
            ));

            function render() {
                boxes.forEach(function (box) {
                    box.checked = !!state[box.dataset.check];
                    const row = box.closest('.bk-step');
                    if (row) row.classList.toggle('is-done', box.checked);
                });

                // Progres per dokumen (langkah)
                document.querySelectorAll('[data-steps-progress]').forEach(function (badge) {
                    const slug = badge.dataset.stepsProgress;
                    const total = parseInt(badge.dataset.total, 10) || 0;
                    let done = 0;
                    document.querySelectorAll('[data-guide="' + slug + '"]').forEach(function (b) {
                        if (state[b.dataset.check]) done++;
                    });
                    badge.textContent = done + '/' + total + ' langkah';
                });

                // Progres keseluruhan (dokumen siap)
                const readyCount = docKeys.filter(function (k) { return state[k]; }).length;
                const percent = docKeys.length ? Math.round((readyCount / docKeys.length) * 100) : 0;
                document.getElementById('progressText').textContent = readyCount + ' dari ' + docKeys.length + ' dokumen siap';
                document.getElementById('progressPercent').textContent = percent + '%';
                document.getElementById('progressFill').style.width = percent + '%';
                document.getElementById('progressBar').setAttribute('aria-valuenow', percent);
            }

            boxes.forEach(function (box) {
                box.addEventListener('change', function () {
                    if (box.checked) { state[box.dataset.check] = true; } else { delete state[box.dataset.check]; }
                    save(state);
                    render();
                });
            });

            document.getElementById('resetChecklist').addEventListener('click', function () {
                if (window.confirm('Hapus semua centang checklist di browser ini?')) {
                    state = {};
                    save(state);
                    render();
                }
            });

            // Sinkron bila checklist diubah dari tab lain.
            window.addEventListener('storage', function (e) {
                if (e.key === STORAGE_KEY) { state = load(); render(); }
            });

            render();
        })();
    </script>
@endpush
