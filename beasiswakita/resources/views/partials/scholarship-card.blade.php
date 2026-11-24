{{-- Kartu beasiswa. Variabel: $scholarship (App\Models\Scholarship) --}}
<article class="card bk-card h-100">
    <div class="card-body d-flex flex-column p-4">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
            <span class="badge bk-level bk-level-{{ strtolower($scholarship->level) }}">
                <i class="bi bi-{{ \App\Models\Scholarship::LEVEL_ICONS[$scholarship->level] ?? 'bookmark' }} me-1"></i>{{ $scholarship->level }}
            </span>
            <span class="badge rounded-pill {{ $scholarship->badge_class }}">
                <i class="bi bi-clock me-1"></i>{{ $scholarship->countdown_label }}
            </span>
        </div>

        <h3 class="h5 fw-bold mb-1">
            <a href="{{ route('scholarships.show', $scholarship) }}" class="bk-title stretched-link text-decoration-none">
                {{ $scholarship->name }}
            </a>
        </h3>
        <p class="text-muted small mb-3">
            <i class="bi bi-building me-1"></i>{{ $scholarship->organizer }}
        </p>

        <p class="small flex-grow-1 mb-3">{{ \Illuminate\Support\Str::limit($scholarship->summary, 140) }}</p>

        <div class="border-top pt-3 small text-muted d-flex justify-content-between gap-2">
            <span><i class="bi bi-calendar-check me-1"></i>Buka {{ $scholarship->open_date->translatedFormat('d M Y') }}</span>
            <span><i class="bi bi-calendar-x me-1"></i>Tutup {{ $scholarship->deadline->translatedFormat('d M Y') }}</span>
        </div>
    </div>
</article>
