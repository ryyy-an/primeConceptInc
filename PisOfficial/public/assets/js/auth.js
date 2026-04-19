/**
 * Authentication Logic (Login Page)
 */

/**
 * Toggle password visibility
 */
function togglePassword() {
    const passwordInput = document.getElementById('password');
    if (!passwordInput) return;
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
}

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            if (!btn || btn.disabled) return; // Prevent double click

            e.preventDefault();
            
            // Start Loading state
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed', 'bg-gray-800');
            btn.innerHTML = `
                <div class="pure-spinner"></div>
                <span class="tracking-widest uppercase text-[11px]">Logging in...</span>
            `;

            // Artificial delay for UX
            setTimeout(() => {
                this.submit();
            }, 1200);
        });
    }
});

// Export globally for inline onclick if needed (though we'd prefer event listeners)
window.togglePassword = togglePassword;
