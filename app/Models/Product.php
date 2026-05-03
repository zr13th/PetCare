<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use Spatie\Image\Enums\Fit;

class Product extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = ['name', 'category_id', 'brand_id', 'pet_type_id', 'slug', 'description', 'unit', 'is_active'];

    public function variants(): HasMany {
        return $this->hasMany(ProductVariant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function petType(): BelongsTo
    {
        return $this->belongsTo(PetType::class);
    }

    // Media
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_images')
             ->useFallbackUrl('/images/placeholder.jpg');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 200, 200)
            ->background('#ffffff')
            ->format('png');
    }
}