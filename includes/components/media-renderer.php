<?php
declare(strict_types=1);
$mediaItems = $mediaItems ?? [];
?>
<?php foreach ($mediaItems as $media): ?>
  <figure class="problem-media">
    <img src="<?= h(asset_url((string)$media['file_path'])) ?>" alt="<?= h($media['alt_text'] ?? '') ?>" class="img-fluid rounded border">
    <?php if (!empty($media['caption_html'])): ?>
      <figcaption class="small text-secondary mt-2"><?= $media['caption_html'] ?></figcaption>
    <?php endif; ?>
  </figure>
<?php endforeach; ?>
