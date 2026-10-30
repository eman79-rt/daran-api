<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Agency extends Model
{
    protected $fillable = ['name', 'email', 'phone'];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function logo(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable')->where('collection', 'logo');
    }
}
