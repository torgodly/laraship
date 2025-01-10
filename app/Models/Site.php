<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    /** @use HasFactory<\Database\Factories\SiteFactory> */
    use HasFactory;
    protected $fillable = [
        'team_id',
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
}
