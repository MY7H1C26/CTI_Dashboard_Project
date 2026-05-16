<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$tables = ['users', 'services', 'reservations', 'alerts', 'incidents', 'indicators', 'investigations', 'threat_intel', 'reports'];
$stats = [];
foreach ($tables as $table) {
    $stats[$table] = (int) $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
}
$severity = $pdo->query('SELECT severity, COUNT(*) total FROM incidents GROUP BY severity')->fetchAll();
$orders = $pdo->query('SELECT status, COUNT(*) total FROM reservations GROUP BY status')->fetchAll();

$pageTitle = 'Statistics';
include __DIR__ . '/../includes/header.php';
?>
<div class="row g-3 mb-4">
    <?php foreach ($stats as $label => $value): ?>
        <div class="col-md-3"><div class="card stat-card"><span><?= e(ucwords(str_replace('_', ' ', $label))) ?></span><strong><?= e($value) ?></strong></div></div>
    <?php endforeach; ?>
</div>
<div class="row g-3">
    <div class="col-md-6">
        <div class="card p-3">
            <h2 class="h5 mb-3">Incidents by Severity</h2>
            <?php foreach ($severity as $row): ?>
                <div class="d-flex justify-content-between border-bottom border-secondary py-2"><span><?= e($row['severity']) ?></span><strong><?= e($row['total']) ?></strong></div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3">
            <h2 class="h5 mb-3">Reservations by Status</h2>
            <?php foreach ($orders as $row): ?>
                <div class="d-flex justify-content-between border-bottom border-secondary py-2"><span><?= e($row['status']) ?></span><strong><?= e($row['total']) ?></strong></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
