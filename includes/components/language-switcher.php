<?php require_once __DIR__ . '/../functions.php'; ?>
<div class="btn-group btn-group-sm" role="group" aria-label="<?= e(t('language')) ?>">
    <?php foreach (get_available_languages() as $language): ?>
        <?php $active = $language['code'] === current_lang(); ?>
        <a class="btn <?= $active ? 'btn-accent' : 'btn-outline-secondary' ?>" href="<?= e(lang_url($language['code'])) ?>" data-lang-link="<?= e($language['code']) ?>">
            <?= e(strtoupper($language['code'])) ?>
        </a>
    <?php endforeach; ?>
</div>
