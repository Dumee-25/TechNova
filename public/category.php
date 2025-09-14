<?php
include 'includes/db.php';
include 'includes/header.php';

$categories = ['AI', 'Gadgets', 'Programming', 'Startups', 'Cybersecurity'];
$category = isset($_GET['cat']) ? $_GET['cat'] : '';

if (!in_array($category, $categories)) {
    header('Location: index.php');
    exit();
}

// Pagination setup
$articles_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $articles_per_page;

// Get total articles for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) FROM news WHERE category = ?");
$stmt->execute([$category]);
$total_articles = $stmt->fetchColumn();
$total_pages = ceil($total_articles / $articles_per_page);

// Get articles for current page
$stmt = $pdo->prepare("SELECT * FROM news WHERE category = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $category, PDO::PARAM_STR);
$stmt->bindValue(2, $articles_per_page, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="page-header">
        <h1><?php echo $category; ?> News</h1>
        <p>Latest news and updates about <?php echo $category; ?></p>
    </div>

    <div class="content-wrapper">
        <div class="main-content">
            <?php if (count($articles) > 0): ?>
            <div class="news-grid">
                <?php foreach ($articles as $article): ?>
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

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="category.php?cat=<?php echo $category; ?>&page=<?php echo $page - 1; ?>" class="btn">Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="category.php?cat=<?php echo $category; ?>&page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="category.php?cat=<?php echo $category; ?>&page=<?php echo $page + 1; ?>" class="btn">Next</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <p>No articles found in this category.</p>
            <?php endif; ?>
        </div>

        <aside class="sidebar">
            <h3>Categories</h3>
            <ul class="category-list">
                <?php foreach ($categories as $cat): ?>
                <li><a href="category.php?cat=<?php echo $cat; ?>" class="<?php echo $cat == $category ? 'active' : ''; ?>"><?php echo $cat; ?></a></li>
                <?php endforeach; ?>
            </ul>

            <h3>Newsletter</h3>
            <p>Subscribe to our newsletter for the latest tech news.</p>
            <form action="index.php" method="POST" class="newsletter-form">
                <input type="email" name="email" placeholder="Your email address" required>
                <button type="submit">Subscribe</button>
            </form>
        </aside>
    </div>
</div>

<?php include 'includes/footer.php'; ?>