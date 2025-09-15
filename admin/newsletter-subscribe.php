<?php
$page_title = "Manage Subscribers";
include 'includes/config.php';
include 'includes/header.php';

// Get all newsletter subscribers
$subscribers = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-grid">
    <div class="card">
        <div class="card-header">
            <h2>All Subscribers</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Date Subscribed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscribers as $subscriber): ?>
                <tr>
                    <td><?php echo htmlspecialchars($subscriber['email']); ?></td>
                    <td><?php echo date('M j, Y', strtotime($subscriber['created_at'])); ?></td>
                    <td>
                        <a href="newsletter-subscribe.php?delete=<?php echo $subscriber['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this subscriber?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Delete success alert
    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'true'): ?>
    alert('Subscriber deleted successfully.');
    <?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
