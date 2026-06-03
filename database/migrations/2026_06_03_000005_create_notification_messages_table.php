<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_messages', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 40)->default('system');
            $table->string('recipient', 120);
            $table->string('subject', 160);
            $table->text('message');
            $table->string('status', 40)->default('pending');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_messages');
    }
};
