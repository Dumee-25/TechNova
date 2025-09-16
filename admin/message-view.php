<?php
/*
    Last Updated: Sep 16, 2025
    Updated by: Sachith Dilshan Kaluwitharana   
*/

$page_title = "Manage Messages";
include 'includes/config.php';
include 'includes/header.php';


date_default_timezone_set('Asia/Colombo');


$checkColumn = $pdo->query("SHOW COLUMNS FROM contact_messages LIKE 'is_deleted'");
if ($checkColumn->rowCount() == 0) {
    $pdo->exec("ALTER TABLE contact_messages ADD COLUMN is_deleted TINYINT(1) DEFAULT 0");
}


$checkReplyColumn = $pdo->query("SHOW COLUMNS FROM contact_messages LIKE 'replied_message'");
if ($checkReplyColumn->rowCount() == 0) {
    $pdo->exec("ALTER TABLE contact_messages ADD COLUMN replied_message TEXT NULL DEFAULT NULL AFTER replied_at");
}


$checkPhoneColumn = $pdo->query("SHOW COLUMNS FROM contact_messages LIKE 'phone'");
if ($checkPhoneColumn->rowCount() == 0) {
    $pdo->exec("ALTER TABLE contact_messages ADD COLUMN phone VARCHAR(20) NULL DEFAULT NULL AFTER email");
}


if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $deleteStmt = $pdo->prepare("UPDATE contact_messages SET is_deleted = 1 WHERE id = ?");
    if ($deleteStmt->execute([$deleteId])) {
        $success = "Message moved to trash.";
    } else {
        $error = "Error moving message to trash.";
    }
}


if (isset($_GET['permanent_delete'])) {
    $deleteId = intval($_GET['permanent_delete']);
    $deleteStmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    if ($deleteStmt->execute([$deleteId])) {
        $success = "Message permanently deleted.";
    } else {
        $error = "Error deleting message.";
    }
}


if (isset($_GET['restore'])) {
    $restoreId = intval($_GET['restore']);
    $restoreStmt = $pdo->prepare("UPDATE contact_messages SET is_deleted = 0 WHERE id = ?");
    if ($restoreStmt->execute([$restoreId])) {
        $success = "Message restored successfully.";
    } else {
        $error = "Error restoring message.";
    }
}


if (isset($_GET['mark_replied'])) {
    $messageId = intval($_GET['mark_replied']);
    $updateStmt = $pdo->prepare("UPDATE contact_messages SET replied = 1, replied_at = NOW() WHERE id = ?");
    if ($updateStmt->execute([$messageId])) {
        $success = "Message marked as replied.";
    } else {
        $error = "Error updating message status.";
    }
}


$view = isset($_GET['view']) ? $_GET['view'] : 'all';


$query = "SELECT * FROM contact_messages WHERE ";
switch ($view) {
    case 'replied':
        $query .= "replied = 1 AND is_deleted = 0";
        break;
    case 'pending':
        $query .= "replied = 0 AND is_deleted = 0";
        break;
    case 'trash':
        $query .= "is_deleted = 1";
        break;
    default:
        $query .= "is_deleted = 0";
        break;
}
$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);


$countAll = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_deleted = 0")->fetchColumn();
$countReplied = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE replied = 1 AND is_deleted = 0")->fetchColumn();
$countPending = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE replied = 0 AND is_deleted = 0")->fetchColumn();
$countTrash = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_deleted = 1")->fetchColumn();


function formatSriLankanDate($dateString) {
    if (!$dateString) return '';
    $date = new DateTime($dateString);
    $date->setTimezone(new DateTimeZone('Asia/Colombo'));
    return $date->format('M j, Y g:i A');
}
?>

<style>
:root {
    --bg-color: #f8f9fa;
    --card-bg: #fff;
    --text-color: #212529;
    --border-color: #dee2e6;
    --primary-color: #007bff;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
    --light-gray: #f8f9fa;
    --dark-gray: #343a40;
    --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    --radius-lg: 12px;
    --radius-md: 8px;
    --radius-sm: 6px;
}

