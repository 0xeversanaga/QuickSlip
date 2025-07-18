<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Faculty Login - QuickSlip</title>
	<link rel="stylesheet" href="../style.css">
</head>
<body>
	<div class="container">
		<div class="login-box">
			<button class="back-btn" onclick="location.href='../index.html'">< Back</button>
			<h2>Faculty Login</h2>
			<form action="authenticate.php" method="POST">
				<label for="username">Username:</label>
				<input type="text" name="faculty_id" placeholder="Enter Faculty ID" required class="input-field">
				<label for="password">Password:</label>
				<input type="password" name="password" placeholder="Enter Password" required class="input-field">
				<button type="submit" class="login-btn">Log In</button>
			</form>
			<p class="link-text"><a href="#">Forgot Password?</a></p>
		</div>
	</div>
</body>
</html>
