<?php
$page_title = "Edit News Article";
include 'includes/config.php';
include 'includes/header.php';

$categories = ['AI', 'Gadgets', 'Programming', 'Startups', 'Cybersecurity'];

// Get article data
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage-news.php');
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: manage-news.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = $_POST['category'];
    $author = trim($_POST['author']);
    $current_image = $article['image'];
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../public/uploads/';
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                // Delete old image if it exists and is not default
                if ($current_image && file_exists("../public/uploads/$current_image") && $current_image != 'default.jpg') {
                    unlink("../public/uploads/$current_image");
                }
                $current_image = $file_name;
            }
        }
    }
    
    // Update database
    try {
        $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ?, image = ?, category = ?, author = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$title, $content, $current_image, $category, $author, $id]);
        
        $success = "Article updated successfully!";
        
        // Refresh article data
        $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error updating article: " . $e->getMessage();
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Edit Article</h2>
    </div>
    
    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form action="edit-news.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" required><?php echo htmlspecialchars($article['content']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" required>
                <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat; ?>" <?php echo $article['category'] == $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="author">Author</label>
            <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($article['author']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="image">Featured Image</label>
            <?php if ($article['image'] && $article['image'] != 'default.jpg'): ?>
            <div style="margin-bottom: 10px;">
                <img src="../public/uploads/<?php echo $article['image']; ?>" alt="Current image" style="max-width: 200px; height: auto;">
            </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/*">
            <small>Leave empty to keep current image</small>
        </div>
        
        <button type="submit" class="btn">Update Article</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>