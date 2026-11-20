<?php
require_once dirname(__DIR__) . '/config/db.php';
requireAuth();

$pageTitle = 'Создание нового проекта';
$formAction = 'project_create.php';
$code = '';
$name = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $code = strtoupper(trim($_POST['code'] ?? ''));
    $name = trim($_POST['name'] ?? '');

    if ($code === '' || !preg_match('/^[A-Z0-9_-]{2,10}$/', $code)) {
        $errors[] = 'Код проекта должен содержать от 2 до 10 латинских букв, цифр, дефис или подчёркивание.';
    }
    if (mb_strlen($name) < 3 || mb_strlen($name) > 100) {
        $errors[] = 'Название проекта должно содержать от 3 до 100 символов.';
    }

    if (empty($errors)) {
        $check = $pdo->prepare("SELECT project_id FROM Projects WHERE code = ?");
        $check->execute([$code]);
        if ($check->fetch()) {
            $errors[] = 'Проект с таким кодом уже существует.';
        } else {
            $ownerId = (int)$_SESSION['user_id'];
            $stmt = $pdo->prepare("INSERT INTO Projects (code, name, lead_id, owner_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$code, $name, $ownerId, $ownerId]);

            setFlash('success', "Проект «{$name}» ({$code}) успешно создан.");
            header('Location: projects.php');
            exit;
        }
    }
}

require dirname(__DIR__) . '/views/projects/form.html';
