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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'membership_id')) {
                $table->unsignedBigInteger('membership_id')->nullable()->after('role');
                $table->foreign('membership_id')->references('id')->on('memberships')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'membership_id')) {
                $table->dropForeign(['membership_id']);
                $table->dropColumn('membership_id');
            }
        });
    }
};
