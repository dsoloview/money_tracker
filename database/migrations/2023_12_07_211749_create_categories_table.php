<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_category_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('icon_id')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type')->default('expense');
            $table->timestamps();

            $table
                ->foreign('parent_category_id')
                ->references('id')
                ->on('categories')
                ->nullOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('icon_id')
                ->references('id')
                ->on('icons')
                ->nullOnDelete();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
