<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('telegram_user_states', function (Blueprint $table) {
            $table->dropForeign(['telegram_user_id']);
            $table->dropIndex('telegram_user_states_telegram_user_id_foreign');
            $table->unsignedBigInteger('telegram_user_id')->change();
            $table->foreign('telegram_user_id')->references('id')->on('telegram_users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
    }
};
