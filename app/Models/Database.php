<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Database extends Model
{
    /** @use HasFactory<\Database\Factories\DatabaseFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'team_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function users()
    {
        return $this->belongsToMany(DbUser::class);
    }

}
