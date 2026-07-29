<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('borrowing_id')->nullable()->constrained('borrowings')->nullOnDelete()->after('order_id');
            $table->index('borrowing_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['borrowing_id']);
            $table->dropIndex(['borrowing_id']);
            $table->dropColumn('borrowing_id');
        });
    }
};
