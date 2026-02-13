<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('two_factor_secrets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('secret_encrypted');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
        Schema::table('two_factor_secrets', function (Blueprint $table) {
            $table->unique('user_id');
        });

        Schema::create('backup_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code_hash', 64);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
        Schema::table('backup_codes', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_codes');
        Schema::dropIfExists('two_factor_secrets');
    }
};
