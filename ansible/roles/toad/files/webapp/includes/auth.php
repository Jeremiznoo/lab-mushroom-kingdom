<?php
define('MKB_DB_PATH', '/mnt/data/missionboard-data/users.db');

function mkb_db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . MKB_DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $pdo;
}

function mkb_is_authenticated(): bool
{
    return !empty($_SESSION['authenticated']);
}

function mkb_attempt_login(string $username, string $password): bool
{
    try {
        $stmt = mkb_db()->prepare('SELECT password_hash FROM users WHERE username = :u');
        $stmt->execute(['u' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return false;
    }
    if ($row && password_verify($password, $row['password_hash'])) {
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $username;
        return true;
    }
    return false;
}

function mkb_logout(): void
{
    unset($_SESSION['authenticated'], $_SESSION['username']);
}
