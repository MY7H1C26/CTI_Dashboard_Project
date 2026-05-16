<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM alerts WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
        flash('success', 'Alert deleted.');
    } elseif (($_POST['action'] ?? '') === 'update') {
        $stmt = $pdo->prepare('UPDATE alerts SET severity = ?, status = ? WHERE id = ?');
        $stmt->execute([$_POST['severity'], $_POST['status'], (int) $_POST['id']]);
        flash('success', 'Alert updated.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO alerts (title, source, severity, status, description) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_POST['title'], $_POST['source'], $_POST['severity'], $_POST['status'], $_POST['description']]);
        flash('success', 'Alert added.');
    }
    redirect('alerts.php');
}

$alerts = $pdo->query('SELECT * FROM alerts ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Alerts';
include __DIR__ . '/includes/header.php';
?>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card p-3">
            <h2 class="h5 mb-3">Add Alert</h2>
            <form method="post">
                <div class="mb-3"><label class="form-label">Title</label><input name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Source</label><input name="source" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Severity</label><select name="severity" class="form-select"><option>Low</option><option>Medium</option><option>High</option><option>Critical</option></select></div>
                <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option>Open</option><option>In Progress</option><option>Closed</option></select></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4"></textarea></div>
                <button class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card p-3">
            <input class="form-control mb-3" placeholder="Search alerts..." data-table-search="#alertsTable">
            <div class="table-responsive">
                <table class="table align-middle" id="alertsTable">
                    <thead><tr><th>Title</th><th>Source</th><th>Severity / Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($alerts as $alert): ?>
                        <tr>
                            <td><?= e($alert['title']) ?></td>
                            <td><?= e($alert['source']) ?></td>
                            <td>
                                <form method="post" class="d-flex gap-2">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= e($alert['id']) ?>">
                                    <select name="severity" class="form-select form-select-sm">
                                        <?php foreach (['Low', 'Medium', 'High', 'Critical'] as $severity): ?>
                                            <option <?= $alert['severity'] === $severity ? 'selected' : '' ?>><?= e($severity) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select name="status" class="form-select form-select-sm">
                                        <?php foreach (['Open', 'In Progress', 'Closed'] as $status): ?>
                                            <option <?= $alert['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-sm btn-outline-success">Update</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" data-confirm="Delete this alert?">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e($alert['id']) ?>">
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
