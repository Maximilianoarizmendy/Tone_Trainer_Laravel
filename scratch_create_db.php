<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS tone_trainer_db");
    echo "Base de datos creada o ya existente.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
