<?php
session_start();
require 'config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE liceo_email = ? AND role = ?");
    $stmt->execute([$email, $role]);
    $user = $stmt->fetch();


    if ($password && $user["password_hash"]) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: {$role}/dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials";
        echo "<script>showLoginForm('{$role}');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickSlip Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error-message {
            color: #e74c3c;
            background: #fadbd8;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
            display: <?= $error ? 'block' : 'none' ?>;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="branding">
                <h1>QuickSlip</h1>
                <h2>Automated Offense Slip System for PDSA in Liceo De Cagayan University</h2>
                <br><br>
                <p>The research aims to develop "QuickSlip" an automated web and mobile application system, to digitize the distribution of offense slips and community service slips at Liceo De Cagayan University, replacing manual processes that cause delays, overcrowding, and academic disruptions.</p>
                <br><br>
                <p>The study was initiated to resolve inefficiencies in the current Prefect of Discipline and Student Affairs (PDSA) office workflow, where students waste class time waiting for physical slips, faculty face administrative burdens, and the manual system hinders transparency and timely disciplinary actions.</p>
            </div>
            <div class="image-overlay"></div>
            <img src="images/rodelsa-hall.jpg" alt="Liceo Campus" class="campus-image">
        </div>

        <div class="right-section">
            <div id="role-selection" class="login-container">
                <h3>LOGIN AS</h3>
                <button onclick="showLoginForm('admin')">ADMIN</button>
                <button onclick="showLoginForm('faculty')">FACULTY</button>
                <button onclick="showLoginForm('student')">STUDENT</button>
                <p class="register-link">Need an account? <a href="#">Register here</a></p>
            </div>

            <div id="login-form" class="login-container hidden">
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <h3 id="form-title">ADMIN LOGIN</h3>
                <form method="POST" action="index.php">
                    <input type="hidden" name="role" id="hidden-role">
                    <input type="email" name="email" placeholder="Email (@liceo.edu.ph)" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" class="login-btn">Login</button>
                    <button type="button" onclick="hideLoginForm()" class="back-btn">← Back</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modified from your script.js
        function showLoginForm(role) {
            document.getElementById('role-selection').classList.add('hidden');
            document.getElementById('login-form').classList.remove('hidden');
            document.getElementById('form-title').textContent = `${role.toUpperCase()} LOGIN`;
            document.getElementById('hidden-role').value = role;
        }

        function hideLoginForm() {
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('role-selection').classList.remove('hidden');
            document.querySelector('.error-message').style.display = 'none';
        }

        // Show error if exists on page load
        <?php if ($error): ?>
        document.addEventListener('DOMContentLoaded', function() {
            showLoginForm('<?= $_POST['role'] ?? 'admin' ?>');
        });
        <?php endif; ?>
    </script>
</body>
</html>
