<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar si ya existen las columnas de verificación
        if (!Schema::hasColumn('users', 'is_verified')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_verified')->default(false)->after('active');
                $table->string('verification_document')->nullable()->after('is_verified');
            });
        }

        // Memberships table
        if (!Schema::hasTable('memberships')) {
            Schema::create('memberships', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('price', 10, 2);
                $table->integer('duration_days');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Payments table — usa integer para coincidir con users.id (int)
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->unsignedBigInteger('membership_id')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('status')->default('completed');
                $table->string('payment_method')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('membership_id')->references('id')->on('memberships')->nullOnDelete();
            });
        }

        // Challenges table
        if (!Schema::hasTable('challenges')) {
            Schema::create('challenges', function (Blueprint $table) {
                $table->id();
                $table->integer('trainer_id');
                $table->string('title');
                $table->text('description');
                $table->enum('type', ['weekly', 'monthly']);
                $table->string('goal_type');
                $table->decimal('goal_value', 10, 2);
                $table->date('start_date');
                $table->date('end_date');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('trainer_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        // Challenge User Pivot table
        if (!Schema::hasTable('challenge_user')) {
            Schema::create('challenge_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('challenge_id');
                $table->integer('user_id');
                $table->decimal('current_progress', 10, 2)->default(0);
                $table->boolean('completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->foreign('challenge_id')->references('id')->on('challenges')->cascadeOnDelete();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->unique(['challenge_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_user');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('memberships');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'verification_document']);
        });
    }
};
