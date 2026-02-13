<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->decimal('overall_score', 5, 2)->default(0);
            $table->string('verification_status', 30)->default('unverified');
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();
        });
        Schema::table('property_scores', function (Blueprint $table) {
            $table->unique('property_id');
        });

        Schema::create('score_factors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_score_id')->constrained()->cascadeOnDelete();
            $table->string('factor_key', 60);
            $table->decimal('weight', 5, 2)->default(0);
            $table->decimal('value', 10, 2)->default(0);
            $table->timestamps();
        });
        Schema::table('score_factors', function (Blueprint $table) {
            $table->index('property_score_id');
        });

        Schema::create('score_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->decimal('previous_score', 5, 2)->nullable();
            $table->decimal('new_score', 5, 2);
            $table->json('factors_snapshot')->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();
        });
        Schema::table('score_history', function (Blueprint $table) {
            $table->index('property_id');
            $table->index('calculated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_history');
        Schema::dropIfExists('score_factors');
        Schema::dropIfExists('property_scores');
    }
};
