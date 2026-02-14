<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Release extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ReleaseFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $fillable = [
        'product_id',
        'version',
        'release_notes',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('zip')
            ->singleFile()
            ->useDisk('media');
    }

    public function getZipFile(): ?Media
    {
        return $this->getFirstMedia('zip');
    }
}
