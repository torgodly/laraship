<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Source extends Model
{
    use HasUuid;

    protected $guarded = ['id'];

    public function owners(): MorphToMany
    {
        return $this->morphToMany(\App\Models\User::class, 'owner', 'source_owner', 'source_id', 'owner_id')
            ->orWhere('owner_type', \App\Models\Team::class);
    }

    //owners "many to many" with morph

    public function userOwners(): MorphToMany
    {
        return $this->morphedByMany(\App\Models\User::class, 'owner', 'source_owner');
    }

    // Relationship to get only User owners

    public function teamOwners(): MorphToMany
    {
        return $this->morphedByMany(\App\Models\Team::class, 'owner', 'source_owner');
    }

    // Relationship to get only Team owners

    public function getisCompletedAttribute(): bool
    {
        return $this->app_name && $this->client_id;
    }

    //isCompleted

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'events' => 'array',
            'owner' => 'array',
        ];
    }
}
