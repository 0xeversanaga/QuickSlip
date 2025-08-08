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
	} else {composer require wildbit/postmark-php
		passwordField.type = 'password';
	}
}
