<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('UPDATE reservations SET status = ? WHERE id = ?');
    $stmt->execute([$_POST['status'], (int) $_POST['id']]);
    flash('success', 'Reservation updated.');
    redirect('reservations.php');
}

$reservations = $pdo->query('
    SELECT reservations.*, users.name AS user_name, users.email, services.name AS service_name
    FROM reservations
    JOIN users ON users.id = reservations.user_id
    JOIN services ON services.id = reservations.service_id
    ORDER BY reservations.created_at DESC
')->fetchAll();
$pageTitle = 'Reservations';
include __DIR__ . '/../includes/header.php';
?>
<div class="card p-3">
    <input class="form-control mb-3" placeholder="Search reservations..." data-table-search="#reservationsTable">
    <div class="table-responsive">
        <table class="table align-middle" id="reservationsTable">
            <thead><tr><th>Client</th><th>Service</th><th>Message</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?= e($reservation['user_name']) ?><br><small class="text-secondary"><?= e($reservation['email']) ?></small></td>
                    <td><?= e($reservation['service_name']) ?></td>
                    <td><?= e($reservation['message']) ?></td>
                    <td>
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="id" value="<?= e($reservation['id']) ?>">
                            <select name="status" class="form-select form-select-sm">
                                <?php foreach (['Pending', 'Approved', 'Rejected', 'Completed'] as $status): ?>
                                    <option <?= $reservation['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-sm btn-outline-success">Save</button>
                        </form>
                    </td>
                    <td><?= e($reservation['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
