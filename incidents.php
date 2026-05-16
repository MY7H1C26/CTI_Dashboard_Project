<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM incidents WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
        flash('success', 'Incident deleted.');
    } elseif (($_POST['action'] ?? '') === 'update') {
        $stmt = $pdo->prepare('UPDATE incidents SET severity = ?, status = ? WHERE id = ?');
        $stmt->execute([$_POST['severity'], $_POST['status'], (int) $_POST['id']]);
        flash('success', 'Incident updated.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO incidents (title, severity, status, assigned_to, description) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_POST['title'], $_POST['severity'], $_POST['status'], $_POST['assigned_to'], $_POST['description']]);
        flash('success', 'Incident added.');
    }
    redirect('incidents.php');
}

$incidents = $pdo->query('SELECT * FROM incidents ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Incidents';
include __DIR__ . '/includes/header.php';
?>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card p-3">
            <h2 class="h5 mb-3">Add Incident</h2>
            <form method="post">
                <div class="mb-3"><label class="form-label">Title</label><input name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Severity</label><select name="severity" class="form-select"><option>Low</option><option>Medium</option><option>High</option><option>Critical</option></select></div>
                <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option>Open</option><option>Investigating</option><option>Resolved</option></select></div>
                <div class="mb-3"><label class="form-label">Assigned to</label><input name="assigned_to" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4"></textarea></div>
                <button class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card p-3">
            <input class="form-control mb-3" placeholder="Search incidents..." data-table-search="#incidentsTable">
            <div class="table-responsive">
                <table class="table align-middle" id="incidentsTable">
                    <thead><tr><th>Title</th><th>Assigned</th><th>Severity / Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($incidents as $incident): ?>
                        <tr>
                            <td><?= e($incident['title']) ?></td>
                            <td><?= e($incident['assigned_to']) ?></td>
                            <td>
                                <form method="post" class="d-flex gap-2">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= e($incident['id']) ?>">
                                    <select name="severity" class="form-select form-select-sm">
                                        <?php foreach (['Low', 'Medium', 'High', 'Critical'] as $severity): ?>
                                            <option <?= $incident['severity'] === $severity ? 'selected' : '' ?>><?= e($severity) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select name="status" class="form-select form-select-sm">
                                        <?php foreach (['Open', 'Investigating', 'Resolved'] as $status): ?>
                                            <option <?= $incident['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-sm btn-outline-success">Update</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" data-confirm="Delete this incident?">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e($incident['id']) ?>">
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
