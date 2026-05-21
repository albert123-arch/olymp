<?php
require_once __DIR__ . '/../functions.php';
function render_problem_media($problemId, $role)
{
    if (!has_real_config()) {
        return;
    }
    if (!table_exists('problem_media')) {
        return;
    }
    $langFilter = column_exists('problem_media', 'lang') ? 'AND (pm.lang IS NULL OR pm.lang = :lang)' : '';
    $publishedFilter = column_exists('problem_media', 'is_published') ? 'AND pm.is_published = 1' : '';
    $originalNameExpr = column_exists('problem_media', 'original_name') ? 'pm.original_name' : "''";
    $fileSizeExpr = column_exists('problem_media', 'file_size') ? 'pm.file_size' : '0';
    $captionJoin = table_exists('problem_media_texts')
        ? 'LEFT JOIN problem_media_texts pmt ON pmt.media_id = pm.id AND pmt.lang = :lang'
        : '';
    $captionExpr = table_exists('problem_media_texts') ? 'pmt.caption_html' : 'NULL';
    $altExpr = table_exists('problem_media_texts') ? 'pmt.alt_text' : 'NULL';
    $params = ['problem_id' => $problemId, 'role' => $role];
    if (strpos($captionJoin . $langFilter, ':lang') !== false) {
        $params['lang'] = current_lang();
    }
    $items = fetch_all(
        "SELECT pm.*, {$originalNameExpr} AS original_name, {$fileSizeExpr} AS file_size, {$captionExpr} AS caption_html, {$altExpr} AS alt_text
         FROM problem_media pm
         {$captionJoin}
         WHERE pm.problem_id = :problem_id
           AND pm.role = :role
           {$publishedFilter}
           {$langFilter}
         ORDER BY pm.sort_order, pm.id",
        $params
    );
    foreach ($items as $item): ?>
        <figure class="problem-media my-3">
            <img class="img-fluid rounded shadow-sm" src="<?= e(url($item['file_path'], [])) ?>" alt="<?= e($item['alt_text'] ?? '') ?>">
            <?php if (!empty($item['caption_html'])): ?><figcaption class="small text-muted mt-1"><?= math_html($item['caption_html']) ?></figcaption><?php endif; ?>
        </figure>
    <?php endforeach;
}
