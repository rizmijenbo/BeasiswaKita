<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'BeasiswaKita - Pusat Informasi Kesempatan Pendidikan. Temukan Peluang, Raih Masa Depan.')">
    <meta name="theme-color" content="#0A2540">

    <title>@yield('title', 'BeasiswaKita') | {{ config('app.name') }}</title>

    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='22' fill='%230A2540'/%3E%3Cpath d='M50 24 14 41l36 17 28-13v20h8V41L50 24Zm-22 40v10c0 6 10 12 22 12s22-6 22-12V64L50 75 28 64Z' fill='%23fff'/%3E%3C/svg%3E">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --bk-navy: #0A2540;
            --bk-navy-2: #0D3B66;
            --bk-blue: #2563EB;
            --bk-blue-dark: #1D4ED8;
            --bk-bg: #F8FAFC;
            --bk-border: #E2E8F0;
            --bk-text: #1E293B;
            --bk-muted: #64748B;

            --bs-primary: #2563EB;
            --bs-primary-rgb: 37, 99, 235;
            --bs-link-color: #2563EB;
            --bs-link-color-rgb: 37, 99, 235;
            --bs-link-hover-color: #1D4ED8;
            --bs-link-hover-color-rgb: 29, 78, 216;
            --bs-body-font-family: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            --bs-body-color: var(--bk-text);
            --bs-body-bg: var(--bk-bg);
            --bs-border-radius: .75rem;
        }

        body { background: var(--bk-bg); color: var(--bk-text); }
        h1, h2, h3, h4, h5 { color: var(--bk-navy); letter-spacing: -.01em; }
        .text-muted { color: var(--bk-muted) !important; }

        /* Tombol sesuai palet */
        .btn-primary {
            --bs-btn-bg: var(--bk-blue); --bs-btn-border-color: var(--bk-blue);
            --bs-btn-hover-bg: var(--bk-blue-dark); --bs-btn-hover-border-color: var(--bk-blue-dark);
            --bs-btn-active-bg: #1E40AF; --bs-btn-active-border-color: #1E40AF;
            --bs-btn-disabled-bg: var(--bk-blue); --bs-btn-disabled-border-color: var(--bk-blue);
            --bs-btn-focus-shadow-rgb: 37, 99, 235;
            font-weight: 600;
        }
        .btn-outline-primary {
            --bs-btn-color: var(--bk-blue); --bs-btn-border-color: var(--bk-blue);
            --bs-btn-hover-bg: var(--bk-blue); --bs-btn-hover-border-color: var(--bk-blue);
            --bs-btn-active-bg: var(--bk-blue-dark); --bs-btn-active-border-color: var(--bk-blue-dark);
            --bs-btn-focus-shadow-rgb: 37, 99, 235;
            font-weight: 600;
        }
        .form-control:focus, .form-select:focus, .form-check-input:focus {
            border-color: #93B4F5; box-shadow: 0 0 0 .25rem rgba(37, 99, 235, .18);
        }
        .form-check-input:checked { background-color: var(--bk-blue); border-color: var(--bk-blue); }
        .progress-bar { background-color: var(--bk-blue); }

        /* Navbar */
        .bk-navbar { background: var(--bk-navy); box-shadow: 0 2px 12px rgba(10, 37, 64, .25); }
        .bk-navbar .navbar-brand { font-weight: 800; font-size: 1.25rem; color: #fff; }
        .bk-navbar .navbar-brand .accent { color: #7FA8FF; }
        .bk-navbar .nav-link { color: rgba(255, 255, 255, .78); font-weight: 500; border-radius: .5rem; padding: .5rem .85rem; }
        .bk-navbar .nav-link:hover { color: #fff; background: rgba(255, 255, 255, .08); }
        .bk-navbar .nav-link.active { color: #fff; background: rgba(37, 99, 235, .55); }
        .bk-brand-icon {
            display: inline-grid; place-items: center; width: 34px; height: 34px;
            background: var(--bk-blue); border-radius: .6rem; margin-right: .5rem; font-size: 1.1rem;
        }

        /* Hero & page header */
        .bk-hero {
            background:
                radial-gradient(900px 400px at 85% -10%, rgba(37, 99, 235, .45), transparent 60%),
                linear-gradient(135deg, var(--bk-navy) 0%, var(--bk-navy-2) 100%);
            color: #fff; position: relative; overflow: hidden;
        }
        .bk-hero h1, .bk-hero h2 { color: #fff; }
        .bk-hero .lead { color: rgba(255, 255, 255, .8); }
        .bk-page-header { background: linear-gradient(135deg, var(--bk-navy) 0%, var(--bk-navy-2) 100%); color: #fff; }
        .bk-page-header h1 { color: #fff; }
        .bk-page-header p { color: rgba(255, 255, 255, .78); }
        .bk-page-header .breadcrumb a { color: rgba(255, 255, 255, .8); text-decoration: none; }
        .bk-page-header .breadcrumb-item.active, .bk-page-header .breadcrumb-item + .breadcrumb-item::before { color: rgba(255, 255, 255, .6); }

        /* Kartu */
        .bk-card {
            border: 1px solid var(--bk-border); border-radius: 1rem; background: #fff;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .bk-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(10, 37, 64, .10); }
        .bk-card .bk-title { color: var(--bk-navy); }
        .bk-card .bk-title:hover { color: var(--bk-blue); }
        .bk-panel { border: 1px solid var(--bk-border); border-radius: 1rem; background: #fff; }

        /* Badge jenjang */
        .bk-level { font-weight: 600; padding: .45em .75em; border-radius: 999px; }
        .bk-level-smp { background: #E0F2FE; color: #075985; }
        .bk-level-sma { background: #EDE9FE; color: #5B21B6; }
        .bk-level-kuliah { background: #DBEAFE; color: #1E40AF; }
        .bk-level-umum { background: #E2E8F0; color: #334155; }

        /* Statistik */
        .bk-stats { margin-top: -3rem; position: relative; z-index: 2; }
        .bk-stat-number { font-size: 2rem; font-weight: 800; color: var(--bk-navy); line-height: 1; }
        .bk-icon-box {
            width: 48px; height: 48px; display: grid; place-items: center; border-radius: .85rem;
            background: #DBEAFE; color: var(--bk-blue); font-size: 1.4rem; flex: none;
        }

        /* Chip jenjang */
        .bk-chip {
            display: inline-flex; align-items: center; gap: .4rem; padding: .5rem 1rem; border-radius: 999px;
            background: rgba(255, 255, 255, .12); color: #fff; text-decoration: none; font-weight: 600; font-size: .9rem;
            border: 1px solid rgba(255, 255, 255, .25); transition: background .15s ease;
        }
        .bk-chip:hover { background: rgba(255, 255, 255, .24); color: #fff; }
        .bk-chip .count { background: rgba(255, 255, 255, .22); border-radius: 999px; padding: 0 .5rem; font-size: .75rem; }

        /* Filter pill (halaman katalog) */
        .btn-check:checked + .bk-pill { background: var(--bk-navy); color: #fff; border-color: var(--bk-navy); }
        .bk-pill { border-radius: 999px; border: 1px solid var(--bk-border); background: #fff; color: var(--bk-navy); font-weight: 600; }
        .bk-pill:hover { border-color: var(--bk-navy); color: var(--bk-navy); background: #F1F5F9; }
        .btn-check:checked + .bk-pill:hover { color: #fff; background: var(--bk-navy); }
        .btn-check:focus-visible + .bk-pill { box-shadow: 0 0 0 .25rem rgba(37, 99, 235, .25); }

        /* Daftar persyaratan / manfaat */
        .bk-list { list-style: none; padding-left: 0; margin: 0; }
        .bk-list li { position: relative; padding-left: 1.75rem; margin-bottom: .6rem; }
        .bk-list li::before {
            font-family: 'bootstrap-icons'; content: '\F26A'; position: absolute; left: 0; top: 0;
            color: var(--bk-blue); font-size: 1.05rem;
        }

        /* Countdown di halaman detail */
        .bk-countdown-number { font-size: 3.5rem; font-weight: 800; line-height: 1; color: var(--bk-navy); }
        .bk-countdown-danger .bk-countdown-number { color: #DC2626; }

        /* Kalender */
        .bk-cal { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); border: 1px solid var(--bk-border); border-radius: 1rem; overflow: hidden; background: #fff; }
        .bk-cal-head { background: var(--bk-navy); color: #fff; text-align: center; font-weight: 600; font-size: .85rem; padding: .6rem 0; }
        .bk-cal-cell { min-height: 112px; padding: .35rem; border-top: 1px solid var(--bk-border); border-left: 1px solid var(--bk-border); min-width: 0; }
        .bk-cal-cell:nth-child(7n + 1) { border-left: 0; }
        .bk-cal-cell.is-outside { background: #F8FAFC; }
        .bk-cal-cell.is-outside .bk-cal-day { color: #CBD5E1; }
        .bk-cal-day { font-weight: 700; font-size: .85rem; width: 26px; height: 26px; display: inline-grid; place-items: center; border-radius: 50%; color: var(--bk-navy); }
        .bk-cal-cell.is-today .bk-cal-day { background: var(--bk-blue); color: #fff; }
        .bk-cal-event {
            display: block; font-size: .72rem; line-height: 1.25; padding: .2rem .4rem; margin-top: .2rem; border-radius: .4rem;
            text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 600;
        }
        .bk-cal-event.buka { background: #DBEAFE; color: #1E40AF; }
        .bk-cal-event.tutup { background: #FEE2E2; color: #B91C1C; }
        .bk-cal-event:hover { filter: brightness(.95); }
        .bk-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 2px; }
        .bk-dot.buka { background: var(--bk-blue); }
        .bk-dot.tutup { background: #DC2626; }
        @media (max-width: 767.98px) {
            .bk-cal-cell { min-height: 58px; padding: .2rem; text-align: center; }
        }

        /* Checklist */
        .bk-step { padding: .55rem .75rem; border-radius: .6rem; }
        .bk-step:hover { background: #F1F5F9; }
        .bk-step .form-check-input { margin-top: .3rem; }
        .bk-step.is-done label { text-decoration: line-through; color: var(--bk-muted); }

        .bk-sticky { position: sticky; top: 88px; }

        .bk-footer { background: var(--bk-navy); color: rgba(255, 255, 255, .75); }
        .bk-footer a { color: rgba(255, 255, 255, .85); text-decoration: none; }
        .bk-footer a:hover { color: #fff; text-decoration: underline; }
        .bk-footer h6 { color: #fff; }

        .bk-skip { position: absolute; left: -999px; top: 0; z-index: 2000; background: #fff; padding: .5rem 1rem; }
        .bk-skip:focus { left: 8px; top: 8px; }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <a href="#konten" class="bk-skip">Lewati ke konten</a>

    <nav class="navbar navbar-expand-lg navbar-dark bk-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <span class="bk-brand-icon"><i class="bi bi-mortarboard-fill"></i></span>
                Beasiswa<span class="accent">Kita</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"
                    aria-controls="navMenu" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto gap-lg-1 pt-3 pt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i>Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('scholarships.*') ? 'active' : '' }}" href="{{ route('scholarships.index') }}">
                            <i class="bi bi-search me-1"></i>Beasiswa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}" href="{{ route('calendar') }}">
                            <i class="bi bi-calendar3 me-1"></i>Kalender
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guide') ? 'active' : '' }}" href="{{ route('guide') }}">
                            <i class="bi bi-file-earmark-check me-1"></i>Panduan Dokumen
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @if (session('success') || session('error'))
        <div class="container pt-3">
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-start gap-2 mb-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill mt-1"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-start gap-2 mb-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
        </div>
    @endif

    <main id="konten" class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="bk-footer mt-5 pt-5 pb-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center mb-2">
                        <span class="bk-brand-icon text-white"><i class="bi bi-mortarboard-fill"></i></span>
                        <span class="fs-5 fw-bold text-white">Beasiswa<span style="color:#7FA8FF">Kita</span></span>
                    </div>
                    <p class="mb-2">Pusat Informasi Kesempatan Pendidikan.</p>
                    <p class="fst-italic mb-0">"Temukan Peluang, Raih Masa Depan."</p>
                </div>
                <div class="col-6 col-lg-3">
                    <h6 class="fw-bold mb-3">Jelajahi</h6>
                    <ul class="list-unstyled mb-0 d-grid gap-2">
                        <li><a href="{{ route('scholarships.index') }}">Daftar Beasiswa</a></li>
                        <li><a href="{{ route('calendar') }}">Kalender Pendaftaran</a></li>
                        <li><a href="{{ route('guide') }}">Panduan Dokumen</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-4">
                    <h6 class="fw-bold mb-3">Jenjang</h6>
                    <ul class="list-unstyled mb-0 d-grid gap-2">
                        <li><a href="{{ route('scholarships.index', ['jenjang' => 'SMP']) }}">Beasiswa SMP</a></li>
                        <li><a href="{{ route('scholarships.index', ['jenjang' => 'SMA']) }}">Beasiswa SMA/Sederajat</a></li>
                        <li><a href="{{ route('scholarships.index', ['jenjang' => 'Kuliah']) }}">Beasiswa Kuliah</a></li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary-subtle opacity-25 my-4">
            <p class="small mb-1">
                <i class="bi bi-info-circle me-1"></i>
                Informasi di situs ini bersifat ringkasan. Persyaratan dan jadwal dapat berubah;
                selalu pastikan di situs resmi penyelenggara sebelum mendaftar.
            </p>
            <p class="small mb-0">&copy; {{ date('Y') }} BeasiswaKita. Proyek Pengembangan Web.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
