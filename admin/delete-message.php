<?php
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $messageId = intval($_POST['message_id']);

    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$messageId]);

    // Redirect back to manage messages page
    header("Location: manage-messages.php?deleted=1");
    exit;
} else {
    // Invalid request
    header("Location: manage-messages.php?error=invalid_request");
    exit;
}
