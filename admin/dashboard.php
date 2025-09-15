<?php
$page_title = "Dashboard";
include 'includes/config.php';
include 'includes/header.php';


// Get statistics
$news_count = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$subscribers_count = $pdo->query("SELECT COUNT(*) FROM newsletter_subscribers")->fetchColumn();
$messages_count = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$admins_count = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();

// Get latest news articles
$latest_news = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get recent messages
$recent_messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?php echo $news_count; ?></div>
        <div class="stat-label">News Articles</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $subscribers_count; ?></div>
        <div class="stat-label">Subscribers</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $messages_count; ?></div>
        <div class="stat-label">Messages</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $admins_count; ?></div>
        <div class="stat-label">Admins</div>
    </div>
</div>

<div class="content-grid">
    <div class="card">
        <div class="card-header">
            <h2>Latest News Articles</h2>
            <a href="manage-news.php" class="btn btn-sm">View All</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latest_news as $article): ?>
                <tr>
                    <td><?php echo htmlspecialchars($article['title']); ?></td>
                    <td><?php echo $article['category']; ?></td>
                    <td><?php echo date('M j, Y', strtotime($article['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Recent Messages</h2>
            <a href="message-view.php" class="btn btn-sm">View All</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_messages as $message): ?>
                <tr>
                    <td><?php echo htmlspecialchars($message['name']); ?></td>
                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                    <td><?php echo date('M j, Y', strtotime($message['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars(substr($message['message'], 0, 50)) . (strlen($message['message']) > 50 ? '...' : ''); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>