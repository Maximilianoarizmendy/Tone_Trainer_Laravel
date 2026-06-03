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
        if (!Schema::hasColumn('progress', 'is_validated')) {
            Schema::table('progress', function (Blueprint $table) {
                $table->boolean('is_validated')->default(false)->after('protein_intake');
                $table->text('trainer_comment')->nullable()->after('is_validated');
            });
        }
    }

    public function down(): void
    {
        Schema::table('progress', function (Blueprint $table) {
            $table->dropColumn(['is_validated', 'trainer_comment']);
        });
    }
};
