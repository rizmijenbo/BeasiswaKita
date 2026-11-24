@extends('layouts.app')

@section('title', 'Kalender Pendaftaran')
@section('meta_description', 'Kalender jadwal pembukaan dan tenggat pendaftaran beasiswa dalam tampilan bulanan.')

@section('content')
    <section class="bk-page-header py-5">
        <div class="container">
            <h1 class="fw-bold mb-1">Kalender Pendaftaran</h1>
            <p class="mb-0">Pantau jadwal pembukaan dan tenggat seluruh beasiswa dalam satu tampilan.</p>
        </div>
    </section>

    <div class="container mt-4">
        {{-- ============ NAVIGASI BULAN ============ --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('calendar', ['bulan' => $previousMonth->format('Y-m')]) }}"
                   class="btn btn-outline-primary" aria-label="Bulan sebelumnya"><i class="bi bi-chevron-left"></i></a>
                <h2 class="h4 fw-bold mb-0 text-center" style="min-width:11rem">{{ $month->translatedFormat('F Y') }}</h2>
                <a href="{{ route('calendar', ['bulan' => $nextMonth->format('Y-m')]) }}"
                   class="btn btn-outline-primary" aria-label="Bulan berikutnya"><i class="bi bi-chevron-right"></i></a>
                <a href="{{ route('calendar') }}" class="btn btn-primary ms-2">Hari Ini</a>
            </div>

            <div class="d-flex gap-3 small">
                <span><span class="bk-dot buka"></span> Pendaftaran dibuka</span>
                <span><span class="bk-dot tutup"></span> Tenggat / ditutup</span>
            </div>
        </div>

        {{-- ============ GRID KALENDER ============ --}}
        <div class="bk-cal shadow-sm">
            @foreach ($dayNames as $name)
                <div class="bk-cal-head">{{ $name }}</div>
            @endforeach

            @foreach ($weeks as $week)
                @foreach ($week as $day)
                    @php
                        $dayEvents = $events[$day->toDateString()] ?? [];
                        $cellClass = trim(
                            ($day->isSameMonth($month) ? '' : 'is-outside ')
                            .($day->isSameDay($today) ? 'is-today' : '')
                        );
                    @endphp
                    <div class="bk-cal-cell {{ $cellClass }}">
                        <span class="bk-cal-day">{{ $day->day }}</span>

                        {{-- Desktop: label kejadian --}}
                        <div class="d-none d-md-block">
                            @foreach (array_slice($dayEvents, 0, 3) as $event)
                                <a href="{{ route('scholarships.show', $event['scholarship']) }}"
                                   class="bk-cal-event {{ $event['type'] }}"
                                   title="{{ $event['type'] === 'buka' ? 'Dibuka' : 'Tenggat' }}: {{ $event['scholarship']->name }}">
                                    {{ $event['type'] === 'buka' ? 'Buka' : 'Tutup' }}: {{ $event['scholarship']->name }}
                                </a>
                            @endforeach
                            @if (count($dayEvents) > 3)
                                <span class="small text-muted ps-1">+{{ count($dayEvents) - 3 }} lainnya</span>
                            @endif
                        </div>

                        {{-- Mobile: titik penanda (detail ada di agenda di bawah) --}}
                        <div class="d-md-none">
                            @foreach ($dayEvents as $event)
                                <span class="bk-dot {{ $event['type'] }}"></span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>

        {{-- ============ AGENDA BULANAN ============ --}}
        <section class="mt-5">
            <h2 class="h5 fw-bold mb-3">
                <i class="bi bi-list-ul text-primary me-2"></i>Jadwal {{ $month->translatedFormat('F Y') }}
            </h2>

            @if (count($agenda) === 0)
                <div class="bk-panel p-5 text-center text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                    Tidak ada jadwal pembukaan atau tenggat pada bulan ini.
                </div>
            @else
                <div class="bk-panel">
                    <ul class="list-group list-group-flush">
                        @foreach ($agenda as $item)
                            @php $isOpen = $item['type'] === 'buka'; @endphp
                            <li class="list-group-item p-3">
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <div class="text-center flex-shrink-0" style="width:56px">
                                        <div class="fs-4 fw-bold lh-1 {{ $isOpen ? 'text-primary' : 'text-danger' }}">{{ $item['date']->day }}</div>
                                        <div class="small text-muted">{{ $item['date']->translatedFormat('D') }}</div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="badge {{ $isOpen ? 'text-bg-primary' : 'text-bg-danger' }} mb-1">
                                            <i class="bi bi-{{ $isOpen ? 'door-open' : 'alarm' }} me-1"></i>{{ $isOpen ? 'Pendaftaran dibuka' : 'Tenggat pendaftaran' }}
                                        </span>
                                        <div>
                                            <a href="{{ route('scholarships.show', $item['scholarship']) }}"
                                               class="fw-semibold text-decoration-none">{{ $item['scholarship']->name }}</a>
                                        </div>
                                        <div class="small text-muted">
                                            {{ $item['scholarship']->organizer }} &middot; {{ $item['scholarship']->level }}
                                        </div>
                                    </div>
                                    <span class="badge rounded-pill {{ $item['scholarship']->badge_class }}">
                                        {{ $item['scholarship']->countdown_label }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    </div>
@endsection
