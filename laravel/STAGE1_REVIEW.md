# Stage 1 Review

Reviewed before Stage 2.

## Composer

- PHP target: `^8.2`
- Laravel target: `^12.0`
- Filament target: `^4.0`
- No PHP 8.5 requirement.

## Migrations

- Existing legacy tables are not dropped.
- Existing legacy columns are not renamed.
- Missing legacy columns are added only after `Schema::hasColumn(...)` checks.
- New ladder tables are named without prefixes:
  - `problem_ladders`
  - `problem_ladder_steps`
- Ladder foreign key columns use `unsignedInteger` to match the legacy `INT UNSIGNED` primary keys.

Note: migration rollback drops only the new ladder tables created by Stage 1. It does not drop old content tables.

## User Model

- Uses existing table: `users`.
- Uses existing password column: `password_hash`.
- `getAuthPassword()` returns `password_hash`, so existing hashes can be verified by Laravel auth.
- Remember-token writes are disabled because the legacy users table has no `remember_token` column.
- No migration changes the existing users table.

## Filament Resources

- Resources use Eloquent models that define explicit legacy table names.
- Relationship selects match model relationship names, for example:
  - `course`
  - `chapter`
  - `problem`
  - `media`
  - `tag`
  - `ladder`
- HTML/MathJax fields use textareas so backslashes are not modified by a rich editor.

## Test Database First

See `README_DEPLOY.md`, section “First Run on a Test Database”.

## Residual Risks

- Local PHP and Composer were not available during scaffolding, so migrations and Filament boot were not executed locally.
- Filament 4 should be installed with Composer before runtime testing.
- Run `php artisan migrate --pretend` and then `php artisan migrate` on a copied test database before production.
