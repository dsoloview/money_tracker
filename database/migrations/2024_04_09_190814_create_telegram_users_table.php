<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('telegram_id');
            $table->bigInteger('chat_id')->nullable();
            $table->string('username')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->index('telegram_id');
            $table->index('chat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_users');
    }
};
