<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('usuario')->after('verification_status');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->string('region', 80)->nullable()->after('phone');
            $table->string('city', 80)->nullable()->after('region');
        });

        Schema::create('property_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip', 45)->nullable();
            $table->timestamp('viewed_at');
            $table->timestamps();
        });
        Schema::table('property_views', function (Blueprint $table) {
            $table->index('property_id');
            $table->index('viewed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_views');
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['region', 'city']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }
};
