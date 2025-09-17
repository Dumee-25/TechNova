<?php
require_once 'includes/config.php';
require_once 'includes/header.php';


$allowed_roles = ['super_admin'];

if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], $allowed_roles)) {
    header("Location: dashboard.php");
    exit();
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    

    if ($id != $_SESSION['admin_id']) {
        $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
        if ($stmt->execute([$id])) {
            $success = "User deleted successfully.";
        } else {
            $error = "Error deleting user.";
        }
    } else {
        $error = "You cannot delete your own account.";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            $error = "Username or email already exists.";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            

            $stmt = $pdo->prepare("INSERT INTO admins (username, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password, $role])) {
                $success = "User added successfully.";
            } else {
                $error = "Error adding user.";
            }
        }
    }
}


$admins = $pdo->query("SELECT * FROM admins ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Manage Admin Users</h2>
    </div>
    
    <?php if (isset($success)): ?>
    <div class="alert alert-success m-3"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
    <div class="alert alert-danger m-3"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card-body">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">Add New User</h3>
            </div>
            <div class="card-body">
                <form action="manage-users.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select id="role" name="role" class="form-select" required>
                                <option value="editor">Editor</option>
                                <option value="admin">Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                </form>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['username']); ?></td>
                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $admin['role'] == 'super_admin' ? 'danger' : ($admin['role'] == 'admin' ? 'primary' : 'secondary'); ?>">
                                <?php echo $admin['role']; ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($admin['created_at'])); ?></td>
                        <td>
                            <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                            <a href="manage-users.php?delete=<?php echo $admin['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            <?php else: ?>
                            <span class="text-muted">Current User</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
