<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            if (!Schema::hasColumn('user_preferences', 'email_notifications')) {
                $table->boolean('email_notifications')->nullable()->default(true);
            }
            if (!Schema::hasColumn('user_preferences', 'workout_reminders')) {
                $table->boolean('workout_reminders')->nullable()->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropColumn(['email_notifications', 'workout_reminders']);
        });
    }
};