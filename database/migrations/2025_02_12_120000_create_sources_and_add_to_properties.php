<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 60)->unique();
            $table->string('type', 30)->default('platform');
            $table->string('url')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->nullable()->after('user_id');
        });

        DB::table('sources')->insert([
            ['name' => 'BuscaMe', 'slug' => 'buscame', 'type' => 'platform', 'url' => null, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('properties')->update(['source_id' => 1]);

        Schema::table('properties', function (Blueprint $table) {
            $table->foreign('source_id')->references('id')->on('sources')->nullOnDelete();
            $table->index('source_id');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['source_id']);
        });
        Schema::dropIfExists('sources');
    }
};
