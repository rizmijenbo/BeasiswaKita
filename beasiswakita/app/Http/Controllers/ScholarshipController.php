<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReminderRequest;
use App\Models\DocumentGuide;
use App\Models\Reminder;
use App\Models\Requirement;
use App\Models\Scholarship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ScholarshipController extends Controller
{
    /** Jumlah kartu per halaman pada katalog. */
    private const PER_PAGE = 9;

    /** Nilai filter status yang diizinkan pada katalog. */
    private const STATUS_FILTERS = ['buka', 'segera'];

    // ------------------------------------------------------------------
    // 1. Beranda  (/)
    // ------------------------------------------------------------------

    public function home(): View
    {
        $stats = [
            'total' => Scholarship::active()->count(),
            'open' => Scholarship::active()->openNow()->count(),
            'organizers' => Scholarship::active()->distinct()->count('organizer'),
            'guides' => DocumentGuide::count(),
        ];

        // Jumlah beasiswa per jenjang untuk tombol filter cepat.
        $levelCounts = Scholarship::active()
            ->selectRaw('level, count(*) as total')
            ->groupBy('level')
            ->pluck('total', 'level');

        // Beasiswa "Segera Tutup"; bila tidak ada, tampilkan tenggat terdekat.
        $closingSoon = Scholarship::active()->closingSoon()->orderBy('deadline')->limit(6)->get();
        $isUrgent = $closingSoon->isNotEmpty();

        if (! $isUrgent) {
            $closingSoon = Scholarship::active()->openNow()->orderBy('deadline')->limit(3)->get();
        }

        return view('home', [
            'stats' => $stats,
            'levels' => Scholarship::LEVELS,
            'levelCounts' => $levelCounts,
            'closingSoon' => $closingSoon,
            'isUrgent' => $isUrgent,
        ]);
    }

    // ------------------------------------------------------------------
    // 2. Daftar Beasiswa  (/beasiswa)
    // ------------------------------------------------------------------

    public function index(Request $request): View
    {
        $keyword = mb_substr($this->queryString($request, 'q'), 0, 100);

        $level = $this->queryString($request, 'jenjang');
        $level = in_array($level, Scholarship::LEVELS, true) ? $level : null;

        $status = $this->queryString($request, 'status');
        $status = in_array($status, self::STATUS_FILTERS, true) ? $status : null;

        $today = Carbon::today()->toDateString();

        $scholarships = Scholarship::active()
            ->search($keyword)
            ->level($level)
            ->when($status === 'buka', fn ($query) => $query->openNow())
            ->when($status === 'segera', fn ($query) => $query->closingSoon())
            // Yang sudah tutup ditaruh paling akhir, sisanya urut tenggat terdekat.
            ->orderByRaw('CASE WHEN deadline < ? THEN 1 ELSE 0 END', [$today])
            ->orderBy('deadline')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('scholarships.index', [
            'scholarships' => $scholarships,
            'levels' => Scholarship::LEVELS,
            'filters' => [
                'q' => $keyword,
                'jenjang' => $level,
                'status' => $status,
            ],
        ]);
    }

    // ------------------------------------------------------------------
    // 3. Detail Beasiswa  (/beasiswa/{slug})
    // ------------------------------------------------------------------

    public function show(Scholarship $scholarship): View
    {
        abort_unless($scholarship->is_active, 404);

        $scholarship->load(['requirements', 'documentGuides']);

        $related = Scholarship::active()
            ->where('level', $scholarship->level)
            ->whereKeyNot($scholarship->getKey())
            ->orderBy('deadline')
            ->limit(3)
            ->get();

        return view('scholarships.show', [
            'scholarship' => $scholarship,
            'requirementGroups' => $scholarship->requirements->groupBy('category'),
            'categories' => Requirement::CATEGORIES,
            'daysOptions' => Reminder::DAYS_OPTIONS,
            'related' => $related,
        ]);
    }

    // ------------------------------------------------------------------
    // Simpan email pengingat tenggat  (POST /beasiswa/{slug}/pengingat)
    // ------------------------------------------------------------------

    public function storeReminder(StoreReminderRequest $request, Scholarship $scholarship): RedirectResponse
    {
        abort_unless($scholarship->is_active, 404);

        $back = route('scholarships.show', $scholarship).'#pengingat';

        if ($scholarship->is_closed) {
            return redirect($back)->with('error', 'Pendaftaran beasiswa ini sudah ditutup, pengingat tidak dapat dibuat.');
        }

        $data = $request->validated();

        // Email yang sama untuk beasiswa yang sama hanya disimpan sekali (diperbarui).
        Reminder::updateOrCreate(
            [
                'scholarship_id' => $scholarship->getKey(),
                'email' => $data['email'],
            ],
            [
                'days_before' => (int) $data['days_before'],
                'notified_at' => null,
            ],
        );

        return redirect($back)->with(
            'success',
            "Pengingat tersimpan! Kami akan mengirim email ke {$data['email']} {$data['days_before']} hari sebelum tenggat {$scholarship->name}."
        );
    }

    // ------------------------------------------------------------------
    // 4. Kalender  (/kalender?bulan=YYYY-MM)
    // ------------------------------------------------------------------

    public function calendar(Request $request): View
    {
        $month = $this->resolveMonth($this->queryString($request, 'bulan'));

        // Grid kalender dimulai hari Minggu dan berakhir hari Sabtu.
        $gridStart = $month->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $gridEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $range = [$gridStart->toDateString(), $gridEnd->toDateString()];

        $scholarships = Scholarship::active()
            ->where(function ($query) use ($range) {
                $query->whereBetween('open_date', $range)
                    ->orWhereBetween('deadline', $range);
            })
            ->orderBy('deadline')
            ->get();

        // Kumpulkan kejadian (buka/tutup) per tanggal.
        $events = [];

        foreach ($scholarships as $scholarship) {
            if ($scholarship->open_date->between($gridStart, $gridEnd)) {
                $events[$scholarship->open_date->toDateString()][] = [
                    'type' => 'buka',
                    'scholarship' => $scholarship,
                ];
            }

            if ($scholarship->deadline->between($gridStart, $gridEnd)) {
                $events[$scholarship->deadline->toDateString()][] = [
                    'type' => 'tutup',
                    'scholarship' => $scholarship,
                ];
            }
        }

        // Susun hari-hari dalam grid menjadi minggu (7 hari).
        $days = [];
        for ($day = $gridStart->copy(); $day->lte($gridEnd); $day->addDay()) {
            $days[] = $day->copy();
        }
        $weeks = array_chunk($days, 7);

        // Agenda bulanan: hanya kejadian pada bulan yang sedang ditampilkan.
        $agenda = [];
        foreach ($events as $date => $items) {
            if (Carbon::parse($date)->isSameMonth($month)) {
                foreach ($items as $item) {
                    $agenda[] = $item + ['date' => Carbon::parse($date)];
                }
            }
        }
        usort($agenda, fn (array $a, array $b) => [$a['date'], $a['type']] <=> [$b['date'], $b['type']]);

        return view('calendar', [
            'month' => $month,
            'previousMonth' => $month->copy()->subMonthNoOverflow(),
            'nextMonth' => $month->copy()->addMonthNoOverflow(),
            'weeks' => $weeks,
            'events' => $events,
            'agenda' => $agenda,
            'today' => Carbon::today(),
            'dayNames' => ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        ]);
    }

    // ------------------------------------------------------------------
    // 5. Panduan Dokumen  (/panduan-dokumen)
    // ------------------------------------------------------------------

    public function guide(): View
    {
        return view('guide', [
            'guides' => DocumentGuide::orderBy('sort_order')->get(),
        ]);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /** Ambil query string sebagai string aman (abaikan array/null). */
    private function queryString(Request $request, string $key): string
    {
        $value = $request->query($key);

        return is_string($value) ? trim($value) : '';
    }

    /** Ubah "YYYY-MM" menjadi awal bulan; nilai tidak valid -> bulan ini. */
    private function resolveMonth(string $input): Carbon
    {
        if (preg_match('/^20\d{2}-(0[1-9]|1[0-2])$/', $input) === 1) {
            return Carbon::createFromFormat('!Y-m', $input)->startOfMonth();
        }

        return Carbon::today()->startOfMonth();
    }
}
