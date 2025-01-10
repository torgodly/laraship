<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('domain')->unique();
            $table->json('aliases')->nullable();
            $table->string('web_directory')->default('/public');
            $table->string('php_version');
            $table->boolean('wildcard')->default(false);
            $table->boolean('create_database')->default(false);
            $table->string('database_name')->nullable();
            $table->boolean('isolation')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
