<?php declare(strict_types=1); ?>
</main>
<footer class="border-top bg-white py-4">
  <div class="container d-flex flex-wrap justify-content-between gap-2 text-secondary small">
    <span><?= h(t('site_title')) ?></span>
    <span>PHP / MySQL / Bootstrap</span>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= h(asset_url('assets/app.js')) ?>?v=4"></script>
</body>
</html>
