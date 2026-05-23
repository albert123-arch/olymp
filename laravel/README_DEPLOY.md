# Laravel Deployment (Hostinger)

This project targets Laravel 12 and PHP 8.2+.

## 1) Server prerequisites

1. Set PHP version to **8.2 or newer** in Hostinger.
2. Ensure MySQL database exists and credentials are ready.
3. Keep old plain PHP site as backup during transition.

## 2) Get code on server

Use Git pull on Hostinger (or upload files), then run in `laravel/`:

```bash
composer install --no-dev --optimize-autoloader
```

## 3) Create `.env` manually (do not commit)

Create `laravel/.env` on Hostinger and set values manually:

```dotenv
APP_NAME="Olympiad Mathematics"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://olymp.maths4u.sbs

DB_CONNECTION=mysql
DB_HOST=your_hostinger_mysql_host
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

Generate application key:

```bash
php artisan key:generate
```

## 4) Migrations (only after DB backup)

1. Create database backup first.
2. Preview migration:

```bash
php artisan migrate --pretend
```

3. Apply migration safely:

```bash
php artisan migrate --force
```

## 5) Storage and caches

```bash
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 6) Web root / public directory

Preferred:
- Point domain/subdomain document root to `laravel/public`.

If using temporary rollout:
- Deploy Laravel to `new.olymp.maths4u.sbs` or `laravel.olymp.maths4u.sbs` first.
- Validate routes and admin.
- Switch main domain root after verification.

## 7) Admin access

Filament admin URL:

```text
/admin
```

Use existing users table (`password_hash`) with admin/teacher role.

## 8) Git safety checklist

Before every push:

1. `git status`
2. `git diff --name-only`
3. Confirm these are **not** included:
   - `.env`
   - `.env.*` (except `.env.example`)
   - DB dumps (`*.sql`, `*.sql.gz`, `database/*.sql`)
   - secrets/password files
   - `vendor/`, `node_modules/`
4. Push only source code and docs.

Never commit production credentials or SQL exports.

## 9) Legacy transition note

- Old plain PHP files remain untouched as fallback.
- Laravel should become primary only after production validation.
- No destructive deletion is required for cutover.
