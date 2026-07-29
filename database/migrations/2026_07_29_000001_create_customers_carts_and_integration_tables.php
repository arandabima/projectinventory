<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
            $table->unique(['cart_id', 'item_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('buyer_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->index('buyer_id');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway_reference', 100)->nullable()->unique()->after('payment_method');
        });

    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) { $table->dropColumn('gateway_reference'); });
        Schema::table('orders', function (Blueprint $table) { $table->dropConstrainedForeignId('buyer_id'); });
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
