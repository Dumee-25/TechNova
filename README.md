TechNova
Live Site: technova.sdkaluwitharana.lk

A dynamic news/contact/admin site built with PHP. Features an admin interface for managing news, messages, subscribers, dark mode etc.

🚀 Features

Public news articles
Admin dashboard with stats: number of news, subscribers, messages (excluding deleted)
Contact form for users to send messages
Soft-delete & “replied” flags for messages
Unreplied messages count badge in sidebar (for super_admin)
Role-based access control (admin vs super_admin)
Dark-mode toggle (user preference via localStorage)
Responsive UI (sidebar, tables, cards etc.)
Sanitization of user input / HTML escaping (htmlspecialchars) for titles, emails, message previews

🔧 Tech Stack
Backend: PHP (with PDO)
Frontend: HTML, CSS (custom + possibly Bootstrap), FontAwesome, Google Fonts
Database: MySQL or a compatible RDBMS
Client-side scripting for dark mode, UI toggles
Server side: session management, user role logic

🔐 Security & Best Practices
Always hash passwords using password_hash / verify with password_verify
Sanitize all user input (already doing htmlspecialchars in views)
Use sessions securely (set proper cookie-flags)
Ensure the is_deleted + replied logic prevents unwanted exposure of deleted/replied messages
Avoid commiting sensitive data (DB credentials) in config.php → use environment variables or a separate, ignored config file for production
