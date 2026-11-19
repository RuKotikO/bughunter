<?php
$dbFile = __DIR__ . '/bughunter.db';

if (!file_exists($dbFile)) {
    die("Файл базы данных bughunter.db не найден в папке проекта.");
}

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("PRAGMA foreign_keys = ON;");
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
