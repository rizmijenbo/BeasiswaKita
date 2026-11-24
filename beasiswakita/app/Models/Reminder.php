<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    /** Pilihan "ingatkan X hari sebelum tenggat". */
    public const DAYS_OPTIONS = [1, 3, 7, 14];

    protected $fillable = [
        'scholarship_id',
        'email',
        'days_before',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'days_before' => 'integer',
            'notified_at' => 'datetime',
        ];
    }

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }
}
