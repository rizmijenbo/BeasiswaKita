<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DocumentGuide extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'icon',
        'summary',
        'steps',
        'tips',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'steps' => 'array',
            'tips' => 'array',
        ];
    }

    public function scholarships(): BelongsToMany
    {
        return $this->belongsToMany(Scholarship::class);
    }
}
