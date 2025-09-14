<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechNova - Latest Technology News</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <a href="index.php">TechNova</a>
                </div>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="category.php?cat=AI" class="nav-link">AI</a>
                    </li>
                    <li class="nav-item">
                        <a href="category.php?cat=Gadgets" class="nav-link">Gadgets</a>
                    </li>
                    <li class="nav-item">
                        <a href="category.php?cat=Programming" class="nav-link">Programming</a>
                    </li>
                    <li class="nav-item">
                        <a href="category.php?cat=Startups" class="nav-link">Startups</a>
                    </li>
                    <li class="nav-item">
                        <a href="category.php?cat=Cybersecurity" class="nav-link">Cybersecurity</a>
                    </li>
                    <li class="nav-item">
                        <a href="about.php" class="nav-link">About</a>
                    </li>
                    <li class="nav-item">
                        <a href="contact.php" class="nav-link">Contact</a>
                    </li>
                    <?php if (isset($_SESSION['admin_logged_in'])): ?>
                    <li class="nav-item">
                        <a href="../admin/dashboard.php" class="nav-link">Dashboard</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="nav-toggle">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
                <button id="darkModeToggle" class="dark-mode-toggle">🌙</button>
            </div>
        </nav>
    </header>
    <main>