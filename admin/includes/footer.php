        </div>
    </div>

    <script>
        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;

        // Check for saved theme preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            body.setAttribute('data-theme', savedTheme);
            updateToggleIcon();
        }

        darkModeToggle.addEventListener('click', () => {
            if (body.getAttribute('data-theme') === 'dark') {
                body.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
            } else {
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            }
            updateToggleIcon();
        });

        function updateToggleIcon() {
            if (body.getAttribute('data-theme') === 'dark') {
                darkModeToggle.textContent = '☀️';
            } else {
                darkModeToggle.textContent = '🌙';
            }
        }
    </script>
</body>
</html>