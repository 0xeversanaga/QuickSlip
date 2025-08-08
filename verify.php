<?php
session_start();
require 'config/database.php';

$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    
    $stmt = $pdo->prepare("SELECT * FROM pending_registrations WHERE token = ?");
    $stmt->execute([$token]);
    $pending = $stmt->fetch();
    
    if ($pending) {
        
        $currentTime = date('Y-m-d H:i:s');
        if ($currentTime > $pending['token_expiry']) {
            $message = "Verification link has expired. Please register again.";
        } else {
            try {
                $pdo->beginTransaction();
                $userCheck = $pdo->prepare("SELECT * FROM users WHERE liceo_email = ?");
                $userCheck->execute([$pending['liceo_email']]);
                if ($userCheck->fetch()) {
                    $message = "This email is already registered. Please <a href='index.php'>login</a>.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO users (liceo_email, password_hash, role, full_name) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $pending['liceo_email'], 
                        $pending['password_hash'], 
                        $pending['role'],   
                        $pending['full_name']
                    ]);
                    $stmt = $pdo->prepare("DELETE FROM pending_registrations WHERE id = ?");
                    $stmt->execute([$pending['id']]);
                    
                    $pdo->commit();
                    $message = "Account verified successfully! You can now <a href='index.php'>login</a>.";
                }
            } catch (PDOException $e) {
                $pdo->rollBack();
                $message = "Verification failed. Please try again. Error: " . $e->getMessage();
            }
        }
    } else {
        $message = "Invalid token. Please try registering again.";
    }
} else {
    $message = "No token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account - QuickSlip</title> 
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            height: 100vh;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: "";
            position: fixed; 
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('images/rodelsa-hall-high.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(3px); 
            transform: scale(1.05); 
            z-index: -2; 
        }

        body::after {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        .container {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .right-section {
            background: rgba(255, 255, 255, 0.85); 
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .success-message {
            color: #2ecc71;
            background: #eafaf1;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .error-message {
            color: #e74c3c;
            background: #fadbd8;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .register-link a {
            color: #850d16;
            text-decoration: none;
        }
</style>

</head>
<body>
    <div class="container">
        <div class="right-section">
            <div class="login-container">
                <h3>ACCOUNT VERIFICATION</h3>
                <?php if (strpos($message, 'successfully') !== false): ?>
                    <div class="success-message"><?= $message ?></div>
                <?php else: ?>
                    <div class="error-message"><?= $message ?></div>
                <?php endif; ?>
                <p class="register-link">
                    <a href="index.php">Back to Login</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>