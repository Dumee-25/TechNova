<!DOCTYPE html>
<html lang="en">
<body>

  <h1>TechNova</h1>
  <p><strong>Live Site:</strong> <a href="https://technova.sdkaluwitharana.lk" target="_blank">technova.sdkaluwitharana.lk</a></p>

  <p>A dynamic news/contact/admin site built with PHP. Features an admin interface for managing news, messages, subscribers, dark mode etc.</p>

  <h2>🚀 Features</h2>
  <ul>
    <li>Public news articles</li>
    <li>Admin dashboard with stats: number of news, subscribers, messages (excluding deleted)</li>
    <li>Contact form for users to send messages</li>
    <li>Soft-delete & “replied” flags for messages</li>
    <li>Unreplied messages count badge in sidebar (for <code>super_admin</code>)</li>
    <li>Role-based access control (<code>admin</code> vs <code>super_admin</code>)</li>
    <li>Dark-mode toggle (user preference via localStorage)</li>
    <li>Responsive UI (sidebar, tables, cards etc.)</li>
    <li>Sanitization of user input / HTML escaping (<code>htmlspecialchars</code>) for titles, emails, message previews</li>
  </ul>

  <h2>🔧 Tech Stack</h2>
  <ul>
    <li>Backend: PHP (with PDO)</li>
    <li>Frontend: HTML, CSS (custom + possibly Bootstrap), FontAwesome, Google Fonts</li>
    <li>Database: MySQL or a compatible RDBMS</li>
    <li>Client-side scripting for dark mode, UI toggles</li>
    <li>Server side: session management, user role logic</li>
  </ul>

  <h2>🔐 Security & Best Practices</h2>
  <ul>
    <li>Always hash passwords using <code>password_hash</code> / verify with <code>password_verify</code></li>
    <li>Sanitize all user input (already using <code>htmlspecialchars</code> in views)</li>
    <li>Use sessions securely (set proper cookie-flags)</li>
    <li>Ensure the <code>is_deleted</code> + <code>replied</code> logic prevents unwanted exposure of deleted/replied messages</li>
    <li>Avoid committing sensitive data (DB credentials) in <code>config.php</code> → use environment variables or a separate, ignored config file for production</li>
  </ul>

</body>
</html>
