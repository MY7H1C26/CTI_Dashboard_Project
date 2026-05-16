<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'delete') {
        if ((int) $_POST['id'] === (int) current_user()['id']) {
            flash('danger', 'You cannot delete your own account.');
        } else {
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([(int) $_POST['id']]);
            flash('success', 'User deleted.');
        }
    } elseif (($_POST['action'] ?? '') === 'update') {
        $stmt = $pdo->prepare('UPDATE users SET role_id = ?, status = ? WHERE id = ?');
        $stmt->execute([(int) $_POST['role_id'], $_POST['status'], (int) $_POST['id']]);
        flash('success', 'User updated.');
    }
    redirect('users.php');
}

$roles = $pdo->query('SELECT * FROM roles ORDER BY id')->fetchAll();
$users = $pdo->query('
    SELECT users.*, roles.name AS role_name
    FROM users
    JOIN roles ON roles.id = users.role_id
    ORDER BY users.created_at DESC
')->fetchAll();
$pageTitle = 'Manage Users';
include __DIR__ . '/../includes/header.php';
?>
<div class="card p-3">
    <input class="form-control mb-3" placeholder="Search users..." data-table-search="#usersTable">
    <div class="table-responsive">
        <table class="table align-middle" id="usersTable">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e($user['name']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td colspan="2">
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= e($user['id']) ?>">
                            <select name="role_id" class="form-select form-select-sm">
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= e($role['id']) ?>" <?= $role['id'] == $user['role_id'] ? 'selected' : '' ?>><?= e($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="status" class="form-select form-select-sm">
                                <option <?= $user['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option <?= $user['status'] === 'Blocked' ? 'selected' : '' ?>>Blocked</option>
                            </select>
                            <button class="btn btn-sm btn-outline-success">Save</button>
                        </form>
                    </td>
                    <td>
                        <form method="post" data-confirm="Delete this user?">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= e($user['id']) ?>">
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