[data-theme="dark"] {
    --bg-color: #121212;
    --card-bg: #1e1e1e;
    --text-color: #e0e0e0;
    --border-color: #2d2d2d;
    --light-gray: #2d2d2d;
    --shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

body {
    background-color: var(--bg-color);
    color: var(--text-color);
    transition: background-color 0.3s, color 0.3s;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.card {
    background: var(--card-bg);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    padding: 25px;
    margin-bottom: 20px;
    transition: background-color 0.3s, box-shadow 0.3s;
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
}

.card-header h2 {
    margin: 0;
    color: var(--text-color);
    font-weight: 600;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.stat-card {
    background: var(--card-bg);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow);
    padding: 20px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
    border-left: 4px solid var(--primary-color);
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.stat-card h3 {
    margin: 0 0 10px 0;
    font-size: 16px;
    color: var(--text-color);
    font-weight: 500;
}

.stat-number {
    font-size: 28px;
    font-weight: bold;
    color: var(--primary-color);
}

.filters {
    display: flex;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 10px;
}

.filter-btn {
    padding: 10px 20px;
    background: var(--light-gray);
    border: none;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: var(--text-color);
    font-weight: 500;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    text-decoration: none;
    display: inline-block;
}

.filter-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, var(--primary-color), transparent);
    opacity: 0;
    transition: opacity 0.3s;
    border-radius: 50px;
}

.filter-btn:hover::before {
    opacity: 0.1;
}

.filter-btn.active {
    background: var(--primary-color);
    color: white;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.alert {
    padding: 12px 15px;
    margin: 10px 0;
    border-radius: var(--radius-sm);
    font-weight: 500;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.table-container {
    overflow-x: auto;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow);
    background: var(--card-bg);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    min-width: 800px;
}

th, td {
    padding: 15px 12px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
    word-wrap: break-word;
}

th {
    background-color: var(--light-gray);
    font-weight: 600;
    color: var(--text-color);
    position: sticky;
    top: 0;
    z-index: 10;
}

tbody tr:hover {
    background-color: var(--light-gray);
}

.status-badge {
    padding: 6px 12px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
    white-space: nowrap;
}

.status-pending {
    background-color: #fff0c7;
    color: #856404;
}

.status-replied {
    background-color: #c7f6d5;
    color: #155724;
}

.status-trashed {
    background: #ffcdd2;
    color: #c62828;
}

.btn {
    padding: 8px 14px;
    font-size: 13px;
    cursor: pointer;
    border-radius: var(--radius-sm);
    text-decoration: none;
    color: #fff;
    display: inline-block;
    border: none;
    transition: all 0.2s;
    margin-right: 6px;
    margin-bottom: 4px;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    white-space: nowrap;
}

.btn-view {
    background: #2196F3;
}

.btn-reply {
    background: #FF9800;
}

.btn-delete {
    background: #f44336;
}

.btn-restore {
    background: #4CAF50;
}

.btn-permanent-delete {
    background: #9C27B0;
}

.btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.15);
}

.empty-state {
    text-align: center;
    padding: 40px 0;
    color: var(--text-color);
}

.empty-state i {
    font-size: 50px;
    margin-bottom: 15px;
    color: var(--border-color);
}

/* Lightbox Styles */
.litebox {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 1000;
    justify-content: center;
    align-items: center;
    padding: 20px;
    box-sizing: border-box;
    overflow-y: auto;
}

.litebox-content {
    background-color: var(--card-bg);
    border-radius: var(--radius-lg);
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    padding: 30px;
    position: relative;
    color: var(--text-color);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
    margin: auto;
}

.litebox-close {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 28px;
    cursor: pointer;
    color: var(--text-color);
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.3s;
    z-index: 1001;
}

.litebox-close:hover {
    background: rgba(0,0,0,0.1);
}

