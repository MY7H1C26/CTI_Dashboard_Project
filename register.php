<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        flash('danger', 'Please enter a valid name, email, and password of at least 6 characters.');
    } else {
        try {
            register_user($name, $email, $password);
            flash('success', 'Account created. You can login now.');
            redirect('login.php');
        } catch (PDOException $e) {
            flash('danger', 'Email already exists.');
        }
    }
}

$pageTitle = 'Register';
include __DIR__ . '/includes/header.php';
?>
<section class="auth-card">
    <h1 class="h3 mb-2">Create Account</h1>
    <p class="text-secondary mb-4">Register as a SOC analyst/client.</p>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Full name</label>
            <input name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" minlength="6" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Organization</label>
            <input name="organization" class="form-control">
        </div>
        <button class="btn btn-success w-100">Register</button>
    </form>
    <p class="mt-3 mb-0 text-secondary">Already registered? <a href="login.php">Login</a></p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
