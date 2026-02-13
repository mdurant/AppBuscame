<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan_slug', 60);
            $table->string('status', 30)->default('active');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamps();
        });
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('ends_at');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('payable_type', 100);
            $table->unsignedBigInteger('payable_id');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('CLP');
            $table->string('status', 30)->default('pending');
            $table->string('gateway', 40)->nullable();
            $table->string('gateway_reference')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('gateway_reference');
            $table->index(['payable_type', 'payable_id']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number')->unique();
            $table->string('type', 40);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('CLP');
            $table->string('status', 30)->default('issued');
            $table->string('file_path')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscriptions');
    }
};