.litebox-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
    padding-right: 50px;
}

.litebox-body {
    margin-bottom: 20px;
    flex: 1;
    overflow-y: auto;
}

.litebox-footer {
    display: flex;
    justify-content: flex-end;
    padding-top: 15px;
    border-top: 1px solid var(--border-color);
    gap: 10px;
    flex-wrap: wrap;
}

.message-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.meta-item {
    padding: 12px;
    background: var(--light-gray);
    border-radius: var(--radius-sm);
}

.meta-label {
    font-weight: bold;
    margin-right: 5px;
    display: block;
    color: var(--primary-color);
    font-size: 0.9em;
}

.message-content {
    line-height: 1.6;
    white-space: pre-wrap;
    padding: 15px;
    background: var(--light-gray);
    border-radius: var(--radius-sm);
    margin-bottom: 20px;
    word-wrap: break-word;
}

.reply-content {
    line-height: 1.6;
    white-space: pre-wrap;
    padding: 15px;
    background: var(--light-gray);
    border-radius: var(--radius-sm);
    border-left: 4px solid var(--success-color);
    word-wrap: break-word;
}

.reply-section {
    margin-top: 25px;
    padding-top: 15px;
    border-top: 1px solid var(--border-color);
}

.reply-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    flex-wrap: wrap;
    gap: 10px;
}

.reply-header h4 {
    margin: 0;
    color: var(--success-color);
}


td:last-child, th:last-child {
    width: 200px;
    min-width: 200px;
}


td:nth-child(2), th:nth-child(2) { 
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
}

td:nth-child(3), th:nth-child(3) { 
    width: 120px;
    min-width: 120px;
}

@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .card {
        padding: 15px;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .filters {
        justify-content: center;
    }
    
    .filter-btn {
        flex: 1;
        min-width: 120px;
        text-align: center;
    }
    
    .table-container {
        overflow-x: scroll;
        -webkit-overflow-scrolling: touch;
    }
    
    table {
        min-width: 600px;
    }
    
    th, td {
        padding: 10px 8px;
        font-size: 14px;
    }
    
    .btn {
        padding: 6px 10px;
        font-size: 12px;
        margin-right: 4px;
    }
    
    .litebox-content {
        width: 95%;
        padding: 20px;
        margin: 20px auto;
    }
    
    .message-meta {
        grid-template-columns: 1fr;
    }
    
    .litebox-footer {
        flex-wrap: wrap;
    }
    
    .reply-header {
        flex-direction: column;
        align-items: flex-start;
    }
}


@media (max-width: 480px) {
    .container {
        padding: 10px;
    }
    
    .card {
        padding: 15px;
    }
    
    table {
        font-size: 13px;
    }
    
    th, td {
        padding: 8px 6px;
    }
    
    .btn {
        padding: 5px 8px;
        font-size: 11px;
        margin-right: 3px;
        margin-bottom: 3px;
    }
}
</style>

