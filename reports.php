<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM reports WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
        flash('success', 'Report deleted.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO reports (title, type, content, created_by) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_POST['title'], $_POST['type'], $_POST['content'], current_user()['id']]);
        flash('success', 'Report added.');
    }
    redirect('reports.php');
}

$reports = $pdo->query('
    SELECT reports.*, users.name AS author
    FROM reports
    LEFT JOIN users ON users.id = reports.created_by
    ORDER BY reports.created_at DESC
')->fetchAll();
$pageTitle = 'Reports';
include __DIR__ . '/includes/header.php';
?>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card p-3">
            <h2 class="h5 mb-3">Create Report</h2>
            <form method="post">
                <div class="mb-3"><label class="form-label">Title</label><input name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Type</label><select name="type" class="form-select"><option>Incident Report</option><option>Threat Brief</option><option>Investigation Summary</option></select></div>
                <div class="mb-3"><label class="form-label">Content</label><textarea name="content" class="form-control" rows="7" required></textarea></div>
                <button class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card p-3">
            <input class="form-control mb-3" placeholder="Search reports..." data-table-search="#reportsTable">
            <div class="table-responsive">
                <table class="table align-middle" id="reportsTable">
                    <thead><tr><th>Title</th><th>Type</th><th>Author</th><th>Date</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?= e($report['title']) ?></td>
                            <td><?= e($report['type']) ?></td>
                            <td><?= e($report['author'] ?? 'System') ?></td>
                            <td><?= e($report['created_at']) ?></td>
                            <td>
                                <form method="post" data-confirm="Delete this report?">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e($report['id']) ?>">
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
