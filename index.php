<?php
session_start();
require 'config/database.php';
require 'vendor/autoload.php'; // For PHPMailer

use Postmark\PostmarkClient;


// Initialize variables
$error = '';
$reg_error = '';
$reg_success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
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

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
	$email = filter_input(INPUT_POST, 'reg_email', FILTER_SANITIZE_EMAIL);
	$password = $_POST['reg_password'];
	$confirm_password = $_POST['reg_confirm_password'];
	$role = $_POST['reg_role'];
	$full_name = $_POST['reg_name'];

	// Validate inputs
	if (!preg_match('/@liceo\.edu\.ph$/', $email)) {
		$reg_error = "Only liceo.edu.ph email addresses are allowed.";
	} elseif ($password !== $confirm_password) {
		$reg_error = "Passwords do not match.";
	} elseif (strlen($password) < 8) {
		$reg_error = "Password must be at least 8 characters long.";
	} else {
		// Check if email exists
		$stmt = $pdo->prepare("SELECT * FROM users WHERE liceo_email = ?");
		$stmt->execute([$email]);
		
		if ($stmt->fetch()) {
			$reg_error = "Email already registered.";
		} else {
			// Generate verification token
			$token = bin2hex(random_bytes(32));
			$expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
			$stmt = $pdo->prepare("INSERT INTO pending_registrations (liceo_email, password_hash, role, full_name, token, token_expiry) VALUES (?, ?, ?, ?, ?, ?)");
			$password_hash = md5($password); // Using same method as login
			
			if ($stmt->execute([$email, $password_hash, $role, $full_name, $token, $expiry])) {
				$client = new PostmarkClient("db57da22-d895-4db4-9785-b1675efaf6ca");
				$verificationLink = "http://{$_SERVER['HTTP_HOST']}/verify.php?token=$token";
				
				try {
					$sendResult = $client->sendEmail(
						"esanaga15688@liceo.edu.ph", // From
						"esanaga15688@liceo.edu.ph",            // To
						"Verify your QuickSlip account",         // Subject
						"<h2>QuickSlip Account Verification</h2>
						<p>Thank you for registering with QuickSlip!</p>
						<p>Please click the button below to verify your account:</p>
						<p><a href=\"$verificationLink\" style=\"display: inline-block; background-color: #850d16; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;\">Verify Account</a></p>
						<p>This link will expire in 1 hour.</p>
						<p>If you didn't request this, please ignore this email.</p>" // HTML body
					); 
					$reg_success = "Verification email sent! Please check your inbox to complete registration.";
				} catch (Exception $e) {
					$reg_error = "Message could not be sent. Mailer Error: {$sendResult}";
				}
			} else {
				$reg_error = "Database error. Please try again.";
			}
		}
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
		:root {
			--success-color: #2ecc71;
		}
		
		.error-message {
			color: #e74c3c;
			background: #fadbd8;
			padding: 10px;
			border-radius: 4px;
			margin-bottom: 15px;
			text-align: center;
			display: <?= $error ? 'block' : 'none' ?>;
		}
		
		.success-message {
			color: var(--success-color);
			background: #eafaf1;
			padding: 10px;
			border-radius: 4px;
			margin-bottom: 15px;
			text-align: center;
		}
		
		.register-form select {
			width: 100%;
			padding: 0.75rem;
			margin: 0.5rem 0;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 1rem;
			background: white;
		}
		
		.password-container {
			position: relative;
		}
		
		.toggle-password {
			position: absolute;
			right: 10px;
			top: 50%;
			transform: translateY(-50%);
			cursor: pointer;
			color: #850d16;
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
				<p class="register-link">Need an account? <a href="#" onclick="showRegisterForm()">Register here</a></p>
			</div>

			<div id="login-form" class="login-container hidden">
				<div class="error-message"><?= htmlspecialchars($error) ?></div>
				<h3 id="form-title">ADMIN LOGIN</h3>
				<form method="POST" action="index.php">
					<input type="hidden" name="login" value="1">
					<input type="hidden" name="role" id="hidden-role">
					<input type="email" name="email" placeholder="Email (@liceo.edu.ph)" required>
					<div class="password-container">
						<input type="password" name="password" id="login-password" placeholder="Password" required>
						<span class="toggle-password" onclick="togglePassword('login-password')">👁️</span>
					</div>
					<button type="submit" class="login-btn">Login</button>
					<button type="button" onclick="hideLoginForm()" class="back-btn">← Back</button>
				</form>
			</div>
			
			<div id="register-form" class="login-container hidden register-form">
				<?php if ($reg_error): ?>
					<div class="error-message"><?= htmlspecialchars($reg_error) ?></div>
				<?php endif; ?>
				
				<?php if ($reg_success): ?>
					<div class="success-message"><?= htmlspecialchars($reg_success) ?></div>
					<p class="register-link">
						<a href="#" onclick="showRoleSelection()">Back to Login</a>
					</p>
				<?php else: ?>
					<h3>CREATE ACCOUNT</h3>
					<form method="POST" action="index.php">
						<input type="hidden" name="register" value="1">
						<select name="reg_role" required>
							<option value="" disabled selected>Select Role</option>
							<option value="student">Student</option>
							<option value="faculty">Faculty</option>
						</select>
						<input type="text" name="reg_name" placeholder="Enter Full name (eg. Ever Sanaga)" pattern="^[A-Za-z ]{2,}$" 
        title="Please enter at least 2 characters using letters only." required>
						<input type="email" name="reg_email" placeholder="Email (@liceo.edu.ph)" required>
						<div class="password-container">
							<input type="password" name="reg_password" id="reg-password" placeholder="Password" required>
							<span class="toggle-password" onclick="togglePassword('reg-password')">👁️</span>
						</div>
						<div class="password-container">
							<input type="password" name="reg_confirm_password" id="reg-confirm-password" placeholder="Confirm Password" required>
							<span class="toggle-password" onclick="togglePassword('reg-confirm-password')">👁️</span>
						</div>
						<button type="submit" class="login-btn">Register</button>
						<button type="button" onclick="showRoleSelection()" class="back-btn">← Back</button>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<script>
		// Show login form
		function showLoginForm(role) {
			document.getElementById('role-selection').classList.add('hidden');
			document.getElementById('register-form').classList.add('hidden');
			document.getElementById('login-form').classList.remove('hidden');
			document.getElementById('form-title').textContent = `${role.toUpperCase()} LOGIN`;
			document.getElementById('hidden-role').value = role;
		}
		
		// Hide login form
		function hideLoginForm() {
			document.getElementById('login-form').classList.add('hidden');
			document.getElementById('register-form').classList.add('hidden');
			document.getElementById('role-selection').classList.remove('hidden');
			document.querySelector('.error-message').style.display = 'none';
		}
		
		// Show registration form
		function showRegisterForm() {
			document.getElementById('role-selection').classList.add('hidden');
			document.getElementById('login-form').classList.add('hidden');
			document.getElementById('register-form').classList.remove('hidden');
		}
		
		// Show role selection
		function showRoleSelection() {
			document.getElementById('login-form').classList.add('hidden');
			document.getElementById('register-form').classList.add('hidden');
			document.getElementById('role-selection').classList.remove('hidden');
		}
		
		// Toggle password visibility
		function togglePassword(fieldId) {
			const passwordField = document.getElementById(fieldId);
			if (passwordField.type === 'password') {
				passwordField.type = 'text';
			} else {
				passwordField.type = 'password';
			}
		}

		// Show error if exists on page load
		<?php if ($error): ?>
		document.addEventListener('DOMContentLoaded', function() {
			showLoginForm('<?= $_POST['role'] ?? 'admin' ?>');
		});
		<?php endif; ?>
		
		<?php if ($reg_error || $reg_success): ?>
		document.addEventListener('DOMContentLoaded', function() {
			showRegisterForm();
		});
		<?php endif; ?>
	</script>
</body>
</html>
