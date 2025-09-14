<?php
include 'includes/db.php';
include 'includes/header.php';

// Handle newsletter subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
            $stmt->execute([$email]);
            $newsletter_success = "Thank you for subscribing to our newsletter!";
        } catch (PDOException $e) {
            $newsletter_error = "This email is already subscribed.";
        }
    } else {
        $newsletter_error = "Please enter a valid email address.";
    }
}

// Get featured articles (top 3 most recent)
$stmt = $pdo->prepare("SELECT * FROM news ORDER BY created_at DESC LIMIT 3");
$stmt->execute();
$featured_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get latest articles (next 10 most recent)
$stmt = $pdo->prepare("SELECT * FROM news ORDER BY created_at DESC LIMIT 10 OFFSET 3");
$stmt->execute();
$latest_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <section class="hero">
        <div class="carousel">
            <div class="carousel-inner">
                <?php foreach ($featured_articles as $article): ?>
                <div class="carousel-item">
                    <img src="uploads/<?php echo $article['image'] ?? 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                    <div class="carousel-caption">
                        <h2><?php echo htmlspecialchars($article['title']); ?></h2>
                        <a href="news.php?id=<?php echo $article['id']; ?>" class="btn">Read More</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <div class="content-wrapper">
        <div class="main-content">
            <h2>Latest News</h2>
            <div class="news-grid">
                <?php foreach ($latest_articles as $article): ?>
                <div class="news-card">
                    <img src="uploads/<?php echo $article['image'] ?? 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                    <div class="news-card-content">
                        <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                        <div class="news-meta">
                            <span>By <?php echo htmlspecialchars($article['author']); ?></span> • 
                            <span><?php echo date('M j, Y', strtotime($article['created_at'])); ?></span>
                        </div>
                        <p><?php echo substr(strip_tags($article['content']), 0, 150) . '...'; ?></p>
                        <a href="news.php?id=<?php echo $article['id']; ?>" class="btn">Read More</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <aside class="sidebar">
            <h3>Categories</h3>
            <ul class="category-list">
                <li><a href="category.php?cat=AI">Artificial Intelligence</a></li>
                <li><a href="category.php?cat=Gadgets">Gadgets</a></li>
                <li><a href="category.php?cat=Programming">Programming</a></li>
                <li><a href="category.php?cat=Startups">Startups</a></li>
                <li><a href="category.php?cat=Cybersecurity">Cybersecurity</a></li>
            </ul>

            <h3>Newsletter</h3>
            <p>Subscribe to our newsletter for the latest tech news.</p>
            <form action="index.php" method="POST" class="newsletter-form">
                <input type="email" name="email" placeholder="Your email address" required>
                <button type="submit">Subscribe</button>
            </form>
            <?php if (isset($newsletter_success)): ?>
                <p style="color: green; margin-top: 10px;"><?php echo $newsletter_success; ?></p>
            <?php endif; ?>
            <?php if (isset($newsletter_error)): ?>
                <p style="color: red; margin-top: 10px;"><?php echo $newsletter_error; ?></p>
            <?php endif; ?>
        </aside>
    </div>
</div>

<?php include 'includes/footer.php'; ?>