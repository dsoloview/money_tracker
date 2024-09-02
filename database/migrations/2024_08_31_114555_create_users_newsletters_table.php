<?php

use App\Enums\Newsletter\NewsletterPeriodsEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users_newsletters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('newsletter_id')->constrained()->onDelete('cascade');
            $table->foreignId('channel_id')->nullable()->constrained('newsletter_channels')->nullOnDelete();
            $table->boolean('subscribed')->default(false);
            $table->dateTime('subscribed_at')->nullable();
            $table->dateTime('unsubscribed_at')->nullable();
            $table->string('period')->default(NewsletterPeriodsEnum::OFF->value);
            $table->timestamps();

            $table->unique(['user_id', 'newsletter_id', 'channel_id']);
            $table->index('period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_newsletters');
    }
};
