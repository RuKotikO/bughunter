<?php
require_once 'db.php';

$tableNames = ['Users', 'Projects', 'Bugs', 'Comments', 'BugHistory'];
$tablesData = [];

foreach ($tableNames as $name) {
    $stmt = $pdo->query("SELECT * FROM " . $name);
    $tablesData[$name] = $stmt->fetchAll();
}

require 'view.html';
