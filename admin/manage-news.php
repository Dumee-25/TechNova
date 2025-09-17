<?php
$page_title = "Manage News";
include 'includes/config.php';
include 'includes/header.php';

// Handle delete action
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image path to delete file
    $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();
    
    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    if ($stmt->execute([$id])) {
        // Delete image file if exists
        if ($image && file_exists("../public/uploads/$image") && $image != 'default.jpg') {
            unlink("../public/uploads/$image");
        }
        $success = "Article deleted successfully.";
    } else {
        $error = "Error deleting article.";
    }
}

// Get all news articles
$news = $pdo->query("SELECT * FROM news ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2>Manage News Articles</h2>
        <a href="add-news.php" class="btn">Add New Article</a>
    </div>
    
    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Author</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($news as $article): ?>
            <tr>
                <td><?php echo htmlspecialchars($article['title']); ?></td>
                <td><?php echo $article['category']; ?></td>
                <td><?php echo htmlspecialchars($article['author']); ?></td>
                <td><?php echo date('M j, Y', strtotime($article['created_at'])); ?></td>
                <td>
                    <a href="../public/news.php?id=<?php echo $article['id']; ?>" target="_blank" class="btn btn-sm">View</a>
                    <a href="edit-news.php?id=<?php echo $article['id']; ?>" class="btn btn-sm">Edit</a>
                    <a href="manage-news.php?delete=<?php echo $article['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this article?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>