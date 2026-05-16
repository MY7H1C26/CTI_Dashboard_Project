<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

$stats = [];
foreach (['alerts', 'incidents', 'indicators', 'reservations'] as $table) {
    $stats[$table] = (int) $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
}

$incidentsBySeverity = $pdo->query('SELECT severity, COUNT(*) total FROM incidents GROUP BY severity')->fetchAll();
$latestAlerts = $pdo->query('SELECT * FROM alerts ORDER BY created_at DESC LIMIT 5')->fetchAll();

$pageTitle = 'Dashboard';
$pageSubtitle = 'Operational view of alerts, incidents, IOCs, and CTI service activity';
include __DIR__ . '/includes/header.php';
?>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card stat-card"><span>Alerts</span><strong><?= $stats['alerts'] ?></strong></div></div>
    <div class="col-md-3"><div class="card stat-card"><span>Incidents</span><strong><?= $stats['incidents'] ?></strong></div></div>
    <div class="col-md-3"><div class="card stat-card"><span>IOCs</span><strong><?= $stats['indicators'] ?></strong></div></div>
    <div class="col-md-3"><div class="card stat-card"><span>Orders</span><strong><?= $stats['reservations'] ?></strong></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Latest Alerts</h2>
                <a href="alerts.php" class="btn btn-sm btn-outline-success">Manage</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Title</th><th>Severity</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php foreach ($latestAlerts as $alert): ?>
                        <tr>
                            <td><?= e($alert['title']) ?></td>
                            <td><span class="badge badge-severity-<?= e(strtolower($alert['severity'])) ?>"><?= e($alert['severity']) ?></span></td>
                            <td><?= e($alert['status']) ?></td>
                            <td><?= e($alert['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card p-3">
            <h2 class="h5 mb-3">Incidents by Severity</h2>
            <?php foreach ($incidentsBySeverity as $item): ?>
                <div class="d-flex justify-content-between border-bottom border-secondary py-2">
                    <span><?= e($item['severity']) ?></span>
                    <strong><?= e($item['total']) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
