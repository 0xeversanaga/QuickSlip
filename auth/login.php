<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE liceo_email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    echo $user['password_hash'];
    echo md5($password);

    if (md5($password) && $user['password_hash']) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        switch ($user['role']) {
            case 'admin':
                header('Location: /admin/dashboard.php');
                break;
            case 'faculty':
                header('Location: /faculty/report.php');
                break;
            case 'student':
                header('Location: /student/dashboard.php');
                break;
        }
        exit();
    } else {
        $error = "Invalid credentials";
    }
}
?>
