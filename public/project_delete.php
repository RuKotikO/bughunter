<?php
require_once dirname(__DIR__) . '/config/db.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: projects.php');
    exit;
}

verifyCsrf();

$projectId = (int)($_POST['project_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM Projects WHERE project_id = ?");
$stmt->execute([$projectId]);
$project = $stmt->fetch();

if (!$project) {
    setFlash('error', 'Проект не найден.');
    header('Location: projects.php');
    exit;
}

if (!canManageProject($pdo, $project)) {
    setFlash('error', 'У вас нет прав для удаления этого проекта.');
    header('Location: projects.php');
    exit;
}

// Проверка наличия багов (запрет при bug > 0)
$bugCountStmt = $pdo->prepare("SELECT COUNT(*) FROM Bugs WHERE project_id = ?");
$bugCountStmt->execute([$projectId]);
$bugsCount = (int)$bugCountStmt->fetchColumn();

if ($bugsCount > 0) {
    setFlash('error', "Нельзя удалить проект «{$project['name']}»: в нём есть дефекты ({$bugsCount} шт.).");
    header('Location: projects.php');
    exit;
}

$del = $pdo->prepare("DELETE FROM Projects WHERE project_id = ?");
$del->execute([$projectId]);

setFlash('success', "Проект «{$project['name']}» успешно удалён.");
header('Location: projects.php');
exit;
