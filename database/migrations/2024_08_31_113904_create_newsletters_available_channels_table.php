<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('newsletters_available_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->constrained()->onDelete('cascade');
            $table->foreignId('channel_id')->constrained('newsletter_channels')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletters_channels');
    }
};
