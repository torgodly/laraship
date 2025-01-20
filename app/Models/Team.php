<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function sites(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Site::class);
    }

    //database
    public function databases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Database::class);
    }

    public function databaseUsers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DbUser::class);
    }

}
