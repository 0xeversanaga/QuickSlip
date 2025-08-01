function showLoginForm(role) {
    document.getElementById('role-selection').classList.add('hidden');
    document.getElementById('login-form').classList.remove('hidden');
    document.getElementById('form-title').textContent = `${role.toUpperCase()} LOGIN`;
}

function hideLoginForm() {
    document.getElementById('login-form').classList.add('hidden');
    document.getElementById('role-selection').classList.remove('hidden');
}

