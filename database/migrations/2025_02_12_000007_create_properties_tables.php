<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40);
            $table->string('status', 30)->default('draft');
            $table->string('rental_type', 30)->nullable();
            $table->decimal('cost_amount', 12, 2)->nullable();
            $table->string('cost_currency', 3)->default('CLP');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->boolean('includes_cleaning')->default(false);
            $table->unsignedTinyInteger('completeness_percent')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
        Schema::table('properties', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index(['status', 'published_at']);
        });

        Schema::create('property_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('address_line')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 2)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('place_id')->nullable();
            $table->timestamps();
        });
        Schema::table('property_addresses', function (Blueprint $table) {
            $table->unique('property_id');
            $table->index(['latitude', 'longitude']);
        });

        Schema::create('property_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::table('property_photos', function (Blueprint $table) {
            $table->index(['property_id', 'sort_order']);
        });

        Schema::create('availability_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
        Schema::table('availability_ranges', function (Blueprint $table) {
            $table->index('property_id');
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_ranges');
        Schema::dropIfExists('property_photos');
        Schema::dropIfExists('property_addresses');
        Schema::dropIfExists('properties');
    }
};
