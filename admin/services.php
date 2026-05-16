<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM services WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
        flash('success', 'Service deleted.');
    } else {
        try {
            $fileName = upload_service_file($_FILES['image'] ?? []);
            $stmt = $pdo->prepare('INSERT INTO services (name, description, price, status, image) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$_POST['name'], $_POST['description'], (float) $_POST['price'], $_POST['status'], $fileName]);
            if ($fileName) {
                $upload = $pdo->prepare('INSERT INTO uploads (user_id, file_name, original_name, mime_type, related_table, related_id) VALUES (?, ?, ?, ?, "services", ?)');
                $upload->execute([current_user()['id'], $fileName, $_FILES['image']['name'], mime_content_type(__DIR__ . '/../assets/uploads/' . $fileName), $pdo->lastInsertId()]);
            }
            flash('success', 'Service saved.');
        } catch (RuntimeException $e) {
            flash('danger', $e->getMessage());
        }
    }
    redirect('services.php');
}

$services = $pdo->query('SELECT * FROM services ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Manage Services';
include __DIR__ . '/../includes/header.php';
?>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card p-3">
            <h2 class="h5 mb-3">Add Service/Product</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4"></textarea></div>
                <div class="mb-3"><label class="form-label">Price</label><input type="number" step="0.01" name="price" class="form-control" value="0"></div>
                <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option>Active</option><option>Inactive</option></select></div>
                <div class="mb-3"><label class="form-label">Image/PDF</label><input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.gif,.pdf"></div>
                <button class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card p-3">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Name</th><th>Price</th><th>Status</th><th>File</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?= e($service['name']) ?></td>
                            <td><?= number_format((float) $service['price'], 2) ?></td>
                            <td><?= e($service['status']) ?></td>
                            <td><?= $service['image'] ? '<a href="../assets/uploads/' . e($service['image']) . '">View</a>' : 'None' ?></td>
                            <td>
                                <form method="post" data-confirm="Delete this service?">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e($service['id']) ?>">
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
<?php include __DIR__ . '/../includes/footer.php'; ?>
