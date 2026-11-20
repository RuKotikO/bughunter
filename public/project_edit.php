<?php
require_once dirname(__DIR__) . '/config/db.php';
requireAuth();

$projectId = (int)($_GET['id'] ?? $_POST['project_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM Projects WHERE project_id = ?");
$stmt->execute([$projectId]);
$project = $stmt->fetch();

if (!$project) {
    setFlash('error', 'Проект не найден.');
    header('Location: projects.php');
    exit;
}

// Серверная проверка прав доступа
if (!canManageProject($pdo, $project)) {
    setFlash('error', 'У вас нет прав для редактирования этого проекта.');
    header('Location: projects.php');
    exit;
}

$pageTitle = 'Редактирование проекта';
$formAction = 'project_edit.php?id=' . $projectId;
$code = $project['code'];
$name = $project['name'];
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
        $check = $pdo->prepare("SELECT project_id FROM Projects WHERE code = ? AND project_id != ?");
        $check->execute([$code, $projectId]);
        if ($check->fetch()) {
            $errors[] = 'Другой проект уже использует этот код.';
        } else {
            $update = $pdo->prepare("UPDATE Projects SET code = ?, name = ? WHERE project_id = ?");
            $update->execute([$code, $name, $projectId]);

            setFlash('success', "Проект «{$name}» успешно обновлён.");
            header('Location: projects.php');
            exit;
        }
    }
}

require dirname(__DIR__) . '/views/projects/form.html';
