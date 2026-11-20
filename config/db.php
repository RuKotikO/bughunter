<?php
ob_start();
session_start();

$dbFile = dirname(__DIR__) . '/database/bughunter.db';

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("PRAGMA foreign_keys = ON;");
} catch (PDOException $e) {
    die("Ошибка БД: " . $e->getMessage());
}

// Защита закрытых страниц
function requireAuth(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

// Генерация CSRF-токена
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Проверка CSRF-токена при POST-запросах
function verifyCsrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Ошибка безопасности: недействительный CSRF-токен.');
    }
}

// Проверка прав доступа к проекту (владелец или Администратор)
function canManageProject(PDO $pdo, array $project): bool {
    $userId = (int)($_SESSION['user_id'] ?? 0);
    if ((int)$project['owner_id'] === $userId) {
        return true;
    }
    $stmt = $pdo->prepare("SELECT role FROM Users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $role = $stmt->fetchColumn();
    return $role === 'Администратор';
}

// Флеш-сообщения (успех / ошибка)
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
