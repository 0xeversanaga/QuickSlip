function showLoginForm(role) {
    document.getElementById('role-selection').classList.add('hidden');
    document.getElementById('login-form').classList.remove('hidden');
    document.getElementById('form-title').textContent = `${role.toUpperCase()} LOGIN`;
}

function hideLoginForm() {
    document.getElementById('login-form').classList.add('hidden');
    document.getElementById('role-selection').classList.remove('hidden');
}

// Optional: Form submission handling
document.querySelector('#login-form form').addEventListener('submit', function(e) {
    e.preventDefault();
    const role = document.getElementById('form-title').textContent.replace(' LOGIN', '');
    alert(`Logging in as ${role}...`);
    // Replace with actual authentication logic
});
