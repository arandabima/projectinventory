<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->string('supplier_name', 120);
            $table->string('recipient_name', 120);
            $table->decimal('total_amount', 15, 2);
            $table->string('status', 40)->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index('order_number');
            $table->index(['status', 'created_at']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
