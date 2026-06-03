<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Agregar columnas de Stripe a la tabla payments existente.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_intent_id')) {
                $table->string('payment_intent_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('payments', 'customer_id')) {
                $table->string('customer_id')->nullable()->after('payment_intent_id');
            }
            if (!Schema::hasColumn('payments', 'amount_cents')) {
                $table->bigInteger('amount_cents')->default(0)->after('amount');
            }
            if (!Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 3)->default('usd')->after('amount_cents');
            }
            if (!Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $columns = ['payment_intent_id', 'customer_id', 'amount_cents', 'currency', 'paid_at'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
