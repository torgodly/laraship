<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbUser extends Model
{
    /** @use HasFactory<\Database\Factories\DbUserFactory> */
    use HasFactory;

    protected $fillable = [
        'username',
        'team_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function databases()
    {
        return $this->belongsToMany(Database::class, 'database_user');
    }
}
