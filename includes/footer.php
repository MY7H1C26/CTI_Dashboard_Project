<?php if (is_logged_in()): ?>
    </main>
</div>
<?php else: ?>
</main>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $base ?? '' ?>assets/js/app.js"></script>
</body>
</html>