<div class="container">
    <div class="card">

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="stats-container">
            <div class="stat-card">
                <h3>All Messages</h3>
                <div class="stat-number"><?php echo $countAll; ?></div>
            </div>
            <div class="stat-card">
                <h3>Replied</h3>
                <div class="stat-number"><?php echo $countReplied; ?></div>
            </div>
            <div class="stat-card">
                <h3>Pending</h3>
                <div class="stat-number"><?php echo $countPending; ?></div>
            </div>
            <div class="stat-card">
                <h3>Trash</h3>
                <div class="stat-number"><?php echo $countTrash; ?></div>
            </div>
        </div>

        <div class="filters">
            <a href="?view=all" class="filter-btn <?php echo $view == 'all' ? 'active' : ''; ?>">All (<?php echo $countAll; ?>)</a>
            <a href="?view=replied" class="filter-btn <?php echo $view == 'replied' ? 'active' : ''; ?>">Replied (<?php echo $countReplied; ?>)</a>
            <a href="?view=pending" class="filter-btn <?php echo $view == 'pending' ? 'active' : ''; ?>">Pending (<?php echo $countPending; ?>)</a>
            <a href="?view=trash" class="filter-btn <?php echo $view == 'trash' ? 'active' : ''; ?>">Trash (<?php echo $countTrash; ?>)</a>
        </div>

        <div class="table-container" id="messagesTable">
            <?php if (count($messages) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                <td title="<?php echo htmlspecialchars($message['email']); ?>"><?php echo htmlspecialchars($message['email']); ?></td>
                                <td><?php echo isset($message['phone']) ? htmlspecialchars($message['phone']) : 'N/A'; ?></td>
                                <td><?php echo formatSriLankanDate($message['created_at']); ?></td>
                                <td>
                                    <?php if ($message['is_deleted']): ?>
                                        <span class="status-badge status-trashed">Trashed</span>
                                    <?php else: ?>
                                        <span class="status-badge <?php echo $message['replied'] ? 'status-replied' : 'status-pending'; ?>">
                                            <?php echo $message['replied'] ? 'Replied' : 'Pending'; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-view view-message" 
                                       data-id="<?php echo $message['id']; ?>"
                                       data-name="<?php echo htmlspecialchars($message['name']); ?>"
                                       data-email="<?php echo htmlspecialchars($message['email']); ?>"
                                       data-phone="<?php echo isset($message['phone']) ? htmlspecialchars($message['phone']) : 'N/A'; ?>"
                                       data-date="<?php echo formatSriLankanDate($message['created_at']); ?>"
                                       data-status="<?php echo $message['is_deleted'] ? 'trashed' : ($message['replied'] ? 'replied' : 'pending'); ?>"
                                       data-message="<?php echo htmlspecialchars($message['message']); ?>"
                                       data-replied="<?php echo $message['replied']; ?>"
                                       data-replied-at="<?php echo $message['replied_at'] ? formatSriLankanDate($message['replied_at']) : ''; ?>"
                                       data-replied-message="<?php echo $message['reply_message'] ? htmlspecialchars($message['reply_message']) : ''; ?>"
                                       data-is-deleted="<?php echo $message['is_deleted']; ?>">View</a>
                                    
                                    <?php if (!$message['is_deleted']): ?>
                                        <?php if (!$message['replied']): ?>
                                            <a href="reply-message.php?id=<?php echo $message['id']; ?>" class="btn btn-reply">Reply</a>
                                        <?php endif; ?>
                                        <a href="?delete=<?php echo $message['id']; ?>&view=<?php echo $view; ?>" class="btn btn-delete" onclick="return confirm('Move this message to trash?')">Delete</a>
                                    <?php else: ?>
                                        <a href="?restore=<?php echo $message['id']; ?>&view=<?php echo $view; ?>" class="btn btn-restore" onclick="return confirm('Restore this message?')">Restore</a>
                                        <a href="?permanent_delete=<?php echo $message['id']; ?>&view=<?php echo $view; ?>" class="btn btn-permanent-delete" onclick="return confirm('Permanently delete this message? This cannot be undone!')">Permanent Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div>📭</div>
                    <h3>No messages found</h3>
                    <p>There are no messages in this category.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="litebox" id="messageLitebox">
    <div class="litebox-content">
        <span class="litebox-close" id="closeLitebox">&times;</span>
        <div class="litebox-header">
            <h3 id="liteboxSubject">Message Details</h3>
        </div>
        <div class="litebox-body">
            <div class="message-meta">
                <div class="meta-item">
                    <span class="meta-label">From:</span>
                    <span id="liteboxName"></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Email:</span>
                    <span id="liteboxEmail"></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Phone:</span>
                    <span id="liteboxPhone"></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Date:</span>
                    <span id="liteboxDate"></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Status:</span>
                    <span id="liteboxStatus"></span>
                </div>
            </div>
            
            <h4>Message:</h4>
            <div class="message-content" id="liteboxMessage"></div>
            
            <div id="replySection" class="reply-section" style="display: none;">
                <div class="reply-header">
                    <h4>Your Reply:</h4>
                    <span id="liteboxRepliedAt" class="meta-label"></span>
                </div>
                <div class="reply-content" id="liteboxRepliedMessage"></div>
            </div>
        </div>
        <div class="litebox-footer">
            <div id="liteboxActions"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const viewButtons = document.querySelectorAll('.view-message');
    const litebox = document.getElementById('messageLitebox');
    const closeLitebox = document.getElementById('closeLitebox');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
           
            const messageId = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const email = this.getAttribute('data-email');
            const phone = this.getAttribute('data-phone');
            const date = this.getAttribute('data-date');
            const status = this.getAttribute('data-status');
            const message = this.getAttribute('data-message');
            const replied = this.getAttribute('data-replied') === '1';
            const repliedAt = this.getAttribute('data-replied-at');
            const repliedMessage = this.getAttribute('data-replied-message');
            const isDeleted = this.getAttribute('data-is-deleted') === '1';
            
           
            document.getElementById('liteboxSubject').textContent = 'Message from ' + name;
            document.getElementById('liteboxName').textContent = name;
            document.getElementById('liteboxEmail').textContent = email;
            document.getElementById('liteboxPhone').textContent = phone;
            document.getElementById('liteboxDate').textContent = date;
            document.getElementById('liteboxMessage').textContent = message;
            
            
            let statusHtml = '';
            if (isDeleted) {
                statusHtml = '<span class="status-badge status-trashed">Trashed</span>';
            } else {
                if (replied) {
                    statusHtml = '<span class="status-badge status-replied">Replied</span>';
                } else {
                    statusHtml = '<span class="status-badge status-pending">Pending</span>';
                }
            }
            document.getElementById('liteboxStatus').innerHTML = statusHtml;
            
            
            const replySection = document.getElementById('replySection');
            if (replied && repliedMessage && repliedMessage.trim() !== '') {
                replySection.style.display = 'block';
                document.getElementById('liteboxRepliedAt').textContent = repliedAt ? 'Replied on: ' + repliedAt : '';
                document.getElementById('liteboxRepliedMessage').textContent = repliedMessage;
            } else {
                replySection.style.display = 'none';
            }
            
          
            let actionsHtml = '';
            const currentView = '<?php echo $view; ?>';
            
            if (!isDeleted) {
                if (!replied) {
                    actionsHtml += '<a href="reply-message.php?id=' + messageId + '" class="btn btn-reply">Reply</a>';
                }
                actionsHtml += '<a href="?delete=' + messageId + '&view=' + currentView + '" class="btn btn-delete" onclick="return confirm(\'Move this message to trash?\')">Delete</a>';
            } else {
                actionsHtml += '<a href="?restore=' + messageId + '&view=' + currentView + '" class="btn btn-restore" onclick="return confirm(\'Restore this message?\')">Restore</a>';
                actionsHtml += '<a href="?permanent_delete=' + messageId + '&view=' + currentView + '" class="btn btn-permanent-delete" onclick="return confirm(\'Permanently delete this message? This cannot be undone!\')">Permanent Delete</a>';
            }
            document.getElementById('liteboxActions').innerHTML = actionsHtml;
            
          
            litebox.style.display = 'flex';
            document.body.style.overflow = 'hidden';
    });
    
    
    function closeLiteboxModal() {
        litebox.style.display = 'none';
        document.body.style.overflow = 'auto'; 
    }
    
    
    closeLitebox.addEventListener('click', closeLiteboxModal);
    

    litebox.addEventListener('click', function(e) {
        if (e.target === litebox) {
            closeLiteboxModal();
        }
    });
    

    document.addEventListener('keyup', function(e) {
        if (e.key === "Escape" && litebox.style.display === 'flex') {
            closeLiteboxModal();
        }
    });
    
   
    document.querySelector('.litebox-content').addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
</script>

<?php include 'includes/footer.php'; ?>