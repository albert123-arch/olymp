<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_admin();

function admin_header(string $title): void
{
    $pageTitle = $title;
    include __DIR__ . '/../includes/layout/header.php';
    echo '<div class="admin-layout">';
    include __DIR__ . '/../includes/components/admin-sidebar.php';
    echo '<section>';
}

function admin_footer(): void
{
    echo '</section></div>';
    include __DIR__ . '/../includes/layout/footer.php';
}

function admin_languages(): array
{
    return get_available_languages();
}

function admin_textarea(string $name, string $value = '', int $rows = 5): string
{
    return '<textarea class="form-control font-monospace" rows="' . $rows . '" name="' . e($name) . '">' . e($value) . '</textarea>';
}

function upload_problem_image(int $problemId, string $problemCode): void
{
    if (empty($_FILES['image']['name'])) {
        return;
    }
    $file = $_FILES['image'];
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Invalid upload');
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    if (!isset($allowed[$ext])) {
        throw new RuntimeException('File type is not allowed');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if ($mime !== $allowed[$ext]) {
        throw new RuntimeException('MIME type mismatch');
    }
    $dir = __DIR__ . '/../uploads/problems/' . preg_replace('/[^A-Za-z0-9_-]/', '-', $problemCode);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = bin2hex(random_bytes(10)) . '.' . $ext;
    $target = $dir . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Cannot move upload');
    }
    $relative = 'uploads/problems/' . basename($dir) . '/' . $name;
    $columns = ['problem_id', 'role', 'file_path', 'mime_type', 'sort_order', 'created_at'];
    $values = [$problemId, $_POST['image_role'] ?? 'statement', $relative, $mime, 0];
    $placeholders = ['?', '?', '?', '?', '?', 'NOW()'];
    if (column_exists('problem_media', 'lang')) {
        $columns[] = 'lang';
        $values[] = ($_POST['image_lang'] ?? '') ?: null;
        $placeholders[] = '?';
    }
    if (column_exists('problem_media', 'original_name')) {
        $columns[] = 'original_name';
        $values[] = $file['name'];
        $placeholders[] = '?';
    }
    if (column_exists('problem_media', 'file_size')) {
        $columns[] = 'file_size';
        $values[] = $file['size'];
        $placeholders[] = '?';
    }
    if (column_exists('problem_media', 'is_published')) {
        $columns[] = 'is_published';
        $values[] = 1;
        $placeholders[] = '?';
    }
    execute_query(
        'INSERT INTO problem_media (`' . implode('`,`', $columns) . '`) VALUES (' . implode(',', $placeholders) . ')',
        $values
    );
}
