<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tablas de dominio de Tone Trainer.
     */
    public function up(): void
    {
        // ── Training Plans ───────────────────────────────────────
        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->string('day_group', 50);
            $table->string('exercise', 100);
            $table->integer('series');
            $table->integer('reps');
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });

        // ── Training Completions ────────────────────────────────
        Schema::create('training_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained('training_plans')->cascadeOnDelete();
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'completed_at']);
        });

        // ── Nutrition Plans ──────────────────────────────────────
        Schema::create('nutrition_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('day_of_week', 20)->nullable();
            $table->string('meal_type', 20)->nullable();
            $table->string('food_name', 100)->nullable();
            $table->integer('calories')->nullable();
            $table->integer('protein')->nullable();
            $table->integer('carbs')->nullable();
            $table->integer('fats')->nullable();
            $table->timestamps();
        });

        // ── Progress (Unified) ───────────────────────────────────────────
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('body_fat', 5, 2)->nullable();
            $table->decimal('muscle_mass', 5, 2)->nullable();
            $table->decimal('bmi', 5, 2)->nullable();
            $table->decimal('water_intake', 5, 2)->nullable();
            $table->decimal('protein_intake', 6, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── Goals ───────────────────────────────────────────────
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 50);
            $table->decimal('target_value', 10, 2);
            $table->decimal('current_value', 10, 2)->default(0);
            $table->string('unit', 20)->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['active', 'completed', 'failed'])->default('active');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // ── Achievements ────────────────────────────────────────
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('badge_name', 100);
            $table->string('badge_icon', 50)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('earned_at')->useCurrent();
            $table->timestamps();
        });

        // ── Messages ────────────────────────────────────────────
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->tinyInteger('is_read')->default(0);
            $table->timestamps();

            $table->index(['sender_id', 'receiver_id'], 'idx_conversation');
            $table->index(['receiver_id', 'is_read'], 'idx_receiver_unread');
            $table->index('created_at', 'idx_created');
        });

        // ── Workout Calendars ────────────────────────────────────
        Schema::create('workout_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('workout_date');
            $table->string('workout_type', 50)->nullable();
            $table->string('title')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('completed')->default(0);
            $table->integer('duration_minutes')->nullable();
            $table->integer('calories_burned')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'workout_date', 'title'], 'unique_user_date');
        });

        // ── User Preferences ────────────────────────────────────
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('goal', 100)->nullable();
            $table->enum('training_level', ['beginner', 'intermediate', 'advanced'])->nullable();
            $table->tinyInteger('weekly_frequency')->nullable();
            $table->string('training_type', 100)->nullable();
            $table->text('physical_restrictions')->nullable();
            $table->string('preferred_schedule', 20)->nullable();
            $table->boolean('reminders')->nullable();
            $table->boolean('push_notifications')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('workout_calendars');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('goals');
        Schema::dropIfExists('progress');
        Schema::dropIfExists('nutrition_plans');
        Schema::dropIfExists('training_completions');
        Schema::dropIfExists('training_plans');
    }
};

