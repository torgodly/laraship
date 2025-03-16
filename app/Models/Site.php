<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    /** @use HasFactory<\Database\Factories\SiteFactory> */
    use HasFactory;
    use HasUuid;

    protected $guarded = ['id'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function isPending(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === \App\Enums\SiteStatus::Pending->value,
        );
    }

    //is pending

    public function isInitialized(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === \App\Enums\SiteStatus::Initialized->value,
        );
    }

    //is initialized

    public function isDeployed(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === \App\Enums\SiteStatus::Deployed->value,
        );
    }

    //is deployed

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }


    //deployments

    protected function casts(): array
    {
        return [
            'aliases' => 'array',
        ];
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn() => 'https://' . $this->domain,
        );
    }

}
