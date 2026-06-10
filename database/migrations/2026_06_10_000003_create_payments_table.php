<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('status', 40)->default('pending');
            $table->string('payment_method', 40)->default('doku');
            $table->string('doku_reference_number', 100)->nullable();
            $table->string('doku_transaction_id', 100)->nullable();
            $table->json('doku_response_json')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('doku_reference_number');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
