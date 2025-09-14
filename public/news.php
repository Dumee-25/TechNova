<?php
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$article_id = $_GET['id'];

// Get the article
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: index.php');
    exit();
}

// Get related articles (same category, excluding current)
$stmt = $pdo->prepare("SELECT * FROM news WHERE category = ? AND id != ? ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$article['category'], $article_id]);
$related_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <article class="news-article">
        <div class="article-header">
            <h1><?php echo htmlspecialchars($article['title']); ?></h1>
            <div class="article-meta">
                <span>By <?php echo htmlspecialchars($article['author']); ?></span> • 
                <span><?php echo date('F j, Y', strtotime($article['created_at'])); ?></span> • 
                <span><?php echo $article['category']; ?></span>
            </div>
        </div>
        
        <img src="uploads/<?php echo $article['image'] ?? 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="article-image">
        
        <div class="article-content">
            <?php echo nl2br(htmlspecialchars($article['content'])); ?>
        </div>
    </article>

    <section class="related-articles">
        <h2>Related Articles</h2>
        <div class="news-grid">
            <?php foreach ($related_articles as $related): ?>
            <div class="news-card">
                <img src="uploads/<?php echo $related['image'] ?? 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                <div class="news-card-content">
                    <h3><?php echo htmlspecialchars($related['title']); ?></h3>
                    <div class="news-meta">
                        <span>By <?php echo htmlspecialchars($related['author']); ?></span> • 
                        <span><?php echo date('M j, Y', strtotime($related['created_at'])); ?></span>
                    </div>
                    <a href="news.php?id=<?php echo $related['id']; ?>" class="btn">Read More</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="comments">
        <h2>Comments</h2>
        <!-- Disqus Comment System -->
        <div id="disqus_thread"></div>
        <script>
            var disqus_config = function () {
                this.page.url = '<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>';
                this.page.identifier = 'article_<?php echo $article_id; ?>';
            };
            
            (function() {
                var d = document, s = d.createElement('script');
                s.src = 'https://technews-website.disqus.com/embed.js';
                s.setAttribute('data-timestamp', +new Date());
                (d.head || d.body).appendChild(s);
            })();
        </script>
        <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    </section>
</div>

<?php include 'includes/footer.php'; ?>