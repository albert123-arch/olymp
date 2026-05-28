<?php

namespace App\Support;

use App\Models\Problem;
use App\Models\ProblemMedia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class ProblemMediaUpload
{
    public const ROLES = ['statement', 'hint', 'solution', 'extra'];

    public const MAX_BYTES = 5 * 1024 * 1024;

    private const IMAGE_EXTENSIONS = ['svg', 'png', 'jpg', 'jpeg', 'webp'];

    private const EXTRA_EXTENSIONS = ['svg', 'png', 'jpg', 'jpeg', 'webp', 'pdf'];

    public static function roleLabels(): array
    {
        return [
            'statement' => 'Statement',
            'hint' => 'Hint',
            'solution' => 'Solution',
            'extra' => 'Extra',
        ];
    }

    public static function normalizeRole(?string $role): string
    {
        $role = mb_strtolower(trim((string) $role), 'UTF-8');

        return match ($role) {
            'question', 'problem' => 'statement',
            'answer' => 'solution',
            'teacher', 'teacher_note' => 'extra',
            'hint', 'solution', 'extra' => $role,
            default => 'statement',
        };
    }

    public static function uploadsRoot(): string
    {
        return base_path('../uploads/problems');
    }

    public static function problemDirectory(Problem $problem): string
    {
        return self::uploadsRoot().DIRECTORY_SEPARATOR.(int) $problem->id;
    }

    public static function publicBase(Problem $problem): string
    {
        return '/uploads/problems/'.(int) $problem->id;
    }

    public static function storeUploadedFile(mixed $file, Problem $problem, string $role, ?string $lang = null): ProblemMedia
    {
        $role = self::normalizeRole($role);
        $extension = mb_strtolower((string) $file->getClientOriginalExtension(), 'UTF-8');
        $allowed = $role === 'extra' ? self::EXTRA_EXTENSIONS : self::IMAGE_EXTENSIONS;

        if (! in_array($extension, $allowed, true)) {
            throw new RuntimeException("Unsupported {$role} file type: {$extension}");
        }

        $size = (int) $file->getSize();
        if ($size > self::MAX_BYTES) {
            throw new RuntimeException('File is larger than 5 MB: '.$file->getClientOriginalName());
        }

        $directory = self::problemDirectory($problem);
        File::ensureDirectoryExists($directory, 0775, true);

        $filename = self::uniqueFilename($problem, $role, $extension, $file->getClientOriginalName());
        $target = $directory.DIRECTORY_SEPARATOR.$filename;

        if (! @copy($file->getRealPath(), $target)) {
            throw new RuntimeException('Could not move uploaded file: '.$file->getClientOriginalName());
        }

        return ProblemMedia::query()->create([
            'problem_id' => (int) $problem->id,
            'role' => $role,
            'lang' => self::blankToNull($lang),
            'file_path' => self::publicBase($problem).'/'.$filename,
            'original_name' => (string) $file->getClientOriginalName(),
            'mime_type' => (string) ($file->getMimeType() ?: self::mimeFromExtension($extension)),
            'file_size' => $size,
            'sort_order' => self::nextSortOrder((int) $problem->id, $role),
            'is_published' => true,
        ]);
    }

    public static function deleteMediaFileIfSafe(ProblemMedia $media): void
    {
        $path = trim((string) $media->file_path);
        $prefix = '/uploads/problems/'.(int) $media->problem_id.'/';

        if ($path === '' || ! str_starts_with($path, $prefix)) {
            return;
        }

        $relative = ltrim(substr($path, strlen('/uploads/problems/')), '/\\');
        $candidate = self::uploadsRoot().DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
        $root = realpath(self::uploadsRoot());
        $file = realpath($candidate);

        if ($root && $file && str_starts_with($file, $root) && is_file($file)) {
            @unlink($file);
        }
    }

    public static function mediaCounts(Collection $media): array
    {
        $counts = array_fill_keys(self::ROLES, 0);

        foreach ($media as $item) {
            $role = self::normalizeRole($item->role ?? 'statement');
            $counts[$role] = ($counts[$role] ?? 0) + 1;
        }

        return $counts;
    }

    public static function hasMissingText(Collection $media): bool
    {
        foreach ($media as $item) {
            $texts = $item->relationLoaded('texts') ? $item->texts : collect();
            if ($texts->isEmpty()) {
                return true;
            }

            foreach ($texts as $text) {
                if (! filled($text->alt_text) && ! filled($text->caption_html)) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function uniqueFilename(Problem $problem, string $role, string $extension, string $originalName): string
    {
        $code = Str::slug((string) ($problem->problem_code ?: 'problem-'.$problem->id), '_');
        $base = Str::slug(pathinfo($originalName, PATHINFO_FILENAME), '_');
        $base = $base !== '' ? $base : $role;
        $candidateBase = trim($code.'_'.$role.'_'.$base, '_');
        $directory = self::problemDirectory($problem);
        $index = 1;

        do {
            $filename = $candidateBase.'_'.$index.'.'.$extension;
            $index++;
        } while (is_file($directory.DIRECTORY_SEPARATOR.$filename));

        return $filename;
    }

    private static function nextSortOrder(int $problemId, string $role): int
    {
        return (int) ProblemMedia::query()
            ->where('problem_id', $problemId)
            ->where('role', $role)
            ->max('sort_order') + 1;
    }

    private static function mimeFromExtension(string $extension): string
    {
        return match ($extension) {
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };
    }

    private static function blankToNull(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' || $value === 'all' ? null : $value;
    }
}
