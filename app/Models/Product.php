<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use HasTranslations;
    use InteractsWithMedia;

    /** @var array<int, string> */
    public array $translatable = [
        'short_description',
        'long_description',
    ];

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'long_description',
        'type',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductType::class,
            'is_active' => 'boolean',
        ];
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function releases(): HasMany
    {
        return $this->hasMany(Release::class);
    }

    public function latestRelease(): HasOne
    {
        return $this->hasOne(Release::class)->latestOfMany();
    }

    public function activePackages(): HasMany
    {
        return $this->packages()->where('is_active', true);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('banner')
            ->useFallbackUrl(url: asset('images/fallback-banner.jpg'))
            ->useDisk('media')
            ->singleFile()
            ->registerMediaConversions(function (?Media $media = null) {
                $this
                    ->addMediaConversion('banner')
                    ->fit(Fit::Contain, 1920, 1080)
                    ->format('webp')
                    ->queued();
            });

        $this
            ->addMediaCollection('screenshots')
            ->useDisk('media')
            ->registerMediaConversions(function (?Media $media = null) {
                $this
                    ->addMediaConversion('thumbnail')
                    ->fit(Fit::Contain, 480, 360)
                    ->format('webp')
                    ->sharpen(10)
                    ->nonQueued();
            });
    }

    /**
     * @return \Illuminate\Support\Collection<int, Media>
     */
    public function getScreenshots(): \Illuminate\Support\Collection
    {
        return $this->getMedia('screenshots');
    }
}
