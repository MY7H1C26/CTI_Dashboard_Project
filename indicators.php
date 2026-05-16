<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM indicators WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
        flash('success', 'IOC deleted.');
    } elseif (($_POST['action'] ?? '') === 'update') {
        $stmt = $pdo->prepare('UPDATE indicators SET confidence = ?, source = ? WHERE id = ?');
        $stmt->execute([(int) $_POST['confidence'], $_POST['source'], (int) $_POST['id']]);
        flash('success', 'IOC updated.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO indicators (type, value, threat_type, confidence, source) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_POST['type'], $_POST['value'], $_POST['threat_type'], (int) $_POST['confidence'], $_POST['source']]);
        flash('success', 'IOC added.');
    }
    redirect('indicators.php');
}

$indicators = $pdo->query('SELECT * FROM indicators ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Indicators of Compromise';
include __DIR__ . '/includes/header.php';
?>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card p-3">
            <h2 class="h5 mb-3">Add IOC</h2>
            <form method="post">
                <div class="mb-3"><label class="form-label">Type</label><select name="type" class="form-select"><option>IP</option><option>Domain</option><option>Hash</option><option>URL</option></select></div>
                <div class="mb-3"><label class="form-label">Value</label><input name="value" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Threat Type</label><input name="threat_type" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Confidence</label><input type="number" name="confidence" class="form-control" min="0" max="100" value="70"></div>
                <div class="mb-3"><label class="form-label">Source</label><input name="source" class="form-control"></div>
                <button class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card p-3">
            <input class="form-control mb-3" placeholder="Search IOCs..." data-table-search="#iocTable">
            <div class="table-responsive">
                <table class="table align-middle" id="iocTable">
                    <thead><tr><th>Type</th><th>Value</th><th>Threat</th><th>Confidence / Source</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($indicators as $ioc): ?>
                        <tr>
                            <td><?= e($ioc['type']) ?></td>
                            <td><?= e($ioc['value']) ?></td>
                            <td><?= e($ioc['threat_type']) ?></td>
                            <td>
                                <form method="post" class="d-flex gap-2">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= e($ioc['id']) ?>">
                                    <input type="number" name="confidence" class="form-control form-control-sm" min="0" max="100" value="<?= e($ioc['confidence']) ?>">
                                    <input name="source" class="form-control form-control-sm" value="<?= e($ioc['source']) ?>">
                                    <button class="btn btn-sm btn-outline-success">Update</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" data-confirm="Delete this IOC?">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e($ioc['id']) ?>">
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
