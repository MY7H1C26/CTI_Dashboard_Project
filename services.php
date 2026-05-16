<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

$services = $pdo->query('SELECT * FROM services WHERE status = "Active" ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'CTI Services';
$pageSubtitle = 'Request cyber threat intelligence products or incident investigation support';
include __DIR__ . '/includes/header.php';
?>
<div class="row g-3">
    <?php foreach ($services as $service): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card p-3 h-100">
                <?php if ($service['image']): ?>
                    <img src="assets/uploads/<?= e($service['image']) ?>" class="service-img mb-3" alt="<?= e($service['name']) ?>">
                <?php else: ?>
                    <div class="service-img mb-3 d-flex align-items-center justify-content-center text-secondary">CTI Service</div>
                <?php endif; ?>
                <h2 class="h5"><?= e($service['name']) ?></h2>
                <p class="text-secondary flex-grow-1"><?= e($service['description']) ?></p>
                <div class="d-flex justify-content-between align-items-center">
                    <strong><?= number_format((float) $service['price'], 2) ?> TND</strong>
                    <a class="btn btn-success btn-sm" href="reserve.php?service_id=<?= e($service['id']) ?>">Reserve</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
