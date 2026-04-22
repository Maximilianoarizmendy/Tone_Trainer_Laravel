<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tablas de dominio de Tone Trainer.
     * Usa hasTable() para no destruir tablas que ya existan en MySQL.
     */
    public function up(): void
    {
        // ── Training Plan ───────────────────────────────────────
        if (!Schema::hasTable('training_plan')) {
            Schema::create('training_plan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
                $table->string('day_group', 50);
                $table->string('exercise', 100);
                $table->integer('series');
                $table->integer('reps');
                $table->text('description')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // ── Training Completions ────────────────────────────────
        if (!Schema::hasTable('training_completions')) {
            Schema::create('training_completions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedBigInteger('exercise_id');
                $table->timestamp('completed_at')->useCurrent();

                $table->foreign('exercise_id')->references('id')->on('training_plan')->cascadeOnDelete();
                $table->index(['user_id', 'completed_at']);
            });
        }

        // ── Nutrition Plan ──────────────────────────────────────
        if (!Schema::hasTable('nutrition_plan')) {
            Schema::create('nutrition_plan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('day_of_week', 20)->nullable();
                $table->string('meal_type', 20)->nullable();
                $table->string('food_name', 100)->nullable();
                $table->integer('calories')->nullable();
                $table->integer('protein')->nullable();
                $table->integer('carbs')->nullable();
                $table->integer('fats')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // ── Progress ────────────────────────────────────────────
        if (!Schema::hasTable('progress')) {
            Schema::create('progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->decimal('weight', 5, 2)->nullable();
                $table->decimal('body_fat', 5, 2)->nullable();
                $table->decimal('muscle_mass', 5, 2)->nullable();
                $table->decimal('bmi', 5, 2)->nullable();
                $table->decimal('water_intake', 5, 2)->nullable();
                $table->decimal('protein_intake', 6, 2)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // ── Progress Metrics ────────────────────────────────────
        if (!Schema::hasTable('progress_metrics')) {
            Schema::create('progress_metrics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->date('date')->useCurrent();
                $table->decimal('weight', 5, 2)->nullable();
                $table->decimal('height', 5, 2)->nullable();
                $table->decimal('imc', 5, 2)->nullable();
                $table->decimal('body_fat', 5, 2)->nullable();
                $table->decimal('muscle_mass', 5, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->decimal('bmi', 5, 2)->nullable();
                $table->decimal('water_intake', 5, 2)->nullable();
                $table->decimal('protein_intake', 6, 2)->nullable();
            });
        }

        // ── Goals ───────────────────────────────────────────────
        if (!Schema::hasTable('goals')) {
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
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('completed_at')->nullable();
            });
        }

        // ── Achievements ────────────────────────────────────────
        if (!Schema::hasTable('achievements')) {
            Schema::create('achievements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('badge_name', 100);
                $table->string('badge_icon', 50)->nullable();
                $table->text('description')->nullable();
                $table->timestamp('earned_at')->useCurrent();
            });
        }

        // ── Messages ────────────────────────────────────────────
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
                $table->text('message');
                $table->tinyInteger('is_read')->default(0);
                $table->timestamp('created_at')->useCurrent();

                $table->index(['sender_id', 'receiver_id'], 'idx_conversation');
                $table->index(['receiver_id', 'is_read'], 'idx_receiver_unread');
                $table->index('created_at', 'idx_created');
            });
        }

        // ── Workout Calendar ────────────────────────────────────
        if (!Schema::hasTable('workout_calendar')) {
            Schema::create('workout_calendar', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->date('workout_date');
                $table->string('workout_type', 50)->nullable();
                $table->string('title')->nullable();
                $table->text('notes')->nullable();
                $table->tinyInteger('completed')->default(0);
                $table->integer('duration_minutes')->nullable();
                $table->integer('calories_burned')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->unique(['user_id', 'workout_date', 'title'], 'unique_user_date');
            });
        }

        // ── User Preferences ────────────────────────────────────
        if (!Schema::hasTable('user_preferences')) {
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
                $table->timestamp('last_update')->nullable()->useCurrentOnUpdate();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_completions');
        Schema::dropIfExists('training_plan');
        Schema::dropIfExists('nutrition_plan');
        Schema::dropIfExists('progress');
        Schema::dropIfExists('progress_metrics');
        Schema::dropIfExists('goals');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('workout_calendar');
        Schema::dropIfExists('user_preferences');
    }
};
