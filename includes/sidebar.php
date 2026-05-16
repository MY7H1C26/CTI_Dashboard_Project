<?php $base = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false ? '../' : ''; ?>
<aside class="sidebar">
    <a class="brand" href="<?= $base ?>dashboard.php">
        <span class="brand-mark">CTI</span>
        <span>Dashboard</span>
    </a>
    <nav>
        <a href="<?= $base ?>dashboard.php">Dashboard</a>
        <a href="<?= $base ?>services.php">Services</a>
        <a href="<?= $base ?>alerts.php">Alerts</a>
        <a href="<?= $base ?>incidents.php">Incidents</a>
        <a href="<?= $base ?>indicators.php">IOCs</a>
        <a href="<?= $base ?>reports.php">Reports</a>
        <a href="<?= $base ?>profile.php">Profile</a>
        <?php if (is_admin()): ?>
            <div class="nav-label">Administration</div>
            <a href="<?= $base ?>admin/users.php">Users</a>
            <a href="<?= $base ?>admin/services.php">Services Admin</a>
            <a href="<?= $base ?>admin/reservations.php">Reservations</a>
            <a href="<?= $base ?>admin/statistics.php">Statistics</a>
        <?php endif; ?>
        <a href="<?= $base ?>logout.php" class="logout-link">Logout</a>
    </nav>
</aside>
