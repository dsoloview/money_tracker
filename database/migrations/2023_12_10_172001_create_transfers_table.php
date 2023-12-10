<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_from_id');
            $table->unsignedBigInteger('account_to_id');
            $table->string('comment')->nullable();
            $table->bigInteger('amount');
            $table->timestamps();

            $table->foreign('account_from_id')
                ->references('id')
                ->on('accounts')
                ->cascadeOnDelete();

            $table->foreign('account_to_id')
                ->references('id')
                ->on('accounts')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
