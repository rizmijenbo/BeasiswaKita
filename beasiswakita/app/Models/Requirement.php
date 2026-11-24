<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requirement extends Model
{
    /** kategori => [label, ikon Bootstrap Icons] (urutan = urutan tampil). */
    public const CATEGORIES = [
        'akademik' => ['label' => 'Persyaratan Akademik', 'icon' => 'mortarboard'],
        'administrasi' => ['label' => 'Persyaratan Administrasi', 'icon' => 'folder2-open'],
        'umum' => ['label' => 'Persyaratan Umum', 'icon' => 'person-check'],
    ];

    protected $fillable = [
        'scholarship_id',
        'category',
        'description',
        'sort_order',
    ];

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }
}
