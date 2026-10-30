<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\OfferType;
use App\Enums\PropertyStatus;
use App\Services\PropertyCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Property extends Model
{
    public ?int $cacheOldProvinceId = null;

    protected $fillable = ['agency_id', 'province_id', 'title', 'description', 'type', 'offer_type', 'status', 'price_amount', 'currency', 'address', 'bedrooms', 'bathrooms', 'area_sqm', 'owner_phone', 'commission_percent', 'internal_notes', 'is_featured', 'approved_at'];

    protected $hidden = ['commission_percent', 'internal_notes', 'owner_phone'];

    protected function casts(): array
    {
        return ['type' => 'string', 'offer_type' => OfferType::class, 'status' => PropertyStatus::class, 'currency' => Currency::class, 'price_amount' => 'decimal:2', 'area_sqm' => 'decimal:2', 'is_featured' => 'boolean', 'approved_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('publicly_visible', function (Builder $q) {
            $user = auth('sanctum')->user();
            if ($user?->agency_id) {
                $q->where(function (Builder $visible) use ($user) {
                    $visible->where('agency_id', $user->agency_id)->orWhere(function (Builder $public) {
                        $public->where('status', PropertyStatus::Available->value)->whereNotNull('approved_at');
                    });
                });
            } else {
                $q->where('status', PropertyStatus::Available->value)->whereNotNull('approved_at');
            }
        });
        static::updating(function (self $p) {
            $p->cacheOldProvinceId = (int) $p->getOriginal('province_id');
        });
        static::saved(function (self $p) {
            app(PropertyCache::class)->forgetProperty($p, $p->cacheOldProvinceId ?? null);
        });
        static::deleted(function (self $p) {
            app(PropertyCache::class)->forgetProperty($p, (int) $p->province_id);
        });
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'property')->orderBy('sort_order');
    }

    public function scopeInProvince(Builder $q, ?int $id): Builder
    {
        return $id ? $q->where('province_id', $id) : $q;
    }

    public function scopeOfOfferType(Builder $q, ?string $type): Builder
    {
        return $type ? $q->where('offer_type', $type) : $q;
    }

    public function scopePriceRange(Builder $q, ?float $min, ?float $max): Builder
    {
        return $q->when($min !== null, fn ($x) => $x->where('price_amount', '>=', $min))->when($max !== null, fn ($x) => $x->where('price_amount', '<=', $max));
    }
}
