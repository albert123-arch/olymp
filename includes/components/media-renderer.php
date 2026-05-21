<?php
require_once __DIR__ . '/../functions.php';
function render_problem_media(int $problemId, string $role): void
{
    if (!has_real_config()) {
        return;
    }
    $items = fetch_all(
        "SELECT pm.*, pmt.caption_html, pmt.alt_text
         FROM problem_media pm
         LEFT JOIN problem_media_texts pmt ON pmt.media_id = pm.id AND pmt.lang = :lang
         WHERE pm.problem_id = :problem_id
           AND pm.role = :role
           AND pm.is_published = 1
           AND (pm.lang IS NULL OR pm.lang = :lang)
         ORDER BY pm.sort_order, pm.id",
        ['problem_id' => $problemId, 'role' => $role, 'lang' => current_lang()]
    );
    foreach ($items as $item): ?>
        <figure class="problem-media my-3">
            <img class="img-fluid rounded shadow-sm" src="<?= e(url($item['file_path'], [])) ?>" alt="<?= e($item['alt_text'] ?? '') ?>">
            <?php if (!empty($item['caption_html'])): ?><figcaption class="small text-muted mt-1"><?= $item['caption_html'] ?></figcaption><?php endif; ?>
        </figure>
    <?php endforeach;
}

