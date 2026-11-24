<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Scholarship extends Model
{
    /** Jenjang pendidikan yang didukung (urutan = urutan tampil di filter). */
    public const LEVELS = ['SMP', 'SMA', 'Kuliah', 'Umum'];

    /** Ikon Bootstrap Icons untuk tiap jenjang. */
    public const LEVEL_ICONS = [
        'SMP' => 'backpack',
        'SMA' => 'book',
        'Kuliah' => 'mortarboard',
        'Umum' => 'globe2',
    ];

    /** Sisa hari (<=) agar beasiswa dianggap "Segera Tutup" (badge merah). */
    public const URGENT_DAYS = 14;

    /** Sisa hari (<=) untuk badge peringatan kuning. */
    public const WARNING_DAYS = 30;

    protected $fillable = [
        'name',
        'slug',
        'organizer',
        'level',
        'summary',
        'description',
        'benefits',
        'open_date',
        'deadline',
        'official_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'open_date' => 'date',
            'deadline' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /** Route model binding memakai slug: /beasiswa/{slug}. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ------------------------------------------------------------------
    // Relasi
    // ------------------------------------------------------------------

    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class)->orderBy('sort_order')->orderBy('id');
    }

    public function documentGuides(): BelongsToMany
    {
        return $this->belongsToMany(DocumentGuide::class)->orderBy('document_guides.sort_order');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Pencarian berdasarkan nama beasiswa atau penyelenggara. */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return $query;
        }

        // Escape karakter wildcard LIKE agar input pengguna diperlakukan literal.
        $like = '%'.addcslashes($keyword, '%_\\').'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('name', 'like', $like)
                ->orWhere('organizer', 'like', $like);
        });
    }

    public function scopeLevel(Builder $query, ?string $level): Builder
    {
        return in_array($level, self::LEVELS, true)
            ? $query->where('level', $level)
            : $query;
    }

    /** Pendaftaran sedang dibuka: open_date <= hari ini <= deadline. */
    public function scopeOpenNow(Builder $query): Builder
    {
        $today = Carbon::today()->toDateString();

        return $query->whereDate('open_date', '<=', $today)
            ->whereDate('deadline', '>=', $today);
    }

    /** Sedang dibuka dan tenggatnya <= URGENT_DAYS hari lagi. */
    public function scopeClosingSoon(Builder $query): Builder
    {
        return $query->openNow()
            ->whereDate('deadline', '<=', Carbon::today()->addDays(self::URGENT_DAYS)->toDateString());
    }

    // ------------------------------------------------------------------
    // Accessors (status & countdown)
    // ------------------------------------------------------------------

    /** Sisa hari sampai tenggat. 0 = hari ini, negatif = sudah lewat. */
    protected function daysLeft(): Attribute
    {
        return Attribute::get(function (): int {
            $today = Carbon::today();
            $deadline = $this->deadline->copy()->startOfDay();

            return (int) round($today->diffInDays($deadline, false));
        });
    }

    /** Hari sampai pendaftaran dibuka (0 jika sudah dibuka). */
    protected function daysUntilOpen(): Attribute
    {
        return Attribute::get(function (): int {
            $diff = (int) round(Carbon::today()->diffInDays($this->open_date->copy()->startOfDay(), false));

            return max($diff, 0);
        });
    }

    /** upcoming | open | closing_soon | closed */
    protected function status(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->days_until_open > 0) {
                return 'upcoming';
            }

            if ($this->days_left < 0) {
                return 'closed';
            }

            return $this->days_left <= self::URGENT_DAYS ? 'closing_soon' : 'open';
        });
    }

    protected function isClosed(): Attribute
    {
        return Attribute::get(fn (): bool => $this->status === 'closed');
    }

    protected function isUrgent(): Attribute
    {
        return Attribute::get(fn (): bool => $this->status === 'closing_soon');
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn (): string => match ($this->status) {
            'upcoming' => 'Segera Dibuka',
            'closing_soon' => 'Segera Tutup',
            'closed' => 'Ditutup',
            default => 'Dibuka',
        });
    }

    /** Teks countdown, mis. "Tutup dalam 5 hari". */
    protected function countdownLabel(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->status === 'upcoming') {
                return 'Dibuka dalam '.$this->days_until_open.' hari';
            }

            return match (true) {
                $this->days_left < 0 => 'Pendaftaran ditutup',
                $this->days_left === 0 => 'Tutup hari ini',
                $this->days_left === 1 => 'Tutup besok',
                default => 'Tutup dalam '.$this->days_left.' hari',
            };
        });
    }

    /** Kelas Bootstrap untuk badge countdown (merah jika mendesak). */
    protected function badgeClass(): Attribute
    {
        return Attribute::get(function (): string {
            return match (true) {
                $this->status === 'upcoming' => 'text-bg-info',
                $this->status === 'closed' => 'text-bg-secondary',
                $this->days_left <= self::URGENT_DAYS => 'text-bg-danger',
                $this->days_left <= self::WARNING_DAYS => 'text-bg-warning',
                default => 'text-bg-success',
            };
        });
    }
}
