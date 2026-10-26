<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header('Location: dashboard.php');
        } elseif ($user['role'] === 'patroller') {
            header('Location: patroller_dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        header('Location: index.php?error=Invalid%20email%20or%20password');
    }
}
?>