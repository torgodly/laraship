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
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained();
            $table->string('repository_url');
            $table->string('branch');
            $table->string('commit_hash')->nullable();
            $table->string('action_type');
            $table->string('status')->default(\App\Enums\DeploymentStatus::Pending->value);
            $table->foreignId('triggered_by')->nullable()->constrained('users');
            $table->text('output')->nullable();
            $table->timestamp('deployed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployments');
    }
};
