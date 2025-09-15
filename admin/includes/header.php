<?php
// Include configuration
require_once 'config.php';

// Set page title if not already set
if (!isset($page_title)) {
    $page_title = 'Admin Dashboard';
}

// Get the base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];

// Extract the base path
$base_path = str_replace('/admin/', '', $uri);
$base_path = preg_replace('/\/([^\/]*\.php).*/', '', $base_path);
$site_url = $protocol . '://' . $host . $base_path;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - TechNova Admin</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    :root {
    --primary: #1e88e5;
    --secondary: #546e7a;
    --accent: #ff6f00;
    --background: #f9f9f9;
    --text: #212121;
    --card-bg: #ffffff;
    --border: #e0e0e0;
    --sidebar-width: 250px;
}

[data-theme="dark"] {
    --primary: #90caf9;
    --secondary: #cfd8dc;
    --accent: #ffb300;
    --background: #121212;
    --text: #eeeeee;
    --card-bg: #1e1e1e;
    --border: #333333;
}

body {
    font-family: 'Roboto', sans-serif;
    background-color: var(--background);
    color: var(--text);
    line-height: 1.6;
    transition: background-color 0.3s, color 0.3s;
}

/* Layout */
.admin-container {
    display: flex;
    min-height: 100vh;
}

/* Sidebar */
.sidebar {
    width: var(--sidebar-width);
    background-color: var(--card-bg);
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
    position: fixed;
    height: 100vh;
    overflow-y: auto;
    z-index: 1000;
    transition: transform 0.3s ease-in-out;
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border);
    text-align: center;
}

.sidebar-header h2 {
    font-family: 'Montserrat', sans-serif;
    color: var(--primary);
    font-size: 1.4rem;
}

.sidebar-menu {
    list-style: none;
    padding: 1rem 0;
}

.sidebar-menu li {
    margin-bottom: 0.5rem;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.25rem;
    color: var(--text);
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s;
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    background-color: var(--primary);
    color: #fff;
    transform: translateX(5px);
}

.sidebar-menu i {
    width: 20px;
    margin-right: 0.75rem;
}

/* Main Content */
.main-content {
    flex: 1;
    margin-left: var(--sidebar-width);
    padding: 2rem;
    transition: margin-left 0.3s;
}

/* Header */
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    margin-bottom: 2rem;
    border-bottom: 1px solid var(--border);
}

.admin-header h1 {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.6rem;
}

/* Header Actions */
.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.dark-mode-toggle {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    transition: transform 0.2s;
}
.dark-mode-toggle:hover {
    transform: rotate(20deg);
}

.user-menu {
    position: relative;
}

.user-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: none;
    border: 1px solid var(--border);
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    color: var(--text);
    transition: all 0.3s;
}
.user-btn:hover {
    background-color: rgba(30, 136, 229, 0.1);
    border-color: var(--primary);
}

.user-dropdown {
    position: absolute;
    top: 110%;
    right: 0;
    background-color: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 0.5rem;
    min-width: 160px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: none;
}
.user-dropdown a {
    display: block;
    padding: 0.6rem;
    color: var(--text);
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s;
}
.user-dropdown a:hover {
    background-color: var(--primary);
    color: #fff;
}
.user-menu:hover .user-dropdown {
    display: block;
}

/* Cards */
.card {
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-3px);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border);
}
.card-header h2 {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.2rem;
}

/* Stats */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}
.stat-card {
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    padding: 1.5rem;
    text-align: center;
}
.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary);
}
.stat-label {
    color: var(--secondary);
}

/* Tables */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
}
th, td {
    padding: 0.85rem;
    text-align: left;
    border-bottom: 1px solid var(--border);
}
th {
    background-color: rgba(30, 136, 229, 0.08);
    font-weight: 600;
}
tr:hover {
    background-color: rgba(0, 0, 0, 0.03);
}

/* Forms */
.form-group {
    margin-bottom: 1.5rem;
}
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}
.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border);
    border-radius: 6px;
    background-color: var(--background);
    color: var(--text);
    transition: border 0.3s, box-shadow 0.3s;
}
.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 5px rgba(30, 136, 229, 0.4);
}
textarea {
    min-height: 150px;
    resize: vertical;
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 0.65rem 1.25rem;
    font-size: 0.95rem;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: all 0.3s;
}
.btn:hover {
    transform: translateY(-2px);
}
.btn-primary {
    background-color: var(--primary);
    color: #fff;
}
.btn-primary:hover {
    background-color: var(--accent);
}
.btn-danger {
    background-color: #e53935;
    color: #fff;
}
.btn-danger:hover {
    background-color: #c62828;
}
.btn-success {
    background-color: #43a047;
    color: #fff;
}
.btn-success:hover {
    background-color: #388e3c;
}

/* Alerts */
.alert {
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
    font-weight: 500;
}
.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
/* Dark Mode Fixes */
[data-theme="dark"] body {
    color: var(--text);
}

[data-theme="dark"] .sidebar-menu a {
    color: var(--text);
}
[data-theme="dark"] .sidebar-menu a.active,
[data-theme="dark"] .sidebar-menu a:hover {
    background-color: var(--primary);
    color: #fff;
}

[data-theme="dark"] th {
    background-color: rgba(144, 202, 249, 0.15);
    color: var(--text);
}

[data-theme="dark"] .stat-label {
    color: var(--secondary);
}

[data-theme="dark"] .user-btn {
    color: var(--text);
    border-color: var(--border);
}

[data-theme="dark"] .user-dropdown {
    background-color: var(--card-bg);
    border-color: var(--border);
}
[data-theme="dark"] .user-dropdown a {
    color: var(--text);
}
[data-theme="dark"] .user-dropdown a:hover {
    background-color: var(--primary);
    color: #fff;
}

[data-theme="dark"] .card,
[data-theme="dark"] .stat-card {
    background-color: var(--card-bg);
    color: var(--text);
}
/* Ensure dropdown stays visible and clickable */
.user-menu {
    position: relative;
    display: inline-block;
    z-index: 1000; /* keeps dropdown on top */
}

.user-btn {
    cursor: pointer;
    position: relative;
    z-index: 1001;
}

.user-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background-color: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    display: none; /* hidden by default */
    min-width: 180px;
    z-index: 1002; /* dropdown above everything */
}

/* Show dropdown on hover */
.user-menu:hover .user-dropdown,
.user-menu:focus-within .user-dropdown {
    display: block;
}


/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        width: 80%;
    }
    .sidebar.active {
        transform: translateX(0);
    }
    .main-content {
        margin-left: 0;
    }
}

    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>TechNova Admin</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage-news.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage-news.php' ? 'active' : ''; ?>"><i class="fas fa-newspaper"></i> Manage News</a></li>
                <li><a href="add-news.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add-news.php' ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> Add News</a></li>
                <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 'admin'): ?>
                <li><a href="manage-users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage-users.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Manage Users</a></li>
                <li><a href="message-view.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'message-view.php' ? 'active' : ''; ?>"><i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="newsletter-subscribe.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'newsletter-subscribe.php' ? 'active' : ''; ?>"><i class="fas fa-user-plus"></i> Subscribers</a></li>
                <?php endif; ?>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <header class="admin-header">
                <h1><?php echo $page_title; ?></h1>
                <div class="header-actions">
                    <button id="darkModeToggle" class="dark-mode-toggle">🌙</button>
                    <div class="user-menu">
                        <button class="user-btn">
                            <i class="fas fa-user"></i>
                            <?php echo $_SESSION['admin_username']; ?>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown">
                            <a href="<?php echo $site_url; ?>/tech-news-website/public/index.php">View Site</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            </header>