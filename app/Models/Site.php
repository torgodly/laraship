<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Site extends Model
{
    /** @use HasFactory<\Database\Factories\SiteFactory> */
    use HasFactory;

    protected $fillable = [
        'team_id',
        'type',
        'name',
        'domain',
        'aliases',
        'web_directory',
        'php_version',
        'wildcard',
        'create_database',
        'database_name',
        'isolation',
    ];

    protected function casts(): array
    {
        return [
            'aliases' => 'array',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

}
