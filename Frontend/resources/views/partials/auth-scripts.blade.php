<script>
    function initializeAuth() {
        const userDropdown = document.getElementById('user-dropdown');
        const loginBtn = document.getElementById('login-btn');
        const userName = document.getElementById('user-name');
        const logoutBtn = document.getElementById('logout-btn');

        if (typeof AuthManager === 'undefined') {
            console.error('AuthManager no está disponible');
            if (loginBtn) loginBtn.style.display = 'block';
            return;
        }

        if (AuthManager.isAuthenticated()) {
            const userData = AuthManager.getUserData();
            if (userData && userData.nombre) {
                userName.textContent = userData.nombre;
                userDropdown.style.display = 'block';
                loginBtn.style.display = 'none';
            } else {
                userDropdown.style.display = 'none';
                loginBtn.style.display = 'block';
            }
        } else {
            userDropdown.style.display = 'none';
            loginBtn.style.display = 'block';
        }

        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                AuthManager.clearAuth();
                window.location.href = '{{ route('home') }}';
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initializeAuth();
    });
</script>
