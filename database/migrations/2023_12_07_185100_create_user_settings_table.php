<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('main_currency_id');
            $table->unsignedBigInteger('language_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('main_currency_id')->references('id')->on('currencies');
            $table->foreign('language_id')->references('id')->on('languages');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
