<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

$serviceId = (int) ($_GET['service_id'] ?? $_POST['service_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM services WHERE id = ? AND status = "Active"');
$stmt->execute([$serviceId]);
$service = $stmt->fetch();

if (!$service) {
    flash('danger', 'Service not found.');
    redirect('services.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO reservations (user_id, service_id, message, status) VALUES (?, ?, ?, "Pending")');
    $stmt->execute([current_user()['id'], $serviceId, trim($_POST['message'] ?? '')]);
    flash('success', 'Reservation request sent.');
    redirect('profile.php');
}

$pageTitle = 'Reserve Service';
include __DIR__ . '/includes/header.php';
?>
<div class="card p-3">
    <h2 class="h4"><?= e($service['name']) ?></h2>
    <p class="text-secondary"><?= e($service['description']) ?></p>
    <form method="post" class="mt-3">
        <input type="hidden" name="service_id" value="<?= e($service['id']) ?>">
        <div class="mb-3">
            <label class="form-label">Request details / incident description</label>
            <textarea name="message" class="form-control" rows="6" required></textarea>
        </div>
        <button class="btn btn-success">Submit Request</button>
        <a href="services.php" class="btn btn-outline-light">Cancel</a>
    </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
