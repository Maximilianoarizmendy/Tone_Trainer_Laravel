<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$migrations = [
    '0001_01_01_000000_create_users_table',
    '0001_01_01_000001_create_cache_table',
    '0001_01_01_000002_create_jobs_table',
    '2026_04_20_175147_create_tone_trainer_tables',
    '2026_04_21_000000_add_notification_settings',
];

echo "Sincronizando migraciones en MySQL...\n";

foreach ($migrations as $m) {
    $exists = DB::table('migrations')->where('migration', $m)->exists();
    if (!$exists) {
        DB::table('migrations')->insert([
            'migration' => $m,
            'batch' => 1
        ]);
        echo "Marcada como completada: $m\n";
    }
}

echo "Sincronización terminada. Ejecutando migraciones faltantes...\n";
