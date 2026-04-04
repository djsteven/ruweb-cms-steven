<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $appends = [
        'url',
        'is_image',
        'formatted_size',
    ];

    protected $fillable = [
        'filename',
        'original_filename',
        'path',
        'mime_type',
        'extension',
        'size',
        'alt',
        'title',
        'disk',
        'uploaded_by',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getUrlAttribute(): string
    {
        return $this->url();
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function getIsImageAttribute(): bool
    {
        return $this->isImage();
    }

    public function formattedSize(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public function getFormattedSizeAttribute(): string
    {
        return $this->formattedSize();
    }
}
