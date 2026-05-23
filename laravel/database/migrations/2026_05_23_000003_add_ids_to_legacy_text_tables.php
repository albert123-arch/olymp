<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->upgradeLegacyTextTable('course_texts', [
            ['course_id', 'lang'],
            ['course_id', 'language_id'],
        ]);

        $this->upgradeLegacyTextTable('chapter_texts', [
            ['chapter_id', 'lang'],
            ['chapter_id', 'language_id'],
        ]);

        $this->upgradeLegacyTextTable('problem_texts', [
            ['problem_id', 'lang'],
            ['problem_id', 'language_id'],
        ]);

        $this->upgradeLegacyTextTable('problem_media_texts', [
            ['media_id', 'lang'],
            ['problem_media_id', 'lang'],
            ['media_id', 'language_id'],
            ['problem_media_id', 'language_id'],
        ]);

        $this->upgradeLegacyTextTable('tag_texts', [
            ['tag_id', 'lang'],
            ['tag_id', 'language_id'],
        ]);
    }

    public function down(): void
    {
        // Intentionally no-op to avoid destructive changes in legacy schema.
    }

    /**
     * @param array<int, array<int, string>> $uniqueColumnCandidates
     */
    private function upgradeLegacyTextTable(string $table, array $uniqueColumnCandidates): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        if (! Schema::hasColumn($table, 'id')) {
            $relationUniqueColumns = $this->resolveUniqueColumns($table, $uniqueColumnCandidates);
            $foreignKeyColumn = $relationUniqueColumns[0] ?? null;
            if (is_string($foreignKeyColumn)) {
                $this->ensureIndexStartingWith($table, $foreignKeyColumn);
            }

            $this->dropPrimaryKeyIfExists($table);
            DB::statement("ALTER TABLE `{$table}` ADD COLUMN `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
        }

        $relationUniqueColumns = $this->resolveUniqueColumns($table, $uniqueColumnCandidates);
        if ($relationUniqueColumns === null) {
            return;
        }

        if ($this->hasUniqueIndexForColumns($table, $relationUniqueColumns)) {
            return;
        }

        $indexName = $this->uniqueIndexName($table, $relationUniqueColumns);

        Schema::table($table, function (Blueprint $schema) use ($relationUniqueColumns, $indexName): void {
            $schema->unique($relationUniqueColumns, $indexName);
        });
    }

    private function dropPrimaryKeyIfExists(string $table): void
    {
        $result = DB::selectOne(
            'SELECT COUNT(*) AS total
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_TYPE = "PRIMARY KEY"',
            [$table],
        );
        $hasPrimaryKey = ((int) ($result->total ?? 0)) > 0;

        if ($hasPrimaryKey) {
            DB::statement("ALTER TABLE `{$table}` DROP PRIMARY KEY");
        }
    }

    private function ensureIndexStartingWith(string $table, string $leadingColumn): void
    {
        $rows = DB::select(
            'SELECT INDEX_NAME, GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX SEPARATOR ",") AS index_columns
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
             GROUP BY INDEX_NAME',
            [$table],
        );

        foreach ($rows as $row) {
            if ((string) $row->INDEX_NAME === 'PRIMARY') {
                continue;
            }

            $columns = explode(',', (string) $row->index_columns);
            if (($columns[0] ?? null) === $leadingColumn) {
                return;
            }
        }

        $indexName = substr('idx_' . $table . '_' . $leadingColumn, 0, 63);
        Schema::table($table, function (Blueprint $schema) use ($leadingColumn, $indexName): void {
            $schema->index([$leadingColumn], $indexName);
        });
    }

    /**
     * @param array<int, array<int, string>> $uniqueColumnCandidates
     * @return array<int, string>|null
     */
    private function resolveUniqueColumns(string $table, array $uniqueColumnCandidates): ?array
    {
        foreach ($uniqueColumnCandidates as $candidate) {
            $allColumnsExist = true;

            foreach ($candidate as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    $allColumnsExist = false;
                    break;
                }
            }

            if ($allColumnsExist) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @param array<int, string> $columns
     */
    private function hasUniqueIndexForColumns(string $table, array $columns): bool
    {
        $target = implode(',', $columns);

        $rows = DB::select(
            'SELECT INDEX_NAME, NON_UNIQUE, GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX SEPARATOR ",") AS index_columns
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
             GROUP BY INDEX_NAME, NON_UNIQUE',
            [$table],
        );

        foreach ($rows as $row) {
            if ((int) $row->NON_UNIQUE !== 0) {
                continue;
            }

            if ((string) $row->index_columns === $target) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, string> $columns
     */
    private function uniqueIndexName(string $table, array $columns): string
    {
        $raw = 'uq_' . $table . '_' . implode('_', $columns);
        $normalized = strtolower(preg_replace('/[^a-z0-9_]+/', '_', $raw) ?? $raw);

        return substr($normalized, 0, 63);
    }
};
