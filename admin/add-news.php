<?php
$page_title = "Add News Article";
include 'includes/config.php';
include 'includes/header.php';

$categories = ['AI', 'Gadgets', 'Programming', 'Startups', 'Cybersecurity'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = $_POST['category'];
    $author = trim($_POST['author']);
    
    // Handle image upload
    $image = 'default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../public/uploads/';
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $image = $file_name;
            }
        }
    }
    
    // Insert into database
    try {
        $stmt = $pdo->prepare("INSERT INTO news (title, content, image, category, author) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $content, $image, $category, $author]);
        
        $success = "Article added successfully!";
        
        // Clear form fields
        $title = $content = $author = '';
        $category = 'AI';
    } catch (PDOException $e) {
        $error = "Error adding article: " . $e->getMessage();
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Add New Article</h2>
    </div>
    
    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form action="add-news.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" required><?php echo isset($content) ? htmlspecialchars($content) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" required>
                <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat; ?>" <?php echo (isset($category) && $category == $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="author">Author</label>
            <input type="text" id="author" name="author" value="<?php echo isset($author) ? htmlspecialchars($author) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="image">Featured Image</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        
        <button type="submit" class="btn">Add Article</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>