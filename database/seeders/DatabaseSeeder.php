<?php

namespace Database\Seeders;

use App\Models\Database;
use App\Models\DbUser;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'admin@admin.com',
//        ])->each(function (User $user): void {
//            $user->teams()->saveMany(Team::factory(4)->make());
//        });

        //each team has 3 databases 2 with users and 1 without
        Database::factory(3)->create()->each(function (Database $database): void {
            $database->users()->saveMany(DbUser::factory(2)->make());
        });
    }
}
