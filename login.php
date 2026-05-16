<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login_user($email, $password)) {
        flash('success', 'Welcome back.');
        redirect('dashboard.php');
    }

    flash('danger', 'Invalid email or password.');
}

$pageTitle = 'Login';
include __DIR__ . '/includes/header.php';
?>
<section class="auth-card">
    <h1 class="h3 mb-2">CTI Dashboard</h1>
    <p class="text-secondary mb-4">Sign in to your cyber threat intelligence workspace.</p>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-success w-100">Login</button>
    </form>
    <p class="mt-3 mb-0 text-secondary">No account? <a href="register.php">Create one</a></p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
