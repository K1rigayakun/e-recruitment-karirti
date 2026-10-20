<?php

namespace App\Models;

use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'original_name',
        'path',
        'mime_type',
        'size_bytes',
        'sha256',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Lamaran yang menggunakan dokumen ini (untuk cegah hapus CV yang terpakai). */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'cv_document_id');
    }
}
