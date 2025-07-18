<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Login - QuickSlip</title>
	<link rel="stylesheet" href="../style.css">
</head>
<body>
	<div class="container">
		<div class="login-box">
			<button class="back-btn" onclick="location.href='../index.html'">⮜ Back</button>
			<h2>Admin Login</h2>
			<form action="authenticate.php" method="POST">
				<input type="text" name="admin_id" placeholder="Admin ID" required class="input-field">
				<input type="password" name="password" placeholder="Password" required class="input-field">
				<button type="submit" class="login-btn">Log In</button>
			</form>
			<p class="link-text"><a href="#">Forgot Password?</a></p>
		</div>
	</div>
</body>
</html>
