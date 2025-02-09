<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('type')->default('github');
            $table->string('organization_name')->nullable();
            $table->string('app_name');
            $table->string('app_id')->nullable();
            $table->string('app_url')->nullable();
            $table->text('description')->nullable();
            $table->string("client_id")->nullable();
            $table->string("webhook_secret")->nullable();
            $table->string("client_secret")->nullable();
            $table->text("private_key")->nullable();
            $table->json('permissions')->nullable();
            $table->json('events')->nullable();
            $table->json('owner')->nullable();
            $table->string('installation_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
