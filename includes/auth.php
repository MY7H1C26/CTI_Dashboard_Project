<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function login_user($email, $password)
{
    global $pdo;

    $stmt = $pdo->prepare('
        SELECT users.*, roles.name AS role_name
        FROM users
        JOIN roles ON roles.id = users.role_id
        WHERE users.email = ? AND users.status = "Active"
        LIMIT 1
    ');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'role_name' => $user['role_name'],
        ];
        return true;
    }

    return false;
}

function register_user($name, $email, $password)
{
    global $pdo;

    $role = $pdo->prepare('SELECT id FROM roles WHERE name = ? LIMIT 1');
    $role->execute(['User / SOC Analyst']);
    $roleId = $role->fetchColumn();

    $stmt = $pdo->prepare('INSERT INTO users (role_id, name, email, password, phone, organization) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $roleId,
        $name,
        $email,
        password_hash($password, PASSWORD_DEFAULT),
        $_POST['phone'] ?? null,
        $_POST['organization'] ?? null,
    ]);
}
