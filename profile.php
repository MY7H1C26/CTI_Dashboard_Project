<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

$userId = current_user()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('UPDATE users SET name = ?, phone = ?, organization = ? WHERE id = ?');
    $stmt->execute([
        trim($_POST['name'] ?? ''),
        trim($_POST['phone'] ?? ''),
        trim($_POST['organization'] ?? ''),
        $userId,
    ]);
    $_SESSION['user']['name'] = trim($_POST['name'] ?? '');
    flash('success', 'Profile updated.');
    redirect('profile.php');
}

$stmt = $pdo->prepare('SELECT users.*, roles.name AS role_name FROM users JOIN roles ON roles.id = users.role_id WHERE users.id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

$orders = $pdo->prepare('
    SELECT reservations.*, services.name AS service_name
    FROM reservations
    JOIN services ON services.id = reservations.service_id
    WHERE reservations.user_id = ?
    ORDER BY reservations.created_at DESC
');
$orders->execute([$userId]);

$pageTitle = 'Profile';
include __DIR__ . '/includes/header.php';
?>
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card p-3">
            <h2 class="h5 mb-3">Account</h2>
            <form method="post">
                <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="<?= e($user['name']) ?>" required></div>
                <div class="mb-3"><label class="form-label">Email</label><input class="form-control" value="<?= e($user['email']) ?>" disabled></div>
                <div class="mb-3"><label class="form-label">Role</label><input class="form-control" value="<?= e($user['role_name']) ?>" disabled></div>
                <div class="mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= e($user['phone']) ?>"></div>
                <div class="mb-3"><label class="form-label">Organization</label><input name="organization" class="form-control" value="<?= e($user['organization']) ?>"></div>
                <button class="btn btn-success">Save Profile</button>
            </form>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card p-3">
            <h2 class="h5 mb-3">My Reservations</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Service</th><th>Status</th><th>Requested</th></tr></thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr><td><?= e($order['service_name']) ?></td><td><?= e($order['status']) ?></td><td><?= e($order['created_at']) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
