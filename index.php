<?php
require_once 'db.php';

// Защита закрытой страницы
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$tablesData = [
    'Users'      => $pdo->query("SELECT user_id, full_name, email, role FROM Users")->fetchAll(),
    'Projects'   => $pdo->query("SELECT * FROM Projects")->fetchAll(),
    'Bugs'       => $pdo->query("SELECT * FROM Bugs")->fetchAll(),
    'Comments'   => $pdo->query("SELECT * FROM Comments")->fetchAll(),
    'BugHistory' => $pdo->query("SELECT * FROM BugHistory")->fetchAll(),
];

require 'view.html';
