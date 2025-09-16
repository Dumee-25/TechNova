<?php
session_start(); 


require_once 'includes/config.php';


if (isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$info = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE LOWER(username) = LOWER(?) LIMIT 1");
            $stmt->execute([strtolower($username)]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin) {
                $hash = trim($admin['password']); 
                if (password_verify($password, $hash)) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_role'] = $admin['role'];

                    header('Location: dashboard.php');
                    exit();
                } else {
                    $error = 'Invalid username or password.';
                }
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - TechNews</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #1e88e5;
    --secondary: #37474f;
    --accent: #ff6f00;
    --background: #f9f9f9;
    --text: #212121;
    --card-bg: #ffffff;
    --border: #e0e0e0;
}
[data-theme="dark"] {
    --primary: #90caf9;
    --secondary: #b0bec5;
    --accent: #ffb300;
    --background: #121212;
    --text: #eeeeee;
    --card-bg: #1e1e1e;
    --border: #333333;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Roboto', sans-serif;
    background-color: var(--background);
    color: var(--text);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    transition: background-color 0.3s, color 0.3s;
}
.login-container {
    background-color: var(--card-bg);
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
}
.login-header { text-align: center; margin-bottom: 2rem; }
.login-header h1 { font-family: 'Montserrat', sans-serif; color: var(--primary); margin-bottom: 0.5rem; }
.form-group { margin-bottom: 1.5rem; }
.form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
.form-group input { width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 4px; background-color: var(--background); color: var(--text); }
.btn { display: inline-block; width: 100%; padding: 0.75rem; background-color: var(--primary); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; transition: background-color 0.3s; }
.btn:hover { background-color: var(--accent); }
.redirect { display: inline-block; width: 100%; padding: 0.75rem; background-color: var(--secondary); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; margin-top: 0.5rem; transition: background-color 0.3s; }
.redirect:hover { background-color: var(--accent); }
.error { color: #e53935; margin-bottom: 1rem; text-align: center; }
.info { color: #43a047; margin-bottom: 1rem; text-align: center; }
.theme-toggle { position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; }
</style>
</head>
<body>
<button id="darkModeToggle" class="theme-toggle">🌙</button>
<div class="login-container">
    <div class="login-header">
        <h1>TechNews Admin</h1>
        <p>Sign in to your account</p>
    </div>
    <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($info): ?><div class="info"><?php echo $info; ?></div><?php endif; ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Sign In</button>
        <button type="button" class="redirect" onclick="window.location.href='../public/index.php'">Back to Site</button>
    </form>
</div>
<script>
const darkModeToggle = document.getElementById('darkModeToggle');
const body = document.body;
const savedTheme = localStorage.getItem('theme');
if (savedTheme) { body.setAttribute('data-theme', savedTheme); updateToggleIcon(); }
darkModeToggle.addEventListener('click', () => {
    if(body.getAttribute('data-theme') === 'dark') { body.removeAttribute('data-theme'); localStorage.setItem('theme','light'); }
    else { body.setAttribute('data-theme','dark'); localStorage.setItem('theme','dark'); }
    updateToggleIcon();
});
function updateToggleIcon() { darkModeToggle.textContent = (body.getAttribute('data-theme') === 'dark') ? '☀️' : '🌙'; }
</script>
</body>
</html>
